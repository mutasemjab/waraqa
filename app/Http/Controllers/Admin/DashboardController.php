<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Clas; // Replace with your actual class model name
use App\Models\Driver;
use App\Models\Order;
use App\Models\Provider;
use Google\Service\DriveActivity\Drive;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_providers' => Provider::count(),
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 1)->count(),
            'completed_orders' => Order::where('status', 3)->count(),
            'cancelled_orders' => Order::whereIn('status', [4, 5])->count(),
            'today_orders' => Order::whereDate('created_at', today())->count(),
            'revenue_today' => Order::where('status', 3)
                ->whereDate('created_at', today())
                ->sum('total_prices'),
            'revenue_month' => Order::where('status', 3)
                ->whereMonth('created_at', now()->month)
                ->sum('total_prices')
        ];

        // Recent orders
        $recentOrders = Order::with([
            'user:id,name',
        ])->latest()->limit(10)->get();

        // Orders by status for chart
        $ordersByStatus = Order::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();

        return view('admin.dashboard', compact('stats', 'recentOrders', 'ordersByStatus'));
    }
}
