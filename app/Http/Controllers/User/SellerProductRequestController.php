<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\SellerProductRequest;
use App\Models\SellerProductRequestItem;
use App\Models\Product;
use App\Enums\SellerProductRequestStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SellerProductRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:web', 'role:seller']);
    }

    public function index()
    {
        $user = Auth::user();
        $requests = $user->sellerProductRequests()
            ->with(['items.product', 'order', 'approver'])
            ->latest()
            ->paginate(15);

        return view('user.sellerProductRequests.index', compact('requests'));
    }

    public function create()
    {
        $products = Product::with('category')->get();
        return view('user.sellerProductRequests.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.requested_quantity' => 'required|integer|min:1',
            'note' => 'nullable|string|max:1000'
        ]);

        DB::transaction(function () use ($validated, $request) {
            $sellerProductRequest = Auth::user()->sellerProductRequests()->create([
                'note' => $request->note,
            ]);

            foreach ($validated['items'] as $item) {
                SellerProductRequestItem::create([
                    'seller_product_request_id' => $sellerProductRequest->id,
                    'product_id' => $item['product_id'],
                    'requested_quantity' => $item['requested_quantity'],
                ]);
            }
        });

        return redirect()->route('user.sellerProductRequests.index')
            ->with('success', __('messages.request_created_successfully'));
    }

    public function show(SellerProductRequest $sellerProductRequest)
    {
        if ($sellerProductRequest->user_id !== Auth::id()) {
            abort(403);
        }

        $sellerProductRequest->load(['items.product.category', 'order', 'approver']);
        return view('user.sellerProductRequests.show', compact('sellerProductRequest'));
    }

    public function destroy(SellerProductRequest $sellerProductRequest)
    {
        if ($sellerProductRequest->user_id !== Auth::id()) {
            abort(403);
        }

        if ($sellerProductRequest->status !== SellerProductRequestStatus::PENDING) {
            return back()->with('error', __('messages.can_only_delete_pending_requests'));
        }

        $sellerProductRequest->delete();

        return redirect()->route('user.sellerProductRequests.index')
            ->with('success', __('messages.request_deleted_successfully'));
    }
}
