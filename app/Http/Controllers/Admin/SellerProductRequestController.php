<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SellerProductRequest;
use App\Models\SellerProductRequestItem;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\NoteVoucher;
use App\Models\VoucherProduct;
use App\Models\Warehouse;
use App\Enums\SellerProductRequestStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SellerProductRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:sellerProductRequest-table')->only(['index', 'show']);
        $this->middleware('permission:sellerProductRequest-approve')->only(['showApprovalForm', 'approve']);
        $this->middleware('permission:sellerProductRequest-reject')->only(['reject']);
    }

    public function index(Request $request)
    {
        $query = SellerProductRequest::with(['user', 'items.product.category']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $requests = $query->latest()->paginate(20);
        $users = \App\Models\User::role('seller')->get();

        return view('admin.sellerProductRequests.index', compact('requests', 'users'));
    }

    public function show(SellerProductRequest $sellerProductRequest)
    {
        $sellerProductRequest->load(['user', 'items.product', 'approver', 'order']);
        return view('admin.sellerProductRequests.show', compact('sellerProductRequest'));
    }

    public function showApprovalForm(SellerProductRequest $sellerProductRequest)
    {
        if ($sellerProductRequest->status !== SellerProductRequestStatus::PENDING) {
            return back()->with('error', __('messages.only_pending_requests_can_be_approved'));
        }

        $sellerProductRequest->load('items.product');
        $warehouses = Warehouse::all();
        return view('admin.sellerProductRequests.approve', compact('sellerProductRequest', 'warehouses'));
    }

    public function approve(Request $request, SellerProductRequest $sellerProductRequest)
    {
        if ($sellerProductRequest->status !== SellerProductRequestStatus::PENDING) {
            return back()->with('error', __('messages.only_pending_requests_can_be_approved'));
        }

        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:seller_product_request_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price_with_tax' => 'required|numeric|min:0',
            'items.*.tax_percentage' => 'nullable|numeric|min:0|max:100',
            'from_warehouse_id' => 'required|exists:warehouses,id'
        ]);

        // 1. Verify that requested quantities are available in the selected warehouse
        $fromWarehouseId = $validated['from_warehouse_id'];
        $insufficientStockErrors = [];

        foreach ($validated['items'] as $itemData) {
            $item = SellerProductRequestItem::findOrFail($itemData['id']);
            $requestedQuantity = $itemData['quantity'];
            $productId = $item->product_id;

            // Calculate available quantity: (quantity received TO warehouse) - (quantity sent FROM warehouse)
            $availableQuantity = DB::table('voucher_products as vp')
                ->join('note_vouchers as nv', 'vp.note_voucher_id', '=', 'nv.id')
                ->where('vp.product_id', $productId)
                ->selectRaw('
                    COALESCE(SUM(CASE WHEN nv.to_warehouse_id = ? THEN vp.quantity ELSE 0 END), 0) -
                    COALESCE(SUM(CASE WHEN nv.from_warehouse_id = ? THEN vp.quantity ELSE 0 END), 0) as available
                ', [$fromWarehouseId, $fromWarehouseId])
                ->value('available') ?? 0;

            if ($availableQuantity < $requestedQuantity) {
                $productName = $item->product->name_ar ?? $item->product->name_en;
                $insufficientStockErrors[] = "{$productName}: " . __('messages.available') . " {$availableQuantity}, " . __('messages.requested') . " {$requestedQuantity}";
            }
        }

        if (!empty($insufficientStockErrors)) {
            return back()->with('error', __('messages.insufficient_stock') . '<br>' . implode('<br>', $insufficientStockErrors));
        }

        DB::transaction(function () use ($validated, $sellerProductRequest) {
            // إنشاء رقم Order فريد
            $lastOrderNumber = DB::table('settings')
                ->where('key', 'last_order_number')
                ->lockForUpdate()
                ->value('value') ?? 1000;

            $newOrderNumber = $lastOrderNumber + 1;
            $orderNumber = 'PO-' . $newOrderNumber;

            DB::table('settings')->updateOrInsert(
                ['key' => 'last_order_number'],
                ['value' => $newOrderNumber]
            );

            // Calculate totals using price with tax
            $totalPrice = 0;
            $totalTaxAmount = 0;

            foreach ($validated['items'] as $itemData) {
                $item = SellerProductRequestItem::findOrFail($itemData['id']);
                $quantity = $itemData['quantity'];
                $priceWithTax = $itemData['price_with_tax'];
                $taxPercentage = $itemData['tax_percentage'] ?? 0;

                // Use price with tax directly
                $itemTotal = $priceWithTax * $quantity;
                $totalPrice += $itemTotal;

                // Calculate tax amount from price with tax
                $taxMultiplier = 1 + ($taxPercentage / 100);
                $basePrice = $taxMultiplier > 0 ? $priceWithTax / $taxMultiplier : 0;
                $itemTaxAmount = $itemTotal - ($basePrice * $quantity);
                $totalTaxAmount += $itemTaxAmount;

                // Update the item record
                $item->update([
                    'approved_quantity' => $quantity,
                    'approved_price' => $priceWithTax,
                    'approved_tax_percentage' => $taxPercentage,
                ]);
            }

            // Create Order with total price including tax
            $order = Order::create([
                'number' => $orderNumber,
                'user_id' => $sellerProductRequest->user_id,
                'status' => 1, // PENDING
                'payment_status' => 2, // Unpaid
                'order_type' => 1,
                'date' => now(),
                'order_date' => now(),
                'total_prices' => $totalPrice,
                'total_taxes' => $totalTaxAmount,
                'paid_amount' => 0,
                'remaining_amount' => $totalPrice,
                'note' => 'Order created from Seller Product Request #' . $sellerProductRequest->id,
            ]);

            // 2. Create seller's warehouse if not exists
            $toWarehouse = Warehouse::firstOrCreate(
                ['user_id' => $sellerProductRequest->user_id],
                [
                    'name_ar' => 'مستودع ' . $sellerProductRequest->user->name,
                    'name_en' => $sellerProductRequest->user->name . ' Warehouse',
                ]
            );

            // 3. Create Transfer Note Voucher (type 3) for inventory transfer
            $lastVoucherNumber = NoteVoucher::max('number') ?? 0;
            $voucherNumber = $lastVoucherNumber + 1;

            $noteVoucher = NoteVoucher::create([
                'number' => $voucherNumber,
                'from_warehouse_id' => $validated['from_warehouse_id'],
                'to_warehouse_id' => $toWarehouse->id,
                'note_voucher_type_id' => 3, // Transfer Note Voucher type
                'date_note_voucher' => now(),
                'note' => 'Transfer for Seller Request #' . $sellerProductRequest->id,
                'order_id' => $order->id,
            ]);

            // Add OrderProducts and VoucherProducts
            foreach ($validated['items'] as $itemData) {
                $item = SellerProductRequestItem::findOrFail($itemData['id']);
                $quantity = $itemData['quantity'];
                $priceWithTax = $itemData['price_with_tax'];
                $taxPercentage = $itemData['tax_percentage'] ?? 0;

                // Calculate base price from price with tax
                $taxMultiplier = 1 + ($taxPercentage / 100);
                $basePrice = $taxMultiplier > 0 ? $priceWithTax / $taxMultiplier : 0;

                $itemTotalBeforeTax = $basePrice * $quantity;
                $itemTaxAmount = ($itemTotalBeforeTax * $taxPercentage) / 100;
                $itemTotalAfterTax = $priceWithTax * $quantity;

                // Create OrderProduct
                OrderProduct::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $quantity,
                    'unit_price' => $basePrice,
                    'tax_percentage' => $taxPercentage,
                    'tax_value' => $itemTaxAmount,
                    'total_price_before_tax' => $itemTotalBeforeTax,
                    'total_price_after_tax' => $itemTotalAfterTax,
                ]);

                // Create VoucherProduct
                VoucherProduct::create([
                    'note_voucher_id' => $noteVoucher->id,
                    'product_id' => $item->product_id,
                    'quantity' => $quantity,
                    'purchasing_price' => $priceWithTax,
                    'tax_percentage' => $taxPercentage,
                    'note' => 'From Seller Request #' . $sellerProductRequest->id,
                ]);
            }

            // تحديث الطلب
            $sellerProductRequest->update([
                'status' => SellerProductRequestStatus::APPROVED->value,
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'order_id' => $order->id,
            ]);
        });

        return redirect()->route('sellerProductRequests.show', $sellerProductRequest)
            ->with('success', __('messages.request_approved_successfully'));
    }

    public function reject(Request $request, SellerProductRequest $sellerProductRequest)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);

        $sellerProductRequest->update([
            'status' => SellerProductRequestStatus::REJECTED->value,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        return redirect()->route('sellerProductRequests.index')
            ->with('success', __('messages.request_rejected_successfully'));
    }
}
