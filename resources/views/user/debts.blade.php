@extends('layouts.user')

@section('title', __('messages.my_debts'))
@section('page-title', __('messages.my_debts'))

@section('content')
<div class="page-header">
    <h1 class="page-title">{{ __('messages.my_debts') }}</h1>
    <p class="page-subtitle">{{ __('messages.manage_your_outstanding_debts') }}</p>
</div>

<!-- Debt Summary Cards -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-danger text-white">
            <div class="card-body text-center">
                <h3><x-riyal-icon /> {{ number_format($totalDebt, 2) }}</h3>
                <p class="mb-0">{{ __('messages.total_outstanding_debt') }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-warning text-white">
            <div class="card-body text-center">
                <h3>{{ $debts->where('status', 1)->count() }}</h3>
                <p class="mb-0">{{ __('messages.active_debts') }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <h3>{{ $debts->where('status', 2)->count() }}</h3>
                <p class="mb-0">{{ __('messages.paid_debts') }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('user.debts') }}" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">{{ __('messages.status') }}</label>
                <select name="status" class="form-select">
                    <option value="">{{ __('messages.all_statuses') }}</option>
                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>{{ __('messages.active') }}</option>
                    <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>{{ __('messages.paid') }}</option>
                </select>
            </div>
            
            <div class="col-md-8">
                <label class="form-label">&nbsp;</label>
                <div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-1"></i>{{ __('messages.filter') }}
                    </button>
                    <a href="{{ route('user.debts') }}" class="btn btn-secondary">
                        <i class="fas fa-refresh me-1"></i>{{ __('messages.clear') }}
                    </a>
                    @if($totalDebt > 0)
                        <button type="button" class="btn btn-success" onclick="requestFullPayment()">
                            <i class="fas fa-credit-card me-1"></i>{{ __('messages.pay_all_debts') }}
                        </button>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Debts List -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-credit-card me-2"></i>{{ __('messages.debts_list') }}
            <span class="badge bg-primary ms-2">{{ $debts->total() }}</span>
        </h5>
    </div>
    <div class="card-body">
        @if($debts->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>{{ __('messages.debt_id') }}</th>
                            <th>{{ __('messages.related_order') }}</th>
                            <th>{{ __('messages.total_amount') }}</th>
                            <th>{{ __('messages.paid_amount') }}</th>
                            <th>{{ __('messages.remaining_amount') }}</th>
                            <th>{{ __('messages.status') }}</th>
                            <th>{{ __('messages.created_date') }}</th>
                            <th>{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($debts as $debt)
                            <tr class="{{ $debt->status == 1 ? 'table-warning' : 'table-success' }}">
                                <td>
                                    <strong>#{{ $debt->id }}</strong>
                                </td>
                                <td>
                                    <a href="{{ route('user.orders.show', $debt->order->id) }}" class="text-decoration-none">
                                        <strong>{{ $debt->order->number }}</strong>
                                    </a>
                                    <br>
                                    <small class="text-muted">
                                        {{ Carbon\Carbon::parse($debt->order->date)->format('Y-m-d') }}
                                    </small>
                                </td>
                                <td class="fw-bold"><x-riyal-icon /> {{ number_format($debt->total_amount, 2) }}</td>
                                <td class="text-success"><x-riyal-icon /> {{ number_format($debt->paid_amount, 2) }}</td>
                                <td>
                                    @if($debt->remaining_amount > 0)
                                        <span class="text-danger fw-bold"><x-riyal-icon /> {{ number_format($debt->remaining_amount, 2) }}</span>
                                    @else
                                        <span class="text-success fw-bold">{{ __('messages.fully_paid') }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($debt->status == 1)
                                        <span class="badge bg-warning">{{ __('messages.active') }}</span>
                                    @else
                                        <span class="badge bg-success">{{ __('messages.paid') }}</span>
                                    @endif
                                </td>
                                <td>{{ $debt->created_at->format('Y-m-d') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('user.orders.show', $debt->order->id) }}" 
                                           class="btn btn-sm btn-outline-primary" 
                                           title="{{ __('messages.view_order') }}">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        @if($debt->status == 1 && $debt->remaining_amount > 0)
                                            <button class="btn btn-sm btn-outline-success" 
                                                    title="{{ __('messages.request_payment') }}"
                                                    onclick="showPaymentModal({{ $debt->id }}, '{{ $debt->order->number }}', {{ $debt->remaining_amount }})">
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
                {{ $debts->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                <h4 class="mt-3 text-success">{{ __('messages.no_debts_found') }}</h4>
                <p class="text-muted">{{ __('messages.all_payments_completed') }}</p>
                <a href="{{ route('user.orders') }}" class="btn btn-primary">
                    <i class="fas fa-shopping-cart me-1"></i>{{ __('messages.view_orders') }}
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Payment Progress -->
@if($debts->count() > 0)
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-chart-pie me-2"></i>{{ __('messages.payment_progress') }}
            </h5>
        </div>
        <div class="card-body">
            @php
                $totalDebtAmount = $debts->sum('total_amount');
                $totalPaidAmount = $debts->sum('paid_amount');
                $progressPercentage = $totalDebtAmount > 0 ? ($totalPaidAmount / $totalDebtAmount) * 100 : 0;
            @endphp
            
            <div class="row">
                <div class="col-md-8">
                    <h6>{{ __('messages.overall_payment_progress') }}</h6>
                    <div class="progress" style="height: 25px;">
                        <div class="progress-bar bg-success" 
                             style="width: {{ $progressPercentage }}%"
                             aria-valuenow="{{ $progressPercentage }}" 
                             aria-valuemin="0" 
                             aria-valuemax="100">
                            {{ number_format($progressPercentage, 1) }}%
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mt-2">
                        <small class="text-muted">{{ __('messages.paid') }}: <x-riyal-icon /> {{ number_format($totalPaidAmount, 2) }}</small>
                        <small class="text-muted">{{ __('messages.total') }}: <x-riyal-icon /> {{ number_format($totalDebtAmount, 2) }}</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center">
                        <h4 class="text-danger"><x-riyal-icon /> {{ number_format($totalDebt, 2) }}</h4>
                        <p class="text-muted">{{ __('messages.remaining_to_pay') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Tips -->
    <div class="card mt-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">
                <i class="fas fa-lightbulb me-2"></i>{{ __('messages.payment_tips') }}
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="text-center mb-3">
                        <i class="fas fa-calendar-check text-info" style="font-size: 2rem;"></i>
                        <h6 class="mt-2">{{ __('messages.pay_on_time') }}</h6>
                        <p class="text-muted small">{{ __('messages.pay_on_time_description') }}</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center mb-3">
                        <i class="fas fa-piggy-bank text-success" style="font-size: 2rem;"></i>
                        <h6 class="mt-2">{{ __('messages.partial_payments') }}</h6>
                        <p class="text-muted small">{{ __('messages.partial_payments_description') }}</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center mb-3">
                        <i class="fas fa-handshake text-primary" style="font-size: 2rem;"></i>
                        <h6 class="mt-2">{{ __('messages.contact_support') }}</h6>
                        <p class="text-muted small">{{ __('messages.contact_support_description') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Payment Request Modal -->
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
                    {{ __('messages.payment_request_info') }}
                </div>
                
                <form id="paymentRequestForm">
                    <input type="hidden" id="debt_id" name="debt_id">
                    
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.order_number') }}</label>
                        <input type="text" id="order_number" class="form-control" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.remaining_amount') }}</label>
                        <input type="text" id="remaining_amount" class="form-control" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.payment_amount') }} <span class="text-danger">*</span></label>
                        <input type="number" 
                               name="payment_amount" 
                               id="payment_amount"
                               class="form-control" 
                               step="0.01" 
                               min="0.01"
                               required
                               placeholder="{{ __('messages.enter_amount_to_pay') }}">
                        <div class="form-text">{{ __('messages.enter_full_amount_or_partial') }}</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.payment_method') }}</label>
                        <select name="payment_method" class="form-select" required>
                            <option value="">{{ __('messages.select_payment_method') }}</option>
                            <option value="cash">{{ __('messages.cash') }}</option>
                            <option value="bank_transfer">{{ __('messages.bank_transfer') }}</option>
                            <option value="credit_card">{{ __('messages.credit_card') }}</option>
                            <option value="digital_wallet">{{ __('messages.digital_wallet') }}</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.notes') }}</label>
                        <textarea name="notes" 
                                  class="form-control" 
                                  rows="3" 
                                  placeholder="{{ __('messages.payment_notes_placeholder') }}"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    {{ __('messages.cancel') }}
                </button>
                <button type="button" class="btn btn-success" onclick="submitPaymentRequest()">
                    <i class="fas fa-paper-plane me-1"></i>{{ __('messages.send_request') }}
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Pay All Debts Modal -->
<div class="modal fade" id="payAllModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('messages.pay_all_debts') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    {{ __('messages.pay_all_debts_confirmation') }}
                </div>
                
                <div class="text-center">
                    <h3 class="text-danger"><x-riyal-icon /> {{ number_format($totalDebt, 2) }}</h3>
                    <p class="text-muted">{{ __('messages.total_amount_to_pay') }}</p>
                </div>
                
                <form id="payAllForm">
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.payment_method') }}</label>
                        <select name="payment_method" class="form-select" required>
                            <option value="">{{ __('messages.select_payment_method') }}</option>
                            <option value="cash">{{ __('messages.cash') }}</option>
                            <option value="bank_transfer">{{ __('messages.bank_transfer') }}</option>
                            <option value="credit_card">{{ __('messages.credit_card') }}</option>
                            <option value="digital_wallet">{{ __('messages.digital_wallet') }}</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.notes') }}</label>
                        <textarea name="notes" 
                                  class="form-control" 
                                  rows="3" 
                                  placeholder="{{ __('messages.full_payment_notes_placeholder') }}"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    {{ __('messages.cancel') }}
                </button>
                <button type="button" class="btn btn-success" onclick="submitPayAllRequest()">
                    <i class="fas fa-money-bill-wave me-1"></i>{{ __('messages.request_full_payment') }}
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentMaxAmount = 0;

function showPaymentModal(debtId, orderNumber, remainingAmount) {
    currentMaxAmount = remainingAmount;
    
    document.getElementById('debt_id').value = debtId;
    document.getElementById('order_number').value = orderNumber;
    document.getElementById('remaining_amount').value = '$' + remainingAmount.toFixed(2);
    document.getElementById('payment_amount').max = remainingAmount;
    document.getElementById('payment_amount').value = '';
    
    const modal = new bootstrap.Modal(document.getElementById('paymentModal'));
    modal.show();
}

function requestFullPayment() {
    const modal = new bootstrap.Modal(document.getElementById('payAllModal'));
    modal.show();
}

function submitPaymentRequest() {
    const form = document.getElementById('paymentRequestForm');
    const formData = new FormData(form);
    const paymentAmount = parseFloat(formData.get('payment_amount'));
    
    // Validate payment amount
    if (paymentAmount <= 0 || paymentAmount > currentMaxAmount) {
        alert('{{ __("messages.invalid_payment_amount") }}');
        return;
    }
    
    if (!formData.get('payment_method')) {
        alert('{{ __("messages.please_select_payment_method") }}');
        return;
    }
    
    // Here you would normally send an AJAX request to submit the payment request
    // For now, we'll just show a success message
    alert('{{ __("messages.payment_request_sent_successfully") }}');
    
    const modal = bootstrap.Modal.getInstance(document.getElementById('paymentModal'));
    modal.hide();
    
    // Reset form
    form.reset();
}

function submitPayAllRequest() {
    const form = document.getElementById('payAllForm');
    const formData = new FormData(form);
    
    if (!formData.get('payment_method')) {
        alert('{{ __("messages.please_select_payment_method") }}');
        return;
    }
    
    if (confirm('{{ __("messages.confirm_pay_all_debts") }}')) {
        // Here you would normally send an AJAX request
        alert('{{ __("messages.full_payment_request_sent") }}');
        
        const modal = bootstrap.Modal.getInstance(document.getElementById('payAllModal'));
        modal.hide();
        
        // Reset form
        form.reset();
    }
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