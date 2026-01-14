@extends('layouts.admin')
@section('title')
{{ __('messages.purchases_report') }}
@endsection

@section('content')
<style>
    @media print {
        body * {
            visibility: hidden;
        }

        .printable-section,
        .printable-section * {
            visibility: visible;
        }

        .printable-section {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }

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

        thead,
        thead *,
        tbody,
        tbody * {
            visibility: visible !important;
        }

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

        .page-break {
            page-break-after: always;
        }

        .card-header {
            display: none !important;
        }
    }
</style>

<div class="card">
    <div class="card-header">
        <h3 class="card-title card_title_center">{{ __('messages.purchases_report') }}</h3>
    </div>

    <div class="card-body">
        <!-- Filters Section -->
        <div class="row mb-4 no-print">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('messages.Filters') }}</h3>
                    </div>
                    <form method="GET" action="{{ route('admin.reports.purchases') }}" class="form-horizontal">
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

                                <!-- Provider -->
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="provider_id">{{ __('messages.provider') }}</label>
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

                                <!-- Warehouse -->
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="warehouse_id">{{ __('messages.warehouse') }}</label>
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

                                <!-- Status -->
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="status">{{ __('messages.Status') }}</label>
                                        <select class="form-control select2" id="status" name="status">
                                            <option value="">{{ __('messages.All') }}</option>
                                            @foreach ($statuses as $status)
                                            <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                                {{ __('messages.purchase_status_' . $status) }}
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
                                        <a href="{{ route('admin.reports.purchases') }}" class="btn btn-secondary btn-block">
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
                    <span class="info-box-icon bg-info"><i class="fas fa-shopping-basket"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">{{ __('messages.total_purchases') }}</span>
                        <span class="info-box-number">{{ $statistics['total_purchases'] }}</span>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-success"><i class="fas fa-money-bill"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">{{ __('messages.total_amount') }}</span>
                        <span class="info-box-number">{{ number_format($statistics['total_amount'], 2) }} <x-riyal-icon /></span>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-warning"><i class="fas fa-receipt"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">{{ __('messages.total_taxes') }}</span>
                        <span class="info-box-number">{{ number_format($statistics['total_tax'], 2) }} <x-riyal-icon /></span>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-primary"><i class="fas fa-calculator"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">{{ __('messages.total_with_tax') }}</span>
                        <span class="info-box-number">{{ number_format($statistics['total_with_tax'], 2) }} <x-riyal-icon /></span>
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
                                        <th>{{ __('messages.purchase_number') }}</th>
                                        <th>{{ __('messages.purchase_date') }}</th>
                                        <th>{{ __('messages.provider') }}</th>
                                        <th>{{ __('messages.warehouse') }}</th>
                                        <th>{{ __('messages.Status') }}</th>
                                        <th>{{ __('messages.items_count') }}</th>
                                        <th>{{ __('messages.total_amount') }}</th>
                                        <th>{{ __('messages.total_taxes') }}</th>
                                        <th>{{ __('messages.total_with_tax') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($data as $purchase)
                                    <tr>
                                        <td>{{ $purchase->purchase_number }}</td>
                                        <td>{{ $purchase->created_at ? $purchase->created_at->format('Y-m-d') : '-' }}</td>
                                        <td>{{ $purchase->provider->name ?? '-' }}</td>
                                        <td>{{ $purchase->warehouse->name ?? '-' }}</td>
                                        <td>
                                            @if($purchase->status == 'pending')
                                                <span class="badge badge-warning">{{ __('messages.purchase_status_pending') }}</span>
                                            @elseif($purchase->status == 'confirmed')
                                                <span class="badge badge-info">{{ __('messages.purchase_status_confirmed') }}</span>
                                            @elseif($purchase->status == 'received')
                                                <span class="badge badge-success">{{ __('messages.purchase_status_received') }}</span>
                                            @elseif($purchase->status == 'paid')
                                                <span class="badge badge-primary">{{ __('messages.purchase_status_paid') }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $purchase->items->sum('quantity') }}</td>
                                        <td>{{ number_format($purchase->total_amount ?? 0, 2) }} <x-riyal-icon /></td>
                                        <td>{{ number_format($purchase->total_tax ?? 0, 2) }} <x-riyal-icon /></td>
                                        <td>{{ number_format(($purchase->total_amount ?? 0) + ($purchase->total_tax ?? 0), 2) }} <x-riyal-icon /></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="font-weight-bold">
                                        <td colspan="5" class="text-center">{{ __('messages.Total') }}</td>
                                        <td>{{ $statistics['total_items'] }}</td>
                                        <td>{{ number_format($statistics['total_amount'], 2) }} <x-riyal-icon /></td>
                                        <td>{{ number_format($statistics['total_tax'], 2) }} <x-riyal-icon /></td>
                                        <td>{{ number_format($statistics['total_with_tax'], 2) }} <x-riyal-icon /></td>
                                    </tr>
                                </tfoot>
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

        <!-- Statistics by Status -->
        <div class="row no-print">
            <div class="col-md-6">
                <div class="card card-default">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('messages.statistics_by_status') }}</h3>
                    </div>
                    <div class="card-body">
                        @if(count($statistics['by_status']) > 0)
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>{{ __('messages.Status') }}</th>
                                    <th>{{ __('messages.Count') }}</th>
                                    <th>{{ __('messages.total_amount') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($statistics['by_status'] as $status => $stats)
                                <tr>
                                    <td>{{ __('messages.purchase_status_' . $status) }}</td>
                                    <td>{{ $stats['count'] }}</td>
                                    <td>{{ number_format($stats['amount'], 2) }} <x-riyal-icon /></td>
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
                        <h3 class="card-title">{{ __('messages.statistics_by_provider') }}</h3>
                    </div>
                    <div class="card-body">
                        @if(count($statistics['by_provider']) > 0)
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>{{ __('messages.provider') }}</th>
                                    <th>{{ __('messages.Count') }}</th>
                                    <th>{{ __('messages.total_amount') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($statistics['by_provider'] as $providerName => $stats)
                                <tr>
                                    <td>{{ $providerName }}</td>
                                    <td>{{ $stats['count'] }}</td>
                                    <td>{{ number_format($stats['amount'], 2) }} <x-riyal-icon /></td>
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

        <!-- Statistics by Warehouse -->
        <div class="row no-print mt-4">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('messages.statistics_by_warehouse') }}</h3>
                    </div>
                    <div class="card-body">
                        @if(count($statistics['by_warehouse']) > 0)
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>{{ __('messages.warehouse') }}</th>
                                    <th>{{ __('messages.Count') }}</th>
                                    <th>{{ __('messages.total_amount') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($statistics['by_warehouse'] as $warehouseName => $stats)
                                <tr>
                                    <td>{{ $warehouseName }}</td>
                                    <td>{{ $stats['count'] }}</td>
                                    <td>{{ number_format($stats['amount'], 2) }} <x-riyal-icon /></td>
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
                    return "{{ __('messages.no_results') }}";
                }
            }
        });
    });
</script>
@endsection
