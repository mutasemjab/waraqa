<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Provider;
use App\Models\Product;
use App\Models\NoteVoucher;
use App\Models\VoucherProduct;
use App\Models\BookRequest;
use App\Models\BookRequestItem;
use App\Models\BookRequestResponse;
use App\Models\Warehouse;
use App\Models\NoteVoucherType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:purchase-table')->only(['index']);
        $this->middleware('permission:purchase-add')->only(['create', 'store']);
        $this->middleware('permission:purchase-delete')->only(['destroy']);
        $this->middleware('permission:purchase-confirm')->only(['confirm']);
        $this->middleware('permission:purchase-receive')->only(['markAsReceived']);
    }

    // عرض قائمة المشتريات
    public function index()
    {
        $purchases = Purchase::with(['provider', 'items.product', 'bookRequestResponse'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        return view('admin.purchases.index', compact('purchases'));
    }

    // صفحة إنشاء مشترية جديدة
    public function create()
    {
        $providers = Provider::all();
        return view('admin.purchases.create', compact('providers'));
    }

    // حفظ المشترية الجديدة
    public function store(Request $request)
    {
        $request->validate([
            'provider_id' => 'nullable|exists:providers,id',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'notes' => 'nullable|string',
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.price_with_tax' => 'required|numeric|min:0',
            'products.*.tax_percentage' => 'required|numeric|min:0|max:100',
        ]);

        DB::beginTransaction();
        try {
            $totalTax = 0;
            $totalAmount = 0;
            $purchaseItems = [];

            // حساب الإجمالي
            foreach ($request->products as $productData) {
                $product = Product::find($productData['id']);
                $quantity = $productData['quantity'];
                $priceWithTax = $productData['price_with_tax'];
                $taxPercentage = $productData['tax_percentage'];

                // حساب السعر بدون الضريبة من السعر الشامل
                $unitPrice = $priceWithTax / (1 + $taxPercentage / 100);

                $totalPrice = $priceWithTax * $quantity;
                $taxValue = $totalPrice - ($unitPrice * $quantity);

                $totalTax += $taxValue;
                $totalAmount += $totalPrice;

                $purchaseItems[] = [
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'tax_percentage' => $taxPercentage,
                    'total_price' => $totalPrice,
                ];
            }

            // إنشاء رقم المشترية باستخدام جدول settings
            $purchaseNumber = DB::transaction(function () {
                $setting = DB::table('settings')
                    ->where('key', 'last_purchase_number')
                    ->lockForUpdate()
                    ->first();

                if ($setting) {
                    $newNumber = $setting->value + 1;
                    DB::table('settings')
                        ->where('key', 'last_purchase_number')
                        ->update(['value' => $newNumber]);
                } else {
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

            // إنشاء المشترية
            $purchase = Purchase::create([
                'purchase_number' => $purchaseNumber,
                'provider_id' => $request->provider_id,
                'warehouse_id' => $request->warehouse_id,
                'notes' => $request->notes,
                'total_amount' => $totalAmount,
                'total_tax' => $totalTax,
                'status' => 'pending',
            ]);

            // إنشاء عناصر المشترية
            $purchaseItemsData = array_map(function ($item) use ($purchase) {
                return [
                    'purchase_id' => $purchase->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'tax_percentage' => $item['tax_percentage'],
                    'total_price' => $item['total_price'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }, $purchaseItems);
            PurchaseItem::insert($purchaseItemsData);

            // إنشاء طلب كتب تلقائي من نفس المنتجات
            if ($purchase->provider_id) {
                $bookRequest = BookRequest::create([
                    'provider_id' => $purchase->provider_id,
                    'user_id' => auth()->id(),
                    'purchase_id' => $purchase->id,
                    'note' => 'طلب تلقائي من عملية شراء: ' . $purchaseNumber,
                ]);

                // إضافة عناصر طلب الكتب
                $bookRequestItemsData = array_map(function ($item) use ($bookRequest) {
                    return [
                        'book_request_id' => $bookRequest->id,
                        'product_id' => $item['product_id'],
                        'requested_quantity' => $item['quantity'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }, $purchaseItems);
                BookRequestItem::insert($bookRequestItemsData);
            }

            DB::commit();
            return redirect()->route('purchases.show', $purchase)
                ->with('success', 'تم إنشاء المشترية بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'خطأ: ' . $e->getMessage());
        }
    }

    // عرض تفاصيل المشترية
    public function show(Purchase $purchase)
    {
        $purchase->load([
            'provider',
            'items.product',
            'bookRequest.items.product',
            'bookRequest.items.responses',
            'bookRequestResponse'
        ]);

        // Return JSON if it's an AJAX request
        if (request()->expectsJson()) {
            return response()->json($purchase);
        }

        return view('admin.purchases.show', compact('purchase'));
    }

    // حذف المشترية
    public function destroy(Purchase $purchase)
    {
        if ($purchase->status !== 'pending') {
            return back()->with('error', 'لا يمكن حذف مشترية تم تأكيدها');
        }

        $purchase->delete();
        return redirect()->route('purchases.index')
            ->with('success', 'تم حذف المشترية بنجاح');
    }

    // تأكيد المشترية
    public function confirm(Purchase $purchase)
    {
        try {
            if ($purchase->items()->count() === 0) {
                return back()->with('error', 'لا يمكن تأكيد مشترية بدون عناصر');
            }

            $purchase->update(['status' => 'confirmed']);

            return back()->with('success', 'تم تأكيد المشترية بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * Mark a purchase as received by updating its status and received date.
     *
     * @param Request $request
     * @param Purchase $purchase
     * @return \Illuminate\Http\RedirectResponse
     */
    public function markAsReceived(Request $request, Purchase $purchase)
    {
        try {
            // 1. Validate received date
            $validated = $request->validate([
                'received_date' => 'required|date|date_format:Y-m-d|before_or_equal:today',
            ]);

            DB::beginTransaction();

            // 2. Update purchase status to received with the received date
            $purchase->update(array_merge($validated, ['status' => 'received']));

            DB::commit();

            // 3. Return success message
            return back()->with('success', __('messages.purchase_marked_as_received_successfully'));
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', __('messages.error') . ': ' . $e->getMessage());
        }
    }

    /**
     * Approve a book request response and create warehouse voucher.
     * All prices in the system are inclusive of tax.
     *
     * @param Request $request
     * @param BookRequestResponse $response
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approveResponse(Request $request, BookRequestResponse $response)
    {
        try {
            // 1. Validate request data
            $validated = $request->validate([
                'quantity' => 'required|numeric|min:1|max:' . $response->available_quantity,
                'price' => 'required|numeric|min:0',
                'tax_percentage' => 'nullable|numeric|min:0|max:100',
            ]);

            $approvedQuantity = (int)$validated['quantity'];
            $approvedPriceWithTax = (float)$validated['price'];
            $approvedTax = (float)($validated['tax_percentage'] ?? 0);

            DB::beginTransaction();

            // 2. Update response status to approved
            $response->update([
                'quantity' => $approvedQuantity,
                'price' => $approvedPriceWithTax,
                'tax_percentage' => $approvedTax,
                'status' => 'approved'
            ]);

            // 3. Update book request status to approved
            $bookRequest = $response->bookRequestItem->bookRequest;
            $bookRequest->update(['status' => 'approved']);

            // 4. Update purchase status to confirmed and update items with approved data
            $purchase = Purchase::where('book_request_response_id', $response->id)->first();
            if ($purchase) {
                // Update purchase items with approved quantities, prices, and taxes
                foreach ($purchase->items as $purchaseItem) {
                    // Check if this item matches the response product
                    if ($purchaseItem->product_id === $response->bookRequestItem->product_id) {
                        $purchaseItem->update([
                            'quantity' => $approvedQuantity,
                            'unit_price' => $approvedPriceWithTax / (1 + $approvedTax / 100),
                            'tax_percentage' => $approvedTax,
                            'total_price' => $approvedPriceWithTax * $approvedQuantity,
                        ]);
                    }
                }

                // Recalculate purchase totals
                $totalPrice = $purchase->items->sum('total_price');
                $totalTax = 0;
                foreach ($purchase->items as $item) {
                    $subtotal = $item->total_price / (1 + $item->tax_percentage / 100);
                    $totalTax += $item->total_price - $subtotal;
                }

                // Update purchase with new totals and status
                $purchase->update([
                    'total_amount' => $totalPrice,
                    'total_tax' => $totalTax,
                    'status' => 'confirmed'
                ]);
            }

            // 5. Get or create main warehouse
            $mainWarehouse = Warehouse::first() ?? Warehouse::create(['name' => __('messages.main_warehouse')]);

            // 6. Get the input voucher type
            $inVoucherType = NoteVoucherType::where('in_out_type', 1)->first();
            if (!$inVoucherType) {
                throw new \Exception(__('messages.input_voucher_type_not_found'));
            }

            // 7. Create input voucher with sequential number
            $lastNumber = NoteVoucher::max('number') ?? 0;
            $newNumber = $lastNumber + 1;

            $noteVoucher = NoteVoucher::create([
                'number' => $newNumber,
                'note_voucher_type_id' => $inVoucherType->id,
                'from_warehouse_id' => null,
                'to_warehouse_id' => $mainWarehouse->id,
                'date_note_voucher' => now(),
                'provider_id' => $response->provider_id,
                'note' => __('messages.input_voucher_from_purchase', [
                    'purchase_number' => $purchase->purchase_number ?? 'N/A'
                ]),
            ]);

            // 8. Add product to voucher with approved quantities and prices
            VoucherProduct::create([
                'note_voucher_id' => $noteVoucher->id,
                'product_id' => $response->bookRequestItem->product_id,
                'quantity' => $approvedQuantity,
                'purchasing_price' => $approvedPriceWithTax,
                'tax_percentage' => $approvedTax,
                'note' => __('messages.approved_purchase', [
                    'purchase_number' => $purchase->purchase_number ?? 'N/A'
                ]),
            ]);

            DB::commit();
            return back()->with('success', __('messages.response_approved_successfully'));
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', __('messages.error') . ': ' . $e->getMessage());
        }
    }

    // رفض رد طلب الكتب
    public function rejectResponse(BookRequestResponse $response)
    {
        try {
            DB::beginTransaction();

            // تحديث حالة الرد إلى مرفوض
            $response->update(['status' => 'rejected']);

            // تحديث حالة طلب الكتب إلى مرفوض
            if ($response->bookRequestItem && $response->bookRequestItem->bookRequest) {
                $response->bookRequestItem->bookRequest->update(['status' => 'rejected']);
            }

            // تحديث حالة الشراء إلى مرفوض
            $purchase = Purchase::where('book_request_response_id', $response->id)->first();
            if ($purchase) {
                $purchase->update(['status' => 'rejected']);
            }

            DB::commit();
            return back()->with('success', 'تم رفض الرد وتحديث الحالات بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }
}
