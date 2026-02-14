@extends('layouts.admin')

@section('title', __('messages.register_seller_sale'))
@section('page-title', __('messages.register_seller_sale'))

@section('content')
<div class="page-header">
    <h1 class="page-title">{{ __('messages.register_seller_sale') }}</h1>
    <p class="page-subtitle">{{ __('messages.register_new_sale_for_seller') ?? 'Register a new sale for a seller' }}</p>
</div>

<form action="{{ route('admin.seller-sales.store') }}" method="POST" id="salForm">
    @csrf

    <div class="row">
        <!-- Left Column - Sale Info -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('messages.sale_information') }}</h5>
                </div>
                <div class="card-body">
                    <!-- Seller Selection (NEW) -->
                    <div class="mb-3">
                        <label class="form-label" for="seller_id">
                            <span class="text-danger">*</span> {{ __('messages.select_seller') }}
                        </label>
                        <select name="seller_id" id="seller_id" class="form-control @error('seller_id') is-invalid @enderror" required>
                            <option value="">-- {{ __('messages.select_seller') }} --</option>
                            @foreach($sellers as $seller)
                                <option value="{{ $seller->id }}" data-commission="{{ $seller->commission_percentage ?? 0 }}" {{ old('seller_id') == $seller->id ? 'selected' : '' }}>
                                    {{ $seller->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('seller_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Sale Date -->
                    <div class="mb-3">
                        <label class="form-label" for="sale_date">
                            <span class="text-danger">*</span> {{ __('messages.sale_date') }}
                        </label>
                        <input type="date" name="sale_date" id="sale_date" class="form-control @error('sale_date') is-invalid @enderror"
                               value="{{ old('sale_date', now()->format('Y-m-d')) }}" required>
                        @error('sale_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Notes -->
                    <div class="mb-3">
                        <label class="form-label" for="notes">{{ __('messages.notes') }}</label>
                        <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="2">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Products Section -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('messages.products') }}</h5>
                </div>
                <div class="card-body">
                    @if($errors->has('products'))
                        <div class="alert alert-danger mb-3">
                            {{ $errors->first('products') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table" id="productsTable">
                            <thead>
                                <tr>
                                    <th style="width: 28%;">{{ __('messages.product') }}</th>
                                    <th style="width: 10%;">{{ __('messages.available_quantity') }}</th>
                                    <th style="width: 10%;">{{ __('messages.quantity') }}</th>
                                    <th style="width: 14%;">{{ __('messages.price_with_tax') ?? 'السعر (شامل الضريبة)' }}</th>
                                    <th style="width: 12%;">{{ __('messages.tax_percentage') }}</th>
                                    <th style="width: 14%;">{{ __('messages.total_after_tax') }}</th>
                                    <th style="width: 12%;">{{ __('messages.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody id="productsBody">
                                <!-- Rows will be added by JavaScript -->
                            </tbody>
                        </table>
                    </div>

                    <button type="button" class="btn btn-success mt-3" id="addProductBtn">
                        <i class="fas fa-plus me-2"></i>{{ __('messages.add_product_row') ?? 'Add Product' }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Right Column - Summary -->
        <div class="col-md-4">
            <div class="card sticky-top" style="top: 20px;">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">{{ __('messages.summary') }}</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ __('messages.total_items') }}:</span>
                            <strong id="summaryTotalItems">0</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ __('messages.subtotal') }}:</span>
                            <strong id="summarySubtotal">0.00</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ __('messages.total_tax') }}:</span>
                            <strong id="summaryTax" class="text-warning">0.00</strong>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="h5">{{ __('messages.total_amount') }}:</span>
                            <strong class="h5 text-primary" id="summaryTotal">0.00</strong>
                        </div>

                        <!-- Commission Info -->
                        <hr>
                        <div class="alert alert-info mb-0" id="commissionInfo" style="display: none;">
                            <h6 class="mb-2">{{ __('messages.commission') }}</h6>
                            <div class="d-flex justify-content-between mb-2">
                                <span id="commissionLabel">{{ __('messages.seller_commission') }}:</span>
                                <strong id="commissionAmount">0.00</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>{{ __('messages.amount_due_to_waraqa') }}:</span>
                                <strong id="dueToWaraqa">0.00</strong>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save me-2"></i>{{ __('messages.record_sale') ?? 'Record Sale' }}
                        </button>
                        <a href="{{ route('admin.seller-sales.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>{{ __('messages.back_to_list') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

@endsection

@push('scripts')
<script>
const productsData = @json($sellers->map(fn($s) => ['id' => $s->id, 'name' => $s->name])->keyBy('id'));

let productRowCount = 0;
let sellerProducts = {};

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    const sellerSelect = document.getElementById('seller_id');

    if(sellerSelect.value) {
        loadSellerProducts(sellerSelect.value);
    }

    sellerSelect.addEventListener('change', function() {
        clearProductRows();
        if(this.value) {
            loadSellerProducts(this.value);
        }
        setTimeout(function() {
            calculateSummary();
        }, 100);
    });

    if(document.getElementById('productsBody').children.length === 0) {
        addProductRow();
    }

    document.getElementById('addProductBtn').addEventListener('click', addProductRow);
    calculateSummary();
});

// Load seller products via AJAX
function loadSellerProducts(sellerId) {
    const url = new URL('{{ route("admin.seller-sales.get-products", ":id") }}'.replace(':id', sellerId), window.location.origin);

    fetch(url)
        .then(response => response.json())
        .then(data => {
            sellerProducts = {};
            data.forEach(product => {
                sellerProducts[product.id] = {
                    name: product.name_ar || product.name_en,
                    code: product.code,
                    availableQuantity: product.available_quantity
                };
            });
            updateProductSelects();
        })
        .catch(error => console.error('Error loading products:', error));
}

// Update product select dropdowns
function updateProductSelects() {
    document.querySelectorAll('.product-select').forEach(select => {
        const currentValue = select.value;
        select.innerHTML = '';

        const option = document.createElement('option');
        option.value = '';
        option.textContent = "{{ __('messages.select_product') }}";
        select.appendChild(option);

        Object.entries(sellerProducts).forEach(([id, product]) => {
            const opt = document.createElement('option');
            opt.value = id;
            opt.textContent = product.name;
            opt.setAttribute('data-available', product.availableQuantity);
            if(id == currentValue) opt.selected = true;
            select.appendChild(opt);
        });
    });
}

// Add product row
function addProductRow() {
    const tbody = document.getElementById('productsBody');
    const rowId = productRowCount++;

    const row = document.createElement('tr');
    row.id = 'product-row-' + rowId;

    // Product select
    const productCell = document.createElement('td');
    const productSelect = document.createElement('select');
    productSelect.setAttribute('name', 'products[' + rowId + '][product_id]');
    productSelect.className = 'form-control product-select';
    productSelect.setAttribute('data-row', rowId);
    productSelect.required = true;
    productSelect.addEventListener('change', function() { updateRowDisplay(rowId); });
    productCell.appendChild(productSelect);

    // Available qty
    const qtyCell = document.createElement('td');
    const qtyBadge = document.createElement('span');
    qtyBadge.className = 'badge bg-info available-qty';
    qtyBadge.setAttribute('data-row', rowId);
    qtyBadge.textContent = '0';
    qtyCell.appendChild(qtyBadge);

    // Quantity
    const quantityCell = document.createElement('td');
    const quantityInput = document.createElement('input');
    quantityInput.setAttribute('type', 'number');
    quantityInput.setAttribute('name', 'products[' + rowId + '][quantity]');
    quantityInput.className = 'form-control quantity-input';
    quantityInput.setAttribute('data-row', rowId);
    quantityInput.setAttribute('min', '1');
    quantityInput.required = true;
    quantityInput.value = '1';
    quantityInput.addEventListener('input', function() { calculateRow(rowId); });
    quantityInput.addEventListener('change', function() { calculateRow(rowId); });
    quantityCell.appendChild(quantityInput);

    // Unit Price
    const priceCell = document.createElement('td');
    const priceInput = document.createElement('input');
    priceInput.setAttribute('type', 'number');
    priceInput.setAttribute('name', 'products[' + rowId + '][unit_price]');
    priceInput.className = 'form-control form-control-lg unit-price-input';
    priceInput.setAttribute('data-row', rowId);
    priceInput.setAttribute('step', '0.01');
    priceInput.setAttribute('min', '0');
    priceInput.setAttribute('placeholder', '0.00');
    priceInput.required = true;
    priceInput.value = '0.00';
    priceInput.style.fontSize = '1rem';
    priceInput.addEventListener('input', function() { calculateRow(rowId); });
    priceInput.addEventListener('change', function() { calculateRow(rowId); });
    priceCell.appendChild(priceInput);

    // Tax %
    const taxCell = document.createElement('td');
    const taxInput = document.createElement('input');
    taxInput.setAttribute('type', 'number');
    taxInput.setAttribute('name', 'products[' + rowId + '][tax_percentage]');
    taxInput.className = 'form-control form-control-lg tax-percentage-input';
    taxInput.setAttribute('data-row', rowId);
    taxInput.setAttribute('step', '0.01');
    taxInput.setAttribute('min', '0');
    taxInput.setAttribute('max', '100');
    taxInput.setAttribute('placeholder', '0');
    taxInput.value = '0';
    taxInput.style.fontSize = '1rem';
    taxInput.addEventListener('input', function() { calculateRow(rowId); });
    taxInput.addEventListener('change', function() { calculateRow(rowId); });
    taxCell.appendChild(taxInput);

    // Total
    const totalCell = document.createElement('td');
    const totalInput = document.createElement('input');
    totalInput.setAttribute('type', 'text');
    totalInput.className = 'form-control row-total';
    totalInput.setAttribute('data-row', rowId);
    totalInput.readOnly = true;
    totalInput.value = '0.00';
    totalCell.appendChild(totalInput);

    // Actions
    const actionCell = document.createElement('td');
    const removeBtn = document.createElement('button');
    removeBtn.setAttribute('type', 'button');
    removeBtn.className = 'btn btn-sm btn-danger';
    removeBtn.innerHTML = '<i class="fas fa-trash"></i>';
    removeBtn.addEventListener('click', function() { removeProductRow(rowId); });
    actionCell.appendChild(removeBtn);

    row.appendChild(productCell);
    row.appendChild(qtyCell);
    row.appendChild(quantityCell);
    row.appendChild(priceCell);
    row.appendChild(taxCell);
    row.appendChild(totalCell);
    row.appendChild(actionCell);

    tbody.appendChild(row);
    updateProductSelects();
}

// Update row display
function updateRowDisplay(rowId) {
    const select = document.querySelector('[data-row="' + rowId + '"].product-select');
    const option = select.selectedOptions[0];
    const availableQty = option.getAttribute('data-available') || '0';

    document.querySelector('[data-row="' + rowId + '"].available-qty').textContent = availableQty;
    calculateRow(rowId);
}

// Calculate row total
function calculateRow(rowId) {
    const quantity = parseFloat(document.querySelector('[data-row="' + rowId + '"].quantity-input').value) || 0;
    const unitPrice = parseFloat(document.querySelector('[data-row="' + rowId + '"].unit-price-input').value) || 0;
    const taxPercentage = parseFloat(document.querySelector('[data-row="' + rowId + '"].tax-percentage-input').value) || 0;

    const totalAfterTax = (quantity * unitPrice).toFixed(2);

    document.querySelector('[data-row="' + rowId + '"].row-total').value = totalAfterTax;

    calculateSummary();
}

// Calculate summary
function calculateSummary() {
    let totalItems = 0;
    let totalBeforeTax = 0;
    let totalTax = 0;

    document.querySelectorAll('#productsBody tr').forEach(row => {
        const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
        const unitPrice = parseFloat(row.querySelector('.unit-price-input').value) || 0;
        const taxPercentage = parseFloat(row.querySelector('.tax-percentage-input').value) || 0;

        const totalAfterTax = quantity * unitPrice;

        if(taxPercentage > 0) {
            const beforeTax = totalAfterTax / (1 + (taxPercentage / 100));
            const tax = totalAfterTax - beforeTax;
            totalBeforeTax += beforeTax;
            totalTax += tax;
        } else {
            totalBeforeTax += totalAfterTax;
        }

        totalItems += quantity;
    });

    const grandTotal = (totalBeforeTax + totalTax).toFixed(2);

    document.getElementById('summaryTotalItems').textContent = totalItems;
    document.getElementById('summarySubtotal').textContent = totalBeforeTax.toFixed(2);
    document.getElementById('summaryTax').textContent = totalTax.toFixed(2);
    document.getElementById('summaryTotal').textContent = grandTotal;

    // Calculate and display commission if seller is selected
    const sellerSelect = document.getElementById('seller_id');
    if(sellerSelect.value && sellerSelect.selectedOptions.length > 0) {
        // Get seller commission percentage from data attribute
        const option = sellerSelect.selectedOptions[0];
        const commissionPercentage = parseFloat(option.getAttribute('data-commission')) || 0;

        const commissionAmount = (grandTotal * (commissionPercentage / 100)).toFixed(2);
        const dueToWaraqa = (grandTotal - commissionAmount).toFixed(2);

        document.getElementById('commissionInfo').style.display = 'block';
        document.getElementById('commissionLabel').textContent = "{{ __('messages.seller_commission') }} (" + commissionPercentage + "%):";
        document.getElementById('commissionAmount').textContent = commissionAmount;
        document.getElementById('dueToWaraqa').textContent = dueToWaraqa;
    } else {
        document.getElementById('commissionInfo').style.display = 'none';
    }
}

// Remove product row
function removeProductRow(rowId) {
    const row = document.getElementById('product-row-' + rowId);
    if(row) {
        row.remove();
        calculateSummary();
    }
}

// Clear all product rows
function clearProductRows() {
    document.getElementById('productsBody').innerHTML = '';
    productRowCount = 0;
    addProductRow();
    calculateSummary();
}

// Form validation on submit
document.getElementById('salForm').addEventListener('submit', function(e) {
    const sellerId = document.getElementById('seller_id').value;
    const productsBody = document.getElementById('productsBody');

    if(!sellerId) {
        e.preventDefault();
        alert("{{ __('messages.select_seller') }}");
        return false;
    }

    if(productsBody.children.length === 0) {
        e.preventDefault();
        alert("{{ __('messages.please_add_at_least_one_product') ?? 'Please add at least one product' }}");
        return false;
    }

    let hasInvalidRows = false;
    document.querySelectorAll('#productsBody tr').forEach(row => {
        const productId = row.querySelector('.product-select').value;
        const quantity = row.querySelector('.quantity-input').value;
        const unitPrice = row.querySelector('.unit-price-input').value;

        if(!productId || !quantity || !unitPrice) {
            hasInvalidRows = true;
        }
    });

    if(hasInvalidRows) {
        e.preventDefault();
        alert("{{ __('messages.please_fill_all_required_fields') ?? 'Please fill all required fields' }}");
        return false;
    }
});
</script>

<style>
.sticky-top {
    position: sticky;
    top: 20px;
    z-index: 100;
}

@media (max-width: 768px) {
    .sticky-top {
        position: relative;
        top: auto;
    }
}
</style>
@endpush
