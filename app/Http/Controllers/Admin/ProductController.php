<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{

    public function search(Request $request)
    {
        $query = $request->input('term');
        $products = Product::where('name_ar', 'LIKE', "%{$query}%")
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name_ar,
                    'tax' => $product->tax,
                    'selling_price' => $product->selling_price_for_user,
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
        $providers = DB::table('providers')->get(); 
        return view('admin.products.create', compact('categories', 'providers'));
    }


   public function store(Request $request)
    {
        $request->validate([
            'name_en'       => 'required|string|max:255',
            'name_ar'       => 'required|string|max:255',
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
            'category_id'   => $request->category_id,
            'provider_id'   => $request->provider_id,
            'selling_price' => $request->selling_price,
            'tax'           => $request->tax ?? 16,
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
        $providers = DB::table('providers')->get();

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
            'category_id'   => $request->category_id,
            'provider_id'   => $request->provider_id,
            'selling_price' => $request->selling_price,
            'tax'           => $request->tax ?? 16,
            'photo'         => $photoPath,
            'updated_at'    => now(),
        ]);

        return redirect()->route('products.index')->with('success', __('messages.Product_Updated'));
    }

   

}