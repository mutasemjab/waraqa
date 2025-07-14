@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('messages.edit_debt') }} #{{ $userDept->id }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('user_depts.show', $userDept) }}" class="btn btn-info btn-sm">
                            <i class="fas fa-eye"></i> {{ __('messages.view') }}
                        </a>
                        <a href="{{ route('user_depts.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> {{ __('messages.back') }}
                        </a>
                    </div>
                </div>

                <form action="{{ route('user_depts.update', $userDept) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="user_id">{{ __('messages.user') }} <span class="text-danger">*</span></label>
                                    <select name="user_id" id="user_id" class="form-control @error('user_id') is-invalid @enderror" required>
                                        <option value="">{{ __('messages.select_user') }}</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ (old('user_id', $userDept->user_id) == $user->id) ? 'selected' : '' }}>
                                                {{ $user->name }} - {{ $user->email }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('user_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="order_id">{{ __('messages.order') }} <span class="text-danger">*</span></label>
                                    <select name="order_id" id="order_id" class="form-control @error('order_id') is-invalid @enderror" required>
                                        <option value="">{{ __('messages.select_order') }}</option>
                                        @foreach($orders as $order)
                                            <option value="{{ $order->id }}" 
                                                    data-total="{{ $order->total_prices }}" 
                                                    data-paid="{{ $order->paid_amount }}"
                                                    data-remaining="{{ $order->remaining_amount }}"
                                                    {{ (old('order_id', $userDept->order_id) == $order->id) ? 'selected' : '' }}>
                                                {{ $order->number }} - {{ $order->user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('order_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="total_amount">{{ __('messages.total_amount') }} <span class="text-danger">*</span></label>
                                    <input type="number" name="total_amount" id="total_amount" class="form-control @error('total_amount') is-invalid @enderror" 
                                           step="0.01" min="0" value="{{ old('total_amount', $userDept->total_amount) }}" required>
                                    @error('total_amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="paid_amount">{{ __('messages.paid_amount') }}</label>
                                    <input type="number" name="paid_amount" id="paid_amount" class="form-control @error('paid_amount') is-invalid @enderror" 
                                           step="0.01" min="0" value="{{ old('paid_amount', $userDept->paid_amount) }}">
                                    @error('paid_amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="remaining_amount">{{ __('messages.remaining_amount') }}</label>
                                    <input type="text" id="remaining_amount" class="form-control" value="{{ $userDept->remaining_amount }}" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">{{ __('messages.status') }} <span class="text-danger">*</span></label>
                                    <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                                        <option value="1" {{ (old('status', $userDept->status) == 1) ? 'selected' : '' }}>{{ __('messages.active') }}</option>
                                        <option value="2" {{ (old('status', $userDept->status) == 2) ? 'selected' : '' }}>{{ __('messages.paid') }}</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('messages.created_at') }}</label>
                                    <input type="text" class="form-control" value="{{ $userDept->created_at->format('Y-m-d H:i') }}" readonly>
                                </div>
                            </div>
                        </div>

                        <!-- Current Information Display -->
                        <div class="row">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <h6><i class="fas fa-info-circle"></i> {{ __('messages.current_information') }}</h6>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <strong>{{ __('messages.current_user') }}:</strong><br>
                                            {{ $userDept->user->name }}
                                        </div>
                                        <div class="col-md-3">
                                            <strong>{{ __('messages.current_order') }}:</strong><br>
                                            {{ $userDept->order->number }}
                                        </div>
                                        <div class="col-md-3">
                                            <strong>{{ __('messages.current_total') }}:</strong><br>
                                            {{ number_format($userDept->total_amount, 2) }}
                                        </div>
                                        <div class="col-md-3">
                                            <strong>{{ __('messages.current_remaining') }}:</strong><br>
                                            <span class="{{ $userDept->remaining_amount > 0 ? 'text-danger' : 'text-success' }}">
                                                {{ number_format($userDept->remaining_amount, 2) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> {{ __('messages.update') }}
                        </button>
                        <a href="{{ route('user_depts.show', $userDept) }}" class="btn btn-info">
                            <i class="fas fa-eye"></i> {{ __('messages.view') }}
                        </a>
                        <a href="{{ route('user_depts.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> {{ __('messages.cancel') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Calculate remaining amount when total or paid amount changes
    function calculateRemaining() {
        var total = parseFloat($('#total_amount').val()) || 0;
        var paid = parseFloat($('#paid_amount').val()) || 0;
        var remaining = total - paid;
        $('#remaining_amount').val(remaining.toFixed(2));
        
        // Update status automatically based on remaining amount
        if (remaining <= 0) {
            $('#status').val('2'); // Paid
        } else {
            $('#status').val('1'); // Active
        }
    }

    $('#total_amount, #paid_amount').on('input', calculateRemaining);

    // Auto-fill amounts when order is selected
    $('#order_id').change(function() {
        var selectedOption = $(this).find('option:selected');
        if (selectedOption.val()) {
            var total = selectedOption.data('total');
            
            // Only update if current total is 0 or user confirms
            var currentTotal = parseFloat($('#total_amount').val()) || 0;
            if (currentTotal === 0 || confirm('{{ __('messages.update_amounts_from_order') }}?')) {
                $('#total_amount').val(total);
                calculateRemaining();
            }
        }
    });

    // Initial calculation
    calculateRemaining();
});
</script>
@endsection