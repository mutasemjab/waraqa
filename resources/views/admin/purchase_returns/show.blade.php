{{-- resources/views/admin/purchase_returns/show.blade.php --}}
@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>{{ __('messages.purchase_returns') }} - {{ $purchaseReturn->number }}</h4>
                    <div>
                        <a href="{{ route('admin.purchase-returns.edit', $purchaseReturn) }}" class="btn btn-warning btn-sm">
                            {{ __('messages.edit') }}
                        </a>
                        <a href="{{ route('admin.purchase-returns.index') }}" class="btn btn-secondary btn-sm">
                            {{ __('messages.back') }}
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="mb-3">{{ __('messages.return_details') }}</h5>
                            <table class="table table-sm">
                                <tr>
                                    <th>{{ __('messages.return_number') }}:</th>
                                    <td>{{ $purchaseReturn->number }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('messages.return_date') }}:</th>
                                    <td>{{ $purchaseReturn->return_date->format('M d, Y') }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('messages.return_status') }}:</th>
                                    <td>
                                        @if($purchaseReturn->status == 'pending')
                                            <span class="badge bg-warning">{{ __('messages.pending') }}</span>
                                        @elseif($purchaseReturn->status == 'sent')
                                            <span class="badge bg-info">{{ __('messages.sent') }}</span>
                                        @else
                                            <span class="badge bg-success">{{ __('messages.received') }}</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>{{ __('messages.warehouse') }}:</th>
                                    <td>{{ $purchaseReturn->warehouse->name ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <h5 class="mb-3">{{ __('messages.purchase_details') }}</h5>
                            <table class="table table-sm">
                                <tr>
                                    <th>{{ __('messages.purchase_number') }}:</th>
                                    <td>{{ $purchaseReturn->purchase->purchase_number }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('messages.provider') }}:</th>
                                    <td>{{ $purchaseReturn->purchase->provider->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('messages.purchase_date') }}:</th>
                                    <td>{{ $purchaseReturn->purchase->created_at->format('M d, Y') }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('messages.original_total') }}:</th>
                                    <td>
                                        <x-riyal-icon />
                                        {{ number_format($purchaseReturn->purchase->total_amount, 2) }}
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-12">
                            @if($purchaseReturn->reason)
                                <h5>{{ __('messages.return_reason') }}</h5>
                                <p class="text-muted">{{ $purchaseReturn->reason }}</p>
                            @endif

                            @if($purchaseReturn->notes)
                                <h5>{{ __('messages.notes') }}</h5>
                                <p class="text-muted">{{ $purchaseReturn->notes }}</p>
                            @endif
                        </div>
                    </div>

                    <h5 class="mb-3">{{ __('messages.returned_items') }}</h5>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('messages.product') }}</th>
                                    <th>{{ __('messages.quantity') }}</th>
                                    <th>{{ __('messages.unit_price') }}</th>
                                    <th>{{ __('messages.total') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($purchaseReturn->returnItems as $item)
                                <tr>
                                    <td>{{ $item->product->name_en ?? $item->product->name_ar }}</td>
                                    <td>{{ $item->quantity_returned }}</td>
                                    <td>
                                        <x-riyal-icon />
                                        {{ number_format($item->unit_price, 2) }}
                                    </td>
                                    <td>
                                        <x-riyal-icon />
                                        {{ number_format($item->total_price, 2) }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">
                                        {{ __('messages.no_items_found') }}
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="3" class="text-end"><strong>{{ __('messages.total') }}:</strong></td>
                                    <td>
                                        <strong>
                                            <x-riyal-icon />
                                            {{ number_format($purchaseReturn->total_amount, 2) }}
                                        </strong>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="mt-3">
                        <a href="{{ route('admin.purchase-returns.index') }}" class="btn btn-secondary">
                            {{ __('messages.back') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
