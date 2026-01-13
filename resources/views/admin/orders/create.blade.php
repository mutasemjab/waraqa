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
                            <input type="hidden" name="user_id" id="user_id" value="">
                            <input type="text" id="seller_search" class="form-control"
                                   placeholder="{{ __('messages.search') }}" autocomplete="off">
                            <div id="sellers-dropdown" class="border rounded mt-1" style="display:none; position: absolute; width: 100%; max-height: 300px; overflow-y: auto; background: white; z-index: 1000;">
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
                            <x-search-select
                                model="App\Models\Warehouse"
                                fieldName="from_warehouse_id"
                                label="fromWarehouse"
                                placeholder="Search..."
                                limit="10"
                                required="true"
                            />
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
                                            <hr class="my-2">
                                            <div class="d-flex justify-content-between align-items-center" id="commission-row" style="display:none;">
                                                <span>{{ __('messages.commission_percentage') ?? 'Commission %' }}:</span>
                                                <span><span id="commission-percentage">0</span>%</span>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center text-success" id="commission-value-row" style="display:none;">
                                                <strong>{{ __('messages.commission_value') ?? 'Commission Value' }}:</strong>
                                                <strong><x-riyal-icon /> <span id="commission-value">0.00</span></strong>
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
                    success: function(data) {
                        if (data.length === 0) {
                            response([{ label: '{{ __("messages.not_found") }}', value: '' }]);
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

        $('.product-row').each(function() {
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
    $('.product-row').each(function() {
        attachQuantityListener($(this));
    });

    // Paid amount change with validation
    $('#paid_amount').on('input', function() {
        const paidAmount = parseFloat($(this).val()) || 0;

        // Calculate current grand total
        let subtotal = 0;
        let totalTax = 0;
        $('.product-row').each(function() {
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

    // Seller search functionality
    let sellerSearchTimer;

    function performSellerSearch(term) {
        const dropdown = $('#sellers-dropdown');

        $.ajax({
            url: '{{ route("sellers.search") }}',
            method: 'GET',
            data: { term: term, limit: 5 },
            success: function(data) {
                if (data.length > 0) {
                    let html = '';
                    data.forEach(function(seller) {
                        html += `<div class="p-2 border-bottom seller-item" data-id="${seller.id}" data-text="${seller.text}" style="cursor: pointer;">
                                ${seller.text}
                            </div>`;
                    });
                    dropdown.html(html).show();

                    // Add click handlers to seller items
                    $('.seller-item').on('click', function() {
                        const id = $(this).data('id');
                        const text = $(this).data('text');
                        $('#user_id').val(id);
                        $('#seller_search').val(text);
                        dropdown.hide();

                        // Load events for selected seller
                        loadSellerEvents(id);
                    });
                } else {
                    dropdown.html('<div class="p-2">{{ __("messages.no_results") }}</div>').show();
                }
            },
            error: function(xhr) {
                console.error('Error searching sellers:', xhr);
            }
        });
    }

    // Show all sellers when focused
    $('#seller_search').on('focus', function() {
        const term = $(this).val().trim();
        if (term.length === 0) {
            performSellerSearch('');
        }
    });

    $('#seller_search').on('input', function() {
        const term = $(this).val().trim();
        const dropdown = $('#sellers-dropdown');

        if (term.length < 1) {
            performSellerSearch('');
            return;
        }

        clearTimeout(sellerSearchTimer);
        sellerSearchTimer = setTimeout(() => {
            performSellerSearch(term);
        }, 300);
    });

    // Close dropdown when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#seller_search, #sellers-dropdown').length) {
            $('#sellers-dropdown').hide();
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
            success: function(data) {
                let options = '<option value="">{{ __("messages.choose_event") }}</option>';
                let validCount = 0;
                let invalidCount = 0;

                // Clear previous events data
                allEventsData = {};

                if (data.length > 0) {
                    data.forEach(function(event) {
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
            error: function(xhr) {
                console.error('Error fetching events:', xhr);
                eventSelect.html('<option value="">{{ __("messages.error_loading_events") }}</option>');
                eventInfo.html('');
                allEventsData = {};
            }
        });
    }

    // Load seller events when user is selected via change event
    $(document).on('change', '#user_id', function() {
        const sellerId = $(this).val();
        loadSellerEvents(sellerId);
    });

    // Store event commission data when event changes
    let eventCommissionData = {};
    let allEventsData = {};

    // Update commission box with live calculations
    const updateCommissionBox = function() {
        if (!eventCommissionData.id) return;

        const percentage = eventCommissionData.percentage;

        // Calculate grand total
        let subtotal = 0;
        let totalTax = 0;

        $('.product-row').each(function() {
            const priceWithoutTax = parseFloat($(this).data('price-without-tax')) || 0;
            const tax = parseFloat($(this).find('.product-tax').val()) || 0;
            const quantity = parseInt($(this).find('.quantity-input').val()) || 0;

            const lineSubtotal = priceWithoutTax * quantity;
            const lineTax = (lineSubtotal * tax) / 100;

            subtotal += lineSubtotal;
            totalTax += lineTax;
        });

        const grandTotal = subtotal + totalTax;
        const commissionValue = (grandTotal * percentage) / 100;

        $('#commission-percentage').text(percentage.toFixed(2));
        $('#commission-value').text(commissionValue.toFixed(2));

        // Show commission rows
        $('#commission-row, #commission-value-row, #commission-divider').show();
    };

    $(document).on('change', '#event_id', function() {
        const eventId = $(this).val();

        if (!eventId || !allEventsData[eventId]) {
            eventCommissionData = {};
            $('#commission-row, #commission-value-row, #commission-divider').hide();
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
    const originalCalculateTotals = function() {
        let subtotal = 0;
        let totalTax = 0;

        $('.product-row').each(function() {
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
        if (eventCommissionData.id) {
            updateCommissionBox();
        }
    };

    // Override calculateTotals to use our version
    function calculateTotals() {
        originalCalculateTotals();
    }
});
</script>
@endsection