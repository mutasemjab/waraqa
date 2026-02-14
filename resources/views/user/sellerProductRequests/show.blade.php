@extends('layouts.user')

@section('title', __('messages.product_request_details'))

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0">{{ __('messages.product_request_details') }} #{{ $sellerProductRequest->id }}</h1>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('user.sellerProductRequests.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> {{ __('messages.back') }}
            </a>
        </div>
    </div>

    @if($message = Session::get('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ $message }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        <!-- Request Status -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body">
                    <h6 class="text-muted mb-3">{{ __('messages.request_status') }}</h6>
                    <div>
                        <span class="badge bg-{{ $sellerProductRequest->status->getColor() }} p-2">
                            {{ $sellerProductRequest->status->getLabelLocalized() }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h6 class="text-muted mb-3">{{ __('messages.created_at') }}</h6>
                    <p class="mb-0">{{ $sellerProductRequest->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>

            @if($sellerProductRequest->approved_at)
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted mb-3">{{ __('messages.approved_at') }}</h6>
                    <p class="mb-2">{{ $sellerProductRequest->approved_at->format('d/m/Y H:i') }}</p>
                    @if($sellerProductRequest->approver)
                    <small class="text-muted">{{ __('messages.approved_by') }}: {{ $sellerProductRequest->approver->name }}</small>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Request Details -->
        <div class="col-md-8">
            <!-- Products Items -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('messages.products') }}</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('messages.product') }}</th>
                                <th>{{ __('messages.requested_quantity') }}</th>
                                <th>{{ __('messages.approved_quantity') }}</th>
                                <th>{{ __('messages.approved_price') }}</th>
                                <th>{{ __('messages.tax_percentage') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sellerProductRequest->items as $item)
                            <tr>
                                <td>
                                    <div>
                                        <strong>{{ $item->product->name_ar ?? $item->product->name_en }}</strong>
                                        @if($item->product->category)
                                        <br>
                                        <small class="text-muted">{{ $item->product->category->name_ar ?? $item->product->category->name_en }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>{{ $item->requested_quantity }}</td>
                                <td>
                                    @if($item->approved_quantity)
                                    <span class="badge bg-success">{{ $item->approved_quantity }}</span>
                                    @else
                                    <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($item->approved_price)
                                    {{ number_format($item->approved_price, 2) }}
                                    @else
                                    <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($item->approved_tax_percentage)
                                    {{ number_format($item->approved_tax_percentage, 2) }}%
                                    @else
                                    <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Notes -->
            @if($sellerProductRequest->note)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('messages.notes') }}</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $sellerProductRequest->note }}</p>
                </div>
            </div>
            @endif

            <!-- Rejection Reason -->
            @if($sellerProductRequest->rejection_reason)
            <div class="card border-danger mb-4">
                <div class="card-header bg-danger-subtle">
                    <h5 class="mb-0 text-danger">{{ __('messages.rejection_reason') }}</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $sellerProductRequest->rejection_reason }}</p>
                </div>
            </div>
            @endif

            <!-- Order Link -->
            @if($sellerProductRequest->order)
            <div class="card border-success mb-4">
                <div class="card-header bg-success-subtle">
                    <h5 class="mb-0 text-success">{{ __('messages.created_order') }}</h5>
                </div>
                <div class="card-body">
                    <p class="mb-2">
                        <strong>{{ __('messages.order_number') }}:</strong>
                        <a href="{{ route('user.orders.show', $sellerProductRequest->order->id) }}">
                            {{ $sellerProductRequest->order->number }}
                        </a>
                    </p>
                    <p class="mb-0">
                        <strong>{{ __('messages.total_amount') }}:</strong>
                        {{ number_format($sellerProductRequest->order->total_prices, 2) }}
                    </p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
