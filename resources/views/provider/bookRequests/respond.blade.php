@extends('layouts.provider')

@section('title', __('messages.submit_response'))
@section('page-title', __('messages.submit_response'))

@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2">
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

                <!-- Request Summary -->
                <div class="alert alert-info mb-4">
                    <h6 class="mb-2">{{ __('messages.Request_Summary') }}</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-1">
                                <strong>{{ __('messages.Product') }}:</strong><br>
                                @if($bookRequest->product)
                                    {{ app()->getLocale() == 'ar' ? $bookRequest->product->name_ar : $bookRequest->product->name_en }}
                                @else
                                    {{ __('messages.product_deleted') }}
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1">
                                <strong>{{ __('messages.Requested_Quantity') }}:</strong><br>
                                <span class="badge bg-primary fs-6">{{ $bookRequest->requested_quantity }} {{ __('messages.units') }}</span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Response Form -->
                <form action="{{ route('provider.bookRequests.storeResponse', $bookRequest->id) }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label for="available_quantity" class="form-label">
                                    {{ __('messages.Available_Quantity') }}
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="number"
                                           name="available_quantity"
                                           id="available_quantity"
                                           class="form-control form-control-lg @error('available_quantity') is-invalid @enderror"
                                           value="{{ old('available_quantity', 0) }}"
                                           min="0"
                                           max="{{ $bookRequest->requested_quantity * 2 }}"
                                           required
                                           placeholder="0">
                                    <span class="input-group-text">{{ __('messages.units') }}</span>
                                </div>
                                <small class="form-text text-muted d-block mt-2">
                                    {{ __('messages.specify_available_quantity_info', ['quantity' => $bookRequest->requested_quantity]) }}
                                </small>
                                @error('available_quantity')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-4">
                                <label for="price" class="form-label">
                                    {{ __('messages.Price') }}
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="number"
                                           step="any"
                                           name="price"
                                           id="price"
                                           class="form-control form-control-lg @error('price') is-invalid @enderror"
                                           value="{{ old('price') }}"
                                           min="0"
                                           required
                                           placeholder="0">
                                    <span class="input-group-text">{{ __('messages.currency') ?? 'KWD' }}</span>
                                </div>
                                @error('price')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="tax_percentage" class="form-label">
                            {{ __('messages.Tax_Percentage') }}
                            <span class="text-muted">({{ __('messages.Optional') }})</span>
                        </label>
                        <div class="input-group">
                            <input type="number"
                                   step="0.01"
                                   name="tax_percentage"
                                   id="tax_percentage"
                                   class="form-control form-control-lg @error('tax_percentage') is-invalid @enderror"
                                   value="{{ old('tax_percentage', 0) }}"
                                   min="0"
                                   max="100"
                                   placeholder="0.00">
                            <span class="input-group-text">%</span>
                        </div>
                        <small class="form-text text-muted d-block mt-2">
                            {{ __('messages.tax_percentage_info') ?? 'حدد نسبة الضريبة المطبقة على هذا المنتج' }}
                        </small>
                        @error('tax_percentage')
                            <span class="invalid-feedback d-block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="note" class="form-label">
                            {{ __('messages.Note') }}
                            <span class="text-muted">({{ __('messages.Optional') }})</span>
                        </label>
                        <textarea name="note"
                                  id="note"
                                  class="form-control @error('note') is-invalid @enderror"
                                  rows="4"
                                  placeholder="{{ __('messages.add_any_additional_notes') }}">{{ old('note') }}</textarea>
                        <small class="form-text text-muted d-block mt-2">
                            {{ __('messages.note_could_contain_delivery_time') }}
                        </small>
                        @error('note')
                            <span class="invalid-feedback d-block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success btn-lg flex-grow-1">
                            <i class="fas fa-check-circle"></i> {{ __('messages.Submit_Response') }}
                        </button>
                        <a href="{{ route('provider.bookRequests.show', $bookRequest->id) }}"
                           class="btn btn-secondary btn-lg">
                            <i class="fas fa-times"></i> {{ __('messages.Cancel') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Help Section -->
        <div class="card mt-4">
            <div class="card-header bg-light">
                <h6 class="mb-0">{{ __('messages.How_To_Respond') }}</h6>
            </div>
            <div class="card-body">
                <ol class="mb-0">
                    <li>{{ __('messages.specify_available_qty_info') }}</li>
                    <li>{{ __('messages.add_delivery_timeline_note') }}</li>
                    <li>{{ __('messages.submit_response_and_wait') }}</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection