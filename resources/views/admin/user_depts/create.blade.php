@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('messages.add_new_debt') }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('user_depts.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> {{ __('messages.back') }}
                        </a>
                    </div>
                </div>

                <form action="{{ route('user_depts.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="user_id">{{ __('messages.user') }} <span class="text-danger">*</span></label>
                                    <select name="user_id" id="user_id" class="form-control @error('user_id') is-invalid @enderror" required>
                                        <option value="">{{ __('messages.select_user') }}</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
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
                                                    {{ old('order_id') == $order->id ? 'selected' : '' }}>
                                                {{ $order->number }} - {{ $order->user->name }} ({{ __('messages.remaining') }}: {{ number_format($order->remaining_amount, 2) }})
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
                                           step="0.01" min="0" value="{{ old('total_amount') }}" required>
                                    @error('total_amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="paid_amount">{{ __('messages.paid_amount') }}</label>
                                    <input type="number" name="paid_amount" id="paid_amount" class="form-control @error('paid_amount') is-invalid @enderror" 
                                           step="0.01" min="0" value="{{ old('paid_amount', 0) }}">
                                    @error('paid_amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="remaining_amount">{{ __('messages.remaining_amount') }}</label>
                                    <input type="text" id="remaining_amount" class="form-control" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">{{ __('messages.status') }} <span class="text-danger">*</span></label>
                                    <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                                        <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>{{ __('messages.active') }}</option>
                                        <option value="2" {{ old('status') == '2' ? 'selected' : '' }}>{{ __('messages.paid') }}</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> {{ __('messages.save') }}
                        </button>
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
    }

    $('#total_amount, #paid_amount').on('input', calculateRemaining);

    // Auto-fill amounts when order is selected
    $('#order_id').change(function() {
        var selectedOption = $(this).find('option:selected');
        if (selectedOption.val()) {
            var total = selectedOption.data('total');
            var remaining = selectedOption.data('remaining');
            
            $('#total_amount').val(total);
            $('#paid_amount').val(0);
            calculateRemaining();
        }
    });

    // Initial calculation
    calculateRemaining();
});
</script>
@endsection