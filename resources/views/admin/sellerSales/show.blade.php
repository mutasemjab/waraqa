@extends('layouts.admin')

@section('title', __('messages.sale_details') . ' - ' . $sale->sale_number)
@section('page-title', $sale->sale_number)

@section('content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h1 class="page-title">{{ __('messages.sale_details') }}</h1>
            <p class="page-subtitle">{{ __('messages.sale_number') }}: <strong>{{ $sale->sale_number }}</strong></p>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.seller-sales.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>{{ __('messages.back_to_list') }}
            </a>
        </div>
    </div>
</div>

<div class="row">
    <!-- Main Content -->
    <div class="col-md-8">
        <!-- Sale Header Info -->
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0">{{ __('messages.sale_information') }}</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="mb-2">
                            <strong>{{ __('messages.sale_number') }}:</strong><br>
                            <span class="badge bg-primary">{{ $sale->sale_number }}</span>
                        </p>
                        <p class="mb-2">
                            <strong>{{ __('messages.date') }}:</strong><br>
                            {{ \Carbon\Carbon::parse($sale->sale_date)->format('Y-m-d H:i') }}
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-2">
                            <strong>{{ __('messages.seller_name') }}:</strong><br>
                            {{ $sale->user->name }}
                        </p>
                    </div>
                </div>

                @if($sale->notes)
                    <div class="alert alert-info">
                        <strong>{{ __('messages.notes') }}:</strong><br>
                        {{ $sale->notes }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Sale Items -->
        <div class="card mt-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">{{ __('messages.sale_items') }}</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>{{ __('messages.product') }}</th>
                                <th>{{ __('messages.product_code') }}</th>
                                <th class="text-center">{{ __('messages.quantity') }}</th>
                                <th class="text-right">{{ __('messages.price_with_tax') ?? 'السعر (شامل الضريبة)' }}</th>
                                <th class="text-center">{{ __('messages.tax_percentage') }}</th>
                                <th class="text-right">{{ __('messages.subtotal') }}</th>
                                <th class="text-right">{{ __('messages.tax_amount') }}</th>
                                <th class="text-right">{{ __('messages.total') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sale->items as $item)
                                <tr>
                                    <td>
                                        <strong>{{ $item->product_name }}</strong>
                                    </td>
                                    <td>
                                        <code>{{ $item->product_code ?? 'N/A' }}</code>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-success">{{ $item->quantity }}</span>
                                    </td>
                                    <td class="text-right">
                                        <x-riyal-icon /> {{ number_format($item->unit_price, 2) }}
                                    </td>
                                    <td class="text-center">
                                        @if($item->tax_percentage > 0)
                                            <span class="badge bg-warning">{{ $item->tax_percentage }}%</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        <x-riyal-icon /> {{ number_format($item->total_price_before_tax, 2) }}
                                    </td>
                                    <td class="text-right">
                                        @if($item->total_tax > 0)
                                            <span class="text-warning"><x-riyal-icon /> {{ number_format($item->total_tax, 2) }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        <strong><x-riyal-icon /> {{ number_format($item->total_price_after_tax, 2) }}</strong>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Panel -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">{{ __('messages.summary') }}</h5>
            </div>
            <div class="card-body">
                <div class="summary-item mb-3">
                    <div class="d-flex justify-content-between">
                        <span>{{ __('messages.total_items') }}:</span>
                        <strong>{{ $sale->items->count() }}</strong>
                    </div>
                </div>

                <div class="summary-item mb-3">
                    <div class="d-flex justify-content-between">
                        <span>{{ __('messages.total_quantity') }}:</span>
                        <strong>{{ $sale->items->sum('quantity') }}</strong>
                    </div>
                </div>

                <div class="summary-item mb-3">
                    <div class="d-flex justify-content-between">
                        <span>{{ __('messages.subtotal') }}:</span>
                        <strong><x-riyal-icon /> {{ number_format($sale->items->sum('total_price_before_tax'), 2) }}</strong>
                    </div>
                </div>

                <div class="summary-item mb-3">
                    <div class="d-flex justify-content-between">
                        <span>{{ __('messages.total_tax') }}:</span>
                        <strong class="text-warning"><x-riyal-icon /> {{ number_format($sale->total_tax, 2) }}</strong>
                    </div>
                </div>

                <hr>

                <div class="summary-item mb-3">
                    <div class="d-flex justify-content-between">
                        <h5>{{ __('messages.total_amount') }}:</h5>
                        <h5 class="text-primary"><x-riyal-icon /> {{ number_format($sale->total_amount, 2) }}</h5>
                    </div>
                </div>

                <!-- Commission Info -->
                @if($sale->user && $sale->user->commission_percentage)
                    <hr>
                    <div class="alert alert-info">
                        <h6 class="mb-2">{{ __('messages.commission') }}</h6>
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ __('messages.seller_commission') }} ({{ $sale->user->commission_percentage }}%):</span>
                            <strong><x-riyal-icon /> {{ number_format($sale->total_amount * ($sale->user->commission_percentage / 100), 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>{{ __('messages.amount_due_to_waraqa') }}:</span>
                            <strong><x-riyal-icon /> {{ number_format($sale->total_amount * (1 - $sale->user->commission_percentage / 100), 2) }}</strong>
                        </div>
                    </div>
                @endif

                <!-- Actions -->
                <div class="d-grid gap-2 mt-4">
                    <a href="{{ route('admin.seller-sales.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>{{ __('messages.back_to_list') }}
                    </a>
                </div>
            </div>
        </div>

        <!-- Metadata -->
        <div class="card mt-3">
            <div class="card-header bg-light">
                <h6 class="mb-0">{{ __('messages.metadata') }}</h6>
            </div>
            <div class="card-body" style="font-size: 0.85rem;">
                <p class="mb-2">
                    <strong>{{ __('messages.created_at') }}:</strong><br>
                    {{ $sale->created_at->format('Y-m-d H:i:s') }}
                </p>
                <p class="mb-0">
                    <strong>{{ __('messages.updated_at') }}:</strong><br>
                    {{ $sale->updated_at->format('Y-m-d H:i:s') }}
                </p>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    @media print {
        .page-header {
            display: none;
        }
        .btn {
            display: none !important;
        }
    }

    .summary-item {
        padding-bottom: 0.5rem;
        border-bottom: 1px solid #e9ecef;
    }

    .summary-item:last-child {
        border-bottom: none;
    }
</style>
@endpush
