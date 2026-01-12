<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SalesReturn;
use App\Models\SalesReturnItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\NoteVoucher;
use App\Models\VoucherProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesReturnController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:sales-return-table')->only(['index']);
        $this->middleware('permission:sales-return-add')->only(['create', 'store']);
        $this->middleware('permission:sales-return-edit')->only(['edit', 'update']);
        $this->middleware('permission:sales-return-delete')->only(['destroy']);
    }

    public function index()
    {
        $returns = SalesReturn::with(['order', 'user'])
            ->latest()
            ->paginate(15);
        return view('admin.sales_returns.index', compact('returns'));
    }

    public function create()
    {
        $orders = Order::with('user', 'orderProducts.product')
            ->where('status', 1)
            ->where('order_type', 1)
            ->get();
        return view('admin.sales_returns.create', compact('orders'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'status' => 'nullable|in:pending,approved,received',
            'return_date' => 'required|date',
            'reason' => 'nullable|string',
            'notes' => 'nullable|string',
            'products' => 'required|array',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity_returned' => 'required|integer|min:1',
            'products.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $order = Order::findOrFail($request->order_id);
            $totalAmount = 0;
            $returnNumber = 'SR-' . date('YmdHis');

            $salesReturn = SalesReturn::create([
                'number' => $returnNumber,
                'order_id' => $order->id,
                'status' => $request->status ?? 'pending',
                'return_date' => $request->return_date,
                'reason' => $request->reason,
                'notes' => $request->notes,
                'total_amount' => 0,
                'user_id' => auth()->id(),
            ]);

            foreach ($request->products as $productData) {
                $product = Product::findOrFail($productData['product_id']);
                $quantity = $productData['quantity_returned'];
                $unitPrice = $productData['unit_price'];
                $totalPrice = $quantity * $unitPrice;
                $totalAmount += $totalPrice;

                SalesReturnItem::create([
                    'sales_return_id' => $salesReturn->id,
                    'product_id' => $product->id,
                    'quantity_returned' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                ]);
            }

            $salesReturn->update(['total_amount' => $totalAmount]);

            // Update order status to Refund
            $order->update(['status' => 6]); // 6 = Refund

            // Create Receipt Note Voucher (سند إدخال) for returned items
            $nextNoteVoucherNumber = (DB::table('note_vouchers')->max('number') ?? 0) + 1;
            $noteVoucher = NoteVoucher::create([
                'number' => $nextNoteVoucherNumber,
                'date_note_voucher' => now()->toDateString(),
                'note' => 'مردود مبيعات - ' . $salesReturn->number . ' - من العميل: ' . $order->user->name,
                'to_warehouse_id' => 1, // Default to first warehouse
                'note_voucher_type_id' => 1, // Receipt Note Voucher (سند إدخال)
            ]);

            // Add returned products to voucher
            foreach ($salesReturn->returnItems as $returnItem) {
                VoucherProduct::create([
                    'quantity' => $returnItem->quantity_returned,
                    'purchasing_price' => $returnItem->unit_price,
                    'note' => 'مردود مبيعات - ' . ($returnItem->product->name_en ?? $returnItem->product->name_ar),
                    'product_id' => $returnItem->product_id,
                    'note_voucher_id' => $noteVoucher->id
                ]);
            }

            DB::commit();
            return redirect()->route('admin.sales-returns.index')
                ->with('success', __('messages.return_created_successfully'));
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function show(SalesReturn $salesReturn)
    {
        $salesReturn->load(['order.user', 'order.orderProducts.product', 'user', 'returnItems.product']);
        return view('admin.sales_returns.show', compact('salesReturn'));
    }

    public function edit(SalesReturn $salesReturn)
    {
        $orders = Order::with('user', 'orderProducts.product')
            ->where('status', 1)
            ->where('order_type', 1)
            ->get();
        $salesReturn->load(['returnItems', 'order.orderProducts.product']);
        return view('admin.sales_returns.edit', compact('salesReturn', 'orders'));
    }

    public function update(Request $request, SalesReturn $salesReturn)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'status' => 'required|in:pending,approved,received',
            'return_date' => 'required|date',
            'reason' => 'nullable|string',
            'notes' => 'nullable|string',
            'products' => 'required|array',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity_returned' => 'required|integer|min:1',
            'products.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $totalAmount = 0;

            // Delete old items
            SalesReturnItem::where('sales_return_id', $salesReturn->id)->delete();

            // Create new items
            foreach ($request->products as $productData) {
                $product = Product::findOrFail($productData['product_id']);
                $quantity = $productData['quantity_returned'];
                $unitPrice = $productData['unit_price'];
                $totalPrice = $quantity * $unitPrice;
                $totalAmount += $totalPrice;

                SalesReturnItem::create([
                    'sales_return_id' => $salesReturn->id,
                    'product_id' => $product->id,
                    'quantity_returned' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                ]);
            }

            // Update return
            $salesReturn->update([
                'order_id' => $request->order_id,
                'status' => $request->status,
                'return_date' => $request->return_date,
                'reason' => $request->reason,
                'notes' => $request->notes,
                'total_amount' => $totalAmount,
            ]);

            // Update Receipt Note Voucher if exists
            $existingVoucher = NoteVoucher::where('note', 'like', '%' . $salesReturn->number . '%')->first();
            if ($existingVoucher) {
                // Delete old voucher products
                VoucherProduct::where('note_voucher_id', $existingVoucher->id)->delete();

                // Add updated products to voucher
                foreach ($salesReturn->returnItems as $returnItem) {
                    VoucherProduct::create([
                        'quantity' => $returnItem->quantity_returned,
                        'purchasing_price' => $returnItem->unit_price,
                        'note' => 'مردود مبيعات - ' . ($returnItem->product->name_en ?? $returnItem->product->name_ar),
                        'product_id' => $returnItem->product_id,
                        'note_voucher_id' => $existingVoucher->id
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('admin.sales-returns.index')
                ->with('success', __('messages.return_updated_successfully'));
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function destroy(SalesReturn $salesReturn)
    {
        try {
            // Delete associated note voucher
            $voucher = NoteVoucher::where('note', 'like', '%' . $salesReturn->number . '%')->first();
            if ($voucher) {
                VoucherProduct::where('note_voucher_id', $voucher->id)->delete();
                $voucher->delete();
            }

            $salesReturn->delete();
            return back()->with('success', __('messages.return_deleted_successfully'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function searchOrders(Request $request)
    {
        $term = $request->get('term', '');
        $limit = $request->get('limit', 10);

        $orders = Order::with('user', 'orderProducts.product')
            ->where('status', 1)
            ->where('order_type', 1)
            ->where(function ($query) use ($term) {
                $query->where('number', 'like', "%{$term}%")
                    ->orWhere('orders.id', 'like', "%{$term}%")
                    ->orWhereHas('user', function ($userQuery) use ($term) {
                        $userQuery->where('name', 'like', "%{$term}%");
                    });
            })
            ->limit($limit)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'text' => $order->number . ' - ' . $order->user->name . ' (' . $order->date->format('M d, Y') . ')',
                    'number' => $order->number,
                    'orderProducts' => $order->orderProducts,
                ];
            });

        return response()->json($orders);
    }
}
