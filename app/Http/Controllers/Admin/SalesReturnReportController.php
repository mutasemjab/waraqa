<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SalesReturn;
use App\Models\User;
use Illuminate\Http\Request;

class SalesReturnReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:sales-return-table')->only(['index']);
    }

    public function index(Request $request)
    {
        $query = SalesReturn::with(['order.user', 'user', 'returnItems.product']);

        // Filter by date range
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('return_date', [$request->from_date, $request->to_date]);
        } elseif ($request->filled('from_date')) {
            $query->whereDate('return_date', '>=', $request->from_date);
        } elseif ($request->filled('to_date')) {
            $query->whereDate('return_date', '<=', $request->to_date);
        }

        // Filter by user/customer (order's user)
        if ($request->filled('user_id')) {
            $query->whereHas('order', function ($q) use ($request) {
                $q->where('user_id', $request->user_id);
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Get data
        $data = $query->orderBy('return_date', 'desc')->get();

        // Calculate statistics
        $statistics = $this->calculateStatistics($data);

        // Get filters data
        $users = User::whereHas('orders.salesReturns')->get();
        if ($users->isEmpty()) {
            $users = User::whereHas('orders')->get();
        }

        return view('admin.reports.salesReturnsReport', compact(
            'data',
            'statistics',
            'users'
        ));
    }

    private function calculateStatistics($data)
    {
        $stats = [
            'total_returns' => $data->count(),
            'total_amount' => 0,
            'total_quantity' => 0,
            'by_status' => [],
            'by_user' => [],
            'by_product' => [],
        ];

        // Status mapping
        $statusMap = [
            'pending' => 'pending',
            'approved' => 'approved',
            'received' => 'received',
        ];

        foreach ($data as $return) {
            $stats['total_amount'] += $return->total_amount ?? 0;

            // Calculate total quantity
            $returnQuantity = 0;
            foreach ($return->returnItems as $item) {
                $returnQuantity += $item->quantity_returned ?? 0;

                // By product
                $productName = $item->product->name_ar ?? $item->product->name_en ?? 'Unknown';
                if (!isset($stats['by_product'][$productName])) {
                    $stats['by_product'][$productName] = ['count' => 0, 'quantity' => 0, 'amount' => 0];
                }
                $stats['by_product'][$productName]['count']++;
                $stats['by_product'][$productName]['quantity'] += $item->quantity_returned ?? 0;
                $stats['by_product'][$productName]['amount'] += $item->total_price ?? 0;
            }
            $stats['total_quantity'] += $returnQuantity;

            // By status
            $status = $return->status ?? 'pending';
            if (!isset($stats['by_status'][$status])) {
                $stats['by_status'][$status] = ['count' => 0, 'amount' => 0];
            }
            $stats['by_status'][$status]['count']++;
            $stats['by_status'][$status]['amount'] += $return->total_amount ?? 0;

            // By user (customer from order)
            if ($return->order && $return->order->user) {
                $userName = $return->order->user->name ?? 'Unknown';
                if (!isset($stats['by_user'][$userName])) {
                    $stats['by_user'][$userName] = ['count' => 0, 'amount' => 0];
                }
                $stats['by_user'][$userName]['count']++;
                $stats['by_user'][$userName]['amount'] += $return->total_amount ?? 0;
            }
        }

        return $stats;
    }
}
