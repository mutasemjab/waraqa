@extends('layouts.admin')
@section('title')
{{ __('messages.Edit') }} {{ __('messages.noteVoucherTypes') }}
@endsection



@section('contentheaderlink')
<a href="{{ route('noteVoucherTypes.index') }}"> {{ __('messages.noteVoucherTypes') }} </a>
@endsection

@section('contentheaderactive')
{{ __('messages.Edit') }}
@endsection


@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title card_title_center"> {{ __('messages.Edit') }} {{ __('messages.noteVoucherTypes') }} </h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">


        <form action="{{ route('noteVoucherTypes.update', $data['id']) }}" method="post" enctype='multipart/form-data'>
            <div class="row">
                @csrf


                <div class="col-md-6">
                    <div class="form-group">
                        <label>{{ __('messages.Name') }}</label>
                        <input name="name" id="name" class="" value="{{ old('name', $data['name']) }}">
                        @error('name')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>{{ __('messages.Name_en') }}</label>
                        <input name="name_en" id="name_en" class="" value="{{ old('name_en', $data['name_en']) }}">
                        @error('name_en')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>


                <div class="col-md-6">
                    <div class="form-group">
                        <label>{{ __('messages.in_out_type') }}</label>
                        <select name="in_out_type" id="in_out_type" class="form-control">
                            <option value="">Select</option>
                            <option @if ($data->in_out_type == 1) selected="selected" @endif value="1">ادخال</option>
                            <option @if ($data->in_out_type == 2) selected="selected" @endif value="2">اخراج</option>
                            <option @if ($data->in_out_type == 3) selected="selected" @endif value="3">نقل</option>
                        </select>
                        @error('in_out_type')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label>{{ __('messages.have_price') }}</label>
                        <select name="have_price" id="have_price" class="form-control">
                            <option value="">Select</option>
                            <option @if ($data->have_price == 1) selected="selected" @endif value="1">Yes</option>
                            <option @if ($data->have_price == 2) selected="selected" @endif value="2">No</option>
                        </select>
                        @error('have_price')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>





                <div class="col-md-6">
                    <div class="form-group">
                        <label> {{ __('messages.header') }}</label>
                        <textarea name="header" id="header" class="form-control" value="{{ old('header') }}" rows="12">{{$data['header']}}</textarea>
                        @error('header')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label> {{ __('messages.footer') }}</label>
                        <textarea name="footer" id="footer" class="form-control" value="{{ old('footer') }}" rows="12">{{$data['footer']}}</textarea>
                        @error('footer')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>






                <div class="col-md-12">
                    <div class="form-group text-center">
                        <button id="do_add_item_cardd" type="submit" class="btn btn-primary btn-sm">
                            {{ __('messages.Update') }}</button>
                        <a href="{{ route('noteVoucherTypes.index') }}" class="btn btn-sm btn-danger">{{
                            __('messages.Cancel') }}</a>

                    </div>
                </div>

            </div>
        </form>



    </div>




</div>
</div>
@endsection

@section('script')

@endsection
