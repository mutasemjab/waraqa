<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use App\Models\Provider;
use App\Enums\OrderStatus;
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
            'total_users' => User::role('seller')->count(),
            'total_providers' => Provider::count(),
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', OrderStatus::PENDING->value)->count(),
            'completed_orders' => Order::where('status', OrderStatus::DONE->value)->count(),
            'cancelled_orders' => Order::where('status', OrderStatus::CANCELLED->value)->count(),
            'today_orders' => Order::whereDate('created_at', today())->count(),
            'revenue_today' => Order::where('status', OrderStatus::DONE->value)
                ->whereDate('created_at', today())
                ->sum('paid_amount'),
            'revenue_month' => Order::where('status', OrderStatus::DONE->value)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('paid_amount')
        ];

        // Recent orders
        $recentOrders = Order::with([
            'user:id,name',
        ])->latest()->limit(10)->get();

        // Orders by status
        $ordersByStatus = [
            'pending' => Order::where('status', OrderStatus::PENDING->value)->count(),
            'completed' => Order::where('status', OrderStatus::DONE->value)->count(),
            'cancelled' => Order::where('status', OrderStatus::CANCELLED->value)->count(),
            'refund' => Order::where('status', OrderStatus::REFUNDED->value)->count(),
        ];

        return view('admin.dashboard', compact('stats', 'recentOrders', 'ordersByStatus'));
    }
}
