@extends('layouts.admin')

@section('title', __('messages.Create_Purchase'))

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>{{ __('messages.Create_Purchase') }}</h4>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>{{ __('messages.error') }}!</strong>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <form action="{{ route('purchases.store') }}" method="POST" id="purchaseForm">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <x-search-select
                                    model="App\Models\Provider"
                                    fieldName="provider_id"
                                    label="provider"
                                    placeholder="Search..."
                                    limit="10"
                                    required="false"
                                    value="{{ old('provider_id') }}"
                                />
                            </div>

                            <div class="col-md-6">
                                <x-search-select
                                    model="App\Models\Warehouse"
                                    fieldName="warehouse_id"
                                    label="warehouse"
                                    placeholder="Search..."
                                    limit="10"
                                    required="false"
                                    value="{{ old('warehouse_id') }}"
                                />
                            </div>
                        </div>

                        <!-- Products Section -->
                        <div class="mb-3">
                            <label class="form-label"><strong>{{ __('messages.Products') }} *</strong></label>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="productsTable">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>{{ __('messages.Product') }}</th>
                                            <th style="width: 100px;">{{ __('messages.Quantity') }}</th>
                                            <th style="width: 120px;">{{ __('messages.Tax') }}%</th>
                                            <th style="width: 120px;">{{ __('messages.Price_with_tax') }}</th>
                                            <th style="width: 50px;">{{ __('messages.Actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody id="productsContainer">
                                        <tr class="product-row">
                                            <td>
                                                <input type="hidden" name="products[0][id]" class="product-id" required />
                                                <input type="text" class="form-control product-search" placeholder="{{ __('messages.search_product') }}" autocomplete="off" required />
                                            </td>
                                            <td>
                                                <input type="number" name="products[0][quantity]" class="form-control quantity-input" min="1" value="1" required />
                                            </td>
                                            <td>
                                                <input type="number" name="products[0][tax_percentage]" class="form-control tax-input" min="0" max="100" value="0" step="0.01" required />
                                            </td>
                                            <td>
                                                <input type="number" name="products[0][price_with_tax]" class="form-control price-with-tax-input" min="0" step="0.01" value="0" required />
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-danger btn-sm remove-product" style="display: none;">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <button type="button" class="btn btn-success btn-sm" id="addProductBtn">
                                <i class="fas fa-plus"></i> {{ __('messages.add_product') }}
                            </button>
                            @error('products')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Summary Section -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="notes" class="form-label">{{ __('messages.notes') }}</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <div class="mb-4 pb-3 border-bottom">
                                            <h5 class="card-title">{{ __('messages.Purchase_Summary') }}</h5>
                                        </div>
                                        <div class="d-flex justify-content-between mb-3">
                                            <span>{{ __('messages.total_amount') }}:</span>
                                            <strong id="subtotal" class="h6">0.00</strong>
                                        </div>
                                        <div class="d-flex justify-content-between mb-4">
                                            <span>{{ __('messages.Total_Tax') }}:</span>
                                            <strong id="tax-total" class="h6">0.00</strong>
                                        </div>
                                        <hr class="my-3">
                                        <div class="d-flex justify-content-between pt-3">
                                            <span><strong>{{ __('messages.Grand_Total') }}:</strong></span>
                                            <strong id="grand-total" class="h4 text-primary">0.00</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> {{ __('messages.Save') }}
                            </button>
                            <a href="{{ route('purchases.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> {{ __('messages.Cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    let rowIdx = 1;

    // Submit form
    $('#purchaseForm').on('submit', function(e) {
        $('#productsContainer').find('.product-row').each(function() {
            const priceInput = $(this).find('.price-with-tax-input');
            const actualPrice = parseFloat(priceInput.data('actual-price')) || parseFloat(priceInput.val()) || 0;
            priceInput.val(actualPrice);
        });
    });

    // Initialize first row product search
    initializeProductSearch();

    // Add product row
    $('#addProductBtn').on('click', function() {
        const newRow = `
            <tr class="product-row">
                <td>
                    <input type="hidden" name="products[${rowIdx}][id]" class="product-id" required />
                    <input type="text" class="form-control product-search" placeholder="{{ __('messages.search_product') }}" autocomplete="off" required />
                </td>
                <td>
                    <input type="number" name="products[${rowIdx}][quantity]" class="form-control quantity-input" min="1" value="1" required />
                </td>
                <td>
                    <input type="number" name="products[${rowIdx}][tax_percentage]" class="form-control tax-input" min="0" max="100" value="0" step="0.01" required />
                </td>
                <td>
                    <input type="number" name="products[${rowIdx}][price_with_tax]" class="form-control price-with-tax-input" min="0" step="0.01" value="0" required />
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm remove-product">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        $('#productsContainer').append(newRow);
        initializeProductSearch();
        updateDeleteButtons();
        rowIdx++;
    });

    // Remove product row
    $(document).on('click', '.remove-product', function(e) {
        e.preventDefault();
        $(this).closest('.product-row').remove();
        updateDeleteButtons();
        calculateTotals();
    });

    // Calculate totals when values change
    $(document).on('change keyup', '.quantity-input, .price-with-tax-input, .tax-input', function() {
        calculateTotals();
    });

    function initializeProductSearch() {
        $('.product-search:not(.ui-autocomplete-input)').autocomplete({
            source: function(request, response) {
                // تحقق من وجود المورد المختار
                const providerId = $('[name="provider_id"]').val();
                if (!providerId) {
                    Swal.fire({
                        icon: 'warning',
                        title: '{{ __("messages.warning") }}',
                        text: '{{ __("messages.Please_select_provider_first") }}',
                        confirmButtonText: '{{ __("messages.Ok") }}'
                    });
                    response([]);
                    return;
                }

                // احصل على قائمة المنتجات المختارة بالفعل
                const selectedProductIds = [];
                $('#productsContainer').find('.product-id').each(function() {
                    const id = $(this).val();
                    if (id) {
                        selectedProductIds.push(id);
                    }
                });

                console.log('Selected Product IDs:', selectedProductIds);
                console.log('Search Term:', request.term);
                console.log('Exclude IDs String:', selectedProductIds.join(','));
                console.log('Provider ID:', providerId);

                $.ajax({
                    url: '{{ route("products.search") }}',
                    dataType: 'json',
                    cache: false,
                    data: {
                        term: request.term,
                        exclude_ids: selectedProductIds.join(','),
                        provider_id: providerId
                    },
                    success: function(data) {
                        if (data.length === 0) {
                            response([{ label: 'Not Found', value: '' }]);
                        } else {
                            response($.map(data, function(item) {
                                return {
                                    label: item.name,
                                    value: item.name,
                                    id: item.id,
                                    price: item.price_without_tax,
                                    tax: item.tax
                                };
                            }));
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', status, error);
                    }
                });
            },
            select: function(event, ui) {
                if (ui.item.value === '') {
                    event.preventDefault();
                } else {
                    const row = $(this).closest('.product-row');
                    const priceInput = row.find('.price-with-tax-input');

                    // حساب السعر الشامل الضريبة
                    const basePrice = parseFloat(ui.item.price || 0);
                    const taxPercentage = parseFloat(ui.item.tax || 0);
                    const priceWithTax = basePrice + (basePrice * taxPercentage / 100);

                    row.find('.product-id').val(ui.item.id);
                    row.find('.product-search').val(ui.item.label);
                    priceInput.val(priceWithTax.toFixed(2)).data('actual-price', priceWithTax);
                    row.find('.tax-input').val(ui.item.tax || 0);

                    // تحديث نتائج البحث في جميع الصفوف الأخرى
                    reinitializeAllSearches();

                    calculateTotals();
                    return false;
                }
            },
            minLength: 2
        });
    }

    function updateDeleteButtons() {
        const rows = $('#productsContainer').find('.product-row');
        rows.each(function() {
            $(this).find('.remove-product').toggle(rows.length > 1);
        });
    }

    function reinitializeAllSearches() {
        // إعادة تهيئة البحث في جميع الصفوف
        $('.product-search').autocomplete('destroy');
        initializeProductSearch();
    }

    function calculateTotals() {
        let grandTotal = 0;
        let totalTax = 0;

        $('#productsContainer').find('.product-row').each(function() {
            const quantity = parseFloat($(this).find('.quantity-input').val()) || 0;
            const priceInput = $(this).find('.price-with-tax-input');
            // استخدام القيمة الدقيقة من data attribute إن وجدت، وإلا استخدام قيمة الـ input
            const priceWithTax = parseFloat(priceInput.data('actual-price')) || parseFloat(priceInput.val()) || 0;
            const taxPercentage = parseFloat($(this).find('.tax-input').val()) || 0;

            const rowTotal = quantity * priceWithTax;

            // حساب الضريبة من السعر الشامل
            const priceBeforeTax = priceWithTax / (1 + taxPercentage / 100);
            const rowTax = priceWithTax - priceBeforeTax;

            grandTotal += rowTotal;
            totalTax += (rowTax * quantity);
        });

        // حساب المجموع قبل الضريبة
        const subtotal = grandTotal - totalTax;

        // عرض النتائج مقربة إلى رقمين عشريين فقط
        $('#subtotal').text(subtotal.toFixed(2));
        $('#tax-total').text(totalTax.toFixed(2));
        $('#grand-total').text(grandTotal.toFixed(2));
    }

    // Initialize on load
    updateDeleteButtons();
    calculateTotals();
});
</script>

<style>
    .table td {
        vertical-align: middle;
    }
    .product-row input {
        font-size: 0.875rem;
    }
</style>
@endsection
