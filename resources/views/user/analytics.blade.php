@extends('layouts.user')

@section('title', __('messages.analytics'))
@section('page-title', __('messages.analytics'))

@section('content')
<div class="page-header">
    <h1 class="page-title">{{ __('messages.shopping_analytics') }}</h1>
    <p class="page-subtitle">{{ __('messages.analyze_your_shopping_patterns') }}</p>
</div>

<!-- Quick Stats -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon primary">
            <i class="fas fa-shopping-bag"></i>
        </div>
        <div class="stat-content">
            <h3>{{ $topProducts->sum('total_quantity') }}</h3>
            <p>{{ __('messages.total_items_purchased') }}</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon success">
            <i class="fas fa-dollar-sign"></i>
        </div>
        <div class="stat-content">
            <h3><x-riyal-icon /> {{ number_format($topProducts->sum('total_spent'), 2) }}</h3>
            <p>{{ __('messages.total_amount_spent') }}</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon warning">
            <i class="fas fa-chart-line"></i>
        </div>
        <div class="stat-content">
            <h3><x-riyal-icon /> {{ number_format($topProducts->avg('total_spent'), 2) }}</h3>
            <p>{{ __('messages.average_order_value') }}</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon info">
            <i class="fas fa-percentage"></i>
        </div>
        <div class="stat-content">
            <h3>{{ number_format($paymentStats['fully_paid'] / max(array_sum($paymentStats), 1) * 100, 1) }}%</h3>
            <p>{{ __('messages.payment_completion_rate') }}</p>
        </div>
    </div>
</div>

<div class="row">
    <!-- Monthly Spending Chart -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-chart-area me-2"></i>{{ __('messages.monthly_spending_trend') }}
                </h5>
            </div>
            <div class="card-body">
                <canvas id="monthlySpendingChart" height="300"></canvas>
            </div>
        </div>
        
        <!-- Category Spending -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-chart-pie me-2"></i>{{ __('messages.spending_by_category') }}
                </h5>
            </div>
            <div class="card-body">
                @if($categorySpending->count() > 0)
                    <canvas id="categoryChart" height="300"></canvas>
                @else
                    <div class="alert alert-info" role="alert">
                        <i class="fas fa-info-circle me-2"></i>
                        {{ __('messages.no_data_available') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Top Products & Payment Stats -->
    <div class="col-lg-4">
        <!-- Payment Status -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-credit-card me-2"></i>{{ __('messages.payment_patterns') }}
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span>{{ __('messages.fully_paid') }}</span>
                        <span>{{ $paymentStats['fully_paid'] }}</span>
                    </div>
                    <div class="progress mb-2">
                        <div class="progress-bar bg-success" style="width: {{ array_sum($paymentStats) > 0 ? ($paymentStats['fully_paid'] / array_sum($paymentStats)) * 100 : 0 }}%"></div>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span>{{ __('messages.partially_paid') }}</span>
                        <span>{{ $paymentStats['partially_paid'] }}</span>
                    </div>
                    <div class="progress mb-2">
                        <div class="progress-bar bg-warning" style="width: {{ array_sum($paymentStats) > 0 ? ($paymentStats['partially_paid'] / array_sum($paymentStats)) * 100 : 0 }}%"></div>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span>{{ __('messages.unpaid') }}</span>
                        <span>{{ $paymentStats['unpaid'] }}</span>
                    </div>
                    <div class="progress mb-2">
                        <div class="progress-bar bg-danger" style="width: {{ array_sum($paymentStats) > 0 ? ($paymentStats['unpaid'] / array_sum($paymentStats)) * 100 : 0 }}%"></div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Top Products -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-star me-2"></i>{{ __('messages.most_purchased_products') }}
                </h5>
            </div>
            <div class="card-body">
                @if($topProducts->count() > 0)
                    @foreach($topProducts->take(5) as $product)
                        <div class="d-flex align-items-center mb-3">
                            <div class="product-image-small me-3">
                                @if($product->photo)
                                    <img src="{{ asset('storage/' . $product->photo) }}" alt="{{ $product->name_ar }}" class="rounded" width="40" height="40">
                                @else
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="fas fa-box text-muted"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">{{ $product->name_ar }}</h6>
                                <small class="text-muted">
                                    {{ $product->total_quantity }} {{ __('messages.items') }} • 
                                    <x-riyal-icon /> {{ number_format($product->total_spent, 2) }}
                                </small>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-3">
                        <i class="fas fa-box text-muted" style="font-size: 2rem;"></i>
                        <p class="text-muted mt-2">{{ __('messages.no_products_purchased') }}</p>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Shopping Insights -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-lightbulb me-2"></i>{{ __('messages.shopping_insights') }}
                </h5>
            </div>
            <div class="card-body">
                <div class="insight-item mb-3">
                    <div class="d-flex align-items-center">
                        <div class="insight-icon bg-primary text-white rounded-circle me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-trophy"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">{{ __('messages.favorite_category') }}</h6>
                            <small class="text-muted">
                                @if($categorySpending->count() > 0)
                                    {{ $categorySpending->first()->name_ar }}
                                @else
                                    {{ __('messages.no_data') }}
                                @endif
                            </small>
                        </div>
                    </div>
                </div>
                
                <div class="insight-item mb-3">
                    <div class="d-flex align-items-center">
                        <div class="insight-icon bg-success text-white rounded-circle me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">{{ __('messages.shopping_frequency') }}</h6>
                            <small class="text-muted">
                                @if($monthlySpending->count() > 0)
                                    {{ number_format($monthlySpending->count() / 12, 1) }} {{ __('messages.orders_per_month') }}
                                @else
                                    {{ __('messages.no_data') }}
                                @endif
                            </small>
                        </div>
                    </div>
                </div>
                
                <div class="insight-item">
                    <div class="d-flex align-items-center">
                        <div class="insight-icon bg-warning text-white rounded-circle me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-coins"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">{{ __('messages.savings_potential') }}</h6>
                            <small class="text-muted">
                                @php
                                    $avgSpending = $monthlySpending->avg('total');
                                    $lastMonthSpending = $monthlySpending->first()->total ?? 0;
                                    $savings = $avgSpending > $lastMonthSpending ? $avgSpending - $lastMonthSpending : 0;
                                @endphp
                                @if($savings > 0)
                                    <x-riyal-icon /> {{ number_format($savings, 2) }} {{ __('messages.saved_last_month') }}
                                @else
                                    {{ __('messages.maintain_budget') }}
                                @endif
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Export & Report Actions -->
<div class="card">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h5 class="mb-1">{{ __('messages.generate_detailed_report') }}</h5>
                <p class="text-muted mb-0">{{ __('messages.export_comprehensive_analytics') }}</p>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('user.report') }}" class="btn btn-primary">
                    <i class="fas fa-file-export me-1"></i>{{ __('messages.generate_report') }}
                </a>
                <button class="btn btn-outline-success" onclick="exportToCSV()">
                    <i class="fas fa-file-csv me-1"></i>{{ __('messages.export_csv') }}
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script>
// Monthly Spending Chart
const monthlyCtx = document.getElementById('monthlySpendingChart').getContext('2d');
const monthlyChart = new Chart(monthlyCtx, {
    type: 'line',
    data: {
        labels: [
            @foreach($monthlySpending->reverse() as $spending)
                '{{ Carbon\Carbon::create($spending->year, $spending->month)->format("M Y") }}',
            @endforeach
        ],
        datasets: [{
            label: '{{ __("messages.monthly_spending") }}',
            data: [
                @foreach($monthlySpending->reverse() as $spending)
                    {{ $spending->total }},
                @endforeach
            ],
            borderColor: 'rgb(79, 70, 229)',
            backgroundColor: 'rgba(79, 70, 229, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '{{ __("messages.riyal") }} ' + value.toLocaleString();
                    }
                }
            }
        }
    }
});

