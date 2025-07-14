@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('messages.user_debts_management') }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('user_depts.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> {{ __('messages.add_new_debt') }}
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Filters -->
                    <form method="GET" action="{{ route('user_depts.index') }}" class="mb-3">
                        <div class="row">
                            <div class="col-md-3">
                                <select name="user_id" class="form-control">
                                    <option value="">{{ __('messages.all_users') }}</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="status" class="form-control">
                                    <option value="">{{ __('messages.all_statuses') }}</option>
                                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>{{ __('messages.active') }}</option>
                                    <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>{{ __('messages.paid') }}</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control" placeholder="{{ __('messages.search_by_user_name_or_email') }}" value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-info">{{ __('messages.filter') }}</button>
                                <a href="{{ route('user_depts.index') }}" class="btn btn-secondary">{{ __('messages.clear') }}</a>
                            </div>
                        </div>
                    </form>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>{{ __('messages.id') }}</th>
                                    <th>{{ __('messages.user') }}</th>
                                    <th>{{ __('messages.order_number') }}</th>
                                    <th>{{ __('messages.total_amount') }}</th>
                                    <th>{{ __('messages.paid_amount') }}</th>
                                    <th>{{ __('messages.remaining_amount') }}</th>
                                    <th>{{ __('messages.status') }}</th>
                                    <th>{{ __('messages.created_at') }}</th>
                                    <th>{{ __('messages.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($userDepts as $debt)
                                    <tr>
                                        <td>{{ $debt->id }}</td>
                                        <td>
                                            <strong>{{ $debt->user->name }}</strong><br>
                                            <small class="text-muted">{{ $debt->user->email }}</small>
                                        </td>
                                        <td>
                                            <a href="{{ route('orders.show', $debt->order) }}">
                                                {{ $debt->order->number }}
                                            </a>
                                        </td>
                                        <td class="text-right">{{ number_format($debt->total_amount, 2) }}</td>
                                        <td class="text-right">{{ number_format($debt->paid_amount, 2) }}</td>
                                        <td class="text-right">
                                            <strong class="{{ $debt->remaining_amount > 0 ? 'text-danger' : 'text-success' }}">
                                                {{ number_format($debt->remaining_amount, 2) }}
                                            </strong>
                                        </td>
                                        <td>
                                            <span class="badge {{ $debt->status_badge }}">
                                                {{ $debt->status_label }}
                                            </span>
                                        </td>
                                        <td>{{ $debt->created_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('user_depts.show', $debt) }}" class="btn btn-info btn-sm">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('user_depts.edit', $debt) }}" class="btn btn-warning btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @if($debt->status == 1)
                                                    <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#paymentModal{{ $debt->id }}">
                                                        <i class="fas fa-money-bill"></i>
                                                    </button>
                                                @endif
                                                <form action="{{ route('user_depts.destroy', $debt) }}" method="POST" style="display:inline;" onsubmit="return confirm('{{ __('messages.are_you_sure') }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Payment Modal -->
                                    @if($debt->status == 1)
                                        <div class="modal fade" id="paymentModal{{ $debt->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form action="{{ route('user_depts.make_payment', $debt) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">{{ __('messages.make_payment') }}</h5>
                                                            <button type="button" class="close" data-dismiss="modal">
                                                                <span>&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <label>{{ __('messages.remaining_amount') }}</label>
                                                                <input type="text" class="form-control" value="{{ number_format($debt->remaining_amount, 2) }}" readonly>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>{{ __('messages.payment_amount') }} <span class="text-danger">*</span></label>
                                                                <input type="number" name="payment_amount" class="form-control" step="0.01" max="{{ $debt->remaining_amount }}" required>
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
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">{{ __('messages.no_debts_found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $userDepts->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Auto-hide success/error messages
    setTimeout(function() {
        $('.alert').fadeOut();
    }, 5000);
</script>
@endsection