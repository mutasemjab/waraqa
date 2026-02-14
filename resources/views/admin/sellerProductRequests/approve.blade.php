@extends('layouts.admin')

@section('title', __('messages.approve_request'))

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0">{{ __('messages.approve_request') }} #{{ $sellerProductRequest->id }}</h1>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('sellerProductRequests.show', $sellerProductRequest) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> {{ __('messages.back') }}
            </a>
        </div>
    </div>

    @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>{{ __('messages.validation_errors') }}</strong>
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        <!-- Seller Info -->
        <div class="col-md-4">
            <div class="card">
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
        </div>

        <!-- Approval Form -->
        <div class="col-md-8">
            <form action="{{ route('sellerProductRequests.approve', $sellerProductRequest) }}" method="POST" id="approveForm">
                @csrf

                <!-- From Warehouse -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('messages.warehouse_settings') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">{{ __('messages.from_warehouse') }} *</label>
                            <select name="from_warehouse_id" class="form-control" required>
                                <option value="">{{ __('messages.select_warehouse') }}</option>
                                @forelse($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}">
                                    {{ $warehouse->name ?? 'Warehouse #' . $warehouse->id }}
                                </option>
                                @empty
                                <option value="" disabled>{{ __('messages.no_warehouses_available') }}</option>
                                @endforelse
                            </select>
                            @error('from_warehouse_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Products Items -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('messages.products') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('messages.product') }}</th>
                                        <th>{{ __('messages.requested_quantity') }}</th>
                                        <th>{{ __('messages.approved_quantity') }}</th>
                                        <th>{{ __('messages.price_with_tax') }}</th>
                                        <th>{{ __('messages.tax_percentage') }}</th>
                                        <th>{{ __('messages.subtotal') }}</th>
                                    </tr>
                                </thead>
                                <tbody id="itemsBody">
                                    @foreach($sellerProductRequest->items as $index => $item)
                                    <tr class="item-row" data-item-id="{{ $item->id }}" data-item-index="{{ $index }}">
                                        <td>
                                            <div class="small">
                                                <strong>{{ $item->product->name_ar ?? $item->product->name_en }}</strong>
                                                @if($item->product->category)
                                                <br>
                                                <small class="text-muted">{{ $item->product->category->name_ar ?? $item->product->category->name_en }}</small>
                                                @endif
                                            </div>
                                            <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $item->requested_quantity }}</span>
                                        </td>
                                        <td>
                                            <input type="number"
                                                   class="form-control quantity-input"
                                                   name="items[{{ $index }}][quantity]"
                                                   value="{{ $item->requested_quantity }}"
                                                   min="1"
                                                   max="{{ $item->requested_quantity }}"
                                                   required>
                                        </td>
                                        <td>
                                            <input type="number"
                                                   class="form-control price-input"
                                                   name="items[{{ $index }}][price_with_tax]"
                                                   step="0.01"
                                                   min="0"
                                                   placeholder="0.00"
                                                   value="{{ $item->product->selling_price ?? '' }}"
                                                   required>
                                        </td>
                                        <td>
                                            <input type="number"
                                                   class="form-control tax-input"
                                                   name="items[{{ $index }}][tax_percentage]"
                                                   step="0.01"
                                                   min="0"
                                                   max="100"
                                                   placeholder="0"
                                                   value="{{ $item->product->tax ?? 0 }}">
                                        </td>
                                        <td>
                                            <span class="subtotal">0.00</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <td colspan="5" class="text-end"><strong>{{ __('messages.total') }}:</strong></td>
                                        <td>
                                            <strong class="total-amount">0.00</strong>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check"></i> {{ __('messages.approve') }}
                    </button>
                    <a href="{{ route('sellerProductRequests.show', $sellerProductRequest) }}" class="btn btn-secondary">
                        {{ __('messages.cancel') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('approveForm');
    const itemRows = document.querySelectorAll('.item-row');

    function updateRowSubtotal(row) {
        const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
        const priceWithTax = parseFloat(row.querySelector('.price-input').value) || 0;

        const subtotal = quantity * priceWithTax;
        row.querySelector('.subtotal').textContent = subtotal.toFixed(2);

        updateTotalAmount();
    }

    function updateTotalAmount() {
        let total = 0;
        itemRows.forEach(row => {
            const subtotalText = row.querySelector('.subtotal').textContent;
            total += parseFloat(subtotalText) || 0;
        });
        document.querySelector('.total-amount').textContent = total.toFixed(2);
    }

    itemRows.forEach(row => {
        row.querySelector('.quantity-input').addEventListener('change', () => updateRowSubtotal(row));
        row.querySelector('.price-input').addEventListener('input', () => updateRowSubtotal(row));
        row.querySelector('.tax-input').addEventListener('input', () => updateRowSubtotal(row));

        // Calculate subtotal on page load with default values
        updateRowSubtotal(row);
    });
});
</script>
@endsection
