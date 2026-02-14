@extends('layouts.user')

@section('title', __('messages.seller_product_requests'))

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0">{{ __('messages.my_product_requests') }}</h1>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('user.sellerProductRequests.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> {{ __('messages.create_product_request') }}
            </a>
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
                        {{ Auth::user()->sellerProductRequests()->where('status', \App\Enums\SellerProductRequestStatus::PENDING->value)->count() }}
                    </h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h6 class="text-muted">{{ __('messages.approved_requests') }}</h6>
                    <h3 class="mb-0 text-success">
                        {{ Auth::user()->sellerProductRequests()->where('status', \App\Enums\SellerProductRequestStatus::APPROVED->value)->count() }}
                    </h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h6 class="text-muted">{{ __('messages.rejected_requests') }}</h6>
                    <h3 class="mb-0 text-danger">
                        {{ Auth::user()->sellerProductRequests()->where('status', \App\Enums\SellerProductRequestStatus::REJECTED->value)->count() }}
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
                        <th>{{ __('messages.products') }}</th>
                        <th>{{ __('messages.request_status') }}</th>
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
                                @foreach($request->items->take(3) as $item)
                                <div>{{ $item->product->name_ar ?? $item->product->name_en }} ({{ $item->requested_quantity }})</div>
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
                            <small>{{ $request->created_at->format('d/m/Y') }}</small>
                        </td>
                        <td>
                            <a href="{{ route('user.sellerProductRequests.show', $request) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if($request->status === \App\Enums\SellerProductRequestStatus::PENDING)
                            <form action="{{ route('user.sellerProductRequests.destroy', $request) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('{{ __('messages.confirm_delete') }}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">
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
