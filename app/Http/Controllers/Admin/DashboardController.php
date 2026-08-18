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
    public function index()
    {
        $stats = [
            'total_users' => 0,
            'total_providers' => 0,
            'total_orders' => 0,
            'pending_orders' => 0,
            'completed_orders' => 0,
            'cancelled_orders' => 0,
            'today_orders' => 0,
            'revenue_today' => 0,
            'revenue_month' => 0,
        ];
        $recentOrders = collect();
        $ordersByStatus = ['pending' => 0, 'completed' => 0, 'cancelled' => 0, 'refund' => 0];

        if (auth()->user()->can('seller-table')) {
            $stats['total_users'] = User::role('seller')->count();
        }

        if (auth()->user()->can('provider-table')) {
            $stats['total_providers'] = Provider::count();
        }

        if (auth()->user()->can('order-table')) {
            $stats['total_orders'] = Order::count();
            $stats['pending_orders'] = Order::where('status', OrderStatus::PENDING->value)->count();
            $stats['completed_orders'] = Order::where('status', OrderStatus::DONE->value)->count();
            $stats['cancelled_orders'] = Order::where('status', OrderStatus::CANCELLED->value)->count();
            $stats['today_orders'] = Order::whereDate('created_at', today())->count();
            $stats['revenue_today'] = Order::where('status', OrderStatus::DONE->value)
                ->whereDate('created_at', today())
                ->sum('paid_amount');
            $stats['revenue_month'] = Order::where('status', OrderStatus::DONE->value)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('paid_amount');

            $recentOrders = Order::with([
                'user:id,name',
            ])->latest()->limit(10)->get();

            $ordersByStatus = [
                'pending' => $stats['pending_orders'],
                'completed' => $stats['completed_orders'],
                'cancelled' => $stats['cancelled_orders'],
                'refund' => Order::where('status', OrderStatus::REFUNDED->value)->count(),
            ];
        }

        return view('admin.dashboard', compact('stats', 'recentOrders', 'ordersByStatus'));
    }
}
