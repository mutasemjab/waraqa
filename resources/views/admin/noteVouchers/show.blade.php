@extends('layouts.admin')

@section('content')
<div class="container">
    <h2>{{ $noteVoucher->noteVoucherType->name }}</h2>

    <!-- Display the header -->
    @if($noteVoucher->noteVoucherType->header)
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="alert alert-info">
                    {!! $noteVoucher->noteVoucherType->header !!}
                </div>
            </div>
        </div>
    @endif

    <div class="row mb-3">
        <div class="col-md-6">
            <strong>{{ __('messages.Date') }}:</strong> {{ $noteVoucher->date_note_voucher }}
        </div>
        <div class="col-md-6">
            <strong>{{ __('messages.number') }}:</strong> {{ $noteVoucher->number }}
        </div>

        @if ($noteVoucher->noteVoucherType->in_out_type == 1)
            <!-- For Receipt Type (in_out_type = 1): From Provider to Warehouse -->
            <div class="col-md-6">
                <strong>{{ __('messages.provider') }}:</strong> {{ $noteVoucher->provider->name }}
            </div>
            <div class="col-md-6">
                <strong>{{ __('messages.toWarehouse') }}:</strong> {{ $noteVoucher->toWarehouse->name }}
            </div>
        @elseif ($noteVoucher->noteVoucherType->in_out_type == 2)
            <!-- For Outgoing Type (in_out_type = 2): From Warehouse to Provider -->
            <div class="col-md-6">
                <strong>{{ __('messages.fromWarehouse') }}:</strong> {{ $noteVoucher->fromWarehouse->name }}
            </div>
            <div class="col-md-6">
                <strong>{{ __('messages.provider') }}:</strong> {{ $noteVoucher->provider->name }}
            </div>
        @else
            <!-- For Transfer Type (in_out_type = 3): From Warehouse to Warehouse -->
            <div class="col-md-6">
                <strong>{{ __('messages.fromWarehouse') }}:</strong> {{ $noteVoucher->fromWarehouse->name }}
            </div>
            <div class="col-md-6">
                <strong>{{ __('messages.toWarehouse') }}:</strong> {{ $noteVoucher->toWarehouse->name }}
            </div>
        @endif

        <div class="col-md-12">
            <strong>{{ __('messages.Note') }}:</strong> {{ $noteVoucher->note }}
        </div>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>{{ __('messages.product') }}</th>
                <th>{{ __('messages.quantity') }}</th>
                @if($noteVoucher->noteVoucherType->have_price == 1)
                <th>{{ __('messages.purchasing_Price') }}</th>
                @endif
                <th>{{ __('messages.Note') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($noteVoucher->voucherProducts as $voucherProduct)
                <tr>
                    <td>{{ $voucherProduct->product->name_ar }}</td>
                    <td>{{ $voucherProduct->quantity }}</td>
                    @if($noteVoucher->noteVoucherType->have_price == 1)
                        <td>{{ $voucherProduct->purchasing_price }}</td>
                    @endif
                    <td>{{ $voucherProduct->note }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Display the footer -->
    @if($noteVoucher->noteVoucherType->footer)
        <div class="row mt-3">
            <div class="col-md-12">
                <div class="alert alert-secondary">
                    {{ $noteVoucher->noteVoucherType->footer ?? null}}
                </div>
            </div>
        </div>
    @endif

    <a href="{{ route('noteVouchers.index') }}" class="btn btn-secondary">Back</a>
</div>
@endsection