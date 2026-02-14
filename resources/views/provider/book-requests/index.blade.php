@extends('layouts.provider')

@section('title', __('messages.book_requests'))
@section('page-title', __('messages.pending_book_requests'))

@section('content')
<div class="page-header mb-4">
    <h1 class="page-title">{{ __('messages.pending_book_requests') }}</h1>
    <p class="page-subtitle">{{ __('messages.respond_to_customer_requests') }}</p>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-list-check me-2"></i>{{ __('messages.book_requests_awaiting_response') }}
        </h5>
    </div>
    <div class="card-body">
        @if($bookRequests->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover table-sm">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('messages.request_number') }}</th>
                            <th>{{ __('messages.quantity') }}</th>
                            <th>{{ __('messages.requested_date') }}</th>
                            <th>{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bookRequests as $request)
                        <tr>
                            <td>
                                <strong>#{{ $request->id }}</strong>
                            </td>
                            <td>
                                <small>
                                    @php
                                        $totalQty = $request->items->sum('requested_quantity');
                                    @endphp
                                    {{ $totalQty }} {{ __('messages.units') }}
                                </small>
                            </td>
                            <td>
                                <small>{{ $request->created_at->format('Y-m-d') }}</small>
                            </td>
                            <td>
                                @php
                                    $firstItem = $request->items->first();
                                @endphp
                                @if($firstItem)
                                    <a href="{{ route('provider.bookRequests.respond', $firstItem->id) }}" class="btn btn-sm btn-success">
                                        <i class="fas fa-reply"></i> {{ __('messages.respond') }}
                                    </a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $bookRequests->links() }}
            </div>
        @else
            <div class="alert alert-success" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ __('messages.no_pending_book_requests') }}
            </div>
        @endif
    </div>
</div>

@endsection
