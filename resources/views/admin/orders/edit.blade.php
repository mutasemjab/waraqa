{{-- resources/views/admin/orders/edit.blade.php --}}
@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h4>{{ __('messages.edit_order') }} - {{ $order->number }}</h4>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <form action="{{ route('orders.update', $order) }}" method="POST" id="orderForm">
                        @csrf
                        @method('PUT')

                        <div class="form-group mb-3">
                            <label>{{ __('messages.buyer_type') }}</label>
                            <div class="btn-group btn-group-toggle" data-toggle="buttons" role="group">
                                <label class="btn btn-outline-primary active">
                                    <input type="radio" name="buyer_type" value="seller" checked> {{ __('messages.seller') }}
                                </label>
                                <label class="btn btn-outline-primary">
                                    <input type="radio" name="buyer_type" value="customer"> {{ __('messages.customer') }}
                                </label>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="user_id" id="user-search-label">{{ __('messages.select_seller') }}</label>
                                    <input type="hidden" name="user_id" id="user_id" value="{{ $order->user_id }}">
                                    <input type="text" id="user_search" class="form-control"
                                           placeholder="{{ __('messages.search') }}" autocomplete="off"
                                           value="{{ $order->user ? $order->user->name : '' }}">
                                    <div id="users-dropdown" class="border rounded mt-1" style="display:none; position: absolute; width: 100%; max-height: 300px; overflow-y: auto; background: white; z-index: 1000;">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">{{ __('messages.order_status') }}</label>
                                    <select name="status" id="status" class="form-control" required>
                                        @foreach(\App\Enums\OrderStatus::cases() as $status)
                                            <option value="{{ $status->value }}" {{ $order->status == $status->value ? 'selected' : '' }}>{{ $status->getLabelLocalized() }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="order_date">{{ __('messages.order_date') }}</label>
                            <input type="date" name="order_date" id="order_date" class="form-control" required
                                   value="{{ $order->order_date ? $order->order_date->format('Y-m-d') : '' }}">
                        </div>

                        {{-- Event Selection Input (Edit Mode) - Hidden Field --}}
                        {{-- Populated dynamically after page load with seller's events --}}
                        {{-- Pre-selects the event from the current order if one exists --}}
                        <div class="form-group mb-3" style="display: none;">
                            <label for="event_id">{{ __('messages.select_event') }}</label>
                            <select name="event_id" id="event_id" class="form-control">
                                <option value="">{{ __('messages.choose_event') }}</option>
                            </select>
                            {{-- Info showing count of total, active, and expired events --}}
                            <small class="form-text text-muted d-block mt-2">
                                <i class="fas fa-info-circle"></i>
                                <span id="event-info"></span>
                            </small>
                        </div>


                        @php
                            $orderNoteVoucher = \App\Models\NoteVoucher::where('order_id', $order->id)->first();
                            $currentWarehouseId = $orderNoteVoucher ? $orderNoteVoucher->from_warehouse_id : null;
                        @endphp

                        <div class="form-group mb-3">
                            <x-search-select
                                model="App\Models\Warehouse"
                                fieldName="from_warehouse_id"
                                label="fromWarehouse"
                                placeholder="Search..."
                                limit="10"
                                required="true"
                                :value="$currentWarehouseId"
                            />
                        </div>

                        <div class="form-group mb-3">
                            <label>{{ __('messages.products') }}</label>
                            <div id="products-container">
                                @foreach($order->orderProducts as $index => $orderProduct)
                                <div class="product-row mb-3 p-3 border rounded">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <input type="hidden" class="form-control product-id" name="products[{{ $index }}][id]" value="{{ $orderProduct->product_id }}" required />
                                            <input type="hidden" class="form-control product-price" name="products[{{ $index }}][price]" value="{{ $orderProduct->product->selling_price ?? 0 }}" />
                                            <input type="hidden" class="form-control product-tax" name="products[{{ $index }}][tax]" value="{{ $orderProduct->tax_percentage }}" />
                                            <input type="text" class="form-control product-search" name="products[{{ $index }}][name]"
                                                   placeholder="{{ __('messages.search_product') }}"
                                                   value="{{ $orderProduct->product ? (app()->getLocale() === 'ar' ? ($orderProduct->product->name_ar ?? $orderProduct->product->name_en) : $orderProduct->product->name_en) : '' }}" />
                                        </div>
                                        <div class="col-md-3">
                                            <input type="number" name="products[{{ $index }}][quantity]"
                                                   class="form-control quantity-input"
                                                   placeholder="{{ __('messages.quantity') }}"
                                                   min="1"
                                                   value="{{ $orderProduct->quantity }}"
                                                   required>
                                        </div>
                                        <div class="col-md-3">
                                            <span><x-riyal-icon style="width: 14px; height: 14px;" /> <span class="line-total">{{ number_format($orderProduct->total_price_after_tax, 2) }}</span></span>
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-danger remove-product btn-sm"
                                                    {{ $loop->first && $order->orderProducts->count() == 1 ? 'disabled' : '' }}>×</button>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <button type="button" class="btn btn-success" id="add-product">{{ __('messages.add_product') }}</button>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="paid_amount">{{ __('messages.paid_amount') }}</label>
                                    <input type="number" name="paid_amount" id="paid_amount"
                                           class="form-control" min="0" step="0.01"
                                           value="{{ $order->paid_amount }}"
                                           placeholder="0.00">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label>{{ __('messages.order_summary') }}</label>
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span>{{ __('messages.subtotal') }}:</span>
                                                <span><x-riyal-icon /> <span id="subtotal">{{ number_format($order->total_prices - $order->total_taxes, 2) }}</span></span>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span>{{ __('messages.tax') }}:</span>
                                                <span><x-riyal-icon /> <span id="tax-total">{{ number_format($order->total_taxes, 2) }}</span></span>
                                            </div>
                                            <div class="d-flex justify-content-between font-weight-bold align-items-center">
                                                <span>{{ __('messages.total') }}:</span>
                                                <span><x-riyal-icon /> <span id="grand-total">{{ number_format($order->total_prices, 2) }}</span></span>
                                            </div>
                                            {{-- Event Commission Display Section (Edit Mode) --}}
                                            {{-- Shows when an event is selected for the order --}}
                                            {{-- Calculated as: (subtotal * event.commission_percentage) / 100 --}}
                                            {{-- Hidden when no event is selected --}}
                                            <div id="event-commission-container" style="display:none;" class="mb-2">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span>{{ __('messages.event_commission') ?? 'Event Commission' }}:</span>
                                                    {{-- Event commission percentage from event.commission_percentage --}}
                                                    <span><span id="event-commission-percentage">0</span>%</span>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center text-info">
                                                    <small>{{ __('messages.commission_value') ?? 'Value' }}:</small>
                                                    {{-- Calculated commission value in currency --}}
                                                    <small><x-riyal-icon /> <span id="event-commission-value">0.00</span></small>
                                                </div>
                                            </div>

                                            {{-- Seller Commission Display Section (Edit Mode) --}}
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

                                            {{-- Total Commission Display Section (Edit Mode) --}}
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
                                                <span><x-riyal-icon /> <span id="remaining-amount">{{ number_format($order->remaining_amount, 2) }}</span></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="note">{{ __('messages.note') }}</label>
                            <textarea name="note" id="note" class="form-control" rows="3"
                                      placeholder="{{ __('messages.optional_note') }}">{{ $order->note }}</textarea>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary" id="submitBtn">{{ __('messages.update_order') }}</button>
                            <a href="{{ route('orders.show', $order) }}" class="btn btn-info">{{ __('messages.view_order') }}</a>
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
    let rowIdx = {{ $order->orderProducts->count() }};
    const currentEventId = {{ $order->event_id ?? 'null' }};
    const currentUserId = {{ $order->user_id ?? 'null' }};
    let userSearchTimer;
    let buyerType = 'seller'; // Default type
    let allEventsData = {};
    let eventCommissionData = {};
    let currentSellerCommission = {{ $order->user ? ($order->user->commission_percentage ?? 0) : 0 }};

    // Detect buyer type based on current user role
    @php
        $currentUserRoles = $order->user ? $order->user->getRoleNames()->toArray() : [];
    @endphp
    let detectedBuyerType = @json(in_array('customer', $currentUserRoles ?? []) ? 'customer' : 'seller');
    buyerType = detectedBuyerType;

    // Set the correct radio button
    $(`input[name="buyer_type"][value="${buyerType}"]`).prop('checked', true);
    $(`input[name="buyer_type"][value="${buyerType}"]`).closest('label').addClass('active');
    $(`input[name="buyer_type"][value!="${buyerType}"]`).closest('label').removeClass('active');

    // Update label
    if (buyerType === 'seller') {
        $('#user-search-label').text('{{ __("messages.select_seller") }}');
    } else {
        $('#user-search-label').text('{{ __("messages.select_customer") }}');
    }

    // Initialize existing product rows with data attributes
    @foreach($order->orderProducts as $index => $orderProduct)
    (function() {
        const row = $('.product-row').eq({{ $index }});
        const sellingPrice = {{ $orderProduct->product->selling_price ?? 0 }};
        const taxRate = {{ $orderProduct->tax_percentage ?? 15 }};
        const priceWithoutTax = sellingPrice / (1 + (taxRate / 100));
        row.data('price-without-tax', priceWithoutTax);
        row.data('available-quantity', 9999); // Default high value for existing products
    })();
    @endforeach

    function initializeProductSearch() {
        $('.product-search').autocomplete({
            source: function(request, response) {
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

                    const sellingPrice = parseFloat(ui.item.selling_price);
                    const taxRate = parseFloat(ui.item.tax);

                    selectedRow.find('.product-id').val(productId);
                    selectedRow.find('.product-search').val(ui.item.label);
                    selectedRow.find('.product-price').val(sellingPrice.toFixed(2));
                    selectedRow.find('.product-tax').val(taxRate);

                    const priceWithoutTax = sellingPrice / (1 + (taxRate / 100));
                    selectedRow.data('price-without-tax', priceWithoutTax);

                    const warehouseInput = $('input[name="from_warehouse_id"]').closest('.form-group').find('input[type="hidden"]');
                    const warehouseId = warehouseInput.val() || warehouseInput.data('value');

                    let quantityUrl = '{{ route("products.available-quantity", ":productId") }}'.replace(':productId', productId);
                    let params = [];
                    if (warehouseId) params.push('warehouse_id=' + warehouseId);
                    if (orderNoteVoucherId) params.push('exclude_voucher_id=' + orderNoteVoucherId);
                    
                    if (params.length > 0) {
                        quantityUrl += '?' + params.join('&');
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

        newRow.find('[name]').each(function() {
            const name = $(this).attr('name');
            if (name) {
                $(this).attr('name', name.replace(/\[\d+\]/, `[${rowIdx}]`));
            }
            $(this).val('');
        });

        newRow.find('.line-total').text('0.00');
        newRow.find('.remove-product').prop('disabled', false);
        newRow.removeData('price-without-tax');
        newRow.removeData('available-quantity');

        container.append(newRow);
        rowIdx++;

        initializeProductSearch();
        attachQuantityListener(newRow);
    });

    // Remove product row
    $(document).on('click', '.remove-product:not(:disabled)', function() {
        const productRows = $('.product-row');
        if (productRows.length > 1) {
            $(this).closest('.product-row').remove();
            calculateTotals();
        } else {
            Swal.fire({
                icon: 'warning',
                title: '{{ __("messages.warning") }}',
                text: '{{ __("messages.cannot_remove_last_product") }}',
                confirmButtonText: '{{ __("messages.confirm") }}'
            });
        }
    });

    function attachQuantityListener(row) {
        row.find('.quantity-input').off('input').on('input', function() {
            const enteredQuantity = parseInt($(this).val()) || 0;
            const availableQuantity = row.data('available-quantity') || 9999;

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
    }

    // Initialize product search and quantity listeners
    initializeProductSearch();
    $('.product-row').each(function() {
        attachQuantityListener($(this));
    });

    // Update commission on load if no event but seller commission exists
    if (!currentEventId && currentSellerCommission > 0) {
        updateCommissionBox();
    }

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

        // Round both values to 2 decimals to handle floating-point precision issues
        const roundedPaidAmount = parseFloat(paidAmount.toFixed(2));
        const roundedGrandTotal = parseFloat(grandTotal.toFixed(2));

        if (roundedPaidAmount > roundedGrandTotal) {
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
            success: function(data) {
                if (data.length > 0) {
                    let html = '';
                    data.forEach(function(user) {
                        html += `<div class="p-2 border-bottom user-item" data-id="${user.id}" data-text="${user.text}" data-commission="${user.commission_percentage}" style="cursor: pointer;">
                                ${user.text}
                            </div>`;
                    });
                    dropdown.html(html).show();

                    $('.user-item').on('click', function() {
                        const id = $(this).data('id');
                        const text = $(this).data('text');
                        const commission = parseFloat($(this).data('commission')) || 0;

                        $('#user_id').val(id);
                        $('#user_search').val(text);
                        dropdown.hide();

                        // Update seller commission
                        currentSellerCommission = commission;
                        updateCommissionBox();

                        if (buyerType === 'seller') {
                            loadSellerEvents(id);
                        } else {
                            clearEvents();
                        }
                    });
                } else {
                    dropdown.html('<div class="p-2">{{ __("messages.no_results") }}</div>').show();
                }
            },
            error: function(xhr) {
                console.error('Error searching users:', xhr);
            }
        });
    }

    // Update label and clear search when buyer type changes
    $(document).on('change', 'input[name="buyer_type"]', function() {
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

    $('#user_search').on('focus', function() {
        const term = $(this).val().trim();
        if (term.length === 0) {
            performUserSearch('');
        }
    });

    $('#user_search').on('input', function() {
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

    $(document).on('click', function(e) {
        if (!$(e.target).closest('#user_search, #users-dropdown').length) {
            $('#users-dropdown').hide();
        }
    });

    // Update commission box with live calculations
    const updateCommissionBox = function() {
        let eventPercentage = eventCommissionData.percentage || 0;
        let sellerPercentage = 0;

        if (buyerType === 'seller' && currentSellerCommission > 0) {
            sellerPercentage = currentSellerCommission;
        }

        let subtotal = 0;
        
        // Calculate grand total
        $('.product-row').each(function() {
            const priceWithoutTax = parseFloat($(this).data('price-without-tax')) || 0;
            const quantity = parseInt($(this).find('.quantity-input').val()) || 0;
            subtotal += priceWithoutTax * quantity;
        });

        let totalCommission = 0;
        let hasCommission = false;

        {{--
            Commission Display Logic - Handles 4 scenarios:
            1. No commissions (both event & seller = 0) → Hide all commission sections
            2. Event commission only → Show Event Commission + Total Commission
            3. Seller commission only → Show Seller Commission + Total Commission
            4. Both commissions → Show Event + Seller + Total Commission
        --}}

        // Handle Event Commission
        {{-- Scenario: User selected an event with commission_percentage > 0 --}}
        if (eventPercentage > 0) {
            const eventValue = (subtotal * eventPercentage) / 100;
            $('#event-commission-percentage').text(eventPercentage.toFixed(2));
            $('#event-commission-value').text(eventValue.toFixed(2));
            $('#event-commission-container').show();
            totalCommission += eventValue;
            hasCommission = true;
        } else {
            {{-- Hide when: no event selected, or event has no commission --}}
            $('#event-commission-container').hide();
        }

        // Handle Seller Commission
        {{-- Scenario: Buyer is a seller AND seller has commission_percentage set > 0 --}}
        if (sellerPercentage > 0) {
            const sellerValue = (subtotal * sellerPercentage) / 100;
            $('#seller-commission-percentage').text(sellerPercentage.toFixed(2));
            $('#seller-commission-value').text(sellerValue.toFixed(2));
            $('#seller-commission-container').show();
            totalCommission += sellerValue;
            hasCommission = true;
        } else {
            {{-- Hide when: buyer is customer, or seller has no commission_percentage --}}
            $('#seller-commission-container').hide();
        }

        {{--
            Total Commission Display Logic
            Shows the SUM of Event Commission + Seller Commission (if any exist)

            Scenarios where Total Commission is visible:
            1. Event only: Shows Event + Total = Event value
            2. Seller only: Shows Seller + Total = Seller value
            3. Both: Shows Event + Seller + Total = sum
        --}}
        if (hasCommission) {
            $('#total-commission-value').text(totalCommission.toFixed(2));
            $('#total-commission-container').show();
            $('#commission-divider').show();
        } else {
            {{-- Hide all commission sections when no commissions apply --}}
            $('#total-commission-container').hide();
            $('#commission-divider').hide();
        }
    };

    function loadSellerEvents(sellerId, selectedEventId = null) {
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

                        const selected = selectedEventId && event.id == selectedEventId ? 'selected' : '';
                        if (event.is_valid) {
                            options += `<option value="${event.id}" ${selected}>${event.text} <span style="color: green;">✓</span></option>`;
                            validCount++;
                        } else {
                            options += `<option value="${event.id}" ${selected} style="color: #999;">${event.text}</option>`;
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

                    // If there was a selected event, show commission rows
                    if (selectedEventId && allEventsData[selectedEventId]) {
                        const eventData = allEventsData[selectedEventId];
                        eventCommissionData = {
                            id: eventData.id,
                            percentage: parseFloat(eventData.commission_percentage)
                        };
                        updateCommissionBox();
                    } else {
                        // If no event is selected (deselected or not found), check if we should fall back to seller commission
                        updateCommissionBox();
                    }
                } else {
                    options = '<option value="">{{ __("messages.no_valid_events") }}</option>';
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

    // Clear events function
    function clearEvents() {
        $('#event_id').html('<option value="">{{ __("messages.choose_event") }}</option>');
        $('#event-info').html('');
        allEventsData = {};
        eventCommissionData = {};
        allEventsData = {};
        eventCommissionData = {};
        // clearEvents might be called when user changes. We should update commission box to hide or show seller commission.
        // But if user changed, currentSellerCommission should be valid for new user?
        // Actually clearEvents is used when changing type to Customer.
        updateCommissionBox();
    }

    $(document).on('change', '#user_id', function() {
        const userId = $(this).val();
        if (buyerType === 'seller') {
            loadSellerEvents(userId);
        } else {
            clearEvents();
        }
    });

    // Handle event selection change
    $(document).on('change', '#event_id', function() {
        const eventId = $(this).val();

        if (!eventId || !allEventsData[eventId]) {
            eventCommissionData = {};
        if (!eventId || !allEventsData[eventId]) {
            eventCommissionData = {};
            // Fallback to seller commission
            updateCommissionBox();
            return;
        }
        }

        // Get event data from stored events
        const eventData = allEventsData[eventId];
        eventCommissionData = {
            id: eventData.id,
            percentage: parseFloat(eventData.commission_percentage)
        };
        updateCommissionBox();
    });

    // Load events for current user on page load
    if (currentUserId) {
        loadSellerEvents(currentUserId, currentEventId);
    }
    
    // Fetch available quantities for existing products
    function fetchExistingQuantities() {
        const warehouseInput = $('input[name="from_warehouse_id"]').closest('.form-group').find('input[type="hidden"]');
        const warehouseId = warehouseInput.val() || warehouseInput.data('value');

        if (!warehouseId) return;

        $('.product-row').each(function() {
            const row = $(this);
            const productId = row.find('.product-id').val();
            
            if (productId) {
                 let quantityUrl = '{{ route("products.available-quantity", ":productId") }}'.replace(':productId', productId);
                let params = [];
                params.push('warehouse_id=' + warehouseId);
                if (orderNoteVoucherId) params.push('exclude_voucher_id=' + orderNoteVoucherId);
                
                quantityUrl += '?' + params.join('&');

                $.ajax({
                    url: quantityUrl,
                    method: 'GET',
                    success: function(data) {
                        row.data('available-quantity', data.available_quantity);
                        // Re-validate current quantity if there's a value
                         if (row.find('.quantity-input').val()) {
                             row.find('.quantity-input').trigger('input');
                         }
                    },
                    error: function(xhr) {
                        console.error('Error fetching available quantity for product ' + productId, xhr);
                    }
                });
            }
        });
    }

    // Call it initially
    fetchExistingQuantities();

    // Listen for warehouse changes
    $(document).on('change', 'input[name="from_warehouse_id"]', function() {
        console.log('Warehouse changed, refetching quantities...');
        fetchExistingQuantities();
    });

    // Initial calculation
    calculateTotals();
});
</script>
@endsection
