@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="card">
                <div class="card-header">
                    <h4>{{ __('messages.Edit_Product') }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Basic Information -->
                            <div class="col-md-6">
                                <h5 class="mb-3">{{ __('messages.Basic_Information') }}</h5>

                                <div class="mb-3">
                                    <label for="name_ar" class="form-label">{{ __('messages.Name_Arabic') }}</label>
                                    <input type="text" class="form-control @error('name_ar') is-invalid @enderror"
                                           id="name_ar" name="name_ar" value="{{ old('name_ar', $product->name_ar) }}" required>
                                    @error('name_ar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-3">
                                    <label for="name_en" class="form-label">{{ __('messages.Name_English') }}</label>
                                    <input type="text" class="form-control @error('name_en') is-invalid @enderror"
                                           id="name_en" name="name_en" value="{{ old('name_en', $product->name_en) }}" required>
                                    @error('name_en')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-3">
                                    <label for="sku" class="form-label">{{ __('messages.SKU') }}</label>
                                    <input type="text" class="form-control @error('sku') is-invalid @enderror"
                                           id="sku" name="sku" value="{{ old('sku', $product->sku) }}">
                                    @error('sku')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-3">
                                    <label for="category_id" class="form-label">{{ __('messages.Category') }}</label>
                                    <select class="form-control @error('category_id') is-invalid @enderror"
                                            id="category_id" name="category_id">
                                        <option value="">{{ __('messages.Select_Category') }}</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                                {{ app()->getLocale() == 'ar' ? $category->name_ar : $category->name_en }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-3">
                                    <label for="provider_id" class="form-label">{{ __('messages.provider') }}</label>
                                    <select class="form-control @error('provider_id') is-invalid @enderror"
                                            id="provider_id" name="provider_id">
                                        <option value="">{{ __('messages.Select_provider') }}</option>
                                        @foreach($providers as $provider)
                                            <option value="{{ $provider->id }}" {{ old('provider_id', $product->provider_id) == $provider->id ? 'selected' : '' }}>
                                                {{ $provider->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('provider_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-3">
                                    <label for="selling_price" class="form-label">{{ __('messages.Price_with_tax') }} (15%)</label>
                                    <input type="number" class="form-control @error('selling_price') is-invalid @enderror"
                                           id="selling_price" name="selling_price"
                                           value="{{ old('selling_price', $product->selling_price) }}"
                                           step="any" min="0" required>
                                    @error('selling_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-3">
                                    <label for="tax" class="form-label">{{ __('messages.Tax_Value') }}</label>
                                    <input type="number" class="form-control"
                                           id="tax" name="tax" value="{{ old('tax', $product->tax ?? 15) }}" step="any" min="0">
                                </div>

                            </div>
                        </div>

                        <!-- Images -->
                        <div class="row">
                            <div class="col-md-12">

                                @if($product->photo)
                                    <div class="mb-3">
                                        <img src="{{ asset('storage/' . $product->photo) }}" alt="Product Image" width="150">
                                    </div>
                                @endif

                                <div class="mb-3">
                                    <label for="photo" class="form-label">{{ __('messages.Upload_Image') }}</label>
                                    <input type="file" class="form-control @error('photo') is-invalid @enderror"
                                           id="photo" name="photo">
                                    @error('photo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">{{ __('messages.Update') }}</button>
                            <a href="{{ route('products.index') }}" class="btn btn-secondary">{{ __('messages.Cancel') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
