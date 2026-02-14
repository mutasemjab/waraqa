<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NoteVoucher;
use App\Models\VoucherProduct;
use App\Models\Product;
use App\Models\User;
use App\Models\SellerSale;
use App\Models\SellerSaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SellerSalesController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:admin-seller-sales-list')->only(['index']);
        $this->middleware('permission:admin-seller-sales-create')->only(['create', 'store']);
        $this->middleware('permission:admin-seller-sales-view')->only(['show']);
    }

    /**
     * Display a listing of all seller sales
     */
    public function index(Request $request)
    {
        $query = SellerSale::with('user');

        // Filter by seller
        if ($request->filled('seller_id')) {
            $query->where('user_id', $request->seller_id);
        }

        // Filter by date
        if ($request->filled('date_from')) {
            $query->whereDate('sale_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('sale_date', '<=', $request->date_to);
        }

        // Search by sale number
        if ($request->filled('search')) {
            $query->where('sale_number', 'like', '%' . $request->search . '%');
        }

        $sales = $query->latest('sale_date')->paginate(20);

        // Get sellers for filter dropdown
        $sellers = User::role('seller')
            ->whereHas('warehouse')
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return view('admin.sellerSales.index', compact('sales', 'sellers'));
    }

    /**
     * Show the form for creating a new seller sale
     */
    public function create()
    {
        // Get sellers with warehouses
        $sellers = User::role('seller')
            ->whereHas('warehouse')
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'commission_percentage']);

        return view('admin.sellerSales.create', compact('sellers'));
    }

    /**
     * Store a newly created seller sale in database
     */
    public function store(Request $request)
    {
        $request->validate([
            'seller_id' => 'required|exists:users,id',
            'sale_date' => 'required|date',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.unit_price' => 'required|numeric|min:0',
            'products.*.tax_percentage' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string'
        ]);

        // Get selected seller and validate
        $seller = User::findOrFail($request->seller_id);
        $userWarehouse = $seller->warehouse;

        if (!$userWarehouse) {
            return back()->with('error', __('messages.seller_has_no_warehouse'))->withInput();
        }

        // Check if seller has enough inventory for all products
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
            // Generate unique sale number using database locking
            $saleNumber = DB::transaction(function () {
                $setting = DB::table('settings')
                    ->where('key', 'last_seller_sale_number')
                    ->lockForUpdate()
                    ->first();

                if ($setting) {
                    $newNumber = $setting->value + 1;
                    DB::table('settings')
                        ->where('key', 'last_seller_sale_number')
                        ->update(['value' => $newNumber]);
                } else {
                    $newNumber = 1001;
                    DB::table('settings')->insert([
                        'key' => 'last_seller_sale_number',
                        'value' => $newNumber,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }

                return 'PO-' . $newNumber;
            });

            // Calculate totals
            $totalAmount = 0;
            $totalTax = 0;

            // Create seller sale with selected seller
            $sellerSale = SellerSale::create([
                'user_id' => $seller->id,
                'sale_number' => $saleNumber,
                'sale_date' => $request->sale_date,
                'notes' => $request->notes,
                'total_amount' => 0,
                'total_tax' => 0
            ]);

            // Create sale items
            foreach ($request->products as $productData) {
                $product = Product::find($productData['product_id']);
                $quantity = (int)$productData['quantity'];
                $unitPrice = (float)$productData['unit_price'];
                $taxPercentage = (float)($productData['tax_percentage'] ?? 0);

                // Since unitPrice is inclusive of tax
                $totalPriceAfterTax = round($quantity * $unitPrice, 2);

                // Calculate price before tax and tax amount
                if ($taxPercentage > 0) {
                    $totalPriceBeforeTax = round($totalPriceAfterTax / (1 + ($taxPercentage / 100)), 2);
                    $tax = round($totalPriceAfterTax - $totalPriceBeforeTax, 2);
                } else {
                    $totalPriceBeforeTax = $totalPriceAfterTax;
                    $tax = 0;
                }

                SellerSaleItem::create([
                    'seller_sale_id' => $sellerSale->id,
                    'product_id' => $product->id,
                    'product_name' => app()->getLocale() == 'ar' ? $product->name_ar : $product->name_en,
                    'product_code' => $product->code,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'tax_percentage' => $taxPercentage,
                    'total_price_before_tax' => $totalPriceBeforeTax,
                    'total_tax' => $tax,
                    'total_price_after_tax' => $totalPriceAfterTax
                ]);

                $totalAmount += $totalPriceAfterTax;
                $totalTax += $tax;
            }

            // Update seller sale totals
            $sellerSale->update([
                'total_amount' => round($totalAmount, 2),
                'total_tax' => round($totalTax, 2)
            ]);

            // Create automatic exit note voucher for inventory deduction
            $numberOnly = (int)str_replace('PO-', '', $saleNumber);

            $noteVoucher = NoteVoucher::create([
                'number' => $numberOnly,
                'date_note_voucher' => $request->sale_date,
                'from_warehouse_id' => $userWarehouse->id,
                'note_voucher_type_id' => 2, // OUT type
                'note' => 'الموزع: ' . $userWarehouse->name . ' | رقم المبيعة: ' . $saleNumber
            ]);

            // Create voucher products for inventory deduction
            foreach ($request->products as $productData) {
                VoucherProduct::create([
                    'note_voucher_id' => $noteVoucher->id,
                    'product_id' => $productData['product_id'],
                    'quantity' => (int)$productData['quantity'],
                    'purchasing_price' => (float)$productData['unit_price'],
                    'tax_percentage' => (float)($productData['tax_percentage'] ?? 0)
                ]);
            }

            DB::commit();

            return redirect()->route('admin.seller-sales.index')
                ->with('success', __('messages.sale_recorded_successfully') . ' - ' . $saleNumber);

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', __('messages.error_recording_sale') . ': ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified seller sale
     */
    public function show($id)
    {
        $sale = SellerSale::with(['user', 'items.product'])->findOrFail($id);
        return view('admin.sellerSales.show', compact('sale'));
    }

    /**
     * Get available products for a seller (AJAX endpoint)
     */
    public function getSellerProducts($sellerId)
    {
        $seller = User::findOrFail($sellerId);
        $userWarehouse = $seller->warehouse;

        if (!$userWarehouse) {
            return response()->json(['error' => __('messages.seller_has_no_warehouse')], 422);
        }

        $availableProducts = $this->getUserAvailableProducts($userWarehouse->id);

        return response()->json($availableProducts);
    }

    /**
     * Get available products for a warehouse
     */
    private function getUserAvailableProducts($warehouseId)
    {
        $products = Product::all();
        $availableProducts = collect();

        foreach ($products as $product) {
            // Products received in warehouse
            $received = VoucherProduct::whereHas('noteVoucher', function($q) use ($warehouseId) {
                $q->where('to_warehouse_id', $warehouseId);
            })->where('product_id', $product->id)->sum('quantity');

            // Products sold from warehouse
            $sold = VoucherProduct::whereHas('noteVoucher', function($q) use ($warehouseId) {
                $q->where('from_warehouse_id', $warehouseId);
            })->where('product_id', $product->id)->sum('quantity');

            // Available quantity
            $availableQuantity = $received - $sold;

            // Add only if available
            if ($availableQuantity > 0) {
                $product->available_quantity = $availableQuantity;
                $availableProducts->push($product);
            }
        }

        return $availableProducts;
    }
}
