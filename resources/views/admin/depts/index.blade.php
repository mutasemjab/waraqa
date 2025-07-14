@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>{{ __('messages.user_debts_management') }}</h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h5 class="card-title">{{ __('messages.total_active_debts') }}</h5>
                                    <h3 class="text-warning">{{ $debts->count() }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h5 class="card-title">{{ __('messages.total_debt_amount') }}</h5>
                                    <h3 class="text-danger">${{ number_format($debts->sum('remaining_amount'), 2) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h5 class="card-title">{{ __('messages.total_paid_amount') }}</h5>
                                    <h3 class="text-success">${{ number_format($debts->sum('paid_amount'), 2) }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>{{ __('messages.user') }}</th>
                                    <th>{{ __('messages.order_number') }}</th>
                                    <th>{{ __('messages.order_date') }}</th>
                                    <th>{{ __('messages.total_amount') }}</th>
                                    <th>{{ __('messages.paid_amount') }}</th>
                                    <th>{{ __('messages.remaining_amount') }}</th>
                                    <th>{{ __('messages.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($debts as $debt)
                                <tr>
                                    <td>
                                        <strong>{{ $debt->user->name }}</strong><br>
                                        <small class="text-muted">{{ $debt->user->phone }}</small>
                                    </td>
                                    <td>{{ $debt->order->number }}</td>
                                    <td>{{ $debt->order->date->format('M d, Y') }}</td>
                                    <td>${{ number_format($debt->total_amount, 2) }}</td>
                                    <td>${{ number_format($debt->paid_amount, 2) }}</td>
                                    <td>
                                        <span class="badge bg-warning">${{ number_format($debt->remaining_amount, 2) }}</span>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-success" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#paymentModal{{ $debt->id }}">
                                            {{ __('messages.receive_payment') }}
                                        </button>
                                    </td>
                                </tr>

                                <!-- Payment Modal -->
                                <div class="modal fade" id="paymentModal{{ $debt->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">{{ __('messages.receive_payment') }} - {{ $debt->user->name }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="{{ route('admin.receive-payment', $debt) }}" method="POST">
                                                @csrf
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <strong>{{ __('messages.current_debt') }}:</strong><br>
                                                            ${{ number_format($debt->remaining_amount, 2) }}
                                                        </div>
                                                        <div class="col-md-6">
                                                            <strong>{{ __('messages.order_number') }}:</strong><br>
                                                            {{ $debt->order->number }}
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="form-group">
                                                        <label for="payment_amount{{ $debt->id }}">{{ __('messages.payment_amount') }}</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text">$</span>
                                                            <input type="number" 
                                                                   name="payment_amount" 
                                                                   id="payment_amount{{ $debt->id }}"
                                                                   class="form-control" 
                                                                   min="0" 
                                                                   max="{{ $debt->remaining_amount }}"
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
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">{{ __('messages.no_active_debts') }}</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
              