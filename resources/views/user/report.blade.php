@extends('layouts.user')

@section('title', __('messages.shopping_report'))
@section('page-title', __('messages.shopping_report'))

@section('content')
<div class="page-header">
    <h1 class="page-title">{{ __('messages.comprehensive_shopping_report') }}</h1>
    <p class="page-subtitle">{{ __('messages.detailed_analysis_of_shopping_behavior') }}</p>
</div>

<!-- User Information -->
<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">
            <i class="fas fa-user me-2"></i>{{ __('messages.user_information') }}
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr>
                        <td><strong>{{ __('messages.name') }}:</strong></td>
                        <td>{{ $report['user_info']['name'] }}</td>
                    </tr>
                    <tr>
                        <td><strong>{{ __('messages.email') }}:</strong></td>
                        <td>{{ $report['user_info']['email'] ?? __('messages.not_provided') }}</td>
                    </tr>
                    <tr>
                        <td><strong>{{ __('messages.phone') }}:</strong></td>
                        <td>{{ $report['user_info']['phone'] }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr>
                        <td><strong>{{ __('messages.member_since') }}:</strong></td>
                        <td>{{ $report['user_info']['member_since'] }}</td>
                    </tr>
                    <tr>
                        <td><strong>{{ __('messages.last_order') }}:</strong></td>
                        <td>{{ $report['user_info']['last_order'] ?? __('messages.no_orders') }}</td>
                    </tr>
                    <tr>
                        <td><strong>{{ __('messages.report_generated') }}:</strong></td>
                        <td>{{ now()->format('Y-m-d') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Order Summary -->
<div class="card mb-4">
    <div class="card-header bg-success text-white">
        <h5 class="mb-0">
            <i class="fas fa-shopping-cart me-2"></i>{{ __('messages.order_summary') }}
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <div class="text-center">
                    <h3 class="text-primary">{{ $report['order_summary']['total_orders'] }}</h3>
                    <p class="text-muted">{{ __('messages.total_orders') }}</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="text-center">
                    <h3 class="text-success"><x-riyal-icon /> {{ number_format($report['order_summary']['total_spent'], 2) }}</h3>
                    <p class="text-muted">{{ __('messages.total_spent') }}</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="text-center">
                    <h3 class="text-info"><x-riyal-icon /> {{ number_format($report['order_summary']['average_order_value'], 2) }}</h3>
                    <p class="text-muted">{{ __('messages.average_order_value') }}</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="text-center">
                    <h3 class="text-warning">{{ $report['order_summary']['pending_orders'] }}</h3>
                    <p class="text-muted">{{ __('messages.pending_orders') }}</p>
                </div>
            </div>
        </div>
        
        <hr>
        
        <div class="row">
            <div class="col-md-4">
                <div class="d-flex justify-content-between">
                    <span>{{ __('messages.completed_orders') }}:</span>
                    <span class="badge bg-success">{{ $report['order_summary']['completed_orders'] }}</span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="d-flex justify-content-between">
                    <span>{{ __('messages.cancelled_orders') }}:</span>
                    <span class="badge bg-danger">{{ $report['order_summary']['cancelled_orders'] }}</span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="d-flex justify-content-between">
                    <span>{{ __('messages.completion_rate') }}:</span>
                    <span class="badge bg-info">
                        {{ $report['order_summary']['total_orders'] > 0 ? number_format(($report['order_summary']['completed_orders'] / $report['order_summary']['total_orders']) * 100, 1) : 0 }}%
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Summary -->
<div class="card mb-4">
    <div class="card-header bg-warning text-white">
        <h5 class="mb-0">
            <i class="fas fa-credit-card me-2"></i>{{ __('messages.payment_summary') }}
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <div class="text-center">
                    <h4 class="text-danger"><x-riyal-icon /> {{ number_format($report['payment_summary']['total_debt'], 2) }}</h4>
                    <p class="text-muted">{{ __('messages.outstanding_debt') }}</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-center">
                    <h4 class="text-success"><x-riyal-icon /> {{ number_format($report['payment_summary']['total_paid'], 2) }}</h4>
                    <p class="text-muted">{{ __('messages.total_paid') }}</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-center">
                    <h4 class="text-info">{{ number_format($report['payment_summary']['payment_completion_rate'], 1) }}%</h4>
                    <p class="text-muted">{{ __('messages.payment_completion_rate') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Top Products -->
<div class="card mb-4">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0">
            <i class="fas fa-star me-2"></i>{{ __('messages.top_purchased_products') }}
        </h5>
    </div>
    <div class="card-body">
        @if($report['top_products']->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>{{ __('messages.rank') }}</th>
                            <th>{{ __('messages.product') }}</th>
                            <th>{{ __('messages.quantity_purchased') }}</th>
                            <th>{{ __('messages.total_spent') }}</th>
                            <th>{{ __('messages.percentage_of_total') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($report['top_products'] as $index => $product)
                            <tr>
                                <td>
                                    <span class="badge bg-primary">#{{ $index + 1 }}</span>
                                </td>
                                <td>
                                    <strong>{{ $product->name_ar }}</strong>
                                    @if($product->name_en)
                                        <br><small class="text-muted">{{ $product->name_en }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $product->total_quantity }}</span>
                                </td>
                                <td>
                                    <strong><x-riyal-icon /> {{ number_format($product->total_spent, 2) }}</strong>
                                </td>
                                <td>
                                    @php
                                        $percentage = $report['order_summary']['total_spent'] > 0 ? 
                                            ($product->total_spent / $report['order_summary']['total_spent']) * 100 : 0;
                                    @endphp
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar" style="width: {{ $percentage }}%">
                                            {{ number_format($percentage, 1) }}%
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-4">
                <i class="fas fa-box text-muted" style="font-size: 3rem;"></i>
                <h5 class="mt-3 text-muted">{{ __('messages.no_products_purchased') }}</h5>
                <p class="text-muted">{{ __('messages.start_shopping_to_see_analytics') }}</p>
            </div>
        @endif
    </div>
</div>

<!-- Recent Activity -->
<div class="card mb-4">
    <div class="card-header bg-secondary text-white">
        <h5 class="mb-0">
            <i class="fas fa-history me-2"></i>{{ __('messages.recent_activity') }}
        </h5>
    </div>
    <div class="card-body">
        @if($report['recent_activity']->count() > 0)
            <div class="timeline">
                @foreach($report['recent_activity'] as $order)
                    <div class="timeline-item">
                        <div class="timeline-marker bg-primary"></div>
                        <div class="timeline-content">
                            <h6 class="mb-1">{{ __('messages.order') }} {{ $order->number }}</h6>
                            <p class="text-muted mb-1"><x-riyal-icon /> {{ number_format($order->total_prices, 2) }} • {{ $order->orderProducts->count() }} {{ __('messages.items') }}</p>
                            <small class="text-muted">{{ Carbon\Carbon::parse($order->date)->format('Y-m-d') }}</small>
                            
                            @if($order->orderProducts->count() > 0)
                                <div class="mt-2">
                                    <small class="text-muted">{{ __('messages.products') }}: </small>
                                    @foreach($order->orderProducts->take(3) as $orderProduct)
                                        <span class="badge bg-light text-dark me-1">{{ $orderProduct->product->name_ar }}</span>
                                    @endforeach
                                    @if($order->orderProducts->count() > 3)
                                        <span class="text-muted">+{{ $order->orderProducts->count() - 3 }} {{ __('messages.more') }}</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-4">
                <i class="fas fa-history text-muted" style="font-size: 3rem;"></i>
                <h5 class="mt-3 text-muted">{{ __('messages.no_recent_activity') }}</h5>
            </div>
        @endif
    </div>
</div>

<!-- Report Actions -->
<div class="card">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h5 class="mb-1">{{ __('messages.share_this_report') }}</h5>
                <p class="text-muted mb-0">{{ __('messages.send_report_to_admin_for_analysis') }}</p>
            </div>
            <div class="col-md-4 text-end">
                <button class="btn btn-primary" onclick="printReport()">
                    <i class="fas fa-print me-1"></i>{{ __('messages.print_report') }}
                </button>
                <button class="btn btn-success" onclick="sendToAdmin()">
                    <i class="fas fa-share me-1"></i>{{ __('messages.send_to_admin') }}
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid white;
    box-shadow: 0 0 0 2px #e9ecef;
}

.timeline-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border-left: 3px solid var(--primary-color);
}

@media print {
    .page-header,
    .btn,
    .navbar,
    .sidebar {
        display: none !important;
    }
    
    .card {
        border: 1px solid #dee2e6 !important;
        box-shadow: none !important;
    }
}
</style>
@endsection

@push('scripts')
<script>
function printReport() {
    window.print();
}

function sendToAdmin() {
    // This would typically send the report to admin via AJAX
    if (confirm('{{ __("messages.confirm_send_report_to_admin") }}')) {
        // Simulate sending
        alert('{{ __("messages.report_sent_successfully") }}');
    }
}
</script>
@endpush