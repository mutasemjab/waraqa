@extends('layouts.provider')

@section('title', __('messages.purchase_details'))
@section('page-title', __('messages.purchase_details'))

@section('content')
<div class="page-header mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">{{ __('messages.purchase_details') }}</h1>
            <p class="page-subtitle">{{ __('messages.purchase_number') }}: {{ $purchase->purchase_number }}</p>
        </div>
        <a href="{{ route('provider.orders') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>{{ __('messages.back') }}
        </a>
    </div>
</div>

<div class="row">
    <!-- Purchase Information -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">{{ __('messages.purchase_information') }}</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-sm-6">
                        <label class="form-label text-muted small">{{ __('messages.purchase_number') }}</label>
                        <p class="fw-bold">{{ $purchase->purchase_number }}</p>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label text-muted small">{{ __('messages.status') }}</label>
                        <p>
                            @php
                                $statusColors = [
                                    'pending' => 'warning',
                                    'confirmed' => 'info',
                                    'received' => 'success',
                                    'paid' => 'primary',
                                    'rejected' => 'danger'
                                ];
                                $statusColor = $statusColors[$purchase->status] ?? 'secondary';
                            @endphp
                            <span class="badge bg-{{ $statusColor }}">{{ ucfirst($purchase->status) }}</span>
                        </p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-6">
                        <label class="form-label text-muted small">{{ __('messages.date') }}</label>
                        <p class="fw-bold">{{ $purchase->created_at->format('Y-m-d') }}</p>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label text-muted small">{{ __('messages.expected_delivery') }}</label>
                        <p class="fw-bold">
                            {{ $purchase->bookRequestResponse->expected_delivery_date ?? '-' }}
                        </p>
                    </div>
                </div>

                @if($purchase->notes)
                <div class="row">
                    <div class="col-12">
                        <label class="form-label text-muted small">{{ __('messages.notes') }}</label>
                        <p class="fw-bold">{{ $purchase->notes }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Financial Summary -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">{{ __('messages.financial_summary') }}</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3 border-bottom pb-3">
                    <div class="col-6">
                        <label class="form-label text-muted small">{{ __('messages.subtotal') }}</label>
                    </div>
                    <div class="col-6 text-end">
                        <p class="fw-bold">
                            <x-riyal-icon />
                            {{ number_format($purchase->total_amount - $purchase->total_tax, 2) }}
                        </p>
                    </div>
                </div>

                <div class="row mb-3 border-bottom pb-3">
                    <div class="col-6">
                        <label class="form-label text-muted small">{{ __('messages.tax') }}</label>
                    </div>
                    <div class="col-6 text-end">
                        <p class="fw-bold">
                            <x-riyal-icon />
                            {{ number_format($purchase->total_tax, 2) }}
                        </p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-6">
                        <label class="form-label text-muted">{{ __('messages.total_amount') }}</label>
                    </div>
                    <div class="col-6 text-end">
                        <p class="fw-bold fs-5">
                            <x-riyal-icon />
                            {{ number_format($purchase->total_amount, 2) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Items Table -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">{{ __('messages.sold_items') ?? __('messages.purchase_items') }}</h5>
    </div>
    <div class="card-body">
        @if($purchase->items->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>{{ __('messages.product') }}</th>
                            <th class="text-center">{{ __('messages.quantity') }}</th>
                            <th class="text-end">{{ __('messages.unit_price') }}</th>
                            <th class="text-end">{{ __('messages.subtotal') }}</th>
                            <th class="text-end">{{ __('messages.tax') }}</th>
                            <th class="text-end">{{ __('messages.total') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($purchase->items as $item)
                        <tr>
                            <td>
                                <p class="fw-bold mb-0">
                                    {{ app()->getLocale() == 'ar' ? $item->product->name_ar : $item->product->name_en }}
                                </p>
                            </td>
                            <td class="text-center">{{ $item->quantity }}</td>
                            <td class="text-end">
                                <x-riyal-icon />
                                {{ number_format($item->unit_price, 2) }}
                            </td>
                            <td class="text-end">
                                <x-riyal-icon />
                                {{ number_format($item->unit_price * $item->quantity, 2) }}
                            </td>
                            <td class="text-end">
                                <x-riyal-icon />
                                {{ number_format(($item->unit_price * $item->quantity * $item->tax_percentage) / 100, 2) }}
                            </td>
                            <td class="text-end">
                                <p class="fw-bold">
                                    <x-riyal-icon />
                                    {{ number_format($item->total_price, 2) }}
                                </p>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="3" class="text-end fw-bold">{{ __('messages.grand_total') }}:</td>
                            <td class="text-end fw-bold">
                                <x-riyal-icon />
                                {{ number_format($purchase->items->sum(function($item) { return $item->unit_price * $item->quantity; }), 2) }}
                            </td>
                            <td class="text-end fw-bold">
                                <x-riyal-icon />
                                {{ number_format($purchase->total_tax, 2) }}
                            </td>
                            <td class="text-end">
                                <p class="fw-bold fs-5">
                                    <x-riyal-icon />
                                    {{ number_format($purchase->items->sum('total_price'), 2) }}
                                </p>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @else
            <div class="alert alert-info" role="alert">
                <i class="fas fa-info-circle me-2"></i>
                {{ __('messages.no_items_found') }}
            </div>
        @endif
    </div>
</div>

<!-- Book Request Items Section -->
@if($purchase->bookRequest && $purchase->bookRequest->items->count() > 0)
<div id="bookRequestSection"></div>
<div class="card mt-4">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0">
            <i class="fas fa-list me-2"></i>{{ __('messages.book_request_items') }}
        </h5>
    </div>
    <div class="card-body">
        <p class="text-muted mb-3">{{ __('messages.respond_to_book_request_items') }}</p>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>{{ __('messages.product') }}</th>
                        <th class="text-center">{{ __('messages.requested_quantity') }}</th>
                        <th class="text-center">{{ __('messages.status') }}</th>
                        <th class="text-center">{{ __('messages.action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchase->bookRequest->items as $item)
                    <tr>
                        <td>
                            <p class="fw-bold mb-0">
                                {{ app()->getLocale() == 'ar' ? $item->product->name_ar : $item->product->name_en }}
                            </p>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-primary">{{ $item->requested_quantity }} {{ __('messages.units') }}</span>
                        </td>
                        <td class="text-center">
                            @php
                                $myResponse = $item->responses->where('provider_id', auth()->user()->provider->id)->first();
                            @endphp
                            @if($myResponse)
                                @php
                                    $statusColors = [
                                        'pending' => 'warning',
                                        'approved' => 'success',
                                        'rejected' => 'danger',
                                    ];
                                    $statusColor = $statusColors[$myResponse->status] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $statusColor }}">
                                    {{ __('messages.' . ucfirst($myResponse->status)) }}
                                </span>
                            @else
                                <span class="badge bg-secondary">{{ __('messages.pending') }}</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if(!$myResponse)
                                <a href="{{ route('provider.bookRequests.respond', $item->id) }}"
                                   class="btn btn-sm btn-success">
                                    <i class="fas fa-reply me-1"></i>{{ __('messages.respond') }}
                                </a>
                            @else
                                <span class="text-muted">{{ __('messages.responded') }}</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

@endsection
