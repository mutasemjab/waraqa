@extends('layouts.admin')
@section('title')
الرئيسية
@endsection


@section('css')
<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}
.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}
.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}
.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}
.border-left-danger {
    border-left: 0.25rem solid #e74a3b !important;
}
.border-left-dark {
    border-left: 0.25rem solid #5a5c69 !important;
}
.border-left-secondary {
    border-left: 0.25rem solid #858796 !important;
}
.badge-sm {
    font-size: 0.7rem;
    padding: 0.25rem 0.5rem;
}
.card-header {
    background-color: #f8f9fc;
    border-bottom: 1px solid #e3e6f0;
}
.chart-pie {
    position: relative;
    height: 15rem;
}
.quick-actions .btn {
    text-align: left;
    padding: 1rem;
    border-radius: 0.35rem;
    transition: all 0.3s;
}
.quick-actions .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
.card-footer .btn {
    color: white !important;
}
.card-footer .btn:hover {
    color: white !important;
}
</style>
@endsection



@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('messages.dashboard') }}</h1>
        <a href="{{ route('orders.index') }}" class="btn btn-primary">
            <i class="fas fa-list"></i> {{ __('messages.Orders') }}
        </a>
    </div>

    <!-- Statistics Cards Row -->
    <div class="row">
        <!-- Total Orders -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                {{ __('messages.users') }}
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total_users']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light py-2 text-center">
                    <a href="{{ route('sellers.index') }}" class="btn btn-sm btn-primary text-white">
                        <i class="fas fa-eye"></i> {{ __('messages.view_all') }}
                    </a>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                {{ __('messages.providers') }}
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total_providers']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light py-2 text-center">
                    <a href="{{ route('providers.index') }}" class="btn btn-sm btn-primary text-white">
                        <i class="fas fa-eye"></i> {{ __('messages.view_all') }}
                    </a>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                {{ __('messages.Orders') }}
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total_orders']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light py-2 text-center">
                    <a href="{{ route('orders.index') }}" class="btn btn-sm btn-primary text-white">
                        <i class="fas fa-eye"></i> {{ __('messages.view_all') }}
                    </a>
                </div>
            </div>
        </div>

        <!-- Pending Orders -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-{{ \App\Enums\OrderStatus::PENDING->getColor() }} shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-{{ \App\Enums\OrderStatus::PENDING->getColor() }} text-uppercase mb-1">
                                {{ __('messages.pending_orders') }}
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['pending_orders']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light py-2 text-center">
                    <a href="{{ route('orders.index', ['status' => \App\Enums\OrderStatus::PENDING->value]) }}" class="btn btn-sm btn-{{ \App\Enums\OrderStatus::PENDING->getColor() }} text-white">
                        <i class="fas fa-eye"></i> {{ __('messages.view_all') }}
                    </a>
                </div>
            </div>
        </div>

        <!-- Completed Orders -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-{{ \App\Enums\OrderStatus::DONE->getColor() }} shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-{{ \App\Enums\OrderStatus::DONE->getColor() }} text-uppercase mb-1">
                                {{ __('messages.completed_orders') }}
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['completed_orders']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light py-2 text-center">
                    <a href="{{ route('orders.index', ['status' => \App\Enums\OrderStatus::DONE->value]) }}" class="btn btn-sm btn-{{ \App\Enums\OrderStatus::DONE->getColor() }} text-white">
                        <i class="fas fa-eye"></i> {{ __('messages.view_all') }}
                    </a>
                </div>
            </div>
        </div>

        <!-- Today's Revenue -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                {{ __('messages.today_revenue') }}
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><x-riyal-icon /> {{ number_format($stats['revenue_today'], 2) }}</div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light py-2 text-center">
                    <a href="{{ route('orders.index', ['date' => today()->format('Y-m-d')]) }}" class="btn btn-sm btn-info text-white">
                        <i class="fas fa-eye"></i> {{ __('messages.view_all') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Second Row Stats -->
    <div class="row">
        <!-- Today's Orders -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-dark shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">
                                {{ __('messages.today_orders') }}
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['today_orders']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light py-2 text-center">
                    <a href="{{ route('orders.index', ['date' => today()->format('Y-m-d')]) }}" class="btn btn-sm btn-dark text-white">
                        <i class="fas fa-eye"></i> {{ __('messages.view_all') }}
                    </a>
                </div>
            </div>
        </div>

        <!-- Cancelled Orders -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-{{ \App\Enums\OrderStatus::CANCELLED->getColor() }} shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-{{ \App\Enums\OrderStatus::CANCELLED->getColor() }} text-uppercase mb-1">
                                {{ __('messages.cancelled_orders') }}
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['cancelled_orders']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light py-2 text-center">
                    <a href="{{ route('orders.index', ['status' => \App\Enums\OrderStatus::CANCELLED->value]) }}" class="btn btn-sm btn-{{ \App\Enums\OrderStatus::CANCELLED->getColor() }} text-white">
                        <i class="fas fa-eye"></i> {{ __('messages.view_all') }}
                    </a>
                </div>
            </div>
        </div>

        <!-- Monthly Revenue -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                {{ __('messages.monthly_revenue') }}
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><x-riyal-icon /> {{ number_format($stats['revenue_month'], 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light py-2 text-center">
                    <a href="{{ route('orders.index', ['month' => now()->format('Y-m'), 'status' => \App\Enums\OrderStatus::DONE->value]) }}" class="btn btn-sm btn-secondary text-white">
                        <i class="fas fa-eye"></i> {{ __('messages.view_all') }}
                    </a>
                </div>
            </div>
        </div>

        <!-- Order Success Rate -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-{{ \App\Enums\OrderStatus::DONE->getColor() }} shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-{{ \App\Enums\OrderStatus::DONE->getColor() }} text-uppercase mb-1">
                                {{ __('messages.success_rate') }}
                            </div>
                            @php
                                // Success rate = completed orders / total orders (regardless of payment status)
                                $successRate = $stats['total_orders'] > 0 ? ($stats['completed_orders'] / $stats['total_orders']) * 100 : 0;
                            @endphp
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($successRate, 1) }}%</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percentage fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light py-2 text-center">
                    <a href="{{ route('orders.index', ['status' => \App\Enums\OrderStatus::DONE->value]) }}" class="btn btn-sm btn-{{ \App\Enums\OrderStatus::DONE->getColor() }} text-white">
                        <i class="fas fa-eye"></i> {{ __('messages.view_all') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Orders by Status Chart -->
        <div class="col-xl-6 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('messages.orders_by_status') }}</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="ordersStatusChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        @foreach(\App\Enums\OrderStatus::cases() as $status)
                        <span class="mr-2">
                            <i class="fas fa-circle text-{{ $status->getColor() }}"></i> {{ $status->getLabelLocalized() }}
                        </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="col-xl-6 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('messages.recent_orders') }}</h6>
                    <a href="{{ route('orders.index') }}" class="btn btn-sm btn-primary">{{ __('messages.view_all') }}</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive" style="max-height: 400px;">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>{{ __('messages.order_number') }}</th>
                                    <th>{{ __('messages.user') }}</th>
                                    <th>{{ __('messages.date') }}</th>
                                    <th>{{ __('messages.status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentOrders as $order)
                                <tr>
                                    <td>
                                        <a href="{{ route('orders.show', $order->id) }}" class="text-decoration-none">
                                            {{ $order->number }}
                                        </a>
                                    </td>
                                    <td>{{ $order->user->name ?? 'N/A' }}</td>
                                    <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
                                    <td>
                                        @php
                                            $statusEnum = \App\Enums\OrderStatus::tryFrom($order->status);
                                        @endphp
                                        <span class="badge badge-{{ $statusEnum?->getColor() ?? 'secondary' }} badge-sm">
                                            {{ $statusEnum?->getLabelLocalized() ?? 'N/A' }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">{{ __('messages.no_orders_found') }}</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('messages.quick_actions') }}</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('orders.index', ['status' => \App\Enums\OrderStatus::PENDING->value]) }}" class="btn btn-{{ \App\Enums\OrderStatus::PENDING->getColor() }} btn-block">
                                <i class="fas fa-clock"></i> {{ __('messages.pending_orders') }}
                                <span class="badge badge-light ml-2">{{ $stats['pending_orders'] }}</span>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('orders.index', ['date' => today()->format('Y-m-d')]) }}" class="btn btn-info btn-block">
                                <i class="fas fa-calendar-day"></i> {{ __('messages.today_orders') }}
                                <span class="badge badge-light ml-2">{{ $stats['today_orders'] }}</span>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('orders.index', ['status' => \App\Enums\OrderStatus::DONE->value]) }}" class="btn btn-{{ \App\Enums\OrderStatus::DONE->getColor() }} btn-block">
                                <i class="fas fa-check-circle"></i> {{ __('messages.completed_orders') }}
                                <span class="badge badge-light ml-2">{{ $stats['completed_orders'] }}</span>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('orders.index', ['status' => \App\Enums\OrderStatus::CANCELLED->value]) }}" class="btn btn-{{ \App\Enums\OrderStatus::CANCELLED->getColor() }} btn-block">
                                <i class="fas fa-times-circle"></i> {{ __('messages.cancelled_orders') }}
                                <span class="badge badge-light ml-2">{{ $stats['cancelled_orders'] }}</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // Orders by Status Pie Chart
    var ctx = document.getElementById('ordersStatusChart').getContext('2d');
    var ordersData = @json($ordersByStatus);

    var statusLabels = {
        'pending': '{{ \App\Enums\OrderStatus::PENDING->getLabelLocalized() }}',
        'completed': '{{ \App\Enums\OrderStatus::DONE->getLabelLocalized() }}',
        'cancelled': '{{ \App\Enums\OrderStatus::CANCELLED->getLabelLocalized() }}',
        'refund': '{{ \App\Enums\OrderStatus::REFUNDED->getLabelLocalized() }}'
    };

    var statusColors = {
        'pending': '#36b9cc',  // info (blue) - Pending
        'completed': '#1cc88a', // success (green) - Done
        'cancelled': '#e74a3b', // danger (red) - Cancelled
        'refund': '#f6c23e'    // warning (yellow) - Refunded
    };

    var labels = [];
    var data = [];
    var colors = [];

    Object.keys(ordersData).forEach(function(key) {
        if (ordersData[key] > 0) {
            labels.push(statusLabels[key] || 'Unknown');
            data.push(ordersData[key]);
            colors.push(statusColors[key] || '#858796');
        }
    });
    
    var myPieChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: colors,
                hoverBackgroundColor: colors.map(color => color + 'CC'),
                hoverBorderColor: "rgba(234, 236, 244, 1)",
            }],
        },
        options: {
            maintainAspectRatio: false,
            tooltips: {
                backgroundColor: "rgb(255,255,255)",
                bodyFontColor: "#858796",
                borderColor: '#dddfeb',
                borderWidth: 1,
                xPadding: 15,
                yPadding: 15,
                displayColors: false,
                caretPadding: 10,
                callbacks: {
                    label: function(tooltipItem, data) {
                        var dataset = data.datasets[tooltipItem.datasetIndex];
                        var total = dataset.data.reduce(function(previousValue, currentValue) {
                            return previousValue + currentValue;
                        });
                        var currentValue = dataset.data[tooltipItem.index];
                        var percentage = Math.floor(((currentValue / total) * 100) + 0.5);
                        return data.labels[tooltipItem.index] + ': ' + currentValue + ' (' + percentage + '%)';
                    }
                }
            },
            legend: {
                display: false
            },
            cutoutPercentage: 70,
        },
    });

    // Auto-refresh dashboard every 60 seconds
    setInterval(function() {
        location.reload();
    }, 60000);
});
</script>
@endsection

