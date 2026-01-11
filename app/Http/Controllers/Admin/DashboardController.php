<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use App\Models\Provider;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:dashboard-view')->only(['index']);
    }

    public function index()
    {
        $stats = [
            'total_users' => User::role('seller')->count(), // Count only sellers
            'total_providers' => Provider::count(),
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', '!=', 1)->count(), // Pending orders (any status != Done)
            'completed_orders' => Order::where('status', 1)->count(), // Status 1 = Done
            'cancelled_orders' => Order::where('status', 2)->count(), // Status 2 = Cancelled
            'today_orders' => Order::whereDate('created_at', today())->count(),
            'revenue_today' => Order::where('status', 1)
                ->whereDate('created_at', today())
                ->sum('paid_amount'),
            'revenue_month' => Order::where('status', 1)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('paid_amount')
        ];

        // Recent orders
        $recentOrders = Order::with([
            'user:id,name',
        ])->latest()->limit(10)->get();

        // Orders by status (status only, ignoring payment status)
        $ordersByStatus = [
            'completed' => Order::where('status', 1)->count(), // Completed
            'cancelled' => Order::where('status', 2)->count(), // Cancelled
            'refund' => Order::where('status', 6)->count(), // Refund
            'pending' => Order::whereNotIn('status', [1, 2, 6])->count() // Any other pending status
        ];

        return view('admin.dashboard', compact('stats', 'recentOrders', 'ordersByStatus'));
    }
}
