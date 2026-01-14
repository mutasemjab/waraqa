@extends('layouts.admin')
@section('title')
{{ __('messages.Warehouse') }} {{ __('messages.Report') }}
@endsection

@section('content')
<style>
    @media print {
        /* Hide everything except printable content */
        body * {
            visibility: hidden;
        }

        /* Show only the printable section */
        .printable-section,
        .printable-section * {
            visibility: visible;
        }

        /* Position printable section at top of page */
        .printable-section {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }

        /* Hide buttons, filters, and navigation */
        .no-print,
        .btn,
        .card-header .card-title,
        nav,
        .main-sidebar,
        .main-header,
        footer,
        .content-header,
        .breadcrumb {
            display: none !important;
        }

        /* Clean table styling for print */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th,
        table td {
            border: 1px solid #000;
            padding: 8px;
            font-size: 12px;
        }

        /* Make sure table header is visible */
        thead,
        thead *,
        tbody,
        tbody * {
            visibility: visible !important;
        }

        /* Style table header for print */
        .custom_thead,
        .custom_thead th {
            background-color: #007bff !important;
            color: white !important;
            font-weight: bold !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .card {
            border: none;
            box-shadow: none;
        }

        /* Page breaks */
        .page-break {
            page-break-after: always;
        }

        /* Hide card headers in print */
        .card-header {
            display: none !important;
        }
    }
</style>

<div class="card">
    <div class="card-header">
        <h3 class="card-title card_title_center">{{ __('messages.Warehouse') }} {{ __('messages.Report') }}</h3>
    </div>

    <div class="card-body">
        <!-- Filters Section -->
        <div class="row mb-4 no-print">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('messages.Filters') }}</h3>
                    </div>
                    <form method="GET" action="{{ route('admin.reports.warehouseMovement') }}" class="form-horizontal">
                        <div class="card-body">
                            <div class="row">
                                <!-- From Date -->
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="from_date">{{ __('messages.from_date') }}</label>
                                        <input type="date" class="form-control" id="from_date" name="from_date"
                                            value="{{ request('from_date') }}">
                                    </div>
                                </div>

                                <!-- To Date -->
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="to_date">{{ __('messages.to_date') }}</label>
                                        <input type="date" class="form-control" id="to_date" name="to_date"
                                            value="{{ request('to_date') }}">
                                    </div>
                                </div>

                                <!-- Warehouse -->
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="warehouse_id">{{ __('messages.Warehouse') }}</label>
                                        <select class="form-control" id="warehouse_id" name="warehouse_id">
                                            <option value="">{{ __('messages.All') }}</option>
                                            @foreach ($warehouses as $warehouse)
                                            <option value="{{ $warehouse->id }}" {{ request('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                                {{ $warehouse->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Product -->
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="product_id">{{ __('messages.Product') }}</label>
                                        <select class="form-control" id="product_id" name="product_id">
                                            <option value="">{{ __('messages.All') }}</option>
                                            @foreach ($products as $product)
                                            <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                                {{ $product->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Provider -->
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="provider_id">{{ __('messages.Provider') }}</label>
                                        <select class="form-control" id="provider_id" name="provider_id">
                                            <option value="">{{ __('messages.All') }}</option>
                                            @foreach ($providers as $provider)
                                            <option value="{{ $provider->id }}" {{ request('provider_id') == $provider->id ? 'selected' : '' }}>
                                                {{ $provider->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="col-md-3 d-flex align-items-end">
                                    <div class="form-group" style="width: 100%;">
                                        <button type="submit" class="btn btn-primary btn-block">
                                            <i class="fas fa-search"></i> {{ __('messages.Search') }}
                                        </button>
                                    </div>
                                </div>

                                <div class="col-md-3 d-flex align-items-end">
                                    <div class="form-group" style="width: 100%;">
                                        <a href="{{ route('admin.reports.warehouseMovement') }}" class="btn btn-secondary btn-block">
                                            <i class="fas fa-redo"></i> {{ __('messages.Reset') }}
                                        </a>
                                    </div>
                                </div>

                                <div class="col-md-3 d-flex align-items-end">
                                    <div class="form-group" style="width: 100%;">
                                        <button type="button" class="btn btn-info btn-block" onclick="window.print()">
                                            <i class="fas fa-print"></i> {{ __('messages.print') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Statistics Section -->
        <div class="row mb-4 no-print">
            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="fas fa-boxes"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">{{ __('messages.Total') }} {{ __('messages.Movements') }}</span>
                        <span class="info-box-number">{{ $statistics['total_movements'] }}</span>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-success"><i class="fas fa-arrow-down"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">{{ __('messages.total_quantity_in') }}</span>
                        <span class="info-box-number">{{ number_format($statistics['total_quantity_in'], 2) }}</span>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-warning"><i class="fas fa-arrow-up"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">{{ __('messages.total_quantity_out') }}</span>
                        <span class="info-box-number">{{ number_format($statistics['total_quantity_out'], 2) }}</span>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-danger"><i class="fas fa-chart-line"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">{{ __('messages.Total') }} {{ __('messages.Value') }}</span>
                        <span class="info-box-number">{{ number_format($statistics['total_value_in'] + $statistics['total_value_out'], 2) }} <x-riyal-icon /></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Table -->
        <div class="row mb-4 printable-section">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header no-print">
                        <h3 class="card-title">{{ __('messages.Details') }}</h3>
                    </div>
                    <div class="card-body">
                        @if($movements->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="custom_thead">
                                    <tr>
                                        <th>{{ __('messages.date_note_voucher') }}</th>
                                        <th>{{ __('messages.Type') }}</th>
                                        <th>{{ __('messages.Product') }}</th>
                                        <th>{{ __('messages.Quantity') }}</th>
                                        <th>{{ __('messages.Price') }}</th>
                                        <th>{{ __('messages.Value') }}</th>
                                        <th>{{ __('messages.Warehouse') }}</th>
                                        <th>{{ __('messages.Provider') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($movements as $movement)
                                    @foreach($movement->voucherProducts as $voucherProduct)
                                    <tr>
                                        <td>{{ $movement->date_note_voucher->format('Y-m-d') }}</td>
                                        <td>{{ $movement->noteVoucherType->name ?? '-' }}</td>
                                        <td>{{ $voucherProduct->product->name ?? '-' }}</td>
                                        <td>{{ number_format($voucherProduct->quantity ?? 0, 2) }}</td>
                                        <td>{{ number_format($voucherProduct->purchasing_price ?? ($voucherProduct->product->selling_price ?? 0), 2) }} <x-riyal-icon /></td>
                                        <td>{{ number_format(($voucherProduct->quantity ?? 0) * ($voucherProduct->purchasing_price ?? ($voucherProduct->product->selling_price ?? 0)), 2) }} <x-riyal-icon /></td>
                                        <td>
                                            @if($movement->fromWarehouse)
                                            <span class="badge badge-primary">{{ $movement->fromWarehouse->name }}</span>
                                            @elseif($movement->toWarehouse)
                                            <span class="badge badge-primary">{{ $movement->toWarehouse->name }}</span>
                                            @else
                                            <span class="badge badge-secondary">-</span>
                                            @endif
                                        </td>
                                        <td>{{ $movement->provider->name ?? '-' }}</td>
                                    </tr>
                                    @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="row mt-3 no-print">
                            <div class="col-md-12">
                                {{ $movements->links() }}
                            </div>
                        </div>
                        @else
                        <div class="alert alert-info">
                            {{ __('messages.No_data') }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics by Product -->
        <div class="row no-print">
            <div class="col-md-6">
                <div class="card card-default">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('messages.Statistics by') }} {{ __('messages.Product') }}</h3>
                    </div>
                    <div class="card-body">
                        @if(count($statistics['by_product']) > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead>
                                    <tr>
                                        <th>{{ __('messages.Product') }}</th>
                                        <th>{{ __('messages.qty_in') }}</th>
                                        <th>{{ __('messages.qty_out') }}</th>
                                        <th>{{ __('messages.value_in') }}</th>
                                        <th>{{ __('messages.value_out') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($statistics['by_product'] as $product => $stats)
                                    <tr>
                                        <td>{{ $product }}</td>
                                        <td>{{ number_format($stats['quantity_in'], 2) }}</td>
                                        <td>{{ number_format($stats['quantity_out'], 2) }}</td>
                                        <td>{{ number_format($stats['value_in'], 2) }} <x-riyal-icon /></td>
                                        <td>{{ number_format($stats['value_out'], 2) }} <x-riyal-icon /></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="alert alert-info">{{ __('messages.No_data') }}</div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Statistics by Warehouse -->
            <div class="col-md-6">
                <div class="card card-default">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('messages.Statistics by') }} {{ __('messages.Warehouse') }}</h3>
                    </div>
                    <div class="card-body">
                        @if(count($statistics['by_warehouse']) > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead>
                                    <tr>
                                        <th>{{ __('messages.Warehouse') }}</th>
                                        <th>{{ __('messages.Incoming') }}</th>
                                        <th>{{ __('messages.Outgoing') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($statistics['by_warehouse'] as $warehouse => $stats)
                                    <tr>
                                        <td>{{ $warehouse }}</td>
                                        <td>{{ $stats['incoming'] }}</td>
                                        <td>{{ $stats['outgoing'] }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="alert alert-info">{{ __('messages.No_data') }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection