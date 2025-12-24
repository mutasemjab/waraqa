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

                        <x-search-select
                            :model="'App\Models\Product'"
                            fieldName="product_id"
                            label="Product"
                            placeholder="Search for product..."
                            :limit="10"
                            :required="true"
                            :value="old('product_id')"
                            displayColumn="name_ar"
                        />

                        <x-search-select
                            :model="'App\Models\Provider'"
                            fieldName="provider_id"
                            label="provider"
                            placeholder="Search for provider..."
                            :limit="10"
                            :required="true"
                            :value="old('provider_id')"
                            displayColumn="name"
                        />

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