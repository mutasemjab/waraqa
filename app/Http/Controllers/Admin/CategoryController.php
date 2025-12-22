<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:category-table')->only(['index']);
        $this->middleware('permission:category-add')->only(['create', 'store']);
        $this->middleware('permission:category-edit')->only(['edit', 'update']);
    }

    public function index()
    {
        $categories =Category::get();
        
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
        ]);

        DB::table('categories')->insert([
            'name_en' => $request->name_en,
            'name_ar' => $request->name_ar,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('categories.index')->with('success', __('messages.Category_Created'));
    }

    public function edit($id)
    {
        $category = DB::table('categories')->where('id', $id)->first();
        
        if (!$category) {
            return redirect()->route('categories.index')->with('error', __('messages.Category_Not_Found'));
        }


        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
        ]);

        DB::table('categories')->where('id', $id)->update([
            'name_en' => $request->name_en,
            'name_ar' => $request->name_ar,
            'updated_at' => now(),
        ]);

        return redirect()->route('categories.index')->with('success', __('messages.Category_Updated'));
    }
}