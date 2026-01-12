<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Provider;
use App\Models\Product;
use App\Models\NoteVoucher;
use App\Models\VoucherProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:purchase-table')->only(['index']);
        $this->middleware('permission:purchase-add')->only(['create', 'store']);
        $this->middleware('permission:purchase-edit')->only(['edit', 'update']);
        $this->middleware('permission:purchase-delete')->only(['destroy']);
        $this->middleware('permission:purchase-confirm')->only(['confirm']);
        $this->middleware('permission:purchase-receive')->only(['markAsReceived']);
    }

    // عرض قائمة المشتريات
    public function index()
    {
        $purchases = Purchase::with(['provider', 'items.product'])
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
            'expected_delivery_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.unit_price' => 'required|numeric|min:0',
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
                $unitPrice = $productData['unit_price'];
                $taxPercentage = $productData['tax_percentage'];

                $totalPrice = $unitPrice * $quantity;
                $taxValue = ($totalPrice * $taxPercentage) / 100;
                $totalPriceWithTax = $totalPrice + $taxValue;

                $totalTax += $taxValue;
                $totalAmount += $totalPriceWithTax;

                $purchaseItems[] = [
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'tax_percentage' => $taxPercentage,
                    'total_price' => $totalPriceWithTax,
                ];
            }

            // إنشاء رقم المشترية
            $lastPurchase = Purchase::max('id') ?? 0;
            $purchaseNumber = 'PUR-' . date('Y') . '-' . str_pad($lastPurchase + 1, 5, '0', STR_PAD_LEFT);

            // إنشاء المشترية
            $purchase = Purchase::create([
                'purchase_number' => $purchaseNumber,
                'provider_id' => $request->provider_id,
                'warehouse_id' => $request->warehouse_id,
                'expected_delivery_date' => $request->expected_delivery_date,
                'notes' => $request->notes,
                'total_amount' => $totalAmount,
                'total_tax' => $totalTax,
                'status' => 'pending',
            ]);

            // إنشاء عناصر المشترية
            foreach ($purchaseItems as $item) {
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'tax_percentage' => $item['tax_percentage'],
                    'total_price' => $item['total_price'],
                ]);
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
        $purchase->load(['provider', 'items.product', 'bookRequestResponse']);
        return view('admin.purchases.show', compact('purchase'));
    }

    // صفحة التعديل
    public function edit(Purchase $purchase)
    {
        if ($purchase->status !== 'pending') {
            return back()->with('error', 'لا يمكن تعديل مشترية تم تأكيدها');
        }

        $providers = Provider::all();
        $purchase->load('items');
        return view('admin.purchases.edit', compact('purchase', 'providers'));
    }

    // تحديث المشترية
    public function update(Request $request, Purchase $purchase)
    {
        if ($purchase->status !== 'pending') {
            return back()->with('error', 'لا يمكن تعديل مشترية تم تأكيدها');
        }

        $request->validate([
            'provider_id' => 'nullable|exists:providers,id',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'expected_delivery_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.unit_price' => 'required|numeric|min:0',
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
                $unitPrice = $productData['unit_price'];
                $taxPercentage = $productData['tax_percentage'];

                $totalPrice = $unitPrice * $quantity;
                $taxValue = ($totalPrice * $taxPercentage) / 100;
                $totalPriceWithTax = $totalPrice + $taxValue;

                $totalTax += $taxValue;
                $totalAmount += $totalPriceWithTax;

                $purchaseItems[] = [
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'tax_percentage' => $taxPercentage,
                    'total_price' => $totalPriceWithTax,
                ];
            }

            // تحديث المشترية
            $purchase->update([
                'provider_id' => $request->provider_id ?? null,
                'warehouse_id' => $request->warehouse_id ?? null,
                'expected_delivery_date' => $request->expected_delivery_date,
                'notes' => $request->notes,
                'total_amount' => $totalAmount,
                'total_tax' => $totalTax,
            ]);

            // حذف العناصر القديمة
            $purchase->items()->delete();

            // إضافة العناصر الجديدة
            foreach ($purchaseItems as $item) {
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'tax_percentage' => $item['tax_percentage'],
                    'total_price' => $item['total_price'],
                ]);
            }

            DB::commit();
            return redirect()->route('purchases.show', $purchase)
                ->with('success', 'تم تحديث المشترية بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'خطأ: ' . $e->getMessage());
        }
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

    // تحديد تاريخ الاستلام
    public function markAsReceived(Request $request, Purchase $purchase)
    {
        try {
            $validated = $request->validate([
                'received_date' => 'required|date',
            ]);

            DB::beginTransaction();

            // تحديث حالة عملية الشراء
            $purchase->update(array_merge($validated, ['status' => 'received']));

            // إنشاء سند إدخال
            $lastVoucher = NoteVoucher::max('number') ?? 0;
            $voucherNumber = $lastVoucher + 1;

            $noteVoucher = NoteVoucher::create([
                'number' => $voucherNumber,
                'date_note_voucher' => $validated['received_date'],
                'note_voucher_type_id' => 1, // 1 = سند إدخال
                'to_warehouse_id' => $purchase->warehouse_id,
                'provider_id' => $purchase->provider_id,
                'note' => 'سند إدخال من عملية الشراء رقم: ' . $purchase->purchase_number,
            ]);

            // إضافة منتجات السند من عناصر عملية الشراء
            foreach ($purchase->items as $item) {
                VoucherProduct::create([
                    'note_voucher_id' => $noteVoucher->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'purchasing_price' => $item->unit_price,
                    'tax_percentage' => $item->tax_percentage,
                ]);
            }

            DB::commit();
            return back()->with('success', 'تم تحديث حالة عملية الشراء إلى مستلمة وإنشاء سند إدخال بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }
}
