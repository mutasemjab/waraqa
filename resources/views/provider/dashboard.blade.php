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
            <p>{{ __('messages.items_provided') }}</p>
        </div>
    </div>
</div>


<!-- Completed Purchases -->
<div class="row mt-4">
    <div class="col-lg-12">
        @if($completedPurchases->count() > 0)
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-check-circle me-2"></i>{{ __('messages.completed_purchases') }}
                    </h5>
                    <a href="{{ route('provider.orders') }}" class="btn btn-sm btn-light">
                        {{ __('messages.view_all') }}
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('messages.purchase_number') }}</th>
                                    <th>{{ __('messages.products') }}</th>
                                    <th>{{ __('messages.quantity') }}</th>
                                    <th>{{ __('messages.date') }}</th>
                                    <th>{{ __('messages.total_amount') }}</th>
                                    <th>{{ __('messages.status') }}</th>
                                    <th>{{ __('messages.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($completedPurchases as $order)
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
                                    <td>
                                        @php
                                            $statusColors = [
                                                'confirmed' => 'info',
                                                'received' => 'success',
                                                'paid' => 'primary'
                                            ];
                                            $statusColor = $statusColors[$order->status] ?? 'secondary';
                                        @endphp
                                        <span class="badge bg-{{ $statusColor }}">{{ ucfirst($order->status) }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('provider.purchases.show', $order->id) }}" class="btn btn-sm btn-info" title="{{ __('messages.view') }}">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Pending Book Requests -->
<div class="row mt-4">
    <div class="col-lg-12">
        @if($pendingBookRequests->count() > 0)
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center bg-warning">
                    <h5 class="mb-0">
                        <i class="fas fa-list-check me-2"></i>{{ __('messages.pending_book_requests') }}
                    </h5>
                    <a href="{{ route('provider.bookRequests') }}" class="btn btn-sm btn-light">
                        {{ __('messages.view_all') }}
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('messages.request_number') }}</th>
                                    <th>{{ __('messages.quantity') }}</th>
                                    <th>{{ __('messages.requested_date') }}</th>
                                    <th>{{ __('messages.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingBookRequests as $request)
                                <tr>
                                    <td>
                                        <strong>#{{ $request->id }}</strong>
                                    </td>
                                    <td>
                                        <small>
                                            @php
                                                $totalQty = $request->items->sum('requested_quantity');
                                            @endphp
                                            {{ $totalQty }} {{ __('messages.units') }}
                                        </small>
                                    </td>
                                    <td>
                                        <small>{{ $request->created_at->format('Y-m-d') }}</small>
                                    </td>
                                    <td>
                                        <a href="{{ route('provider.bookRequests') }}" class="btn btn-sm btn-warning" title="{{ __('messages.respond') }}">
                                            <i class="fas fa-reply"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection