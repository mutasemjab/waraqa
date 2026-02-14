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
                                <label>{{ __('messages.buyer_type') }}</label>
                                <div class="btn-group btn-group-toggle" data-toggle="buttons" role="group">
                                    <label class="btn btn-outline-primary active">
                                        <input type="radio" name="buyer_type" value="seller" checked>
                                        {{ __('messages.seller') }}
                                    </label>
                                    <label class="btn btn-outline-primary">
                                        <input type="radio" name="buyer_type" value="customer">
                                        {{ __('messages.customer') }}
                                    </label>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="user_id" id="user-search-label">{{ __('messages.select_seller') }}</label>
                                <input type="hidden" name="user_id" id="user_id" value="">
                                <input type="text" id="user_search" class="form-control"
                                    placeholder="{{ __('messages.search') }}" autocomplete="off">
                                <div id="users-dropdown" class="border rounded mt-1"
                                    style="display:none; position: absolute; width: 100%; max-height: 300px; overflow-y: auto; background: white; z-index: 1000;">
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="order_date">{{ __('messages.order_date') }}</label>
                                <input type="date" name="order_date" id="order_date" class="form-control" required>
                            </div>


                            <div class="form-group mb-3">
                                <x-search-select model="App\Models\Warehouse" fieldName="from_warehouse_id"
                                    label="fromWarehouse" placeholder="Search..." limit="10" required="true" filter="without_user" />
                            </div>

                            <div class="form-group mb-3">
                                <label>{{ __('messages.products') }}</label>
                                <div id="products-container">
                                    <div class="product-row mb-3 p-3 border rounded">
                                        <div class="row align-items-center">
                                            <div class="col-md-4">
                                                <input type="hidden" class="form-control product-id" name="products[0][id]"
                                                    required />
                                                <input type="hidden" class="form-control product-price"
                                                    name="products[0][price]" />
                                                <input type="hidden" class="form-control product-tax"
                                                    name="products[0][tax]" />
                                                <input type="text" class="form-control product-search"
                                                    name="products[0][name]"
                                                    placeholder="{{ __('messages.search_product') }}" />
                                            </div>
                                            <div class="col-md-2">
                                                <input type="number" name="products[0][quantity]"
                                                    class="form-control quantity-input"
                                                    placeholder="{{ __('messages.quantity') }}" min="1" required>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="input-group input-group-sm">
                                                    <input type="text" class="form-control unit-price-display" readonly value="0.00">
                                                    <span class="input-group-text"><x-riyal-icon style="width: 14px; height: 14px;" /></span>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="input-group input-group-sm">
                                                    <input type="text" class="form-control line-total-display" readonly value="0.00">
                                                    <span class="input-group-text"><x-riyal-icon style="width: 14px; height: 14px;" /></span>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <button type="button" class="btn btn-danger remove-product btn-sm"
                                                    disabled>×</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-success"
                                    id="add-product">{{ __('messages.add_product') }}</button>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="paid_amount">{{ __('messages.paid_amount') }}</label>
                                        <input type="number" name="paid_amount" id="paid_amount" class="form-control"
                                            min="0" step="0.01" placeholder="0.00">
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
                                                <div
                                                    class="d-flex justify-content-between font-weight-bold align-items-center">
                                                    <span>{{ __('messages.total') }}:</span>
                                                    <span><x-riyal-icon /> <span id="grand-total">0.00</span></span>
                                                </div>
                                                {{-- Seller Commission Display Section --}}
                                                {{-- Shows when seller has commission_percentage set in their user profile --}}
                                                {{-- Calculated as: (subtotal * user.commission_percentage) / 100 --}}
                                                {{-- Hidden when seller has no commission or not a seller --}}
                                                <div id="seller-commission-container" style="display:none;" class="mb-2">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <span>{{ __('messages.seller_commission') }}:</span>
                                                        {{-- Seller commission percentage from user.commission_percentage --}}
                                                        <span><span id="seller-commission-percentage">0</span>%</span>
                                                    </div>
                                                    <div class="d-flex justify-content-between align-items-center text-info">
                                                        <small>{{ __('messages.commission_value') ?? 'Value' }}:</small>
                                                        {{-- Calculated commission value in currency --}}
                                                        <small><x-riyal-icon /> <span id="seller-commission-value">0.00</span></small>
                                                    </div>
                                                </div>

                                                {{-- Customer Discount Display Section --}}
                                                {{-- Shows when buyer type is customer --}}
                                                {{-- Calculated as: (grand_total * discount_percentage) / 100 --}}
                                                {{-- Hidden when buyer is not a customer --}}
                                                <div id="customer-discount-container" style="display:none;" class="mb-2">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <span>{{ __('messages.customer_discount') ?? 'Customer Discount' }}:</span>
                                                        {{-- Customer discount percentage from user.commission_percentage (reused field) --}}
                                                        <span><span id="customer-discount-percentage">0</span>%</span>
                                                    </div>
                                                    <div class="d-flex justify-content-between align-items-center text-info">
                                                        <small>{{ __('messages.discount_value') ?? 'Discount Value' }}:</small>
                                                        {{-- Calculated discount value in currency --}}
                                                        <small><x-riyal-icon /> <span id="customer-discount-value">0.00</span></small>
                                                    </div>
                                                </div>

                                                {{-- Total Commission Display Section --}}
                                                {{-- Shows the sum of event commission + seller commission --}}
                                                {{-- Hidden when neither event commission nor seller commission applies --}}
                                                <div id="total-commission-container" style="display:none;"
                                                    class="d-flex justify-content-between align-items-center text-success border-top pt-1 mt-1">
                                                    <strong>{{ __('messages.total_commission') ?? 'Total Commission' }}:</strong>
                                                    {{-- Sum of event commission value + seller commission value --}}
                                                    <strong><x-riyal-icon /> <span id="total-commission-value">0.00</span></strong>
                                                </div>

                                                {{-- Visual separator divider displayed only when commissions exist --}}
                                                <hr class="my-2" id="commission-divider" style="display:none;">
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
                                <label for="status">{{ __('messages.status') }}</label>
                                <select name="status" id="status" class="form-control" required>
                                    <option value="">{{ __('messages.select_status') ?? 'Select Status' }}</option>
                                    @foreach(\App\Enums\OrderStatus::cases() as $status)
                                        <option value="{{ $status->value }}">{{ $status->getLabelLocalized() }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label for="note">{{ __('messages.note') }}</label>
                                <textarea name="note" id="note" class="form-control" rows="3"
                                    placeholder="{{ __('messages.optional_note') }}"></textarea>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary"
                                    id="submitBtn">{{ __('messages.create_order') }}</button>
                                <a href="{{ route('orders.index') }}"
                                    class="btn btn-secondary">{{ __('messages.cancel') }}</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            let rowIdx = 1;
            let userSearchTimer;
            let buyerType = 'seller'; // Default type
            let currentSellerCommission = 0;

            function initializeProductSearch() {
                $('.product-search').autocomplete({
                    source: function (request, response) {
                        // Get the selected warehouse
                        const warehouseInput = $('input[name="from_warehouse_id"]').closest('.form-group').find('input[type="hidden"]');
                        const warehouseId = warehouseInput.val() || warehouseInput.data('value');

                        if (!warehouseId) {
                            Swal.fire({
                                icon: 'warning',
                                title: '{{ __("messages.warning") }}',
                                text: '{{ __("messages.select_warehouse_first") }}',
                                confirmButtonText: '{{ __("messages.confirm") }}'
                            });
                            response([]);
                            return;
                        }

                        $.ajax({
                            url: '{{ route("products.search") }}',
                            dataType: 'json',
                            data: {
                                term: request.term,
                                warehouse_id: warehouseId
                            },
                            success: function (data) {
                                if (data.length === 0) {
                                    response([{ label: '{{ __("messages.not_found") }}', value: '' }]);
                                } else {
                                    response($.map(data, function (item) {
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
                            error: function (xhr, status, error) {
                                console.error('AJAX Error:', status, error);
                            }
                        });
                    },
                    minLength: 2,
                    select: function (event, ui) {
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
                            selectedRow.find('.unit-price-display').val(sellingPrice.toFixed(2));

                            // Calculate price without tax from selling price: priceWithoutTax = sellingPrice / (1 + tax/100)
                            const priceWithoutTax = sellingPrice / (1 + (taxRate / 100));
                            selectedRow.data('price-without-tax', priceWithoutTax);

                            // Fetch available quantity with warehouse_id
                            const warehouseInput = $('input[name="from_warehouse_id"]').closest('.form-group').find('input[type="hidden"]');
                            const warehouseId = warehouseInput.val() || warehouseInput.data('value');

                            let quantityUrl = '{{ route("products.available-quantity", ":productId") }}'.replace(':productId', productId);
                            if (warehouseId) {
                                quantityUrl += '?warehouse_id=' + warehouseId;
                            }

                            $.ajax({
                                url: quantityUrl,
                                method: 'GET',
                                success: function (data) {
                                    selectedRow.data('available-quantity', data.available_quantity);
                                },
                                error: function (xhr) {
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
            $('#add-product').on('click', function () {
                const container = $('#products-container');
                const newRow = container.find('.product-row').first().clone();

                // Update indices
                newRow.find('[name]').each(function () {
                    const name = $(this).attr('name');
                    if (name) {
                        $(this).attr('name', name.replace(/\[0\]/, `[${rowIdx}]`));
                    }
                    $(this).val('');
                });

                newRow.find('.unit-price-display').val('0.00');
                newRow.find('.line-total-display').val('0.00');
                newRow.find('.remove-product').prop('disabled', false);

                container.append(newRow);
                rowIdx++;

                initializeProductSearch();
                attachQuantityListener(newRow);
            });

            // Remove product row
            $(document).on('click', '.remove-product:not(:disabled)', function () {
                $(this).closest('.product-row').remove();
                calculateTotals();
            });

            function attachQuantityListener(row) {
                row.find('.quantity-input').off('input').on('input', function () {
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
                const priceWithoutTax = parseFloat(row.data('price-without-tax')) || 0;
                const quantity = parseInt(row.find('.quantity-input').val()) || 0;
                const tax = parseFloat(row.find('.product-tax').val()) || 0;

                const subtotal = priceWithoutTax * quantity;
                const taxAmount = (subtotal * tax) / 100;
                const total = subtotal + taxAmount;

                row.find('.line-total-display').val(total.toFixed(2));
                calculateTotals();
            }

            function calculateTotals() {
                let subtotal = 0;
                let totalTax = 0;

                $('.product-row').each(function () {
                    const priceWithoutTax = parseFloat($(this).data('price-without-tax')) || 0;
                    const tax = parseFloat($(this).find('.product-tax').val()) || 0;
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

                {{-- Calculate remaining amount, taking discount/commission into account --}}
                {{-- For customers: remaining = grand_total - discount --}}
                {{-- For sellers: remaining = grand_total - seller_commission --}}
                let amountDue = grandTotal;
                let deduction = 0;

                if (buyerType === 'customer' && currentSellerCommission > 0) {
                    // Customer discount calculated on grand total
                    deduction = (grandTotal * currentSellerCommission) / 100;
                    amountDue = grandTotal - deduction;
                } else if (buyerType === 'seller') {
                    // Seller commission calculated on subtotal
                    let sellerPercentage = currentSellerCommission || 0;

                    if (sellerPercentage > 0) {
                        deduction += (subtotal * sellerPercentage) / 100;
                    }

                    amountDue = grandTotal - deduction;
                }

                const remainingAmount = Math.max(0, amountDue - paidAmount);

                $('#subtotal').text(subtotal.toFixed(2));
                $('#tax-total').text(totalTax.toFixed(2));
                $('#grand-total').text(grandTotal.toFixed(2));
                $('#remaining-amount').text(remainingAmount.toFixed(2));

                {{-- Update commission/discount display --}}
                updateCommissionBox();
            }

            // Initialize first row search and quantity listener
            initializeProductSearch();
            $('.product-row').each(function () {
                attachQuantityListener($(this));
            });

            // Paid amount change with validation
            $('#paid_amount').on('input', function () {
                const paidAmount = parseFloat($(this).val()) || 0;

                // Calculate current grand total
                let subtotal = 0;
                let totalTax = 0;
                $('.product-row').each(function () {
                    const priceWithoutTax = parseFloat($(this).data('price-without-tax')) || 0;
                    const tax = parseFloat($(this).find('.product-tax').val()) || 0;
                    const quantity = parseInt($(this).find('.quantity-input').val()) || 0;
                    const lineSubtotal = priceWithoutTax * quantity;
                    const lineTax = (lineSubtotal * tax) / 100;
                    subtotal += lineSubtotal;
                    totalTax += lineTax;
                });
                const grandTotal = subtotal + totalTax;

                {{-- Calculate amount due, taking discount/commission into account --}}
                {{-- For customers: amount_due = grand_total - discount --}}
                {{-- For sellers: amount_due = grand_total - (event_commission + seller_commission) --}}
                let amountDue = grandTotal;
                let deduction = 0;

                if (buyerType === 'customer' && currentSellerCommission > 0) {
                    // Customer discount calculated on grand total
                    deduction = (grandTotal * currentSellerCommission) / 100;
                    amountDue = grandTotal - deduction;
                } else if (buyerType === 'seller') {
                    // Seller commission calculated on subtotal
                    let sellerPercentage = currentSellerCommission || 0;

                    if (sellerPercentage > 0) {
                        deduction += (subtotal * sellerPercentage) / 100;
                    }

                    amountDue = grandTotal - deduction;
                }

                // Round both values to 2 decimals to handle floating-point precision issues
                const roundedPaidAmount = parseFloat(paidAmount.toFixed(2));
                const roundedAmountDue = parseFloat(amountDue.toFixed(2));

                if (roundedPaidAmount > roundedAmountDue) {
                    Swal.fire({
                        icon: 'error',
                        title: '{{ __("messages.error") }}',
                        text: '{{ __("messages.paid_amount_exceeds_total") }}',
                        confirmButtonText: '{{ __("messages.confirm") }}'
                    });
                    $(this).val(amountDue.toFixed(2));
                }

                calculateTotals();
            });

            // User search functionality
            function performUserSearch(term) {
                const dropdown = $('#users-dropdown');
                const searchUrl = buyerType === 'seller'
                    ? '{{ route("sellers.search") }}'
                    : '{{ route("customers.search") }}';

                $.ajax({
                    url: searchUrl,
                    method: 'GET',
                    data: { term: term, limit: 5 },
                    success: function (data) {
                        if (data.length > 0) {
                            let html = '';
                            data.forEach(function (user) {
                                html += `<div class="p-2 border-bottom user-item" data-id="${user.id}" data-text="${user.text}" data-commission="${user.commission_percentage}" style="cursor: pointer;">
                                        ${user.text}
                                    </div>`;
                            });
                            dropdown.html(html).show();

                            // Add click handlers to user items - when user selects from dropdown
                            $('.user-item').on('click', function () {
                                const id = $(this).data('id');
                                const text = $(this).data('text');
                                // Get user's commission_percentage from the dropdown item data
                                const commission = parseFloat($(this).data('commission')) || 0;

                                // Set selected user in hidden input
                                $('#user_id').val(id);
                                // Show selected user name in search field
                                $('#user_search').val(text);
                                // Hide dropdown
                                dropdown.hide();

                                {{-- Update seller commission percentage from user profile --}}
                                currentSellerCommission = commission;
                                // Recalculate and display commission boxes
                                updateCommissionBox();
                            });
                        } else {
                            dropdown.html('<div class="p-2">{{ __("messages.no_results") }}</div>').show();
                        }
                    },
                    error: function (xhr) {
                        console.error('Error searching users:', xhr);
                    }
                });
            }

            // Update label and clear search when buyer type changes
            $(document).on('change', 'input[name="buyer_type"]', function () {
                buyerType = $(this).val();
                const label = $('#user-search-label');

                if (buyerType === 'seller') {
                    label.text('{{ __("messages.select_seller") }}');
                } else {
                    label.text('{{ __("messages.select_customer") }}');
                }

                // Clear previous selections
                $('#user_id').val('');
                $('#user_search').val('');
                $('#users-dropdown').html('').hide();
                currentSellerCommission = 0;

                {{-- Update commission/discount display when buyer type changes --}}
                calculateTotals();
            });

            // Show all users when focused
            $('#user_search').on('focus', function () {
                const term = $(this).val().trim();
                if (term.length === 0) {
                    performUserSearch('');
                }
            });

            $('#user_search').on('input', function () {
                const term = $(this).val().trim();

                if (term.length < 1) {
                    performUserSearch('');
                    return;
                }

                clearTimeout(userSearchTimer);
                userSearchTimer = setTimeout(() => {
                    performUserSearch(term);
                }, 300);
            });

            // Close dropdown when clicking outside
            $(document).on('click', function (e) {
                if (!$(e.target).closest('#user_search, #users-dropdown').length) {
                    $('#users-dropdown').hide();
                }
            });

            {{-- Update commission box with live calculations --}}
            {{-- This function recalculates and displays all commission information --}}
            {{-- Called whenever: products change, seller changes, quantities updated --}}
            const updateCommissionBox = function () {
                // CRITICAL: Only show commission/discount if a user is actually selected
                // If no user is selected, hide everything immediately
                if (!currentSellerCommission || currentSellerCommission <= 0) {
                    // Use cssText with !important to override Bootstrap's d-flex class
                    $('#customer-discount-container').css('cssText', 'display: none !important');
                    $('#seller-commission-container').css('cssText', 'display: none !important');
                    $('#total-commission-container').css('cssText', 'display: none !important');
                    $('#commission-divider').css('cssText', 'display: none !important');
                    return;
                }

                let sellerPercentage = currentSellerCommission; // User is selected with commission > 0

                // Calculate subtotal (price without tax × quantity) for all products
                let subtotal = 0;
                let totalTax = 0;
                $('.product-row').each(function () {
                    const priceWithoutTax = parseFloat($(this).data('price-without-tax')) || 0;
                    const tax = parseFloat($(this).find('.product-tax').val()) || 0;
                    const quantity = parseInt($(this).find('.quantity-input').val()) || 0;

                    const lineSubtotal = priceWithoutTax * quantity;
                    const lineTax = (lineSubtotal * tax) / 100;

                    subtotal += lineSubtotal;
                    totalTax += lineTax;
                });

                const grandTotal = subtotal + totalTax;

                {{-- Customer Discount Display Section --}}
                {{-- When buyer type is 'customer', show discount based on grand total --}}
                {{-- Formula: (grand_total × discount_percentage) / 100 --}}
                if (buyerType === 'customer') {
                    // Calculate customer discount value from grand total
                    const discountValue = (grandTotal * sellerPercentage) / 100;
                    // Display percentage
                    $('#customer-discount-percentage').text(sellerPercentage.toFixed(2));
                    // Display calculated discount value
                    $('#customer-discount-value').text(discountValue.toFixed(2));
                    // Show customer discount container
                    $('#customer-discount-container').css('cssText', '');
                    // Hide seller commission container when customer is selected
                    $('#seller-commission-container').css('cssText', 'display: none !important');
                    $('#total-commission-container').css('cssText', 'display: none !important');
                    $('#commission-divider').css('cssText', 'display: none !important');
                } else {
                    // Seller selected - hide customer discount and show commission boxes
                    $('#customer-discount-container').css('cssText', 'display: none !important');

                    {{-- Seller Commission Calculation and Display --}}
                    {{-- Formula: (subtotal × user.commission_percentage) / 100 --}}
                    {{-- Only shown when: buyerType='seller' AND user.commission_percentage > 0 --}}
                    const sellerValue = (subtotal * sellerPercentage) / 100;
                    $('#seller-commission-percentage').text(sellerPercentage.toFixed(2));
                    $('#seller-commission-value').text(sellerValue.toFixed(2));
                    $('#seller-commission-container').css('cssText', '');

                    {{-- Total Commission Display (which is just seller commission in this case) --}}
                    $('#total-commission-value').text(sellerValue.toFixed(2));
                    $('#total-commission-container').css('cssText', '');
                    $('#commission-divider').css('cssText', '');
                }
            };

            // Store original calculateTotals function
            const originalCalculateTotals = function () {
                let subtotal = 0;
                let totalTax = 0;

                $('.product-row').each(function () {
                    const priceWithoutTax = parseFloat($(this).data('price-without-tax')) || 0;
                    const tax = parseFloat($(this).find('.product-tax').val()) || 0;
                    const quantity = parseInt($(this).find('.quantity-input').val()) || 0;

                    const lineSubtotal = priceWithoutTax * quantity;
                    const lineTax = (lineSubtotal * tax) / 100;

                    subtotal += lineSubtotal;
                    totalTax += lineTax;
                });

                const grandTotal = subtotal + totalTax;
                const paidAmount = parseFloat($('#paid_amount').val()) || 0;

                {{-- Calculate remaining amount, taking discount/commission into account --}}
                {{-- For customers: remaining = grand_total - discount --}}
                {{-- For sellers: remaining = grand_total - seller_commission --}}
                let amountDue = grandTotal;
                let deduction = 0;

                if (buyerType === 'customer' && currentSellerCommission > 0) {
                    // Customer discount calculated on grand total
                    deduction = (grandTotal * currentSellerCommission) / 100;
                    amountDue = grandTotal - deduction;
                } else if (buyerType === 'seller') {
                    // Seller commission calculated on subtotal
                    let sellerPercentage = currentSellerCommission || 0;

                    if (sellerPercentage > 0) {
                        deduction += (subtotal * sellerPercentage) / 100;
                    }

                    amountDue = grandTotal - deduction;
                }

                const remainingAmount = Math.max(0, amountDue - paidAmount);

                $('#subtotal').text(subtotal.toFixed(2));
                $('#tax-total').text(totalTax.toFixed(2));
                $('#grand-total').text(grandTotal.toFixed(2));
                $('#remaining-amount').text(remainingAmount.toFixed(2));

                // Update commission/discount display
                updateCommissionBox();
            };

            // Override calculateTotals to use our version
            function calculateTotals() {
                originalCalculateTotals();
            }

            // Initialize commission/discount boxes (hidden by default since no user selected yet)
            updateCommissionBox();

            // Handle warehouse change - re-fetch available quantities
            $(document).on('change', 'input[name="from_warehouse_id"]', function() {
                const warehouseInput = $(this);
                const warehouseId = warehouseInput.val() || warehouseInput.data('value');

                if (!warehouseId) return;

                $('.product-row').each(function() {
                    const row = $(this);
                    const productId = row.find('.product-id').val();
                    
                    if (productId) {
                        let quantityUrl = '{{ route("products.available-quantity", ":productId") }}'.replace(':productId', productId);
                        quantityUrl += '?warehouse_id=' + warehouseId;

                        $.ajax({
                            url: quantityUrl,
                            method: 'GET',
                            success: function(data) {
                                row.data('available-quantity', data.available_quantity);
                                // Re-validate current quantity
                                row.find('.quantity-input').trigger('input');
                            },
                            error: function(xhr) {
                                console.error('Error fetching available quantity:', xhr);
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection