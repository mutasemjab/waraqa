@extends('layouts.admin')



@section('content')
    <div class="container">
        <h2>
            @if($note_voucher_type->in_out_type == 1)
                {{ __('messages.in_out_type_1') }}
            @elseif($note_voucher_type->in_out_type == 2)
                {{ __('messages.in_out_type_2') }}
            @else
                {{ __('messages.in_out_type_3') }}
            @endif
        </h2>
        <form action="{{ route('noteVouchers.store') }}" method="POST">
            @csrf

            <input type="hidden" name="redirect_to" id="redirect_to" value="index">

            <button type="submit" class="btn btn-primary" onclick="setRedirect('index')">{{ __('messages.Submit') }}</button>
            <button type="submit" class="btn btn-primary" onclick="setRedirect('show')">{{ __('messages.Save_Print') }}</button>

            <input type="hidden" name="note_voucher_type_id" value="{{ $note_voucher_type_id }}" class="form-control" required>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="date_note_voucher"> {{ __('messages.Date') }}</label>
                    <input type="date" name="date_note_voucher" class="form-control" required>
                </div>
            </div>




            <!-- For Receipt Type (in_out_type = 1): From Recipient to Warehouse -->
            @if ($note_voucher_type->in_out_type == 1)
                <!-- Receipt Type: Direct provider field + warehouse destination -->
                <div class="col-md-6">
                    <x-search-select
                        model="App\Models\Provider"
                        fieldName="provider_id"
                        label="provider"
                        placeholder="Search..."
                        limit="10"
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
                        required="true"
                    />
                </div>

                @if ($note_voucher_type->in_out_type == 2)
                    <!-- For Outgoing Type (in_out_type = 2): From Warehouse to Recipient -->
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>{{ __('messages.recipient_type') }}</label>
                            <div class="btn-group btn-group-toggle" data-toggle="buttons" role="group">
                                <label class="btn btn-outline-primary active">
                                    <input type="radio" name="recipient_type" value="provider" checked> {{ __('messages.provider') }}
                                </label>
                                <label class="btn btn-outline-primary">
                                    <input type="radio" name="recipient_type" value="user"> {{ __('messages.customer') }}
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6" id="provider-field" style="display: block;">
                        <x-search-select
                            model="App\Models\Provider"
                            fieldName="provider_id"
                            label="provider"
                            placeholder="Search..."
                            limit="10"
                            required="true"
                        />
                    </div>

                    <div class="col-md-6" id="user-field" style="display: none;">
                        <x-search-select
                            model="App\Models\User"
                            fieldName="user_id"
                            label="customer"
                            placeholder="Search..."
                            limit="10"
                            displayColumn="name"
                            filter="with_role:customer"
                            required="false"
                        />
                    </div>

                @elseif ($note_voucher_type->in_out_type == 3)
                    <!-- For Transfer Type (in_out_type = 3): From Warehouse to Warehouse -->
                    <div class="col-md-6">
                        <x-search-select
                            model="App\Models\Warehouse"
                            fieldName="toWarehouse"
                            label="toWarehouse"
                            placeholder="Search..."
                            limit="10"
                            required="true"
                            excludeField="fromWarehouse"
                        />
                    </div>
                @endif
            @endif


         



            <div class="col-md-6">
                <div class="form-group">
                    <label for="note">{{ __('messages.Note') }}</label>
                    <textarea name="note" class="form-control"></textarea>
                </div>
            </div>

