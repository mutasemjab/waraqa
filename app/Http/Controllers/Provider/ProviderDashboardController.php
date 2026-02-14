<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProviderDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:web', 'role:provider']);
    }

    public function profile()
    {
        $user = Auth::user();
        return view('provider.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'phone' => 'required|string|unique:users,phone,' . $user->id,
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ];

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $photoName = time() . '_' . $photo->getClientOriginalName();
            $photo->storeAs('public/users', $photoName);
            $updateData['photo'] = 'users/' . $photoName;
        }

        $user->update($updateData);

        return back()->with('success', __('messages.profile_updated_successfully'));
    }

    public function index()
    {
        $user = Auth::user();
        $provider = $user->provider;

        // Get provider statistics
        $stats = [
            'total_orders' => $this->getTotalOrdersCount($provider->id),
            'total_sold_items' => $this->getTotalSoldItems($provider->id),
        ];

        // Recent orders containing provider's products
        $recentOrders = $this->getRecentOrders($provider->id, 5);

        // Completed/Recent purchases
        $completedPurchases = \App\Models\Purchase::where('provider_id', $provider->id)
            ->whereIn('status', ['confirmed', 'received', 'paid'])
            ->with(['items.product', 'warehouse', 'bookRequest.items'])
            ->latest()
            ->take(5)
            ->get();

        // Pending book requests (without responses from this provider)
        $pendingBookRequests = \App\Models\BookRequest::where('provider_id', $provider->id)
            ->with(['items.responses', 'items.product', 'user'])
            ->whereDoesntHave('items.responses', function ($query) use ($provider) {
                $query->where('provider_id', $provider->id);
            })
            ->latest()
            ->take(5)
            ->get();

        return view('provider.dashboard', compact('stats', 'recentOrders', 'completedPurchases', 'pendingBookRequests'));
    }

    public function orders()
    {
        $user = Auth::user();
        $provider = $user->provider;

        // Get all purchases (provider's orders)
        $orders = \App\Models\Purchase::where('provider_id', $provider->id)
            ->with(['items.product', 'warehouse', 'bookRequestResponse'])
            ->latest()
            ->paginate(15);

        return view('provider.orders', compact('orders'));
    }

    public function purchases()
    {
        $user = Auth::user();
        $provider = $user->provider;

        // Get purchases that have a response from this provider
        $orders = \App\Models\Purchase::where('provider_id', $provider->id)
            ->whereNotNull('book_request_response_id')
            ->with(['items.product', 'warehouse', 'bookRequest.items', 'bookRequestResponse'])
            ->latest()
            ->paginate(15);

        return view('provider.orders', compact('orders'));
    }

    public function bookRequests()
    {
        $user = Auth::user();
        $provider = $user->provider;

        // Get pending book requests (without responses from this provider)
        $bookRequests = \App\Models\BookRequest::where('provider_id', $provider->id)
            ->with(['items.responses', 'items.product', 'user'])
            ->whereDoesntHave('items.responses', function ($query) use ($provider) {
                $query->where('provider_id', $provider->id);
            })
            ->latest()
            ->paginate(15);

        return view('provider.book-requests.index', compact('bookRequests'));
    }

    public function getPendingPurchasesCount()
    {
        $user = Auth::user();
        $provider = $user->provider;
        return \App\Models\Purchase::where('provider_id', $provider->id)
            ->where('status', 'pending')
            ->count();
    }

    public function getPendingBookRequestsCount()
    {
        $user = Auth::user();
        $provider = $user->provider;
        return \App\Models\BookRequest::where('provider_id', $provider->id)
            ->whereDoesntHave('items.responses', function ($query) use ($provider) {
                $query->where('provider_id', $provider->id);
            })
            ->count();
    }

    public function showPurchase($id)
    {
        $user = Auth::user();
        $provider = $user->provider;

        // Get the purchase and verify it belongs to this provider
        $purchase = \App\Models\Purchase::where('provider_id', $provider->id)
            ->with([
                'items.product',
                'warehouse',
                'bookRequestResponse',
                'provider',
                'bookRequest.items.product',
                'bookRequest.items.responses'
            ])
            ->findOrFail($id);

        return view('provider.purchases.show', compact('purchase'));
    }

    // Helper Methods

    private function getTotalOrdersCount($providerId)
    {
        return \App\Models\Purchase::where('provider_id', $providerId)
            ->whereIn('status', ['confirmed', 'received', 'paid'])
            ->count();
    }

    private function getTotalSoldItems($providerId)
    {
        return \App\Models\Purchase::where('provider_id', $providerId)
            ->whereIn('status', ['confirmed', 'received', 'paid'])
            ->with('items')
            ->get()
            ->sum(function($purchase) {
                return $purchase->items->sum('quantity');
            });
    }

    private function getRecentOrders($providerId, $limit)
    {
        return \App\Models\Purchase::where('provider_id', $providerId)
            ->with([
                'items.product',
                'warehouse',
                'bookRequestResponse',
                'bookRequest.items.responses'
            ])
            ->latest()
            ->take($limit)
            ->get();
    }

}