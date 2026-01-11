@extends('layouts.provider')

@section('title', __('messages.Orders'))
@section('page-title', __('messages.Orders'))

@section('content')
<div class="page-header mb-4">
    <h1 class="page-title">{{ __('messages.Orders') }}</h1>
    <p class="page-subtitle">{{ __('messages.manage_orders') }}</p>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">{{ __('messages.orders_list') }}</h5>
    </div>
    <div class="card-body">
        @if($orders->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>{{ __('messages.order_number') }}</th>
                            <th>{{ __('messages.user') }}</th>
                            <th>{{ __('messages.date') }}</th>
                            <th>{{ __('messages.total_price') }}</th>
                            <th>{{ __('messages.status') }}</th>
                            <th>{{ __('messages.payment_status') }}</th>
                            <th>{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                        <tr>
                            <td>
                                <a href="{{ route('provider.orders') }}" class="text-decoration-none fw-bold">
                                    {{ $order->number ?? 'N/A' }}
                                </a>
                            </td>
                            <td>{{ $order->user->name ?? 'N/A' }}</td>
                            <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
                            <td><x-riyal-icon /> {{ number_format($order->total_prices, 2) }}</td>
                            <td>
                                @php
                                    $statusClass = $order->status == 1 ? 'success' : ($order->status == 2 ? 'danger' : 'warning');
                                    $statusText = $order->status == 1 ? __('messages.completed') : ($order->status == 2 ? __('messages.cancelled') : __('messages.pending'));
                                @endphp
                                <span class="badge bg-{{ $statusClass }}">{{ $statusText }}</span>
                            </td>
                            <td>
                                @php
                                    $paymentClass = $order->payment_status == 1 ? 'success' : 'warning';
                                    $paymentText = $order->payment_status == 1 ? __('messages.paid') : __('messages.unpaid');
                                @endphp
                                <span class="badge bg-{{ $paymentClass }}">{{ $paymentText }}</span>
                            </td>
                            <td>
                                <a href="{{ route('provider.orders') }}" class="btn btn-sm btn-info" title="{{ __('messages.view') }}">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $orders->links() }}
            </div>
        @else
            <div class="alert alert-info" role="alert">
                <i class="fas fa-info-circle me-2"></i>
                {{ __('messages.no_orders_found') }}
            </div>
        @endif
    </div>
</div>

@endsection
