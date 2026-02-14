<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\BookRequestItem;
use App\Models\BookRequestResponse;
use Illuminate\Http\Request;

class BookRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:web', 'role:provider']);
    }

    // صفحة إنشاء الرد على عنصر من طلب الكتب
    public function createResponse(BookRequestItem $bookRequestItem)
    {
        $user = auth()->user();
        $provider = $user->provider;

        // التحقق من أن العنصر موجود في طلب موجه لهذا المورد
        if ($bookRequestItem->bookRequest->provider_id !== $provider->id) {
            abort(403);
        }

        $bookRequestItem->load(['product', 'bookRequest']);
        $hasResponse = $bookRequestItem->responses()
            ->where('provider_id', $provider->id)
            ->exists();

        return view('provider.bookRequests.respond', compact('bookRequestItem', 'hasResponse'));
    }

    // حفظ الرد على عنصر من طلب الكتب
    public function storeResponse(Request $request, BookRequestItem $bookRequestItem)
    {
        $user = auth()->user();
        $provider = $user->provider;

        if ($bookRequestItem->bookRequest->provider_id !== $provider->id) {
            abort(403);
        }

        $validated = $request->validate([
            'available_quantity' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'tax_percentage' => 'nullable|numeric|min:0|max:100',
            'expected_delivery_date' => 'nullable|date',
            'note' => 'nullable|string',
        ]);

        // التحقق من عدم وجود رد سابق من هذا المورد على نفس العنصر
        $existingResponse = BookRequestResponse::where('book_request_item_id', $bookRequestItem->id)
            ->where('provider_id', $provider->id)
            ->first();

        if ($existingResponse) {
            return redirect()->back()->with('error', 'لقد قمت برد الفعل على هذا العنصر من قبل');
        }

        $response = BookRequestResponse::create([
            'book_request_item_id' => $bookRequestItem->id,
            'provider_id' => $provider->id,
            'available_quantity' => $validated['available_quantity'],
            'price' => $validated['price'],
            'tax_percentage' => $validated['tax_percentage'] ?? 0,
            'status' => 'pending',
            'note' => $validated['note'] ?? null,
            'expected_delivery_date' => $validated['expected_delivery_date'] ?? null,
        ]);

        // Update the purchase with the book request response
        $purchase = $bookRequestItem->bookRequest->purchase;
        if ($purchase) {
            $purchase->update(['book_request_response_id' => $response->id]);
        }

        return redirect()->route('provider.purchases.show', $purchase->id)
            ->with('success', 'تم إرسال الرد بنجاح');
    }
}
