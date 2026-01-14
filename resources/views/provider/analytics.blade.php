@extends('layouts.provider')

@section('title', __('messages.analytics'))
@section('page-title', __('messages.analytics'))

@section('content')
<div class="page-header mb-4">
    <h1 class="page-title">{{ __('messages.analytics') }}</h1>
    <p class="page-subtitle">{{ __('messages.analyze_your_business_performance') }}</p>
</div>

<!-- Revenue Overview -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <h6 class="text-muted mb-2">{{ __('messages.total_revenue') }}</h6>
                <h3 class="text-success mb-0">
                    <x-riyal-icon /> {{ number_format($revenueBreakdown['total_orders_revenue'], 2) }}
                </h3>
                <small class="text-muted">{{ __('messages.from_all_orders') }}</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <h6 class="text-muted mb-2">{{ __('messages.current_month_revenue') }}</h6>
                <h3 class="text-primary mb-0">
                    <x-riyal-icon /> {{ number_format($revenueBreakdown['current_month_revenue'], 2) }}
                </h3>
                <small class="text-muted">{{ __('messages.this_month') }}</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <h6 class="text-muted mb-2">{{ __('messages.previous_month_revenue') }}</h6>
                <h3 class="text-info mb-0">
                    <x-riyal-icon /> {{ number_format($revenueBreakdown['previous_month_revenue'], 2) }}
                </h3>
                <small class="text-muted">{{ __('messages.last_month') }}</small>
            </div>
        </div>
    </div>
</div>

<!-- Product Performance -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-chart-bar me-2"></i>{{ __('messages.product_performance') }}
        </h5>
    </div>
    <div class="card-body">
        @if($productPerformance->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>{{ __('messages.product_name') }}</th>
                            <th>{{ __('messages.total_ordered') }}</th>
                            <th>{{ __('messages.total_sold') }}</th>
                            <th>{{ __('messages.revenue') }}</th>
                            <th>{{ __('messages.status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($productPerformance as $product)
                        <tr>
                            <td>
                                <strong>{{ app()->getLocale() == 'ar' ? $product->name_ar : $product->name_en }}</strong>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $product->order_products_sum_quantity ?? 0 }} {{ __('messages.items') }}</span>
                            </td>
                            <td>
                                <span class="badge bg-success">{{ $product->voucher_products_sum_quantity ?? 0 }} {{ __('messages.items') }}</span>
                            </td>
                            <td>
                                <strong class="text-success">
                                    <x-riyal-icon /> {{ number_format($product->order_products_sum_total_price_after_tax ?? 0, 2) }}
                                </strong>
                            </td>
                            <td>
                                @if(($product->order_products_sum_quantity ?? 0) > 10)
                                    <span class="badge bg-success">{{ __('messages.high') }}</span>
                                @elseif(($product->order_products_sum_quantity ?? 0) > 5)
                                    <span class="badge bg-warning">{{ __('messages.medium') }}</span>
                                @else
                                    <span class="badge bg-danger">{{ __('messages.low') }}</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-info" role="alert">
                <i class="fas fa-info-circle me-2"></i>
                {{ __('messages.no_product_data_yet') }}
            </div>
        @endif
    </div>
</div>

<!-- Monthly Sales Trends -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-chart-line me-2"></i>{{ __('messages.monthly_sales_trends') }}
        </h5>
    </div>
    <div class="card-body">
        @if($monthlySales->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>{{ __('messages.month') }}</th>
                            <th>{{ __('messages.quantity') }}</th>
                            <th>{{ __('messages.revenue') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($monthlySales as $sale)
                        <tr>
                            <td>
                                <strong>
                                    {{ \Carbon\Carbon::createFromFormat('Y-m', $sale->year . '-' . str_pad($sale->month, 2, '0', STR_PAD_LEFT))->format('M Y') }}
                                </strong>
                            </td>
                            <td>
                                <span class="badge bg-primary">{{ $sale->total_quantity }} {{ __('messages.items') }}</span>
                            </td>
                            <td>
                                <strong class="text-success">
                                    <x-riyal-icon /> {{ number_format($sale->total_revenue, 2) }}
                                </strong>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-info" role="alert">
                <i class="fas fa-info-circle me-2"></i>
                {{ __('messages.no_sales_data_yet') }}
            </div>
        @endif
    </div>
</div>

@endsection
