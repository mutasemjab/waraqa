@extends('layouts.user')

@section('title', __('messages.my_orders'))
@section('page-title', __('messages.my_orders'))

@section('content')
<div class="page-header">
    <h1 class="page-title">{{ __('messages.my_orders') }}</h1>
    <p class="page-subtitle">{{ __('messages.view_and_manage_your_orders') }}</p>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('user.orders') }}" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">{{ __('messages.order_status') }}</label>
                <select name="status" class="form-select">
                    <option value="">{{ __('messages.all_statuses') }}</option>
                    @foreach(\App\Enums\OrderStatus::cases() as $status)
                        <option value="{{ $status->value }}" {{ request('status') == $status->value ? 'selected' : '' }}>{{ $status->getLabelLocalized() }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">{{ __('messages.payment_status') }}</label>
                <select name="payment_status" class="form-select">
                    <option value="">{{ __('messages.all_payments') }}</option>
                    @foreach(\App\Enums\PaymentStatus::cases() as $paymentStatus)
                        <option value="{{ $paymentStatus->value }}" {{ request('payment_status') == $paymentStatus->value ? 'selected' : '' }}>{{ $paymentStatus->getLabelLocalized() }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-2">
                <label class="form-label">{{ __('messages.date_from') }}</label>
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>
            
            <div class="col-md-2">
                <label class="form-label">{{ __('messages.date_to') }}</label>
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
            
            <div class="col-md-2">
                <label class="form-label">{{ __('messages.search') }}</label>
                <input type="text" name="search" class="form-control" placeholder="{{ __('messages.order_number') }}" value="{{ request('search') }}">
            </div>
            
            <div class="col-12">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search me-1"></i>{{ __('messages.filter') }}
                </button>
                <a href="{{ route('user.orders') }}" class="btn btn-secondary">
                    <i class="fas fa-refresh me-1"></i>{{ __('messages.clear') }}
                </a>
                <a href="{{ route('user.analytics') }}" class="btn btn-info">
                    <i class="fas fa-chart-line me-1"></i>{{ __('messages.view_analytics') }}
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Orders List -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-shopping-cart me-2"></i>{{ __('messages.orders_list') }}
            <span class="badge bg-primary ms-2">{{ $orders->total() }}</span>
        </h5>
    </div>
    <div class="card-body">
        @if($orders->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>{{ __('messages.order_number') }}</th>
                            <th>{{ __('messages.date') }}</th>
                            <th>{{ __('messages.items') }}</th>
                            <th>{{ __('messages.total_amount') }}</th>
                            <th>{{ __('messages.paid_amount') }}</th>
                            <th>{{ __('messages.remaining') }}</th>
                            <th>{{ __('messages.order_status') }}</th>
                            <th>{{ __('messages.payment_status') }}</th>
                            <th>{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                            <tr>
                                <td>
                                    <strong>{{ $order->number }}</strong>
                                    @if($order->note)
                                        <br><small class="text-muted">{{ Str::limit($order->note, 30) }}</small>
                                    @endif
                                </td>
                                <td>{{ Carbon\Carbon::parse($order->date)->format('Y-m-d H:i') }}</td>
                                <td>
                                    <span class="badge bg-info">{{ $order->orderProducts->count() }} {{ __('messages.items') }}</span>
                                </td>
                                <td class="fw-bold"><x-riyal-icon /> {{ number_format($order->total_prices, 2) }}</td>
                                <td class="text-success"><x-riyal-icon /> {{ number_format($order->paid_amount, 2) }}</td>
                                <td>
                                    @if($order->remaining_amount > 0)
                                        <span class="text-danger fw-bold"><x-riyal-icon /> {{ number_format($order->remaining_amount, 2) }}</span>
                                    @else
                                        <span class="text-success">{{ __('messages.fully_paid') }}</span>
                                    @endif
                                </td>
                                <td>
                                    {!! \App\Enums\OrderStatus::tryFrom($order->status)?->getBadgeHtml() ?? '<span class="badge bg-secondary">N/A</span>' !!}
                                </td>
                                <td>
                                    {!! \App\Enums\PaymentStatus::tryFrom($order->payment_status)?->getBadgeHtml() ?? '<span class="badge bg-secondary">N/A</span>' !!}
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('user.orders.show', $order->id) }}" class="btn btn-sm btn-outline-primary" title="{{ __('messages.view_details') }}">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($order->remaining_amount > 0)
                                            <button class="btn btn-sm btn-outline-success" title="{{ __('messages.make_payment') }}" onclick="showPaymentModal({{ $order->id }}, '{{ $order->number }}', {{ $order->remaining_amount }})">
                                                <i class="fas fa-credit-card"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $orders->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-shopping-cart text-muted" style="font-size: 4rem;"></i>
                <h4 class="mt-3 text-muted">{{ __('messages.no_orders_found') }}</h4>
                <p class="text-muted">{{ __('messages.no_orders_match_criteria') }}</p>
                <a href="{{ route('user.orders') }}" class="btn btn-primary">
                    <i class="fas fa-refresh me-1"></i>{{ __('messages.clear_filters') }}
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Order Summary Cards -->
@if($orders->count() > 0)
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h3><x-riyal-icon /> {{ number_format($orders->sum('total_prices'), 2) }}</h3>
                    <p class="mb-0">{{ __('messages.total_value') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h3><x-riyal-icon /> {{ number_format($orders->sum('paid_amount'), 2) }}</h3>
                    <p class="mb-0">{{ __('messages.total_paid') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h3><x-riyal-icon /> {{ number_format($orders->sum('remaining_amount'), 2) }}</h3>
                    <p class="mb-0">{{ __('messages.total_remaining') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h3>{{ $orders->sum(function($order) { return $order->orderProducts->sum('quantity'); }) }}</h3>
                    <p class="mb-0">{{ __('messages.total_items') }}</p>
                </div>
            </div>
        </div>
    </div>
@endif

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

@push('scripts')
<script>
function showPaymentModal(orderId, orderNumber, remainingAmount) {
    document.getElementById('order_id').value = orderId;
    document.getElementById('order_number').value = orderNumber;
    document.getElementById('remaining_amount').value = remainingAmount.toFixed(2);

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
</script>
@endpush