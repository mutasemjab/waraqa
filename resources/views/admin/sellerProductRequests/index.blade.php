@extends('layouts.admin')

@section('title', __('messages.seller_product_requests'))

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0">{{ __('messages.seller_product_requests') }}</h1>
        </div>
    </div>

    @if($message = Session::get('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ $message }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if($message = Session::get('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ $message }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Filters -->
    <div class="card mb-4 border">
        <div class="card-header bg-light py-2">
            <h6 class="mb-0"><i class="fas fa-filter me-2"></i>{{ __('messages.filters') }}</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('sellerProductRequests.index') }}">
                <div class="row">
                    <!-- Seller -->
                    <div class="col-md-3 mb-3">
                        <label for="user_id">{{ __('messages.seller') }}</label>
                        <select name="user_id" id="user_id" class="form-control">
                            <option value="">{{ __('messages.all') }}</option>
                            @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status -->
                    <div class="col-md-3 mb-3">
                        <label for="status">{{ __('messages.status') }}</label>
                        <select name="status" id="status" class="form-control">
                            <option value="">{{ __('messages.all') }}</option>
                            @foreach(\App\Enums\SellerProductRequestStatus::cases() as $status)
                            <option value="{{ $status->value }}" {{ request('status') == $status->value ? 'selected' : '' }}>
                                {{ $status->getLabelLocalized() }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- From Date -->
                    <div class="col-md-3 mb-3">
                        <label for="from_date">{{ __('messages.from_date') }}</label>
                        <input type="date" name="from_date" id="from_date" class="form-control"
                               value="{{ request('from_date') }}">
                    </div>

                    <!-- To Date -->
                    <div class="col-md-3 mb-3">
                        <label for="to_date">{{ __('messages.to_date') }}</label>
                        <input type="date" name="to_date" id="to_date" class="form-control"
                               value="{{ request('to_date') }}">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-2"></i>{{ __('messages.search') }}
                        </button>
                        <a href="{{ route('sellerProductRequests.index') }}" class="btn btn-secondary">
                            <i class="fas fa-redo me-2"></i>{{ __('messages.reset') }}
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h6 class="text-muted">{{ __('messages.total_product_requests') }}</h6>
                    <h3 class="mb-0">{{ $requests->total() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h6 class="text-muted">{{ __('messages.pending_requests') }}</h6>
                    <h3 class="mb-0 text-warning">
                        {{ \App\Models\SellerProductRequest::where('status', \App\Enums\SellerProductRequestStatus::PENDING->value)->count() }}
                    </h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h6 class="text-muted">{{ __('messages.approved_requests') }}</h6>
                    <h3 class="mb-0 text-success">
                        {{ \App\Models\SellerProductRequest::where('status', \App\Enums\SellerProductRequestStatus::APPROVED->value)->count() }}
                    </h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h6 class="text-muted">{{ __('messages.rejected_requests') }}</h6>
                    <h3 class="mb-0 text-danger">
                        {{ \App\Models\SellerProductRequest::where('status', \App\Enums\SellerProductRequestStatus::REJECTED->value)->count() }}
                    </h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Requests Table -->
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>{{ __('messages.seller') }}</th>
                        <th>{{ __('messages.products') }}</th>
                        <th>{{ __('messages.status') }}</th>
                        <th>{{ __('messages.created_at') }}</th>
                        <th>{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $request)
                    <tr>
                        <td>
                            <small class="text-muted">#{{ $request->id }}</small>
                        </td>
                        <td>
                            <div class="small">
                                <strong>{{ $request->user->name }}</strong>
                                <br>
                                <small class="text-muted">{{ $request->user->email }}</small>
                            </div>
                        </td>
                        <td>
                            <div class="small">
                                @foreach($request->items->take(3) as $item)
                                <div>
                                    {{ $item->product->name_ar ?? $item->product->name_en }}
                                    <span class="badge bg-secondary">{{ $item->requested_quantity }}</span>
                                </div>
                                @endforeach
                                @if($request->items->count() > 3)
                                <div class="text-muted">+{{ $request->items->count() - 3 }} {{ __('messages.more') }}</div>
                                @endif
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-{{ $request->status->getColor() }}">
                                {{ $request->status->getLabelLocalized() }}
                            </span>
                        </td>
                        <td>
                            <small>{{ $request->created_at->format('d/m/Y H:i') }}</small>
                        </td>
                        <td>
                            <a href="{{ route('sellerProductRequests.show', $request) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if($request->status === \App\Enums\SellerProductRequestStatus::PENDING)
                            <a href="{{ route('sellerProductRequests.approve.form', $request) }}" class="btn btn-sm btn-success">
                                <i class="fas fa-check"></i>
                            </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            {{ __('messages.no_data') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $requests->links() }}
    </div>
</div>
@endsection
