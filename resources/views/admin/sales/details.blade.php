@extends('layouts.admin')

@section('title', __('messages.sale_details'))

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-title">
                    <i class="fas fa-receipt me-2"></i>{{ __('messages.sale_details') }}
                </h1>
                <p class="page-subtitle">{{ __('messages.view_sale_information') }}</p>
            </div>
            <div class="col-auto">
                <a href="javascript:history.back()" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>{{ __('messages.back') }}
                </a>
            </div>
        </div>
    </div>

    <!-- Sale Header Card -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="fas fa-info-circle me-2"></i>{{ __('messages.sale_information') }}
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="text-muted">{{ __('messages.sale_number') }}</label>
                        <p class="h5"><strong>{{ $sale->sale_number }}</strong></p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="text-muted">{{ __('messages.date') }}</label>
                        <p class="h5"><strong>{{ \Carbon\Carbon::parse($sale->sale_date)->format('Y-m-d H:i') }}</strong></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Information Card -->
    <div class="card mb-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">
                <i class="fas fa-user me-2"></i>{{ __('messages.customer_information') }}
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="text-muted">{{ __('messages.customer_name') }}</label>
                        <p class="h5"><strong>{{ $sale->customer_name }}</strong></p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted">{{ __('messages.customer_phone') }}</label>
                        <p class="h5"><strong>{{ $sale->customer_phone ?? '-' }}</strong></p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="text-muted">{{ __('messages.customer_email') }}</label>
                        <p class="h5"><strong>{{ $sale->customer_email ?? '-' }}</strong></p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted">{{ __('messages.customer_address') }}</label>
                        <p class="h5"><strong>{{ $sale->customer_address ?? '-' }}</strong></p>
                    </div>
                </div>
            </div>
            @if ($sale->notes)
                <div class="row">
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="text-muted">{{ __('messages.notes') }}</label>
                            <p class="h5"><strong>{{ $sale->notes }}</strong></p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Sale Items Card -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">
                <i class="fas fa-boxes me-2"></i>{{ __('messages.items') }}
            </h5>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>{{ __('messages.product_name') }}</th>
                        <th>{{ __('messages.product_code') }}</th>
                        <th>{{ __('messages.quantity') }}</th>
                        <th>{{ __('messages.unit_price') }}</th>
                        <th>{{ __('messages.tax_percentage') }}</th>
                        <th>{{ __('messages.total_before_tax') }}</th>
                        <th>{{ __('messages.tax_amount') }}</th>
                        <th>{{ __('messages.total_after_tax') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sale->items as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <strong>{{ $item->product_name }}</strong>
                                @if($item->product)
                                    <br>
                                    <small class="text-muted">{{ $item->product->name ?? '-' }}</small>
                                @endif
                            </td>
                            <td>{{ $item->product_code ?? '-' }}</td>
                            <td>
                                <span class="badge bg-info">{{ $item->quantity }}</span>
                            </td>
                            <td>{{ number_format($item->unit_price, 2) }} <x-riyal-icon style="width: 12px; height: 12px;" /></td>
                            <td>{{ $item->tax_percentage }}%</td>
                            <td>{{ number_format($item->total_price_before_tax, 2) }} <x-riyal-icon style="width: 12px; height: 12px;" /></td>
                            <td>{{ number_format($item->total_tax, 2) }} <x-riyal-icon style="width: 12px; height: 12px;" /></td>
                            <td>
                                <strong>{{ number_format($item->total_price_after_tax, 2) }} <x-riyal-icon style="width: 12px; height: 12px;" /></strong>
                            </td>
                        </tr>
                        @if($item->notes)
                            <tr class="table-light">
                                <td colspan="9" class="text-muted ps-4">
                                    <small><i class="fas fa-sticky-note me-2"></i>{{ $item->notes }}</small>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                <i class="fas fa-inbox" style="font-size: 2rem;"></i>
                                <p class="mt-2">{{ __('messages.no_items_found') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Summary Card -->
    <div class="card">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">
                <i class="fas fa-calculator me-2"></i>{{ __('messages.summary') }}
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <label class="text-muted d-block mb-2">{{ __('messages.total_quantity') }}</label>
                            <h3 class="mb-0">
                                <strong>{{ $sale->items->sum('quantity') }}</strong>
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-warning text-white">
                        <div class="card-body text-center">
                            <label class="d-block mb-2">{{ __('messages.total_tax') }}</label>
                            <h3 class="mb-0">
                                <strong>{{ number_format($sale->total_tax, 2) }}</strong>
                            </h3>
                            <small><x-riyal-icon style="width: 14px; height: 14px;" /></small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <label class="d-block mb-2">{{ __('messages.total_amount') }}</label>
                            <h3 class="mb-0">
                                <strong>{{ number_format($sale->total_amount, 2) }}</strong>
                            </h3>
                            <small><x-riyal-icon style="width: 14px; height: 14px;" /></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
