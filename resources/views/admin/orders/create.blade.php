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
                                <label for="event_id">{{ __('messages.select_event') }}</label>
                                <select name="event_id" id="event_id" class="form-control">
                                    <option value="">{{ __('messages.choose_event') }}</option>
                                </select>
                                <small class="form-text text-muted d-block mt-2">
                                    <i class="fas fa-info-circle"></i>
                                    <span id="event-info"></span>
                                </small>
                            </div>


                            <div class="form-group mb-3">
                                <x-search-select model="App\Models\Warehouse" fieldName="from_warehouse_id"
                                    label="fromWarehouse" placeholder="Search..." limit="10" required="true" />
                            </div>

                            <div class="form-group mb-3">
                                <label>{{ __('messages.products') }}</label>
                                <div id="products-container">
                                    <div class="product-row mb-3 p-3 border rounded">
                                        <div class="row">
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
                                            <div class="col-md-3">
                                                <input type="number" name="products[0][quantity]"
                                                    class="form-control quantity-input"
                                                    placeholder="{{ __('messages.quantity') }}" min="1" required>
                                            </div>
                                            <div class="col-md-3">
                                                <span><x-riyal-icon style="width: 14px; height: 14px;" /> <span
                                                        class="line-total">0.00</span></span>
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
                                                <hr class="my-2">
                                                <div id="event-commission-container" style="display:none;" class="mb-2">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <span>{{ __('messages.event_commission') ?? 'Event Commission' }}:</span>
                                                        <span><span id="event-commission-percentage">0</span>%</span>
                                                    </div>
                                                    <div class="d-flex justify-content-between align-items-center text-info">
                                                        <small>{{ __('messages.commission_value') ?? 'Value' }}:</small>
                                                        <small><x-riyal-icon /> <span id="event-commission-value">0.00</span></small>
                                                    </div>
                                                </div>

                                                <div id="seller-commission-container" style="display:none;" class="mb-2">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <span>{{ __('messages.seller_commission') }}:</span>
                                                        <span><span id="seller-commission-percentage">0</span>%</span>
                                                    </div>
                                                    <div class="d-flex justify-content-between align-items-center text-info">
                                                        <small>{{ __('messages.commission_value') ?? 'Value' }}:</small>
                                                        <small><x-riyal-icon /> <span id="seller-commission-value">0.00</span></small>
                                                    </div>
                                                </div>

                                                <div id="total-commission-container" style="display:none;"
                                                    class="d-flex justify-content-between align-items-center text-success border-top pt-1 mt-1">
                                                    <strong>{{ __('messages.total_commission') ?? 'Total Commission' }}:</strong>
                                                    <strong><x-riyal-icon /> <span id="total-commission-value">0.00</span></strong>
                                                </div>

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
                                    <option value="1">{{ __('messages.completed') }}</option>
                                    <option value="2">{{ __('messages.cancelled') }}</option>
                                    <option value="6">{{ __('messages.refund') ?? 'Refund' }}</option>
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
            let allEventsData = {};
            let eventCommissionData = {};
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

                newRow.find('.line-total').text('0.00');
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

                row.find('.line-total').text(total.toFixed(2));
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
                const remainingAmount = Math.max(0, grandTotal - paidAmount);

                $('#subtotal').text(subtotal.toFixed(2));
                $('#tax-total').text(totalTax.toFixed(2));
                $('#grand-total').text(grandTotal.toFixed(2));
                $('#remaining-amount').text(remainingAmount.toFixed(2));
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

                if (paidAmount > grandTotal) {
                    Swal.fire({
                        icon: 'error',
                        title: '{{ __("messages.error") }}',
                        text: '{{ __("messages.paid_amount_exceeds_total") }}',
                        confirmButtonText: '{{ __("messages.confirm") }}'
                    });
                    $(this).val(grandTotal.toFixed(2));
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
                            data.forEach(function (user) {
                                html += `<div class="p-2 border-bottom user-item" data-id="${user.id}" data-text="${user.text}" data-commission="${user.commission_percentage}" style="cursor: pointer;">
                                        ${user.text}
                                    </div>`;
                            });
                            dropdown.html(html).show();

                            // Add click handlers to user items
                            $('.user-item').on('click', function () {
                                const id = $(this).data('id');
                                const text = $(this).data('text');
                                const commission = parseFloat($(this).data('commission')) || 0;

                                $('#user_id').val(id);
                                $('#user_search').val(text);
                                dropdown.hide();

                                // Update seller commission
                                currentSellerCommission = commission;
                                updateCommissionBox(); 

                                // Load events for selected user if it's a seller
                                if (buyerType === 'seller') {
                                    loadSellerEvents(id);
                                } else {
                                    // Clear events for customers
                                    clearEvents();
                                }
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
                clearEvents();
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

            // Load seller events function
            function loadSellerEvents(sellerId) {
                const eventSelect = $('#event_id');
                const eventInfo = $('#event-info');

                if (!sellerId) {
                    eventSelect.html('<option value="">{{ __("messages.choose_event") }}</option>');
                    eventInfo.html('');
                    allEventsData = {};
                    return;
                }

                $.ajax({
                    url: '{{ route("sellers.events", ":sellerId") }}'.replace(':sellerId', sellerId),
                    method: 'GET',
                    success: function (data) {
                        let options = '<option value="">{{ __("messages.choose_event") }}</option>';
                        let validCount = 0;
                        let invalidCount = 0;

                        // Clear previous events data
                        allEventsData = {};

                        if (data.length > 0) {
                            data.forEach(function (event) {
                                // Store event data for later use
                                allEventsData[event.id] = event;

                                if (event.is_valid) {
                                    options += `<option value="${event.id}">${event.text} <span style="color: green;">✓</span></option>`;
                                    validCount++;
                                } else {
                                    options += `<option value="${event.id}" style="color: #999;">${event.text}</option>`;
                                    invalidCount++;
                                }
                            });

                            // Update info message
                            let infoMsg = `{{ __("messages.total_events") }}: ${data.length} | `;
                            infoMsg += `<span style="color: green;"><i class="fas fa-check-circle"></i> {{ __("messages.active_events") }}: ${validCount}</span>`;
                            if (invalidCount > 0) {
                                infoMsg += ` | <span style="color: #999;"><i class="fas fa-times-circle"></i> {{ __("messages.expired_events") }}: ${invalidCount}</span>`;
                            }
                            eventInfo.html(infoMsg);
                        } else {
                            options = '<option value="">{{ __("messages.no_events") }}</option>';
                            eventInfo.html('');
                        }

                        eventSelect.html(options);
                    },
                    error: function (xhr) {
                        console.error('Error fetching events:', xhr);
                        eventSelect.html('<option value="">{{ __("messages.error_loading_events") }}</option>');
                        eventInfo.html('');
                        allEventsData = {};
                    }
                });
            }

            // Clear events function
            function clearEvents() {
                $('#event_id').html('<option value="">{{ __("messages.choose_event") }}</option>');
                $('#event-info').html('');
                allEventsData = {};
                $('#event_id').html('<option value="">{{ __("messages.choose_event") }}</option>');
                $('#event-info').html('');
                allEventsData = {};
                eventCommissionData = {};
                // Do not hide if seller commission applies? 
                // Wait, clearEvents is often called when switching context. 
                // But if we just cleared events, we might still have seller commission if the user is still selected.
                // However, usually clearEvents is called when User changes or Type changes.
                // If User changes, Seller Commission also changes (handled in user click).
                
                // Let's rely on updateCommissionBox to hide if needed.
                updateCommissionBox();
            }

            // Load seller events when user is selected via change event
            $(document).on('change', '#user_id', function () {
                const userId = $(this).val();
                if (buyerType === 'seller') {
                    loadSellerEvents(userId);
                } else {
                    clearEvents();
                }
            });

            // Update commission box with live calculations
            const updateCommissionBox = function () {
                let eventPercentage = eventCommissionData.percentage || 0;
                let sellerPercentage = 0;

                if (buyerType === 'seller' && currentSellerCommission > 0) {
                    sellerPercentage = currentSellerCommission;
                }

                let subtotal = 0;
                
                // Calculate grand total (subtotal needed for commission)
                $('.product-row').each(function () {
                    const priceWithoutTax = parseFloat($(this).data('price-without-tax')) || 0;
                    const quantity = parseInt($(this).find('.quantity-input').val()) || 0;
                    subtotal += priceWithoutTax * quantity;
                });

                let totalCommission = 0;
                let hasCommission = false;

                // Handle Event Commission
                if (eventPercentage > 0) {
                    const eventValue = (subtotal * eventPercentage) / 100;
                    $('#event-commission-percentage').text(eventPercentage.toFixed(2));
                    $('#event-commission-value').text(eventValue.toFixed(2));
                    $('#event-commission-container').show();
                    totalCommission += eventValue;
                    hasCommission = true;
                } else {
                    $('#event-commission-container').hide();
                }

                // Handle Seller Commission
                if (sellerPercentage > 0) {
                    const sellerValue = (subtotal * sellerPercentage) / 100;
                    $('#seller-commission-percentage').text(sellerPercentage.toFixed(2));
                    $('#seller-commission-value').text(sellerValue.toFixed(2));
                    $('#seller-commission-container').show();
                    totalCommission += sellerValue;
                    hasCommission = true;
                } else {
                    $('#seller-commission-container').hide();
                }

                if (hasCommission) {
                    $('#total-commission-value').text(totalCommission.toFixed(2));
                    $('#total-commission-container').show();
                    $('#commission-divider').show();
                } else {
                    $('#total-commission-container').hide();
                    $('#commission-divider').hide();
                }
            };

            $(document).on('change', '#event_id', function () {
                const eventId = $(this).val();

                if (!eventId || !allEventsData[eventId]) {
                    eventCommissionData = {};
                    updateCommissionBox();
                    return;
                }

                // Get event data from stored events
                const eventData = allEventsData[eventId];
                eventCommissionData = {
                    id: eventData.id,
                    percentage: parseFloat(eventData.commission_percentage)
                };
                updateCommissionBox();
            });

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
                const remainingAmount = Math.max(0, grandTotal - paidAmount);

                $('#subtotal').text(subtotal.toFixed(2));
                $('#tax-total').text(totalTax.toFixed(2));
                $('#grand-total').text(grandTotal.toFixed(2));
                $('#remaining-amount').text(remainingAmount.toFixed(2));

                // Update commission if event is selected
                // Update commission if event is selected or seller has commission
                updateCommissionBox();
            };

            // Override calculateTotals to use our version
            // Override calculateTotals to use our version
            function calculateTotals() {
                originalCalculateTotals();
            }

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