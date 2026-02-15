@extends('layouts.user')

@section('title', __('messages.sale_details'))
@section('page-title', __('messages.sale_details'))

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">{{ __('messages.sale_details') }}</h1>
            <p class="page-subtitle">{{ $sale->sale_number }}</p>
        </div>
        <a href="{{ route('user.sales.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>{{ __('messages.back') }}
        </a>
    </div>
</div>

<div class="row">
    <!-- Sale Information -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>{{ __('messages.sale_information') }}
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted">{{ __('messages.sale_number') }}</label>
                            <p class="fw-bold">{{ $sale->sale_number }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">{{ __('messages.sale_date') }}</label>
                            <p class="fw-bold">{{ Carbon\Carbon::parse($sale->sale_date)->format('Y-m-d') }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted">{{ __('messages.status') }}</label>
                            <p>
                                <span class="badge bg-{{ $sale->status->getColor() }} fs-6">
                                    {{ $sale->status->getLabel() }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
                @if($sale->status === App\Enums\SellerSaleStatus::REJECTED)
                    <div class="alert alert-danger mt-3">
                        <strong>{{ __('messages.rejection_reason') }}:</strong><br>
                        {{ $sale->rejection_reason }}
                    </div>
                @endif
                @if($sale->status !== App\Enums\SellerSaleStatus::PENDING)
                    <div class="alert alert-info mt-3">
                        <strong>{{ __('messages.processed_by') }}:</strong> {{ $sale->approvedBy?->name ?? 'N/A' }} - {{ $sale->approved_at?->format('Y-m-d H:i') ?? 'N/A' }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Products Table -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-box me-2"></i>{{ __('messages.products_sold') }}
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('messages.product_name') }}</th>
                                <th width="80">{{ __('messages.quantity') }}</th>
                                <th width="100">{{ __('messages.unit_price') }}</th>
                                <th width="80">{{ __('messages.tax') }}%</th>
                                <th width="120">{{ __('messages.total') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sale->items as $item)
                                <tr>
                                    <td>
                                        <strong>{{ $item->product_name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $item->product_code }}</small>
                                    </td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-end">
                                        <x-riyal-icon style="width: 12px; height: 12px;" /> {{ number_format($item->unit_price, 2) }}
                                    </td>
                                    <td class="text-center">{{ number_format($item->tax_percentage, 2) }}%</td>
                                    <td class="text-end fw-bold">
                                        <x-riyal-icon style="width: 12px; height: 12px;" /> {{ number_format($item->total_price_after_tax, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Card -->
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-calculator me-2"></i>{{ __('messages.sale_summary') }}
                </h5>
            </div>
            <div class="card-body">
                <div class="summary-item d-flex justify-content-between mb-3">
                    <span>{{ __('messages.total_items') }}:</span>
                    <strong>{{ $sale->items->sum('quantity') }}</strong>
                </div>

                <div class="summary-item d-flex justify-content-between mb-3">
                    <span>{{ __('messages.total_before_tax') }}:</span>
                    <strong>
                        <x-riyal-icon style="width: 12px; height: 12px;" />
                        {{ number_format($sale->items->sum('total_price_before_tax'), 2) }}
                    </strong>
                </div>

                <div class="summary-item d-flex justify-content-between mb-3">
                    <span>{{ __('messages.total_tax') }}:</span>
                    <strong class="text-warning">
                        <x-riyal-icon style="width: 12px; height: 12px;" />
                        {{ number_format($sale->items->sum('total_tax'), 2) }}
                    </strong>
                </div>

                <hr>

                <div class="summary-item d-flex justify-content-between mb-0">
                    <strong>{{ __('messages.total_after_tax') }}:</strong>
                    <strong class="text-success fs-5">
                        <x-riyal-icon style="width: 14px; height: 14px;" />
                        {{ number_format($sale->total_amount, 2) }}
                    </strong>
                </div>
            </div>
        </div>

        <!-- Notes -->
        @if($sale->notes)
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-sticky-note me-2"></i>{{ __('messages.notes') }}
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">{{ $sale->notes }}</p>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
