@extends('layouts.admin')
@section('title')
{{ __('messages.noteVouchers') }}
@endsection



@section('content')



<div class="card">
    <div class="card-header">
        <h3 class="card-title card_title_center"> {{ __('messages.noteVouchers') }} </h3>

        {{-- <a href="{{ route('noteVouchers.create') }}" class="btn btn-sm btn-success"> {{ __('messages.New') }} {{
            __('messages.noteVouchers') }}</a> --}}

        @can('noteVoucher-table')
        <a href="{{ route('admin.reports.noteVouchers') }}" class="btn btn-sm btn-info" style="float: right;">
            <i class="fas fa-chart-bar"></i> {{ __('messages.Report') }}
        </a>
        @endcan

    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">


            </div>

        </div>
        <div class="clearfix"></div>

        <div id="ajax_responce_serarchDiv" class="col-md-12">
            @can('noteVoucher-table')
            @if (@isset($data) && !@empty($data) && count($data) > 0)
            <table id="example2" class="table table-bordered table-hover">
                <thead class="custom_thead">


                    <th>{{ __('messages.number') }}</th>
                    <th>{{ __('messages.note') }}</th>
                    <th>{{ __('messages.date_note_voucher') }}</th>
                    <th>{{ __('messages.noteVoucherTypes') }}</th>

                    <th></th>
                </thead>
                <tbody>
                    @foreach ($data as $info)
                    <tr>


                        <td>{{ $info->number }}</td>
                        <td>{{ $info->note ?? null }}</td>
                        <td>{{ $info->date_note_voucher }}</td>
                        <td>{{ $info->noteVoucherType->name }}</td>


                        <td>
                            @can('noteVoucher-edit')
                            <a href="{{ route('noteVouchers.edit', $info->id) }}" class="btn btn-sm  btn-primary">{{
                                __('messages.Edit') }}</a>
                            @endcan
                            @can('noteVoucher-delete')
                            <form action="{{ route('noteVouchers.destroy', $info->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">{{ __('messages.Delete') }}</button>
                            </form>
                            @endcan

                        </td>


                    </tr>
                    @endforeach



                </tbody>
            </table>
            <br>
            
            @else
            <div class="alert alert-danger">
                {{ __('messages.No_data') }} </div>
            @endif
            @endcan

        </div>



    </div>

</div>

</div>

@endsection

@section('script')
<script src="{{ asset('assets/admin/js/sliderss.js') }}"></script>
@endsection
