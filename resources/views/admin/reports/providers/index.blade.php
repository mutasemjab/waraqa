@extends('layouts.admin')
@section('title')
    {{ __('messages.providers_report') }}
@endsection

@section('content')
    <style>
        /* Print Styles - PDF/Print Optimization */
        @media print {
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            html,
            body {
                width: 100%;
                height: 100%;
                margin: 0;
                padding: 0;
            }

            body * {
                visibility: hidden;
            }

            .printable-section,
            .printable-section * {
                visibility: visible;
            }

            .printable-section {
                position: static;
                width: 100%;
                page-break-after: always;
                page-break-inside: avoid;
            }

            .provider-info-section {
                page-break-after: always;
            }

            /* Hide unwanted elements */
            .no-print,
            .main-sidebar,
            .main-header,
            .content-header,
            footer,
            .btn-group,
            .statistics-section,
            .info-box,
            .info-box-icon,
            .info-box-content,
            button,
            .btn,
            .form-group,
            .form-control,
            label,
            input[type="text"],
            input[type="date"],
            input[type="hidden"],
            select,
            .loadingSpinner {
                display: none !important;
                visibility: hidden !important;
            }

            /* Hide search section card */
            .row.mb-4>.col-md-12>.card.card-default {
                display: none !important;
                visibility: hidden !important;
            }

            /* Ensure printable sections are visible */
            .printable-section,
            .provider-info-section,
            .distribution-section,
            .sales-section,
            .purchases-section,
            .refunds-section,
            .sellers-payments-section,
            .sales-by-place-section,
            .book-requests-section {
                display: block !important;
                visibility: visible !important;
            }

            /* Show card headers for printable sections */
            .printable-section .card-header,
            .provider-info-section .card-header {
                display: block !important;
                visibility: visible !important;
            }

            /* Card styling */
            .card {
                border: 1px solid #ddd;
                box-shadow: none;
                page-break-inside: avoid;
                margin-bottom: 20px;
            }

            .card-body {
                padding: 15px;
            }

            /* Provider Info - Table Style */
            .provider-info-section {
                page-break-after: always;
            }

            .provider-info-section {
                background: white;
                page-break-after: always;
            }

            .provider-info-section h3 {
                font-size: 16px;
                font-weight: bold;
                margin-bottom: 15px;
                border-bottom: 2px solid #4472C4;
                padding-bottom: 10px;
                color: #333;
            }

            .provider-info-section .card {
                border: 1px solid #ddd;
                page-break-inside: avoid;
            }

            .provider-info-section .card-header {
                background-color: #4472C4 !important;
                color: white !important;
                padding: 10px;
                border-bottom: 1px solid #333;
                display: block !important;
                visibility: visible !important;
            }

            .provider-info-section .card-title {
                color: white !important;
                font-weight: bold !important;
                font-size: 14px !important;
                margin: 0 !important;
            }

            .provider-info-section .row {
                margin-bottom: 10px;
                page-break-inside: avoid;
            }

            .provider-info-section p {
                margin: 5px 0;
                font-size: 11px;
                display: block;
                page-break-inside: avoid;
            }

            .provider-info-section strong {
                color: #4472C4;
                font-weight: bold;
                min-width: 100px;
                display: inline-block;
            }

            .provider-info-section .col-md-6 {
                page-break-inside: avoid;
            }

            /* Table Styling */
            table {
                width: 100%;
                border-collapse: collapse;
                margin: 15px 0;
                font-size: 11px;
            }

            thead {
                background-color: #4472C4;
                color: white;
            }

            thead th {
                padding: 10px;
                text-align: center;
                border: 1px solid #333;
                font-weight: bold;
                font-size: 12px;
            }

            tbody td {
                padding: 8px 10px;
                border: 1px solid #ddd;
                text-align: center;
            }

            tbody tr:nth-child(odd) {
                background-color: #f9f9f9;
            }

            tbody tr:nth-child(even) {
                background-color: #fff;
            }

            /* Section Headers */
            .print-section-title {
                font-size: 14px;
                font-weight: bold;
                margin-top: 20px;
                margin-bottom: 10px;
                padding-bottom: 8px;
                border-bottom: 2px solid #4472C4;
                color: #333;
                page-break-after: avoid;
            }

            /* Summary Table */
            .summary-table {
                width: 100%;
                margin: 15px 0;
                border-collapse: collapse;
            }

            .summary-table td {
                padding: 8px;
                border: 1px solid #ddd;
                font-size: 11px;
            }

            .summary-table td:first-child {
                font-weight: bold;
                background-color: #f0f0f0;
                width: 40%;
            }

            .summary-table td:last-child {
                text-align: right;
                font-weight: 600;
            }

            /* Page breaks */
            .page-break {
                page-break-after: always;
                display: block;
            }

            /* Remove extra margins for print */
            .mb-4 {
                margin-bottom: 0px !important;
            }

            .mb-3 {
                margin-bottom: 0px !important;
            }

            .row {
                margin-bottom: 0 !important;
                margin-left: 0 !important;
                margin-right: 0 !important;
            }

            .col-md-12,
            .col-md-6,
            .col-md-4,
            .col-md-3,
            .col-md-2 {
                padding-left: 0 !important;
                padding-right: 0 !important;
            }

            /* Ensure tables are visible */
            #productsTableContainer table,
            #bookRequestsTableContainer table,
            #distributionTableContainer table,
            #salesTableContainer table,
            #refundsTableContainer table,
            #sellersPaymentsTableContainer table {
                visibility: visible !important;
            }
        }

        /* Screen styles */
        .print-section-title {
            display: none;
        }

        /* Tables styling for screen */
        table {
            font-size: 13px;
        }

        tbody td {
            padding: 10px;
        }

        /* Additional print optimizations */
        @media print {

            /* Ensure table headers are always visible */
            .printable-section table {
                visibility: visible !important;
            }

            .printable-section table thead {
                display: table-header-group !important;
                visibility: visible !important;
                background-color: #4472C4 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .printable-section table thead tr {
                display: table-row !important;
                visibility: visible !important;
            }

            .printable-section table thead th {
                display: table-cell !important;
                visibility: visible !important;
                background-color: #4472C4 !important;
                color: white !important;
                border: 1px solid #333 !important;
                padding: 10px !important;
            }

            /* Hide only the last column (actions column) */
            thead tr th:last-child,
            tbody tr td:last-child {
                display: none !important;
                visibility: hidden !important;
            }

            /* Optimize text alignment */
            thead {
                display: table-header-group !important;
                visibility: visible !important;
            }

            thead tr {
                display: table-row !important;
                visibility: visible !important;
            }

            thead.table-light {
                background-color: #4472C4 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            thead th {
                display: table-cell !important;
                visibility: visible !important;
                text-align: center !important;
                background-color: #4472C4 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                color: white !important;
                font-weight: bold !important;
                font-size: 12px !important;
                border: 1px solid #333 !important;
                padding: 10px !important;
                vertical-align: middle !important;
            }

            tbody {
                display: table-row-group !important;
                visibility: visible !important;
            }

            tbody tr {
                display: table-row !important;
                visibility: visible !important;
            }

            tbody td {
                display: table-cell !important;
                visibility: visible !important;
                text-align: center;
                vertical-align: middle;
                border: 1px solid #ddd !important;
                padding: 8px !important;
                font-size: 11px !important;
            }

            tbody td:first-child {
                text-align: center;
                font-weight: bold;
                width: 4%;
                background-color: #f5f5f5;
            }

            tbody td:nth-child(2) {
                text-align: right;
                font-weight: 600;
                text-align: left;
            }

            /* Hide badges styling - show plain text */
            .badge {
                background-color: transparent !important;
                color: #000 !important;
                border: 1px solid #999 !important;
                padding: 3px 6px !important;
                display: inline-block !important;
            }

            .badge-info {
                background-color: #e8f4f8 !important;
                border-color: #4472C4 !important;
            }

            .badge-success {
                background-color: #e8f5e9 !important;
                border-color: #4caf50 !important;
            }

            .badge-primary {
                background-color: #e3f2fd !important;
                border-color: #2196f3 !important;
            }

            .badge-warning {
                background-color: #fff3e0 !important;
                border-color: #ff9800 !important;
            }

            .badge-danger {
                background-color: #ffebee !important;
                border-color: #f44336 !important;
            }

            /* Table responsive hide wrapper for print */
            .table-responsive {
                display: block !important;
                overflow: visible !important;
                width: 100% !important;
            }

            /* Hide SVG icons completely in tables */
            svg.riyal-icon {
                display: none !important;
                visibility: hidden !important;
                width: 0 !important;
                height: 0 !important;
            }

            /* Hide small and text-muted styling */
            small.text-muted {
                color: #666 !important;
                display: inline !important;
            }

            /* Ensure table visibility */
            .table {
                margin: 0px !important;
                margin-bottom: 1px !important;
                color: #000 !important;
                width: 100% !important;
                border-collapse: collapse !important;
            }

            /* Better spacing for print */
            tbody tr {
                page-break-inside: avoid !important;
                border: 1px solid #ddd !important;
            }

            tbody tr:nth-child(odd) {
                background-color: #fafafa !important;
            }

            tbody tr:nth-child(even) {
                background-color: #fff !important;
            }

            /* Table footer styling */
            tfoot {
                display: table-footer-group;
            }

            tfoot tr {
                background-color: #e8e8e8 !important;
                font-weight: bold !important;
                page-break-inside: avoid !important;
            }

            tfoot td {
                border: 1px solid #333 !important;
                padding: 10px !important;
                text-align: right !important;
                font-weight: bold !important;
                font-size: 12px !important;
            }

            tfoot td:first-child {
                text-align: center;
                background-color: #d0d0d0;
            }

            /* Section styling */
            .distribution-section,
            .sales-section,
            .purchases-section,
            .refunds-section,
            .sellers-payments-section,
            .sales-by-place-section,
            .book-requests-section,
            .products-section {
                margin-bottom: 3px !important;
                margin-top: 3px !important;
                background: white;
            }

            /* Section card styling */
            .distribution-section .card,
            .sales-section .card,
            .purchases-section .card,
            .refunds-section .card,
            .sellers-payments-section .card,
            .sales-by-place-section .card,
            .book-requests-section .card,
            .products-section .card {
                border: 1px solid #ddd;
                page-break-inside: avoid;
            }

            /* Section header styling */
            .distribution-section .card-header,
            .sales-section .card-header,
            .purchases-section .card-header,
            .refunds-section .card-header,
            .sellers-payments-section .card-header,
            .sales-by-place-section .card-header,
            .book-requests-section .card-header,
            .products-section .card-header {
                background-color: #4472C4 !important;
                color: white !important;
                padding: 10px;
                border-bottom: 1px solid #333;
                display: block !important;
                visibility: visible !important;
            }

            .distribution-section .card-title,
            .sales-section .card-title,
            .purchases-section .card-title,
            .refunds-section .card-title,
            .sellers-payments-section .card-title,
            .sales-by-place-section .card-title,
            .book-requests-section .card-title,
            .products-section .card-title {
                color: white !important;
                font-weight: bold !important;
                font-size: 14px !important;
                margin: 0 !important;
            }

            .distribution-section .card-header.bg-primary,
            .sales-section .card-header.bg-success,
            .purchases-section .card-header.bg-info,
            .refunds-section .card-header.bg-danger,
            .sellers-payments-section .card-header.bg-warning,
            .sales-by-place-section .card-header.bg-success {
                background-color: #4472C4 !important;
            }

            /* Strong text styling */
            td strong {
                font-weight: bold !important;
                color: #000 !important;
            }

            /* Section body styling */
            .distribution-section .card-body,
            .sales-section .card-body,
            .purchases-section .card-body,
            .refunds-section .card-body,
            .sellers-payments-section .card-body,
            .sales-by-place-section .card-body,
            .book-requests-section .card-body,
            .products-section .card-body {
                padding: 5px;
                background: white;
            }

            .card-body {
                padding: 5px !important;
            }

            .card {
                margin-bottom: 3px !important;
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
                                        <button type="button" class="btn btn-warning" id="displayOptionsBtn"
                                            data-toggle="modal" data-target="#displayOptionsModal">
                                            <i class="fas fa-sliders-h"></i> {{ __('messages.display_options') }}
                                        </button>
                                        <button type="button" class="btn btn-success" id="exportBtn" data-toggle="modal"
                                            data-target="#exportOptionsModal">
                                            <i class="fas fa-file-excel"></i> {{ __('messages.export_excel') }}
                                        </button>
                                        <button type="button" class="btn btn-info" id="printBtn">
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
            <div id="providerInfoSection" style="display:none;" class="provider-info-section printable-section">
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
                                        <p><strong>{{ __('messages.address') }}:</strong></p>
                                        <p id="provider-address">-</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>{{ __('messages.created_at') }}:</strong></p>
                                        <p id="provider-created-at">-</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>{{ __('messages.status') }}:</strong></p>
                                        <p id="provider-status">-</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistics Section -->
                <div class="row mb-4 statistics-section">
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
                <div class="row mb-4 products-section">
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
                <div class="row mb-4 purchases-section printable-section" style="display:block;">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header bg-info text-white">
                                <h3 class="card-title"><i class="fas fa-shopping-cart"></i>
                                    {{ __('messages.purchases') }}</h3>
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
                <div class="row book-requests-section">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">{{ __('messages.book_requests_and_responses') }}</h3>
                            </div>
                            <div class="card-body">
                                <!-- Book Requests Statistics -->
                                <div class="row mb-4" id="bookRequestsStatistics" style="display: none;">
                                    <div class="col-md-2">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-info"><i class="fas fa-list"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">{{ __('messages.number_of_requests') }}</span>
                                                <span class="info-box-number" id="stat-total-book-requests">0</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-success"><i class="fas fa-check"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">{{ __('messages.approved') }}</span>
                                                <span class="info-box-number" id="stat-approved-requests">0</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-danger"><i class="fas fa-times"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">{{ __('messages.rejected') }}</span>
                                                <span class="info-box-number" id="stat-rejected-requests">0</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">{{ __('messages.pending') }}</span>
                                                <span class="info-box-number" id="stat-pending-requests">0</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-secondary"><i class="fas fa-percent"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">{{ __('messages.approval_rate') }}</span>
                                                <span class="info-box-number"><span
                                                        id="stat-approval-rate">0</span>%</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-primary"><i
                                                    class="fas fa-shopping-cart"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">{{ __('messages.import_value') }}</span>
                                                <span class="info-box-number"><span id="stat-import-value">0.00</span>
                                                    <x-riyal-icon /></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-info"><i class="fas fa-receipt"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">{{ __('messages.import_tax') }}</span>
                                                <span class="info-box-number"><span id="stat-import-tax">0.00</span>
                                                    <x-riyal-icon /></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-success"><i
                                                    class="fas fa-calculator"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">{{ __('messages.total') }}</span>
                                                <span class="info-box-number"><span id="stat-import-total">0.00</span>
                                                    <x-riyal-icon /></span>
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

            <!-- Distribution Section -->
            <div class="row mb-4 distribution-section printable-section" style="display:block;">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h3 class="card-title"><i class="fas fa-boxes"></i>
                                {{ __('messages.distribution_on_distribution_points') }}</h3>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-info"><i class="fas fa-warehouse"></i></span>
                                        <div class="info-box-content">
                                            <span
                                                class="info-box-text">{{ __('messages.number_of_distribution_points') }}</span>
                                            <span class="info-box-number" id="dist-total-warehouses">0</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-success"><i class="fas fa-boxes"></i></span>
                                        <div class="info-box-content">
                                            <span
                                                class="info-box-text">{{ __('messages.total_distributed_quantity') }}</span>
                                            <span class="info-box-number" id="dist-total-quantity">0</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="distributionTableContainer">
                                <p class="text-center text-muted">{{ __('messages.no_data_available') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sales by Warehouse Section -->
            <div class="row mb-4 sales-section printable-section" style="display:block;">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h3 class="card-title"><i class="fas fa-chart-bar"></i>
                                {{ __('messages.sales_by_distribution_point') }}</h3>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-success"><i class="fas fa-box"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">{{ __('messages.total_sold') }}</span>
                                            <span class="info-box-number" id="sales-total-quantity">0</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-primary"><i
                                                class="fas fa-money-bill-wave"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">{{ __('messages.total_revenue') }}</span>
                                            <span class="info-box-number" id="sales-total-revenue">0.00</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="salesTableContainer">
                                <p class="text-center text-muted">{{ __('messages.no_data_available') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Refunds Section -->
            <div class="row mb-4 refunds-section printable-section" style="display:block;">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header bg-danger text-white">
                            <h3 class="card-title"><i class="fas fa-undo"></i> {{ __('messages.refunds') }}</h3>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-warning"><i class="fas fa-reply"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">{{ __('messages.total_returned') }}</span>
                                            <span class="info-box-number" id="refunds-total-quantity">0</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-danger"><i
                                                class="fas fa-money-bill-wave"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">{{ __('messages.total_refund_amount') }}</span>
                                            <span class="info-box-number" id="refunds-total-amount">0.00</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="refundsTableContainer">
                                <p class="text-center text-muted">{{ __('messages.no_data_available') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sellers Payments Section -->
            <div class="row mb-4 sellers-payments-section printable-section" style="display:block;">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header bg-warning text-white">
                            <h3 class="card-title"><i class="fas fa-money-bill-wave"></i>
                                {{ __('messages.distribution_points_payments') }}</h3>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-info"><i class="fas fa-calculator"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">{{ __('messages.total_quantity') }}</span>
                                            <span class="info-box-number" id="payments-total-amount">0.00</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-success"><i class="fas fa-check-circle"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">{{ __('messages.total_paid') }}</span>
                                            <span class="info-box-number" id="payments-total-paid">0.00</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-danger"><i
                                                class="fas fa-exclamation-circle"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">{{ __('messages.total_remaining') }}</span>
                                            <span class="info-box-number" id="payments-total-remaining">0.00</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="sellersPaymentsTableContainer">
                                <p class="text-center text-muted">{{ __('messages.no_data_available') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stock Balance Section -->
            <div class="row mb-4 stock-balance-section printable-section" style="display:block;">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h3 class="card-title"><i class="fas fa-cubes"></i> {{ __('messages.remaining_balance') }}
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-info"><i class="fas fa-cubes"></i></span>
                                        <div class="info-box-content">
                                            <span
                                                class="info-box-text">{{ __('messages.total_remaining_quantity') }}</span>
                                            <span class="info-box-number" id="stock-total-remaining">0</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="stockBalanceTableContainer">
                                <p class="text-center text-muted">{{ __('messages.no_data_available') }}</p>
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

    <!-- Display Options Modal -->
    <div class="modal fade" id="displayOptionsModal" tabindex="-1" role="dialog"
        aria-labelledby="displayOptionsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title" id="displayOptionsModalLabel">
                        <i class="fas fa-sliders-h"></i> خيارات العرض
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-3">{{ __('messages.select_sections_to_display') }}</p>

                    <!-- Provider Info Section -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input display-section-toggle"
                                            id="display_provider_info" data-section="provider_info" checked>
                                        <label class="custom-control-label font-weight-bold" for="display_provider_info">
                                            <i class="fas fa-user"></i> {{ __('messages.provider_info') }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Statistics Section -->
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input display-section-toggle"
                                            id="display_statistics" data-section="statistics" checked>
                                        <label class="custom-control-label font-weight-bold" for="display_statistics">
                                            <i class="fas fa-chart-pie"></i> {{ __('messages.statistics') }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Products Section -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input display-section-toggle"
                                            id="display_products" data-section="products" checked>
                                        <label class="custom-control-label font-weight-bold" for="display_products">
                                            <i class="fas fa-box"></i> {{ __('messages.products_table') }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Purchases Section -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input display-section-toggle"
                                            id="display_purchases" data-section="purchases" checked>
                                        <label class="custom-control-label font-weight-bold" for="display_purchases">
                                            <i class="fas fa-shopping-cart"></i> {{ __('messages.purchases') }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Distribution & Sales Section -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input display-section-toggle"
                                            id="display_distribution" data-section="distribution" checked>
                                        <label class="custom-control-label font-weight-bold" for="display_distribution">
                                            <i class="fas fa-boxes"></i>
                                            {{ __('messages.distribution_on_distribution_points') }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sales Section -->
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input display-section-toggle"
                                            id="display_sales" data-section="sales" checked>
                                        <label class="custom-control-label font-weight-bold" for="display_sales">
                                            <i class="fas fa-chart-bar"></i> {{ __('messages.sales') }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Refunds & Sellers Payments Section -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input display-section-toggle"
                                            id="display_refunds" data-section="refunds" checked>
                                        <label class="custom-control-label font-weight-bold" for="display_refunds">
                                            <i class="fas fa-undo"></i> {{ __('messages.refunds') }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sellers Payments Section -->
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input display-section-toggle"
                                            id="display_sellers_payments" data-section="sellers_payments" checked>
                                        <label class="custom-control-label font-weight-bold"
                                            for="display_sellers_payments">
                                            <i class="fas fa-money-bill-wave"></i>
                                            {{ __('messages.distribution_points_payments') }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Stock Balance & Book Requests Section -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input display-section-toggle"
                                            id="display_stock_balance" data-section="stock_balance" checked>
                                        <label class="custom-control-label font-weight-bold" for="display_stock_balance">
                                            <i class="fas fa-cubes"></i> {{ __('messages.remaining_balance') }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Book Requests Section -->
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input display-section-toggle"
                                            id="display_book_requests" data-section="book_requests" checked>
                                        <label class="custom-control-label font-weight-bold" for="display_book_requests">
                                            <i class="fas fa-book"></i> {{ __('messages.book_requests') }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="border-top pt-3">
                        <div class="btn-group btn-group-sm w-100">
                            <button type="button" class="btn btn-outline-primary flex-grow-1" id="selectAllDisplay">
                                <i class="fas fa-check-double"></i> {{ __('messages.select_all') }}
                            </button>
                            <button type="button" class="btn btn-outline-secondary flex-grow-1" id="deselectAllDisplay">
                                <i class="fas fa-times"></i> {{ __('messages.cancel_all') }}
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> {{ __('messages.close') }}
                    </button>
                    <button type="button" class="btn btn-warning" id="applyDisplayOptions">
                        <i class="fas fa-check"></i> {{ __('messages.apply') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Export Options Modal -->
    <div class="modal fade" id="exportOptionsModal" tabindex="-1" role="dialog"
        aria-labelledby="exportOptionsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="exportOptionsModalLabel">
                        <i class="fas fa-file-excel"></i> {{ __('messages.export_options') }}
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-3">{{ __('messages.select_export_sections') }}</p>

                    <div class="row">
                        <!-- Provider Info Section -->
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input section-toggle"
                                            id="export_provider_info" checked data-section="provider_info">
                                        <label class="custom-control-label font-weight-bold" for="export_provider_info">
                                            <i class="fas fa-user"></i> {{ __('messages.provider_info') }}
                                        </label>
                                    </div>
                                </div>
                                <div class="card-body" id="provider_info_options">
                                    <div class="custom-control custom-checkbox mb-2">
                                        <input type="checkbox" class="custom-control-input export-option"
                                            id="export_provider_name" name="export_options[]" value="provider_name"
                                            checked>
                                        <label class="custom-control-label"
                                            for="export_provider_name">{{ __('messages.name') }}</label>
                                    </div>
                                    <div class="custom-control custom-checkbox mb-2">
                                        <input type="checkbox" class="custom-control-input export-option"
                                            id="export_provider_email" name="export_options[]" value="provider_email"
                                            checked>
                                        <label class="custom-control-label"
                                            for="export_provider_email">{{ __('messages.email') }}</label>
                                    </div>
                                    <div class="custom-control custom-checkbox mb-2">
                                        <input type="checkbox" class="custom-control-input export-option"
                                            id="export_provider_phone" name="export_options[]" value="provider_phone"
                                            checked>
                                        <label class="custom-control-label"
                                            for="export_provider_phone">{{ __('messages.phone') }}</label>
                                    </div>
                                    <div class="custom-control custom-checkbox mb-2">
                                        <input type="checkbox" class="custom-control-input export-option"
                                            id="export_provider_country" name="export_options[]" value="provider_country"
                                            checked>
                                        <label class="custom-control-label"
                                            for="export_provider_country">{{ __('messages.country') }}</label>
                                    </div>
                                    <div class="custom-control custom-checkbox mb-2">
                                        <input type="checkbox" class="custom-control-input export-option"
                                            id="export_provider_address" name="export_options[]" value="provider_address"
                                            checked>
                                        <label class="custom-control-label"
                                            for="export_provider_address">{{ __('messages.address') }}</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Statistics Section -->
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input section-toggle"
                                            id="export_statistics" checked data-section="statistics">
                                        <label class="custom-control-label font-weight-bold" for="export_statistics">
                                            <i class="fas fa-chart-bar"></i> {{ __('messages.statistics') }}
                                        </label>
                                    </div>
                                </div>
                                <div class="card-body" id="statistics_options">
                                    <div class="custom-control custom-checkbox mb-2">
                                        <input type="checkbox" class="custom-control-input export-option"
                                            id="export_total_products" name="export_options[]" value="total_products"
                                            checked>
                                        <label class="custom-control-label"
                                            for="export_total_products">{{ __('messages.total_products') }}</label>
                                    </div>
                                    <div class="custom-control custom-checkbox mb-2">
                                        <input type="checkbox" class="custom-control-input export-option"
                                            id="export_total_quantity" name="export_options[]" value="total_quantity"
                                            checked>
                                        <label class="custom-control-label"
                                            for="export_total_quantity">{{ __('messages.total_quantity') }}</label>
                                    </div>
                                    <div class="custom-control custom-checkbox mb-2">
                                        <input type="checkbox" class="custom-control-input export-option"
                                            id="export_total_revenue" name="export_options[]" value="total_revenue"
                                            checked>
                                        <label class="custom-control-label"
                                            for="export_total_revenue">{{ __('messages.total_revenue') }}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Products Section -->
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input section-toggle export-option"
                                            id="export_products" name="export_options[]" value="products" checked data-section="products">
                                        <label class="custom-control-label font-weight-bold" for="export_products">
                                            <i class="fas fa-box"></i> {{ __('messages.products_table') }}
                                        </label>
                                    </div>
                                </div>
                                <div class="card-body" id="products_options">
                                    <small class="text-muted">{{ __('messages.products_table_description') }}</small>
                                </div>
                            </div>
                        </div>

                        <!-- Purchases Section -->
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input section-toggle export-option"
                                            id="export_purchases" name="export_options[]" value="purchases" checked data-section="purchases">
                                        <label class="custom-control-label font-weight-bold" for="export_purchases">
                                            <i class="fas fa-shopping-cart"></i> {{ __('messages.purchases') }}
                                        </label>
                                    </div>
                                </div>
                                <div class="card-body" id="purchases_options">
                                    <small class="text-muted">{{ __('messages.purchases_table_description') }}</small>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="row">
                        <!-- Distribution Section -->
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input section-toggle export-option"
                                            id="export_distribution" name="export_options[]" value="distribution" checked
                                            data-section="distribution">
                                        <label class="custom-control-label font-weight-bold" for="export_distribution">
                                            <i class="fas fa-boxes"></i>
                                            {{ __('messages.distribution_on_distribution_points') }}
                                        </label>
                                    </div>
                                </div>
                                <div class="card-body" id="distribution_options">
                                    <small
                                        class="text-muted">{{ __('messages.distribution_on_points_description') }}</small>
                                </div>
                            </div>
                        </div>

                        <!-- Sales Section -->
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input section-toggle export-option"
                                            id="export_sales" name="export_options[]" value="sales" checked
                                            data-section="sales">
                                        <label class="custom-control-label font-weight-bold" for="export_sales">
                                            <i class="fas fa-chart-bar"></i> المبيعات
                                        </label>
                                    </div>
                                </div>
                                <div class="card-body" id="sales_options">
                                    <small class="text-muted">{{ __('messages.sales_by_point_description') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Refunds Section -->
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input section-toggle export-option"
                                            id="export_refunds" name="export_options[]" value="refunds" checked
                                            data-section="refunds">
                                        <label class="custom-control-label font-weight-bold" for="export_refunds">
                                            <i class="fas fa-undo"></i> {{ __('messages.refunds') }}
                                        </label>
                                    </div>
                                </div>
                                <div class="card-body" id="refunds_options">
                                    <small class="text-muted">{{ __('messages.returned_sales_description') }}</small>
                                </div>
                            </div>
                        </div>

                        <!-- Sellers Payments Section -->
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input section-toggle export-option"
                                            id="export_sellers_payments" name="export_options[]" value="sellers_payments"
                                            checked data-section="sellers_payments">
                                        <label class="custom-control-label font-weight-bold"
                                            for="export_sellers_payments">
                                            <i class="fas fa-money-bill-wave"></i>
                                            {{ __('messages.distribution_points_payments') }}
                                        </label>
                                    </div>
                                </div>
                                <div class="card-body" id="sellers_payments_options">
                                    <small
                                        class="text-muted">{{ __('messages.distribution_points_payments_summary') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Stock Balance Section -->
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input section-toggle export-option"
                                            id="export_stock_balance" name="export_options[]" value="stock_balance"
                                            checked data-section="stock_balance">
                                        <label class="custom-control-label font-weight-bold" for="export_stock_balance">
                                            <i class="fas fa-cubes"></i> {{ __('messages.remaining_balance') }}
                                        </label>
                                    </div>
                                </div>
                                <div class="card-body" id="stock_balance_options">
                                    <small class="text-muted">{{ __('messages.remaining_quantities_in_stock') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Book Requests Section -->
                        <div class="col-md-12">
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input section-toggle export-option"
                                            id="export_book_requests" name="export_options[]" value="book_requests" checked data-section="book_requests">
                                        <label class="custom-control-label font-weight-bold" for="export_book_requests">
                                            <i class="fas fa-book"></i> {{ __('messages.book_requests_section') }}
                                        </label>
                                    </div>
                                </div>
                                <div class="card-body" id="book_requests_options">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="custom-control custom-checkbox mb-2">
                                                <input type="checkbox" class="custom-control-input export-option"
                                                    id="export_book_stats" name="export_options[]" value="book_stats"
                                                    checked>
                                                <label class="custom-control-label"
                                                    for="export_book_stats">{{ __('messages.book_requests_stats') }}</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="custom-control custom-checkbox mb-2">
                                                <input type="checkbox" class="custom-control-input export-option"
                                                    id="export_book_details" name="export_options[]" value="book_details"
                                                    checked>
                                                <label class="custom-control-label"
                                                    for="export_book_details">{{ __('messages.book_requests_details') }}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="border-top pt-3">
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-primary" id="selectAllOptions">
                                <i class="fas fa-check-double"></i> {{ __('messages.select_all') }}
                            </button>
                            <button type="button" class="btn btn-outline-secondary" id="deselectAllOptions">
                                <i class="fas fa-times"></i> {{ __('messages.deselect_all') }}
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> {{ __('messages.cancel') }}
                    </button>
                    <button type="button" class="btn btn-success" id="confirmExport">
                        <i class="fas fa-file-excel"></i> {{ __('messages.export_report') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            // ============ Translations for Tables ============
            const trans = {
                productName: "{{ __('messages.product_name') }}",
                sku: "{{ __('messages.sku') }}",
                unitPrice: "{{ __('messages.unit_price') }}",
                totalQuantity: "{{ __('messages.total_quantity') }}",
                total: "{{ __('messages.total') }}",
                date: "{{ __('messages.date') ?? 'Date' }}",
                status: "{{ __('messages.status') ?? 'Status' }}",
                requestedQuantity: "{{ __('messages.requested_quantity') ?? 'Requested Quantity' }}",
                availableQuantity: "{{ __('messages.available_quantity') ?? 'Available Quantity' }}",
                price: "{{ __('messages.price') ?? 'Price' }}",
                tax: "{{ __('messages.tax') ?? 'Tax' }}",
                totalWithTax: "{{ __('messages.total_with_tax') ?? 'Total with Tax' }}",
                notes: "{{ __('messages.notes') ?? 'Notes' }}",
                noData: "{{ __('messages.no_data_available') ?? 'No data available' }}",
                warehouse: "{{ __('messages.warehouse') ?? 'Distribution Point' }}",
                quantity: "{{ __('messages.quantity') ?? 'Quantity' }}",
                revenue: "{{ __('messages.revenue') ?? 'Revenue' }}",
                amount: "{{ __('messages.amount') ?? 'Amount' }}"
            };

            let providerSearchTimer;
            const dropdown = $('#providers-dropdown');
            const searchInput = $('#provider_search');
            const searchBtn = $('#searchBtn');
            const providerIdInput = $('#provider_id');
            const providerInfoSection = $('#providerInfoSection');
            const noDataMessage = $('#noDataMessage');
            const loadingSpinner = $('#loadingSpinner');

            // Perform provider search
            function performProviderSearch(term) {
                $.ajax({
                    url: '{{ route('admin.providers.search') }}',
                    method: 'GET',
                    data: {
                        term: term,
                        limit: 10
                    },
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
                            });
                        } else {
                            dropdown.html(
                                '<div class="p-2 text-muted">{{ __('messages.no_results') }}</div>'
                            ).show();
                        }
                    },
                    error: function(xhr) {
                        console.error('Error searching providers:', xhr);
                        dropdown.html('<div class="p-2 text-danger">{{ __('messages.error') }}</div>')
                            .show();
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
                        title: '{{ __('messages.warning') }}',
                        text: '{{ __('messages.please_select_provider_first') }}',
                        confirmButtonText: '{{ __('messages.confirm') }}'
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

                $.ajax({
                    url: '{{ route('admin.providers.report.data', ':id') }}'.replace(':id', providerId),
                    method: 'GET',
                    data: {
                        from_date: fromDate,
                        to_date: toDate
                    },
                    success: function(response) {
                        if (response.success) {
                            populateProviderInfo(response);
                            loadBookRequestsData(providerId);
                            loadingSpinner.hide();
                            providerInfoSection.show();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: '{{ __('messages.error') }}',
                                text: response.message ||
                                    '{{ __('messages.error_loading_data') }}',
                                confirmButtonText: '{{ __('messages.confirm') }}'
                            });
                            loadingSpinner.hide();
                            noDataMessage.show();
                        }
                    },
                    error: function(xhr) {
                        console.error('Error loading provider data:', xhr);
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __('messages.error') }}',
                            text: '{{ __('messages.error_loading_data') }}',
                            confirmButtonText: '{{ __('messages.confirm') }}'
                        });
                        loadingSpinner.hide();
                        noDataMessage.show();
                    }
                });
            }

            // Load book requests data
            function loadBookRequestsData(providerId) {
                const fromDate = $('#from_date').val();
                const toDate = $('#to_date').val();

                $.ajax({
                    url: '{{ route('admin.reports.providers.bookRequests', ':id') }}'.replace(':id',
                        providerId),
                    method: 'GET',
                    data: {
                        from_date: fromDate,
                        to_date: toDate
                    },
                    success: function(response) {
                        if (response.success) {
                            populateBookRequestsInfo(response);
                        } else {
                            $('#bookRequestsTableContainer').html(
                                '<p class="text-center text-muted">{{ __('messages.no_data_available') }}</p>'
                            );
                        }
                    },
                    error: function(xhr) {
                        console.error('Error loading book requests:', xhr);
                        $('#bookRequestsTableContainer').html(
                            '<p class="text-center text-danger">{{ __('messages.error_loading_data') }}</p>'
                        );
                    }
                });
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
                                <th>${trans.productName}</th>
                                <th>${trans.requestedQuantity}</th>
                                <th>${trans.availableQuantity}</th>
                                <th>${trans.price}</th>
                                <th>${trans.tax}</th>
                                <th>${trans.totalWithTax}</th>
                                <th>${trans.status}</th>
                                <th>${trans.date}</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

                    const riyalIcon =
                        '<svg class="riyal-icon" style="width: 18px; height: 18px; display: inline-block; margin: 0 4px; vertical-align: middle;" viewBox="0 0 1124.14 1256.39" xmlns="http://www.w3.org/2000/svg"><path fill="currentColor" d="M699.62,1113.02h0c-20.06,44.48-33.32,92.75-38.4,143.37l424.51-90.24c20.06-44.47,33.31-92.75,38.4-143.37l-424.51,90.24Z"/><path fill="currentColor" d="M1085.73,895.8c20.06-44.47,33.32-92.75,38.4-143.37l-330.68,70.33v-135.2l292.27-62.11c20.06-44.47,33.32-92.75,38.4-143.37l-330.68,70.27V66.13c-50.67,28.45-95.67,66.32-132.25,110.99v403.35l-132.25,28.11V0c-50.67,28.44-95.67,66.32-132.25,110.99v525.69l-295.91,62.88c-20.06,44.47-33.33,92.75-38.42,143.37l334.33-71.05v170.26l-358.3,76.14c-20.06,44.47-33.32,92.75-38.4,143.37l375.04-79.7c30.53-6.35,56.77-24.4,73.83-49.24l68.78-101.97v-.02c7.14-10.55,11.3-23.27,11.3-36.97v-149.98l132.25-28.11v270.4l424.53-90.28Z"/></svg>';

                    requests.forEach(function(request, index) {
                        const statusBadge = request.status_badge;
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
                    $('#bookRequestsTableContainer').html(
                        '<p class="text-center text-muted">{{ __('messages.no_book_requests_for_this_provider') }}</p>'
                        );
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
                $('#provider-created-at').text(provider.created_at || '-');

                // Status Badge
                const statusClass = provider.activate == 1 ? 'success' : 'danger';
                const statusText = provider.activate == 1 ? '{{ __('messages.active') }}' :
                    '{{ __('messages.inactive') }}';
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
                                <th>${trans.productName}</th>
                                <th>${trans.sku}</th>
                                <th>${trans.unitPrice}</th>
                                <th>${trans.totalQuantity}</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

                    const riyalIcon =
                        '<svg class="riyal-icon" style="width: 18px; height: 18px; display: inline-block; margin: 0 4px; vertical-align: middle;" viewBox="0 0 1124.14 1256.39" xmlns="http://www.w3.org/2000/svg"><path fill="currentColor" d="M699.62,1113.02h0c-20.06,44.48-33.32,92.75-38.4,143.37l424.51-90.24c20.06-44.47,33.31-92.75,38.4-143.37l-424.51,90.24Z"/><path fill="currentColor" d="M1085.73,895.8c20.06-44.47,33.32-92.75,38.4-143.37l-330.68,70.33v-135.2l292.27-62.11c20.06-44.47,33.32-92.75,38.4-143.37l-330.68,70.27V66.13c-50.67,28.45-95.67,66.32-132.25,110.99v403.35l-132.25,28.11V0c-50.67,28.44-95.67,66.32-132.25,110.99v525.69l-295.91,62.88c-20.06,44.47-33.33,92.75-38.42,143.37l334.33-71.05v170.26l-358.3,76.14c-20.06,44.47-33.32,92.75-38.4,143.37l375.04-79.7c30.53-6.35,56.77-24.4,73.83-49.24l68.78-101.97v-.02c7.14-10.55,11.3-23.27,11.3-36.97v-149.98l132.25-28.11v270.4l424.53-90.28Z"/></svg>';

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
                                <td colspan="4" class="text-right">${trans.total}:</td>
                                <td><span class="badge badge-info">${stats.total_quantity}</span></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            `;

                    $('#productsTableContainer').html(productsHtml);
                } else {
                    $('#productsTableContainer').html(
                        '<p class="text-center text-muted">{{ __('messages.no_data_available') }}</p>');
                }
            }

            // Allow Enter key to search
            searchInput.on('keypress', function(e) {
                if (e.which == 13) {
                    e.preventDefault();
                    searchBtn.click();
                }
            });

            // Export Button Click - Show Modal
            $('#exportBtn').on('click', function(e) {
                const providerId = providerIdInput.val();
                if (!providerId) {
                    e.stopPropagation();
                    Swal.fire({
                        icon: 'warning',
                        title: '{{ __('messages.warning') }}',
                        text: '{{ __('messages.please_select_provider_first') }}',
                        confirmButtonText: '{{ __('messages.confirm') }}'
                    });
                    return false;
                }
            });

            // Section Toggle - Enable/Disable child options
            $('.section-toggle').on('change', function() {
                const section = $(this).data('section');
                const isChecked = $(this).is(':checked');
                $(`#${section}_options`).find('input[type="checkbox"]').prop('checked', isChecked);
                $(`#${section}_options`).toggleClass('text-muted', !isChecked);
            });

            // Select All Options
            $('#selectAllOptions').on('click', function() {
                $('#exportOptionsModal input[type="checkbox"]').prop('checked', true);
                $('#exportOptionsModal .card-body').removeClass('text-muted');
            });

            // Deselect All Options
            $('#deselectAllOptions').on('click', function() {
                $('#exportOptionsModal input[type="checkbox"]').prop('checked', false);
                $('#exportOptionsModal .card-body').addClass('text-muted');
            });

            // Confirm Export
            $('#confirmExport').on('click', function() {
                const providerId = providerIdInput.val();
                const fromDate = $('#from_date').val();
                const toDate = $('#to_date').val();

                // Collect export options
                const exportOptions = {
                    provider_info: $('#export_provider_info').is(':checked'),
                    provider_name: $('#export_provider_name').is(':checked'),
                    provider_email: $('#export_provider_email').is(':checked'),
                    provider_phone: $('#export_provider_phone').is(':checked'),
                    provider_country: $('#export_provider_country').is(':checked'),
                    provider_address: $('#export_provider_address').is(':checked'),
                    statistics: $('#export_statistics').is(':checked'),
                    total_products: $('#export_total_products').is(':checked'),
                    total_quantity: $('#export_total_quantity').is(':checked'),
                    total_revenue: $('#export_total_revenue').is(':checked'),
                    products: $('#export_products').is(':checked'),
                    purchases: $('#export_purchases').is(':checked'),
                    distribution: $('#export_distribution').is(':checked'),
                    sales: $('#export_sales').is(':checked'),
                    refunds: $('#export_refunds').is(':checked'),
                    sellers_payments: $('#export_sellers_payments').is(':checked'),
                    stock_balance: $('#export_stock_balance').is(':checked'),
                    book_requests: $('#export_book_requests').is(':checked'),
                    book_stats: $('#export_book_stats').is(':checked'),
                    book_details: $('#export_book_details').is(':checked'),
                };

                // Check if at least one option is selected
                const hasSelection = Object.values(exportOptions).some(v => v === true);
                if (!hasSelection) {
                    Swal.fire({
                        icon: 'warning',
                        title: '{{ __('messages.warning') }}',
                        text: '{{ __('messages.please_select_at_least_one_option') }}',
                        confirmButtonText: '{{ __('messages.confirm') }}'
                    });
                    return;
                }

                // Build URL with export options
                let url = '{{ route('admin.reports.providers.export') }}' +
                    '?provider_id=' + providerId +
                    '&from_date=' + fromDate +
                    '&to_date=' + toDate +
                    '&export_options=' + encodeURIComponent(JSON.stringify(exportOptions));

                // Close modal and download
                $('#exportOptionsModal').modal('hide');
                window.location.href = url;
            });

            // Display Options - Select All
            $('#selectAllDisplay').on('click', function() {
                $('#displayOptionsModal input[type="checkbox"]').prop('checked', true);
            });

            // Display Options - Deselect All
            $('#deselectAllDisplay').on('click', function() {
                $('#displayOptionsModal input[type="checkbox"]').prop('checked', false);
            });

            // Apply Display Options
            $('#applyDisplayOptions').on('click', function() {
                const displayOptions = {
                    provider_info: $('#display_provider_info').is(':checked'),
                    statistics: $('#display_statistics').is(':checked'),
                    products: $('#display_products').is(':checked'),
                    purchases: $('#display_purchases').is(':checked'),
                    distribution: $('#display_distribution').is(':checked'),
                    sales: $('#display_sales').is(':checked'),
                    refunds: $('#display_refunds').is(':checked'),
                    sellers_payments: $('#display_sellers_payments').is(':checked'),
                    stock_balance: $('#display_stock_balance').is(':checked'),
                    book_requests: $('#display_book_requests').is(':checked'),
                };

                // Store in window for print/export
                window.displayOptions = displayOptions;

                // Apply to page
                applyDisplayOptions(displayOptions);

                // Close modal
                $('#displayOptionsModal').modal('hide');

                // Show success message
                Swal.fire({
                    icon: 'success',
                    title: '{{ __('messages.done') }}',
                    text: '{{ __('messages.display_options_applied_successfully') }}',
                    timer: 1500,
                    showConfirmButton: false
                });
            });

            // Apply display options to page
            function applyDisplayOptions(options) {
                $('.provider-info-section').toggle(options.provider_info);
                $('.statistics-section').toggle(options.statistics);
                $('.products-section').toggle(options.products);
                $('.purchases-section').toggle(options.purchases);
                $('.distribution-section').toggle(options.distribution);
                $('.sales-section').toggle(options.sales);
                $('.refunds-section').toggle(options.refunds);
                $('.sellers-payments-section').toggle(options.sellers_payments);
                $('.stock-balance-section').toggle(options.stock_balance);
                $('.book-requests-section').toggle(options.book_requests);
            }

            // Print with display options
            $('#printBtn').on('click', function() {
                // Apply current display options before printing
                const displayOptions = {
                    provider_info: $('#display_provider_info').is(':checked'),
                    statistics: $('#display_statistics').is(':checked'),
                    products: $('#display_products').is(':checked'),
                    purchases: $('#display_purchases').is(':checked'),
                    distribution: $('#display_distribution').is(':checked'),
                    sales: $('#display_sales').is(':checked'),
                    refunds: $('#display_refunds').is(':checked'),
                    sellers_payments: $('#display_sellers_payments').is(':checked'),
                    stock_balance: $('#display_stock_balance').is(':checked'),
                    book_requests: $('#display_book_requests').is(':checked'),
                };
                applyDisplayOptions(displayOptions);
                window.print();
            });

            // Load Distribution Data
            function loadDistributionData(providerId) {
                const fromDate = $('#from_date').val();
                const toDate = $('#to_date').val();

                $.ajax({
                    url: '{{ route('admin.reports.providers.distribution', ':id') }}'.replace(':id',
                        providerId),
                    method: 'GET',
                    data: {
                        from_date: fromDate,
                        to_date: toDate
                    },
                    success: function(response) {
                        if (response.success && response.distributions) {
                            $('#dist-total-warehouses').text(response.summary.total_warehouses || 0);
                            $('#dist-total-quantity').text(response.summary.total_distributed || 0);
                            populateDistributionTable(response.distributions);
                        }
                    },
                    error: function(xhr) {
                        console.error('Error loading distribution data:', xhr);
                        $('#distributionTableContainer').html(
                            '<p class="text-center text-danger">{{ __('messages.error_loading_data') }}</p>'
                        );
                    }
                });
            }

            // Populate Distribution Table
            function populateDistributionTable(data) {
                if (data.length === 0) {
                    $('#distributionTableContainer').html(
                        '<p class="text-center text-muted">{{ __('messages.no_data_available') }}</p>');
                    return;
                }

                let html = `
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>{{ __('messages.distribution_point_name') }}</th>
                            <th>{{ __('messages.product') }}</th>
                            <th>{{ __('messages.quantity') }}</th>
                            <th>{{ __('messages.date') }}</th>
                            <th>{{ __('messages.voucher_number') }}</th>
                        </tr>
                    </thead>
                    <tbody>
        `;

                data.forEach(function(item, index) {
                    html += `
                <tr>
                    <td>${index + 1}</td>
                    <td><strong>${item.warehouse_name}</strong></td>
                    <td>${item.product_name}</td>
                    <td><span class="badge badge-info">${item.quantity}</span></td>
                    <td>${item.date}</td>
                    <td><small class="text-muted">${item.note_voucher_number}</small></td>
                </tr>
            `;
                });

                html += `
                    </tbody>
                </table>
            </div>
        `;

                $('#distributionTableContainer').html(html);
            }

            // Load Sales by Warehouse Data
            function loadSalesByWarehouse(providerId) {
                const fromDate = $('#from_date').val();
                const toDate = $('#to_date').val();

                $.ajax({
                    url: '{{ route('admin.reports.providers.salesByWarehouse', ':id') }}'.replace(':id',
                        providerId),
                    method: 'GET',
                    data: {
                        from_date: fromDate,
                        to_date: toDate
                    },
                    success: function(response) {
                        if (response.success && response.sales) {
                            $('#sales-total-quantity').text(response.summary.total_sold || 0);
                            $('#sales-total-revenue').text(parseFloat(response.summary.total_revenue ||
                                0).toFixed(2));
                            populateSalesTable(response.sales);
                        }
                    },
                    error: function(xhr) {
                        console.error('Error loading sales data:', xhr);
                        $('#salesTableContainer').html(
                            '<p class="text-center text-danger">{{ __('messages.error_loading_data') }}</p>'
                        );
                    }
                });
            }

            // Populate Sales Table
            function populateSalesTable(data) {
                if (data.length === 0) {
                    $('#salesTableContainer').html(
                        '<p class="text-center text-muted">{{ __('messages.no_data_available') }}</p>');
                    return;
                }

                const riyalIcon =
                    '<svg class="riyal-icon" style="width: 14px; height: 14px; display: inline-block; margin: 0 2px; vertical-align: middle;" viewBox="0 0 1124.14 1256.39" xmlns="http://www.w3.org/2000/svg"><path fill="currentColor" d="M699.62,1113.02h0c-20.06,44.48-33.32,92.75-38.4,143.37l424.51-90.24c20.06-44.47,33.31-92.75,38.4-143.37l-424.51,90.24Z"/><path fill="currentColor" d="M1085.73,895.8c20.06-44.47,33.32-92.75,38.4-143.37l-330.68,70.33v-135.2l292.27-62.11c20.06-44.47,33.32-92.75,38.4-143.37l-330.68,70.27V66.13c-50.67,28.45-95.67,66.32-132.25,110.99v403.35l-132.25,28.11V0c-50.67,28.44-95.67,66.32-132.25,110.99v525.69l-295.91,62.88c-20.06,44.47-33.33,92.75-38.42,143.37l334.33-71.05v170.26l-358.3,76.14c-20.06,44.47-33.32,92.75-38.4,143.37l375.04-79.7c30.53-6.35,56.77-24.4,73.83-49.24l68.78-101.97v-.02c7.14-10.55,11.3-23.27,11.3-36.97v-149.98l132.25-28.11v270.4l424.53-90.28Z"/></svg>';

                let html = `
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>{{ __('messages.distribution_point') }}</th>
                            <th>{{ __('messages.product') }}</th>
                            <th>{{ __('messages.quantity') }}</th>
                            <th>{{ __('messages.revenue') }}</th>
                            <th>{{ __('messages.date') }}</th>
                            <th>{{ __('messages.order_number') }}</th>
                        </tr>
                    </thead>
                    <tbody>
        `;

                data.forEach(function(item, index) {
                    html += `
                <tr>
                    <td>${index + 1}</td>
                    <td><strong>${item.warehouse_name}</strong></td>
                    <td>${item.product_name}</td>
                    <td><span class="badge badge-info">${item.quantity_sold}</span></td>
                    <td>${parseFloat(item.revenue).toFixed(2)} ${riyalIcon}</td>
                    <td>${item.date}</td>
                    <td><small class="text-muted">${item.order_number}</small></td>
                </tr>
            `;
                });

                html += `
                    </tbody>
                </table>
            </div>
        `;

                $('#salesTableContainer').html(html);
            }

            // Load Refunds Data
            function loadRefundsData(providerId) {
                const fromDate = $('#from_date').val();
                const toDate = $('#to_date').val();

                $.ajax({
                    url: '{{ route('admin.reports.providers.refunds', ':id') }}'.replace(':id',
                        providerId),
                    method: 'GET',
                    data: {
                        from_date: fromDate,
                        to_date: toDate
                    },
                    success: function(response) {
                        if (response.success && response.refunds) {
                            $('#refunds-total-quantity').text(response.summary.total_returned || 0);
                            $('#refunds-total-amount').text(parseFloat(response.summary.total_amount ||
                                0).toFixed(2));
                            populateRefundsTable(response.refunds);
                        }
                    },
                    error: function(xhr) {
                        console.error('Error loading refunds data:', xhr);
                        $('#refundsTableContainer').html(
                            '<p class="text-center text-danger">{{ __('messages.error_loading_data') }}</p>'
                        );
                    }
                });
            }

            // Populate Refunds Table
            function populateRefundsTable(data) {
                if (data.length === 0) {
                    $('#refundsTableContainer').html(
                        '<p class="text-center text-muted">{{ __('messages.no_data_available') }}</p>');
                    return;
                }

                const riyalIcon =
                    '<svg class="riyal-icon" style="width: 14px; height: 14px; display: inline-block; margin: 0 2px; vertical-align: middle;" viewBox="0 0 1124.14 1256.39" xmlns="http://www.w3.org/2000/svg"><path fill="currentColor" d="M699.62,1113.02h0c-20.06,44.48-33.32,92.75-38.4,143.37l424.51-90.24c20.06-44.47,33.31-92.75,38.4-143.37l-424.51,90.24Z"/><path fill="currentColor" d="M1085.73,895.8c20.06-44.47,33.32-92.75,38.4-143.37l-330.68,70.33v-135.2l292.27-62.11c20.06-44.47,33.32-92.75,38.4-143.37l-330.68,70.27V66.13c-50.67,28.45-95.67,66.32-132.25,110.99v403.35l-132.25,28.11V0c-50.67,28.44-95.67,66.32-132.25,110.99v525.69l-295.91,62.88c-20.06,44.47-33.33,92.75-38.42,143.37l334.33-71.05v170.26l-358.3,76.14c-20.06,44.47-33.32,92.75-38.4,143.37l375.04-79.7c30.53-6.35,56.77-24.4,73.83-49.24l68.78-101.97v-.02c7.14-10.55,11.3-23.27,11.3-36.97v-149.98l132.25-28.11v270.4l424.53-90.28Z"/></svg>';

                let html = `
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>{{ __('messages.distribution_point') }}</th>
                            <th>{{ __('messages.product') }}</th>
                            <th>{{ __('messages.quantity') }}</th>
                            <th>{{ __('messages.amount') }}</th>
                            <th>{{ __('messages.date') }}</th>
                            <th>{{ __('messages.order_number') }}</th>
                        </tr>
                    </thead>
                    <tbody>
        `;

                data.forEach(function(item, index) {
                    html += `
                <tr>
                    <td>${index + 1}</td>
                    <td><strong>${item.warehouse_name}</strong></td>
                    <td>${item.product_name}</td>
                    <td><span class="badge badge-warning">${item.quantity_returned}</span></td>
                    <td>${parseFloat(item.amount).toFixed(2)} ${riyalIcon}</td>
                    <td>${item.date}</td>
                    <td><small class="text-muted">${item.order_number}</small></td>
                </tr>
            `;
                });

                html += `
                    </tbody>
                </table>
            </div>
        `;

                $('#refundsTableContainer').html(html);
            }

            // Load Sellers Payments Data
            function loadSellersPaymentsData(providerId) {
                $.ajax({
                    url: '{{ route('admin.reports.providers.sellersPayments', ':id') }}'.replace(':id',
                        providerId),
                    method: 'GET',
                    success: function(response) {
                        if (response.success && response.payments) {
                            $('#payments-total-amount').text(parseFloat(response.summary
                                .total_orders_amount || 0).toFixed(2));
                            $('#payments-total-paid').text(parseFloat(response.summary.total_paid || 0)
                                .toFixed(2));
                            $('#payments-total-remaining').text(parseFloat(response.summary
                                .total_remaining || 0).toFixed(2));
                            populatePaymentsTable(response.payments);
                        }
                    },
                    error: function(xhr) {
                        console.error('Error loading payments data:', xhr);
                        $('#sellersPaymentsTableContainer').html(
                            '<p class="text-center text-danger">{{ __('messages.error_loading_data') }}</p>'
                        );
                    }
                });
            }

            // Populate Payments Table
            function populatePaymentsTable(data) {
                if (data.length === 0) {
                    $('#sellersPaymentsTableContainer').html(
                        '<p class="text-center text-muted">{{ __('messages.no_data_available') }}</p>');
                    return;
                }

                const riyalIcon =
                    '<svg class="riyal-icon" style="width: 14px; height: 14px; display: inline-block; margin: 0 2px; vertical-align: middle;" viewBox="0 0 1124.14 1256.39" xmlns="http://www.w3.org/2000/svg"><path fill="currentColor" d="M699.62,1113.02h0c-20.06,44.48-33.32,92.75-38.4,143.37l424.51-90.24c20.06-44.47,33.31-92.75,38.4-143.37l-424.51,90.24Z"/><path fill="currentColor" d="M1085.73,895.8c20.06-44.47,33.32-92.75,38.4-143.37l-330.68,70.33v-135.2l292.27-62.11c20.06-44.47,33.32-92.75,38.4-143.37l-330.68,70.27V66.13c-50.67,28.45-95.67,66.32-132.25,110.99v403.35l-132.25,28.11V0c-50.67,28.44-95.67,66.32-132.25,110.99v525.69l-295.91,62.88c-20.06,44.47-33.33,92.75-38.42,143.37l334.33-71.05v170.26l-358.3,76.14c-20.06,44.47-33.32,92.75-38.4,143.37l375.04-79.7c30.53-6.35,56.77-24.4,73.83-49.24l68.78-101.97v-.02c7.14-10.55,11.3-23.27,11.3-36.97v-149.98l132.25-28.11v270.4l424.53-90.28Z"/></svg>';

                let html = `
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>{{ __('messages.distribution_point') }}</th>
                            <th>{{ __('messages.total_quantity') }}</th>
                            <th>{{ __('messages.paid') }}</th>
                            <th>{{ __('messages.remaining') }}</th>
                            <th>{{ __('messages.status') }}</th>
                            <th>{{ __('messages.last_order') }}</th>
                        </tr>
                    </thead>
                    <tbody>
        `;

                data.forEach(function(item, index) {
                    const statusBadge = item.payment_status === 'paid' ?
                        '<span class="badge badge-success">{{ __('messages.paid') }}</span>' :
                        '<span class="badge badge-warning">{{ __('messages.in_debt') }}</span>';

                    html += `
                <tr>
                    <td>${index + 1}</td>
                    <td><strong>${item.seller_name}</strong></td>
                    <td>${parseFloat(item.total_orders_amount).toFixed(2)}</td>
                    <td><span class="badge badge-success">${parseFloat(item.paid_amount).toFixed(2)}</span></td>
                    <td><span class="badge badge-danger">${parseFloat(item.remaining_amount).toFixed(2)}</span></td>
                    <td>${statusBadge}</td>
                    <td>${item.last_order_date}</td>
                </tr>
            `;
                });

                html += `
                    </tbody>
                </table>
            </div>
        `;

                $('#sellersPaymentsTableContainer').html(html);
            }

            // Load Stock Balance Data
            function loadStockBalanceData(providerId) {
                $.ajax({
                    url: '{{ route('admin.reports.providers.stockBalance', ':id') }}'.replace(':id',
                        providerId),
                    method: 'GET',
                    success: function(response) {
                        if (response.success && response.stock) {
                            $('#stock-total-remaining').text(response.summary.total_remaining || 0);
                            populateStockBalanceTable(response.stock);
                        }
                    },
                    error: function(xhr) {
                        console.error('Error loading stock balance data:', xhr);
                        $('#stockBalanceTableContainer').html(
                            '<p class="text-center text-danger">{{ __('messages.error_loading_data') }}</p>'
                            );
                    }
                });
            }

            // Populate Stock Balance Table
            function populateStockBalanceTable(data) {
                if (data.length === 0) {
                    $('#stockBalanceTableContainer').html(
                        '<p class="text-center text-muted">{{ __('messages.no_data_available') }}</p>');
                    return;
                }

                let html = `
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>{{ __('messages.distribution_point') }}</th>
                            <th>{{ __('messages.product') }}</th>
                            <th>{{ __('messages.distributed') }}</th>
                            <th>{{ __('messages.sold') }}</th>
                            <th>{{ __('messages.returned') }}</th>
                            <th>{{ __('messages.remaining') }}</th>
                        </tr>
                    </thead>
                    <tbody>
        `;

                data.forEach(function(item, index) {
                    const remainingBadge = item.quantity_remaining > 0 ?
                        '<span class="badge badge-info">' + item.quantity_remaining + '</span>' :
                        '<span class="badge badge-secondary">0</span>';

                    html += `
                <tr>
                    <td>${index + 1}</td>
                    <td><strong>${item.warehouse_name}</strong></td>
                    <td>${item.product_name}</td>
                    <td><span class="badge badge-primary">${item.quantity_distributed}</span></td>
                    <td><span class="badge badge-success">${item.quantity_sold}</span></td>
                    <td><span class="badge badge-warning">${item.quantity_returned}</span></td>
                    <td>${remainingBadge}</td>
                </tr>
            `;
                });

                html += `
                    </tbody>
                </table>
            </div>
        `;

                $('#stockBalanceTableContainer').html(html);
            }

            // Load Purchases Data
            function loadPurchasesData(providerId) {
                const fromDate = $('#from_date').val();
                const toDate = $('#to_date').val();

                $.ajax({
                    url: '{{ route('admin.reports.providers.purchases', ':id') }}'.replace(':id',
                        providerId),
                    method: 'GET',
                    data: {
                        from_date: fromDate,
                        to_date: toDate
                    },
                    success: function(response) {
                        if (response.success && response.purchases) {
                            populatePurchasesTable(response.purchases);
                        }
                    },
                    error: function(xhr) {
                        console.error('Error loading purchases data:', xhr);
                        $('#purchasesTableContainer').html(
                            '<p class="text-center text-danger">{{ __('messages.error_loading_data') }}</p>'
                        );
                    }
                });
            }

            // Populate Purchases Table
            function populatePurchasesTable(data) {
                if (data.length === 0) {
                    $('#purchasesTableContainer').html(
                        '<p class="text-center text-muted">{{ __('messages.no_data_available') }}</p>');
                    return;
                }

                const riyalIcon =
                    '<svg class="riyal-icon" style="width: 14px; height: 14px; display: inline-block; margin: 0 2px; vertical-align: middle;" viewBox="0 0 1124.14 1256.39" xmlns="http://www.w3.org/2000/svg"><path fill="currentColor" d="M699.62,1113.02h0c-20.06,44.48-33.32,92.75-38.4,143.37l424.51-90.24c20.06-44.47,33.31-92.75,38.4-143.37l-424.51,90.24Z"/><path fill="currentColor" d="M1085.73,895.8c20.06-44.47,33.32-92.75,38.4-143.37l-330.68,70.33v-135.2l292.27-62.11c20.06-44.47,33.32-92.75,38.4-143.37l-330.68,70.27V66.13c-50.67,28.45-95.67,66.32-132.25,110.99v403.35l-132.25,28.11V0c-50.67,28.44-95.67,66.32-132.25,110.99v525.69l-295.91,62.88c-20.06,44.47-33.33,92.75-38.42,143.37l334.33-71.05v170.26l-358.3,76.14c-20.06,44.47-33.32,92.75-38.4,143.37l375.04-79.7c30.53-6.35,56.77-24.4,73.83-49.24l68.78-101.97v-.02c7.14-10.55,11.3-23.27,11.3-36.97v-149.98l132.25-28.11v270.4l424.53-90.28Z"/></svg>';

                let html = `
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>{{ __('messages.purchase_number') }}</th>
                            <th>{{ __('messages.total_amount') }}</th>
                            <th>{{ __('messages.tax') }}</th>
                            <th>{{ __('messages.status') }}</th>
                            <th>{{ __('messages.date') }}</th>
                        </tr>
                    </thead>
                    <tbody>
        `;

                data.forEach(function(item, index) {
                    html += `
                <tr>
                    <td>${index + 1}</td>
                    <td><strong>${item.purchase_number}</strong></td>
                    <td>${parseFloat(item.total_amount).toFixed(2)} ${riyalIcon}</td>
                    <td>${parseFloat(item.total_tax).toFixed(2)} ${riyalIcon}</td>
                    <td><span class="badge badge-secondary">${item.status}</span></td>
                    <td>${item.created_at}</td>
                </tr>
            `;
                });

                html += `
                    </tbody>
                </table>
            </div>
        `;

                $('#purchasesTableContainer').html(html);
            }

            // Update loadProviderData to load all sections
            const originalLoadProviderData = loadProviderData;

            function newLoadProviderData(providerId) {
                originalLoadProviderData(providerId);
                loadPurchasesData(providerId);
                loadDistributionData(providerId);
                loadSalesByWarehouse(providerId);
                loadRefundsData(providerId);
                loadSellersPaymentsData(providerId);
                loadStockBalanceData(providerId);
            }

            // Override search button to load all data
            searchBtn.off('click').on('click', function() {
                const providerId = providerIdInput.val();

                if (!providerId) {
                    Swal.fire({
                        icon: 'warning',
                        title: '{{ __('messages.warning') }}',
                        text: '{{ __('messages.please_select_provider_first') }}',
                        confirmButtonText: '{{ __('messages.confirm') }}'
                    });
                    return;
                }

                newLoadProviderData(providerId);
            });
        });
    </script>
@endsection
