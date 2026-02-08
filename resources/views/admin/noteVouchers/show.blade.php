@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <!-- Header Card -->
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">{{ __('messages.noteVouchers') }} - {{ $noteVoucher->noteVoucherType->name }}</h3>
        </div>
        <div class="card-body">
            <!-- Display the header -->
            @if($noteVoucher->noteVoucherType->header)
                <div class="alert alert-info mb-3">
                    {!! $noteVoucher->noteVoucherType->header !!}
                </div>
            @endif

            <!-- Basic Information -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="info-box">
                        <div class="info-box-content">
                            <span class="info-box-text">{{ __('messages.number') }}</span>
                            <span class="info-box-number" style="font-size: 1.2rem;">{{ $noteVoucher->number }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-box">
                        <div class="info-box-content">
                            <span class="info-box-text">{{ __('messages.Date') }}</span>
                            <span class="info-box-number" style="font-size: 1.2rem;">{{ $noteVoucher->date_note_voucher->format('Y-m-d') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-box">
                        <div class="info-box-content">
                            <span class="info-box-text">{{ __('messages.noteVoucherTypes') }}</span>
                            <span class="info-box-number" style="font-size: 1rem;">{{ $noteVoucher->noteVoucherType->name }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- From and To Details Section -->
            <div class="row">
                <!-- From Details -->
                <div class="col-md-6">
                    <div class="card card-outline card-info">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('messages.from') }}</h3>
                        </div>
                        <div class="card-body">
                            @if ($noteVoucher->noteVoucherType->in_out_type == 1)
                                <!-- Type 1: From Provider/User/Event to Warehouse -->
                                @if ($noteVoucher->provider)
                                    <p><strong>{{ __('messages.Provider') }}:</strong> {{ $noteVoucher->provider->name }}</p>
                                    @if($noteVoucher->provider->phone)
                                        <p><strong>{{ __('messages.Phone') }}:</strong> {{ $noteVoucher->provider->phone }}</p>
                                    @endif
                                @elseif ($noteVoucher->user)
                                    <p><strong>{{ __('messages.customer') }}:</strong> {{ $noteVoucher->user->name }}</p>
                                    @if($noteVoucher->user->phone)
                                        <p><strong>{{ __('messages.Phone') }}:</strong> {{ $noteVoucher->user->phone }}</p>
                                    @endif
                                @elseif ($noteVoucher->event)
                                    <p><strong>{{ __('messages.event') }}:</strong> {{ $noteVoucher->event->name }}</p>
                                @else
                                    <p>-</p>
                                @endif
                            @elseif ($noteVoucher->noteVoucherType->in_out_type == 2)
                                <!-- Type 2: From Warehouse to Provider/User/Event -->
                                <p><strong>{{ __('messages.Warehouse') }}:</strong> {{ $noteVoucher->fromWarehouse->name ?? '-' }}</p>
                                @if($noteVoucher->fromWarehouse)
                                    <p><strong>{{ __('messages.description') }}:</strong> {{ $noteVoucher->fromWarehouse->description ?? '-' }}</p>
                                @endif
                            @else
                                <!-- Type 3: From Warehouse to Warehouse -->
                                <p><strong>{{ __('messages.Warehouse') }}:</strong> {{ $noteVoucher->fromWarehouse->name ?? '-' }}</p>
                                @if($noteVoucher->fromWarehouse)
                                    <p><strong>{{ __('messages.description') }}:</strong> {{ $noteVoucher->fromWarehouse->description ?? '-' }}</p>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>

                <!-- To Details -->
                <div class="col-md-6">
                    <div class="card card-outline card-success">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('messages.to') }}</h3>
                        </div>
                        <div class="card-body">
                            @if ($noteVoucher->noteVoucherType->in_out_type == 1)
                                <!-- Type 1: To Warehouse -->
                                <p><strong>{{ __('messages.Warehouse') }}:</strong> {{ $noteVoucher->toWarehouse->name ?? '-' }}</p>
                                @if($noteVoucher->toWarehouse)
                                    <p><strong>{{ __('messages.description') }}:</strong> {{ $noteVoucher->toWarehouse->description ?? '-' }}</p>
                                @endif
                            @elseif ($noteVoucher->noteVoucherType->in_out_type == 2)
                                <!-- Type 2: To Provider/User/Event -->
                                @if ($noteVoucher->provider)
                                    <p><strong>{{ __('messages.Provider') }}:</strong> {{ $noteVoucher->provider->name }}</p>
                                    @if($noteVoucher->provider->phone)
                                        <p><strong>{{ __('messages.Phone') }}:</strong> {{ $noteVoucher->provider->phone }}</p>
                                    @endif
                                @elseif ($noteVoucher->user)
                                    <p><strong>{{ __('messages.customer') }}:</strong> {{ $noteVoucher->user->name }}</p>
                                    @if($noteVoucher->user->phone)
                                        <p><strong>{{ __('messages.Phone') }}:</strong> {{ $noteVoucher->user->phone }}</p>
                                    @endif
                                @elseif ($noteVoucher->event)
                                    <p><strong>{{ __('messages.event') }}:</strong> {{ $noteVoucher->event->name }}</p>
                                @else
                                    <p>-</p>
                                @endif
                            @else
                                <!-- Type 3: To Warehouse -->
                                <p><strong>{{ __('messages.Warehouse') }}:</strong> {{ $noteVoucher->toWarehouse->name ?? '-' }}</p>
                                @if($noteVoucher->toWarehouse)
                                    <p><strong>{{ __('messages.description') }}:</strong> {{ $noteVoucher->toWarehouse->description ?? '-' }}</p>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @if($noteVoucher->note)
            <div class="alert alert-warning mt-3">
                <strong>{{ __('messages.Note') }}:</strong> {{ $noteVoucher->note }}
            </div>
            @endif
        </div>
    </div>

    <!-- Products Table -->
    <div class="card card-info mt-4">
        <div class="card-header">
            <h3 class="card-title">{{ __('messages.order_products') }}</h3>
        </div>
        <div class="card-body">
            @if($noteVoucher->voucherProducts->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>{{ __('messages.product') }}</th>
                            <th>{{ __('messages.quantity') }}</th>
                            <th>{{ __('messages.unit_price') }} ({{ __('messages.tax_inclusive') }})</th>
                            <th>{{ __('messages.tax_percentage') }}</th>
                            <th>{{ __('messages.Total') }}</th>
                            <th>{{ __('messages.Note') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $total_quantity = 0;
                            $total_price_before_tax = 0;
                            $total_tax = 0;
                        @endphp
                        @foreach ($noteVoucher->voucherProducts as $key => $voucherProduct)
                            @php
                                $quantity = $voucherProduct->quantity ?? 0;
                                $purchasing_price = $voucherProduct->purchasing_price ?? 0; // السعر شامل الضريبة
                                $tax_percentage = $voucherProduct->tax_percentage ?? 0;

                                // Calculate totals using Tax Inclusive Pricing logic (same as OrderController)
                                $total_price_after_tax = round($purchasing_price * $quantity, 2);

                                // Back-calculate the pre-tax total
                                $tax_divisor = 1 + ($tax_percentage / 100);
                                $item_price_before_tax = round($total_price_after_tax / $tax_divisor, 2);

                                // Calculate tax value as the difference
                                $tax_amount = round($total_price_after_tax - $item_price_before_tax, 2);

                                $total_quantity += $quantity;
                                $total_price_before_tax += $item_price_before_tax;
                                $total_tax += $tax_amount;
                            @endphp
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $voucherProduct->product->name_ar ?? $voucherProduct->product->name }}</td>
                                <td>{{ number_format($quantity, 2) }}</td>
                                <td>{{ number_format($purchasing_price, 2) }}</td>
                                <td>{{ $tax_percentage }}%</td>
                                <td>{{ number_format($total_price_after_tax, 2) }} <x-riyal-icon /></td>
                                <td>{{ $voucherProduct->note ?? '-' }}</td>
                            </tr>
                        @endforeach
                        <tr class="table-warning">
                            <td colspan="2"><strong>{{ __('messages.Total') }} ({{ __('messages.without_tax') }})</strong></td>
                            <td><strong>{{ number_format($total_quantity, 2) }}</strong></td>
                            <td>-</td>
                            <td>-</td>
                            <td><strong>{{ number_format($total_price_before_tax, 2) }} <x-riyal-icon /></strong></td>
                            <td>-</td>
                        </tr>
                        @if($total_tax > 0)
                        <tr class="table-light">
                            <td colspan="5"><strong>{{ __('messages.tax_value') }}</strong></td>
                            <td colspan="2"><strong>{{ number_format($total_tax, 2) }} <x-riyal-icon /></strong></td>
                        </tr>
                        <tr class="table-success">
                            <td colspan="5"><strong>{{ __('messages.total_after_tax') }}</strong></td>
                            <td colspan="2"><strong>{{ number_format($total_price_before_tax + $total_tax, 2) }} <x-riyal-icon /></strong></td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            @else
                <div class="alert alert-warning">
                    {{ __('messages.No_data') }}
                </div>
            @endif
        </div>
    </div>

    <!-- Display the footer -->
    @if($noteVoucher->noteVoucherType->footer)
        <div class="alert alert-secondary mt-4">
            {!! $noteVoucher->noteVoucherType->footer !!}
        </div>
    @endif

    <!-- Action Buttons -->
    <div class="mt-4">
        <button type="button" class="btn btn-primary" onclick="window.print()">
            <i class="fas fa-print"></i> {{ __('messages.print') }}
        </button>
        <a href="{{ route('noteVouchers.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> {{ __('messages.back') }}
        </a>
    </div>
</div>
@endsection