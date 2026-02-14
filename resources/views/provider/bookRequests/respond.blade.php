@extends('layouts.provider')

@section('title', __('messages.submit_response'))
@section('page-title', __('messages.submit_response'))

@section('content')
<div class="page-header mb-4">
    <h1 class="page-title">{{ __('messages.submit_response') }}</h1>
    <p class="page-subtitle">{{ __('messages.respond_to_customer_requests') }}</p>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-edit me-2"></i>{{ __('messages.submit_response') }}
                </h5>
            </div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>{{ __('messages.Error') }}</strong>
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form action="{{ route('provider.bookRequests.storeResponse', $bookRequestItem->id) }}" method="POST" id="responseForm">
                    @csrf

                    <!-- Products Table -->
                    <div class="mb-4">
                        <h6 class="mb-3">{{ __('messages.product_details') }}</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th width="200">{{ __('messages.product') }}</th>
                                        <th width="110">{{ __('messages.Requested_Quantity') }}</th>
                                        <th width="110">{{ __('messages.Available_Quantity') }} <span class="text-danger">*</span></th>
                                        <th width="130">{{ __('messages.Price') }} <small>({{ __('messages.tax_inclusive') }})</small> <span class="text-danger">*</span></th>
                                        <th width="110">{{ __('messages.Tax') }} % <small>({{ __('messages.Optional') }})</small></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <input type="text" class="form-control" disabled value="{{ $bookRequestItem->product ? (app()->getLocale() == 'ar' ? $bookRequestItem->product->name_ar : $bookRequestItem->product->name_en) : __('messages.product_deleted') }}">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" disabled value="{{ $bookRequestItem->requested_quantity }}">
                                        </td>
                                        <td>
                                            <input type="number"
                                                   name="available_quantity"
                                                   id="available_quantity"
                                                   class="form-control @error('available_quantity') is-invalid @enderror"
                                                   value="{{ old('available_quantity', 0) }}"
                                                   min="0"
                                                   max="{{ $bookRequestItem->requested_quantity * 2 }}"
                                                   required
                                                   placeholder="0">
                                            @error('available_quantity')
                                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                            @enderror
                                        </td>
                                        <td>
                                            <input type="number"
                                                   step="any"
                                                   name="price"
                                                   id="price"
                                                   class="form-control @error('price') is-invalid @enderror"
                                                   value="{{ old('price', 0) }}"
                                                   min="0"
                                                   required
                                                   placeholder="0">
                                            @error('price')
                                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                            @enderror
                                        </td>
                                        <td>
                                            <input type="number"
                                                   step="0.01"
                                                   name="tax_percentage"
                                                   id="tax_percentage"
                                                   class="form-control @error('tax_percentage') is-invalid @enderror"
                                                   value="{{ old('tax_percentage', 0) }}"
                                                   min="0"
                                                   max="100"
                                                   placeholder="0.00">
                                            @error('tax_percentage')
                                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                            @enderror
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Expected Delivery Date -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="expected_delivery_date" class="form-label">
                                {{ __('messages.expected_delivery') }}
                                <span class="text-muted">({{ __('messages.Optional') }})</span>
                            </label>
                            <input type="date"
                                   name="expected_delivery_date"
                                   id="expected_delivery_date"
                                   class="form-control @error('expected_delivery_date') is-invalid @enderror"
                                   value="{{ old('expected_delivery_date') }}">
                            @error('expected_delivery_date')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Note -->
                    <div class="mb-4">
                        <label for="note" class="form-label">
                            {{ __('messages.Note') }}
                            <span class="text-muted">({{ __('messages.Optional') }})</span>
                        </label>
                        <textarea name="note"
                                  id="note"
                                  class="form-control @error('note') is-invalid @enderror"
                                  rows="3"
                                  placeholder="{{ __('messages.add_any_additional_notes') }}">{{ old('note') }}</textarea>
                        @error('note')
                            <span class="invalid-feedback d-block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Submit Buttons -->
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success btn-lg flex-grow-1">
                            <i class="fas fa-check-circle me-2"></i> {{ __('messages.Submit_Response') }}
                        </button>
                        <a href="{{ route('provider.bookRequests') }}"
                           class="btn btn-secondary btn-lg">
                            <i class="fas fa-times me-2"></i> {{ __('messages.Cancel') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

