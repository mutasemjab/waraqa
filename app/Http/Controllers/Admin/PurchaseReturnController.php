<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnItem;
use App\Models\Purchase;
use App\Models\Product;
use App\Models\NoteVoucher;
use App\Models\VoucherProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseReturnController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:purchase-return-table')->only(['index']);
        $this->middleware('permission:purchase-return-add')->only(['create', 'store']);
        $this->middleware('permission:purchase-return-edit')->only(['edit', 'update']);
        $this->middleware('permission:purchase-return-delete')->only(['destroy']);
    }

    public function index()
    {
        $returns = PurchaseReturn::with(['purchase', 'provider'])
            ->latest()
            ->paginate(15);
        return view('admin.purchase_returns.index', compact('returns'));
    }

    public function create()
    {
        $purchases = Purchase::with('provider', 'items.product')
            ->where('status', 'received')
            ->get();
        return view('admin.purchase_returns.create', compact('purchases'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'purchase_id' => 'required|exists:purchases,id',
            'status' => 'nullable|in:pending,sent,received',
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
            $purchase = Purchase::findOrFail($request->purchase_id);
            $totalAmount = 0;
            $returnNumber = 'PR-' . date('YmdHis');

            $purchaseReturn = PurchaseReturn::create([
                'number' => $returnNumber,
                'purchase_id' => $purchase->id,
                'provider_id' => $purchase->provider_id,
                'warehouse_id' => $purchase->warehouse_id,
                'status' => $request->status ?? 'pending',
                'return_date' => $request->return_date,
                'reason' => $request->reason,
                'notes' => $request->notes,
                'total_amount' => 0,
            ]);

            foreach ($request->products as $productData) {
                $product = Product::findOrFail($productData['product_id']);
                $quantity = $productData['quantity_returned'];
                $unitPrice = $productData['unit_price'];
                $totalPrice = $quantity * $unitPrice;
                $totalAmount += $totalPrice;

                PurchaseReturnItem::create([
                    'purchase_return_id' => $purchaseReturn->id,
                    'product_id' => $product->id,
                    'quantity_returned' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                ]);
            }

            $purchaseReturn->update(['total_amount' => $totalAmount]);

            // Create Dispatch Note Voucher (سند إخراج) for returned items
            $nextNoteVoucherNumber = (DB::table('note_vouchers')->max('number') ?? 0) + 1;
            $noteVoucher = NoteVoucher::create([
                'number' => $nextNoteVoucherNumber,
                'date_note_voucher' => now()->toDateString(),
                'note' => 'مردود مشتريات - ' . $purchaseReturn->number . ' - إلى المورد: ' . ($purchase->provider->name ?? 'N/A'),
                'from_warehouse_id' => $purchase->warehouse_id, // Products are returned FROM this warehouse
                'note_voucher_type_id' => 2, // Dispatch Note Voucher (سند إخراج)
                'provider_id' => $purchase->provider_id,
            ]);

            // Add returned products to voucher
            foreach ($purchaseReturn->returnItems as $returnItem) {
                VoucherProduct::create([
                    'quantity' => $returnItem->quantity_returned,
                    'purchasing_price' => $returnItem->unit_price,
                    'note' => 'مردود مشتريات - ' . ($returnItem->product->name_en ?? $returnItem->product->name_ar),
                    'product_id' => $returnItem->product_id,
                    'note_voucher_id' => $noteVoucher->id
                ]);
            }

            DB::commit();
            return redirect()->route('admin.purchase-returns.index')
                ->with('success', __('messages.return_created_successfully'));
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function show(PurchaseReturn $purchaseReturn)
    {
        $purchaseReturn->load(['purchase.provider', 'purchase.items.product', 'provider', 'returnItems.product']);
        return view('admin.purchase_returns.show', compact('purchaseReturn'));
    }

    public function edit(PurchaseReturn $purchaseReturn)
    {
        $purchases = Purchase::with('provider', 'items.product')
            ->where('status', 'received')
            ->get();
        $purchaseReturn->load(['returnItems', 'purchase.items.product']);
        return view('admin.purchase_returns.edit', compact('purchaseReturn', 'purchases'));
    }

    public function update(Request $request, PurchaseReturn $purchaseReturn)
    {
        $request->validate([
            'purchase_id' => 'required|exists:purchases,id',
            'status' => 'required|in:pending,sent,received',
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
            PurchaseReturnItem::where('purchase_return_id', $purchaseReturn->id)->delete();

            // Create new items
            foreach ($request->products as $productData) {
                $product = Product::findOrFail($productData['product_id']);
                $quantity = $productData['quantity_returned'];
                $unitPrice = $productData['unit_price'];
                $totalPrice = $quantity * $unitPrice;
                $totalAmount += $totalPrice;

                PurchaseReturnItem::create([
                    'purchase_return_id' => $purchaseReturn->id,
                    'product_id' => $product->id,
                    'quantity_returned' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                ]);
            }

            // Update return
            $purchaseReturn->update([
                'purchase_id' => $request->purchase_id,
                'status' => $request->status,
                'return_date' => $request->return_date,
                'reason' => $request->reason,
                'notes' => $request->notes,
                'total_amount' => $totalAmount,
            ]);

            // Update Dispatch Note Voucher if exists
            $existingVoucher = NoteVoucher::where('note', 'like', '%' . $purchaseReturn->number . '%')->first();
            if ($existingVoucher) {
                // Delete old voucher products
                VoucherProduct::where('note_voucher_id', $existingVoucher->id)->delete();

                // Add updated products to voucher
                foreach ($purchaseReturn->returnItems as $returnItem) {
                    VoucherProduct::create([
                        'quantity' => $returnItem->quantity_returned,
                        'purchasing_price' => $returnItem->unit_price,
                        'note' => 'مردود مشتريات - ' . ($returnItem->product->name_en ?? $returnItem->product->name_ar),
                        'product_id' => $returnItem->product_id,
                        'note_voucher_id' => $existingVoucher->id
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('admin.purchase-returns.index')
                ->with('success', __('messages.return_updated_successfully'));
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function destroy(PurchaseReturn $purchaseReturn)
    {
        try {
            // Delete associated note voucher
            $voucher = NoteVoucher::where('note', 'like', '%' . $purchaseReturn->number . '%')->first();
            if ($voucher) {
                VoucherProduct::where('note_voucher_id', $voucher->id)->delete();
                $voucher->delete();
            }

            $purchaseReturn->delete();
            return back()->with('success', __('messages.return_deleted_successfully'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function searchPurchases(Request $request)
    {
        $term = $request->get('term', '');
        $limit = $request->get('limit', 10);

        $purchases = Purchase::with('provider', 'items.product')
            ->where('status', 'received')
            ->where(function ($query) use ($term) {
                $query->where('purchase_number', 'like', "%{$term}%")
                    ->orWhere('purchases.id', 'like', "%{$term}%")
                    ->orWhereHas('provider', function ($providerQuery) use ($term) {
                        $providerQuery->where('name', 'like', "%{$term}%");
                    });
            })
            ->limit($limit)
            ->get()
            ->map(function ($purchase) {
                return [
                    'id' => $purchase->id,
                    'text' => $purchase->purchase_number . ' - ' . ($purchase->provider->name ?? 'N/A') . ' (' . $purchase->created_at->format('M d, Y') . ')',
                    'number' => $purchase->purchase_number,
                    'items' => $purchase->items->toArray(),
                ];
            });

        return response()->json($purchases);
    }
}
