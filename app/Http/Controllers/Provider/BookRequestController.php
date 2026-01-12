<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\BookRequest;
use App\Models\BookRequestResponse;
use Illuminate\Http\Request;

class BookRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:web', 'role:provider']);
    }

    // عرض قائمة الطلبات الموجهة لهذا المورد
    public function index()
    {
        $user = auth()->user();
        $provider = $user->provider;
        $bookRequests = BookRequest::where('provider_id', $provider->id)
            ->with(['product.category', 'responses.provider'])
            ->get();
        return view('provider.bookRequests.index', compact('bookRequests', 'provider'));
    }

    // عرض تفاصيل الطلب
    public function show(BookRequest $bookRequest)
    {
        $user = auth()->user();
        $provider = $user->provider;

        // التحقق من أن الطلب موجه لهذا المورد
        if ($bookRequest->provider_id !== $provider->id) {
            abort(403);
        }

        $bookRequest->load(['product.category', 'responses.provider']);
        $hasResponse = $bookRequest->responses()
            ->where('provider_id', $provider->id)
            ->exists();

        return view('provider.bookRequests.show', compact('bookRequest', 'hasResponse', 'provider'));
    }

    // صفحة إنشاء الرد
    public function createResponse(BookRequest $bookRequest)
    {
        $user = auth()->user();
        $provider = $user->provider;

        if ($bookRequest->provider_id !== $provider->id) {
            abort(403);
        }

        $bookRequest->load('product');
        return view('provider.bookRequests.respond', compact('bookRequest'));
    }

    // حفظ الرد
    public function storeResponse(Request $request, BookRequest $bookRequest)
    {
        $user = auth()->user();
        $provider = $user->provider;

        if ($bookRequest->provider_id !== $provider->id) {
            abort(403);
        }

        $validated = $request->validate([
            'available_quantity' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'tax_percentage' => 'nullable|numeric|min:0|max:100',
            'note' => 'nullable|string',
        ]);

        // التحقق من عدم وجود رد سابق من هذا المورد
        $existingResponse = BookRequestResponse::where('book_request_id', $bookRequest->id)
            ->where('provider_id', $provider->id)
            ->first();

        if ($existingResponse) {
            return redirect()->back()->with('error', 'لقد قمت برد الفعل على هذا الطلب من قبل');
        }

        BookRequestResponse::create([
            'book_request_id' => $bookRequest->id,
            'provider_id' => $provider->id,
            'available_quantity' => $validated['available_quantity'],
            'price' => $validated['price'],
            'tax_percentage' => $validated['tax_percentage'] ?? 0,
            'status' => 'pending',
            'note' => $validated['note'] ?? null,
        ]);

        return redirect()->route('provider.bookRequests.show', $bookRequest)
            ->with('success', 'تم إرسال الرد بنجاح');
    }
}
