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

    // Admin: List all orders with filters
    public function index(Request $request)
    {
        $query = Order::with(['user', 'orderProducts.product']);

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'pending') {
                // Pending = not completed, cancelled, or refunded
                $query->whereNotIn('status', [1, 2, 6]);
            } elseif ($request->status === 'not_done') {
                // Not done = any status except 1 (done)
                $query->where('status', '!=', 1);
            } else {
                $query->where('status', $request->status);
            }
        }

        // Filter by payment status
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by date (single date)
        if ($request->filled('date')) {
            $query->whereDate('order_date', $request->date);
        }

        // Filter by date range
        if ($request->filled('from_date')) {
            $query->whereDate('order_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('order_date', '<=', $request->to_date);
        }

        // Filter by order number
        if ($request->filled('order_number')) {
            $query->where('number', 'like', '%' . $request->order_number . '%');
        }

        // Filter by month (format: YYYY-MM)
        if ($request->filled('month')) {
            $monthYear = explode('-', $request->month);
            if (count($monthYear) === 2) {
                $query->whereYear('order_date', $monthYear[0])
                      ->whereMonth('order_date', $monthYear[1]);
            }
        }

        $orders = $query->latest()->paginate(15)->withQueryString();

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
            'order_date' => 'required|date',
            'status' => 'required|in:1,2,6',
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
                $quantity = (int)$productData['quantity'];
                
                // Tax Inclusive Pricing Logic
                // 1. Determine the canonical Total Price After Tax (what the user pays)
                $sellingPrice = (float)$product->selling_price;
                $totalPriceAfterTax = round($sellingPrice * $quantity, 2);

                // 2. Back-calculate the Pre-Tax Total
                $taxPercentage = (float)$product->tax;
                $taxDivisor = 1 + ($taxPercentage / 100);
                
                // We round the pre-tax total to 2 decimals as this is a financial record
                $totalPriceBeforeTax = round($totalPriceAfterTax / $taxDivisor, 2);
                
                // 3. Calculate Tax Value as the difference to ensure sum matches Total
                $taxValue = round($totalPriceAfterTax - $totalPriceBeforeTax, 2);
                
                // 4. Derive Unit Price (Before Tax) for reference
                $unitPriceBeforeTax = round($totalPriceBeforeTax / $quantity, 2);

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

            // Round the accumulators to ensure precision
            $totalTaxes = round($totalTaxes, 2);
            $totalPrices = round($totalPrices, 2);

            $paidAmount = round($request->paid_amount ?? 0, 2);
            $remainingAmount = round($totalPrices - $paidAmount, 2);

            // Create order
            $order = Order::create([
                'number' => 'ORD-' . time(),
                'status' => $request->status,
                'total_taxes' => $totalTaxes,
                'total_prices' => $totalPrices,
                'paid_amount' => $paidAmount,
                'remaining_amount' => $remainingAmount,
                'payment_status' => $remainingAmount > 0 ? 2 : 1,
                'order_type' => 1,
                'date' => now(),
                'order_date' => $request->order_date,
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

            // Create note voucher based on user role (seller or customer)
            $nextNoteVoucherNumber = (DB::table('note_vouchers')->max('number') ?? 0) + 1;
            $user = User::find($request->user_id);

            // Determine note voucher type and warehouse based on user role
            $noteVoucherData = [
                'number' => $nextNoteVoucherNumber,
                'date_note_voucher' => now()->toDateString(),
                'note' => __('messages.order_number') . ': ' . $order->number,
                'from_warehouse_id' => $request->from_warehouse_id,
                'order_id' => $order->id,
                'user_id' => $request->user_id, // Link to the user (seller or customer)
            ];

            if ($user && $user->hasRole('seller')) {
                // Seller: Create Transfer type (3) to seller's warehouse
                $sellerWarehouse = Warehouse::where('user_id', $request->user_id)->first();
                $noteVoucherData['to_warehouse_id'] = $sellerWarehouse ? $sellerWarehouse->id : null;
                $noteVoucherData['note_voucher_type_id'] = 3; // Transfer Note Voucher type
            } else {
                // Customer: Create Out type (2) without to_warehouse
                $noteVoucherData['note_voucher_type_id'] = 2; // Out Note Voucher type
            }

            $noteVoucher = NoteVoucher::create($noteVoucherData);

            // Create voucher products for each product in the order
            foreach ($orderProducts as $orderProduct) {
                $product = Product::find($orderProduct['product_id']);
                VoucherProduct::create([
                    'quantity' => $orderProduct['quantity'],
                    'purchasing_price' => $product ? $product->selling_price : null,
                    'tax_percentage' => $orderProduct['tax_percentage'],
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
        'from_warehouse_id' => 'required|exists:warehouses,id',
        'event_id' => 'nullable|exists:events,id',
        'order_date' => 'required|date',
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
            $quantity = (int)$productData['quantity'];
            
            // Tax Inclusive Pricing Logic
            $sellingPrice = (float)$product->selling_price;
            $totalPriceAfterTax = round($sellingPrice * $quantity, 2);

            $taxPercentage = (float)$product->tax;
            $taxDivisor = 1 + ($taxPercentage / 100);
            
            $totalPriceBeforeTax = round($totalPriceAfterTax / $taxDivisor, 2);
            $taxValue = round($totalPriceAfterTax - $totalPriceBeforeTax, 2);
            $unitPriceBeforeTax = round($totalPriceBeforeTax / $quantity, 2);

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

        // Round accumulators
        $totalTaxes = round($totalTaxes, 2);
        $totalPrices = round($totalPrices, 2);

        $paidAmount = round($request->paid_amount ?? 0, 2);
        $remainingAmount = round($totalPrices - $paidAmount, 2);

        // Update order
        $order->update([
            'status' => $request->status,
            'total_taxes' => $totalTaxes,
            'total_prices' => $totalPrices,
            'paid_amount' => $paidAmount,
            'remaining_amount' => $remainingAmount,
            'payment_status' => $remainingAmount <= 0 ? 1 : 2, // Strict check on rounded value
            'order_date' => $request->order_date,
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

        // Update existing note voucher if exists
        $existingNoteVoucher = NoteVoucher::where('order_id', $order->id)->first();
        if ($existingNoteVoucher) {
            $user = User::find($request->user_id);

            $updateData = [
                'from_warehouse_id' => $request->from_warehouse_id,
                'user_id' => $request->user_id,
            ];

            if ($user && $user->hasRole('seller')) {
                // Seller: Update to seller's warehouse
                $sellerWarehouse = Warehouse::where('user_id', $request->user_id)->first();
                $updateData['to_warehouse_id'] = $sellerWarehouse ? $sellerWarehouse->id : null;
                $updateData['note_voucher_type_id'] = 3; // Transfer
            } else {
                // Customer: Remove to_warehouse and set type to Out
                $updateData['to_warehouse_id'] = null;
                $updateData['note_voucher_type_id'] = 2; // Out
            }

            $existingNoteVoucher->update($updateData);

            // Update voucher products
            VoucherProduct::where('note_voucher_id', $existingNoteVoucher->id)->delete();

            foreach ($orderProducts as $orderProduct) {
                $product = Product::find($orderProduct['product_id']);
                VoucherProduct::create([
                    'quantity' => $orderProduct['quantity'],
                    'purchasing_price' => $product ? $product->selling_price : null,
                    'tax_percentage' => $orderProduct['tax_percentage'],
                    'note' => __('messages.product_outgoing_for_order'),
                    'product_id' => $orderProduct['product_id'],
                    'note_voucher_id' => $existingNoteVoucher->id
                ]);
            }
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
            // If fully paid, mark debt as paid
            if ($debt) {
                $debt->update([
                    'paid_amount' => $totalPrices,
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
     * @return \Illuminate\Http\JsonResponse
     */
public function getSellerEvents($sellerId)
{
    $seller = User::findOrFail($sellerId);
    $now = now();

    $events = Event::where('user_id', $seller->id)
        ->orderBy('start_date', 'desc')
        ->get()
        ->map(function ($event) use ($now) {
            $isValid = $event->start_date <= $now && $event->end_date >= $now;
            return [
                'id' => $event->id,
                'text' => $event->name,
                'is_valid' => $isValid,
                'commission_percentage' => $event->commission_percentage
            ];
        });

    return response()->json($events);
}


}