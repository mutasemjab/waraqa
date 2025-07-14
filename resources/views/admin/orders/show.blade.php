{{-- resources/views/admin/orders/show.blade.php --}}
@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>{{ __('messages.order_details') }} - {{ $order->number }}</h4>
                    <div>
                        <a href="{{ route('orders.edit', $order) }}" class="btn btn-warning">{{ __('messages.edit_order') }}</a>
                   
                        <a href="{{ route('orders.index') }}" class="btn btn-secondary">{{ __('messages.back_to_list') }}</a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <div class="row mb-4">
                        <!-- Order Information -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>{{ __('messages.order_information') }}</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>{{ __('messages.order_number') }}:</strong></td>
                                            <td>{{ $order->number }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('messages.date') }}:</strong></td>
                                            <td>{{ $order->date->format('M d, Y H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('messages.status') }}:</strong></td>
                                            <td>
                                                @if($order->status == 1)
                                                    <span class="badge bg-success">{{ __('messages.done') }}</span>
                                                @elseif($order->status == 2)
                                                    <span class="badge bg-danger">{{ __('messages.canceled') }}</span>
                                                @else
                                                    <span class="badge bg-info">{{ __('messages.refund') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('messages.payment_status') }}:</strong></td>
                                            <td>
                                                @if($order->payment_status == 1)
                                                    <span class="badge bg-success">{{ __('messages.paid') }}</span>
                                                @else
                                                    <span class="badge bg-warning">{{ __('messages.unpaid') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @if($order->note)
                                        <tr>
                                            <td><strong>{{ __('messages.note') }}:</strong></td>
                                            <td>{{ $order->note }}</td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Customer Information -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>{{ __('messages.customer_information') }}</h5>
                                </div>
                                <div class="card-body">
                                    @if($order->user)
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>{{ __('messages.name') }}:</strong></td>
                                            <td>{{ $order->user->name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('messages.phone') }}:</strong></td>
                                            <td>{{ $order->user->phone }}</td>
                                        </tr>
                                        @if($order->user->email)
                                        <tr>
                                            <td><strong>{{ __('messages.email') }}:</strong></td>
                                            <td>{{ $order->user->email }}</td>
                                        </tr>
                                        @endif
                                    </table>
                                    @else
                                    <p class="text-muted">{{ __('messages.no_customer_assigned') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Products -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5>{{ __('messages.order_products') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>{{ __('messages.product_name') }}</th>
                                            <th>{{ __('messages.unit_price') }}</th>
                                            <th>{{ __('messages.quantity') }}</th>
                                            <th>{{ __('messages.tax_percentage') }}</th>
                                            <th>{{ __('messages.tax_value') }}</th>
                                            <th>{{ __('messages.total_before_tax') }}</th>
                                            <th>{{ __('messages.total_after_tax') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($order->orderProducts as $orderProduct)
                                        <tr>
                                            <td>
                                                <strong>{{ $orderProduct->product->name_en }}</strong><br>
                                                <small class="text-muted">{{ $orderProduct->product->name_ar }}</small>
                                            </td>
                                            <td>${{ number_format($orderProduct->unit_price, 2) }}</td>
                                            <td>{{ $orderProduct->quantity }}</td>
                                            <td>{{ $orderProduct->tax_percentage }}%</td>
                                            <td>${{ number_format($orderProduct->tax_value, 2) }}</td>
                                            <td>${{ number_format($orderProduct->total_price_before_tax, 2) }}</td>
                                            <td><strong>${{ number_format($orderProduct->total_price_after_tax, 2) }}</strong></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-active">
                                            <th colspan="4">{{ __('messages.totals') }}</th>
                                            <th>${{ number_format($order->total_taxes, 2) }}</th>
                                            <th>${{ number_format($order->total_prices - $order->total_taxes, 2) }}</th>
                                            <th><strong>${{ number_format($order->total_prices, 2) }}</strong></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Information -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>{{ __('messages.payment_information') }}</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>{{ __('messages.total_amount') }}:</strong></td>
                                            <td>${{ number_format($order->total_prices, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('messages.paid_amount') }}:</strong></td>
                                            <td class="text-success">${{ number_format($order->paid_amount, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('messages.remaining_amount') }}:</strong></td>
                                            <td class="{{ $order->remaining_amount > 0 ? 'text-danger' : 'text-success' }}">
                                                ${{ number_format($order->remaining_amount, 2) }}
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Debt Information -->
                        @if($order->userDebt && $order->userDebt->status == 1)
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>{{ __('messages.debt_information') }}</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>{{ __('messages.debt_status') }}:</strong></td>
                                            <td>
                                                <span class="badge bg-warning">{{ __('messages.active') }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('messages.debt_created') }}:</strong></td>
                                            <td>{{ $order->userDebt->created_at->format('M d, Y H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('messages.last_payment') }}:</strong></td>
                                            <td>{{ $order->userDebt->updated_at->format('M d, Y H:i') }}</td>
                                        </tr>
                                    </table>
                                    
                                    @if($order->remaining_amount > 0)
                                    <button type="button" class="btn btn-success btn-sm" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#paymentModal">
                                        {{ __('messages.receive_payment') }}
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('messages.confirm_delete') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>{{ __('messages.are_you_sure') }}</p>
                <p><strong>{{ __('messages.order_number') }}:</strong> {{ $order->number }}</p>
                <p class="text-danger">{{ __('messages.this_action_cannot_be_undone') }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                <form action="{{ route('orders.destroy', $order) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">{{ __('messages.delete') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Payment Modal -->
@if($order->userDebt && $order->remaining_amount > 0)
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('messages.receive_payment') }} - {{ $order->user->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('receive-payment', $order->userDebt) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>{{ __('messages.current_debt') }}:</strong><br>
                            ${{ number_format($order->remaining_amount, 2) }}
                        </div>
                        <div class="col-md-6">
                            <strong>{{ __('messages.order_number') }}:</strong><br>
                            {{ $order->number }}
                        </div>
                    </div>
                    <hr>
                    <div class="form-group">
                        <label for="payment_amount">{{ __('messages.payment_amount') }}</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" 
                                   name="payment_amount" 
                                   id="payment_amount"
                                   class="form-control" 
                                   min="0" 
                                   max="{{ $order->remaining_amount }}"
                                   step="0.01" 
                                   required>
                        </div>
                        <small class="text-muted">{{ __('messages.payment_amount_max') }}</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('messages.confirm') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection