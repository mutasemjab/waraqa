@extends('layouts.user')

@section('title', __('messages.sales_returns'))
@section('page-title', __('messages.sales_returns'))

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1 class="page-title">{{ __('messages.sales_returns') }}</h1>
        <p class="page-subtitle">{{ __('messages.all_sales_returns') }}</p>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('user.sales-returns.index') }}" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">{{ __('messages.return_status') }}</label>
                <select name="status" class="form-select">
                    <option value="">{{ __('messages.all') }}</option>
                    <option value="pending"   {{ request('status') == 'pending'   ? 'selected' : '' }}>{{ __('messages.pending') }}</option>
                    <option value="approved"  {{ request('status') == 'approved'  ? 'selected' : '' }}>{{ __('messages.approved') }}</option>
                    <option value="received"  {{ request('status') == 'received'  ? 'selected' : '' }}>{{ __('messages.received') }}</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">{{ __('messages.date_from') }}</label>
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">{{ __('messages.date_to') }}</label>
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-filter me-1"></i>{{ __('messages.filter') }}
                </button>
                <a href="{{ route('user.sales-returns.index') }}" class="btn btn-outline-secondary w-100">
                    <i class="fas fa-times me-1"></i>{{ __('messages.reset') }}
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Returns Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center bg-danger text-white">
        <h5 class="mb-0">
            <i class="fas fa-undo-alt me-2"></i>{{ __('messages.sales_returns') }}
            <span class="badge bg-white text-danger ms-2">{{ $salesReturns->total() }}</span>
        </h5>
    </div>
    <div class="card-body p-0">
        @if($salesReturns->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>{{ __('messages.return_number') }}</th>
                        <th>{{ __('messages.order_number') }}</th>
                        <th>{{ __('messages.products') }}</th>
                        <th>{{ __('messages.total') }}</th>
                        <th>{{ __('messages.return_date') }}</th>
                        <th>{{ __('messages.return_reason') }}</th>
                        <th>{{ __('messages.return_status') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($salesReturns as $return)
                    <tr>
                        <td><strong>{{ $return->number }}</strong></td>
                        <td>
                            <span class="text-muted">{{ $return->order->number ?? '-' }}</span>
                        </td>
                        <td>
                            @foreach($return->returnItems as $item)
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="badge bg-light text-dark border">
                                        {{ app()->getLocale() == 'ar' ? $item->product?->name_ar : $item->product?->name_en }}
                                    </span>
                                    <small class="text-muted">× {{ $item->quantity_returned }}</small>
                                </div>
                            @endforeach
                        </td>
                        <td>
                            <strong><x-riyal-icon /> {{ number_format($return->total_amount, 2) }}</strong>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($return->return_date)->format('Y-m-d') }}</td>
                        <td>
                            <small class="text-muted">{{ $return->reason ?? '-' }}</small>
                        </td>
                        <td>
                            @php
                                $colors = ['pending' => 'warning', 'approved' => 'info', 'received' => 'success'];
                                $labels = [
                                    'pending'  => __('messages.pending'),
                                    'approved' => __('messages.approved'),
                                    'received' => __('messages.received'),
                                ];
                            @endphp
                            <span class="badge bg-{{ $colors[$return->status] ?? 'secondary' }}">
                                {{ $labels[$return->status] ?? $return->status }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center p-3">
            {{ $salesReturns->withQueryString()->links() }}
        </div>

        @else
        <div class="text-center py-5">
            <i class="fas fa-undo-alt text-muted" style="font-size: 3rem; opacity: 0.3;"></i>
            <h6 class="mt-3 text-muted">{{ __('messages.no_returns_found') }}</h6>
        </div>
        @endif
    </div>
</div>
@endsection
