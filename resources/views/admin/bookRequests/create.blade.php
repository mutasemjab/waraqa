@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h4>{{ __('messages.Create_Book_Request') }}</h4>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('bookRequests.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="product_id" class="form-label">{{ __('messages.Product') }} <span class="text-danger">*</span></label>
                            <select name="product_id" id="product_id" class="form-select @error('product_id') is-invalid @enderror" required>
                                <option value="">-- {{ __('messages.Select_Product') }} --</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                        {{ app()->getLocale() == 'ar' ? $product->name_ar : $product->name_en }}
                                    </option>
                                @endforeach
                            </select>
                            @error('product_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="provider_id" class="form-label">{{ __('messages.provider') }} <span class="text-danger">*</span></label>
                            <select name="provider_id" id="provider_id" class="form-select @error('provider_id') is-invalid @enderror" required>
                                <option value="">-- {{ __('messages.Select_Provider') }} --</option>
                                @foreach($providers as $provider)
                                    <option value="{{ $provider->id }}" {{ old('provider_id') == $provider->id ? 'selected' : '' }}>
                                        {{ $provider->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('provider_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="requested_quantity" class="form-label">{{ __('messages.Requested_Quantity') }} <span class="text-danger">*</span></label>
                            <input type="number" name="requested_quantity" id="requested_quantity"
                                   class="form-control @error('requested_quantity') is-invalid @enderror"
                                   value="{{ old('requested_quantity') }}" min="1" required>
                            @error('requested_quantity')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success">
                                {{ __('messages.Create') }}
                            </button>
                            <a href="{{ route('bookRequests.index') }}" class="btn btn-secondary">
                                {{ __('messages.Cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection