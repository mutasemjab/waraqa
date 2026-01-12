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
                                @can('purchase-edit')
                                    @if ($purchase->status === 'pending')
                                        <a href="{{ route('purchases.edit', $purchase) }}" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i> {{ __('messages.Edit') }}
                                        </a>
                                    @endif
                                @endcan
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
                                            @if ($purchase->expected_delivery_date)
                                                {{ \Carbon\Carbon::parse($purchase->expected_delivery_date)->format('Y-m-d') }}
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
                                                <th>{{ __('messages.Unit_Price') }}</th>
                                                <th>{{ __('messages.Tax') }} %</th>
                                                <th>{{ __('messages.total_price') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($purchase->items as $item)
                                                <tr>
                                                    <td>
                                                        @if ($item->product)
                                                            {{ app()->getLocale() === 'ar' ? $item->product->name_ar : $item->product->name_en }}
                                                        @else
                                                            <span class="text-muted">{{ __('messages.Product_Deleted') }}</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $item->quantity }}</td>
                                                    <td>{{ number_format($item->unit_price, 2) }}</td>
                                                    <td>{{ number_format($item->tax_percentage, 2) }}</td>
                                                    <td><strong>{{ number_format($item->total_price, 2) }}</strong></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr class="table-dark">
                                                <td colspan="4" class="text-right"><strong>{{ __('messages.total_amount') }}:</strong></td>
                                                <td><strong>{{ number_format($purchase->items->sum('total_price'), 2) }}</strong></td>
                                            </tr>
                                            @if ($purchase->total_tax)
                                                <tr class="table-light">
                                                    <td colspan="4" class="text-right"><strong>{{ __('messages.Total_Tax') }}:</strong></td>
                                                    <td><strong>{{ number_format($purchase->total_tax, 2) }}</strong></td>
                                                </tr>
                                            @endif
                                        </tfoot>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    {{ __('messages.No_Items') }}
                                </div>
                            @endif
                        </div>

                        <div class="card-footer">
                            @can('purchase-confirm')
                                @if ($purchase->status === 'pending' && $purchase->items->count() > 0)
                                    <form action="{{ route('purchases.confirm', $purchase) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-success" onclick="return confirm('{{ __('messages.are_you_sure') }}');">
                                            <i class="fas fa-check"></i> {{ __('messages.Confirm_Purchase') }}
                                        </button>
                                    </form>
                                @endif
                            @endcan

                            @can('purchase-receive')
                                @if ($purchase->status === 'confirmed')
                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#receiveModal">
                                        <i class="fas fa-download"></i> {{ __('messages.Mark_as_Received') }}
                                    </button>
                                @endif
                            @endcan

                            @can('purchase-delete')
                                @if ($purchase->status === 'pending')
                                    <form action="{{ route('purchases.destroy', $purchase) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('{{ __('messages.Delete_Confirm') }}');">
                                            <i class="fas fa-trash"></i> {{ __('messages.Delete') }}
                                        </button>
                                    </form>
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
