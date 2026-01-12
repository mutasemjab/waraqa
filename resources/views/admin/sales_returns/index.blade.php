{{-- resources/views/admin/sales_returns/index.blade.php --}}
@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>{{ __('messages.sales_returns') }}</h4>
                    <a href="{{ route('admin.sales-returns.create') }}" class="btn btn-primary">
                        {{ __('messages.add_new') }}
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>{{ __('messages.return_number') }}</th>
                                    <th>{{ __('messages.order_number') }}</th>
                                    <th>{{ __('messages.user') }}</th>
                                    <th>{{ __('messages.return_date') }}</th>
                                    <th>{{ __('messages.total') }}</th>
                                    <th>{{ __('messages.return_status') }}</th>
                                    <th>{{ __('messages.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($returns as $return)
                                <tr>
                                    <td><strong>{{ $return->number }}</strong></td>
                                    <td>{{ $return->order->number ?? 'N/A' }}</td>
                                    <td>{{ $return->user->name ?? 'N/A' }}</td>
                                    <td>{{ $return->return_date->format('M d, Y') }}</td>
                                    <td>
                                        <x-riyal-icon />
                                        {{ number_format($return->total_amount, 2) }}
                                    </td>
                                    <td>
                                        @if($return->status == 'pending')
                                            <span class="badge bg-warning">{{ __('messages.pending') }}</span>
                                        @elseif($return->status == 'approved')
                                            <span class="badge bg-success">{{ __('messages.approved') }}</span>
                                        @else
                                            <span class="badge bg-info">{{ __('messages.received') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.sales-returns.show', $return) }}" class="btn btn-sm btn-info" title="{{ __('messages.view') }}">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.sales-returns.edit', $return) }}" class="btn btn-sm btn-warning" title="{{ __('messages.edit') }}">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.sales-returns.destroy', $return) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="{{ __('messages.delete') }}"
                                                    onclick="return confirm('{{ __('messages.Delete_Confirm') }}')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        {{ __('messages.no_returns_found') }}
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{ $returns->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
