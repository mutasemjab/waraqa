@extends('layouts.provider')

@section('title', __('messages.orders'))
@section('page-title', __('messages.orders'))

@section('content')
<div class="page-header mb-4">
    <h1 class="page-title">{{ __('messages.my_sales') }}</h1>
    <p class="page-subtitle">{{ __('messages.manage_your_sales') }}</p>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">{{ __('messages.sales_list') }}</h5>
    </div>
    <div class="card-body">
        @if($orders->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>{{ __('messages.purchase_number') }}</th>
                            <th>{{ __('messages.quantity') }}</th>
                            <th>{{ __('messages.date') }}</th>
                            <th>{{ __('messages.total_amount') }}</th>
                            <th>{{ __('messages.expected_delivery') }}</th>
                            <th>{{ __('messages.status') }}</th>
                            <th>{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                        <tr>
                            <td>
                                <strong class="text-decoration-none">
                                    {{ $order->purchase_number ?? 'N/A' }}
                                </strong>
                            </td>
                            <td>
                                @php
                                    $totalQty = $order->items->sum('quantity');
                                @endphp
                                {{ $totalQty }}
                            </td>
                            <td>{{ $order->created_at->format('Y-m-d') }}</td>
                            <td><x-riyal-icon /> {{ number_format($order->total_amount, 2) }}</td>
                            <td>{{ $order->bookRequestResponse->expected_delivery_date ?? '-' }}</td>
                            <td>
                                @php
                                    $statusColors = [
                                        'pending' => 'warning',
                                        'confirmed' => 'info',
                                        'received' => 'success',
                                        'paid' => 'primary',
                                        'rejected' => 'danger'
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

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $orders->links() }}
            </div>
        @else
            <div class="alert alert-info" role="alert">
                <i class="fas fa-info-circle me-2"></i>
                {{ __('messages.no_purchases_found') }}
            </div>
        @endif
    </div>
</div>

@endsection
