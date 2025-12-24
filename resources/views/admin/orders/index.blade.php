{{-- resources/views/admin/orders/index.blade.php --}}
@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>{{ __('messages.orders_management') }}</h4>
                    <a href="{{ route('orders.create') }}" class="btn btn-primary">{{ __('messages.create_new_order') }}</a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>{{ __('messages.order_number') }}</th>
                                    <th>{{ __('messages.user') }}</th>
                                    <th>{{ __('messages.date') }}</th>
                                    <th>{{ __('messages.total') }}</th>
                                    <th>{{ __('messages.paid') }}</th>
                                    <th>{{ __('messages.remaining') }}</th>
                                    <th>{{ __('messages.status') }}</th>
                                    <th>{{ __('messages.payment_status') }}</th>
                                    <th>{{ __('messages.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)
                                <tr>
                                    <td>{{ $order->number }}</td>
                                    <td>{{ $order->user->name ?? 'N/A' }}</td>
                                    <td>{{ $order->date->format('M d, Y') }}</td>
                                    <td><x-riyal-icon /> {{ number_format($order->total_prices, 2) }}</td>
                                    <td><x-riyal-icon /> {{ number_format($order->paid_amount, 2) }}</td>
                                    <td>
                                        <span class="badge {{ $order->remaining_amount > 0 ? 'bg-warning' : 'bg-success' }}">
                                            <x-riyal-icon /> {{ number_format($order->remaining_amount, 2) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($order->status == 1)
                                            <span class="badge bg-success">{{ __('messages.done') }}</span>
                                        @elseif($order->status == 2)
                                            <span class="badge bg-danger">{{ __('messages.canceled') }}</span>
                                        @else
                                            <span class="badge bg-info">{{ __('messages.refund') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($order->payment_status == 1)
                                            <span class="badge bg-success">{{ __('messages.paid') }}</span>
                                        @else
                                            <span class="badge bg-warning">{{ __('messages.unpaid') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-info">{{ __('messages.view') }}</a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center">{{ __('messages.no_orders_found') }}</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    {{ $orders->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection