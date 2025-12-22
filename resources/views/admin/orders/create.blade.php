{{-- resources/views/admin/orders/create.blade.php --}}
@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h4>{{ __('messages.create_new_order') }}</h4>
                </div>
                <div class="card-body">

                    <form action="{{ route('orders.store') }}" method="POST" id="orderForm">
                        @csrf
                        
                        <div class="form-group mb-3">
                            <label for="user_id">{{ __('messages.select_user') }}</label>
                            <select name="user_id" id="user_id" class="form-control" required>
                                <option value="">{{ __('messages.choose_user') }}</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} - {{ $user->phone }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label>{{ __('messages.products') }}</label>
                            <div id="products-container">
                                <div class="product-row mb-3 p-3 border rounded">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <input type="hidden" class="form-control product-id" name="products[0][id]" required />
                                            <input type="hidden" class="form-control product-price" name="products[0][price]" />
                                            <input type="hidden" class="form-control product-tax" name="products[0][tax]" />
                                            <input type="text" class="form-control product-search" name="products[0][name]" placeholder="{{ __('messages.search_product') }}" />
                                        </div>
                                        <div class="col-md-3">
                                            <input type="number" name="products[0][quantity]"
                                                   class="form-control quantity-input"
                                                   placeholder="{{ __('messages.quantity') }}" min="1" required>
                                        </div>
                                        <div class="col-md-3">
                                            <span><x-riyal-icon style="width: 14px; height: 14px;" /> <span class="line-total">0.00</span></span>
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-danger remove-product btn-sm" disabled>×</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-success" id="add-product">{{ __('messages.add_product') }}</button>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="paid_amount">{{ __('messages.paid_amount') }}</label>
                                    <input type="number" name="paid_amount" id="paid_amount" 
                                           class="form-control" min="0" step="0.01" placeholder="0.00">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label>{{ __('messages.order_summary') }}</label>
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span>{{ __('messages.subtotal') }}:</span>
                                                <span><x-riyal-icon /> <span id="subtotal">0.00</span></span>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span>{{ __('messages.tax') }}:</span>
                                                <span><x-riyal-icon /> <span id="tax-total">0.00</span></span>
                                            </div>
                                            <div class="d-flex justify-content-between font-weight-bold align-items-center">
                                                <span>{{ __('messages.total') }}:</span>
                                                <span><x-riyal-icon /> <span id="grand-total">0.00</span></span>
                                            </div>
                                            <div class="d-flex justify-content-between text-muted align-items-center">
                                                <span>{{ __('messages.remaining') }}:</span>
                                                <span><x-riyal-icon /> <span id="remaining-amount">0.00</span></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="note">{{ __('messages.note') }}</label>
                            <textarea name="note" id="note" class="form-control" rows="3" placeholder="{{ __('messages.optional_note') }}"></textarea>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary" id="submitBtn">{{ __('messages.create_order') }}</button>
                            <a href="{{ route('orders.index') }}" class="btn btn-secondary">{{ __('messages.cancel') }}</a>
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

    function initializeProductSearch() {
        $('.product-search').autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: '{{ route("products.search") }}',
                    dataType: 'json',
                    data: {
                        term: request.term
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
                                    selling_price: item.selling_price,
                                    tax: item.tax,
                                    price_without_tax: item.price_without_tax
                                };
                            }));
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', status, error);
                    }
                });
            },
            minLength: 2,
            select: function(event, ui) {
                if (ui.item.value === '') {
                    event.preventDefault();
                } else {
                    const selectedRow = $(this).closest('.product-row');
                    const productId = ui.item.id;

                    // Fill product details
                    const sellingPrice = parseFloat(ui.item.selling_price);
                    const taxRate = parseFloat(ui.item.tax);

                    selectedRow.find('.product-id').val(productId);
                    selectedRow.find('.product-search').val(ui.item.label);
                    selectedRow.find('.product-price').val(sellingPrice.toFixed(2));
                    selectedRow.find('.product-tax').val(taxRate);

                    // Calculate price without tax from selling price: priceWithoutTax = sellingPrice / (1 + tax/100)
                    const priceWithoutTax = sellingPrice / (1 + (taxRate / 100));
                    selectedRow.data('price-without-tax', priceWithoutTax);

                    // Fetch available quantity
                    $.ajax({
                        url: '{{ route("products.available-quantity", ":productId") }}'.replace(':productId', productId),
                        method: 'GET',
                        success: function(data) {
                            selectedRow.data('available-quantity', data.available_quantity);
                        },
                        error: function(xhr) {
                            console.error('Error fetching available quantity:', xhr);
                        }
                    });

                    calculateLineTotal(selectedRow);

                    // Close autocomplete
                    $(this).autocomplete('close');
                    return false;
                }
            }
        });
    }

    // Add product row
    $('#add-product').on('click', function() {
        const container = $('#products-container');
        const newRow = container.find('.product-row').first().clone();

        // Update indices
        newRow.find('[name]').each(function() {
            const name = $(this).attr('name');
            if (name) {
                $(this).attr('name', name.replace(/\[0\]/, `[${rowIdx}]`));
            }
            $(this).val('');
        });

        newRow.find('.line-total').text('0.00');
        newRow.find('.remove-product').prop('disabled', false);

        container.append(newRow);
        rowIdx++;

        initializeProductSearch();
        attachQuantityListener(newRow);
    });

    // Remove product row
    $(document).on('click', '.remove-product:not(:disabled)', function() {
        $(this).closest('.product-row').remove();
        calculateTotals();
    });

    function attachQuantityListener(row) {
        row.find('.quantity-input').off('input').on('input', function() {
            const enteredQuantity = parseInt($(this).val()) || 0;
            const availableQuantity = row.data('available-quantity') || 0;

            if (enteredQuantity > availableQuantity) {
                const message = '{{ __("messages.quantity_exceeds_available") }}'
                    .replace(':entered', enteredQuantity)
                    .replace(':available', availableQuantity);

                Swal.fire({
                    icon: 'error',
                    title: '{{ __("messages.error") }}',
                    text: message,
                    confirmButtonText: '{{ __("messages.confirm") }}'
                });
                $(this).val(availableQuantity);
            }

            calculateLineTotal(row);
        });
    }

    function calculateLineTotal(row) {
        const price = parseFloat(row.find('.product-price').val()) || 0;
        const quantity = parseInt(row.find('.quantity-input').val()) || 0;

        const subtotal = price * quantity;

        row.find('.line-total').text(subtotal.toFixed(2));
        calculateTotals();
    }

    function calculateTotals() {
        let subtotal = 0;
        let totalTax = 0;

        $('.product-row').each(function() {
            const priceWithoutTax = parseFloat($(this).data('price-without-tax')) || 0;
            const tax = parseFloat($(this).find('.product-tax').val()) || 15;
            const quantity = parseInt($(this).find('.quantity-input').val()) || 0;

            // Subtotal is the price without tax × quantity
            const lineSubtotal = priceWithoutTax * quantity;
            // Tax is calculated from price without tax
            const lineTax = (lineSubtotal * tax) / 100;

            subtotal += lineSubtotal;
            totalTax += lineTax;
        });

        const grandTotal = subtotal + totalTax;
        const paidAmount = parseFloat($('#paid_amount').val()) || 0;
        const remainingAmount = Math.max(0, grandTotal - paidAmount);

        $('#subtotal').text(subtotal.toFixed(2));
        $('#tax-total').text(totalTax.toFixed(2));
        $('#grand-total').text(grandTotal.toFixed(2));
        $('#remaining-amount').text(remainingAmount.toFixed(2));
    }

    // Initialize first row search and quantity listener
    initializeProductSearch();
    $('.product-row').each(function() {
        attachQuantityListener($(this));
    });

    // Paid amount change
    $('#paid_amount').on('input', calculateTotals);
});
</script>
@endsection