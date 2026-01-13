<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class OrderReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:order-table')->only(['index']);
    }

    public function index(Request $request)
    {
        $query = Order::with(['user', 'orderProducts.product']);

        // Filter by date range
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('order_date', [$request->from_date, $request->to_date]);
        } elseif ($request->filled('from_date')) {
            $query->whereDate('order_date', '>=', $request->from_date);
        } elseif ($request->filled('to_date')) {
            $query->whereDate('order_date', '<=', $request->to_date);
        }

        // Filter by user/customer
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment status
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Get data
        $data = $query->orderBy('order_date', 'desc')->get();

        // Calculate statistics
        $statistics = $this->calculateStatistics($data);

        // Get filters data
        $users = User::whereHas('orders')->get();
        $statuses = Order::select('status')->distinct()->pluck('status');
        $paymentStatuses = Order::select('payment_status')->distinct()->pluck('payment_status');

        return view('admin.reports.ordersReport', compact(
            'data',
            'statistics',
            'users',
            'statuses',
            'paymentStatuses'
        ));
    }

    private function calculateStatistics($data)
    {
        $stats = [
            'total_orders' => $data->count(),
            'total_revenue' => 0,
            'total_taxes' => 0,
            'total_paid' => 0,
            'total_remaining' => 0,
            'by_status' => [],
            'by_payment_status' => [],
            'by_user' => [],
        ];

        // Status mapping
        $statusMap = [
            1 => 'done',
            2 => 'canceled',
            3 => 'refund',
        ];

        // Payment status mapping
        $paymentStatusMap = [
            1 => 'paid',
            0 => 'unpaid',
        ];

        foreach ($data as $order) {
            $stats['total_revenue'] += $order->total_prices ?? 0;
            $stats['total_taxes'] += $order->total_taxes ?? 0;
            $stats['total_paid'] += $order->paid_amount ?? 0;
            $stats['total_remaining'] += $order->remaining_amount ?? 0;

            // By status
            $statusKey = $order->status ?? 0;
            $status = $statusMap[$statusKey] ?? 'refund';
            if (!isset($stats['by_status'][$status])) {
                $stats['by_status'][$status] = ['count' => 0, 'revenue' => 0];
            }
            $stats['by_status'][$status]['count']++;
            $stats['by_status'][$status]['revenue'] += $order->total_prices ?? 0;

            // By payment status
            $paymentStatusKey = $order->payment_status ?? 0;
            $paymentStatus = $paymentStatusMap[$paymentStatusKey] ?? 'unpaid';
            if (!isset($stats['by_payment_status'][$paymentStatus])) {
                $stats['by_payment_status'][$paymentStatus] = ['count' => 0, 'revenue' => 0];
            }
            $stats['by_payment_status'][$paymentStatus]['count']++;
            $stats['by_payment_status'][$paymentStatus]['revenue'] += $order->total_prices ?? 0;

            // By user
            if ($order->user_id) {
                $userName = $order->user->name ?? 'Unknown';
                if (!isset($stats['by_user'][$userName])) {
                    $stats['by_user'][$userName] = ['count' => 0, 'revenue' => 0];
                }
                $stats['by_user'][$userName]['count']++;
                $stats['by_user'][$userName]['revenue'] += $order->total_prices ?? 0;
            }
        }

        return $stats;
    }
}
