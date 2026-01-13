<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class EventReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:orders-table')->only(['index']);
    }

    public function index()
    {
        return view('admin.reports.eventsReport');
    }

    public function search(Request $request)
    {
        $term = $request->get('term', '');
        $limit = $request->get('limit', 10);

        $events = Event::where(function ($query) use ($term) {
            $query->where('name', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%");
        })
        ->orderBy('start_date', 'desc')
        ->limit($limit)
        ->get()
        ->map(function ($event) {
            return [
                'id' => $event->id,
                'text' => $event->name . ' (' . ($event->start_date ? $event->start_date->format('Y-m-d') : 'N/A') . ')',
                'name' => $event->name,
                'start_date' => $event->start_date ? $event->start_date->format('Y-m-d') : 'N/A',
            ];
        });

        return response()->json($events);
    }

    public function getEventData(Request $request, $eventId)
    {
        $event = Event::with(['user', 'orders.user', 'orders.orderProducts.product'])
            ->findOrFail($eventId);

        // Get event statistics
        $totalOrders = $event->orders()->count();
        $completedOrders = $event->orders()->where('status', 1)->count();
        $cancelledOrders = $event->orders()->where('status', 2)->count();
        $totalRevenue = $event->orders()->sum('total_prices') ?? 0;
        $totalTaxes = $event->orders()->sum('total_taxes') ?? 0;
        $totalPaid = $event->orders()->sum('paid_amount') ?? 0;
        $totalRemaining = $event->orders()->sum('remaining_amount') ?? 0;

        // Get orders with details
        $orders = $event->orders()
            ->with(['user', 'orderProducts.product'])
            ->orderBy('order_date', 'desc')
            ->get();

        // Get commission details
        $commissionPercentage = $event->commission_percentage ?? 0;
        $totalCommission = ($totalRevenue * $commissionPercentage) / 100;

        // Get customer count
        $uniqueCustomers = $event->orders()->distinct('user_id')->count('user_id');

        return response()->json([
            'success' => true,
            'event' => [
                'id' => $event->id,
                'name' => $event->name,
                'description' => $event->description,
                'start_date' => $event->start_date ? $event->start_date->format('Y-m-d H:i') : '-',
                'end_date' => $event->end_date ? $event->end_date->format('Y-m-d H:i') : '-',
                'creator' => $event->user ? $event->user->name : 'Unknown',
                'commission_percentage' => $commissionPercentage,
            ],
            'statistics' => [
                'total_orders' => $totalOrders,
                'completed_orders' => $completedOrders,
                'cancelled_orders' => $cancelledOrders,
                'unique_customers' => $uniqueCustomers,
                'total_revenue' => number_format($totalRevenue, 2),
                'total_taxes' => number_format($totalTaxes, 2),
                'total_paid' => number_format($totalPaid, 2),
                'total_remaining' => number_format($totalRemaining, 2),
                'total_commission' => number_format($totalCommission, 2),
                'net_revenue' => number_format($totalRevenue - $totalCommission, 2),
            ],
            'orders' => $orders->map(function ($order) {
                return [
                    'id' => $order->id,
                    'number' => $order->number,
                    'date' => $order->order_date ? $order->order_date->format('Y-m-d') : '-',
                    'customer' => $order->user ? $order->user->name : 'Unknown',
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
