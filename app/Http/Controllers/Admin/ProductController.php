<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Provider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:product-table')->only(['index']);
        $this->middleware('permission:product-add')->only(['create', 'store']);
        $this->middleware('permission:product-edit')->only(['edit', 'update']);
        $this->middleware('permission:product-search')->only(['search']);
        $this->middleware('permission:product-available-quantity')->only(['availableQuantity']);
    }

    public function search(Request $request)
    {
        $query = $request->input('term');
        $products = Product::where('name_ar', 'LIKE', "%{$query}%")
            ->orWhere('name_en', 'LIKE', "%{$query}%")
            ->limit(10)
            ->get()
            ->map(function ($product) {
                $tax = $product->tax ?? 15;
                $priceWithoutTax = $product->selling_price / (1 + ($tax / 100));

                return [
                    'id' => $product->id,
                    'name' => app()->getLocale() === 'ar' ? $product->name_ar : $product->name_en,
                    'tax' => $tax,
                    'selling_price' => $product->selling_price,
                    'price_without_tax' => $priceWithoutTax,
                ];
            });

        return response()->json($products);
    }


    public function index()
    {
        $products = Product::get();
        
        return view('admin.products.index', compact('products'));
    }

   public function create()
    {
        $categories = DB::table('categories')->get();
        $providers = Provider::with('user')->get();
        return view('admin.products.create', compact('categories', 'providers'));
    }


   public function store(Request $request)
    {
        $request->validate([
            'name_en'       => 'required|string|max:255',
            'name_ar'       => 'required|string|max:255',
            'sku'           => 'nullable|string|max:255',
            'category_id'   => 'nullable|exists:categories,id',
            'provider_id'   => 'nullable|exists:providers,id',
            'selling_price' => 'required|numeric|min:0',
            'tax'           => 'nullable|numeric|min:0',
            'photo'         => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
          $photoPath = uploadImage('assets/admin/uploads', $request->file('photo'));
        }

        DB::table('products')->insert([
            'name_en'       => $request->name_en,
            'name_ar'       => $request->name_ar,
            'sku'           => $request->sku,
            'category_id'   => $request->category_id,
            'provider_id'   => $request->provider_id,
            'selling_price' => $request->selling_price,
            'tax'           => $request->tax ?? 15,
            'photo'         => $photoPath,
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        return redirect()->route('products.index')->with('success', __('messages.Product_Created'));
    }

    public function edit($id)
    {
        $product = DB::table('products')->where('id', $id)->first();

        if (!$product) {
            return redirect()->route('products.index')->with('error', __('messages.Product_Not_Found'));
        }

        $categories = DB::table('categories')->get();
        $providers = Provider::with('user')->get();

        return view('admin.products.edit', compact('product', 'categories', 'providers'));
    }


    public function update(Request $request, $id)
    {
        $product = DB::table('products')->where('id', $id)->first();

        if (!$product) {
            return redirect()->route('products.index')->with('error', __('messages.Product_Not_Found'));
        }

        $request->validate([
            'name_en'       => 'required|string|max:255',
            'name_ar'       => 'required|string|max:255',
            'sku'           => 'nullable|string|max:255',
            'category_id'   => 'nullable|exists:categories,id',
            'provider_id'   => 'nullable|exists:providers,id',
            'selling_price' => 'required|numeric|min:0',
            'tax'           => 'nullable|numeric|min:0',
            'photo'         => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $photoPath = $product->photo;

        if ($request->hasFile('photo')) {
          $photoPath = uploadImage('assets/admin/uploads', $request->file('photo'));
        }

        DB::table('products')->where('id', $id)->update([
            'name_en'       => $request->name_en,
            'name_ar'       => $request->name_ar,
            'sku'           => $request->sku,
            'category_id'   => $request->category_id,
            'provider_id'   => $request->provider_id,
            'selling_price' => $request->selling_price,
            'tax'           => $request->tax ?? 15,
            'photo'         => $photoPath,
            'updated_at'    => now(),
        ]);

        return redirect()->route('products.index')->with('success', __('messages.Product_Updated'));
    }

    public function availableQuantity($productId)
    {
        $warehouseId = request()->query('warehouse_id');
        $excludeVoucherId = request()->query('exclude_voucher_id');

        // warehouse_id is required for filtering
        if (!$warehouseId) {
            return response()->json([
                'available_quantity' => 0,
                'message' => __('messages.warehouse_required')
            ], 400);
        }

        // Build base query for getting input (receipt) quantities
        $inputQuery = DB::table('voucher_products')
            ->join('note_vouchers', 'voucher_products.note_voucher_id', '=', 'note_vouchers.id')
            ->join('note_voucher_types', 'note_vouchers.note_voucher_type_id', '=', 'note_voucher_types.id')
            ->where('voucher_products.product_id', $productId)
            ->where('note_voucher_types.in_out_type', 1) // Input/Receipt vouchers
            ->where('note_vouchers.to_warehouse_id', $warehouseId);

        $totalInput = $inputQuery->sum('voucher_products.quantity');

        // Build base query for getting output quantities
        $outputQuery = DB::table('voucher_products')
            ->join('note_vouchers', 'voucher_products.note_voucher_id', '=', 'note_vouchers.id')
            ->join('note_voucher_types', 'note_vouchers.note_voucher_type_id', '=', 'note_voucher_types.id')
            ->where('voucher_products.product_id', $productId)
            ->whereIn('note_voucher_types.in_out_type', [2, 3]) // Output and Transfer vouchers
            ->where('note_vouchers.from_warehouse_id', $warehouseId);

        // Exclude the current voucher if provided (useful when editing)
        if ($excludeVoucherId) {
            $outputQuery->where('note_vouchers.id', '!=', $excludeVoucherId);
        }

        $totalOutput = $outputQuery->sum('voucher_products.quantity');

        // Available quantity = Input - Output
        $availableQuantity = ($totalInput ?? 0) - ($totalOutput ?? 0);

        return response()->json([
            'available_quantity' => max(0, $availableQuantity) // Ensure non-negative
        ]);
    }

}