@extends('layouts.user')

@section('title', __('messages.record_sale'))
@section('page-title', __('messages.record_sale'))

@section('content')
<div class="page-header">
    <h1 class="page-title">{{ __('messages.record_new_sale') }}</h1>
    <p class="page-subtitle">{{ __('messages.record_products_sold_from_warehouse') }}</p>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-cash-register me-2"></i>{{ __('messages.sale_information') }}
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('user.sales.store') }}" method="POST" id="salesForm">
                    @csrf
                    
                    <!-- Sale Date -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="sale_date" class="form-label">{{ __('messages.sale_date') }} <span class="text-danger">*</span></label>
                            <input type="date"
                                   class="form-control @error('sale_date') is-invalid @enderror"
                                   id="sale_date"
                                   name="sale_date"
                                   value="{{ old('sale_date', date('Y-m-d')) }}"
                                   required>
                            @error('sale_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    
                    <!-- Products Section -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6>{{ __('messages.products_sold') }}</h6>
                            <button type="button" class="btn btn-success btn-sm" id="addProductBtn">
                                <i class="fas fa-plus me-1"></i>{{ __('messages.add_product') }}
                            </button>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm" id="productsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('messages.product') }}</th>
                                        <th width="80">{{ __('messages.available') }}</th>
                                        <th width="80">{{ __('messages.quantity') }}</th>
                                        <th width="100">{{ __('messages.unit_price') }} <small>({{ __('messages.tax_inclusive') }})</small></th>
                                        <th width="80">{{ __('messages.tax') }} %</th>
                                        <th width="100">{{ __('messages.total') }}</th>
                                        <th width="60">{{ __('messages.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody id="productsTableBody">
                                    <!-- Dynamic rows will be added here -->
                                </tbody>
                            </table>
                        </div>
                        
                        @error('products')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Notes -->
                    <div class="mb-4">
                        <label for="notes" class="form-label">{{ __('messages.notes') }}</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror"
                                  id="notes"
                                  name="notes"
                                  rows="3"
                                  placeholder="{{ __('messages.sale_notes_placeholder') }}">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Submit Buttons -->
                    <div class="text-end">
                        <a href="{{ route('user.sales.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i>{{ __('messages.cancel') }}
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>{{ __('messages.record_sale') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Available Products Sidebar -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-box me-2"></i>{{ __('messages.available_products') }}
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <input type="text" id="productSearch" class="form-control" placeholder="{{ __('messages.search_products') }}">
                </div>

                <div class="available-products-list" style="max-height: 400px; overflow-y: auto;">
                    @if($availableProducts->count() > 0)
                        @foreach($availableProducts->take(5) as $product)
                            <div class="product-item border rounded p-3 mb-2 cursor-pointer"
                                 data-product-id="{{ $product->id }}"
                                 data-product-name="{{ strtolower($product->name_ar . ' ' . $product->name_en) }}"
                                 data-available="{{ $product->available_quantity }}"
                                 data-price="{{ $product->selling_price }}"
                                 data-tax="{{ $product->tax ?? 0 }}">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">{{ $product->name_ar }}</h6>
                                        @if($product->name_en)
                                            <small class="text-muted">{{ $product->name_en }}</small>
                                        @endif
                                        <div class="mt-1">
                                            <span class="badge bg-info">{{ $product->available_quantity }} {{ __('messages.available') }}</span>
                                            <span class="badge bg-success"><x-riyal-icon style="width: 12px; height: 12px;" /> {{ number_format($product->selling_price, 2) }}</span>
                                        </div>
                                        @if($product->category)
                                            <small class="text-muted">{{ $product->category->name_ar }}</small>
                                        @endif
                                    </div>
                                    <button type="button" class="btn btn-outline-primary btn-sm add-product-btn">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach

                        @if($availableProducts->count() > 5)
                            <div class="d-grid">
                                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#allProductsModal">
                                    <i class="fas fa-eye me-1"></i>{{ __('messages.view_all') ?? 'عرض المزيد' }}
                                </button>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-box-open text-muted" style="font-size: 2rem;"></i>
                            <h6 class="mt-2 text-muted">{{ __('messages.no_products_available') }}</h6>
                            <p class="text-muted small">{{ __('messages.order_products_first') }}</p>
                            <a href="{{ route('user.orders') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-shopping-cart me-1"></i>{{ __('messages.view_orders') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Sale Summary -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-calculator me-2"></i>{{ __('messages.sale_summary') }}
                </h5>
            </div>
            <div class="card-body">
                <div class="summary-item d-flex justify-content-between mb-2">
                    <span>{{ __('messages.total_items') }}:</span>
                    <span id="totalItems">0</span>
                </div>
                <div class="summary-item d-flex justify-content-between mb-2">
                    <span>{{ __('messages.total_before_tax') ?? 'الإجمالي قبل الضريبة' }}:</span>
                    <span><x-riyal-icon /> <span id="totalBeforeTax">0.00</span></span>
                </div>
                <div class="summary-item d-flex justify-content-between mb-2">
                    <span>{{ __('messages.total_tax') ?? 'إجمالي الضريبة' }}:</span>
                    <span><x-riyal-icon /> <span id="totalTax">0.00</span></span>
                </div>
                <div class="summary-item d-flex justify-content-between mb-2 border-top pt-2">
                    <strong>{{ __('messages.total_after_tax') ?? 'الإجمالي شامل الضريبة' }}:</strong>
                    <strong><x-riyal-icon /> <span id="totalAfterTax">0.00</span></strong>
                </div>
                <hr>
                <div class="summary-item d-flex justify-content-between">
                    <strong>{{ __('messages.products_count') }}:</strong>
                    <strong id="productsCount">0</strong>
                </div>
            </div>
        </div>

        <!-- My Commission -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-percent me-2"></i>{{ __('messages.my_commission') }}
                </h5>
            </div>
            <div class="card-body">
                <div class="summary-item d-flex justify-content-between mb-2">
                    <span>{{ __('messages.distribution_point_commission') }} ({{ $user->commission_percentage ?? 0 }}%):</span>
                    <span><x-riyal-icon /> <span id="commissionAmount">0.00</span></span>
                </div>
                <div class="summary-item d-flex justify-content-between border-top pt-2">
                    <strong>{{ __('messages.amount_due_to_waraqa') }}:</strong>
                    <strong><x-riyal-icon /> <span id="remainingAmount">0.00</span></strong>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- All Products Modal -->
<div class="modal fade" id="allProductsModal" tabindex="-1" aria-labelledby="allProductsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="allProductsModalLabel">{{ __('messages.available_products') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <input type="text" id="modalProductSearch" class="form-control" placeholder="{{ __('messages.search_products') }}">
                </div>
                <div class="row" id="allProductsContainer">
                    <!-- Products will be loaded here by JavaScript -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Product Row Template -->
<template id="productRowTemplate">
    <tr class="product-row">
        <td>
            <select name="products[INDEX][product_id]" class="form-select product-select" required>
                <option value="">{{ __('messages.select_product') }}</option>
                @foreach($availableProducts as $product)
                    <option value="{{ $product->id }}"
                            data-available="{{ $product->available_quantity }}"
                            data-price="{{ $product->selling_price }}"
                            data-tax="{{ $product->tax ?? 0 }}">
                        {{ $product->name_ar }} ({{ $product->available_quantity }} {{ __('messages.available') }})
                    </option>
                @endforeach
            </select>
        </td>
        <td class="text-center">
            <span class="available-quantity">-</span>
        </td>
        <td>
            <input type="number"
                   name="products[INDEX][quantity]"
                   class="form-control form-control-sm quantity-input"
                   min="1"
                   max="0"
                   required>
        </td>
        <td>
            <input type="number"
                   name="products[INDEX][unit_price]"
                   class="form-control form-control-sm unit-price-input"
                   step="0.01"
                   min="0"
                   required>
        </td>
        <td>
            <input type="number"
                   name="products[INDEX][tax_percentage]"
                   class="form-control form-control-sm tax-input"
                   step="0.01"
                   min="0"
                   max="100"
                   value="0"
                   placeholder="0">
        </td>
        <td class="text-end">
            <span class="row-total">0.00</span>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-danger btn-sm remove-product-btn">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    </tr>
</template>
@endsection

@push('scripts')
<script>
let productIndex = 0;

// Helper functions (will be populated after DOMContentLoaded)
let addProductRow, setupRowEvents, updateAvailableOptions, updateProductInfo, updateSummary;

// Make this function global (outside DOMContentLoaded)
function addProductFromModal(productId, productName, availableQty, price, tax) {
    // Check if product already added
    const existingSelects = document.querySelectorAll('.product-select');
    for (let select of existingSelects) {
        if (select.value == productId) {
            alert('{{ __("messages.product_already_added") }}');
            return;
        }
    }

    addProductToSale(productId, productName, availableQty, price, tax);

    // Close modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('allProductsModal'));
    if (modal) {
        modal.hide();
    }
}

// Add product to sale from sidebar
function addProductToSale(productId, productName, availableQty, price, tax) {
    // Check if product already added
    const existingSelects = document.querySelectorAll('.product-select');
    for (let select of existingSelects) {
        if (select.value == productId) {
            alert('{{ __("messages.product_already_added") }}');
            return;
        }
    }

    addProductRow();

    // Set the values in the new row
    const rows = document.querySelectorAll('.product-row');
    const lastRow = rows[rows.length - 1];

    const select = lastRow.querySelector('.product-select');
    const quantityInput = lastRow.querySelector('.quantity-input');
    const unitPriceInput = lastRow.querySelector('.unit-price-input');
    const taxInput = lastRow.querySelector('.tax-input');
    const availableSpan = lastRow.querySelector('.available-quantity');

    select.value = productId;
    quantityInput.max = availableQty;
    quantityInput.value = 1;
    unitPriceInput.value = price;
    taxInput.value = tax || 0;
    availableSpan.textContent = availableQty;

    updateSummary();
    updateAvailableOptions();
}

document.addEventListener('DOMContentLoaded', function() {

    // Add styles
    const style = document.createElement('style');
    style.textContent = `
        .cursor-pointer { cursor: pointer; }
        .product-item:hover { background-color: #f8f9fa; }
    `;
    document.head.appendChild(style);

    // Add product row function
    addProductRow = function() {
        const template = document.getElementById('productRowTemplate');
        const clone = template.content.cloneNode(true);

        // Replace INDEX placeholder with actual index
        const htmlString = clone.querySelector('tr').outerHTML.replace(/INDEX/g, productIndex);

        const tbody = document.getElementById('productsTableBody');
        tbody.insertAdjacentHTML('beforeend', htmlString);

        productIndex++;

        // Add event listeners to the new row
        const newRow = tbody.lastElementChild;
        setupRowEvents(newRow);

        updateSummary();
        updateAvailableOptions();
    };

    // Setup event listeners for a row
    setupRowEvents = function(row) {
        const productSelect = row.querySelector('.product-select');
        const quantityInput = row.querySelector('.quantity-input');
        const unitPriceInput = row.querySelector('.unit-price-input');
        const taxInput = row.querySelector('.tax-input');
        const removeBtn = row.querySelector('.remove-product-btn');

        productSelect.addEventListener('change', function() {
            updateProductInfo(this);
            updateAvailableOptions();
        });

        quantityInput.addEventListener('input', updateSummary);
        unitPriceInput.addEventListener('input', updateSummary);
        taxInput.addEventListener('input', updateSummary);

        removeBtn.addEventListener('click', function() {
            row.remove();
            updateSummary();
            updateAvailableOptions();
        });
    };

    // Update available options in all selects (hide already selected products)
    updateAvailableOptions = function() {
        const rows = document.querySelectorAll('.product-row');
        const selectedProductIds = new Set();

        // Collect all selected product IDs
        rows.forEach(row => {
            const select = row.querySelector('.product-select');
            if (select.value) {
                selectedProductIds.add(select.value);
            }
        });

        // Update each select
        rows.forEach(row => {
            const select = row.querySelector('.product-select');
            const options = select.querySelectorAll('option');

            options.forEach(option => {
                if (option.value === '') {
                    // Always show the empty option
                    option.style.display = '';
                } else if (selectedProductIds.has(option.value)) {
                    // Hide if selected in another row AND not the current row's value
                    if (select.value !== option.value) {
                        option.style.display = 'none';
                    } else {
                        option.style.display = '';
                    }
                } else {
                    // Show if not selected
                    option.style.display = '';
                }
            });
        });
    };

    // Update product info when selection changes
    updateProductInfo = function(select) {
        const row = select.closest('tr');
        const option = select.selectedOptions[0];
        const availableSpan = row.querySelector('.available-quantity');
        const quantityInput = row.querySelector('.quantity-input');
        const unitPriceInput = row.querySelector('.unit-price-input');
        const taxInput = row.querySelector('.tax-input');

        if (option && option.value) {
            const available = option.dataset.available;
            const price = option.dataset.price;
            const tax = option.dataset.tax || 0;

            availableSpan.textContent = available;
            quantityInput.max = available;
            quantityInput.value = Math.min(1, available);
            unitPriceInput.value = price;
            taxInput.value = tax;
        } else {
            availableSpan.textContent = '-';
            quantityInput.max = 0;
            quantityInput.value = '';
            unitPriceInput.value = '';
            taxInput.value = '';
        }

        updateSummary();
    };

    // Update summary
    updateSummary = function() {
        let totalItems = 0;
        let totalBeforeTax = 0;
        let totalTaxAmount = 0;
        let totalAfterTax = 0;
        let productsCount = 0;

        const rows = document.querySelectorAll('.product-row');

        rows.forEach(row => {
            const quantityInput = row.querySelector('.quantity-input');
            const unitPriceInput = row.querySelector('.unit-price-input');
            const taxInput = row.querySelector('.tax-input');
            const productSelect = row.querySelector('.product-select');
            const rowTotalSpan = row.querySelector('.row-total');

            if (productSelect.value && quantityInput.value && unitPriceInput.value) {
                const quantity = parseInt(quantityInput.value) || 0;
                const unitPrice = parseFloat(unitPriceInput.value) || 0; // Price is inclusive of tax
                const taxPercentage = parseFloat(taxInput.value) || 0;

                // Total price (inclusive of tax) = quantity * unitPrice
                const total = quantity * unitPrice;

                // Calculate price before tax and tax amount
                let priceBeforeTax = total;
                let taxAmount = 0;

                if (taxPercentage > 0) {
                    // priceBeforeTax = total / (1 + taxPercentage/100)
                    priceBeforeTax = total / (1 + (taxPercentage / 100));
                    // taxAmount = total - priceBeforeTax
                    taxAmount = total - priceBeforeTax;
                }

                rowTotalSpan.textContent = total.toFixed(2);

                totalItems += quantity;
                totalBeforeTax += priceBeforeTax;
                totalTaxAmount += taxAmount;
                totalAfterTax += total;
                productsCount++;
            } else {
                rowTotalSpan.textContent = '0.00';
            }
        });

        document.getElementById('totalItems').textContent = totalItems;
        document.getElementById('totalBeforeTax').textContent = totalBeforeTax.toFixed(2);
        document.getElementById('totalTax').textContent = totalTaxAmount.toFixed(2);
        document.getElementById('totalAfterTax').textContent = totalAfterTax.toFixed(2);
        document.getElementById('productsCount').textContent = productsCount;

        // Calculate commission and remaining amount
        const commissionPercentage = {{ $user->commission_percentage ?? 0 }};
        const commissionAmount = totalAfterTax * (commissionPercentage / 100);
        const remainingAmount = totalAfterTax - commissionAmount;

        document.getElementById('commissionAmount').textContent = commissionAmount.toFixed(2);
        document.getElementById('remainingAmount').textContent = remainingAmount.toFixed(2);
    };

    // Event listeners
    document.getElementById('addProductBtn').addEventListener('click', addProductRow);

    // Product search functionality
    document.getElementById('productSearch').addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const productItems = document.querySelectorAll('.product-item');
        
        productItems.forEach(item => {
            const productName = item.dataset.productName;
            if (productName.includes(searchTerm)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    });

    // Product item click handlers
    document.querySelectorAll('.product-item').forEach(item => {
        item.addEventListener('click', function() {
            const productId = this.dataset.productId;
            const productName = this.querySelector('h6').textContent;
            const availableQty = this.dataset.available;
            const price = this.dataset.price;
            const tax = this.dataset.tax || 0;

            addProductToSale(productId, productName, availableQty, price, tax);
        });
    });

    // Form validation
    document.getElementById('salesForm').addEventListener('submit', function(e) {
        const rows = document.querySelectorAll('.product-row');
        let hasValidProducts = false;

        rows.forEach(row => {
            const productSelect = row.querySelector('.product-select');
            const quantityInput = row.querySelector('.quantity-input');
            const unitPriceInput = row.querySelector('.unit-price-input');

            if (productSelect.value && quantityInput.value && unitPriceInput.value &&
                parseInt(quantityInput.value) > 0 && parseFloat(unitPriceInput.value) > 0) {
                hasValidProducts = true;
            }
        });

        if (!hasValidProducts) {
            e.preventDefault();
            alert('{{ __("messages.please_add_at_least_one_product") }}');
        }
    });

    // Add one initial row
    addProductRow();

    // Load all products in modal
    function loadAllProducts(products = []) {
        const container = document.getElementById('allProductsContainer');

        if (products.length === 0) {
            container.innerHTML = '<div class="col-12 text-center py-4"><p class="text-muted">{{ __("messages.no_products_available") }}</p></div>';
            return;
        }

        let html = '';
        products.forEach(product => {
            html += `
                <div class="col-md-6 mb-3 product-card-item" data-product-name="${(product.name_ar + ' ' + (product.name_en || '')).toLowerCase()}">
                    <div class="card h-100 cursor-pointer" onclick="addProductFromModal(${product.id}, '${product.name_ar}', ${product.available}, ${product.price}, ${product.tax || 0})">
                        <div class="card-body">
                            <h6 class="card-title">${product.name_ar}</h6>
                            ${product.name_en ? `<small class="text-muted d-block mb-2">${product.name_en}</small>` : ''}
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="badge bg-info me-1">${product.available} {{ __('messages.available') }}</span>
                                    <span class="badge bg-success"><x-riyal-icon style="width: 12px; height: 12px;" /> ${product.price.toFixed(2)}</span>
                                </div>
                                <button class="btn btn-primary btn-sm" type="button">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });

        container.innerHTML = html;
    }

    // Search in modal
    document.getElementById('modalProductSearch')?.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const items = document.querySelectorAll('.product-card-item');

        items.forEach(item => {
            const productName = item.dataset.productName;
            if (productName.includes(searchTerm)) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    });

    // Load products when modal is shown
    document.getElementById('allProductsModal')?.addEventListener('show.bs.modal', function() {
        const products = [];
        @foreach($availableProducts as $product)
            products.push({
                id: {{ $product->id }},
                name_ar: '{{ $product->name_ar }}',
                name_en: '{{ $product->name_en ?? '' }}',
                available: {{ $product->available_quantity }},
                price: {{ $product->selling_price }},
                tax: {{ $product->tax ?? 0 }}
            });
        @endforeach
        loadAllProducts(products);
    });
});
</script>
@endpush