<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\Driver;
use App\Models\DriverAssignment;
use App\Models\DriverSchedule;
use App\Models\Event;
use App\Models\NoteVoucher;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\Service;
use App\Models\UserDept;
use App\Models\VoucherProduct;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:order-table')->only(['index']);
        $this->middleware('permission:order-add')->only(['create', 'store']);
        $this->middleware('permission:order-edit')->only(['edit', 'update']);
    }

        // Admin: List all orders
    public function index()
    {
        $orders = Order::with(['user', 'orderProducts.product'])->latest()->paginate(15);
        return view('admin.orders.index', compact('orders'));
    }

    // Admin: Show order details
    public function show(Order $order)
    {
        $order->load(['user', 'orderProducts.product', 'userDepts']);
        return view('admin.orders.show', compact('order'));
    }

   public function create()
    {
        $users = User::where('activate', 1)->get();
        $products = Product::all();
        return view('admin.orders.create', compact('users', 'products'));
    }

    // Admin: Store order
     public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'from_warehouse_id' => 'required|exists:warehouses,id',
            'event_id' => 'nullable|exists:events,id',
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'paid_amount' => 'nullable|numeric|min:0',
            'note' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            $totalTaxes = 0;
            $totalPrices = 0;
            $orderProducts = [];

            // Calculate totals
            foreach ($request->products as $productData) {
                $product = Product::find($productData['id']);
                $quantity = $productData['quantity'];
                $sellingPrice = $product->selling_price; // السعر مع الضريبة
                $taxPercentage = $product->tax;

                // حساب السعر بدون ضريبة من السعر مع الضريبة
                $unitPriceBeforeTax = $sellingPrice / (1 + ($taxPercentage / 100));
                $totalPriceBeforeTax = $unitPriceBeforeTax * $quantity;
                $taxValue = ($totalPriceBeforeTax * $taxPercentage) / 100;
                $totalPriceAfterTax = $totalPriceBeforeTax + $taxValue;
                
                $totalTaxes += $taxValue;
                $totalPrices += $totalPriceAfterTax;
                
                $orderProducts[] = [
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPriceBeforeTax,
                    'total_price_after_tax' => $totalPriceAfterTax,
                    'tax_percentage' => $taxPercentage,
                    'tax_value' => $taxValue,
                    'total_price_before_tax' => $totalPriceBeforeTax
                ];
            }

            $paidAmount = $request->paid_amount ?? 0;
            $remainingAmount = $totalPrices - $paidAmount;

            // Create order
            $order = Order::create([
                'number' => 'ORD-' . time(),
                'status' => 1,
                'total_taxes' => $totalTaxes,
                'total_prices' => $totalPrices,
                'paid_amount' => $paidAmount,
                'remaining_amount' => $remainingAmount,
                'payment_status' => $remainingAmount > 0 ? 2 : 1,
                'order_type' => 1,
                'date' => now(),
                'note' => $request->note,
                'user_id' => $request->user_id,
                'event_id' => $request->event_id
            ]);

            // Create order products
            foreach ($orderProducts as $orderProduct) {
                OrderProduct::create([
                    'order_id' => $order->id,
                    'product_id' => $orderProduct['product_id'],
                    'quantity' => $orderProduct['quantity'],
                    'unit_price' => $orderProduct['unit_price'],
                    'total_price_after_tax' => $orderProduct['total_price_after_tax'],
                    'tax_percentage' => $orderProduct['tax_percentage'],
                    'tax_value' => $orderProduct['tax_value'],
                    'total_price_before_tax' => $orderProduct['total_price_before_tax']
                ]);
            }

            // Create note voucher for outgoing transfer from selected warehouse
            $nextNoteVoucherNumber = (DB::table('note_vouchers')->max('number') ?? 0) + 1;

            // Create note voucher for outgoing (out from selected warehouse)
            $noteVoucher = NoteVoucher::create([
                'number' => $nextNoteVoucherNumber,
                'date_note_voucher' => now()->toDateString(),
                'note' => __('messages.order_number') . ': ' . $order->number,
                'from_warehouse_id' => $request->from_warehouse_id,
                'order_id' => $order->id,
                'note_voucher_type_id' => 2 // Out Note Voucher type
            ]);

            // Create voucher products for each product in the order
            foreach ($orderProducts as $orderProduct) {
                VoucherProduct::create([
                    'quantity' => $orderProduct['quantity'],
                    'purchasing_price' => null,
                    'note' => __('messages.product_outgoing_for_order'),
                    'product_id' => $orderProduct['product_id'],
                    'note_voucher_id' => $noteVoucher->id
                ]);
            }

            // Create debt record if remaining amount > 0
            if ($remainingAmount > 0) {
                UserDept::create([
                    'user_id' => $request->user_id,
                    'order_id' => $order->id,
                    'total_amount' => $totalPrices,
                    'paid_amount' => $paidAmount,
                    'remaining_amount' => $remainingAmount,
                    'status' => 1
                ]);
            }

            DB::commit();
            return redirect()->route('orders.index')->with('success', __('messages.order_created_successfully'));
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', __('messages.error_creating_order') . ': ' . $e->getMessage());
        }
    }


    public function edit(Order $order)
    {
        $users = User::where('activate', 1)->get();
        $products = Product::all();
        $order->load(['orderProducts.product', 'userDepts']);
        
        return view('admin.orders.edit', compact('order', 'users', 'products'));
    }

