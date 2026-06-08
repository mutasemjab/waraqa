@extends('layouts.admin')

@section('title', __('messages.add_products_directly'))

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0">{{ __('messages.add_products_directly') }}</h1>
            <small class="text-muted d-block mt-2">{{ __('messages.add_products_directly_desc') }}</small>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('sellerProductRequests.index') }}" class="btn btn-secondary">
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

    <form action="{{ route('sellerProductRequests.store-direct') }}" method="POST" id="directAddForm">
        @csrf

        <div class="row">
            <!-- Seller Selection -->
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('messages.seller_information') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">{{ __('messages.seller') }} *</label>
                            <select name="user_id" id="sellerSelect" class="form-control" required>
                                <option value="">{{ __('messages.select_seller') }}</option>
                                @forelse($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                @empty
                                <option value="" disabled>{{ __('messages.no_sellers_available') }}</option>
                                @endforelse
                            </select>
                            @error('user_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Warehouse Selection -->
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('messages.warehouse_settings') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">{{ __('messages.from_warehouse') }} *</label>
                            <select name="from_warehouse_id" id="warehouseSelect" class="form-control" required>
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
            </div>
        </div>

        <!-- Products Section -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ __('messages.products') }}</h5>
                <button type="button" class="btn btn-sm btn-primary" id="addProductBtn">
                    <i class="fas fa-plus"></i> {{ __('messages.add_product') }}
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 30%">{{ __('messages.product') }}</th>
                                <th style="width: 15%">{{ __('messages.quantity') }}</th>
                                <th style="width: 18%">{{ __('messages.price_with_tax') }}</th>
                                <th style="width: 15%">{{ __('messages.tax_percentage') }}</th>
                                <th style="width: 12%">{{ __('messages.subtotal') }}</th>
                                <th style="width: 10%">{{ __('messages.action') }}</th>
                            </tr>
                        </thead>
                        <tbody id="productsBody">
                            <!-- Products will be added here dynamically -->
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="4" class="text-end"><strong>{{ __('messages.total') }}:</strong></td>
                                <td>
                                    <strong class="total-amount">0.00</strong>
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @error('products')
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Notes Section -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">{{ __('messages.additional_notes') }}</h5>
            </div>
            <div class="card-body">
                <div class="mb-0">
                    <label class="form-label">{{ __('messages.notes') }}</label>
                    <textarea name="note" class="form-control" rows="3" placeholder="{{ __('messages.enter_notes') }}"></textarea>
                </div>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="d-flex gap-2 mb-4">
            <button type="submit" class="btn btn-success" id="submitBtn">
                <i class="fas fa-check"></i> {{ __('messages.add_products') }}
            </button>
            <a href="{{ route('sellerProductRequests.index') }}" class="btn btn-secondary">
                {{ __('messages.cancel') }}
            </a>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('directAddForm');
    const productsBody = document.getElementById('productsBody');
    const addProductBtn = document.getElementById('addProductBtn');
    const submitBtn = document.getElementById('submitBtn');
    let productIndex = 0;

    const products = @json($products);

    function createProductRow(index) {
        const row = document.createElement('tr');
        row.className = 'product-row';
        row.dataset.rowIndex = index;
        row.innerHTML = `
            <td>
                <select name="products[${index}][id]" class="form-control product-select" required>
                    <option value="">{{ __('messages.select_product') }}</option>
                    ${products.map(p => `
                        <option value="${p.id}" data-price="${p.selling_price}" data-tax="${p.tax || 0}">
                            ${p.name_ar || p.name_en} (SKU: ${p.sku || 'N/A'})
                        </option>
                    `).join('')}
                </select>
            </td>
            <td>
                <input type="number" name="products[${index}][quantity]" class="form-control quantity-input" min="1" value="1" required>
            </td>
            <td>
                <input type="number" name="products[${index}][price_with_tax]" class="form-control price-input" step="0.01" min="0" placeholder="0.00" required>
            </td>
            <td>
                <input type="number" name="products[${index}][tax_percentage]" class="form-control tax-input" step="0.01" min="0" max="100" placeholder="0">
            </td>
            <td>
                <span class="subtotal">0.00</span>
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-danger remove-product-btn">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;

        // Add event listeners to product select
        const productSelect = row.querySelector('.product-select');
        productSelect.addEventListener('change', function() {
            const selectedProduct = products.find(p => p.id == this.value);
            if (selectedProduct) {
                row.querySelector('.price-input').value = selectedProduct.selling_price;
                row.querySelector('.tax-input').value = selectedProduct.tax || 0;
            } else {
                row.querySelector('.price-input').value = '';
                row.querySelector('.tax-input').value = '';
            }
            updateRowSubtotal(row);
        });

        // Add event listeners to quantity and price inputs
        row.querySelector('.quantity-input').addEventListener('change', () => updateRowSubtotal(row));
        row.querySelector('.price-input').addEventListener('input', () => updateRowSubtotal(row));
        row.querySelector('.tax-input').addEventListener('input', () => updateRowSubtotal(row));

        // Add event listener to remove button
        row.querySelector('.remove-product-btn').addEventListener('click', function() {
            row.remove();
            validateProductsCount();
            updateTotalAmount();
        });

        return row;
    }

    function updateRowSubtotal(row) {
        const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
        const priceWithTax = parseFloat(row.querySelector('.price-input').value) || 0;
        const subtotal = quantity * priceWithTax;
        row.querySelector('.subtotal').textContent = subtotal.toFixed(2);
        updateTotalAmount();
    }

    function updateTotalAmount() {
        let total = 0;
        document.querySelectorAll('.product-row').forEach(row => {
            const subtotalText = row.querySelector('.subtotal').textContent;
            total += parseFloat(subtotalText) || 0;
        });
        document.querySelector('.total-amount').textContent = total.toFixed(2);
    }

    function validateProductsCount() {
        const productsCount = document.querySelectorAll('.product-row').length;
        addProductBtn.disabled = false;
        submitBtn.disabled = productsCount === 0;
    }

    addProductBtn.addEventListener('click', function() {
        const row = createProductRow(productIndex++);
        productsBody.appendChild(row);
        validateProductsCount();
    });

    form.addEventListener('submit', function(e) {
        const productsCount = document.querySelectorAll('.product-row').length;
        if (productsCount === 0) {
            e.preventDefault();
            alert('{{ __("messages.please_add_at_least_one_product") }}');
            return false;
        }
    });

    validateProductsCount();
});
</script>
@endsection