<br>
            <table class="table table-bordered" id="products_table">
                <thead>
                    <tr>
                        <th>{{ __('messages.product') }}</th>
                        <th>{{ __('messages.quantity') }}</th>
                        <th>{{ __('messages.unit_price') }} ({{ __('messages.tax_inclusive') }})</th>
                        <th>{{ __('messages.tax') }}</th>
                        @if($note_voucher_type->have_price == 1)
                            <th>{{ __('messages.purchasing_Price') }}</th>
                        @endif
                        <th>{{ __('messages.Note') }}</th>
                        <th>{{ __('messages.Action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <input type="hidden" class="form-control product-id" name="products[0][product_id]" />
                            <input type="text" class="form-control product-search" name="products[0][name]" placeholder="{{ __('messages.Search_product') }}"/>
                        </td>
                        <td><input type="number" class="form-control product-quantity" name="products[0][quantity]" /></td>
                        <td><input type="number" class="form-control product-price" name="products[0][price]" step="any" /></td>
                        <td><input type="number" class="form-control product-tax" name="products[0][tax]" step="any" /></td>
                        @if($note_voucher_type->have_price == 1)
                            <td><input type="number" class="form-control product-purchasing-price" name="products[0][purchasing_price]" step="any" /></td>
                        @endif
                        <td><input type="text" class="form-control" name="products[0][note]" /></td>
                        <td><button type="button" class="btn btn-danger remove-row">{{ __('messages.Delete') }}</button></td>
                    </tr>
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
    let rowIdx = 1;

    function initializeProductSearch() {
        $('.product-search').autocomplete({
            source: function(request, response) {
                const noteVoucherTypeId = {{ $note_voucher_type->in_out_type }};
                let warehouseId = null;

                // Determine which warehouse to check based on voucher type
                if (noteVoucherTypeId === 1) { // Entry: Check To Warehouse
                     const warehouseInput = $('#toWarehouse');
                     warehouseId = warehouseInput.val();
                } else { // Exit/Transfer: Check From Warehouse
                     const warehouseInput = $('#fromWarehouse');
                     warehouseId = warehouseInput.val();
                }

                // Warn if warehouse is not selected (For all types to be safe, or just 2/3)
                // We enforce checking warehouse for correct stock visibility
                if (!warehouseId) {
                     // Only strictly block search for internal/outgoing. For entry, maybe optional? 
                     // But user wants "available quantity" checks, so we need a warehouse.
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
                    selectedRow.find('.product-search').val(ui.item.label);
                    selectedRow.find('.product-price').val(ui.item.price);
                    selectedRow.find('.product-tax').val(ui.item.tax);

                    // Fetch available quantity
                    let url = '{{ route("products.available-quantity", ":productId") }}'.replace(':productId', ui.item.id);

                    const noteVoucherTypeId = {{ $note_voucher_type->in_out_type }};
                    let warehouseId = null;

                    if (noteVoucherTypeId === 1) {
                         const warehouseInput = $('#toWarehouse');
                         warehouseId = warehouseInput.val();
                    } else {
                         const warehouseInput = $('#fromWarehouse');
                         warehouseId = warehouseInput.val();
                    }

                    if (warehouseId) {
                        url += '?warehouse_id=' + warehouseId;
                    }

                    $.ajax({
                        url: url,
                        method: 'GET',
                        success: function(data) {
                            console.log('Available quantity fetched:', data.available_quantity);
                            selectedRow.data('available-quantity', data.available_quantity);
                            
                            // Visual feedback for available quantity
                            // selectedRow.find('.product-quantity').attr('placeholder', 'Max: ' + data.available_quantity);
                            
                            attachQuantityListener(selectedRow);
                        },
                        error: function(xhr) {
                            console.error('Error fetching available quantity:', xhr);
                        }
                    });

                    return false;
                }
            }
        });
    }

    function attachQuantityListener(row) {
        const noteVoucherTypeId = {{ $note_voucher_type->in_out_type }};
        const quantityInput = row.find('.product-quantity');

        // Remove existing listener
        quantityInput.off('input.quantity-validation');

        // Attach new listener
        quantityInput.on('input.quantity-validation', function() {
            const enteredQuantity = parseFloat($(this).val()) || 0;
            const availableQuantity = parseFloat(row.data('available-quantity'));

            if (isNaN(availableQuantity)) {
                 // Optimization: Only warn if we really expect an available quantity (i.e. product selected)
                 // For now, silent return or console log
                 return;
            }

            // Validation Logic
            // We STRICTLY enforce quantity check for Type 2 (Exit) and Type 3 (Transfer)
            // For Type 1 (Entry), checking "Entered < Available" is illogical (we are adding stock), 
            // so we skip the block, unless specifically asked otherwise.
            
            if (noteVoucherTypeId === 2 || noteVoucherTypeId === 3) {
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
                            alert('{{ __('messages.Product_not_found') }}');
                            row.find('.barcode-input').val(''); // Clear the barcode input for retry
                        }
                    },
                    error: function() {
                        alert('{{ __('messages.Error_fetching_product') }}');
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

        @if($note_voucher_type->have_price == 1)
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

    // Initialize first row
    initializeProductSearch();
    attachQuantityListener($('tr').first());
    handleBarcodeInput();

    // Handle recipient type toggle
    $('input[name="recipient_type"]').on('change', function() {
        const selectedType = $(this).val();

        // Hide all fields and remove required from their inputs
        $('#provider-field, #user-field').hide()
            .find('input[type="text"]').removeAttr('required');

        // Show selected field and add required to its text input
        let selectedField = null;
        if (selectedType === 'provider') {
            selectedField = $('#provider-field');
        } else if (selectedType === 'user') {
            selectedField = $('#user-field');
        }

        if (selectedField) {
            selectedField.show().find('input[type="text"]').attr('required', 'required');
        }
    });

    // Initialize: remove required from hidden fields on page load
    $('#user-field').find('input[type="text"]').removeAttr('required');
});


</script>

@endsection