// Category Spending Chart
const categoryChartElement = document.getElementById('categoryChart');
if (categoryChartElement) {
    const categoryCtx = categoryChartElement.getContext('2d');
    const categoryChart = new Chart(categoryCtx, {
        type: 'doughnut',
        data: {
            labels: [
                @foreach($categorySpending as $category)
                    '{{ $category->name_ar }}',
                @endforeach
            ],
            datasets: [{
                data: [
                    @foreach($categorySpending as $category)
                        {{ $category->total_spent }},
                    @endforeach
                ],
                backgroundColor: [
                    '#4f46e5',
                    '#06b6d4',
                    '#10b981',
                    '#f59e0b',
                    '#ef4444',
                    '#8b5cf6',
                    '#ec4899',
                    '#6b7280'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true
                    }
                }
            }
        }
    });
}

// Export to CSV function
function exportToCSV() {
    // Create CSV content
    let csvContent = "data:text/csv;charset=utf-8,";
    csvContent += "{{ __('messages.analytics_export') }}\n\n";
    
    // Monthly spending data
    csvContent += "{{ __('messages.monthly_spending') }}\n";
    csvContent += "{{ __('messages.month') }},{{ __('messages.amount') }}\n";
    @foreach($monthlySpending as $spending)
        csvContent += "{{ Carbon\Carbon::create($spending->year, $spending->month)->format('M Y') }},{{ $spending->total }}\n";
    @endforeach
    
    csvContent += "\n{{ __('messages.top_products') }}\n";
    csvContent += "{{ __('messages.product') }},{{ __('messages.quantity') }},{{ __('messages.total_spent') }}\n";
    @foreach($topProducts as $product)
        csvContent += "{{ $product->name_ar }},{{ $product->total_quantity }},{{ $product->total_spent }}\n";
    @endforeach
    
    // Create download link
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", "shopping_analytics_{{ auth()->user()->name }}_{{ date('Y-m-d') }}.csv");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
</script>
@endpush