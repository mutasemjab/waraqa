@extends('layouts.admin')

@section('title')
{{ __('messages.providers_report') }}
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-12">
            <h1 class="h3 mb-4 text-gray-800">{{ __('messages.providers_report') }}</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('messages.filter_providers_report') }}</h6>
                </div>
                <form id="providersReportForm" method="GET" action="{{ route('admin.reports.providers.index') }}">
                    <div class="card-body">
                        <!-- Provider Field -->
                        <div class="form-group">
                            <label for="provider_id">{{ __('messages.provider') }} <span class="text-danger">*</span></label>
                            <select id="provider_id" name="provider_id" class="form-control" required>
                                <option value="">{{ __('messages.select_provider') }}</option>
                                @foreach(\App\Models\Provider::with('user')->get() as $provider)
                                    <option value="{{ $provider->id }}">{{ $provider->name }}</option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted d-block mt-2">
                                <i class="fas fa-info-circle"></i> {{ __('messages.select_provider_first') }}
                            </small>
                        </div>

                        <!-- Products Field -->
                        <div class="form-group">
                            <label for="product_id">{{ __('messages.product') }}</label>
                            <select id="product_id" name="product_id" class="form-control">
                                <option value="">{{ __('messages.select_product') }}</option>
                                <option value="all">{{ __('messages.all_products') }}</option>
                            </select>
                            <small class="form-text text-muted d-block mt-2">
                                <i class="fas fa-info-circle"></i> اختر المورد أولاً لتحميل المنتجات
                            </small>
                        </div>

                        <!-- Date Range -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="from_date">{{ __('messages.from_date') }}</label>
                                    <input type="date" id="from_date" name="from_date" class="form-control">
                                    <small class="form-text text-muted">{{ __('messages.leave_empty_all_data') }}</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="to_date">{{ __('messages.to_date') }}</label>
                                    <input type="date" id="to_date" name="to_date" class="form-control">
                                    <small class="form-text text-muted">{{ __('messages.leave_empty_all_data') }}</small>
                                </div>
                            </div>
                        </div>

                        <!-- Error Message -->
                        <div id="errorMessage" class="alert alert-danger d-none"></div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter"></i> {{ __('messages.apply') }}
                        </button>
                        <button type="reset" class="btn btn-secondary">
                            <i class="fas fa-redo"></i> {{ __('messages.reset') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Results Section -->
    @if($provider)
    <div class="row mt-4">
        <div class="col-md-12">
            <!-- Provider Info Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">{{ __('messages.provider_information') }}</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>{{ __('messages.provider_name') }}:</strong> {{ $provider->name }}</p>
                            <p><strong>{{ __('messages.email') }}:</strong> {{ $provider->email ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>{{ __('messages.phone') }}:</strong> {{ $provider->phone ?? '-' }}</p>
                            <p><strong>{{ __('messages.country') }}:</strong> {{ $provider->user?->country?->name ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary Stats -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card shadow">
                        <div class="card-body">
                            <div class="text-primary font-weight-bold text-uppercase mb-1">{{ __('messages.total_products') }}</div>
                            <div class="h3 mb-0">{{ $totalItems }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card shadow">
                        <div class="card-body">
                            <div class="text-success font-weight-bold text-uppercase mb-1">{{ __('messages.total_revenue') }}</div>
                            <div class="h3 mb-0">{{ number_format($grandTotal, 2) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card shadow">
                        <div class="card-body">
                            <div class="text-info font-weight-bold text-uppercase mb-1">{{ __('messages.total_sales') }}</div>
                            <div class="h3 mb-0">{{ collect($reportData)->sum('total_quantity') }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card shadow">
                        <div class="card-body">
                            <div class="text-warning font-weight-bold text-uppercase mb-1">{{ __('messages.average_price') }}</div>
                            <div class="h3 mb-0">{{ number_format($grandTotal / max(collect($reportData)->sum('total_quantity'), 1), 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Date Filter Info -->
            @if($fromDate && $toDate)
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="alert alert-info">
                        <i class="fas fa-calendar-alt"></i>
                        {{ __('messages.report_period') }}: <strong>{{ $fromDate }}</strong> {{ __('messages.to') }} <strong>{{ $toDate }}</strong>
                    </div>
                </div>
            </div>
            @else
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="alert alert-info">
                        <i class="fas fa-calendar-alt"></i>
                        {{ __('messages.showing_all_data') }}
                    </div>
                </div>
            </div>
            @endif

            <!-- Products Table -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 bg-primary text-white">
                            <h6 class="m-0 font-weight-bold">{{ __('messages.products_sales_details') }}</h6>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('messages.product_name') }}</th>
                                        <th>{{ __('messages.sku') }}</th>
                                        <th>{{ __('messages.unit_price') }}</th>
                                        <th>{{ __('messages.total_quantity') }}</th>
                                        <th>{{ __('messages.total_revenue') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($reportData as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td><strong>{{ $item['product']->name }}</strong></td>
                                        <td>{{ $item['product']->sku ?? '-' }}</td>
                                        <td>{{ number_format($item['product']->selling_price, 2) }}</td>
                                        <td><span class="badge badge-info">{{ $item['total_quantity'] }}</span></td>
                                        <td><span class="badge badge-success">{{ number_format($item['total_revenue'], 2) }}</span></td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox"></i> {{ __('messages.no_data_available') }}
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                                <tfoot class="bg-light font-weight-bold">
                                    <tr>
                                        <td colspan="4" class="text-right">{{ __('messages.total') }}:</td>
                                        <td><span class="badge badge-info">{{ collect($reportData)->sum('total_quantity') }}</span></td>
                                        <td><span class="badge badge-success">{{ number_format($grandTotal, 2) }}</span></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Orders Details -->
            @if($reportData && count($reportData) > 0)
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 bg-primary text-white">
                            <h6 class="m-0 font-weight-bold">{{ __('messages.orders_details') }}</h6>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>{{ __('messages.order_id') }}</th>
                                        <th>{{ __('messages.product_name') }}</th>
                                        <th>{{ __('messages.quantity') }}</th>
                                        <th>{{ __('messages.unit_price') }}</th>
                                        <th>{{ __('messages.total_price') }}</th>
                                        <th>{{ __('messages.order_date') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reportData as $item)
                                        @foreach($item['order_products'] as $op)
                                        <tr>
                                            <td><a href="{{ route('orders.show', $op->order->id) }}" target="_blank">#{{ $op->order->id }}</a></td>
                                            <td>{{ $item['product']->name }}</td>
                                            <td><span class="badge badge-info">{{ $op->quantity }}</span></td>
                                            <td>{{ number_format($op->price, 2) }}</td>
                                            <td><strong>{{ number_format($op->quantity * $op->price, 2) }}</strong></td>
                                            <td>{{ $op->order->created_at->format('Y-m-d H:i') }}</td>
                                        </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Print Button -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <button type="button" class="btn btn-primary" onclick="window.print()">
                        <i class="fas fa-print"></i> {{ __('messages.print') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Handle Provider Selection Change
        $('#provider_id').on('change', function() {
            const providerId = $(this).val();
            const productSelect = $('#product_id');

            // Reset product select
            productSelect.html('<option value="">{{ __("messages.select_product") }}</option><option value="all">{{ __("messages.all_products") }}</option>');

            if (providerId) {
                // Fetch products via AJAX
                $.ajax({
                    url: '{{ route("admin.reports.providers.getProducts", ":id") }}'.replace(':id', providerId),
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success && response.products.length > 0) {
                            let options = '<option value="">{{ __("messages.select_product") }}</option>';
                            options += '<option value="all">{{ __("messages.all_products") }}</option>';

                            response.products.forEach(function(product) {
                                options += '<option value="' + product.id + '">' + product.name + '</option>';
                            });

                            productSelect.html(options);
                        }
                    },
                    error: function() {
                        $('#errorMessage').removeClass('d-none').text('خطأ في تحميل المنتجات');
                    }
                });
            } else {
                $('#errorMessage').addClass('d-none');
            }
        });

        // Handle Form Submission
        $('#providersReportForm').on('submit', function(e) {
            const providerId = $('#provider_id').val();

            if (!providerId) {
                e.preventDefault();
                $('#errorMessage').removeClass('d-none').text('يرجى اختيار المورد أولاً');
                return false;
            }

            $('#errorMessage').addClass('d-none');
        });
    });
</script>
@endpush
@endsection