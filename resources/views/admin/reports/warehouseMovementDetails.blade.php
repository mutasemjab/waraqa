@extends('layouts.admin')
@section('title')
{{ __('messages.Details') }} - {{ __('messages.Warehouse') }} {{ __('messages.Report') }}
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
            background-color: white !important;
        }
    }
</style>

<div class="card">
    <div class="card-header">
        <h3 class="card-title card_title_center">{{ __('messages.Details') }} - {{ __('messages.Warehouse') }} {{ __('messages.Report') }}</h3>
        <div class="card-tools no-print">
            <a href="{{ route('admin.reports.warehouseMovement') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-right"></i> {{ __('messages.Back') }}
            </a>
            <button type="button" class="btn btn-info btn-sm" onclick="window.print()">
                <i class="fas fa-print"></i> {{ __('messages.print') }}
            </button>
        </div>
    </div>

    <div class="card-body printable-section">
        <!-- Movement Header Info -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('messages.info') }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{ __('messages.date_note_voucher') }}</label>
                                    <p class="form-control-static"><strong>{{ $movement->date_note_voucher->format('Y-m-d') }}</strong></p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{ __('messages.Type') }}</label>
                                    <p class="form-control-static">
                                        <span class="badge badge-primary">{{ $movement->noteVoucherType->name ?? '-' }}</span>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('messages.Warehouse') }}</label>
                                    <p class="form-control-static">
                                        @if($movement->fromWarehouse)
                                        <span class="badge badge-warning">{{ __('messages.from') }}: {{ $movement->fromWarehouse->name }}</span>
                                        @endif
                                        @if($movement->toWarehouse)
                                        <span class="badge badge-success">{{ __('messages.to') }}: {{ $movement->toWarehouse->name }}</span>
                                        @endif
                                        @if(!$movement->fromWarehouse && !$movement->toWarehouse)
                                        <span class="badge badge-secondary">-</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            @if($movement->provider)
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{ __('messages.Provider') }}</label>
                                    <p class="form-control-static"><strong>{{ $movement->provider->name }}</strong></p>
                                </div>
                            </div>
                            @endif
                            @if($movement->event)
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{ __('messages.event') }}</label>
                                    <p class="form-control-static"><strong>{{ $movement->event->name }}</strong></p>
                                </div>
                            </div>
                            @endif
                            @if($movement->order)
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{ __('messages.orders') }}</label>
                                    <p class="form-control-static">
                                        <a href="{{ route('orders.show', $movement->order->id) }}" class="badge badge-info" target="_blank">
                                            #{{ $movement->order->id }}
                                        </a>
                                    </p>
                                </div>
                            </div>
                            @endif
                            @if($movement->user)
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{ __('messages.Created_by') }}</label>
                                    <p class="form-control-static">{{ $movement->user->name }}</p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products Details -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('messages.Products') }} {{ __('messages.Details') }}</h3>
                    </div>
                    <div class="card-body">
                        @if($movement->voucherProducts->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="custom_thead">
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('messages.Product') }}</th>
                                        <th>{{ __('messages.Quantity') }}</th>
                                        <th>{{ __('messages.Price') }}</th>
                                        <th>{{ __('messages.Value') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $totalValue = 0; @endphp
                                    @foreach($movement->voucherProducts as $index => $voucherProduct)
                                    @php
                                        $price = $voucherProduct->purchasing_price ?? ($voucherProduct->product->selling_price ?? 0);
                                        $value = ($voucherProduct->quantity ?? 0) * $price;
                                        $totalValue += $value;
                                    @endphp
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $voucherProduct->product->name ?? '-' }}</td>
                                        <td>{{ number_format($voucherProduct->quantity ?? 0, 2) }}</td>
                                        <td>{{ number_format($price, 2) }} <x-riyal-icon /></td>
                                        <td>{{ number_format($value, 2) }} <x-riyal-icon /></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-light">
                                    <tr>
                                        <th colspan="4" class="text-left">{{ __('messages.Total') }}</th>
                                        <th>{{ number_format($totalValue, 2) }} <x-riyal-icon /></th>
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

        <!-- Notes (if available) -->
        @if($movement->note)
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('messages.Notes') }}</h3>
                    </div>
                    <div class="card-body">
                        <p>{{ $movement->note }}</p>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
