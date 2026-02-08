@extends('layouts.user')

@section('title', __('messages.order_details'))
@section('page-title', __('messages.order_details'))

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">{{ __('messages.order_details') }}</h1>
            <p class="page-subtitle">{{ __('messages.order_number') }}: <strong>{{ $order->number }}</strong></p>
        </div>
        <div>
            <a href="{{ route('user.orders') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>{{ __('messages.back_to_orders') }}
            </a>
        </div>
    </div>
</div>

<!-- Order Summary -->
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>{{ __('messages.order_information') }}
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>{{ __('messages.order_number') }}:</strong> {{ $order->number }}</p>
                        <p><strong>{{ __('messages.order_date') }}:</strong> {{ Carbon\Carbon::parse($order->date)->format('Y-m-d H:i') }}</p>
                        <p><strong>{{ __('messages.total_items') }}:</strong> {{ $order->orderProducts->sum('quantity') }}</p>
                        @if($order->note)
                            <p><strong>{{ __('messages.order_note') }}:</strong> {{ $order->note }}</p>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <p><strong>{{ __('messages.order_status') }}:</strong>
                            {!! \App\Enums\OrderStatus::tryFrom($order->status)?->getBadgeHtml() ?? '<span class="badge bg-secondary">N/A</span>' !!}
                        </p>
                        <p><strong>{{ __('messages.payment_status') }}:</strong>
                            {!! \App\Enums\PaymentStatus::tryFrom($order->payment_status)?->getBadgeHtml() ?? '<span class="badge bg-secondary">N/A</span>' !!}
                        </p>
                        <p><strong>{{ __('messages.created_at') }}:</strong> {{ $order->created_at->format('Y-m-d H:i') }}</p>
                        <p><strong>{{ __('messages.updated_at') }}:</strong> {{ $order->updated_at->format('Y-m-d H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-calculator me-2"></i>{{ __('messages.order_summary') }}
                </h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>{{ __('messages.subtotal') }}:</span>
                    <span><x-riyal-icon /> {{ number_format($order->orderProducts->sum('total_price_after_tax'), 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>{{ __('messages.tax') }}:</span>
                    <span><x-riyal-icon /> {{ number_format($order->orderProducts->sum(function($item) { return $item->total_price_after_tax - $item->total_price; }), 2) }}</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-3">
                    <strong>{{ __('messages.total_amount') }}:</strong>
                    <strong class="text-primary"><x-riyal-icon /> {{ number_format($order->total_prices, 2) }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-success">{{ __('messages.paid_amount') }}:</span>
                    <span class="text-success"><x-riyal-icon /> {{ number_format($order->paid_amount, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span class="{{ $order->remaining_amount > 0 ? 'text-danger' : 'text-success' }}">{{ __('messages.remaining_amount') }}:</span>
                    <span class="{{ $order->remaining_amount > 0 ? 'text-danger' : 'text-success' }}">
                        @if($order->remaining_amount > 0)
                            <x-riyal-icon /> {{ number_format($order->remaining_amount, 2) }}
                        @else
                            {{ __('messages.fully_paid') }}
                        @endif
                    </span>
                </div>
                
                @if($order->remaining_amount > 0)
                    <button class="btn btn-success w-100" onclick="showPaymentModal({{ $order->id }}, '{{ $order->number }}', {{ $order->remaining_amount }})">
                        <i class="fas fa-credit-card me-1"></i>{{ __('messages.make_payment') }}
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Order Products -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-shopping-cart me-2"></i>{{ __('messages.order_products') }}
            <span class="badge bg-primary ms-2">{{ $order->orderProducts->count() }}</span>
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>{{ __('messages.product') }}</th>
                        <th>{{ __('messages.unit_price') }}</th>
                        <th>{{ __('messages.quantity') }}</th>
                        <th>{{ __('messages.subtotal') }}</th>
                        <th>{{ __('messages.tax') }}</th>
                        <th>{{ __('messages.total') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->orderProducts as $orderProduct)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($orderProduct->product && $orderProduct->product->photo)
                                        <img src="{{ asset('assets/admin/uploads/' . $orderProduct->product->photo) }}" 
                                             alt="{{ $orderProduct->product->name_en }}" 
                                             class="me-3 rounded" 
                                             style="width: 50px; height: 50px; object-fit: cover;">
                                    @else
                                        <div class="me-3 rounded bg-light d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <strong>{{ $orderProduct->product ? (app()->getLocale() == 'ar' ? $orderProduct->product->name_ar : $orderProduct->product->name_en) : __('messages.product_not_found') }}</strong>
                                        @if($orderProduct->product && $orderProduct->product->code)
                                            <br><small class="text-muted">{{ __('messages.code') }}: {{ $orderProduct->product->code }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td><x-riyal-icon /> {{ number_format($orderProduct->unit_price, 2) }}</td>
                            <td>
                                <span class="badge bg-info">{{ $orderProduct->quantity }}</span>
                            </td>
                            <td><x-riyal-icon /> {{ number_format($orderProduct->total_price_before_tax, 2) }}</td>
                            <td><x-riyal-icon /> {{ number_format($orderProduct->tax_percentage, 2) }}</td>
                            <td class="fw-bold"><x-riyal-icon /> {{ number_format($orderProduct->total_price_after_tax, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="table-active">
                        <th colspan="3">{{ __('messages.total') }}</th>
                        <th><x-riyal-icon /> {{ number_format($order->orderProducts->sum('total_price'), 2) }}</th>
                        <th><x-riyal-icon /> {{ number_format($order->orderProducts->sum(function($item) { return $item->total_price_after_tax - $item->total_price; }), 2) }}</th>
                        <th class="text-primary"><x-riyal-icon /> {{ number_format($order->orderProducts->sum('total_price_after_tax'), 2) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<!-- Debt Information -->
@if($order->userDepts && $order->userDepts->count() > 0)
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-exclamation-triangle me-2"></i>{{ __('messages.debt_information') }}
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>{{ __('messages.debt_amount') }}</th>
                            <th>{{ __('messages.remaining_amount') }}</th>
                            <th>{{ __('messages.status') }}</th>
                            <th>{{ __('messages.created_at') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->userDepts as $debt)
                            <tr>
                                <td><x-riyal-icon /> {{ number_format($debt->debt_amount, 2) }}</td>
                                <td class="text-danger"><x-riyal-icon /> {{ number_format($debt->remaining_amount, 2) }}</td>
                                <td>
                                    @if($debt->status == 1)
                                        <span class="badge bg-warning">{{ __('messages.active') }}</span>
                                    @else
                                        <span class="badge bg-success">{{ __('messages.paid') }}</span>
                                    @endif
                                </td>
                                <td>{{ $debt->created_at->format('Y-m-d H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endif

<!-- Action Buttons -->
<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <a href="{{ route('user.orders') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i>{{ __('messages.back_to_orders') }}
                </a>
            </div>
            <div>
                @if($order->remaining_amount > 0)
                    <button class="btn btn-success" onclick="showPaymentModal({{ $order->id }}, '{{ $order->number }}', {{ $order->remaining_amount }})">
                        <i class="fas fa-credit-card me-1"></i>{{ __('messages.make_payment') }}
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('messages.payment_request') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    {{ __('messages.payment_request_note') }}
                </div>
                <form id="paymentRequestForm">
                    <input type="hidden" id="order_id" name="order_id">
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.order_number') }}</label>
                        <input type="text" id="order_number" class="form-control" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.remaining_amount') }}</label>
                        <input type="text" id="remaining_amount" class="form-control" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.payment_method') }}</label>
                        <select name="payment_method" class="form-select" required>
                            <option value="">{{ __('messages.select_payment_method') }}</option>
                            <option value="cash">{{ __('messages.cash') }}</option>
                            <option value="bank_transfer">{{ __('messages.bank_transfer') }}</option>
                            <option value="credit_card">{{ __('messages.credit_card') }}</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.notes') }}</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="{{ __('messages.payment_notes_placeholder') }}"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                <button type="button" class="btn btn-success" onclick="submitPaymentRequest()">
                    <i class="fas fa-paper-plane me-1"></i>{{ __('messages.send_request') }}
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
@media print {
    .btn, .modal, .page-header .btn, .card .btn {
        display: none !important;
    }
    
    .card {
        border: 1px solid #dee2e6 !important;
        box-shadow: none !important;
    }
    
    .page-header {
        border-bottom: 2px solid #000;
        padding-bottom: 10px;
        margin-bottom: 20px;
    }
    
    .table {
        border-collapse: collapse !important;
    }
    
    .table th, .table td {
        border: 1px solid #dee2e6 !important;
    }
}

.product-image {
    transition: transform 0.3s ease;
}

.product-image:hover {
    transform: scale(1.1);
}

.badge {
    font-size: 0.875em;
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.table-responsive {
    border-radius: 0.375rem;
}
</style>
@endpush

@push('scripts')
<script>
function showPaymentModal(orderId, orderNumber, remainingAmount) {
    document.getElementById('order_id').value = orderId;
    document.getElementById('order_number').value = orderNumber;
    document.getElementById('remaining_amount').value = '$' + remainingAmount.toFixed(2);
    
    const modal = new bootstrap.Modal(document.getElementById('paymentModal'));
    modal.show();
}

function submitPaymentRequest() {
    // Here you would normally send an AJAX request to submit the payment request
    // For now, we'll just show a success message
    alert('{{ __("messages.payment_request_sent") }}');
    
    const modal = bootstrap.Modal.getInstance(document.getElementById('paymentModal'));
    modal.hide();
    
    // Reset form
    document.getElementById('paymentRequestForm').reset();
}

// Auto-hide alerts
setTimeout(function() {
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(alert => {
        const bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
    });
}, 5000);

// Print functionality
function printOrder() {
    window.print();
}
</script>
@endpush