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




            <!-- For Receipt Type (in_out_type = 1): From Provider to Warehouse -->
            @if ($note_voucher_type->in_out_type == 1)
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
                <!-- For Other Types: From Warehouse to Warehouse -->
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

                @if ($note_voucher_type->in_out_type == 3)
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
                        <th>{{ __('messages.unit_price') }}</th>
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
                    selectedRow.find('.product-price').val(ui.item.price);
                    selectedRow.find('.product-tax').val(ui.item.tax);
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

        $('#products_table tbody').append(newRowHtml);
        rowIdx++;
        initializeProductSearch();
    });

    $(document).on('click', '.remove-row', function() {
        $(this).closest('tr').remove();
    });

    initializeProductSearch();
    handleBarcodeInput();
});


</script>

@endsection