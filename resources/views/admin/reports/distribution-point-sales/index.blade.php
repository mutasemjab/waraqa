@extends('layouts.admin')

@section('title', __('messages.distribution_point_sales_report'))

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-store me-2"></i>{{ __('messages.distribution_point_sales_report') }}
        </h1>
        <p class="page-subtitle">{{ __('messages.view_sales_by_distribution_point') }}</p>
    </div>

    <!-- Search Card -->
    <div class="card card-default mb-4">
        <div class="card-header">
            <h3 class="card-title">{{ __('messages.search') }} {{ __('messages.seller') }}</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Seller Select -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="seller_search">{{ __('messages.seller') }}</label>
                        <input type="hidden" id="seller_id" name="seller_id" value="">
                        <input type="text" id="seller_search" class="form-control" placeholder="{{ __('messages.search_by_name') }}" autocomplete="off">
                        <div id="sellers-dropdown" class="border rounded mt-1" style="display:none; position: absolute; width: calc(50% - 30px); max-height: 300px; overflow-y: auto; background: white; z-index: 1000;"></div>
                    </div>
                </div>

                <!-- Search Button -->
                <div class="col-md-6 d-flex align-items-end">
                    <div class="btn-group w-100">
                        <button type="button" class="btn btn-primary w-100" id="searchBtn">
                            <i class="fas fa-search me-2"></i>{{ __('messages.Search') }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Additional Filters -->
            <div class="row mt-3">
                <!-- Date From -->
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="from_date">{{ __('messages.date_from') }}</label>
                        <input type="date" id="from_date" class="form-control">
                    </div>
                </div>

                <!-- Date To -->
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="to_date">{{ __('messages.date_to') }}</label>
                        <input type="date" id="to_date" class="form-control">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Spinner -->
    <div id="loadingSpinner" class="text-center py-5" style="display: none;">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">{{ __('messages.loading') }}</span>
        </div>
        <p class="text-muted mt-2">{{ __('messages.loading') }}</p>
    </div>

    <!-- No Selection Message -->
    <div id="noSelectionMessage" class="alert alert-info text-center py-5">
        <i class="fas fa-info-circle me-2" style="font-size: 3rem;"></i>
        <h4 class="mt-3">{{ __('messages.select_seller') }}</h4>
        <p class="text-muted">{{ __('messages.select_seller') }}</p>
    </div>

    <!-- Results Container (Hidden by default) -->
    <div id="resultsContainer" style="display: none;">
        <!-- Warehouse Info Card -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5>
                            <strong>{{ __('messages.seller') }}:</strong>
                            <span id="warehouseName"></span>
                        </h5>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4" id="statisticsContainer">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5>
                            <i class="fas fa-receipt me-2"></i>{{ __('messages.total_sales') }}
                        </h5>
                        <h3 id="totalSalesCount">0</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5>
                            <i class="fas fa-coins me-2"></i>{{ __('messages.total_amount') }}
                        </h5>
                        <h3 id="totalAmount">0.00 <x-riyal-icon style="width: 16px; height: 16px;" /></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <h5>
                            <i class="fas fa-percent me-2"></i>{{ __('messages.total_tax') }}
                        </h5>
                        <h3 id="totalTax">0.00 <x-riyal-icon style="width: 16px; height: 16px;" /></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h5>
                            <i class="fas fa-boxes me-2"></i>{{ __('messages.total_quantity') }}
                        </h5>
                        <h3 id="totalQuantity">0</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sales Table -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>{{ __('messages.sales_list') }}
                </h5>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-hover" id="salesTable">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('messages.sale_number') }}</th>
                            <th>{{ __('messages.date') }}</th>
                            <th>{{ __('messages.customer_name') }}</th>
                            <th>{{ __('messages.customer_phone') }}</th>
                            <th>{{ __('messages.products_count') }}</th>
                            <th>{{ __('messages.total_quantity') }}</th>
                            <th>{{ __('messages.total_amount') }}</th>
                            <th>{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody id="salesTableBody">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Riyal icon SVG
    const riyalIcon = '<svg class="riyal-icon" style="width: 12px; height: 12px; display: inline-block; margin: 0 4px; vertical-align: middle;" viewBox="0 0 1124.14 1256.39" xmlns="http://www.w3.org/2000/svg"><path fill="currentColor" d="M699.62,1113.02h0c-20.06,44.48-33.32,92.75-38.4,143.37l424.51-90.24c20.06-44.47,33.31-92.75,38.4-143.37l-424.51,90.24Z"/><path fill="currentColor" d="M1085.73,895.8c20.06-44.47,33.32-92.75,38.4-143.37l-330.68,70.33v-135.2l292.27-62.11c20.06-44.47,33.32-92.75,38.4-143.37l-330.68,70.27V66.13c-50.67,28.45-95.67,66.32-132.25,110.99v403.35l-132.25,28.11V0c-50.67,28.44-95.67,66.32-132.25,110.99v525.69l-295.91,62.88c-20.06,44.47-33.33,92.75-38.42,143.37l334.33-71.05v170.26l-358.3,76.14c-20.06,44.47-33.32,92.75-38.4,143.37l375.04-79.7c30.53-6.35,56.77-24.4,73.83-49.24l68.78-101.97v-.02c7.14-10.55,11.3-23.27,11.3-36.97v-149.98l132.25-28.11v270.4l424.53-90.28Z"/></svg>';

    let sellerSearchTimer;
    const dropdown = $('#sellers-dropdown');
    const searchInput = $('#seller_search');
    const sellerIdInput = $('#seller_id');

    function performSellerSearch(term) {
        $.ajax({
            url: '{{ route("admin.reports.distributionPointSales.search") }}',
            method: 'GET',
            data: {
                term: term,
                limit: 5
            },
            success: function(data) {
                if (data.length > 0) {
                    let html = '';
                    data.forEach(function(seller) {
                        html += '<div class="p-2 border-bottom seller-item" data-id="' + seller.id + '" data-text="' + seller.text + '" style="cursor: pointer; transition: 0.3s;"><strong>' + seller.text + '</strong></div>';
                    });
                    dropdown.html(html).show();

                    // Add hover effect
                    $('.seller-item').hover(function() {
                        $(this).css('background-color', '#f5f5f5');
                    }, function() {
                        $(this).css('background-color', 'transparent');
                    });

                    // Add click handlers
                    $('.seller-item').on('click', function() {
                        const id = $(this).data('id');
                        const text = $(this).data('text');
                        sellerIdInput.val(id);
                        searchInput.val(text);
                        dropdown.hide();
                    });
                } else {
                    dropdown.html('<div class="p-2 text-muted">{{ __("messages.no_results") }}</div>').show();
                }
            },
            error: function(xhr) {
                console.error('Error searching sellers:', xhr);
                dropdown.html('<div class="p-2 text-danger">{{ __("messages.error") }}</div>').show();
            }
        });
    }

    // On focus - Show all sellers
    searchInput.on('focus', function() {
        const term = $(this).val().trim();
        if (term.length === 0) {
            performSellerSearch('');
        }
    });

    // On input - Debounced search (300ms delay)
    searchInput.on('input', function() {
        const term = $(this).val().trim();

        clearTimeout(sellerSearchTimer);
        sellerSearchTimer = setTimeout(() => {
            if (term.length >= 0) {
                performSellerSearch(term);
            }
        }, 300);
    });

    // On document click - Close dropdown
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#seller_search, #sellers-dropdown').length) {
            dropdown.hide();
        }
    });

    // Search button click
    $('#searchBtn').click(function () {
        const sellerId = $('#seller_id').val();

        if (!sellerId) {
            Swal.fire({
                icon: 'warning',
                title: '{{ __("messages.warning") }}',
                text: '{{ __("messages.please_select_seller") }}',
                confirmButtonText: '{{ __("messages.confirm") }}'
            });
            return;
        }

        loadSalesData();
    });

    // Load sales data
    function loadSalesData() {
        const sellerId = $('#seller_id').val();
        const dateFrom = $('#from_date').val();
        const dateTo = $('#to_date').val();

        $('#loadingSpinner').show();
        $('#resultsContainer').hide();

        $.ajax({
            url: '{{ route("admin.reports.distributionPointSales.data") }}',
            type: 'GET',
            data: {
                seller_id: sellerId,
                date_from: dateFrom,
                date_to: dateTo,
            },
            success: function (response) {
                if (response.success) {
                    displayResults(response);
                } else {
                    alert(response.message || '{{ __("messages.error_loading_data") }}');
                }
            },
            error: function () {
                alert('{{ __("messages.error_loading_data") }}');
            },
            complete: function () {
                $('#loadingSpinner').hide();
            }
        });
    }

    // Display results
    function displayResults(response) {
        const seller = response.seller;
        const stats = response.stats;
        const sales = response.sales;

        // Update seller info
        $('#warehouseName').text(seller.name);

        // Update statistics
        $('#totalSalesCount').text(stats.total_sales);
        $('#totalAmount').html(stats.total_amount + ' <svg class="riyal-icon" style="width: 16px; height: 16px; display: inline-block; margin: 0 4px; vertical-align: middle;" viewBox="0 0 1124.14 1256.39" xmlns="http://www.w3.org/2000/svg"><path fill="currentColor" d="M699.62,1113.02h0c-20.06,44.48-33.32,92.75-38.4,143.37l424.51-90.24c20.06-44.47,33.31-92.75,38.4-143.37l-424.51,90.24Z"/><path fill="currentColor" d="M1085.73,895.8c20.06-44.47,33.32-92.75,38.4-143.37l-330.68,70.33v-135.2l292.27-62.11c20.06-44.47,33.32-92.75,38.4-143.37l-330.68,70.27V66.13c-50.67,28.45-95.67,66.32-132.25,110.99v403.35l-132.25,28.11V0c-50.67,28.44-95.67,66.32-132.25,110.99v525.69l-295.91,62.88c-20.06,44.47-33.33,92.75-38.42,143.37l334.33-71.05v170.26l-358.3,76.14c-20.06,44.47-33.32,92.75-38.4,143.37l375.04-79.7c30.53-6.35,56.77-24.4,73.83-49.24l68.78-101.97v-.02c7.14-10.55,11.3-23.27,11.3-36.97v-149.98l132.25-28.11v270.4l424.53-90.28Z"/></svg>');
        $('#totalTax').html(stats.total_tax + ' <svg class="riyal-icon" style="width: 16px; height: 16px; display: inline-block; margin: 0 4px; vertical-align: middle;" viewBox="0 0 1124.14 1256.39" xmlns="http://www.w3.org/2000/svg"><path fill="currentColor" d="M699.62,1113.02h0c-20.06,44.48-33.32,92.75-38.4,143.37l424.51-90.24c20.06-44.47,33.31-92.75,38.4-143.37l-424.51,90.24Z"/><path fill="currentColor" d="M1085.73,895.8c20.06-44.47,33.32-92.75,38.4-143.37l-330.68,70.33v-135.2l292.27-62.11c20.06-44.47,33.32-92.75,38.4-143.37l-330.68,70.27V66.13c-50.67,28.45-95.67,66.32-132.25,110.99v403.35l-132.25,28.11V0c-50.67,28.44-95.67,66.32-132.25,110.99v525.69l-295.91,62.88c-20.06,44.47-33.33,92.75-38.42,143.37l334.33-71.05v170.26l-358.3,76.14c-20.06,44.47-33.32,92.75-38.4,143.37l375.04-79.7c30.53-6.35,56.77-24.4,73.83-49.24l68.78-101.97v-.02c7.14-10.55,11.3-23.27,11.3-36.97v-149.98l132.25-28.11v270.4l424.53-90.28Z"/></svg>');
        $('#totalQuantity').text(stats.total_quantity);

        // Clear table and populate with sales
        const tbody = $('#salesTableBody');
        tbody.empty();

        if (sales.length > 0) {
            sales.forEach(function (sale) {
                const detailsUrl = '{{ route("admin.sales.details", ":id") }}'.replace(':id', sale.id);
                const row = '<tr>'
                    + '<td><strong>' + sale.sale_number + '</strong></td>'
                    + '<td>' + sale.sale_date + '</td>'
                    + '<td>' + sale.customer_name + '</td>'
                    + '<td>' + (sale.customer_phone || '-') + '</td>'
                    + '<td><span class="badge bg-info">' + sale.products_count + '</span></td>'
                    + '<td><span class="badge bg-success">' + sale.total_quantity + '</span></td>'
                    + '<td><strong>' + sale.total_amount + ' ' + riyalIcon + '</strong></td>'
                    + '<td><a href="' + detailsUrl + '" class="btn btn-sm btn-primary" target="_blank"><i class="fas fa-eye"></i> {{ __("messages.details") }}</a></td>'
                    + '</tr>';
                tbody.append(row);
            });
        } else {
            tbody.html('<tr>'
                + '<td colspan="8" class="text-center py-4 text-muted">'
                + '<i class="fas fa-inbox" style="font-size: 2rem;"></i>'
                + '<p class="mt-2">{{ __("messages.no_sales_found") }}</p>'
                + '</td>'
                + '</tr>'
            );
        }

        $('#noSelectionMessage').hide();
        $('#resultsContainer').show();
    }

    // Date change triggers reload
    $('#from_date, #to_date').on('change', function () {
        if ($('#seller_id').val()) {
            loadSalesData();
        }
    });
</script>
@endpush
