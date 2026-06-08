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

        $distributionStats = $this->getDistributionStats($provider->id);

        return view('provider.dashboard', compact('stats', 'recentOrders', 'completedPurchases', 'pendingBookRequests', 'distributionStats'));
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
        $totalPurchased = \App\Models\Purchase::where('provider_id', $providerId)
            ->whereIn('status', ['confirmed', 'received', 'paid'])
            ->with('items')
            ->get()
            ->sum(fn($purchase) => $purchase->items->sum('quantity'));

        $totalReturned = \App\Models\PurchaseReturn::where('provider_id', $providerId)
            ->whereIn('status', ['approved', 'received'])
            ->with('returnItems')
            ->get()
            ->sum(fn($return) => $return->returnItems->sum('quantity_returned'));

        return max(0, $totalPurchased - $totalReturned);
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

    private function getDistributionStats($providerId)
    {
        // Get provider's product IDs via approved BookRequestResponses
        $productIds = \App\Models\BookRequestResponse::where('provider_id', $providerId)
            ->where('status', 'approved')
            ->with('bookRequestItem')
            ->get()
            ->pluck('bookRequestItem.product_id')
            ->filter()
            ->unique()
            ->toArray();

        if (empty($productIds)) {
            return [
                'main_warehouse_qty'   => 0,
                'distribution_points'  => [],
                'total_sold'           => 0,
                'sales_by_outlet'      => [],
            ];
        }

        // --- Main warehouse (Type 1 in - Type 3 out) ---
        $mainWarehouse = \App\Models\Warehouse::whereNull('user_id')->first();

        $mainWarehouseQty = 0;
        if ($mainWarehouse) {
            $receivedMain = \App\Models\VoucherProduct::whereIn('product_id', $productIds)
                ->whereHas('noteVoucher', fn($q) => $q
                    ->where('note_voucher_type_id', 1)
                    ->where('to_warehouse_id', $mainWarehouse->id))
                ->sum('quantity');

            $transferredOutMain = \App\Models\VoucherProduct::whereIn('product_id', $productIds)
                ->whereHas('noteVoucher', fn($q) => $q
                    ->where('note_voucher_type_id', 3)
                    ->where('from_warehouse_id', $mainWarehouse->id))
                ->sum('quantity');

            // Subtract items returned back to the provider from the main warehouse
            $purchaseReturnsFromMain = \App\Models\PurchaseReturn::where('provider_id', $providerId)
                ->where('warehouse_id', $mainWarehouse->id)
                ->whereIn('status', ['approved', 'received'])
                ->with('returnItems')
                ->get()
                ->sum(fn($return) => $return->returnItems
                    ->whereIn('product_id', $productIds)
                    ->sum('quantity_returned'));

            $mainWarehouseQty = max(0, $receivedMain - $transferredOutMain - $purchaseReturnsFromMain);
        }

        // --- Distribution points (Type 3 transfers TO seller warehouses) ---
        // Scope to vouchers that contain at least one of this provider's products
        $transferVouchers = \App\Models\NoteVoucher::where('note_voucher_type_id', 3)
            ->whereHas('voucherProducts', fn($q) => $q->whereIn('product_id', $productIds))
            ->with([
                'toWarehouse.user',
                'voucherProducts' => fn($q) => $q->whereIn('product_id', $productIds),
            ])
            ->get();

        $distributionPoints = [];
        foreach ($transferVouchers as $voucher) {
            $seller = $voucher->toWarehouse?->user;
            if (!$seller || !$seller->hasRole('seller')) {
                continue;
            }
            foreach ($voucher->voucherProducts as $vp) {
                if (in_array($vp->product_id, $productIds)) {
                    $sellerId = $seller->id;
                    if (!isset($distributionPoints[$sellerId])) {
                        $distributionPoints[$sellerId] = [
                            'name' => $seller->name,
                            'qty'  => 0,
                        ];
                    }
                    $distributionPoints[$sellerId]['qty'] += $vp->quantity;
                }
            }
        }

        // --- Sales by outlet (SellerSale) ---
        $sellerSales = \App\Models\SellerSale::whereHas('items', fn($q) => $q->whereIn('product_id', $productIds))
            ->with(['user', 'items' => fn($q) => $q->whereIn('product_id', $productIds)])
            ->get();

        $salesByOutlet = [];
        $totalSold = 0;
        foreach ($sellerSales as $sale) {
            $sellerId = $sale->user_id;
            foreach ($sale->items as $item) {
                if (!isset($salesByOutlet[$sellerId])) {
                    $salesByOutlet[$sellerId] = [
                        'name' => $sale->user?->name ?? 'Unknown',
                        'qty'  => 0,
                    ];
                }
                $salesByOutlet[$sellerId]['qty'] += $item->quantity;
                $totalSold += $item->quantity;
            }
        }

        // Subtract sales returns per outlet
        $salesReturns = \App\Models\SalesReturn::whereIn('status', ['approved', 'received'])
            ->whereHas('returnItems', fn($q) => $q->whereIn('product_id', $productIds))
            ->with(['returnItems' => fn($q) => $q->whereIn('product_id', $productIds)])
            ->get();

        foreach ($salesReturns as $return) {
            $sellerId = $return->user_id;
            foreach ($return->returnItems as $item) {
                $totalSold -= $item->quantity_returned;
                if (isset($salesByOutlet[$sellerId])) {
                    $salesByOutlet[$sellerId]['qty'] -= $item->quantity_returned;
                }
            }
        }

        $totalSold = max(0, $totalSold);
        foreach ($salesByOutlet as $sid => $data) {
            $salesByOutlet[$sid]['qty'] = max(0, $data['qty']);
        }
        // Remove outlets with zero net sales
        $salesByOutlet = array_filter($salesByOutlet, fn($o) => $o['qty'] > 0);

        return [
            'main_warehouse_qty'  => $mainWarehouseQty,
            'distribution_points' => array_values($distributionPoints),
            'total_sold'          => $totalSold,
            'sales_by_outlet'     => array_values($salesByOutlet),
        ];
    }

}