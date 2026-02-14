@extends('layouts.admin')

@section('title', __('messages.Purchase_Details'))

@section('content')
<div class="container">
            @if ($message = Session::get('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>{{ $message }}</strong>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if ($message = Session::get('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>{{ $message }}</strong>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                {{ __('messages.Purchase_Number') }}: <strong>{{ $purchase->purchase_number }}</strong>
                            </h3>
                            <div class="card-tools">
                                <a href="{{ route('purchases.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-arrow-left"></i> {{ __('messages.Back_to_List') }}
                                </a>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('messages.Provider') }}</label>
                                        <p>{{ $purchase->provider->name }}</p>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('messages.Purchase_Status') }}</label>
                                        <p>
                                            @if ($purchase->status === 'pending')
                                                <span class="badge badge-warning">{{ __('messages.Pending') }}</span>
                                            @elseif ($purchase->status === 'confirmed')
                                                <span class="badge badge-info">{{ __('messages.Confirmed') }}</span>
                                            @elseif ($purchase->status === 'received')
                                                <span class="badge badge-success">{{ __('messages.Received') }}</span>
                                            @elseif ($purchase->status === 'paid')
                                                <span class="badge badge-primary">{{ __('messages.Paid') }}</span>
                                            @elseif ($purchase->status === 'rejected')
                                                <span class="badge badge-danger">{{ __('messages.Rejected') }}</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('messages.Expected_Delivery_Date') }}</label>
                                        <p>
                                            @if ($purchase->bookRequestResponse && $purchase->bookRequestResponse->expected_delivery_date)
                                                {{ $purchase->bookRequestResponse->expected_delivery_date }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('messages.Received_Date') }}</label>
                                        <p>
                                            @if ($purchase->received_date)
                                                {{ \Carbon\Carbon::parse($purchase->received_date)->format('Y-m-d') }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>{{ __('messages.notes') }}</label>
                                <p>{{ $purchase->notes ?? '-' }}</p>
                            </div>

                            <hr>

                            <h5>{{ __('messages.Items') }}</h5>

                            @if ($purchase->items->count())
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>{{ __('messages.Product') }}</th>
                                                <th>{{ __('messages.Quantity') }}</th>
                                                <th>{{ __('messages.Price') }} ({{ __('messages.tax_inclusive') }})</th>
                                                <th>{{ __('messages.Tax') }} %</th>
                                                <th>{{ __('messages.Subtotal') }}</th>
                                                <th>{{ __('messages.Tax') }}</th>
                                                <th>{{ __('messages.total_price') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($purchase->items as $item)
                                                @php
                                                    $unit_price_with_tax = $item->unit_price * (1 + $item->tax_percentage / 100);
                                                    $subtotal_without_tax = $item->total_price / (1 + $item->tax_percentage / 100);
                                                    $tax_amount = $item->total_price - $subtotal_without_tax;
                                                @endphp
                                                <tr>
                                                    <td>
                                                        @if ($item->product)
                                                            {{ app()->getLocale() === 'ar' ? $item->product->name_ar : $item->product->name_en }}
                                                        @else
                                                            <span class="text-muted">{{ __('messages.Product_Deleted') }}</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $item->quantity }}</td>
                                                    <td>{{ number_format($unit_price_with_tax, 2) }}</td>
                                                    <td>{{ number_format($item->tax_percentage, 2) }}</td>
                                                    <td>{{ number_format($subtotal_without_tax, 2) }}</td>
                                                    <td>{{ number_format($tax_amount, 2) }}</td>
                                                    <td><strong>{{ number_format($item->total_price, 2) }}</strong></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            @php
                                                $total_with_tax = $purchase->items->sum('total_price');
                                                $total_without_tax = 0;
                                                $total_tax = 0;
                                                foreach ($purchase->items as $item) {
                                                    $subtotal = $item->total_price / (1 + $item->tax_percentage / 100);
                                                    $tax = $item->total_price - $subtotal;
                                                    $total_without_tax += $subtotal;
                                                    $total_tax += $tax;
                                                }
                                            @endphp
                                            <tr class="table-dark">
                                                <td colspan="4" class="text-right"><strong>{{ __('messages.total_amount') }}:</strong></td>
                                                <td><strong>{{ number_format($total_without_tax, 2) }}</strong></td>
                                                <td><strong>{{ number_format($total_tax, 2) }}</strong></td>
                                                <td><strong>{{ number_format($total_with_tax, 2) }}</strong></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    {{ __('messages.No_Items') }}
                                </div>
                            @endif

                            <hr>

                            <h5>{{ __('messages.Responses') }}</h5>

                            @php
                                $allResponses = [];
                                foreach ($purchase->bookRequest->items ?? [] as $item) {
                                    $responses = $item->responses;
                                    $allResponses = array_merge($allResponses, $responses->toArray());
                                }
                            @endphp

                            @if (count($allResponses) > 0)
                                @foreach ($allResponses as $response)
                                    <div class="card mb-3">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0">
                                                {{ $response['provider_id'] ? \App\Models\Provider::find($response['provider_id'])->name : 'Unknown' }}
                                                @if ($response['status'] === 'pending')
                                                    <span class="badge badge-warning float-right">{{ __('messages.Pending') }}</span>
                                                @elseif ($response['status'] === 'approved')
                                                    <span class="badge badge-success float-right">{{ __('messages.Approved') }}</span>
                                                @elseif ($response['status'] === 'rejected')
                                                    <span class="badge badge-danger float-right">{{ __('messages.Rejected') }}</span>
                                                @endif
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label class="text-muted">{{ __('messages.Available_Quantity') }}</label>
                                                    <p>{{ $response['available_quantity'] }}</p>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="text-muted">{{ __('messages.Price') }}</label>
                                                    <p>{{ number_format($response['price'] ?? 0, 2, '.', '') }}</p>
                                                </div>
                                            </div>

                                            @if ($response['note'])
                                                <div class="form-group">
                                                    <label class="text-muted">{{ __('messages.notes') }}</label>
                                                    <p>{{ $response['note'] }}</p>
                                                </div>
                                            @endif

                                            @if ($response['status'] === 'pending')
                                                <hr>
                                                <h6>{{ __('messages.Products') }}</h6>
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th>{{ __('messages.Quantity') }}</th>
                                                                <th>{{ __('messages.Price') }} ({{ __('messages.tax_inclusive') }})</th>
                                                                <th>{{ __('messages.Tax') }} %</th>
                                                                <th>{{ __('messages.Subtotal') }}</th>
                                                                <th>{{ __('messages.Tax') }}</th>
                                                                <th>{{ __('messages.total_price') }}</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr data-response-id="{{ $response['id'] }}">
                                                                <td>
                                                                    <input type="number" name="quantity" class="form-control form-control-sm qty-input" data-response-id="{{ $response['id'] }}" value="{{ $response['available_quantity'] }}" max="{{ $response['available_quantity'] }}" min="1" style="width: 80px;">
                                                                </td>
                                                                <td>
                                                                    <input type="number" name="price" class="form-control form-control-sm price-input" data-response-id="{{ $response['id'] }}" value="{{ number_format($response['price'] ?? 0, 2, '.', '') }}" step="0.01" min="0" style="width: 120px;">
                                                                </td>
                                                                <td>
                                                                    {{ number_format($response['tax_percentage'] ?? 0, 2) }}%
                                                                    <input type="hidden" name="tax_percentage" class="tax-input" data-response-id="{{ $response['id'] }}" value="{{ $response['tax_percentage'] ?? 0 }}">
                                                                </td>
                                                                <td class="subtotal-display" data-response-id="{{ $response['id'] }}">{{ number_format($response['available_quantity'] * ($response['price'] / (1 + ($response['tax_percentage'] ?? 0) / 100)), 2) }}</td>
                                                                <td class="tax-amount-display" data-response-id="{{ $response['id'] }}">{{ number_format($response['available_quantity'] * ($response['price'] / (1 + ($response['tax_percentage'] ?? 0) / 100)) * (($response['tax_percentage'] ?? 0) / 100), 2) }}</td>
                                                                <td class="total-display" data-response-id="{{ $response['id'] }}"><strong>{{ number_format($response['available_quantity'] * $response['price'], 2) }}</strong></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>

                                                <div class="btn-group mt-3" role="group">
                                                    <form action="{{ route('purchases.responses.approve', $response['id']) }}" method="POST" style="display:inline;" class="approve-form" data-response-id="{{ $response['id'] }}">
                                                        @csrf
                                                        <input type="hidden" name="quantity" class="form-quantity" value="{{ $response['available_quantity'] }}">
                                                        <input type="hidden" name="price" class="form-price" value="{{ $response['price'] }}">
                                                        <input type="hidden" name="tax_percentage" class="form-tax" value="{{ $response['tax_percentage'] ?? 0 }}">
                                                        <button type="submit" class="btn btn-sm btn-success approve-btn" data-response-id="{{ $response['id'] }}">
                                                            <i class="fas fa-check"></i> {{ __('messages.Approve') }}
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('purchases.responses.reject', $response['id']) }}" method="POST" style="display:inline;">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('{{ __('messages.are_you_sure') }}');">
                                                            <i class="fas fa-times"></i> {{ __('messages.Reject') }}
                                                        </button>
                                                    </form>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach

                                <script>
                                    $(document).ready(function() {
                                        function updateCalculations(responseId) {
                                            const row = $('tr[data-response-id="' + responseId + '"]');
                                            const qty = parseFloat(row.find('.qty-input').val()) || 1;
                                            const price = parseFloat(row.find('.price-input').val()) || 0;
                                            const taxPercent = parseFloat(row.find('.tax-input').val()) || 0;

                                            const priceWithoutTax = price / (1 + taxPercent / 100);
                                            const subtotal = priceWithoutTax * qty;
                                            const taxAmount = subtotal * (taxPercent / 100);
                                            const total = subtotal + taxAmount;

                                            row.find('.subtotal-display').text(subtotal.toFixed(2));
                                            row.find('.tax-amount-display').text(taxAmount.toFixed(2));
                                            row.find('.total-display').html('<strong>' + total.toFixed(2) + '</strong>');
                                        }

                                        // تحديث الحسابات عند تغيير الكمية أو السعر
                                        $('.qty-input, .price-input').on('change input', function() {
                                            const responseId = $(this).data('response-id');
                                            updateCalculations(responseId);
                                        });

                                        // تحديث الـ hidden inputs من الـ inputs المرئية عند الإرسال
                                        $('.approve-form').on('submit', function(e) {
                                            const responseId = $(this).data('response-id');
                                            const row = $('tr[data-response-id="' + responseId + '"]');
                                            const qty = row.find('.qty-input').val();
                                            const price = row.find('.price-input').val();
                                            const tax = row.find('.tax-input').val();

                                            // تحديث قيم الـ hidden inputs
                                            $(this).find('.form-quantity').val(qty);
                                            $(this).find('.form-price').val(price);
                                            $(this).find('.form-tax').val(tax);
                                        });
                                    });
                                </script>
                            @else
                                <div class="alert alert-info">
                                    {{ __('messages.No_Responses') }}
                                </div>
                            @endif

                        </div>

                        <div class="card-footer">
                            @can('purchase-receive')
                                @if ($purchase->status === 'confirmed')
                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#receiveModal">
                                        <i class="fas fa-download"></i> {{ __('messages.Mark_as_Received') }}
                                    </button>
                                @endif
                            @endcan

                        </div>
                    </div>
                </div>
            </div>
</div>

<!-- Receive Modal -->
    <div class="modal fade" id="receiveModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('messages.Mark_as_Received') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('purchases.mark-as-received', $purchase) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="received_date">{{ __('messages.Received_Date') }} *</label>
                            <input type="date" class="form-control @error('received_date') is-invalid @enderror" id="received_date" name="received_date" required max="{{ date('Y-m-d') }}">
                            @error('received_date')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('messages.Cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('messages.Save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
