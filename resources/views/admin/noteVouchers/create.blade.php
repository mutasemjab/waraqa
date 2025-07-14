@extends('layouts.admin')



@section('content')
    <div class="container">
        <h2>   {{ __('messages.New') }}  {{$note_voucher_type->in_out_type ==1 ? 'ادخال': 'اخراج' }}</h2>
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




            <div class="col-md-6">
                <div class="form-group mt-3">
                    <label for="warehouse">{{ __('messages.fromWarehouse') }}</label>
                    <select name="fromWarehouse" class="form-control" required>
                        @foreach ($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
           
            @if ($note_voucher_type->in_out_type ==1 )
                
         
            <div class="col-md-6">
                <div class="form-group mt-3">
                    <label for="warehouse">{{ __('messages.providers') }}</label>
                    <select name="providers" class="form-control" required>
                        @foreach ($providers as $provider)
                            <option value="{{ $provider->id }}">{{ $provider->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
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
                        <th>{{ __('messages.barcode') }}</th>
                        <th>{{ __('messages.product') }}</th>
                        <th>{{ __('messages.quantity') }}</th>
                        @if($note_voucher_type->have_price == 1)
                            <th>{{ __('messages.purchasing_Price') }}</th>
                        @endif
                        <th>{{ __('messages.Note') }}</th>
                        <th>{{ __('messages.Action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><input type="text" class="form-control barcode-input" name="products[0][barcode]" /></td>
                        <td><input type="text" class="form-control product-search" name="products[0][name]"/></td>
                   
                        <td><input type="number" class="form-control" name="products[0][quantity]" /></td>
                        @if($note_voucher_type->have_price == 1)
                            <td><input type="number" class="form-control" name="products[0][purchasing_price]" step="any" /></td>
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
                                    units: item.units,
                                    unit: item.unit,
                                    id: item.id
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
                    const unitDropdown = selectedRow.find('.product-unit');
                    unitDropdown.empty();

                    // Add main unit as the first option
                    if (ui.item.unit) {
                        unitDropdown.append(`<option value="${ui.item.unit.id}">${ui.item.unit.name}</option>`);
                    }

                    // Add other units
                    if (ui.item.units) {
                        $.each(ui.item.units, function(index, unit) {
                            unitDropdown.append(`<option value="${unit.id}">${unit.name}</option>`);
                        });
                    }
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
        $('#products_table tbody').append(`
            <tr>
                <td><input type="text" class="form-control barcode-input" name="products[${rowIdx}][barcode]" placeholder="{{ __('messages.Scan Barcode') }}" /></td>
                <td><input type="text" class="form-control product-search" name="products[${rowIdx}][name]" /></td>
            
                <td><input type="number" class="form-control" name="products[${rowIdx}][quantity]" /></td>
                @if($note_voucher_type->have_price == 1)
                <td><input type="number" class="form-control" name="products[${rowIdx}][purchasing_price]" step="any" /></td>
                @endif
                <td><input type="text" class="form-control" name="products[${rowIdx}][note]" /></td>
                <td><button type="button" class="btn btn-danger remove-row">{{ __('messages.Delete') }}</button></td>
            </tr>
        `);
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
