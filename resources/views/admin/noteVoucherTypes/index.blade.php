@extends('layouts.admin')
@section('title')
{{ __('messages.noteVoucherTypes') }}
@endsection



@section('content')



<div class="card">
    <div class="card-header">
        <h3 class="card-title card_title_center"> {{ __('messages.noteVoucherTypes') }} </h3>
        <a href="{{ route('noteVoucherTypes.create') }}" class="btn btn-sm btn-success"> {{ __('messages.New') }} {{
            __('messages.noteVoucherTypes') }}</a>

    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">


            </div>

        </div>
        <div class="clearfix"></div>

        <div id="ajax_responce_serarchDiv" class="col-md-12">
            @can('noteVoucherType-table')
            @if (@isset($data) && !@empty($data) && count($data) > 0)
            <table id="example2" class="table table-bordered table-hover">
                <thead class="custom_thead">



                    <th>{{ __('messages.Name') }}</th>
                    <th>{{ __('messages.Name_en') }}</th>
                    <th>{{ __('messages.in_out_type') }}</th>
                    <th>{{ __('messages.have_price') }}</th>


                    <th></th>
                </thead>
                <tbody>
                    @foreach ($data as $info)
                    <tr>


                        <td>{{ $info->name }}</td>
                        <td>{{ $info->name_en }}</td>
                        <td>{{ $info->in_out_type == 1 ? 'ادخال' : 'اخراج' }}</td>
                        <td>{{ $info->have_price == 1 ? 'Yes' : 'No' }}</td>


                        <td>
                            @can('noteVoucherType-edit')
                            <a href="{{ route('noteVoucherTypes.edit', $info->id) }}" class="btn btn-sm  btn-primary">{{
                                __('messages.Edit') }}</a>
                            @endcan
                            @can('noteVoucherType-delete')
                            <form action="{{ route('noteVoucherTypes.destroy', $info->id) }}" method="POST">
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
            {{ $data->links() }}
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
