@extends('layouts.user')

@section('title', __('messages.dashboard'))
@section('page-title', __('messages.dashboard'))

@section('content')
<div class="page-header">
    <h1 class="page-title">{{ __('messages.welcome_back') }}, {{ auth()->user()->name }}!</h1>
    <p class="page-subtitle">{{ __('messages.user_dashboard_subtitle') }}</p>
</div>

<!-- Stats Grid -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon primary">
            <i class="fas fa-shopping-cart"></i>
        </div>
        <div class="stat-content">
            <h3>{{ $stats['total_orders'] }}</h3>
            <p>{{ __('messages.total_orders') }}</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon warning">
            <i class="fas fa-clock"></i>
        </div>
        <div class="stat-content">
            <h3>{{ $stats['pending_orders'] }}</h3>
            <p>{{ __('messages.pending_orders') }}</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon success">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-content">
            <h3>{{ $stats['completed_orders'] }}</h3>
            <p>{{ __('messages.completed_orders') }}</p>
        </div>
    </div>
    
</div>

<!-- Quick Actions -->
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-bolt me-2"></i>{{ __('messages.quick_actions') }}
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <a href="{{ route('user.orders') }}" class="btn btn-outline-primary w-100 py-3">
                            <i class="fas fa-list-alt d-block mb-2" style="font-size: 1.5rem;"></i>
                            {{ __('messages.view_orders') }}
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="{{ route('user.profile') }}" class="btn btn-outline-info w-100 py-3">
                            <i class="fas fa-user-edit d-block mb-2" style="font-size: 1.5rem;"></i>
                            {{ __('messages.edit_profile') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Orders -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-history me-2"></i>{{ __('messages.recent_orders') }}
                </h5>
                <a href="{{ route('user.orders') }}" class="btn btn-sm btn-outline-primary">
                    {{ __('messages.view_all') }}
                </a>
            </div>
            <div class="card-body">
                @if(auth()->user()->orders && auth()->user()->orders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{ __('messages.order_number') }}</th>
                                    <th>{{ __('messages.date') }}</th>
                                    <th>{{ __('messages.total') }}</th>
                                    <th>{{ __('messages.status') }}</th>
                                    <th>{{ __('messages.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(auth()->user()->orders()->latest()->take(5)->get() as $order)
                                    <tr>
                                        <td>{{ $order->number }}</td>
                                        <td>{{ Carbon\Carbon::parse($order->date)->format('Y-m-d') }}</td>
                                        <td><x-riyal-icon /> {{ number_format($order->total_prices, 2) }}</td>
                                        <td>
                                            {!! \App\Enums\OrderStatus::tryFrom($order->status)?->getBadgeHtml() ?? '<span class="badge bg-secondary">N/A</span>' !!}
                                        </td>
                                        <td>
                                            <a href="#" class="btn btn-sm btn-outline-primary">
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
                        <h6 class="mt-3 text-muted">{{ __('messages.no_orders_yet') }}</h6>
                        <p class="text-muted">{{ __('messages.start_shopping_message') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <!-- Profile Summary -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-user me-2"></i>{{ __('messages.profile_summary') }}
                </h5>
            </div>
            <div class="card-body text-center">
                <img src="{{ auth()->user()->photo_url }}" alt="Profile" class="rounded-circle mb-3" width="80" height="80">
                <h6>{{ auth()->user()->name }}</h6>
                <p class="text-muted small">{{ auth()->user()->email }}</p>
                <p class="text-muted small">{{ auth()->user()->phone }}</p>
                <a href="{{ route('user.profile') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-edit me-1"></i>{{ __('messages.edit_profile') }}
                </a>
            </div>
        </div>
        
        <!-- Account Status -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>{{ __('messages.account_status') }}
                </h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span>{{ __('messages.account_status') }}</span>
                    <span class="badge bg-success">{{ __('messages.active') }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span>{{ __('messages.member_since') }}</span>
                    <span class="text-muted">{{ auth()->user()->created_at->format('Y-m-d') }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span>{{ __('messages.total_spent') }}</span>
                    <span class="fw-bold"><x-riyal-icon /> {{ number_format(auth()->user()->orders()->sum('total_prices') ?? 0, 2) }}</span>
                </div>
            </div>
        </div>
        
        <!-- Quick Stats -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-chart-pie me-2"></i>{{ __('messages.quick_stats') }}
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-12">
                        <h4 class="text-primary">{{ auth()->user()->orders()->where('payment_status', 1)->count() }}</h4>
                        <small class="text-muted">{{ __('messages.paid_orders') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Add any dashboard-specific JavaScript here
    console.log('User Dashboard Loaded');
    
    // Auto-refresh stats every 30 seconds (optional)
    // setInterval(function() {
    //     // Refresh dashboard stats via AJAX
    // }, 30000);
</script>
@endpush