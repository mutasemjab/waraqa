<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookRequest;
use App\Models\BookRequestResponse;
use App\Models\Product;
use App\Models\Provider;
use App\Models\NoteVoucher;
use App\Models\NoteVoucherType;
use App\Models\VoucherProduct;
use App\Models\Warehouse;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:bookRequest-table')->only(['index']);
        $this->middleware('permission:bookRequest-add')->only(['create', 'store']);
        $this->middleware('permission:bookRequest-edit')->only(['edit', 'update']);
        $this->middleware('permission:bookRequest-delete')->only(['destroy']);
        $this->middleware('permission:bookRequest-approve')->only(['approve']);
        $this->middleware('permission:bookRequest-reject')->only(['reject']);
    }

    // عرض قائمة الطلبات
    public function index()
    {
        $bookRequests = BookRequest::with(['product', 'provider', 'responses'])->get();
        return view('admin.bookRequests.index', compact('bookRequests'));
    }

    // صفحة إنشاء طلب جديد
    public function create()
    {
        $products = Product::all();
        $providers = Provider::all();
        return view('admin.bookRequests.create', compact('products', 'providers'));
    }

    // حفظ الطلب الجديد
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'provider_id' => 'required|exists:providers,id',
            'requested_quantity' => 'required|integer|min:1',
        ]);

        $bookRequest = BookRequest::create($validated);

        return redirect()->route('bookRequests.show', $bookRequest)->with('success', 'تم إنشاء الطلب بنجاح');
    }

    // عرض تفاصيل الطلب والردود
    public function show(BookRequest $bookRequest)
    {
        $bookRequest->load(['product', 'provider', 'responses' => function ($query) {
            $query->with('provider');
        }]);
        return view('admin.bookRequests.show', compact('bookRequest'));
    }

    // تحديث الطلب
    public function edit(BookRequest $bookRequest)
    {
        $products = Product::all();
        $providers = Provider::all();
        return view('admin.bookRequests.edit', compact('bookRequest', 'products', 'providers'));
    }

    public function update(Request $request, BookRequest $bookRequest)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'provider_id' => 'required|exists:providers,id',
            'requested_quantity' => 'required|integer|min:1',
        ]);

        $bookRequest->update($validated);

        return redirect()->route('bookRequests.show', $bookRequest)->with('success', 'تم تحديث الطلب بنجاح');
    }

    // حذف الطلب
    public function destroy(BookRequest $bookRequest)
    {
        $bookRequest->delete();
        return redirect()->route('bookRequests.index')->with('success', 'تم حذف الطلب بنجاح');
    }

    // عرض تفاصيل رد الطلب
    public function showResponse(BookRequestResponse $response)
    {
        $response->load(['bookRequest.product', 'provider']);
        return view('admin.bookRequests.response-details', compact('response'));
    }

    // الموافقة على رد الطلب
    public function approve(Request $request, BookRequestResponse $response)
    {
        try {
            // تحديث السعر والكمية والضريبة من الـ request
            $validated = $request->validate([
                'quantity' => 'required|numeric|min:1|max:' . $response->available_quantity,
                'price' => 'required|numeric|min:0',
                'tax_percentage' => 'nullable|numeric|min:0|max:100',
            ]);

            // استخراج الكمية والسعر والضريبة المعتمدة
            $approvedQuantity = (int)$validated['quantity'];
            $approvedPrice = (float)$validated['price'];
            $approvedTax = (float)($validated['tax_percentage'] ?? 0);

            // حساب السعر الشامل الضريبة بدقة
            $priceWithTax = $approvedPrice + ($approvedPrice * $approvedTax / 100);
            // تقريب لأقرب فلس (منزلتين عشريتين)
            $priceWithTax = round($priceWithTax, 2);

            // تحديث حالة الرد إلى موافق عليه مع السعر والضريبة والكمية المعتمدة
            $response->update(array_merge($validated, ['status' => 'approved']));

            // حساب الإجمالي
            $totalAmountBeforeTax = $approvedPrice * $approvedQuantity;
            $totalTax = round(($totalAmountBeforeTax * $approvedTax) / 100, 2);
            $totalAmount = round($totalAmountBeforeTax + $totalTax, 2);

            // Generate unique purchase number with PO prefix
            // Using database locking to prevent duplicate numbers in concurrent requests
            $purchaseNumber = DB::transaction(function () {
                // Lock the settings row to prevent concurrent modifications
                $setting = DB::table('settings')
                    ->where('key', 'last_purchase_number')
                    ->lockForUpdate()
                    ->first();

                if ($setting) {
                    // Increment the existing purchase number
                    $newNumber = $setting->value + 1;
                    DB::table('settings')
                        ->where('key', 'last_purchase_number')
                        ->update(['value' => $newNumber]);
                } else {
                    // Initialize with 1001 if setting doesn't exist
                    $newNumber = 1001;
                    DB::table('settings')->insert([
                        'key' => 'last_purchase_number',
                        'value' => $newNumber,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }

                return 'PO-' . $newNumber;
            });

            // إنشاء Purchase (مشترية) تلقائياً
            $purchase = Purchase::create([
                'purchase_number' => $purchaseNumber,
                'provider_id' => $response->provider_id,
                'book_request_response_id' => $response->id,
                'total_amount' => $totalAmount,
                'total_tax' => $totalTax,
                'status' => 'confirmed',
                'notes' => __('messages.approved_book_request') . ' - طلب رقم ' . $response->bookRequest->id,
            ]);

            // إضافة المنتج إلى المشترية
            PurchaseItem::create([
                'purchase_id' => $purchase->id,
                'product_id' => $response->bookRequest->product_id,
                'quantity' => $approvedQuantity,
                'unit_price' => $approvedPrice,
                'tax_percentage' => $approvedTax,
                'total_price' => $totalAmount,
            ]);

            // الحصول على المستودع الرئيسي
            $mainWarehouse = Warehouse::first() ?? Warehouse::create(['name' => __('messages.main_warehouse')]);

            // الحصول على نوع سند الإدخال (شراء من المورد)
            $inVoucherType = NoteVoucherType::where('in_out_type', 1)->first();
            if (!$inVoucherType) {
                return back()->with('error', 'نوع سند الإدخال غير موجود');
            }

            // إنشاء سند إدخال (شراء) من المورد إلى المستودع الرئيسي
            // الحصول على آخر رقم سند
            $lastNumber = NoteVoucher::max('number') ?? 0;
            $newNumber = $lastNumber + 1;

            $noteVoucher = NoteVoucher::create([
                'number' => $newNumber,
                'note_voucher_type_id' => $inVoucherType->id,
                'from_warehouse_id' => null, // من المورد
                'to_warehouse_id' => $mainWarehouse->id, // إلى المستودع الرئيسي
                'date_note_voucher' => now(),
                'provider_id' => $response->provider_id,
                'note' => 'سند إدخال من عملية الشراء رقم: ' . $purchase->purchase_number,
            ]);

            // إضافة المنتج إلى السند بالسعر الشامل الضريبة
            VoucherProduct::create([
                'note_voucher_id' => $noteVoucher->id,
                'product_id' => $response->bookRequest->product_id,
                'quantity' => $approvedQuantity,
                'purchasing_price' => $priceWithTax,  // السعر الشامل الضريبة
                'tax_percentage' => $approvedTax,
                'note' => __('messages.approved_book_request') . ' - طلب رقم ' . $response->bookRequest->id,
            ]);

            return back()->with('success', 'تمت الموافقة على الطلب وتم إنشاء سند الإدخال والمشترية');
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    // رفض رد الطلب
    public function reject(BookRequestResponse $response)
    {
        try {
            // تحديث حالة الرد إلى مرفوض
            $response->update(['status' => 'rejected']);

            return back()->with('success', 'تم رفض الطلب');
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }
}
