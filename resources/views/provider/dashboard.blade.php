@extends('layouts.provider')

@section('title', __('messages.provider_dashboard'))
@section('page-title', __('messages.dashboard'))

@section('content')
<div class="page-header">
    <h1 class="page-title">{{ __('messages.welcome_back') }}, {{ auth('provider')->user()->name }}!</h1>
    <p class="page-subtitle">{{ __('messages.provider_dashboard_subtitle') }}</p>
</div>

<!-- Stats Grid -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon primary">
            <i class="fas fa-box"></i>
        </div>
        <div class="stat-content">
            <h3>{{ $stats['total_products'] }}</h3>
            <p>{{ __('messages.my_products') }}</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon success">
            <i class="fas fa-shopping-cart"></i>
        </div>
        <div class="stat-content">
            <h3>{{ $stats['total_sold_items'] }}</h3>
            <p>{{ __('messages.items_sold_by_users') }}</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon info">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-content">
            <h3>{{ $stats['active_users'] }}</h3>
            <p>{{ __('messages.active_customers') }}</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon warning">
            <i class="fas fa-dollar-sign"></i>
        </div>
        <div class="stat-content">
            <h3>${{ number_format($stats['total_revenue'], 2) }}</h3>
            <p>{{ __('messages.total_revenue') }}</p>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-bolt me-2"></i>{{ __('messages.quick_actions') }}
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-2">
                        <a href="{{ route('provider.products') }}" class="btn btn-outline-primary w-100 py-3">
                            <i class="fas fa-box d-block mb-2" style="font-size: 1.5rem;"></i>
                            {{ __('messages.my_products') }}
                        </a>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('provider.users') }}" class="btn btn-outline-success w-100 py-3">
                            <i class="fas fa-users d-block mb-2" style="font-size: 1.5rem;"></i>
                            {{ __('messages.my_customers') }}
                        </a>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('provider.analytics') }}" class="btn btn-outline-info w-100 py-3">
                            <i class="fas fa-chart-line d-block mb-2" style="font-size: 1.5rem;"></i>
                            {{ __('messages.analytics') }}
                        </a>
                    </div>
                    <div class="col-md-2">
                        <a href="#" class="btn btn-outline-warning w-100 py-3">
                            <i class="fas fa-file-alt d-block mb-2" style="font-size: 1.5rem;"></i>
                            {{ __('messages.reports') }}
                        </a>
                    </div>
                    <div class="col-md-2">
                        <a href="#" class="btn btn-outline-secondary w-100 py-3">
                            <i class="fas fa-warehouse d-block mb-2" style="font-size: 1.5rem;"></i>
                            {{ __('messages.inventory_status') }}
                        </a>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('provider.profile') }}" class="btn btn-outline-dark w-100 py-3">
                            <i class="fas fa-user-cog d-block mb-2" style="font-size: 1.5rem;"></i>
                            {{ __('messages.profile') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="row">
    <!-- Left Column -->
    <div class="col-lg-8">
        <!-- Recent Orders -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-shopping-cart me-2"></i>{{ __('messages.recent_orders_containing_my_products') }}
                </h5>
                <a href="{{ route('provider.orders') }}" class="btn btn-sm btn-outline-primary">
                    {{ __('messages.view_all') }}
                </a>
            </div>
            <div class="card-body">
                @if($recentOrders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{ __('messages.order_number') }}</th>
                                    <th>{{ __('messages.customer') }}</th>
                                    <th>{{ __('messages.date') }}</th>
                                    <th>{{ __('messages.my_products') }}</th>
                                    <th>{{ __('messages.my_revenue') }}</th>
                                    <th>{{ __('messages.status') }}</th>
                                    <th>{{ __('messages.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentOrders as $order)
                                    <tr>
                                        <td><strong>{{ $order->number }}</strong></td>
                                        <td>
                                            <div>
                                                <strong>{{ $order->user->name }}</strong>
                                                <br><small class="text-muted">{{ $order->user->phone }}</small>
                                            </div>
                                        </td>
                                        <td>{{ Carbon\Carbon::parse($order->date)->format('M d, Y') }}</td>
                                        <td>
                                            <span class="badge bg-info">
                                                {{ $order->orderProducts->count() }} {{ __('messages.products') }}
                                            </span>
                                            <br>
                                            <small class="text-muted">
                                                {{ $order->orderProducts->sum('quantity') }} {{ __('messages.items') }}
                                            </small>
                                        </td>
                                        <td class="fw-bold text-success">
                                            ${{ number_format($order->orderProducts->sum('total_price_after_tax'), 2) }}
                                        </td>
                                        <td>
                                            @if($order->status == 1)
                                                <span class="badge bg-success">{{ __('messages.completed') }}</span>
                                            @elseif($order->status == 2)
                                                <span class="badge bg-warning">{{ __('messages.pending') }}</span>
                                            @else
                                                <span class="badge bg-danger">{{ __('messages.cancelled') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="#" class="btn btn-sm btn-outline-primary" onclick="showOrderDetails({{ $order->id }})">
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
                        <i class="fas fa-shopping-cart text-muted" style="font-size: 3rem;"></i>
                        <h6 class="mt-3 text-muted">{{ __('messages.no_recent_orders') }}</h6>
                        <p class="text-muted">{{ __('messages.orders_will_appear_here') }}</p>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Recent Sales by Users -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-cash-register me-2"></i>{{ __('messages.recent_sales_by_users') }}
                </h5>
                <span class="badge bg-success">{{ __('messages.user_sales') }}</span>
            </div>
            <div class="card-body">
                @if($recentSales->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{ __('messages.sale_date') }}</th>
                                    <th>{{ __('messages.user') }}</th>
                                    <th>{{ __('messages.my_products_sold') }}</th>
                                    <th>{{ __('messages.quantity') }}</th>
                                    <th>{{ __('messages.sale_value') }}</th>
                                    <th>{{ __('messages.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentSales as $sale)
                                    <tr>
                                        <td>{{ Carbon\Carbon::parse($sale->date_note_voucher)->format('M d, Y') }}</td>
                                    @if ($sale->fromWarehouse && $sale->fromWarehouse->user)
                                        <div>
                                            <strong>{{ $sale->fromWarehouse->user->name }}</strong>
                                            <br><small class="text-muted">{{ $sale->fromWarehouse->user->phone }}</small>
                                        </div>
                                    @else
                                        <div>
                                            <strong>{{ __('messages.no_user') }}</strong>
                                        </div>
                                    @endif
                                        <td>
                                            @foreach($sale->voucherProducts as $vp)
                                                <span class="badge bg-light text-dark me-1">{{ $vp->product->name_ar }}</span>
                                            @endforeach
                                        </td>
                                        <td>
                                            <span class="badge bg-success">
                                                {{ $sale->voucherProducts->sum('quantity') }} {{ __('messages.items') }}
                                            </span>
                                        </td>
                                        <td class="fw-bold text-success">
                                            ${{ number_format($sale->voucherProducts->sum(function($vp) { return $vp->quantity * $vp->purchasing_price; }), 2) }}
                                        </td>
                                        <td>
                                            <a href="#" class="btn btn-sm btn-outline-success" onclick="showSaleDetails({{ $sale->id }})">
                                                <i class="fas fa-info-circle"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-cash-register text-muted" style="font-size: 3rem;"></i>
                        <h6 class="mt-3 text-muted">{{ __('messages.no_user_sales_yet') }}</h6>
                        <p class="text-muted">{{ __('messages.user_sales_will_appear_here') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Right Column -->
    <div class="col-lg-4">
        <!-- Top Selling Products -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-trophy me-2"></i>{{ __('messages.top_selling_products') }}
                </h5>
            </div>
            <div class="card-body">
                @if($topProducts->count() > 0)
                    @foreach($topProducts as $product)
                        <div class="d-flex align-items-center mb-3 p-2 border rounded">
                            <div class="me-3">
                                @if($product->photo)
                                    <img src="{{ asset('storage/' . $product->photo) }}" alt="{{ $product->name_ar }}" class="rounded" width="50" height="50">
                                @else
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                        <i class="fas fa-box text-muted"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">{{ $product->name_ar }}</h6>
                                <small class="text-muted">{{ $product->name_en }}</small>
                                <div class="mt-1">
                                    <span class="badge bg-primary">{{ $product->order_products_sum_quantity ?? 0 }} {{ __('messages.ordered') }}</span>
                                    <span class="badge bg-success">{{ $product->voucher_products_sum_quantity ?? 0 }} {{ __('messages.sold') }}</span>
                                </div>
                                <div class="text-success fw-bold">${{ number_format($product->selling_price, 2) }}</div>
                            </div>
                            <div>
                                <a href="{{ route('provider.products.details', $product->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-chart-line"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                    
                    <div class="text-center">
                        <a href="{{ route('provider.products') }}" class="btn btn-outline-primary btn-sm">
                            {{ __('messages.view_all_products') }}
                        </a>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-box text-muted" style="font-size: 2rem;"></i>
                        <p class="text-muted mt-2">{{ __('messages.no_products_yet') }}</p>
                        <a href="{{ route('provider.products.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i>{{ __('messages.add_first_product') }}
                        </a>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Business Summary -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-store me-2"></i>{{ __('messages.business_summary') }}
                </h5>
            </div>
            <div class="card-body text-center">
                <img src="{{ auth('provider')->user()->photo_url }}" alt="Provider" class="rounded-circle mb-3" width="80" height="80">
                <h6>{{ auth('provider')->user()->name }}</h6>
                <p class="text-muted small">{{ auth('provider')->user()->email }}</p>
                <p class="text-muted small">{{ auth('provider')->user()->phone }}</p>
                
                <div class="row text-center mt-3">
                    <div class="col-4">
                        <h6 class="text-primary">{{ $stats['total_products'] }}</h6>
                        <small class="text-muted">{{ __('messages.products') }}</small>
                    </div>
                    <div class="col-4">
                        <h6 class="text-success">{{ $stats['active_users'] }}</h6>
                        <small class="text-muted">{{ __('messages.customers') }}</small>
                    </div>
                    <div class="col-4">
                        <h6 class="text-warning">{{ $stats['total_sold_items'] }}</h6>
                        <small class="text-muted">{{ __('messages.sold') }}</small>
                    </div>
                </div>
                
                <a href="{{ route('provider.profile') }}" class="btn btn-primary btn-sm mt-3">
                    <i class="fas fa-edit me-1"></i>{{ __('messages.edit_profile') }}
                </a>
            </div>
        </div>
        
        <!-- Performance Overview -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-chart-pie me-2"></i>{{ __('messages.performance_overview') }}
                </h5>
            </div>
            <div class="card-body">
                <div class="performance-item mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span>{{ __('messages.products_in_user_warehouses') }}</span>
                        <span class="text-primary fw-bold">
                            @php
                                $totalInWarehouses = 0;
                                foreach($topProducts as $product) {
                                    $totalInWarehouses += ($product->order_products_sum_quantity ?? 0) - ($product->voucher_products_sum_quantity ?? 0);
                                }
                            @endphp
                            {{ $totalInWarehouses }}
                        </span>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-primary" style="width: 70%"></div>
                    </div>
                </div>
                
                <div class="performance-item mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span>{{ __('messages.sales_conversion_rate') }}</span>
                        <span class="text-success fw-bold">
                            @php
                                $totalOrdered = $stats['total_sold_items'] + $totalInWarehouses;
                                $conversionRate = $totalOrdered > 0 ? ($stats['total_sold_items'] / $totalOrdered) * 100 : 0;
                            @endphp
                            {{ number_format($conversionRate, 1) }}%
                        </span>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-success" style="width: {{ $conversionRate }}%"></div>
                    </div>
                </div>
                
                <div class="performance-item">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span>{{ __('messages.account_status') }}</span>
                        <span class="badge bg-success">{{ __('messages.active') }}</span>
                    </div>
                    <small class="text-muted">{{ __('messages.member_since') }}: {{ auth('provider')->user()->created_at->format('M Y') }}</small>
                </div>
            </div>
        </div>
        
        <!-- Quick Insights -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-lightbulb me-2"></i>{{ __('messages.quick_insights') }}
                </h5>
            </div>
            <div class="card-body">
                <div class="insight-item mb-3">
                    <div class="d-flex align-items-center">
                        <div class="insight-icon bg-success text-white rounded-circle me-3" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-trending-up small"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">{{ __('messages.best_performing_product') }}</h6>
                            <small class="text-muted">
                                @if($topProducts->count() > 0)
                                    {{ $topProducts->first()->name_ar }}
                                @else
                                    {{ __('messages.no_data_yet') }}
                                @endif
                            </small>
                        </div>
                    </div>
                </div>
                
                <div class="insight-item mb-3">
                    <div class="d-flex align-items-center">
                        <div class="insight-icon bg-info text-white rounded-circle me-3" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-users small"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">{{ __('messages.most_active_customer') }}</h6>
                            <small class="text-muted">
                                @if($recentOrders->count() > 0)
                                    {{ $recentOrders->first()->user->name }}
                                @else
                                    {{ __('messages.no_customers_yet') }}
                                @endif
                            </small>
                        </div>
                    </div>
                </div>
                
                <div class="insight-item">
                    <div class="d-flex align-items-center">
                        <div class="insight-icon bg-warning text-white rounded-circle me-3" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-chart-line small"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">{{ __('messages.growth_opportunity') }}</h6>
                            <small class="text-muted">
                                @if($stats['total_products'] < 5)
                                    {{ __('messages.add_more_products') }}
                                @elseif($stats['active_users'] < 10)
                                    {{ __('messages.expand_customer_base') }}
                                @else
                                    {{ __('messages.optimize_pricing') }}
                                @endif
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Order Details Modal -->
<div class="modal fade" id="orderDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('messages.order_details') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="orderDetailsContent">
                <div class="text-center">
                    <i class="fas fa-spinner fa-spin"></i> {{ __('messages.loading') }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sale Details Modal -->
<div class="modal fade" id="saleDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('messages.sale_details') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="saleDetailsContent">
                <div class="text-center">
                    <i class="fas fa-spinner fa-spin"></i> {{ __('messages.loading') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function showOrderDetails(orderId) {
    const modal = new bootstrap.Modal(document.getElementById('orderDetailsModal'));
    modal.show();
    
    // In a real application, you would make an AJAX call here
    setTimeout(() => {
        document.getElementById('orderDetailsContent').innerHTML = `
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                {{ __('messages.order_details_would_be_loaded_here') }}
            </div>
            <p>{{ __('messages.order_id') }}: ${orderId}</p>
        `;
    }, 1000);
}

function showSaleDetails(saleId) {
    const modal = new bootstrap.Modal(document.getElementById('saleDetailsModal'));
    modal.show();
    
    // In a real application, you would make an AJAX call here
    setTimeout(() => {
        document.getElementById('saleDetailsContent').innerHTML = `
            <div class="alert alert-success">
                <i class="fas fa-cash-register me-2"></i>
                {{ __('messages.sale_details_would_be_loaded_here') }}
            </div>
            <p>{{ __('messages.sale_id') }}: ${saleId}</p>
        `;
    }, 1000);
}

// Auto-refresh dashboard data every 60 seconds
setInterval(function() {
    // You can add AJAX calls here to refresh specific sections
    console.log('Dashboard refresh - {{ now() }}');
}, 60000);
</script>
@endpush