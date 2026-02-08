<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\NoteVoucher;
use App\Models\VoucherProduct;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\SellerSale;
use App\Models\SellerSaleItem;
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

        // Get user's sales from SellerSale
        $query = SellerSale::with('items');

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

        $sales = $query->latest('sale_date')->paginate(10);

        // Calculate statistics
        $allSales = SellerSale::all();
        $stats = [
            'total_sales' => $allSales->count(),
            'total_items_sold' => SellerSaleItem::sum('quantity'),
            'this_month_sales' => SellerSale::whereMonth('sale_date', Carbon::now()->month)
                ->whereYear('sale_date', Carbon::now()->year)
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

        return view('user.sales.create', compact('availableProducts'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $userWarehouse = $user->warehouse;

        if (!$userWarehouse) {
            return redirect()->route('user.dashboard')->with('error', __('messages.no_warehouse_assigned'));
        }

        $request->validate([
            'sale_date' => 'required|date',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'customer_email' => 'nullable|email',
            'customer_address' => 'nullable|string',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.unit_price' => 'required|numeric|min:0',
            'products.*.tax_percentage' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string'
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

            // Create seller sale
            $sellerSale = SellerSale::create([
                'sale_number' => $saleNumber,
                'sale_date' => $request->sale_date,
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'customer_email' => $request->customer_email,
                'customer_address' => $request->customer_address,
                'notes' => $request->notes,
                'total_amount' => 0, // Will be updated after items
                'total_tax' => 0 // Will be updated after items
            ]);

            // Create sale items
            foreach ($request->products as $productData) {
                $product = Product::find($productData['product_id']);
                $quantity = (int)$productData['quantity'];
                $unitPrice = (float)$productData['unit_price']; // This price is inclusive of tax
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
            // Extract number from sale number (e.g., 'PO-1001' -> 1001)
            $numberOnly = (int)str_replace('PO-', '', $saleNumber);

            $noteVoucher = NoteVoucher::create([
                'number' => $numberOnly,
                'date_note_voucher' => $request->sale_date,
                'from_warehouse_id' => $userWarehouse->id,
                'note_voucher_type_id' => 2, // OUT type
                'note' => 'بيع من نقطة التوزيع - ' . $request->customer_name . ' | Sale from Distribution Point - ' . $request->customer_name
            ]);

            // Create voucher products for inventory deduction
            foreach ($request->products as $productData) {
                VoucherProduct::create([
                    'note_voucher_id' => $noteVoucher->id,
                    'product_id' => $productData['product_id'],
                    'quantity' => (int)$productData['quantity'],
                    'purchasing_price' => (float)$productData['unit_price'], // السعر الشامل الضريبة
                    'tax_percentage' => (float)($productData['tax_percentage'] ?? 0)
                ]);
            }

            DB::commit();

            return redirect()->route('user.sales.index')->with('success', __('messages.sale_recorded_successfully') . ' - ' . $saleNumber);

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', __('messages.error_recording_sale') . ': ' . $e->getMessage())->withInput();
        }
    }

 

 

    private function getUserAvailableProducts($warehouseId)
    {
        $products = Product::all();
        $availableProducts = collect();

        foreach ($products as $product) {
            // الكمية المستقبلة في المستودع
            $received = VoucherProduct::whereHas('noteVoucher', function($q) use ($warehouseId) {
                $q->where('to_warehouse_id', $warehouseId);
            })->where('product_id', $product->id)->sum('quantity');

            // الكمية المباعة من المستودع
            $sold = VoucherProduct::whereHas('noteVoucher', function($q) use ($warehouseId) {
                $q->where('from_warehouse_id', $warehouseId);
            })->where('product_id', $product->id)->sum('quantity');

            // الكمية المتاحة
            $availableQuantity = $received - $sold;

            // إضافة فقط إذا كانت متاحة
            if ($availableQuantity > 0) {
                $product->available_quantity = $availableQuantity;
                $availableProducts->push($product);
            }
        }

        return $availableProducts;
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

    public function show($id)
    {
        $sale = SellerSale::with('items.product')->findOrFail($id);

        return view('user.sales.show', compact('sale'));
    }

    public function warehouse()
    {
        $user = Auth::user();
        $warehouse = $user->warehouse;
        $locale = app()->getLocale();
        $nameColumn = $locale === 'ar' ? 'name_ar' : 'name_en';

        if (!$warehouse) {
            return view('user.warehouse.no-warehouse');
        }

        // Get all products with their quantities in this warehouse
        $products = DB::table('voucher_products')
            ->join('note_vouchers', 'voucher_products.note_voucher_id', '=', 'note_vouchers.id')
            ->join('note_voucher_types', 'note_vouchers.note_voucher_type_id', '=', 'note_voucher_types.id')
            ->join('products', 'voucher_products.product_id', '=', 'products.id')
            ->select(
                'voucher_products.product_id',
                DB::raw('products.' . $nameColumn . ' as product_name'),
                DB::raw('SUM(CASE
                    WHEN (note_voucher_types.in_out_type = 1 AND note_vouchers.to_warehouse_id = ' . $warehouse->id . ')
                    OR (note_voucher_types.in_out_type = 3 AND note_vouchers.to_warehouse_id = ' . $warehouse->id . ')
                    THEN voucher_products.quantity
                    ELSE 0
                END) as input_quantity'),
                DB::raw('SUM(CASE
                    WHEN note_voucher_types.in_out_type IN (2, 3) AND note_vouchers.from_warehouse_id = ' . $warehouse->id . '
                    THEN voucher_products.quantity
                    ELSE 0
                END) as output_quantity')
            )
            ->where(function($query) use ($warehouse) {
                $query->where(function($q) use ($warehouse) {
                    $q->where('note_voucher_types.in_out_type', 1)
                      ->where('note_vouchers.to_warehouse_id', $warehouse->id);
                })
                ->orWhere(function($q) use ($warehouse) {
                    $q->where('note_voucher_types.in_out_type', 3)
                      ->where('note_vouchers.to_warehouse_id', $warehouse->id);
                })
                ->orWhere(function($q) use ($warehouse) {
                    $q->whereIn('note_voucher_types.in_out_type', [2, 3])
                      ->where('note_vouchers.from_warehouse_id', $warehouse->id);
                });
            })
            ->groupBy('voucher_products.product_id', 'products.' . $nameColumn)
            ->get();

        return view('user.warehouse.show', compact('warehouse', 'products'));
    }

    public function createWarehouse()
    {
        $user = Auth::user();

        // Check if user already has a warehouse
        if ($user->warehouse) {
            return redirect()->route('user.warehouse')->with('info', __('messages.you_already_have_warehouse'));
        }

        // Create a new warehouse with user's name
        $warehouse = new Warehouse();
        $warehouse->name = $user->name;
        $warehouse->user_id = $user->id;
        $warehouse->save();

        return redirect()->route('user.warehouse')->with('success', __('messages.warehouse_created_successfully'));
    }


}