public function update(Request $request, Order $order)
{
    $request->validate([
        'user_id' => 'required|exists:users,id',
        'event_id' => 'nullable|exists:events,id',
        'products' => 'required|array',
        'products.*.id' => 'required|exists:products,id',
        'products.*.quantity' => 'required|integer|min:1',
        'paid_amount' => 'nullable|numeric|min:0',
        'note' => 'nullable|string',
        'status' => 'required|in:1,2,6'
    ]);

    DB::beginTransaction();
    try {
        $totalTaxes = 0;
        $totalPrices = 0;
        $orderProducts = [];

        // Calculate totals
        foreach ($request->products as $productData) {
            $product = Product::find($productData['id']);
            $quantity = $productData['quantity'];
            $sellingPrice = $product->selling_price; // السعر مع الضريبة
            $taxPercentage = $product->tax;

            // حساب السعر بدون ضريبة من السعر مع الضريبة
            $unitPriceBeforeTax = $sellingPrice / (1 + ($taxPercentage / 100));
            $totalPriceBeforeTax = $unitPriceBeforeTax * $quantity;
            $taxValue = ($totalPriceBeforeTax * $taxPercentage) / 100;
            $totalPriceAfterTax = $totalPriceBeforeTax + $taxValue;
            
            $totalTaxes += $taxValue;
            $totalPrices += $totalPriceAfterTax;
            
            $orderProducts[] = [
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_price' => $unitPriceBeforeTax,
                'total_price_after_tax' => $totalPriceAfterTax,
                'tax_percentage' => $taxPercentage,
                'tax_value' => $taxValue,
                'total_price_before_tax' => $totalPriceBeforeTax
            ];
        }

        $paidAmount = $request->paid_amount ?? 0;
        $remainingAmount = $totalPrices - $paidAmount;

        // Get existing order products for warehouse reversal
        $existingOrderProducts = OrderProduct::where('order_id', $order->id)->get();
        
        // Handle warehouse transfers - First reverse the old transfer
        $mainWarehouse = Warehouse::first(); // Get first available warehouse
        $newUserWarehouse = Warehouse::where('user_id', $request->user_id)->first();

   

        // Delete existing note voucher and voucher products for this order
        $existingNoteVoucher = NoteVoucher::where('order_id', $order->id)->first();
        if ($existingNoteVoucher) {
            VoucherProduct::where('note_voucher_id', $existingNoteVoucher->id)->delete();
            $existingNoteVoucher->delete();
        }

        // Update order
        $order->update([
            'status' => $request->status,
            'total_taxes' => $totalTaxes,
            'total_prices' => $totalPrices,
            'paid_amount' => $paidAmount,
            'remaining_amount' => $remainingAmount,
            'payment_status' => $remainingAmount > 0 ? 2 : 1,
            'note' => $request->note,
            'user_id' => $request->user_id,
            'event_id' => $request->event_id
        ]);

        // Delete existing order products
        OrderProduct::where('order_id', $order->id)->delete();

        // Create new order products
        foreach ($orderProducts as $orderProduct) {
            OrderProduct::create([
                'order_id' => $order->id,
                'product_id' => $orderProduct['product_id'],
                'quantity' => $orderProduct['quantity'],
                'unit_price' => $orderProduct['unit_price'],
                'total_price_after_tax' => $orderProduct['total_price_after_tax'],
                'tax_percentage' => $orderProduct['tax_percentage'],
                'tax_value' => $orderProduct['tax_value'],
                'total_price_before_tax' => $orderProduct['total_price_before_tax']
            ]);
        }

        // Create new note voucher for updated transfer
        $nextNoteVoucherNumber = DB::table('note_vouchers')->max('number') + 1;

        $noteVoucher = NoteVoucher::create([
            'number' => $nextNoteVoucherNumber,
            'date_note_voucher' => now()->toDateString(),
            'note' => 'تحديث تحويل بضاعة للطلب رقم: ' . $order->number,
            'from_warehouse_id' => $mainWarehouse->id,
            'to_warehouse_id' => $newUserWarehouse->id,
            'order_id' => $order->id,
            'note_voucher_type_id' => 2 // Out Note Voucher type
        ]);

        // Create voucher products for each updated product in the order
        foreach ($orderProducts as $orderProduct) {
            VoucherProduct::create([
                'quantity' => $orderProduct['quantity'],
                'purchasing_price' => null, // No price for transfer
                'note' => 'تحديث نقل من المستودع الرئيسي إلى مستودع المستخدم',
                'product_id' => $orderProduct['product_id'],
                'note_voucher_id' => $noteVoucher->id
            ]);
        }

        // Update or create debt record
        $debt = UserDept::where('order_id', $order->id)->first();
        
        if ($remainingAmount > 0) {
            if ($debt) {
                $debt->update([
                    'user_id' => $request->user_id,
                    'total_amount' => $totalPrices,
                    'paid_amount' => $paidAmount,
                    'remaining_amount' => $remainingAmount,
                    'status' => 1
                ]);
            } else {
                UserDept::create([
                    'user_id' => $request->user_id,
                    'order_id' => $order->id,
                    'total_amount' => $totalPrices,
                    'paid_amount' => $paidAmount,
                    'remaining_amount' => $remainingAmount,
                    'status' => 1
                ]);
            }
        } else {
            // If fully paid, mark debt as paid or delete it
            if ($debt) {
                $debt->update([
                    'paid_amount' => $debt->total_amount,
                    'remaining_amount' => 0,
                    'status' => 2
                ]);
            }
        }

        DB::commit();
        return redirect()->route('orders.index')->with('success', __('messages.order_updated_successfully'));
    } catch (\Exception $e) {
        DB::rollback();
        return back()->with('error', __('messages.error_updating_order') . ': ' . $e->getMessage());
    }
}

/**
 * Get seller events with valid dates (API endpoint)
 *
 * @param int $sellerId
 * @return \Illuminate\Http\Response
 */
public function getSellerEvents($sellerId)
{
    $seller = User::findOrFail($sellerId);
    $now = now();

    $events = Event::where('user_id', $seller->id)
        ->where(function ($query) use ($now) {
            $query->where('start_date', '<=', $now)
                ->where('end_date', '>=', $now);
        })
        ->get()
        ->map(function ($event) {
            return [
                'id' => $event->id,
                'text' => $event->name
            ];
        });

    return response()->json($events);
}


}