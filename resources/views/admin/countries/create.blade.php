@extends('layouts.admin')



@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>{{ __('messages.add_country') }}</h4>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('countries.store') }}" method="POST">
                            @csrf

                            <!-- Country Name -->
                            <div class="mb-3">
                                <label for="name" class="form-label">{{ __('messages.country_name_ar') }} <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name_ar') is-invalid @enderror"
                                    id="name_ar" name="name_ar" value="{{ old('name_ar') }}" required>
                                @error('name_ar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                     
                            <div class="mb-3">
                                <label for="name" class="form-label">{{ __('messages.country_name_en') }} <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name_en') is-invalid @enderror"
                                    id="name_en" name="name_en" value="{{ old('name_en') }}" required>
                                @error('name_en')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                      

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('countries.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> {{ __('messages.back') }}
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> {{ __('messages.save') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection




