@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Book Request Details -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>{{ __('messages.Book_Request_Details') }}</h4>
                    <a href="{{ route('bookRequests.index') }}" class="btn btn-secondary">
                        {{ __('messages.Back') }}
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">{{ __('messages.Product') }}</h6>
                            <p>
                                <strong>
                                    {{ app()->getLocale() == 'ar' ? $bookRequest->product->name_ar : $bookRequest->product->name_en }}
                                </strong>
                            </p>

                            <h6 class="text-muted">{{ __('messages.provider') }}</h6>
                            <p>
                                <strong>{{ $bookRequest->provider->name }}</strong>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">{{ __('messages.Requested_Quantity') }}</h6>
                            <p>
                                <span class="badge bg-primary fs-6">{{ $bookRequest->requested_quantity }}</span>
                            </p>

                            <h6 class="text-muted">{{ __('messages.Created_Date') }}</h6>
                            <p>{{ $bookRequest->created_at->format('Y-m-d H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Provider Responses -->
            <div class="card">
                <div class="card-header">
                    <h4>{{ __('messages.Provider_Responses') }}</h4>
                </div>
                <div class="card-body">
                    @if($bookRequest->responses->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>{{ __('messages.provider') }}</th>
                                        <th>{{ __('messages.Available_Quantity') }}</th>
                                        <th>{{ __('messages.Status') }}</th>
                                        <th>{{ __('messages.Note') }}</th>
                                        <th>{{ __('messages.Response_Date') }}</th>
                                        <th>{{ __('messages.Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($bookRequest->responses as $response)
                                        <tr>
                                            <td>
                                                <strong>{{ $response->provider->name }}</strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $response->available_quantity }}</span>
                                            </td>
                                            <td>
                                                @php
                                                    $statusClass = match($response->status) {
                                                        'pending' => 'warning',
                                                        'approved' => 'success',
                                                        'rejected' => 'danger',
                                                        default => 'secondary'
                                                    };
                                                @endphp
                                                <span class="badge bg-{{ $statusClass }}">
                                                    {{ __('messages.' . ucfirst($response->status)) }}
                                                </span>
                                            </td>
                                            <td>
                                                {{ $response->note ?? '-' }}
                                            </td>
                                            <td>
                                                {{ $response->created_at->format('Y-m-d H:i') }}
                                            </td>
                                            <td>
                                                @if($response->status == 'pending')
                                                    <button type="button" class="btn btn-sm btn-success"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#approveModal{{ $response->id }}">
                                                        {{ __('messages.Approve') }}
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-danger"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#rejectModal{{ $response->id }}">
                                                        {{ __('messages.Reject') }}
                                                    </button>
                                                @else
                                                    <span class="text-muted">{{ __('messages.No_Actions') }}</span>
                                                @endif
                                            </td>
                                        </tr>

                                        <!-- Approve Modal -->
                                        <div class="modal fade" id="approveModal{{ $response->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">{{ __('messages.Approve_Response') }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form action="#" method="POST">
                                                        @csrf
                                                        <div class="modal-body">
                                                            <p>{{ __('messages.Confirm_Approve_Message') }}</p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                                {{ __('messages.Cancel') }}
                                                            </button>
                                                            <button type="submit" class="btn btn-success">
                                                                {{ __('messages.Approve') }}
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Reject Modal -->
                                        <div class="modal fade" id="rejectModal{{ $response->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">{{ __('messages.Reject_Response') }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form action="#" method="POST">
                                                        @csrf
                                                        <div class="modal-body">
                                                            <p>{{ __('messages.Confirm_Reject_Message') }}</p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                                {{ __('messages.Cancel') }}
                                                            </button>
                                                            <button type="submit" class="btn btn-danger">
                                                                {{ __('messages.Reject') }}
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            {{ __('messages.No_Responses_Yet') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection