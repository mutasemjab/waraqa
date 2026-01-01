@extends('layouts.admin')
@section('title')
{{ __('messages.noteVouchers') }} {{ __('messages.Report') }}
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
        <h3 class="card-title card_title_center">{{ __('messages.noteVouchers') }} {{ __('messages.Report') }}</h3>
    </div>

    <div class="card-body">
        <!-- Filters Section -->
        <div class="row mb-4 no-print">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('messages.Filters') }}</h3>
                    </div>
                    <form method="GET" action="{{ route('admin.reports.noteVouchers') }}" class="form-horizontal">
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

                                <!-- Note Voucher Type -->
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="note_voucher_type_id">{{ __('messages.noteVoucherTypes') }}</label>
                                        <select class="form-control select2" id="note_voucher_type_id" name="note_voucher_type_id">
                                            <option value="">{{ __('messages.All') }}</option>
                                            @foreach ($noteVoucherTypes as $type)
                                            <option value="{{ $type->id }}" {{ request('note_voucher_type_id') == $type->id ? 'selected' : '' }}>
                                                {{ $type->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Warehouse -->
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="warehouse_id">{{ __('messages.Warehouse') }}</label>
                                        <select class="form-control select2" id="warehouse_id" name="warehouse_id">
                                            <option value="">{{ __('messages.All') }}</option>
                                            @foreach ($warehouses as $warehouse)
                                            <option value="{{ $warehouse->id }}" {{ request('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                                {{ $warehouse->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Provider -->
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="provider_id">{{ __('messages.Provider') }}</label>
                                        <select class="form-control select2" id="provider_id" name="provider_id">
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
                                        <a href="{{ route('admin.reports.noteVouchers') }}" class="btn btn-secondary btn-block">
                                            <i class="fas fa-redo"></i> {{ __('messages.Reset') }}
                                        </a>
                                    </div>
                                </div>

                                <div class="col-md-3 d-flex align-items-end">
                                    <div class="form-group" style="width: 100%;">
                                        <button type="submit" name="export" value="excel" class="btn btn-success btn-block">
                                            <i class="fas fa-file-excel"></i> {{ __('messages.Export') }} Excel
                                        </button>
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
                    <span class="info-box-icon bg-info"><i class="fas fa-file-invoice"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">{{ __('messages.Total') }} {{ __('messages.noteVouchers') }}</span>
                        <span class="info-box-number">{{ $statistics['total_vouchers'] }}</span>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-warning"><i class="fas fa-cube"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">{{ __('messages.Total') }} {{ __('messages.Quantity') }}</span>
                        <span class="info-box-number">{{ number_format($statistics['total_quantity'], 2) }}</span>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-success"><i class="fas fa-money-bill"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">{{ __('messages.Total') }} {{ __('messages.Value') }}</span>
                        <span class="info-box-number">{{ number_format($statistics['total_value'], 2) }} <x-riyal-icon /></span>
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
                        @if($data->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="custom_thead">
                                    <tr>
                                        <th>{{ __('messages.number') }}</th>
                                        <th>{{ __('messages.date_note_voucher') }}</th>
                                        <th>{{ __('messages.noteVoucherTypes') }}</th>
                                        <th>{{ __('messages.Provider') }}</th>
                                        <th>{{ __('messages.Warehouse') }}</th>
                                        <th>{{ __('messages.Quantity') }}</th>
                                        <th>{{ __('messages.Value') }}</th>
                                        <th>{{ __('messages.note') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($data as $voucher)
                                    @php
                                    $voucher_quantity = 0;
                                    $voucher_value = 0;
                                    foreach($voucher->voucherProducts as $voucherProduct) {
                                        $quantity = $voucherProduct->quantity ?? 0;
                                        // Use purchasing_price if available, otherwise use product's selling_price
                                        $price = $voucherProduct->purchasing_price ?? ($voucherProduct->product->selling_price ?? 0);
                                        $voucher_quantity += $quantity;
                                        $voucher_value += $quantity * $price;
                                    }
                                    @endphp
                                    <tr>
                                        <td>{{ $voucher->number }}</td>
                                        <td>{{ $voucher->date_note_voucher->format('Y-m-d') }}</td>
                                        <td>{{ $voucher->noteVoucherType->name }}</td>
                                        <td>{{ $voucher->provider->name ?? '-' }}</td>
                                        <td>
                                            @if($voucher->fromWarehouse)
                                            <strong>من:</strong> {{ $voucher->fromWarehouse->name }}<br>
                                            @endif
                                            @if($voucher->toWarehouse)
                                            <strong>إلى:</strong> {{ $voucher->toWarehouse->name }}<br>
                                            @endif
                                        </td>
                                        <td>{{ number_format($voucher_quantity, 2) }}</td>
                                        <td>{{ number_format($voucher_value, 2) }} <x-riyal-icon /></td>
                                        <td>{{ substr($voucher->note ?? '-', 0, 50) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
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

        <!-- Statistics by Type -->
        <div class="row no-print">
            <div class="col-md-6">
                <div class="card card-default">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('messages.Statistics by') }} {{ __('messages.noteVoucherTypes') }}</h3>
                    </div>
                    <div class="card-body">
                        @if(count($statistics['by_type']) > 0)
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>{{ __('messages.Type') }}</th>
                                    <th>{{ __('messages.Count') }}</th>
                                    <th>{{ __('messages.Quantity') }}</th>
                                    <th>{{ __('messages.Value') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($statistics['by_type'] as $type => $stats)
                                <tr>
                                    <td>{{ $type }}</td>
                                    <td>{{ $stats['count'] }}</td>
                                    <td>{{ number_format($stats['quantity'], 2) }}</td>
                                    <td>{{ number_format($stats['value'], 2) }} <x-riyal-icon /></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @else
                        <div class="alert alert-info">{{ __('messages.No_data') }}</div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Statistics by Provider -->
            <div class="col-md-6">
                <div class="card card-default">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('messages.Statistics by') }} {{ __('messages.Provider') }}</h3>
                    </div>
                    <div class="card-body">
                        @if(count($statistics['by_provider']) > 0)
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>{{ __('messages.Provider') }}</th>
                                    <th>{{ __('messages.Count') }}</th>
                                    <th>{{ __('messages.Quantity') }}</th>
                                    <th>{{ __('messages.Value') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($statistics['by_provider'] as $provider => $stats)
                                <tr>
                                    <td>{{ $provider }}</td>
                                    <td>{{ $stats['count'] }}</td>
                                    <td>{{ number_format($stats['quantity'], 2) }}</td>
                                    <td>{{ number_format($stats['value'], 2) }} <x-riyal-icon /></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
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

@section('script')
<script>
    $(document).ready(function() {
        $('.select2').select2({
            language: {
                noResults: function() {
                    return "لا توجد نتائج";
                }
            }
        });
    });
</script>
@endsection