<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookRequest;
use App\Models\BookRequestResponse;
use App\Models\Product;
use App\Models\Provider;
use Illuminate\Http\Request;

class BookRequestController extends Controller
{
    // عرض قائمة الطلبات
    public function index()
    {
        $bookRequests = BookRequest::with(['product', 'provider', 'responses'])->get();
        return view('admin.bookRequests.index', compact('bookRequests'));
    }

    // صفحة إنشاء طلب جديد
    public function create()
    {
        $products = Product::all();
        $providers = Provider::all();
        return view('admin.bookRequests.create', compact('products', 'providers'));
    }

    // حفظ الطلب الجديد
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'provider_id' => 'required|exists:providers,id',
            'requested_quantity' => 'required|integer|min:1',
        ]);

        $bookRequest = BookRequest::create($validated);

        return redirect()->route('admin.bookRequests.show', $bookRequest)->with('success', 'تم إنشاء الطلب بنجاح');
    }

    // عرض تفاصيل الطلب والردود
    public function show(BookRequest $bookRequest)
    {
        $bookRequest->load(['product', 'provider', 'responses' => function ($query) {
            $query->with('provider');
        }]);
        return view('admin.bookRequests.show', compact('bookRequest'));
    }

    // تحديث الطلب
    public function edit(BookRequest $bookRequest)
    {
        $products = Product::all();
        $providers = Provider::all();
        return view('admin.bookRequests.edit', compact('bookRequest', 'products', 'providers'));
    }

    public function update(Request $request, BookRequest $bookRequest)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'provider_id' => 'required|exists:providers,id',
            'requested_quantity' => 'required|integer|min:1',
        ]);

        $bookRequest->update($validated);

        return redirect()->route('admin.bookRequests.show', $bookRequest)->with('success', 'تم تحديث الطلب بنجاح');
    }

    // حذف الطلب
    public function destroy(BookRequest $bookRequest)
    {
        $bookRequest->delete();
        return redirect()->route('admin.bookRequests.index')->with('success', 'تم حذف الطلب بنجاح');
    }
}
