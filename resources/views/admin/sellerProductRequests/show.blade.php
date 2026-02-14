@extends('layouts.admin')

@section('title', __('messages.product_request_details'))

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0">{{ __('messages.product_request_details') }} #{{ $sellerProductRequest->id }}</h1>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('sellerProductRequests.index') }}" class="btn btn-secondary">
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
        <!-- Seller Info -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('messages.seller') }}</h5>
                </div>
                <div class="card-body">
                    <p class="mb-2">
                        <strong>{{ $sellerProductRequest->user->name }}</strong>
                    </p>
                    <p class="mb-2">
                        <small class="text-muted">{{ $sellerProductRequest->user->email }}</small>
                    </p>
                    @if($sellerProductRequest->user->phone)
                    <p class="mb-0">
                        <small class="text-muted">{{ $sellerProductRequest->user->phone }}</small>
                    </p>
                    @endif
                </div>
            </div>

            <!-- Status -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('messages.request_status') }}</h5>
                </div>
                <div class="card-body">
                    <span class="badge bg-{{ $sellerProductRequest->status->getColor() }} p-2">
                        {{ $sellerProductRequest->status->getLabelLocalized() }}
                    </span>
                </div>
            </div>

            <!-- Timeline -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('messages.timeline') }}</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-secondary"></div>
                            <div class="timeline-content">
                                <p class="mb-1"><small>{{ __('messages.created_at') }}</small></p>
                                <p class="mb-0">{{ $sellerProductRequest->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                        @if($sellerProductRequest->approved_at)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <p class="mb-1"><small>{{ __('messages.approved_at') }}</small></p>
                                <p class="mb-0">{{ $sellerProductRequest->approved_at->format('d/m/Y H:i') }}</p>
                                @if($sellerProductRequest->approver)
                                <p class="mb-0"><small class="text-muted">{{ $sellerProductRequest->approver->name }}</small></p>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Request Details -->
        <div class="col-md-8">
            <!-- Products -->
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
            <div class="card border-success">
                <div class="card-header bg-success-subtle">
                    <h5 class="mb-0 text-success">{{ __('messages.created_order') }}</h5>
                </div>
                <div class="card-body">
                    <p class="mb-2">
                        <strong>{{ __('messages.order_number') }}:</strong>
                        {{ $sellerProductRequest->order->number }}
                    </p>
                    <p class="mb-0">
                        <strong>{{ __('messages.total_amount') }}:</strong>
                        {{ number_format($sellerProductRequest->order->total_prices, 2) }}
                    </p>
                </div>
            </div>
            @endif

            <!-- Actions -->
            @if($sellerProductRequest->status === \App\Enums\SellerProductRequestStatus::PENDING)
            <div class="mt-4 d-flex gap-2">
                <a href="{{ route('sellerProductRequests.approve.form', $sellerProductRequest) }}" class="btn btn-success">
                    <i class="fas fa-check"></i> {{ __('messages.approve') }}
                </a>
                <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                    <i class="fas fa-times"></i> {{ __('messages.reject') }}
                </button>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('messages.reject_request') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('sellerProductRequests.reject', $sellerProductRequest) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.rejection_reason') }} *</label>
                        <textarea class="form-control" name="rejection_reason" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        {{ __('messages.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-danger">
                        {{ __('messages.reject') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
