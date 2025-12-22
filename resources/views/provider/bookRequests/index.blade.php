@extends('layouts.provider')

@section('title', __('messages.book_requests'))
@section('page-title', __('messages.book_requests'))

@section('content')
<div class="page-header">
    <h1 class="page-title">{{ __('messages.book_requests') }}</h1>
    <p class="page-subtitle">{{ __('messages.manage_incoming_book_requests') }}</p>
</div>

<!-- Statistics -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon primary">
            <i class="fas fa-book"></i>
        </div>
        <div class="stat-content">
            <h3>{{ $bookRequests->count() }}</h3>
            <p>{{ __('messages.total_requests') }}</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon success">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-content">
            <h3>{{ $bookRequests->filter(fn($r) => $r->responses->where('provider_id', auth('provider')->id())->count() > 0)->count() }}</h3>
            <p>{{ __('messages.requests_responded') }}</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon warning">
            <i class="fas fa-clock"></i>
        </div>
        <div class="stat-content">
            <h3>{{ $bookRequests->filter(fn($r) => $r->responses->where('provider_id', auth('provider')->id())->count() == 0)->count() }}</h3>
            <p>{{ __('messages.pending_response') }}</p>
        </div>
    </div>
</div>

<!-- Book Requests List -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-list me-2"></i>{{ __('messages.requests_list') }}
            <span class="badge bg-primary ms-2">{{ $bookRequests->count() }}</span>
        </h5>
    </div>
    <div class="card-body">
        @if($bookRequests->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>{{ __('messages.Product') }}</th>
                            <th>{{ __('messages.Requested_Quantity') }}</th>
                            <th>{{ __('messages.Status') }}</th>
                            <th>{{ __('messages.Created_Date') }}</th>
                            <th>{{ __('messages.Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bookRequests as $request)
                            @php
                                $hasResponse = $request->responses->where('provider_id', auth('provider')->id())->count() > 0;
                            @endphp
                            <tr>
                                <td>
                                    <strong>
                                        {{ app()->getLocale() == 'ar' ? $request->product->name_ar : $request->product->name_en }}
                                    </strong>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $request->requested_quantity }}</span>
                                </td>
                                <td>
                                    @if($hasResponse)
                                        <span class="badge bg-success">
                                            <i class="fas fa-check-circle"></i> {{ __('messages.responded') }}
                                        </span>
                                    @else
                                        <span class="badge bg-warning">
                                            <i class="fas fa-clock"></i> {{ __('messages.pending') }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    {{ $request->created_at->format('Y-m-d') }}
                                </td>
                                <td>
                                    <a href="{{ route('provider.bookRequests.show', $request->id) }}"
                                       class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i> {{ __('messages.View') }}
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-inbox" style="font-size: 3rem; color: #ccc;"></i>
                <p class="text-muted mt-3">{{ __('messages.No_Requests_Found') }}</p>
            </div>
        @endif
    </div>
</div>
@endsection