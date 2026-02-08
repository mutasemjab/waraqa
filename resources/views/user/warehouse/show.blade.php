@extends('layouts.user')

@section('title', __('messages.warehouse') . ' - ' . $warehouse->name)
@section('page-title', __('messages.warehouse'))

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">{{ __('messages.warehouse') }} - {{ $warehouse->name }}</h1>
            <p class="page-subtitle">{{ __('messages.warehouse_inventory') }}</p>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-boxes me-2"></i>{{ __('messages.warehouse_details') }}
        </h5>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted">{{ __('messages.warehouse_name') }}</span>
                    <strong>{{ $warehouse->name }}</strong>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted">{{ __('messages.created_at') }}</span>
                    <strong>{{ $warehouse->created_at->format('Y-m-d') }}</strong>
                </div>
            </div>
        </div>

        <hr>

        <h6 class="mb-3">{{ __('messages.warehouse_products') }}</h6>

        @if (count($products) > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('messages.product') }}</th>
                            <th class="text-center">{{ __('messages.quantity') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $item)
                            <tr>
                                <td>{{ $item->product_name }}</td>
                                <td class="text-center">
                                    <span class="badge bg-primary">
                                        {{ max(0, $item->input_quantity - $item->output_quantity) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>{{ __('messages.your_warehouse_is_empty') }}
            </div>
        @endif
    </div>
</div>
@endsection
