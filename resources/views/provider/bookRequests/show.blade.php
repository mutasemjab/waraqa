@extends('layouts.provider')

@section('title', __('messages.book_request_details'))
@section('page-title', __('messages.book_request_details'))

@section('content')
<div class="row">
    <div class="col-md-8">
        <!-- Request Details Card -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-file-invoice me-2"></i>{{ __('messages.request_details') }}
                </h5>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted">{{ __('messages.Product') }}</h6>
                        <h5>
                            @if($bookRequest->product)
                                {{ app()->getLocale() == 'ar' ? $bookRequest->product->name_ar : $bookRequest->product->name_en }}
                            @else
                                {{ __('messages.product_deleted') }}
                            @endif
                        </h5>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">{{ __('messages.Request_ID') }}</h6>
                        <h5>#{{ $bookRequest->id }}</h5>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted">{{ __('messages.Requested_Quantity') }}</h6>
                        <div>
                            <span class="badge bg-info fs-6">{{ $bookRequest->requested_quantity }} {{ __('messages.units') }}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">{{ __('messages.Request_Date') }}</h6>
                        <p>{{ $bookRequest->created_at->format('Y-m-d H:i') }}</p>
                    </div>
                </div>

                @if($bookRequest->product->description ?? null)
                    <div class="mb-4">
                        <h6 class="text-muted">{{ __('messages.Description') }}</h6>
                        <p>{{ $bookRequest->product->description }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Response Section -->
        @if(!$hasResponse)
            <div class="card mb-4 border-success">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-reply me-2"></i>{{ __('messages.submit_your_response') }}
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">
                        {{ __('messages.please_specify_available_quantity') }}
                    </p>
                    <a href="{{ route('provider.bookRequests.respond', $bookRequest->id) }}"
                       class="btn btn-success btn-lg w-100">
                        <i class="fas fa-plus-circle"></i> {{ __('messages.Submit_Response') }}
                    </a>
                </div>
            </div>
        @else
            <div class="card mb-4 border-success">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-check-circle me-2"></i>{{ __('messages.your_response') }}
                    </h5>
                </div>
                <div class="card-body">
                    @php
                        $myResponse = $bookRequest->responses->where('provider_id', $provider->id)->first();
                    @endphp
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">{{ __('messages.Available_Quantity') }}</h6>
                            <h4>
                                <span class="badge bg-info">{{ $myResponse->available_quantity }}</span>
                            </h4>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">{{ __('messages.Status') }}</h6>
                            @php
                                $statusClass = match($myResponse->status) {
                                    'pending' => 'warning',
                                    'approved' => 'success',
                                    'rejected' => 'danger',
                                    default => 'secondary'
                                };
                            @endphp
                            <p>
                                <span class="badge bg-{{ $statusClass }} fs-6">
                                    {{ __('messages.' . ucfirst($myResponse->status)) }}
                                </span>
                            </p>
                        </div>
                    </div>
                    @if($myResponse->note)
                        <div class="mt-3">
                            <h6 class="text-muted">{{ __('messages.Note') }}</h6>
                            <p>{{ $myResponse->note }}</p>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <!-- Sidebar -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">{{ __('messages.Quick_Info') }}</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted">{{ __('messages.Requested_By') }}</small>
                    <p><strong>{{ __('messages.Dar_Waraqa_Admin') }}</strong></p>
                </div>

                @if($bookRequest->product)
                    <div class="mb-3">
                        <small class="text-muted">{{ __('messages.Category') }}</small>
                        <p>
                            <strong>
                                @if($bookRequest->product->category)
                                    {{ app()->getLocale() == 'ar' ? $bookRequest->product->category->name_ar : $bookRequest->product->category->name_en }}
                                @else
                                    {{ __('messages.uncategorized') }}
                                @endif
                            </strong>
                        </p>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted">{{ __('messages.Price') }}</small>
                        <p>
                            <strong>{{ number_format($bookRequest->product->selling_price, 2) }}</strong>
                        </p>
                    </div>
                @endif

                <hr>

                <a href="{{ route('provider.bookRequests.index') }}"
                   class="btn btn-secondary w-100">
                    <i class="fas fa-arrow-left"></i> {{ __('messages.Back_to_Requests') }}
                </a>
            </div>
        </div>

        <!-- Timeline of other responses -->
        @if($bookRequest->responses->count() > 1)
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0">{{ __('messages.Other_Responses') }}</h6>
                </div>
                <div class="card-body">
                    @foreach($bookRequest->responses->where('provider_id', '!=', $provider->id) as $response)
                        <div class="mb-3 pb-3 border-bottom">
                            <h6 class="mb-1">{{ $response->provider->name }}</h6>
                            <small class="text-muted">
                                {{ __('messages.Available') }}:
                                <span class="badge bg-info">{{ $response->available_quantity }}</span>
                            </small>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
@endsection