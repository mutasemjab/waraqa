{{-- resources/views/admin/orders/index.blade.php --}}
@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">{{ __('messages.orders_management') }}</h4>
                    <a href="{{ route('orders.create') }}" class="btn btn-primary">{{ __('messages.create_new_order') }}</a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <!-- Filters -->
                    <div class="card mb-4 border">
                        <div class="card-header bg-light py-2">
                            <h6 class="mb-0"><i class="fas fa-filter mr-2"></i>{{ __('messages.filters') }}</h6>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="{{ route('orders.index') }}">
                                <div class="row">
                                    <!-- Order Number -->
                                    <div class="col-md-3 mb-3">
                                        <label for="order_number">{{ __('messages.order_number') }}</label>
                                        <input type="text" name="order_number" id="order_number" class="form-control"
                                               value="{{ request('order_number') }}" placeholder="{{ __('messages.order_number') }}">
                                    </div>

                                    <!-- User Search Select -->
                                    <div class="col-md-3 mb-3">
                                        <x-search-select
                                            model="App\Models\User"
                                            fieldName="user_id"
                                            label="user"
                                            placeholder="Search..."
                                            limit="10"
                                            :value="request('user_id')"
                                            filter="with_roles:seller,customer"
                                        />
                                    </div>

                                    <!-- Status -->
                                    <div class="col-md-3 mb-3">
                                        <label for="status">{{ __('messages.status') }}</label>
                                        <select name="status" id="status" class="form-control">
                                            <option value="">{{ __('messages.all') }}</option>
                                            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>{{ __('messages.done') }}</option>
                                            <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>{{ __('messages.canceled') }}</option>
                                            <option value="6" {{ request('status') == '6' ? 'selected' : '' }}>{{ __('messages.refund') }}</option>
                                        </select>
                                    </div>

                                    <!-- Payment Status -->
                                    <div class="col-md-3 mb-3">
                                        <label for="payment_status">{{ __('messages.payment_status') }}</label>
                                        <select name="payment_status" id="payment_status" class="form-control">
                                            <option value="">{{ __('messages.all') }}</option>
                                            <option value="1" {{ request('payment_status') == '1' ? 'selected' : '' }}>{{ __('messages.paid') }}</option>
                                            <option value="2" {{ request('payment_status') == '2' ? 'selected' : '' }}>{{ __('messages.unpaid') }}</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- From Date -->
                                    <div class="col-md-3 mb-3">
                                        <label for="from_date">{{ __('messages.from_date') }}</label>
                                        <input type="date" name="from_date" id="from_date" class="form-control"
                                               value="{{ request('from_date') }}">
                                    </div>

                                    <!-- To Date -->
                                    <div class="col-md-3 mb-3">
                                        <label for="to_date">{{ __('messages.to_date') }}</label>
                                        <input type="date" name="to_date" id="to_date" class="form-control"
                                               value="{{ request('to_date') }}">
                                    </div>

                                    <!-- Filter Buttons -->
                                    <div class="col-md-6 mb-3 d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary mr-2">
                                            <i class="fas fa-search mr-1"></i>{{ __('messages.filter') }}
                                        </button>
                                        <a href="{{ route('orders.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-times mr-1"></i>{{ __('messages.clear_filters') }}
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

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