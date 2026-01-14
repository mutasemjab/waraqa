<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;

class CustomerReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:customers-table')->only(['index']);
    }

    public function index()
    {
        return view('admin.reports.customersReport');
    }

    public function search(Request $request)
    {
        $term = $request->get('term', '');
        $limit = $request->get('limit', 10);

        $customers = User::where(function ($query) use ($term) {
            $query->where('name', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%")
                ->orWhere('phone', 'like', "%{$term}%");
        })
        ->whereHas('roles', function ($query) {
            $query->whereIn('name', ['seller', 'customer']);
        })
        ->limit($limit)
        ->get()
        ->map(function ($customer) {
            return [
                'id' => $customer->id,
                'text' => $customer->name . ' (' . ($customer->phone ?? 'N/A') . ')',
                'name' => $customer->name,
                'phone' => $customer->phone,
                'email' => $customer->email,
            ];
        });

        return response()->json($customers);
    }

    public function getCustomerData(Request $request, $customerId)
    {
        $customer = User::with(['orders', 'orders.orderProducts.product'])->findOrFail($customerId);

        // Get customer statistics
        $totalOrders = $customer->orders()->count();
        $completedOrders = $customer->orders()->where('status', 1)->count();
        $cancelledOrders = $customer->orders()->where('status', 2)->count();
        $totalSpent = $customer->orders()->sum('total_prices') ?? 0;
        $totalPaid = $customer->orders()->sum('paid_amount') ?? 0;
        $totalRemaining = $customer->orders()->sum('remaining_amount') ?? 0;
        $totalTaxes = $customer->orders()->sum('total_taxes') ?? 0;

        // Get orders with details
        $orders = $customer->orders()
            ->with(['orderProducts.product'])
            ->orderBy('order_date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'customer' => [
                'id' => $customer->id,
                'name' => $customer->name,
                'email' => $customer->email,
                'phone' => $customer->phone,
                'created_at' => $customer->created_at->format('Y-m-d'),
            ],
            'statistics' => [
                'total_orders' => $totalOrders,
                'completed_orders' => $completedOrders,
                'cancelled_orders' => $cancelledOrders,
                'total_spent' => number_format($totalSpent, 2),
                'total_paid' => number_format($totalPaid, 2),
                'total_remaining' => number_format($totalRemaining, 2),
                'total_taxes' => number_format($totalTaxes, 2),
            ],
            'orders' => $orders->map(function ($order) {
                return [
                    'id' => $order->id,
                    'number' => $order->number,
                    'date' => $order->order_date ? $order->order_date->format('Y-m-d') : '-',
                    'status' => $this->getStatusBadge($order->status),
                    'payment_status' => $this->getPaymentStatusBadge($order->payment_status),
                    'total_prices' => number_format($order->total_prices ?? 0, 2),
                    'total_taxes' => number_format($order->total_taxes ?? 0, 2),
                    'paid_amount' => number_format($order->paid_amount ?? 0, 2),
                    'remaining_amount' => number_format($order->remaining_amount ?? 0, 2),
                    'products_count' => $order->orderProducts->count(),
                    'products' => $order->orderProducts->map(function ($product) {
                        return [
                            'name' => $product->product->name_ar ?? $product->product->name_en ?? 'Unknown',
                            'quantity' => $product->quantity,
                            'unit_price' => number_format($product->unit_price ?? 0, 2),
                            'total_price' => number_format(($product->quantity * $product->unit_price) ?? 0, 2),
                        ];
                    }),
                ];
            }),
        ]);
    }

    private function getStatusBadge($status)
    {
        if ($status == 1) {
            return '<span class="badge badge-success">' . __('messages.done') . '</span>';
        } elseif ($status == 2) {
            return '<span class="badge badge-danger">' . __('messages.canceled') . '</span>';
        } else {
            return '<span class="badge badge-info">' . __('messages.refund') . '</span>';
        }
    }

    private function getPaymentStatusBadge($paymentStatus)
    {
        if ($paymentStatus == 1) {
            return '<span class="badge badge-success">' . __('messages.paid') . '</span>';
        } else {
            return '<span class="badge badge-warning">' . __('messages.unpaid') . '</span>';
        }
    }
}
