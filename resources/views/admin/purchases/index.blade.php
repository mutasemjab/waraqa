@extends('layouts.admin')

@section('title', __('messages.Purchases'))

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('messages.Purchases') }}</h1>
        @can('purchase-add')
            <a href="{{ route('purchases.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> {{ __('messages.Create_Purchase') }}
            </a>
        @endcan
    </div>

    <!-- Alerts -->
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

    <!-- Purchases Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('messages.Purchases') }}</h6>
        </div>
        <div class="card-body">
            @if ($purchases->count())
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>{{ __('messages.ID') }}</th>
                                <th>{{ __('messages.Purchase_Number') }}</th>
                                <th>{{ __('messages.Provider') }}</th>
                                <th>{{ __('messages.total_amount') }}</th>
                                <th>{{ __('messages.Total_Tax') }}</th>
                                <th>{{ __('messages.Purchase_Status') }}</th>
                                <th>{{ __('messages.Expected_Delivery_Date') }}</th>
                                <th>{{ __('messages.Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($purchases as $purchase)
                                <tr>
                                    <td>{{ $purchase->id }}</td>
                                    <td><strong>{{ $purchase->purchase_number }}</strong></td>
                                    <td>{{ $purchase->provider->name }}</td>
                                    <td>{{ number_format($purchase->total_amount, 2) }}</td>
                                    <td>{{ number_format($purchase->total_tax ?? 0, 2) }}</td>
                                    <td>
                                        @if ($purchase->status === 'pending')
                                            <span class="badge badge-warning">{{ __('messages.Pending') }}</span>
                                        @elseif ($purchase->status === 'confirmed')
                                            <span class="badge badge-info">{{ __('messages.Confirmed') }}</span>
                                        @elseif ($purchase->status === 'received')
                                            <span class="badge badge-success">{{ __('messages.Received') }}</span>
                                        @elseif ($purchase->status === 'paid')
                                            <span class="badge badge-primary">{{ __('messages.Paid') }}</span>
                                        @else
                                            <span class="badge badge-secondary">{{ $purchase->status }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($purchase->expected_delivery_date)
                                            {{ \Carbon\Carbon::parse($purchase->expected_delivery_date)->format('Y-m-d') }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('purchases.show', $purchase) }}" class="btn btn-sm btn-info" title="{{ __('messages.view') }}">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @can('purchase-edit')
                                            @if ($purchase->status === 'pending')
                                                <a href="{{ route('purchases.edit', $purchase) }}" class="btn btn-sm btn-warning" title="{{ __('messages.Edit') }}">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endif
                                        @endcan
                                        @can('purchase-confirm')
                                            @if ($purchase->status === 'pending' && $purchase->items->count() > 0)
                                                <form action="{{ route('purchases.confirm', $purchase) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success" title="{{ __('messages.Confirm_Purchase') }}" onclick="return confirm('{{ __('messages.are_you_sure') }}');">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        @endcan
                                        @can('purchase-receive')
                                            @if ($purchase->status === 'confirmed')
                                                <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#receiveModal{{ $purchase->id }}" title="{{ __('messages.Mark_as_Received') }}">
                                                    <i class="fas fa-download"></i>
                                                </button>
                                            @endif
                                        @endcan
                                        @can('purchase-delete')
                                            @if ($purchase->status === 'pending')
                                                <form action="{{ route('purchases.destroy', $purchase) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" title="{{ __('messages.Delete') }}" onclick="return confirm('{{ __('messages.Delete_Confirm') }}');">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        @endcan
                                    </td>
                                </tr>

                                <!-- Receive Modal -->
                                <div class="modal fade" id="receiveModal{{ $purchase->id }}" tabindex="-1" role="dialog">
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
                                                        <label for="received_date{{ $purchase->id }}">{{ __('messages.Received_Date') }} *</label>
                                                        <input type="date" class="form-control @error('received_date') is-invalid @enderror" id="received_date{{ $purchase->id }}" name="received_date" required max="{{ date('Y-m-d') }}">
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
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center">
                    {{ $purchases->links() }}
                </div>
            @else
                <div class="alert alert-info text-center">
                    {{ __('messages.No_Purchases_Found') }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
