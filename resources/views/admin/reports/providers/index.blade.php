@extends('layouts.admin')
@section('title')
{{ __('messages.providers_report') }}
@endsection

@section('content')
<style>
    @media print {
        body * {
            visibility: hidden;
        }
        .printable-section, .printable-section * {
            visibility: visible;
        }
        .printable-section {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .no-print, .main-sidebar, .main-header, .content-header, .card-header, footer {
            display: none !important;
        }
        .card {
            border: none;
            box-shadow: none;
        }
    }
</style>
<div class="card">
    <div class="card-header">
        <h3 class="card-title card_title_center">{{ __('messages.providers_report') }}</h3>
    </div>

    <div class="card-body">
        <!-- Search Section -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('messages.search') }} {{ __('messages.provider') }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="provider_search">{{ __('messages.provider') }}</label>
                                    <input type="hidden" id="provider_id" name="provider_id" value="">
                                    <input type="text" id="provider_search" class="form-control"
                                        placeholder="{{ __('messages.search_by_name') }}" autocomplete="off">
                                    <div id="providers-dropdown" class="border rounded mt-1"
                                        style="display:none; position: absolute; width: calc(50% - 30px); max-height: 300px; overflow-y: auto; background: white; z-index: 1000;">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 d-flex align-items-end">
                                <div class="btn-group w-100">
                                    <button type="button" class="btn btn-primary" id="searchBtn">
                                        <i class="fas fa-search"></i> {{ __('messages.Search') }}
                                    </button>
                                    <button type="button" class="btn btn-success" id="exportBtn">
                                        <i class="fas fa-file-excel"></i> Export Excel
                                    </button>
                                    <button type="button" class="btn btn-info" onclick="window.print()">
                                        <i class="fas fa-print"></i> {{ __('messages.print') }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Date Range Filters -->
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="from_date">{{ __('messages.from_date') }}</label>
                                    <input type="date" id="from_date" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="to_date">{{ __('messages.to_date') }}</label>
                                    <input type="date" id="to_date" class="form-control">
                                </div>
                            </div>
                        </div>

                        <!-- Product Filter -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="product_id">{{ __('messages.product') }}</label>
                                    <select id="product_id" class="form-control">
                                        <option value="">{{ __('messages.select_product') }}</option>
                                        <option value="all">{{ __('messages.all_products') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Loading Spinner -->
        <div id="loadingSpinner" class="text-center" style="display:none;">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">{{ __('messages.loading') }}</span>
            </div>
            <p>{{ __('messages.loading') }}</p>
        </div>

        <!-- Provider Info Section -->
        <div id="providerInfoSection" style="display:none;" class="printable-section">
            <!-- Provider Header Card -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h3 class="card-title" id="provider-header-name">-</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>{{ __('messages.provider_name') }}:</strong></p>
                                    <p id="provider-name">-</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>{{ __('messages.email') }}:</strong></p>
                                    <p id="provider-email">-</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>{{ __('messages.phone') }}:</strong></p>
                                    <p id="provider-phone">-</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>{{ __('messages.country') }}:</strong></p>
                                    <p id="provider-country">-</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>العنوان:</strong></p>
                                    <p id="provider-address">-</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>تاريخ الإنشاء:</strong></p>
                                    <p id="provider-created-at">-</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>الحالة:</strong></p>
                                    <p id="provider-status">-</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Section -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-info"><i class="fas fa-box"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">{{ __('messages.total_products') }}</span>
                            <span class="info-box-number" id="stat-total-products">0</span>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-success"><i class="fas fa-cube"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">{{ __('messages.total_quantity') }}</span>
                            <span class="info-box-number" id="stat-total-quantity">0</span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Products Table -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('messages.products_details') }}</h3>
                        </div>
                        <div class="card-body">
                            <div id="productsTableContainer">
                                <p class="text-center text-muted">{{ __('messages.No_data') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Purchases Section -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('messages.purchases') }}</h3>
                        </div>
                        <div class="card-body">
                            <div id="purchasesTableContainer">
                                <p class="text-center text-muted">{{ __('messages.No_data') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Book Requests Section -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">طلبات الكتب وردود الموردين</h3>
                        </div>
                        <div class="card-body">
                            <!-- Book Requests Statistics -->
                            <div class="row mb-4" id="bookRequestsStatistics" style="display: none;">
                                <div class="col-md-2">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-info"><i class="fas fa-list"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">عدد الطلبات</span>
                                            <span class="info-box-number" id="stat-total-book-requests">0</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-success"><i class="fas fa-check"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">موافق عليه</span>
                                            <span class="info-box-number" id="stat-approved-requests">0</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-danger"><i class="fas fa-times"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">مرفوض</span>
                                            <span class="info-box-number" id="stat-rejected-requests">0</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">قيد الانتظار</span>
                                            <span class="info-box-number" id="stat-pending-requests">0</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-secondary"><i class="fas fa-percent"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">نسبة الموافقة</span>
                                            <span class="info-box-number"><span id="stat-approval-rate">0</span>%</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-primary"><i class="fas fa-shopping-cart"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">قيمة الاستيراد</span>
                                            <span class="info-box-number"><span id="stat-import-value">0.00</span> <x-riyal-icon /></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-info"><i class="fas fa-receipt"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">ضريبة الاستيراد</span>
                                            <span class="info-box-number"><span id="stat-import-tax">0.00</span> <x-riyal-icon /></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-success"><i class="fas fa-calculator"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">الإجمالي</span>
                                            <span class="info-box-number"><span id="stat-import-total">0.00</span> <x-riyal-icon /></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Book Requests Table -->
                            <div id="bookRequestsTableContainer">
                                <p class="text-center text-muted">{{ __('messages.No_data') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- No Data Message -->
        <div id="noDataMessage" class="text-center text-muted py-5">
            <i class="fas fa-building" style="font-size: 3em; margin-bottom: 10px;"></i>
            <p>{{ __('messages.select_provider_to_view_report') }}</p>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$(document).ready(function() {
    let providerSearchTimer;
    const dropdown = $('#providers-dropdown');
    const searchInput = $('#provider_search');
    const searchBtn = $('#searchBtn');
    const providerIdInput = $('#provider_id');
    const providerInfoSection = $('#providerInfoSection');
    const noDataMessage = $('#noDataMessage');
    const loadingSpinner = $('#loadingSpinner');
    const productSelect = $('#product_id');

    // Perform provider search
    function performProviderSearch(term) {
        $.ajax({
            url: '{{ route("admin.providers.search") }}',
            method: 'GET',
            data: { term: term, limit: 10 },
            success: function(data) {
                if (data.length > 0) {
                    let html = '';
                    data.forEach(function(provider) {
                        html += `<div class="p-2 border-bottom provider-item"
                                    data-id="${provider.id}"
                                    data-text="${provider.text}"
                                    style="cursor: pointer; transition: 0.3s;">
                                    <strong>${provider.name}</strong><br>
                                    <small class="text-muted">${provider.email}</small>
                                </div>`;
                    });
                    dropdown.html(html).show();

                    // Add hover effect
                    $('.provider-item').hover(function() {
                        $(this).css('background-color', '#f5f5f5');
                    }, function() {
                        $(this).css('background-color', 'transparent');
                    });

                    // Add click handlers
                    $('.provider-item').on('click', function() {
                        const id = $(this).data('id');
                        const text = $(this).data('text');
                        providerIdInput.val(id);
                        searchInput.val(text);
                        dropdown.hide();
                        loadProductsForProvider(id);
                    });
                } else {
                    dropdown.html('<div class="p-2 text-muted">{{ __("messages.no_results") }}</div>').show();
                }
            },
            error: function(xhr) {
                console.error('Error searching providers:', xhr);
                dropdown.html('<div class="p-2 text-danger">{{ __("messages.error") }}</div>').show();
            }
        });
    }

    // Load products for selected provider
    function loadProductsForProvider(providerId) {
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
                console.error('Error loading products');
            }
        });
    }

    // Show all providers when focused
    searchInput.on('focus', function() {
        const term = $(this).val().trim();
        if (term.length === 0) {
            performProviderSearch('');
        }
    });

    // Search on input
    searchInput.on('input', function() {
        const term = $(this).val().trim();

        clearTimeout(providerSearchTimer);
        providerSearchTimer = setTimeout(() => {
            if (term.length >= 0) {
                performProviderSearch(term);
            }
        }, 300);
    });

    // Close dropdown when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#provider_search, #providers-dropdown').length) {
            dropdown.hide();
        }
    });

    // Search button click
    searchBtn.on('click', function() {
        const providerId = providerIdInput.val();

        if (!providerId) {
            Swal.fire({
                icon: 'warning',
                title: '{{ __("messages.warning") }}',
                text: 'يرجى اختيار المورد أولاً',
                confirmButtonText: '{{ __("messages.confirm") }}'
            });
            return;
        }

        loadProviderData(providerId);
    });

    // Load provider data via AJAX
    function loadProviderData(providerId) {
        loadingSpinner.show();
        providerInfoSection.hide();
        noDataMessage.hide();

        const fromDate = $('#from_date').val();
        const toDate = $('#to_date').val();
        const productId = productSelect.val();

        $.ajax({
            url: '{{ route("admin.providers.report.data", ":id") }}'.replace(':id', providerId),
            method: 'GET',
            data: {
                from_date: fromDate,
                to_date: toDate,
                product_id: productId
            },
            success: function(response) {
                if (response.success) {
                    populateProviderInfo(response);
                    loadPurchasesData(providerId);
                    loadBookRequestsData(providerId);
                    loadingSpinner.hide();
                    providerInfoSection.show();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: '{{ __("messages.error") }}',
                        text: response.message || '{{ __("messages.error_loading_data") }}',
                        confirmButtonText: '{{ __("messages.confirm") }}'
                    });
                    loadingSpinner.hide();
                    noDataMessage.show();
                }
            },
            error: function(xhr) {
                console.error('Error loading provider data:', xhr);
                Swal.fire({
                    icon: 'error',
                    title: '{{ __("messages.error") }}',
                    text: '{{ __("messages.error_loading_data") }}',
                    confirmButtonText: '{{ __("messages.confirm") }}'
                });
                loadingSpinner.hide();
                noDataMessage.show();
            }
        });
    }

    // Load purchases data
    function loadPurchasesData(providerId) {
        const fromDate = $('#from_date').val();
        const toDate = $('#to_date').val();

        $.ajax({
            url: '{{ route("admin.reports.providers.purchases", ":id") }}'.replace(':id', providerId),
            method: 'GET',
            data: {
                from_date: fromDate,
                to_date: toDate
            },
            success: function(response) {
                if (response.success) {
                    populatePurchasesInfo(response);
                } else {
                    $('#purchasesTableContainer').html('<p class="text-center text-muted">{{ __("messages.no_data_available") }}</p>');
                }
            },
            error: function(xhr) {
                console.error('Error loading purchases:', xhr);
                $('#purchasesTableContainer').html('<p class="text-center text-danger">حدث خطأ أثناء تحميل البيانات</p>');
            }
        });
    }

    // Load book requests data
    function loadBookRequestsData(providerId) {
        const fromDate = $('#from_date').val();
        const toDate = $('#to_date').val();

        $.ajax({
            url: '{{ route("admin.reports.providers.bookRequests", ":id") }}'.replace(':id', providerId),
            method: 'GET',
            data: {
                from_date: fromDate,
                to_date: toDate
            },
            success: function(response) {
                if (response.success) {
                    populateBookRequestsInfo(response);
                } else {
                    $('#bookRequestsTableContainer').html('<p class="text-center text-muted">{{ __("messages.no_data_available") }}</p>');
                }
            },
            error: function(xhr) {
                console.error('Error loading book requests:', xhr);
                $('#bookRequestsTableContainer').html('<p class="text-center text-danger">حدث خطأ أثناء تحميل البيانات</p>');
            }
        });
    }

    // Populate purchases information
    function populatePurchasesInfo(data) {
        const purchases = data.purchases;

        if (purchases.length > 0) {
            let purchasesHtml = `
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>رقم الفاتورة</th>
                                <th>التاريخ</th>
                                <th>المبلغ الإجمالي</th>
                                <th>الضريبة</th>
                                <th>الإجمالي مع الضريبة</th>
                                <th>الحالة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            const riyalIcon = '<svg class="riyal-icon" style="width: 18px; height: 18px; display: inline-block; margin: 0 4px; vertical-align: middle;" viewBox="0 0 1124.14 1256.39" xmlns="http://www.w3.org/2000/svg"><path fill="currentColor" d="M699.62,1113.02h0c-20.06,44.48-33.32,92.75-38.4,143.37l424.51-90.24c20.06-44.47,33.31-92.75,38.4-143.37l-424.51,90.24Z"/><path fill="currentColor" d="M1085.73,895.8c20.06-44.47,33.32-92.75,38.4-143.37l-330.68,70.33v-135.2l292.27-62.11c20.06-44.47,33.32-92.75,38.4-143.37l-330.68,70.27V66.13c-50.67,28.45-95.67,66.32-132.25,110.99v403.35l-132.25,28.11V0c-50.67,28.44-95.67,66.32-132.25,110.99v525.69l-295.91,62.88c-20.06,44.47-33.33,92.75-38.42,143.37l334.33-71.05v170.26l-358.3,76.14c-20.06,44.47-33.32,92.75-38.4,143.37l375.04-79.7c30.53-6.35,56.77-24.4,73.83-49.24l68.78-101.97v-.02c7.14-10.55,11.3-23.27,11.3-36.97v-149.98l132.25-28.11v270.4l424.53-90.28Z"/></svg>';

            purchases.forEach(function(purchase, index) {
                const totalWithTax = parseFloat(purchase.total_amount) + parseFloat(purchase.total_tax);
                let statusBadge = '';
                if (purchase.status == 'confirmed') {
                    statusBadge = '<span class="badge badge-success">موافق عليه</span>';
                } else if (purchase.status == 'pending') {
                    statusBadge = '<span class="badge badge-warning">قيد الانتظار</span>';
                } else if (purchase.status == 'received') {
                    statusBadge = '<span class="badge badge-info">تم الاستلام</span>';
                } else {
                    statusBadge = '<span class="badge badge-secondary">' + purchase.status + '</span>';
                }

                const showUrl = '{{ route("purchases.show", ":id") }}'.replace(':id', purchase.id);
                purchasesHtml += `
                    <tr>
                        <td>${index + 1}</td>
                        <td><strong>${purchase.purchase_number}</strong></td>
                        <td>${new Date(purchase.created_at).toLocaleDateString('ar-SA')}</td>
                        <td>${parseFloat(purchase.total_amount).toFixed(2)} ${riyalIcon}</td>
                        <td>${parseFloat(purchase.total_tax).toFixed(2)} ${riyalIcon}</td>
                        <td><span class="badge badge-success">${totalWithTax.toFixed(2)} ${riyalIcon}</span></td>
                        <td>${statusBadge}</td>
                        <td>
                            <a href="${showUrl}" class="btn btn-sm btn-info" title="التفاصيل">
                                <i class="fas fa-eye"></i> التفاصيل
                            </a>
                        </td>
                    </tr>
                `;
            });

            purchasesHtml += `
                        </tbody>
                    </table>
                </div>
            `;

            $('#purchasesTableContainer').html(purchasesHtml);
        } else {
            $('#purchasesTableContainer').html('<p class="text-center text-muted">لا توجد عمليات شراء لهذا المورد</p>');
        }
    }

    // Populate book requests information
    function populateBookRequestsInfo(data) {
        const stats = data.statistics;
        const requests = data.requests;

        // Update statistics
        $('#stat-total-book-requests').text(stats.total_requests);
        $('#stat-approved-requests').text(stats.approved);
        $('#stat-rejected-requests').text(stats.rejected);
        $('#stat-pending-requests').text(stats.pending);
        $('#stat-approval-rate').text(stats.approval_rate);
        $('#stat-import-value').text(stats.total_import_value);
        $('#stat-import-tax').text(stats.total_import_tax);
        $('#stat-import-total').text(stats.total_with_tax);

        // Show statistics
        $('#bookRequestsStatistics').show();

        // Build requests table
        if (requests.length > 0) {
            let requestsHtml = `
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>اسم المنتج</th>
                                <th>الكمية المطلوبة</th>
                                <th>الكمية المتاحة</th>
                                <th>السعر</th>
                                <th>ضريبة</th>
                                <th>الإجمالي مع الضريبة</th>
                                <th>الحالة</th>
                                <th>التاريخ</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            const riyalIcon = '<svg class="riyal-icon" style="width: 18px; height: 18px; display: inline-block; margin: 0 4px; vertical-align: middle;" viewBox="0 0 1124.14 1256.39" xmlns="http://www.w3.org/2000/svg"><path fill="currentColor" d="M699.62,1113.02h0c-20.06,44.48-33.32,92.75-38.4,143.37l424.51-90.24c20.06-44.47,33.31-92.75,38.4-143.37l-424.51,90.24Z"/><path fill="currentColor" d="M1085.73,895.8c20.06-44.47,33.32-92.75,38.4-143.37l-330.68,70.33v-135.2l292.27-62.11c20.06-44.47,33.32-92.75,38.4-143.37l-330.68,70.27V66.13c-50.67,28.45-95.67,66.32-132.25,110.99v403.35l-132.25,28.11V0c-50.67,28.44-95.67,66.32-132.25,110.99v525.69l-295.91,62.88c-20.06,44.47-33.33,92.75-38.42,143.37l334.33-71.05v170.26l-358.3,76.14c-20.06,44.47-33.32,92.75-38.4,143.37l375.04-79.7c30.53-6.35,56.77-24.4,73.83-49.24l68.78-101.97v-.02c7.14-10.55,11.3-23.27,11.3-36.97v-149.98l132.25-28.11v270.4l424.53-90.28Z"/></svg>';

            requests.forEach(function(request, index) {
                const statusBadge = request.status_badge;
                const detailsUrl = '{{ route("bookRequests.responses.show", ":id") }}'.replace(':id', request.id);
                requestsHtml += `
                    <tr>
                        <td>${index + 1}</td>
                        <td><strong>${request.product_name}</strong></td>
                        <td><span class="badge badge-info">${request.requested_quantity}</span></td>
                        <td><span class="badge badge-primary">${request.available_quantity}</span></td>
                        <td>${request.price} ${riyalIcon}</td>
                        <td>${request.tax_percentage}</td>
                        <td><span class="badge badge-success">${request.total_with_tax} ${riyalIcon}</span></td>
                        <td>${statusBadge}</td>
                        <td>${request.created_at}</td>
                        <td>
                            <a href="${detailsUrl}" class="btn btn-sm btn-info" title="التفاصيل">
                                <i class="fas fa-eye"></i> التفاصيل
                            </a>
                        </td>
                    </tr>
                `;
            });

            requestsHtml += `
                        </tbody>
                    </table>
                </div>
            `;

            $('#bookRequestsTableContainer').html(requestsHtml);
        } else {
            $('#bookRequestsTableContainer').html('<p class="text-center text-muted">لا توجد طلبات كتب لهذا المورد</p>');
        }
    }

    // Populate provider information
    function populateProviderInfo(data) {
        const provider = data.provider;
        const stats = data.statistics;
        const products = data.products;

        // Provider info
        $('#provider-header-name').text(provider.name);
        $('#provider-name').text(provider.name);
        $('#provider-email').text(provider.email);
        $('#provider-phone').text(provider.phone);
        $('#provider-country').text(provider.country || '-');
        $('#provider-address').text(provider.address || '-');
        $('#provider-created-at').text(provider.created_at ? new Date(provider.created_at).toLocaleDateString('ar-SA') : '-');

        // Status Badge
        const statusClass = provider.activate == 1 ? 'success' : 'danger';
        const statusText = provider.activate == 1 ? 'نشط' : 'معطل';
        $('#provider-status').html(`<span class="badge bg-${statusClass}">${statusText}</span>`);

        // Statistics
        $('#stat-total-products').text(stats.total_products);
        $('#stat-total-quantity').text(stats.total_quantity);

        // Products table
        if (products.length > 0) {
            let productsHtml = `
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>{{ __('messages.product_name') }}</th>
                                <th>{{ __('messages.sku') }}</th>
                                <th>{{ __('messages.unit_price') }}</th>
                                <th>{{ __('messages.total_quantity') }}</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            const riyalIcon = '<svg class="riyal-icon" style="width: 18px; height: 18px; display: inline-block; margin: 0 4px; vertical-align: middle;" viewBox="0 0 1124.14 1256.39" xmlns="http://www.w3.org/2000/svg"><path fill="currentColor" d="M699.62,1113.02h0c-20.06,44.48-33.32,92.75-38.4,143.37l424.51-90.24c20.06-44.47,33.31-92.75,38.4-143.37l-424.51,90.24Z"/><path fill="currentColor" d="M1085.73,895.8c20.06-44.47,33.32-92.75,38.4-143.37l-330.68,70.33v-135.2l292.27-62.11c20.06-44.47,33.32-92.75,38.4-143.37l-330.68,70.27V66.13c-50.67,28.45-95.67,66.32-132.25,110.99v403.35l-132.25,28.11V0c-50.67,28.44-95.67,66.32-132.25,110.99v525.69l-295.91,62.88c-20.06,44.47-33.33,92.75-38.42,143.37l334.33-71.05v170.26l-358.3,76.14c-20.06,44.47-33.32,92.75-38.4,143.37l375.04-79.7c30.53-6.35,56.77-24.4,73.83-49.24l68.78-101.97v-.02c7.14-10.55,11.3-23.27,11.3-36.97v-149.98l132.25-28.11v270.4l424.53-90.28Z"/></svg>';

            products.forEach(function(product, index) {
                productsHtml += `
                    <tr>
                        <td>${index + 1}</td>
                        <td><strong>${product.name}</strong></td>
                        <td>${product.sku}</td>
                        <td>${product.unit_price} ${riyalIcon}</td>
                        <td><span class="badge badge-info">${product.quantity}</span></td>
                    </tr>
                `;
            });

            productsHtml += `
                        </tbody>
                        <tfoot class="table-light font-weight-bold">
                            <tr>
                                <td colspan="4" class="text-right">{{ __('messages.total') }}:</td>
                                <td><span class="badge badge-info">${stats.total_quantity}</span></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            `;

            $('#productsTableContainer').html(productsHtml);
        } else {
            $('#productsTableContainer').html('<p class="text-center text-muted">{{ __("messages.no_data_available") }}</p>');
        }
    }

    // Allow Enter key to search
    searchInput.on('keypress', function(e) {
        if (e.which == 13) {
            e.preventDefault();
            searchBtn.click();
        }
    });

    // Export Button Click
    $('#exportBtn').on('click', function() {
        const providerId = providerIdInput.val();
        if (!providerId) {
            Swal.fire({
                icon: 'warning',
                title: '{{ __("messages.warning") }}',
                text: 'يرجى اختيار المورد أولاً',
                confirmButtonText: '{{ __("messages.confirm") }}'
            });
            return;
        }

        const fromDate = $('#from_date').val();
        const toDate = $('#to_date').val();
        const productId = productSelect.val();

        let url = '{{ route("admin.reports.providers.export") }}' + 
                  '?provider_id=' + providerId + 
                  '&from_date=' + fromDate + 
                  '&to_date=' + toDate + 
                  '&product_id=' + productId;
        
        window.location.href = url;
    });
});
</script>
@endsection
