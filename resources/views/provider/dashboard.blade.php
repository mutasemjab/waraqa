@extends('layouts.provider')

@section('title', __('messages.provider_dashboard'))
@section('page-title', __('messages.dashboard'))

@section('content')
<div class="page-header">
    <h1 class="page-title">{{ __('messages.welcome_back') }}, {{ auth()->user()->name }}!</h1>
    <p class="page-subtitle">{{ __('messages.provider_dashboard_subtitle') }}</p>
</div>

<!-- Stats Grid -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon primary">
            <i class="fas fa-box"></i>
        </div>
        <div class="stat-content">
            <h3>{{ $stats['total_products'] }}</h3>
            <p>{{ __('messages.my_products') }}</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon success">
            <i class="fas fa-clipboard-list"></i>
        </div>
        <div class="stat-content">
            <h3>{{ $stats['total_orders'] }}</h3>
            <p>{{ __('messages.total_orders') }}</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon info">
            <i class="fas fa-shopping-cart"></i>
        </div>
        <div class="stat-content">
            <h3>{{ $stats['total_sold_items'] }}</h3>
            <p>{{ __('messages.items_sold') }}</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon warning">
            <i class="fas fa-dollar-sign"></i>
        </div>
        <div class="stat-content">
            <h3><x-riyal-icon /> {{ number_format($stats['total_revenue'], 2) }}</h3>
            <p>{{ __('messages.total_revenue') }}</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon success">
            <i class="fas fa-calendar"></i>
        </div>
        <div class="stat-content">
            <h3><x-riyal-icon /> {{ number_format($stats['monthly_revenue'], 2) }}</h3>
            <p>{{ __('messages.monthly_revenue') }}</p>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="row">
    <!-- Full Width -->
    <div class="col-lg-12">
        <!-- Recent Orders -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-shopping-cart me-2"></i>{{ __('messages.recent_orders_containing_my_products') }}
                </h5>
                <a href="{{ route('provider.orders') }}" class="btn btn-sm btn-outline-primary">
                    {{ __('messages.view_all') }}
                </a>
            </div>
            <div class="card-body">
                @if($recentOrders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('messages.purchase_number') }}</th>
                                    <th>{{ __('messages.products') }}</th>
                                    <th>{{ __('messages.quantity') }}</th>
                                    <th>{{ __('messages.date') }}</th>
                                    <th>{{ __('messages.total_amount') }}</th>
                                    <th>{{ __('messages.expected_delivery') }}</th>
                                    <th>{{ __('messages.status') }}</th>
                                    <th>{{ __('messages.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentOrders as $order)
                                <tr>
                                    <td>
                                        <strong class="text-decoration-none">
                                            {{ $order->purchase_number ?? 'N/A' }}
                                        </strong>
                                    </td>
                                    <td>
                                        <small>
                                            @foreach($order->items as $item)
                                                <div>{{ $item->product ? (app()->getLocale() == 'ar' ? $item->product->name_ar : $item->product->name_en) : 'N/A' }}</div>
                                            @endforeach
                                        </small>
                                    </td>
                                    <td>
                                        <small>
                                            @php
                                                $totalQty = $order->items->sum('quantity');
                                            @endphp
                                            {{ $totalQty }} {{ __('messages.items') }}
                                        </small>
                                    </td>
                                    <td>{{ $order->created_at->format('Y-m-d') }}</td>
                                    <td><x-riyal-icon /> {{ number_format($order->total_amount, 2) }}</td>
                                    <td>{{ $order->expected_delivery_date ? $order->expected_delivery_date->format('Y-m-d') : '-' }}</td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'pending' => 'warning',
                                                'confirmed' => 'info',
                                                'received' => 'success',
                                                'paid' => 'primary'
                                            ];
                                            $statusColor = $statusColors[$order->status] ?? 'secondary';
                                        @endphp
                                        <span class="badge bg-{{ $statusColor }}">{{ ucfirst($order->status) }}</span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-info" title="{{ __('messages.view') }}" onclick="viewPurchaseDetails({{ $order->id }})">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info" role="alert">
                        <i class="fas fa-info-circle me-2"></i>
                        {{ __('messages.no_purchases_found') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection