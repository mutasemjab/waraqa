<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\NoteVoucher;
use App\Models\VoucherProduct;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\NoteVoucherType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UserSalesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:web');
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $userWarehouse = $user->warehouse;

        if (!$userWarehouse) {
            return redirect()->route('user.dashboard')->with('error', __('messages.no_warehouse_assigned'));
        }

        // Get user's sales (note vouchers where from_warehouse is user's warehouse)
        $query = NoteVoucher::with(['voucherProducts.product', 'noteVoucherType'])
            ->where('from_warehouse_id', $userWarehouse->id)
            ->whereHas('noteVoucherType', function($q) {
                $q->where('in_out_type', 2); // Out type (sales)
            });

        // Filter by date
        if ($request->filled('date_from')) {
            $query->whereDate('date_note_voucher', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date_note_voucher', '<=', $request->date_to);
        }

        // Search by note voucher number
        if ($request->filled('search')) {
            $query->where('number', 'like', '%' . $request->search . '%');
        }

        $sales = $query->latest('date_note_voucher')->paginate(10);

        // Calculate statistics
        $stats = [
            'total_sales' => NoteVoucher::where('from_warehouse_id', $userWarehouse->id)
                ->whereHas('noteVoucherType', function($q) {
                    $q->where('in_out_type', 2);
                })->count(),
            'total_items_sold' => VoucherProduct::whereHas('noteVoucher', function($q) use ($userWarehouse) {
                $q->where('from_warehouse_id', $userWarehouse->id)
                  ->whereHas('noteVoucherType', function($qt) {
                      $qt->where('in_out_type', 2);
                  });
            })->sum('quantity'),
            'this_month_sales' => NoteVoucher::where('from_warehouse_id', $userWarehouse->id)
                ->whereHas('noteVoucherType', function($q) {
                    $q->where('in_out_type', 2);
                })
                ->whereMonth('date_note_voucher', Carbon::now()->month)
                ->whereYear('date_note_voucher', Carbon::now()->year)
                ->count(),
            'current_inventory' => $this->getCurrentInventoryCount($userWarehouse->id),
        ];

        return view('user.sales.index', compact('sales', 'stats'));
    }

    public function create()
    {
        $user = Auth::user();
        $userWarehouse = $user->warehouse;

        if (!$userWarehouse) {
            return redirect()->route('user.dashboard')->with('error', __('messages.no_warehouse_assigned'));
        }

        // Get user's available products (products they have in their warehouse)
        $availableProducts = $this->getUserAvailableProducts($userWarehouse->id);

        // Get sales note voucher type
        $salesVoucherType = NoteVoucherType::where('in_out_type', 2)->first();

        return view('user.sales.create', compact('availableProducts', 'salesVoucherType'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $userWarehouse = $user->warehouse;

        if (!$userWarehouse) {
            return redirect()->route('user.dashboard')->with('error', __('messages.no_warehouse_assigned'));
        }

        $request->validate([
            'date_note_voucher' => 'required|date',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.selling_price' => 'nullable|numeric|min:0',
            'note' => 'nullable|string'
        ]);

        // Check if user has enough inventory for all products
        $availableProducts = $this->getUserAvailableProducts($userWarehouse->id);
        
        foreach ($request->products as $productData) {
            $availableProduct = $availableProducts->where('id', $productData['product_id'])->first();
            if (!$availableProduct || $availableProduct->available_quantity < $productData['quantity']) {
                return back()->withErrors([
                    'products' => __('messages.insufficient_inventory_for_product', [
                        'product' => Product::find($productData['product_id'])->name_ar,
                        'available' => $availableProduct ? $availableProduct->available_quantity : 0,
                        'requested' => $productData['quantity']
                    ])
                ])->withInput();
            }
        }

        DB::beginTransaction();
        try {
            // Get next note voucher number
            $nextNumber = NoteVoucher::max('number') + 1;

            // Get sales voucher type
            $salesVoucherType = NoteVoucherType::where('in_out_type', 2)->first();

            // Create note voucher for sale (OUT from user warehouse)
            $noteVoucher = NoteVoucher::create([
                'number' => $nextNumber,
                'date_note_voucher' => $request->date_note_voucher,
                'note' => $request->note ?: 'Sale recorded by user',
                'from_warehouse_id' => $userWarehouse->id,
                'to_warehouse_id' => null, // Sale to customer (no warehouse)
                'order_id' => null,
                'note_voucher_type_id' => $salesVoucherType->id
            ]);

            // Create voucher products
            foreach ($request->products as $productData) {
                VoucherProduct::create([
                    'note_voucher_id' => $noteVoucher->id,
                    'product_id' => $productData['product_id'],
                    'quantity' => $productData['quantity'],
                    'purchasing_price' => $productData['selling_price'] ?? null,
                    'note' => 'User sale record'
                ]);
            }

            DB::commit();

            return redirect()->route('user.sales.index')->with('success', __('messages.sale_recorded_successfully'));

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', __('messages.error_recording_sale') . ': ' . $e->getMessage())->withInput();
        }
    }

 

 

    private function getUserAvailableProducts($warehouseId)
    {
        // Get products received in warehouse (IN)
        $receivedProducts = VoucherProduct::select('product_id')
            ->selectRaw('SUM(quantity) as received_quantity')
            ->whereHas('noteVoucher', function($q) use ($warehouseId) {
                $q->where('to_warehouse_id', $warehouseId);
            })
            ->groupBy('product_id');

        // Get products sold from warehouse (OUT)
        $soldProducts = VoucherProduct::select('product_id')
            ->selectRaw('SUM(quantity) as sold_quantity')
            ->whereHas('noteVoucher', function($q) use ($warehouseId) {
                $q->where('from_warehouse_id', $warehouseId);
            })
            ->groupBy('product_id');

        // Calculate available inventory
        $products = Product::select('products.*')
            ->leftJoinSub($receivedProducts, 'received', function($join) {
                $join->on('products.id', '=', 'received.product_id');
            })
            ->leftJoinSub($soldProducts, 'sold', function($join) {
                $join->on('products.id', '=', 'sold.product_id');
            })
            ->selectRaw('COALESCE(received.received_quantity, 0) - COALESCE(sold.sold_quantity, 0) as available_quantity')
            ->having('available_quantity', '>', 0)
            ->with('category')
            ->get();

        return $products;
    }

    private function getCurrentInventoryCount($warehouseId)
    {
        $received = VoucherProduct::whereHas('noteVoucher', function($q) use ($warehouseId) {
            $q->where('to_warehouse_id', $warehouseId);
        })->sum('quantity');

        $sold = VoucherProduct::whereHas('noteVoucher', function($q) use ($warehouseId) {
            $q->where('from_warehouse_id', $warehouseId);
        })->sum('quantity');

        return $received - $sold;
    }

 
}