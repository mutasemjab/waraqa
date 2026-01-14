<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\OrderProduct;
use App\Models\VoucherProduct;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
            'total_products' => $provider->products()->count(),
            'total_orders' => $this->getTotalOrdersCount($provider->id),
            'total_sold_items' => $this->getTotalSoldItems($provider->id),
            'total_revenue' => $this->getTotalRevenue($provider->id),
            'monthly_revenue' => $this->getMonthlyRevenue($provider->id),
        ];

        // Recent orders containing provider's products
        $recentOrders = $this->getRecentOrders($provider->id, 5);

        // Top selling products
        $topProducts = $this->getTopSellingProducts($provider->id, 4);

        return view('provider.dashboard', compact('stats', 'recentOrders', 'topProducts'));
    }

    public function products(Request $request)
    {
        $user = Auth::user();
        $provider = $user->provider;
        
        $query = $provider->products()->with(['category']);

        // Search functionality
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name_ar', 'like', '%' . $request->search . '%')
                  ->orWhere('name_en', 'like', '%' . $request->search . '%');
            });
        }

        // Category filter
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->paginate(12);

        // Get categories for filter
        $categories = \App\Models\Category::all();

        return view('provider.products.index', compact('products', 'categories'));
    }

    public function productDetails($productId)
    {
        $user = Auth::user();
        $provider = $user->provider;

        // Get product with validation that it belongs to provider
        $product = $provider->products()->with('category')->findOrFail($productId);
        
        // Get product analytics
        $analytics = $this->getProductAnalytics($productId);
        
        // Users who have this product
        $usersWithProduct = $this->getUsersWithProduct($productId);
        
        // Sales history of this product
        $salesHistory = $this->getProductSalesHistory($productId);
        
        // Order history
        $orderHistory = $this->getProductOrderHistory($productId);

        return view('provider.products.details', compact(
            'product', 
            'analytics', 
            'usersWithProduct', 
            'salesHistory', 
            'orderHistory'
        ));
    }

    public function analytics()
    {
        $user = Auth::user();
        $provider = $user->provider;

        // Monthly sales data for charts
        $monthlySales = $this->getMonthlySalesData($provider->id);
        
        // Product performance
        $productPerformance = $this->getProductPerformanceData($provider->id);
        
        // User analysis
        $userAnalysis = $this->getUserAnalysisData($provider->id);
        
        // Revenue breakdown
        $revenueBreakdown = $this->getRevenueBreakdown($provider->id);

        return view('provider.analytics', compact(
            'monthlySales', 
            'productPerformance', 
            'userAnalysis', 
            'revenueBreakdown'
        ));
    }

    public function orders()
    {
        $user = Auth::user();
        $provider = $user->provider;

        // Get purchases (provider's orders)
        $orders = \App\Models\Purchase::where('provider_id', $provider->id)
            ->with(['items.product', 'warehouse', 'bookRequestResponse'])
            ->latest()
            ->paginate(15);

        return view('provider.orders', compact('orders'));
    }

    // Helper Methods

    private function getTotalOrdersCount($providerId)
    {
        return \App\Models\Purchase::where('provider_id', $providerId)->count();
    }

    private function getTotalRevenue($providerId)
    {
        return \App\Models\Purchase::where('provider_id', $providerId)
            ->sum('total_amount');
    }

    private function getTotalSoldItems($providerId)
    {
        return \App\Models\Purchase::where('provider_id', $providerId)
            ->with('items')
            ->get()
            ->sum(function($purchase) {
                return $purchase->items->sum('quantity');
            });
    }

    private function getMonthlyRevenue($providerId)
    {
        return \App\Models\Purchase::where('provider_id', $providerId)
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('total_amount');
    }

    private function getRecentOrders($providerId, $limit)
    {
        return \App\Models\Purchase::where('provider_id', $providerId)
            ->with(['items.product', 'warehouse', 'bookRequestResponse'])
            ->latest()
            ->take($limit)
            ->get();
    }

    private function getTopSellingProducts($providerId, $limit)
    {
        return Product::where('provider_id', $providerId)
            ->withSum(['orderProducts' => function($q) {
                $q->whereHas('order', function($order) {
                    $order->where('status', 2); // Completed orders only
                });
            }], 'quantity')
            ->withSum(['voucherProducts' => function($q) {
                $q->whereHas('noteVoucher', function($nv) {
                    $nv->whereNotNull('from_warehouse_id')
                      ->whereNull('to_warehouse_id');
                });
            }], 'quantity')
            ->orderBy('order_products_sum_quantity', 'desc')
            ->take($limit)
            ->get();
    }

    private function getProductAnalytics($productId)
    {
        return [
            'total_ordered' => OrderProduct::where('product_id', $productId)->sum('quantity'),
            'total_sold_by_users' => VoucherProduct::where('product_id', $productId)
                ->whereHas('noteVoucher', function($q) {
                    $q->whereNotNull('from_warehouse_id')->whereNull('to_warehouse_id');
                })->sum('quantity'),
            'current_in_warehouses' => $this->getCurrentInventoryInWarehouses($productId),
            'total_revenue' => OrderProduct::where('product_id', $productId)->sum('total_price_after_tax'),
            'average_selling_price' => VoucherProduct::where('product_id', $productId)->avg('purchasing_price'),
            'total_users' => OrderProduct::where('product_id', $productId)
                ->join('orders', 'order_products.order_id', '=', 'orders.id')
                ->pluck('orders.user_id')
                ->unique()
                ->count(),
        ];
    }

    private function getCurrentInventoryInWarehouses($productId)
    {
        $received = VoucherProduct::where('product_id', $productId)
            ->whereHas('noteVoucher', function($q) {
                $q->whereNotNull('to_warehouse_id');
            })->sum('quantity');

        $sold = VoucherProduct::where('product_id', $productId)
            ->whereHas('noteVoucher', function($q) {
                $q->whereNotNull('from_warehouse_id');
            })->sum('quantity');

        return $received - $sold;
    }

    private function getUsersWithProduct($productId)
    {
        return DB::table('voucher_products as vp')
            ->join('note_vouchers as nv', 'vp.note_voucher_id', '=', 'nv.id')
            ->join('warehouses as w', 'nv.to_warehouse_id', '=', 'w.id')
            ->join('users as u', 'w.user_id', '=', 'u.id')
            ->where('vp.product_id', $productId)
            ->whereNotNull('nv.to_warehouse_id')
            ->select('u.*')
            ->selectRaw('SUM(vp.quantity) as received_quantity')
            ->selectRaw('COALESCE(sold.sold_quantity, 0) as sold_quantity')
            ->selectRaw('SUM(vp.quantity) - COALESCE(sold.sold_quantity, 0) as current_quantity')
            ->leftJoinSub(
                DB::table('voucher_products as vp2')
                    ->join('note_vouchers as nv2', 'vp2.note_voucher_id', '=', 'nv2.id')
                    ->join('warehouses as w2', 'nv2.from_warehouse_id', '=', 'w2.id')
                    ->where('vp2.product_id', $productId)
                    ->select('w2.user_id')
                    ->selectRaw('SUM(vp2.quantity) as sold_quantity')
                    ->groupBy('w2.user_id'),
                'sold',
                'u.id',
                '=',
                'sold.user_id'
            )
            ->groupBy('u.id', 'u.name', 'u.email', 'u.phone', 'u.created_at', 'sold.sold_quantity')
            ->having('current_quantity', '>', 0)
            ->get();
    }

    private function getProductSalesHistory($productId)
    {
        return VoucherProduct::where('product_id', $productId)
            ->whereHas('noteVoucher', function($q) {
                $q->whereNotNull('from_warehouse_id')
                  ->whereNull('to_warehouse_id');
            })
            ->with(['noteVoucher.fromWarehouse.user'])
            ->latest()
            ->take(10)
            ->get();
    }

    private function getProductOrderHistory($productId)
    {
        return OrderProduct::where('product_id', $productId)
            ->with(['order.user'])
            ->latest()
            ->take(10)
            ->get();
    }

    private function getMonthlySalesData($providerId)
    {
        return VoucherProduct::whereHas('product', function($q) use ($providerId) {
            $q->where('provider_id', $providerId);
        })->whereHas('noteVoucher', function($q) {
            $q->whereNotNull('from_warehouse_id')
              ->whereNull('to_warehouse_id')
              ->where('date_note_voucher', '>=', Carbon::now()->subMonths(12));
        })->join('note_vouchers', 'voucher_products.note_voucher_id', '=', 'note_vouchers.id')
          ->select(
              DB::raw('YEAR(note_vouchers.date_note_voucher) as year'),
              DB::raw('MONTH(note_vouchers.date_note_voucher) as month'),
              DB::raw('SUM(voucher_products.quantity) as total_quantity'),
              DB::raw('SUM(voucher_products.quantity * voucher_products.purchasing_price) as total_revenue')
          )
          ->groupBy('year', 'month')
          ->orderBy('year', 'desc')
          ->orderBy('month', 'desc')
          ->get();
    }

    private function getProductPerformanceData($providerId)
    {
        return Product::where('provider_id', $providerId)
            ->withSum(['orderProducts' => function($q) {
                $q->whereHas('order', function($order) {
                    $order->where('status', 2);
                });
            }], 'quantity')
            ->withSum(['voucherProducts' => function($q) {
                $q->whereHas('noteVoucher', function($nv) {
                    $nv->whereNotNull('from_warehouse_id')->whereNull('to_warehouse_id');
                });
            }], 'quantity')
            ->withSum(['orderProducts' => function($q) {
                $q->whereHas('order', function($order) {
                    $order->where('status', 2);
                });
            }], 'total_price_after_tax')
            ->orderBy('order_products_sum_quantity', 'desc')
            ->get();
    }

    private function getUserAnalysisData($providerId)
    {
        return User::whereHas('orders.orderProducts.product', function($q) use ($providerId) {
            $q->where('provider_id', $providerId);
        })->with(['orders.orderProducts.product' => function($q) use ($providerId) {
            $q->where('provider_id', $providerId);
        }])
          ->withCount(['orders as total_orders' => function($q) use ($providerId) {
              $q->whereHas('orderProducts.product', function($product) use ($providerId) {
                  $product->where('provider_id', $providerId);
              });
          }])
          ->orderBy('created_at', 'desc')
          ->take(10)
          ->get();
    }

    private function getRevenueBreakdown($providerId)
    {
        return [
            'total_orders_revenue' => OrderProduct::whereHas('product', function($q) use ($providerId) {
                $q->where('provider_id', $providerId);
            })->sum('total_price_after_tax'),
            'current_month_revenue' => OrderProduct::whereHas('product', function($q) use ($providerId) {
                $q->where('provider_id', $providerId);
            })->whereHas('order', function($q) {
                $q->whereMonth('date', Carbon::now()->month)
                  ->whereYear('date', Carbon::now()->year);
            })->sum('total_price_after_tax'),
            'previous_month_revenue' => OrderProduct::whereHas('product', function($q) use ($providerId) {
                $q->where('provider_id', $providerId);
            })->whereHas('order', function($q) {
                $q->whereMonth('date', Carbon::now()->subMonth()->month)
                  ->whereYear('date', Carbon::now()->subMonth()->year);
            })->sum('total_price_after_tax'),
        ];
    }

}