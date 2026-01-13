@extends('layouts.admin')

@section('content')
    <div class="container">
        <h2>{{ __('messages.Edit') }} {{$noteVoucher->noteVoucherType->in_out_type == 1 ? 'ادخال' : 'اخراج' }}</h2>
        <form action="{{ route('noteVouchers.update', $noteVoucher->id) }}" method="POST">
            @csrf
            @method('PUT')

            <input type="hidden" name="redirect_to" id="redirect_to" value="index">

            <button type="submit" class="btn btn-primary" onclick="setRedirect('index')">{{ __('messages.Submit') }}</button>
            <button type="submit" class="btn btn-primary" onclick="setRedirect('show')">{{ __('messages.Save_Print') }}</button>

            <input type="hidden" name="note_voucher_type_id" value="{{ $noteVoucher->note_voucher_type_id }}" class="form-control" required>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="date_note_voucher"> {{ __('messages.Date') }}</label>
                    <input type="date" name="date_note_voucher" class="form-control" value="{{ $noteVoucher->date_note_voucher ? $noteVoucher->date_note_voucher->format('Y-m-d') : '' }}" required>
                </div>
            </div>

            <!-- For Receipt Type (in_out_type = 1): Direct provider field + warehouse -->
            @if ($noteVoucher->noteVoucherType->in_out_type == 1)
                <div class="col-md-6">
                    <x-search-select
                        model="App\Models\Provider"
                        fieldName="provider_id"
                        label="provider"
                        placeholder="Search..."
                        limit="10"
                        value="{{ $noteVoucher->provider_id }}"
                        required="true"
                    />
                </div>

                <div class="col-md-6">
                    <x-search-select
                        model="App\Models\Warehouse"
                        fieldName="toWarehouse"
                        label="toWarehouse"
                        placeholder="Search..."
                        limit="10"
                        value="{{ $noteVoucher->to_warehouse_id }}"
                        required="true"
                    />
                </div>
            @else
                <!-- For Other Types: From Warehouse -->
                <div class="col-md-6">
                    <x-search-select
                        model="App\Models\Warehouse"
                        fieldName="fromWarehouse"
                        label="fromWarehouse"
                        placeholder="Search..."
                        limit="10"
                        value="{{ $noteVoucher->from_warehouse_id }}"
                        required="true"
                    />
                </div>

                @if ($noteVoucher->noteVoucherType->in_out_type == 2)
                    <!-- For Outgoing Type (in_out_type = 2): From Warehouse to Recipient -->
                    @php
                        $recipientType = 'provider'; // default
                        if ($noteVoucher->provider_id) {
                            $recipientType = 'provider';
                        } elseif ($noteVoucher->user_id) {
                            // Check user role to determine if seller or customer
                            $user = $noteVoucher->user;
                            if ($user && $user->hasRole('seller')) {
                                $recipientType = 'seller';
                            } elseif ($user && $user->hasRole('customer')) {
                                $recipientType = 'user';
                            } else {
                                $recipientType = 'seller'; // default to seller if no specific role
                            }
                        } elseif ($noteVoucher->event_id) {
                            $recipientType = 'event';
                        }
                    @endphp

                    <div class="col-md-12">
                        <div class="form-group">
                            <label>{{ __('messages.recipient_type') }}</label>
                            <div class="btn-group btn-group-toggle" data-toggle="buttons" role="group">
                                <label class="btn btn-outline-primary {{ $recipientType === 'provider' ? 'active' : '' }}">
                                    <input type="radio" name="recipient_type" value="provider" {{ $recipientType === 'provider' ? 'checked' : '' }}> {{ __('messages.provider') }}
                                </label>
                                <label class="btn btn-outline-primary {{ $recipientType === 'seller' ? 'active' : '' }}">
                                    <input type="radio" name="recipient_type" value="seller" {{ $recipientType === 'seller' ? 'checked' : '' }}> {{ __('messages.seller') }}
                                </label>
                                <label class="btn btn-outline-primary {{ $recipientType === 'user' ? 'active' : '' }}">
                                    <input type="radio" name="recipient_type" value="user" {{ $recipientType === 'user' ? 'checked' : '' }}> {{ __('messages.customer') }}
                                </label>
                                <label class="btn btn-outline-primary {{ $recipientType === 'event' ? 'active' : '' }}">
                                    <input type="radio" name="recipient_type" value="event" {{ $recipientType === 'event' ? 'checked' : '' }}> {{ __('messages.event') }}
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6" id="provider-field" style="display: {{ $recipientType === 'provider' ? 'block' : 'none' }};">
                        <x-search-select
                            model="App\Models\Provider"
                            fieldName="provider_id"
                            label="provider"
                            placeholder="Search..."
                            limit="10"
                            value="{{ $noteVoucher->provider_id }}"
                            required="false"
                        />
                    </div>

                    <div class="col-md-6" id="seller-field" style="display: {{ $recipientType === 'seller' ? 'block' : 'none' }};">
                        <x-search-select
                            model="App\Models\User"
                            fieldName="user_id"
                            label="seller"
                            placeholder="Search..."
                            limit="10"
                            displayColumn="name"
                            filter="with_role:seller"
                            value="{{ $noteVoucher->user_id }}"
                            required="false"
                        />
                    </div>

                    <div class="col-md-6" id="user-field" style="display: {{ $recipientType === 'user' ? 'block' : 'none' }};">
                        <x-search-select
                            model="App\Models\User"
                            fieldName="user_id"
                            label="customer"
                            placeholder="Search..."
                            limit="10"
                            displayColumn="name"
                            filter="with_role:customer"
                            value="{{ $noteVoucher->user_id }}"
                            required="false"
                        />
                    </div>

                    <div class="col-md-6" id="event-field" style="display: {{ $recipientType === 'event' ? 'block' : 'none' }};">
                        <x-search-select
                            model="App\Models\Event"
                            fieldName="event_id"
                            label="event"
                            placeholder="Search..."
                            limit="10"
                            value="{{ $noteVoucher->event_id }}"
                            required="false"
                        />
                    </div>
                @elseif ($noteVoucher->noteVoucherType->in_out_type == 3)
                    <!-- For Transfer Type (in_out_type = 3): From Warehouse to Warehouse -->
                    <div class="col-md-6">
                        <x-search-select
                            model="App\Models\Warehouse"
                            fieldName="toWarehouse"
                            label="toWarehouse"
                            placeholder="Search..."
                            limit="10"
                            value="{{ $noteVoucher->to_warehouse_id }}"
                            required="true"
                        />
                    </div>
                @endif
            @endif




            <div class="col-md-6">
                <div class="form-group">
                    <label for="note">{{ __('messages.Note') }}</label>
                    <textarea name="note" class="form-control">{{ $noteVoucher->note }}</textarea>
                </div>
            </div>

            <br>
            <table class="table table-bordered" id="products_table">
                <thead>
                    <tr>
                        <th>{{ __('messages.product') }}</th>
                        <th>{{ __('messages.quantity') }}</th>
                        <th>{{ __('messages.unit_price') }}</th>
                        <th>{{ __('messages.tax') }}</th>
                        @if($noteVoucher->noteVoucherType->have_price == 1)
                            <th>{{ __('messages.purchasing_Price') }}</th>
                        @endif
                        <th>{{ __('messages.Note') }}</th>
                        <th>{{ __('messages.Action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($noteVoucher->voucherProducts as $key => $voucherProduct)
                        <tr>
                            <td>
                                <input type="hidden" class="form-control product-id" name="products[{{ $key }}][product_id]" value="{{ $voucherProduct->product_id }}" />
                                <input type="text" class="form-control product-search" name="products[{{ $key }}][name]" value="{{ $voucherProduct->product ? $voucherProduct->product->name_ar : '' }}" />
                            </td>
                            <td><input type="number" class="form-control product-quantity" name="products[{{ $key }}][quantity]" value="{{ $voucherProduct->quantity }}" /></td>
                            <td><input type="number" class="form-control product-price" name="products[{{ $key }}][price]" value="{{ $voucherProduct->purchasing_price }}" step="any" /></td>
                            <td><input type="number" class="form-control product-tax" name="products[{{ $key }}][tax]" value="{{ $voucherProduct->tax_percentage }}" step="any" /></td>
                            @if($noteVoucher->noteVoucherType->have_price == 1)
                                <td><input type="number" class="form-control product-purchasing-price" name="products[{{ $key }}][purchasing_price]" value="{{ $voucherProduct->purchasing_price }}" step="any"/></td>
                            @endif
                            <td><input type="text" class="form-control" name="products[{{ $key }}][note]" value="{{ $voucherProduct->note ?? '' }}" /></td>
                            <td><button type="button" class="btn btn-danger remove-row">{{ __('messages.Delete') }}</button></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">{{ __('messages.no_data') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <button type="button" class="btn btn-primary" id="add_row">{{ __('messages.Add_Row') }}</button>

        </form>
    </div>
@endsection

@section('js')

<script type="text/javascript">
    function setRedirect(value) {
        document.getElementById('redirect_to').value = value;
    }

    $(document).ready(function() {
        let rowIdx = {{ $noteVoucher->voucherProducts->count() }};

        function initializeProductSearch() {
            $('.product-search').autocomplete({
                source: function(request, response) {
                    // Validation: Check if warehouse is selected for outgoing and transfer vouchers
                    const noteVoucherTypeId = {{ $noteVoucher->noteVoucherType->in_out_type }};
                    if ((noteVoucherTypeId === 2 || noteVoucherTypeId === 3)) {
                        const warehouseInput = $('input[name="fromWarehouse"]').closest('.col-md-6').find('input[type="hidden"]');
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
                    }

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
                                        price: item.selling_price,
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
                minLength: 2,
                select: function(event, ui) {
                    if (ui.item.value === '') {
                        event.preventDefault();
                    } else {
                        const selectedRow = $(this).closest('tr');

                        // Fill product details
                        selectedRow.find('.product-id').val(ui.item.id);
                        selectedRow.find('.product-price').val(ui.item.selling_price);
                        selectedRow.find('.product-tax').val(ui.item.tax);

                        // Fetch available quantity
                        let url = '{{ route("products.available-quantity", ":productId") }}'.replace(':productId', ui.item.id);
                        let queryParams = [];

                        // If this is an outgoing or transfer voucher, add warehouse_id filter
                        const noteVoucherTypeId = {{ $noteVoucher->noteVoucherType->in_out_type }};
                        if ((noteVoucherTypeId === 2 || noteVoucherTypeId === 3)) {
                            const warehouseInput = $('input[name="fromWarehouse"]').closest('.col-md-6').find('input[type="hidden"]');
                            const warehouseId = warehouseInput.val() || warehouseInput.data('value');
                            if (warehouseId) {
                                queryParams.push('warehouse_id=' + warehouseId);
                            }
                        }

                        // Always exclude current voucher when editing to get correct available quantity
                        queryParams.push('exclude_voucher_id={{ $noteVoucher->id }}');

                        if (queryParams.length > 0) {
                            url += '?' + queryParams.join('&');
                        }

                        $.ajax({
                            url: url,
                            method: 'GET',
                            success: function(data) {
                                console.log('Available quantity fetched:', data.available_quantity);
                                selectedRow.data('available-quantity', data.available_quantity);
                                attachQuantityListener(selectedRow);
                            },
                            error: function(xhr) {
                                console.error('Error fetching available quantity:', xhr);
                            }
                        });

                        return false; // Prevent default behavior
                    }
                }
            });
        }

        function attachQuantityListener(row) {
            // Only validate quantity for outgoing (type 2) and transfer (type 3) vouchers
            const noteVoucherTypeId = {{ $noteVoucher->noteVoucherType->in_out_type }};

            if (noteVoucherTypeId !== 2 && noteVoucherTypeId !== 3) {
                return; // Don't validate for receipt type (type 1)
            }

            const quantityInput = row.find('.product-quantity');

            // Remove existing listener using namespace
            quantityInput.off('input.quantity-validation');

            // Attach new listener with namespace
            quantityInput.on('input.quantity-validation', function() {
                const enteredQuantity = parseInt($(this).val()) || 0;
                const availableQuantity = row.data('available-quantity');

                if (availableQuantity === undefined) {
                    Swal.fire({
                        icon: 'warning',
                        title: '{{ __("messages.warning") }}',
                        text: '{{ __("messages.select_warehouse_first") }}',
                        confirmButtonText: '{{ __("messages.confirm") }}'
                    });
                    $(this).val('');
                    return;
                }

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
            });
        }

        function handleBarcodeInput() {
            $(document).on('keypress', '.barcode-input', function(e) {
                if (e.which === 13) { // Enter key
                    const barcode = $(this).val();
                    const row = $(this).closest('tr');

                    // Fetch product details using the barcode
                    $.ajax({
                        url: '{{ route("products.search") }}', // Reuse the same search endpoint
                        method: 'GET',
                        data: { term: barcode },
                        success: function(products) {
                            if (products.length > 0) {
                                const product = products[0]; // Assume the first matching product is correct
                                row.find('.product-search').val(product.name); // Set product name
                                const unitDropdown = row.find('.product-unit');
                                unitDropdown.empty();

                                // Add main unit
                                if (product.unit) {
                                    unitDropdown.append(`<option value="${product.unit.id}">${product.unit.name}</option>`);
                                }

                                // Add other units
                                if (product.units) {
                                    $.each(product.units, function(index, unit) {
                                        unitDropdown.append(`<option value="${unit.id}">${unit.name}</option>`);
                                    });
                                }

                                row.find('.barcode-input').val(''); // Clear the barcode input for the next scan
                            } else {
                                alert('{{ __('messages.Product not found') }}');
                                row.find('.barcode-input').val(''); // Clear the barcode input for retry
                            }
                        },
                        error: function() {
                            alert('{{ __('messages.Error fetching product') }}');
                            row.find('.barcode-input').val(''); // Clear the barcode input
                        }
                    });

                    e.preventDefault(); // Prevent form submission
                }
            });
        }

        $('#add_row').on('click', function() {
            let newRowHtml = `
                <tr>
                    <td>
                        <input type="hidden" class="form-control product-id" name="products[${rowIdx}][product_id]" />
                        <input type="text" class="form-control product-search" name="products[${rowIdx}][name]" placeholder="{{ __('messages.Search_product') }}"/>
                    </td>
                    <td><input type="number" class="form-control product-quantity" name="products[${rowIdx}][quantity]" /></td>
                    <td><input type="number" class="form-control product-price" name="products[${rowIdx}][price]" step="any" /></td>
                    <td><input type="number" class="form-control product-tax" name="products[${rowIdx}][tax]" step="any" /></td>
            `;

            @if($noteVoucher->noteVoucherType->have_price == 1)
                newRowHtml += `<td><input type="number" class="form-control product-purchasing-price" name="products[${rowIdx}][purchasing_price]" step="any" /></td>`;
            @endif

            newRowHtml += `
                    <td><input type="text" class="form-control" name="products[${rowIdx}][note]" /></td>
                    <td><button type="button" class="btn btn-danger remove-row">{{ __('messages.Delete') }}</button></td>
                </tr>
            `;

            const newRow = $(newRowHtml);
            $('#products_table tbody').append(newRow);
            rowIdx++;
            initializeProductSearch();
            attachQuantityListener(newRow);
        });

        $(document).on('click', '.remove-row', function() {
            $(this).closest('tr').remove();
        });

        // Initialize all rows
        initializeProductSearch();

        // For existing rows, fetch available quantity from API
        $('#products_table tbody tr').each(function() {
            const row = $(this);
            const productId = row.find('.product-id').val();

            if (productId) {
                // Fetch available quantity for existing products
                let url = '{{ route("products.available-quantity", ":productId") }}'.replace(':productId', productId);
                let queryParams = [];

                // If this is an outgoing or transfer voucher, add warehouse_id filter
                const noteVoucherTypeId = {{ $noteVoucher->noteVoucherType->in_out_type }};
                if ((noteVoucherTypeId === 2 || noteVoucherTypeId === 3)) {
                    const warehouseInput = $('input[name="fromWarehouse"]').closest('.col-md-6').find('input[type="hidden"]');
                    const warehouseId = warehouseInput.val() || warehouseInput.data('value');
                    if (warehouseId) {
                        queryParams.push('warehouse_id=' + warehouseId);
                    }
                }

                // Always exclude current voucher when editing to get correct available quantity
                queryParams.push('exclude_voucher_id={{ $noteVoucher->id }}');

                if (queryParams.length > 0) {
                    url += '?' + queryParams.join('&');
                }

                $.ajax({
                    url: url,
                    method: 'GET',
                    success: function(data) {
                        row.data('available-quantity', data.available_quantity);
                        attachQuantityListener(row);
                    },
                    error: function(xhr) {
                        console.error('Error fetching available quantity:', xhr);
                    }
                });
            } else {
                attachQuantityListener(row);
            }
        });
        handleBarcodeInput();

        // Handle recipient type toggle
        $('input[name="recipient_type"]').on('change', function() {
            const selectedType = $(this).val();

            // Hide all fields and remove required from their inputs
            $('#provider-field, #seller-field, #user-field, #event-field').hide()
                .find('input[type="text"]').removeAttr('required');

            // Show selected field and add required to its text input
            let selectedField = null;
            if (selectedType === 'provider') {
                selectedField = $('#provider-field');
            } else if (selectedType === 'seller') {
                selectedField = $('#seller-field');
            } else if (selectedType === 'user') {
                selectedField = $('#user-field');
            } else if (selectedType === 'event') {
                selectedField = $('#event-field');
            }

            if (selectedField) {
                selectedField.show().find('input[type="text"]').attr('required', 'required');
            }
        });

        // Initialize: remove required from hidden fields on page load
        $('#provider-field:hidden, #seller-field:hidden, #user-field:hidden, #event-field:hidden')
            .find('input[type="text"]').removeAttr('required');
    });
</script>

@endsection
