@extends('layouts.user')

@section('title', __('messages.create_product_request'))

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0">{{ __('messages.create_product_request') }}</h1>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('user.sellerProductRequests.index') }}" class="btn btn-secondary">
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

    <div class="card">
        <div class="card-body">
            <form action="{{ route('user.sellerProductRequests.store') }}" method="POST" id="requestForm">
                @csrf

                <!-- Products Items -->
                <div class="mb-4">
                    <h5 class="mb-3">{{ __('messages.products') }}</h5>
                    <div id="itemsContainer">
                        <div class="row item-row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">{{ __('messages.product') }} *</label>
                                <select class="form-select product-select" name="items[0][product_id]" required>
                                    <option value="">{{ __('messages.select_product') }}</option>
                                    @foreach($products as $product)
                                    <option value="{{ $product->id }}">
                                        {{ $product->name_ar ?? $product->name_en }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('items.0.product_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">{{ __('messages.quantity') }} *</label>
                                <input type="number" class="form-control" name="items[0][requested_quantity]" min="1" required>
                                @error('items.0.requested_quantity')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="button" class="btn btn-danger remove-item" style="display: none;">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <button type="button" class="btn btn-outline-primary mt-3" id="addItemBtn">
                        <i class="fas fa-plus"></i> {{ __('messages.add_product') }}
                    </button>
                </div>

                <!-- Notes -->
                <div class="mb-4">
                    <label class="form-label">{{ __('messages.notes') }}</label>
                    <textarea class="form-control" name="note" rows="4" placeholder="{{ __('messages.enter_notes') }}"></textarea>
                    @error('note')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> {{ __('messages.create_request') }}
                    </button>
                    <a href="{{ route('user.sellerProductRequests.index') }}" class="btn btn-secondary">
                        {{ __('messages.cancel') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let itemIndex = 1;

    // Add Item Button
    document.getElementById('addItemBtn').addEventListener('click', function() {
        const container = document.getElementById('itemsContainer');
        const newRow = document.createElement('div');
        newRow.className = 'row item-row mb-3';
        newRow.innerHTML = `
            <div class="col-md-6">
                <select class="form-select product-select" name="items[${itemIndex}][product_id]" required>
                    <option value="">{{ __('messages.select_product') }}</option>
                    @foreach($products as $product)
                    <option value="{{ $product->id }}">
                        {{ $product->name_ar ?? $product->name_en }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <input type="number" class="form-control" name="items[${itemIndex}][requested_quantity]" min="1" required>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="button" class="btn btn-danger remove-item">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;
        container.appendChild(newRow);
        itemIndex++;
        updateRemoveButtons();
        updateProductOptions();
    });

    // Remove Item Button
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-item')) {
            e.preventDefault();
            e.target.closest('.item-row').remove();
            updateRemoveButtons();
            updateProductOptions();
        }
    });

    // Update product options when selection changes
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('product-select')) {
            updateProductOptions();
        }
    });

    function updateProductOptions() {
        const rows = document.querySelectorAll('.item-row');
        const selectedIds = new Set();

        // Collect all selected product IDs
        rows.forEach(row => {
            const select = row.querySelector('.product-select');
            if (select.value) {
                selectedIds.add(select.value);
            }
        });

        // Store all products data
        const allProductsData = [
            @foreach($products as $product)
            { id: '{{ $product->id }}', name: '{{ $product->name_ar ?? $product->name_en }}' },
            @endforeach
        ];

        // Update all selects
        rows.forEach(row => {
            const select = row.querySelector('.product-select');
            const currentValue = select.value;

            // Remove all options except the empty one
            while (select.options.length > 1) {
                select.remove(1);
            }

            // Add back available products
            allProductsData.forEach(product => {
                // Only add if it's not selected in other rows OR it's the current selection
                if (!selectedIds.has(product.id) || product.id === currentValue) {
                    const option = document.createElement('option');
                    option.value = product.id;
                    option.textContent = product.name;
                    if (product.id === currentValue) {
                        option.selected = true;
                    }
                    select.appendChild(option);
                }
            });
        });
    }

    function updateRemoveButtons() {
        const rows = document.querySelectorAll('.item-row');
        rows.forEach((row, index) => {
            const removeBtn = row.querySelector('.remove-item');
            if (rows.length === 1) {
                removeBtn.style.display = 'none';
            } else {
                removeBtn.style.display = 'block';
            }
        });
    }

    updateRemoveButtons();
    updateProductOptions();
});
</script>
@endsection
