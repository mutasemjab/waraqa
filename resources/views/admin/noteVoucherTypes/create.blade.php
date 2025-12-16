@extends('layouts.admin')
@section('title')
    {{ __('messages.noteVoucherTypes') }}
@endsection


@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title card_title_center"> {{ __('messages.Add_New') }} {{ __('messages.noteVoucherTypes') }} </h3>
        </div>
        <!-- /.card-header -->
        <div class="card-body">


            <form action="{{ route('noteVoucherTypes.store') }}" method="post" enctype='multipart/form-data'>
                <div class="row">
                    @csrf



                    <div class="col-md-6">
                        <div class="form-group">
                            <label> {{ __('messages.number') }}</label>
                            <input name="number" id="number" class="form-control" value="{{ old('number') }}">
                            @error('number')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label> {{ __('messages.Name') }}</label>
                            <input name="name" id="name" class="form-control" value="{{ old('name') }}">
                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label> {{ __('messages.Name_en') }}</label>
                            <input name="name_en" id="name_en" class="form-control" value="{{ old('name_en') }}">
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
                                <option value="1">ادخال</option>
                                <option value="2">اخراج</option>
                                <option value="3">نقل</option>
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
                                <option value="1">Yes</option>
                                <option value="2">No</option>
                            </select>
                            @error('have_price')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label> {{ __('messages.header') }}</label>
                            <textarea name="header" id="header" class="form-control" value="{{ old('header') }}" rows="12"></textarea>
                            @error('header')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label> {{ __('messages.footer') }}</label>
                            <textarea name="footer" id="footer" class="form-control" value="{{ old('footer') }}" rows="12"></textarea>
                            @error('footer')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>




                    <div class="col-md-12">
                        <div class="form-group text-center">
                            <button id="do_add_item_cardd" type="submit" class="btn btn-primary btn-sm"> {{__('messages.Submit')}}</button>
                            <a href="{{ route('noteVoucherTypes.index') }}" class="btn btn-sm btn-danger">{{__('messages.Cancel')}}</a>

                        </div>
                    </div>

                </div>
            </form>



        </div>




    </div>
    </div>
@endsection


@section('script')
    <script>
        function previewImage() {
            var preview = document.getElementById('image-preview');
            var input = document.getElementById('Item_img');
            var file = input.files[0];
            if (file) {
                preview.style.display = "block";
                var reader = new FileReader();
                reader.onload = function() {
                    preview.src = reader.result;
                }
                reader.readAsDataURL(file);
            } else {
                preview.style.display = "none";
            }
        }
    </script>
@endsection