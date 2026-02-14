@extends('layouts.admin')

@section('title', __('messages.seller_sales_management'))
@section('page-title', __('messages.seller_sales_management'))

@section('content')
<div class="page-header">
    <h1 class="page-title">{{ __('messages.seller_sales_management') }}</h1>
    <p class="page-subtitle">{{ __('messages.manage_seller_sales') ?? 'Manage sales registered by sellers' }}</p>
</div>

<!-- Filters -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.seller-sales.index') }}" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">{{ __('messages.select_seller') }}</label>
                        <select name="seller_id" class="form-control">
                            <option value="">-- {{ __('messages.all_sellers') }} --</option>
                            @foreach($sellers as $seller)
                                <option value="{{ $seller->id }}" {{ request('seller_id') == $seller->id ? 'selected' : '' }}>
                                    {{ $seller->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">{{ __('messages.date_from') }}</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">{{ __('messages.date_to') }}</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">{{ __('messages.search_by_sale_number') }}</label>
                        <input type="text" name="search" class="form-control" placeholder="{{ __('messages.search') }}" value="{{ request('search') }}">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search"></i> {{ __('messages.filter') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Action Button -->
<div class="row mb-4">
    <div class="col-md-12 text-right">
        <a href="{{ route('admin.seller-sales.create') }}" class="btn btn-success btn-lg">
            <i class="fas fa-plus me-2"></i>{{ __('messages.register_seller_sale') }}
        </a>
    </div>
</div>

<!-- Sales List -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-list me-2"></i>{{ __('messages.seller_sales_list') }}
            <span class="badge bg-primary ms-2">{{ $sales->total() }}</span>
        </h5>
    </div>
    <div class="card-body">
        @if($sales->count() > 0)
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>{{ __('messages.sale_number') }}</th>
                            <th>{{ __('messages.seller_name') }}</th>
                            <th>{{ __('messages.date') }}</th>
                            <th>{{ __('messages.products_count') }}</th>
                            <th>{{ __('messages.total_quantity') }}</th>
                            <th>{{ __('messages.total_amount') }}</th>
                            <th>{{ __('messages.total_tax') }}</th>
                            <th>{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sales as $sale)
                            <tr>
                                <td>
                                    <strong>{{ $sale->sale_number }}</strong>
                                </td>
                                <td>
                                    {{ $sale->user->name ?? 'N/A' }}
                                </td>
                                <td>{{ \Carbon\Carbon::parse($sale->sale_date)->format('Y-m-d') }}</td>
                                <td>
                                    <span class="badge bg-info">{{ $sale->items->count() }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-success">{{ $sale->items->sum('quantity') }}</span>
                                </td>
                                <td>
                                    <strong><x-riyal-icon /> {{ number_format($sale->total_amount, 2) }}</strong>
                                </td>
                                <td>
                                    <span class="badge bg-warning"><x-riyal-icon /> {{ number_format($sale->total_tax, 2) }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.seller-sales.show', $sale->id) }}" class="btn btn-sm btn-primary" title="{{ __('messages.view_details') }}">
                                        <i class="fas fa-eye"></i>
                                    </a>
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
                <h4 class="mt-3 text-muted">{{ __('messages.no_sales_found') }}</h4>
                <p class="text-muted">{{ __('messages.no_sales_recorded_yet') ?? 'No seller sales have been recorded yet' }}</p>
                <a href="{{ route('admin.seller-sales.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>{{ __('messages.register_seller_sale') }}
                </a>
            </div>
        @endif
    </div>
</div>

@endsection

@push('scripts')
<script>
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
