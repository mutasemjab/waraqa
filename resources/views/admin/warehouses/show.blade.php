@extends('layouts.admin')
@section('title')
{{ __('messages.warehouse_movements') }}
@endsection

@section('content')

<div class="card">
    <div class="card-header">
        <h3 class="card-title card_title_center"> {{ __('messages.warehouse_movements') }} : {{ $warehouse->name }} </h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        
        <div class="col-md-12">
            @if (@isset($movements) && !@empty($movements) && count($movements) > 0)
            <table id="example2" class="table table-bordered table-hover">
                <thead class="custom_thead">
                    <th>{{ __('messages.Date') }}</th>
                    <th>{{ __('messages.Type') }}</th>
                    <th>{{ __('messages.Note') }}</th>
                    <th>{{ __('messages.Total_Quantity') }}</th>
                    <th>{{ __('messages.details') }}</th>
                    <th>{{ __('messages.Action') }}</th>
                </thead>
                <tbody>
                    @foreach ($movements as $movement)
                    <tr>
                        <td>{{ $movement->date_note_voucher->format('Y-m-d') }}</td>
                        <td>
                            @if($movement->noteVoucherType->in_out_type == 1)
                                <span class="badge badge-success">{{ __('messages.in_out_type_1') }}</span>
                            @elseif($movement->noteVoucherType->in_out_type == 2)
                                <span class="badge badge-warning">{{ __('messages.in_out_type_2') }}</span>
                            @elseif($movement->noteVoucherType->in_out_type == 3)
                                <span class="badge badge-info">{{ __('messages.in_out_type_3') }}</span>
                            @endif
                        </td>
                         <td>
                            {{ $movement->note }}
                            @if($movement->noteVoucherType->in_out_type == 1)
                              <br> <small>{{ __('messages.from') }}: {{ optional($movement->provider)->name ?? $movement->user->name ?? 'N/A' }}</small>
                            @elseif($movement->noteVoucherType->in_out_type == 2)
                               <br> <small>{{ __('messages.to') }}: {{ optional($movement->provider)->name ?? $movement->user->name ?? $movement->event->title ?? 'N/A' }}</small>
                            @elseif($movement->noteVoucherType->in_out_type == 3)
                                @if($movement->from_warehouse_id == $warehouse->id)
                                    <br> <small>{{ __('messages.to_warehouse') }}: {{ optional($movement->toWarehouse)->name }}</small>
                                @else
                                    <br> <small>{{ __('messages.from_warehouse') }}: {{ optional($movement->fromWarehouse)->name }}</small>
                                @endif
                            @endif
                        </td>
                        <td>{{ $movement->voucherProducts->sum('quantity') }}</td>
                        <td>
                             @foreach($movement->voucherProducts as $product)
                                <span class="badge badge-light">{{ $product->product->name_ar ?? $product->product->name_en }} ({{ $product->quantity }})</span>
                             @endforeach
                        </td>
                        <td>
                            @can('noteVoucher-edit')
                             <a href="{{ route('noteVouchers.show', $movement->id) }}" class="btn btn-sm btn-info">{{ __('messages.View') }}</a>
                            @endcan
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <br>
            {{ $movements->links() }}
            @else
            <div class="alert alert-danger">
                {{ __('messages.No_data') }} </div>
            @endif

        </div>

    </div>

</div>

@endsection

@section('script')
<script src="{{ asset('assets/admin/js/sliderss.js') }}"></script>
@endsection
