<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\VoucherProduct;
use App\Models\NoteVoucher;
use App\Models\User;
use App\Models\Warehouse;
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

    public function index()
    {
        $user = Auth::user();
        $provider = $user->provider;
        
        // Get provider statistics
        $stats = [
            'total_products' => $provider->products()->count(),
            'pending_orders' => $this->getPendingOrdersCount($provider->id),
            'completed_orders' => $this->getCompletedOrdersCount($provider->id),
            'total_revenue' => $this->getTotalRevenue($provider->id),
            'total_sold_items' => $this->getTotalSoldItems($provider->id),
            'active_users' => $this->getActiveUsersCount($provider->id),
        ];

        // Recent orders containing provider's products
        $recentOrders = $this->getRecentOrders($provider->id, 5);
        
        // Top selling products
        $topProducts = $this->getTopSellingProducts($provider->id, 4);
        
        // Recent sales (user sales of provider's products)
        $recentSales = $this->getRecentSales($provider->id, 5);

        return view('provider.dashboard', compact('stats', 'recentOrders', 'topProducts', 'recentSales'));
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

    public function users()
    {
        $user = Auth::user();
        $provider = $user->provider;

        // Get users who have purchased provider's products
        $users = $this->getProviderCustomers($provider->id);

        return view('provider.users', compact('users'));
    }

    public function userDetails($userId)
    {
        $authUser = Auth::user();
        $provider = $authUser->provider;
        $user = User::findOrFail($userId);
        
        // Get user's activity with provider's products
        $userActivity = $this->getUserActivityWithProvider($userId, $provider->id);
        
        return view('provider.user-details', compact('user', 'userActivity'));
    }

    // Helper Methods

    private function getPendingOrdersCount($providerId)
    {
        return OrderProduct::whereHas('product', function($q) use ($providerId) {
            $q->where('provider_id', $providerId);
        })->whereHas('order', function($q) {
            $q->where('status', 1); // Pending
        })->distinct('order_id')->count();
    }

    private function getCompletedOrdersCount($providerId)
    {
        return OrderProduct::whereHas('product', function($q) use ($providerId) {
            $q->where('provider_id', $providerId);
        })->whereHas('order', function($q) {
            $q->where('status', 2); // Completed
        })->distinct('order_id')->count();
    }

    private function getTotalRevenue($providerId)
    {
        return OrderProduct::whereHas('product', function($q) use ($providerId) {
            $q->where('provider_id', $providerId);
        })->sum('total_price_after_tax');
    }

    private function getTotalSoldItems($providerId)
    {
        // Items sold by users (from their warehouses)
        return VoucherProduct::whereHas('product', function($q) use ($providerId) {
            $q->where('provider_id', $providerId);
        })->whereHas('noteVoucher', function($q) {
            $q->whereNotNull('from_warehouse_id')
              ->whereNull('to_warehouse_id') // Sales to customers
              ->whereHas('noteVoucherType', function($qt) {
                  $qt->where('in_out_type', 2); // Out type
              });
        })->sum('quantity');
    }

    private function getActiveUsersCount($providerId)
    {
        return OrderProduct::whereHas('product', function($q) use ($providerId) {
                $q->where('provider_id', $providerId);
            })
            ->whereHas('order') // تأكد من وجود الطلب
            ->with('order')
            ->get()
            ->pluck('order.user_id') // اجمع user_id من الطلبات
            ->unique()
            ->count();
    }

    private function getRecentOrders($providerId, $limit)
    {
        return Order::whereHas('orderProducts.product', function($q) use ($providerId) {
            $q->where('provider_id', $providerId);
        })->with(['user', 'orderProducts.product' => function($q) use ($providerId) {
            $q->where('provider_id', $providerId);
        }])->latest()->take($limit)->get();
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

    private function getRecentSales($providerId, $limit)
    {
        return NoteVoucher::whereHas('voucherProducts.product', function($q) use ($providerId) {
            $q->where('provider_id', $providerId);
        })->whereNotNull('from_warehouse_id')
          ->whereNull('to_warehouse_id')
          ->with(['fromWarehouse.user', 'voucherProducts.product' => function($q) use ($providerId) {
              $q->where('provider_id', $providerId);
          }])
          ->latest('date_note_voucher')
          ->take($limit)
          ->get();
    }

    private function getProductAnalytics($productId)
    {
        $product = Product::find($productId);
        
        return [
            'total_ordered' => OrderProduct::where('product_id', $productId)->sum('quantity'),
            'total_sold_by_users' => VoucherProduct::where('product_id', $productId)
                ->whereHas('noteVoucher', function($q) {
                    $q->whereNotNull('from_warehouse_id')->whereNull('to_warehouse_id');
                })->sum('quantity'),
            'current_in_warehouses' => $this->getCurrentInventoryInWarehouses($productId),
            'total_revenue' => OrderProduct::where('product_id', $productId)->sum('total_price_after_tax'),
            'average_selling_price' => VoucherProduct::where('product_id', $productId)->avg('purchasing_price'),
            'total_users' => OrderProduct::where('product_id', $productId)->distinct('order.user_id')->count(),
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
        })->withSum(['orders.orderProducts as total_spent' => function($q) use ($providerId) {
            $q->whereHas('product', function($product) use ($providerId) {
                $product->where('provider_id', $providerId);
            });
        }], 'total_price_after_tax')
          ->withCount(['orders as total_orders' => function($q) use ($providerId) {
              $q->whereHas('orderProducts.product', function($product) use ($providerId) {
                  $product->where('provider_id', $providerId);
              });
          }])
          ->orderBy('total_spent', 'desc')
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

    private function getProviderCustomers($providerId)
    {
        return User::whereHas('orders.orderProducts.product', function($q) use ($providerId) {
            $q->where('provider_id', $providerId);
        })->with(['warehouse'])
          ->withSum(['orders.orderProducts as total_spent' => function($q) use ($providerId) {
              $q->whereHas('product', function($product) use ($providerId) {
                  $product->where('provider_id', $providerId);
              });
          }], 'total_price_after_tax')
          ->withCount(['orders as total_orders' => function($q) use ($providerId) {
              $q->whereHas('orderProducts.product', function($product) use ($providerId) {
                  $product->where('provider_id', $providerId);
              });
          }])
          ->orderBy('total_spent', 'desc')
          ->paginate(15);
    }

    private function getUserActivityWithProvider($userId, $providerId)
    {
        return [
            'orders' => Order::where('user_id', $userId)
                ->whereHas('orderProducts.product', function($q) use ($providerId) {
                    $q->where('provider_id', $providerId);
                })->with(['orderProducts.product' => function($q) use ($providerId) {
                    $q->where('provider_id', $providerId);
                }])->latest()->get(),
            'sales' => NoteVoucher::whereHas('fromWarehouse.user', function($q) use ($userId) {
                $q->where('id', $userId);
            })->whereHas('voucherProducts.product', function($q) use ($providerId) {
                $q->where('provider_id', $providerId);
            })->with(['voucherProducts.product' => function($q) use ($providerId) {
                $q->where('provider_id', $providerId);
            }])->latest('date_note_voucher')->get(),
            'current_inventory' => $this->getUserCurrentInventoryForProvider($userId, $providerId),
        ];
    }

    private function getUserCurrentInventoryForProvider($userId, $providerId)
    {
        $user = User::find($userId);
        if (!$user->warehouse) return collect();

        return Product::where('provider_id', $providerId)
            ->select('products.*')
            ->selectRaw('
                COALESCE(received.received_quantity, 0) - COALESCE(sold.sold_quantity, 0) as current_quantity
            ')
            ->leftJoinSub(
                VoucherProduct::select('product_id')
                    ->selectRaw('SUM(quantity) as received_quantity')
                    ->whereHas('noteVoucher', function($q) use ($user) {
                        $q->where('to_warehouse_id', $user->warehouse->id);
                    })
                    ->groupBy('product_id'),
                'received',
                'products.id',
                '=',
                'received.product_id'
            )
            ->leftJoinSub(
                VoucherProduct::select('product_id')
                    ->selectRaw('SUM(quantity) as sold_quantity')
                    ->whereHas('noteVoucher', function($q) use ($user) {
                        $q->where('from_warehouse_id', $user->warehouse->id);
                    })
                    ->groupBy('product_id'),
                'sold',
                'products.id',
                '=',
                'sold.product_id'
            )
            ->having('current_quantity', '>', 0)
            ->get();
    }
}