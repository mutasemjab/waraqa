@extends("layouts.admin")

@section("title", __("messages.seller_sales"))
@section("page-title", __("messages.seller_sales"))

@section("content")
<div class="page-header mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="page-title">{{ __("messages.seller_sales") }}</h1>
        <a href="{{ route("admin.seller-sales.create") }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>{{ __("messages.register_seller_sale") }}
        </a>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route("admin.seller-sales.index") }}" class="row g-3">
            <div class="col-md-2">
                <label class="form-label">{{ __("messages.filter_by_status") }}</label>
                <select name="status" class="form-control form-select w-100">
                    <option value="">{{ __("messages.all") }}</option>
                    <option value="1" {{ request("status") == "1" ? "selected" : "" }}>{{ __("messages.pending") }}</option>
                    <option value="2" {{ request("status") == "2" ? "selected" : "" }}>{{ __("messages.approved") }}</option>
                    <option value="3" {{ request("status") == "3" ? "selected" : "" }}>{{ __("messages.rejected") }}</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">{{ __("messages.seller") }}</label>
                <select name="seller_id" class="form-control form-select w-100">
                    <option value="">{{ __("messages.all") }}</option>
                    @foreach($sellers as $seller)
                        <option value="{{ $seller->id }}" {{ request("seller_id") == $seller->id ? "selected" : "" }}>{{ $seller->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">{{ __("messages.date") }} {{ __("messages.from") }}</label>
                <input type="date" name="date_from" class="form-control" value="{{ request("date_from") }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">{{ __("messages.date") }} {{ __("messages.to") }}</label>
                <input type="date" name="date_to" class="form-control" value="{{ request("date_to") }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">{{ __("messages.search") }}</label>
                <input type="text" name="search" class="form-control" placeholder="{{ __("messages.sale_number") }}" value="{{ request("search") }}">
            </div>
            <div class="col-md-2 align-self-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-filter"></i> {{ __("messages.filter") }}
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Sales Table -->
<div class="card">
    <div class="table-responsive">
        <table class="table table-striped mb-0">
            <thead class="table-light">
                <tr>
                    <th>{{ __("messages.sale_number") }}</th>
                    <th>{{ __("messages.seller") }}</th>
                    <th>{{ __("messages.sale_date") }}</th>
                    <th>{{ __("messages.total_amount") }}</th>
                    <th>{{ __("messages.status") }}</th>
                    <th>{{ __("messages.actions") }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sales as $sale)
                    <tr>
                        <td class="fw-bold">{{ $sale->sale_number }}</td>
                        <td>{{ $sale->user->name }}</td>
                        <td>{{ Carbon\Carbon::parse($sale->sale_date)->format("Y-m-d") }}</td>
                        <td class="fw-bold">{{ number_format($sale->total_amount, 2) }}</td>
                        <td>
                            <span class="badge bg-{{ $sale->status->getColor() }}">
                                {{ $sale->status->getLabel() }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route("admin.seller-sales.show", $sale->id) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i> {{ __("messages.view") }}
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            {{ __("messages.no_sales_found") }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
<div class="d-flex justify-content-center mt-4">
    {{ $sales->appends(request()->query())->links() }}
</div>
@endsection
