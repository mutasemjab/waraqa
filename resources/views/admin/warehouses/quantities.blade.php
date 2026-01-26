@extends('layouts.admin')
@section('title')
{{ __('messages.warehouses') }} - {{ $warehouse->name }}
@endsection

@section('content')

<div class="card">
    <div class="card-header">
        <h3 class="card-title card_title_center"> {{ __('messages.warehouses') }} - {{ $warehouse->name }} </h3>
        <a href="{{ route('warehouses.index') }}" class="btn btn-sm btn-secondary">{{ __('messages.Back') }}</a>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="clearfix"></div>

        <div id="ajax_responce_serarchDiv" class="col-md-12">
            @if (count($products) > 0)
            <table id="example2" class="table table-bordered table-hover">
                <thead class="custom_thead">
                    <th>{{ __('messages.product') }}</th>
                    <th>{{ __('messages.quantity') }}</th>
                </thead>
                <tbody>
                    @foreach ($products as $item)
                    <tr>
                        <td>{{ $item->product_name }}</td>
                        <td>
                            <span class="badge badge-primary">
                                {{ max(0, $item->input_quantity - $item->output_quantity) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="alert alert-warning">
                {{ __('messages.No_data') }}
            </div>
            @endif
        </div>

    </div>

</div>

</div>

@endsection
