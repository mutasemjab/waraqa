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
use Illuminate\Http\Request;

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

    // الموافقة على رد الطلب
    public function approve(BookRequestResponse $response)
    {
        try {
            // تحديث حالة الرد إلى موافق عليه
            $response->update(['status' => 'approved']);

            // الحصول على المستودع الرئيسي
            $mainWarehouse = Warehouse::first() ?? Warehouse::create(['name' => __('messages.main_warehouse')]);

            // الحصول على نوع سند الإخراج
            $outVoucherType = NoteVoucherType::where('in_out_type', 2)->first();
            if (!$outVoucherType) {
                return back()->with('error', 'نوع سند الإخراج غير موجود');
            }

            // إنشاء سند تحويل (إخراج) للكمية الموافقة عليها
            // الحصول على آخر رقم سند
            $lastNumber = NoteVoucher::max('number') ?? 0;
            $newNumber = $lastNumber + 1;

            $noteVoucher = NoteVoucher::create([
                'number' => $newNumber,
                'note_voucher_type_id' => $outVoucherType->id,
                'from_warehouse_id' => $mainWarehouse->id,
                'to_warehouse_id' => null, // سند إخراج فقط
                'date_note_voucher' => now(),
                'provider_id' => $response->provider_id,
            ]);

            // إضافة المنتج إلى السند
            VoucherProduct::create([
                'note_voucher_id' => $noteVoucher->id,
                'product_id' => $response->bookRequest->product_id,
                'quantity' => $response->available_quantity,
                'note' => __('messages.approved_book_request') . ' - طلب رقم ' . $response->bookRequest->id,
            ]);

            return back()->with('success', 'تمت الموافقة على الطلب وتم إنشاء سند الإخراج');
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
