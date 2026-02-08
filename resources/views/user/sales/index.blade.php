@extends('layouts.user')

@section('title', __('messages.my_sales'))
@section('page-title', __('messages.my_sales'))

@section('content')
<div class="page-header">
    <h1 class="page-title">{{ __('messages.my_sales') }}</h1>
    <p class="page-subtitle">{{ __('messages.track_your_sales_and_inventory') }}</p>
</div>

<!-- Sales Statistics -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon primary">
            <i class="fas fa-cash-register"></i>
        </div>
        <div class="stat-content">
            <h3>{{ $stats['total_sales'] }}</h3>
            <p>{{ __('messages.total_sales') }}</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon success">
            <i class="fas fa-box"></i>
        </div>
        <div class="stat-content">
            <h3>{{ $stats['total_items_sold'] }}</h3>
            <p>{{ __('messages.total_items_sold') }}</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon warning">
            <i class="fas fa-calendar-alt"></i>
        </div>
        <div class="stat-content">
            <h3>{{ $stats['this_month_sales'] }}</h3>
            <p>{{ __('messages.this_month_sales') }}</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon info">
            <i class="fas fa-warehouse"></i>
        </div>
        <div class="stat-content">
            <h3>{{ $stats['current_inventory'] }}</h3>
            <p>{{ __('messages.current_inventory') }}</p>
        </div>
    </div>
</div>

<!-- Actions and Filters -->
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('user.sales.index') }}" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">{{ __('messages.date_from') }}</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">{{ __('messages.date_to') }}</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label">{{ __('messages.search') }}</label>
                        <input type="text" name="search" class="form-control" placeholder="{{ __('messages.search_by_voucher_number') }}" value="{{ request('search') }}">
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <a href="{{ route('user.sales.create') }}" class="btn btn-success btn-lg w-100">
                    <i class="fas fa-plus me-2"></i>{{ __('messages.record_new_sale') }}
                </a>
              
            </div>
        </div>
    </div>
</div>

<!-- Sales List -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-list me-2"></i>{{ __('messages.sales_history') }}
            <span class="badge bg-primary ms-2">{{ $sales->total() }}</span>
        </h5>
    </div>
    <div class="card-body">
        @if($sales->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>{{ __('messages.voucher_number') }}</th>
                            <th>{{ __('messages.date') }}</th>
                            <th>{{ __('messages.customer_info') }}</th>
                            <th>{{ __('messages.products_count') }}</th>
                            <th>{{ __('messages.total_quantity') }}</th>
                            <th>{{ __('messages.notes') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sales as $sale)
                            <tr>
                                <td>
                                    <strong>#{{ $sale->number }}</strong>
                                </td>
                                <td>{{ Carbon\Carbon::parse($sale->date_note_voucher)->format('Y-m-d') }}</td>
                                <td>
                                    @php
                                        $customerInfo = '';
                                        if (strpos($sale->note, 'Customer:') !== false) {
                                            $parts = explode('|', $sale->note);
                                            foreach ($parts as $part) {
                                                if (strpos($part, 'Customer:') !== false) {
                                                    $customerInfo = trim(str_replace('Customer:', '', $part));
                                                    break;
                                                }
                                            }
                                        }
                                    @endphp
                                    @if($customerInfo)
                                        <strong>{{ $customerInfo }}</strong>
                                        @if(strpos($sale->note, 'Phone:') !== false)
                                            @php
                                                $phone = '';
                                                foreach (explode('|', $sale->note) as $part) {
                                                    if (strpos($part, 'Phone:') !== false) {
                                                        $phone = trim(str_replace('Phone:', '', $part));
                                                        break;
                                                    }
                                                }
                                            @endphp
                                            <br><small class="text-muted">{{ $phone }}</small>
                                        @endif
                                    @else
                                        <span class="text-muted">{{ __('messages.customer_not_specified') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $sale->voucherProducts->count() }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-success">{{ $sale->voucherProducts->sum('quantity') }}</span>
                                </td>
                                <td>
                                    @if($sale->note)
                                        @php
                                            $cleanNote = $sale->note;
                                            $cleanNote = preg_replace('/Customer:.*?\|/', '', $cleanNote);
                                            $cleanNote = preg_replace('/Phone:.*?\|/', '', $cleanNote);
                                            $cleanNote = trim($cleanNote, '| ');
                                        @endphp
                                        @if($cleanNote)
                                            <small class="text-muted">{{ Str::limit($cleanNote, 30) }}</small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                               
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $sales->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-cash-register text-muted" style="font-size: 4rem;"></i>
                <h4 class="mt-3 text-muted">{{ __('messages.no_sales_recorded') }}</h4>
                <p class="text-muted">{{ __('messages.start_recording_sales_message') }}</p>
                <a href="{{ route('user.sales.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>{{ __('messages.record_first_sale') }}
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Quick Stats Summary -->
@if($sales->count() > 0)
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h4>{{ $sales->sum(function($sale) { return $sale->voucherProducts->count(); }) }}</h4>
                    <p class="mb-0">{{ __('messages.total_product_types') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h4>{{ $sales->sum(function($sale) { return $sale->voucherProducts->sum('quantity'); }) }}</h4>
                    <p class="mb-0">{{ __('messages.total_items_in_period') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h4>{{ $sales->count() }}</h4>
                    <p class="mb-0">{{ __('messages.total_transactions') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h4>{{ number_format($sales->sum(function($sale) { return $sale->voucherProducts->sum(function($vp) { return $vp->quantity * $vp->purchasing_price; }); }), 2) }}</h4>
                    <p class="mb-0">{{ __('messages.total_sales_value') }}</p>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Quick View Modal -->
<div class="modal fade" id="quickViewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('messages.sale_details') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="quickViewContent">
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
function showSaleDetails(saleId) {
    const modal = new bootstrap.Modal(document.getElementById('quickViewModal'));
    modal.show();
    
    // Simulate loading sale details (you would make an AJAX call here)
    setTimeout(() => {
        document.getElementById('quickViewContent').innerHTML = `
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                {{ __('messages.sale_details_would_load_here') }}
            </div>
            <p>{{ __('messages.click_view_details_for_full_info') }}</p>
        `;
    }, 1000);
}

// Auto-hide alerts
setTimeout(function() {
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(alert => {
        const bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
    });
}, 5000);
</script>
@endpush