@extends('layouts.admin')
@section('title')
{{ __('messages.orders_report') }}
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
        <h3 class="card-title card_title_center">{{ __('messages.orders_report') }}</h3>
    </div>

    <div class="card-body">
        <!-- Filters Section -->
        <div class="row mb-4 no-print">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('messages.Filters') }}</h3>
                    </div>
                    <form method="GET" action="{{ route('admin.reports.orders') }}" class="form-horizontal">
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

                                <!-- User/Customer -->
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="user_id">{{ __('messages.customer') }}</label>
                                        <select class="form-control select2" id="user_id" name="user_id">
                                            <option value="">{{ __('messages.All') }}</option>
                                            @foreach ($users as $user)
                                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}
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
                                            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>{{ __('messages.done') }}</option>
                                            <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>{{ __('messages.canceled') }}</option>
                                            <option value="3" {{ request('status') == '3' ? 'selected' : '' }}>{{ __('messages.refund') }}</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Payment Status -->
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="payment_status">{{ __('messages.payment_status') }}</label>
                                        <select class="form-control select2" id="payment_status" name="payment_status">
                                            <option value="">{{ __('messages.All') }}</option>
                                            <option value="1" {{ request('payment_status') == '1' ? 'selected' : '' }}>{{ __('messages.paid') }}</option>
                                            <option value="0" {{ request('payment_status') == '0' ? 'selected' : '' }}>{{ __('messages.unpaid') }}</option>
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
                                        <a href="{{ route('admin.reports.orders') }}" class="btn btn-secondary btn-block">
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
                    <span class="info-box-icon bg-info"><i class="fas fa-shopping-cart"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">{{ __('messages.total_orders') }}</span>
                        <span class="info-box-number">{{ $statistics['total_orders'] }}</span>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-success"><i class="fas fa-money-bill"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">{{ __('messages.total_revenue') }}</span>
                        <span class="info-box-number">{{ number_format($statistics['total_revenue'], 2) }} <x-riyal-icon /></span>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-warning"><i class="fas fa-receipt"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">{{ __('messages.total_taxes') }}</span>
                        <span class="info-box-number">{{ number_format($statistics['total_taxes'], 2) }} <x-riyal-icon /></span>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-danger"><i class="fas fa-clock"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">{{ __('messages.total_remaining') }}</span>
                        <span class="info-box-number">{{ number_format($statistics['total_remaining'], 2) }} <x-riyal-icon /></span>
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
                                        <th>{{ __('messages.order_date') }}</th>
                                        <th>{{ __('messages.customer') }}</th>
                                        <th>{{ __('messages.Status') }}</th>
                                        <th>{{ __('messages.payment_status') }}</th>
                                        <th>{{ __('messages.total_prices') }}</th>
                                        <th>{{ __('messages.total_taxes') }}</th>
                                        <th>{{ __('messages.paid_amount') }}</th>
                                        <th>{{ __('messages.remaining_amount') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($data as $order)
                                    <tr>
                                        <td>{{ $order->number }}</td>
                                        <td>{{ $order->order_date ? $order->order_date->format('Y-m-d') : '-' }}</td>
                                        <td>{{ $order->user->name ?? '-' }}</td>
                                        <td>
                                            @if($order->status == 1)
                                                <span class="badge badge-success">{{ __('messages.done') }}</span>
                                            @elseif($order->status == 2)
                                                <span class="badge badge-danger">{{ __('messages.canceled') }}</span>
                                            @else
                                                <span class="badge badge-info">{{ __('messages.refund') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($order->payment_status == 1)
                                                <span class="badge badge-success">{{ __('messages.paid') }}</span>
                                            @else
                                                <span class="badge badge-warning">{{ __('messages.unpaid') }}</span>
                                            @endif
                                        </td>
                                        <td>{{ number_format($order->total_prices ?? 0, 2) }} <x-riyal-icon /></td>
                                        <td>{{ number_format($order->total_taxes ?? 0, 2) }} <x-riyal-icon /></td>
                                        <td>{{ number_format($order->paid_amount ?? 0, 2) }} <x-riyal-icon /></td>
                                        <td>{{ number_format($order->remaining_amount ?? 0, 2) }} <x-riyal-icon /></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="font-weight-bold">
                                        <td colspan="5" class="text-center">{{ __('messages.Total') }}</td>
                                        <td>{{ number_format($statistics['total_revenue'], 2) }} <x-riyal-icon /></td>
                                        <td>{{ number_format($statistics['total_taxes'], 2) }} <x-riyal-icon /></td>
                                        <td>{{ number_format($statistics['total_paid'], 2) }} <x-riyal-icon /></td>
                                        <td>{{ number_format($statistics['total_remaining'], 2) }} <x-riyal-icon /></td>
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
                                    <th>{{ __('messages.total_revenue') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($statistics['by_status'] as $status => $stats)
                                <tr>
                                    <td>{{ __('messages.' . $status) }}</td>
                                    <td>{{ $stats['count'] }}</td>
                                    <td>{{ number_format($stats['revenue'], 2) }} <x-riyal-icon /></td>
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

            <!-- Statistics by Payment Status -->
            <div class="col-md-6">
                <div class="card card-default">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('messages.statistics_by_payment_status') }}</h3>
                    </div>
                    <div class="card-body">
                        @if(count($statistics['by_payment_status']) > 0)
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>{{ __('messages.payment_status') }}</th>
                                    <th>{{ __('messages.Count') }}</th>
                                    <th>{{ __('messages.total_revenue') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($statistics['by_payment_status'] as $paymentStatus => $stats)
                                <tr>
                                    <td>{{ __('messages.' . $paymentStatus) }}</td>
                                    <td>{{ $stats['count'] }}</td>
                                    <td>{{ number_format($stats['revenue'], 2) }} <x-riyal-icon /></td>
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

        <!-- Statistics by Customer -->
        <div class="row no-print mt-4">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('messages.statistics_by_customer') }}</h3>
                    </div>
                    <div class="card-body">
                        @if(count($statistics['by_user']) > 0)
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>{{ __('messages.customer') }}</th>
                                    <th>{{ __('messages.Count') }}</th>
                                    <th>{{ __('messages.total_revenue') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($statistics['by_user'] as $userName => $stats)
                                <tr>
                                    <td>{{ $userName }}</td>
                                    <td>{{ $stats['count'] }}</td>
                                    <td>{{ number_format($stats['revenue'], 2) }} <x-riyal-icon /></td>
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
