@extends('layouts.provider')

@section('title', $product->name_ar . ' - ' . __('messages.product_details'))
@section('page-title', __('messages.product_details'))

@section('content')
<div class="page-header">
    <h1 class="page-title">{{ $product->name_ar }}</h1>
    <p class="page-subtitle">{{ __('messages.comprehensive_product_analytics') }}</p>
</div>

<!-- Product Overview -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                @if($product->photo)
                    <img src="{{ asset('assets/admin/uploads/' . $product->photo) }}" alt="{{ $product->name_ar }}" class="img-fluid rounded mb-3" style="max-height: 200px;">
                @else
                    <div class="bg-light rounded d-flex align-items-center justify-content-center mb-3" style="height: 200px;">
                        <i class="fas fa-box text-muted" style="font-size: 4rem;"></i>
                    </div>
                @endif
                
                <h5>{{ $product->name_ar }}</h5>
                @if($product->name_en)
                    <p class="text-muted">{{ $product->name_en }}</p>
                @endif
                
                <div class="row text-center">
                    <div class="col-6">
                        <h4 class="text-success">${{ number_format($product->selling_price, 2) }}</h4>
                        <small class="text-muted">{{ __('messages.selling_price') }}</small>
                    </div>
                    <div class="col-6">
                        <h4 class="text-info">{{ $product->tax }}%</h4>
                        <small class="text-muted">{{ __('messages.tax_rate') }}</small>
                    </div>
                </div>
                
                @if($product->category)
                    <div class="mt-3">
                        <span class="badge bg-primary">{{ $product->category->name_ar }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <!-- Analytics Cards -->
        <div class="row">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon primary">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="stat-content">
                        <h3>{{ $analytics['total_ordered'] }}</h3>
                        <p>{{ __('messages.total_ordered') }}</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon success">
                        <i class="fas fa-cash-register"></i>
                    </div>
                    <div class="stat-content">
                        <h3>{{ $analytics['total_sold_by_users'] }}</h3>
                        <p>{{ __('messages.sold_by_users') }}</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon warning">
                        <i class="fas fa-warehouse"></i>
                    </div>
                    <div class="stat-content">
                        <h3>{{ $analytics['current_in_warehouses'] }}</h3>
                        <p>{{ __('messages.in_warehouses') }}</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon info">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-content">
                        <h3>{{ $analytics['total_users'] }}</h3>
                        <p>{{ __('messages.customers') }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Revenue Information -->
        <div class="card mt-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="text-center">
                            <h4 class="text-success">${{ number_format($analytics['total_revenue'], 2) }}</h4>
                            <p class="text-muted">{{ __('messages.total_revenue_from_product') }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center">
                            <h4 class="text-info">${{ number_format($analytics['average_selling_price'] ?? 0, 2) }}</h4>
                            <p class="text-muted">{{ __('messages.average_user_selling_price') }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center">
                            @php
                                $conversionRate = $analytics['total_ordered'] > 0 ? ($analytics['total_sold_by_users'] / $analytics['total_ordered']) * 100 : 0;
                            @endphp
                            <h4 class="text-warning">{{ number_format($conversionRate, 1) }}%</h4>
                            <p class="text-muted">{{ __('messages.sales_conversion_rate') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Detailed Analytics -->
<div class="row">
    <!-- Users Who Have This Product -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-users me-2"></i>{{ __('messages.users_with_this_product') }}
                </h5>
            </div>
            <div class="card-body">
                @if($usersWithProduct->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{ __('messages.user') }}</th>
                                    <th>{{ __('messages.received') }}</th>
                                    <th>{{ __('messages.sold') }}</th>
                                    <th>{{ __('messages.current_stock') }}</th>
                                    <th>{{ __('messages.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($usersWithProduct as $user)
                                    <tr>
                                        <td>
                                            <div>
                                                <strong>{{ $user->name }}</strong>
                                                <br><small class="text-muted">{{ $user->phone }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">{{ $user->received_quantity }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-success">{{ $user->sold_quantity }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $user->current_quantity > 5 ? 'success' : ($user->current_quantity > 0 ? 'warning' : 'danger') }}">
                                                {{ $user->current_quantity }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('provider.users.details', $user->id) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-users text-muted" style="font-size: 3rem;"></i>
                        <h6 class="mt-3 text-muted">{{ __('messages.no_users_have_this_product') }}</h6>
                        <p class="text-muted">{{ __('messages.product_not_distributed_yet') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Sales History -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-history me-2"></i>{{ __('messages.recent_sales_history') }}
                </h5>
            </div>
            <div class="card-body">
                @if($salesHistory->count() > 0)
                    <div class="timeline">
                        @foreach($salesHistory as $sale)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-success"></div>
                                <div class="timeline-content">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1">{{ $sale->noteVoucher->fromWarehouse->user->name }}</h6>
                                            <p class="text-muted mb-1">
                                                {{ __('messages.sold') }} {{ $sale->quantity }} {{ __('messages.items') }}
                                                @if($sale->purchasing_price)
                                                    {{ __('messages.at') }} ${{ number_format($sale->purchasing_price, 2) }}
                                                @endif
                                            </p>
                                            <small class="text-muted">{{ $sale->noteVoucher->date_note_voucher }}</small>
                                        </div>
                                        <div class="text-end">
                                            @if($sale->purchasing_price)
                                                <div class="fw-bold text-success">
                                                    ${{ number_format($sale->quantity * $sale->purchasing_price, 2) }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-history text-muted" style="font-size: 3rem;"></i>
                        <h6 class="mt-3 text-muted">{{ __('messages.no_sales_history') }}</h6>
                        <p class="text-muted">{{ __('messages.sales_will_appear_here') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Order History -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-shopping-cart me-2"></i>{{ __('messages.order_history') }}
        </h5>
    </div>
    <div class="card-body">
        @if($orderHistory->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>{{ __('messages.order_number') }}</th>
                            <th>{{ __('messages.customer') }}</th>
                            <th>{{ __('messages.date') }}</th>
                            <th>{{ __('messages.quantity') }}</th>
                            <th>{{ __('messages.unit_price') }}</th>
                            <th>{{ __('messages.total_value') }}</th>
                            <th>{{ __('messages.status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orderHistory as $orderProduct)
                            <tr>
                                <td><strong>{{ $orderProduct->order->number }}</strong></td>
                                <td>
                                    <div>
                                        <strong>{{ $orderProduct->order->user->name }}</strong>
                                        <br><small class="text-muted">{{ $orderProduct->order->user->phone }}</small>
                                    </div>
                                </td>
                                <td>{{ Carbon\Carbon::parse($orderProduct->order->date)->format('M d, Y') }}</td>
                                <td>
                                    <span class="badge bg-info">{{ $orderProduct->quantity }}</span>
                                </td>
                                <td>${{ number_format($orderProduct->unit_price, 2) }}</td>
                                <td class="fw-bold text-success">${{ number_format($orderProduct->total_price_after_tax, 2) }}</td>
                                <td>
                                    @if($orderProduct->order->status == 1)
                                        <span class="badge bg-success">{{ __('messages.completed') }}</span>
                                    @elseif($orderProduct->order->status == 2)
                                        <span class="badge bg-warning">{{ __('messages.pending') }}</span>
                                    @else
                                        <span class="badge bg-danger">{{ __('messages.cancelled') }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-4">
                <i class="fas fa-shopping-cart text-muted" style="font-size: 3rem;"></i>
                <h6 class="mt-3 text-muted">{{ __('messages.no_orders_yet') }}</h6>
                <p class="text-muted">{{ __('messages.orders_will_appear_here') }}</p>
            </div>
        @endif
    </div>
</div>

<!-- Action Buttons -->
<div class="card">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h5 class="mb-1">{{ __('messages.product_actions') }}</h5>
                <p class="text-muted mb-0">{{ __('messages.manage_this_product') }}</p>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('provider.products') }}" class="btn btn-secondary me-2">
                    <i class="fas fa-arrow-left me-1"></i>{{ __('messages.back_to_products') }}
                </a>
                <a href="{{ route('provider.products.edit', $product->id) }}" class="btn btn-primary">
                    <i class="fas fa-edit me-1"></i>{{ __('messages.edit_product') }}
                </a>
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
    border-left: 3px solid var(--bs-success);
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    margin-bottom: 20px;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: var(--bs-primary);
}

.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    color: white;
    margin-bottom: 15px;
}

.stat-icon.primary { background: linear-gradient(135deg, var(--bs-primary), #6366f1); }
.stat-icon.success { background: linear-gradient(135deg, var(--bs-success), #059669); }
.stat-icon.warning { background: linear-gradient(135deg, var(--bs-warning), #d97706); }
.stat-icon.info { background: linear-gradient(135deg, var(--bs-info), #0284c7); }

.stat-content h3 {
    font-size: 1.8rem;
    font-weight: 700;
    margin: 0;
    color: var(--bs-dark);
}

.stat-content p {
    margin: 0;
    color: #6b7280;
    font-size: 0.9rem;
}
</style>
@endsection

@push('scripts')
<script>
// Add any product-specific JavaScript here
console.log('Product Details Loaded: {{ $product->name_ar }}');

// Example: Real-time updates for stock levels
setInterval(function() {
    // You could add AJAX calls here to refresh stock data
    console.log('Checking for stock updates...');
}, 30000);
</script>
@endpush