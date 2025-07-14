@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('messages.debt_details') }} #{{ $userDept->id }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('user_depts.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> {{ __('messages.back') }}
                        </a>
                        <a href="{{ route('user_depts.edit', $userDept) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> {{ __('messages.edit') }}
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <!-- User Information -->
                        <div class="col-md-6">
                            <div class="card card-outline card-primary">
                                <div class="card-header">
                                    <h5 class="card-title">{{ __('messages.user_information') }}</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>{{ __('messages.name') }}:</strong></td>
                                            <td>{{ $userDept->user->name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('messages.email') }}:</strong></td>
                                            <td>{{ $userDept->user->email }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('messages.phone') }}:</strong></td>
                                            <td>{{ $userDept->user->phone ?? __('messages.not_specified') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Debt Information -->
                        <div class="col-md-6">
                            <div class="card card-outline card-info">
                                <div class="card-header">
                                    <h5 class="card-title">{{ __('messages.debt_information') }}</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>{{ __('messages.total_amount') }}:</strong></td>
                                            <td class="text-right">{{ number_format($userDept->total_amount, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('messages.paid_amount') }}:</strong></td>
                                            <td class="text-right text-success">{{ number_format($userDept->paid_amount, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('messages.remaining_amount') }}:</strong></td>
                                            <td class="text-right {{ $userDept->remaining_amount > 0 ? 'text-danger' : 'text-success' }}">
                                                <strong>{{ number_format($userDept->remaining_amount, 2) }}</strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('messages.status') }}:</strong></td>
                                            <td>
                                                <span class="badge {{ $userDept->status_badge }}">
                                                    {{ $userDept->status_label }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('messages.created_at') }}:</strong></td>
                                            <td>{{ $userDept->created_at->format('Y-m-d H:i') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Information -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card card-outline card-warning">
                                <div class="card-header">
                                    <h5 class="card-title">{{ __('messages.related_order') }}</h5>
                                    <div class="card-tools">
                                        <a href="{{ route('orders.show', $userDept->order) }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-eye"></i> {{ __('messages.view_order') }}
                                        </a>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <strong>{{ __('messages.order_number') }}:</strong><br>
                                            {{ $userDept->order->number }}
                                        </div>
                                        <div class="col-md-3">
                                            <strong>{{ __('messages.order_date') }}:</strong><br>
                                            {{ $userDept->order->date }}
                                        </div>
                                        <div class="col-md-3">
                                            <strong>{{ __('messages.order_total') }}:</strong><br>
                                            {{ number_format($userDept->order->total_prices, 2) }}
                                        </div>
                                        <div class="col-md-3">
                                            <strong>{{ __('messages.order_status') }}:</strong><br>
                                            <span class="badge badge-{{ $userDept->order->status == 1 ? 'success' : 'warning' }}">
                                                {{ $userDept->order->status == 1 ? __('messages.completed') : __('messages.pending') }}
                                            </span>
                                        </div>
                                    </div>

                                    @if($userDept->order->orderProducts && $userDept->order->orderProducts->count() > 0)
                                        <div class="table-responsive mt-3">
                                            <h6>{{ __('messages.order_products') }}:</h6>
                                            <table class="table table-sm table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('messages.product') }}</th>
                                                        <th>{{ __('messages.quantity') }}</th>
                                                        <th>{{ __('messages.unit_price') }}</th>
                                                        <th>{{ __('messages.total_price') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($userDept->order->orderProducts as $orderProduct)
                                                        <tr>
                                                            <td>{{ $orderProduct->product->name_ar ?? $orderProduct->product->name }}</td>
                                                            <td class="text-center">{{ $orderProduct->quantity }}</td>
                                                            <td class="text-right">{{ number_format($orderProduct->unit_price, 2) }}</td>
                                                            <td class="text-right">{{ number_format($orderProduct->total_price_after_tax, 2) }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Actions -->
                    @if($userDept->status == 1 && $userDept->remaining_amount > 0)
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="card card-outline card-success">
                                    <div class="card-header">
                                        <h5 class="card-title">{{ __('messages.payment_actions') }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#paymentModal">
                                            <i class="fas fa-money-bill"></i> {{ __('messages.make_payment') }}
                                        </button>
                                        
                                        <div class="alert alert-info mt-3">
                                            <i class="fas fa-info-circle"></i>
                                            {{ __('messages.remaining_amount_to_pay') }}: <strong>{{ number_format($userDept->remaining_amount, 2) }}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Modal -->
@if($userDept->status == 1 && $userDept->remaining_amount > 0)
    <div class="modal fade" id="paymentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('user_depts.make_payment', $userDept) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('messages.make_payment') }}</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>{{ __('messages.user') }}</label>
                            <input type="text" class="form-control" value="{{ $userDept->user->name }}" readonly>
                        </div>
                        <div class="form-group">
                            <label>{{ __('messages.remaining_amount') }}</label>
                            <input type="text" class="form-control" value="{{ number_format($userDept->remaining_amount, 2) }}" readonly>
                        </div>
                        <div class="form-group">
                            <label>{{ __('messages.payment_amount') }} <span class="text-danger">*</span></label>
                            <input type="number" name="payment_amount" class="form-control" step="0.01" max="{{ $userDept->remaining_amount }}" required>
                            <small class="form-text text-muted">{{ __('messages.maximum_payment_amount') }}: {{ number_format($userDept->remaining_amount, 2) }}</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('messages.cancel') }}</button>
                        <button type="submit" class="btn btn-success">{{ __('messages.record_payment') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
@endsection