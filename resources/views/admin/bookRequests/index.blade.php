@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h4>{{ __('messages.Book_Requests') }}</h4>
                    <a href="{{ route('bookRequests.create') }}" class="btn btn-primary">
                        {{ __('messages.Create_Request') }}
                    </a>
                </div>
                <div class="card-body">
                    @if($bookRequests->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>{{ __('messages.ID') }}</th>
                                        <th>{{ __('messages.Product') }}</th>
                                        <th>{{ __('messages.provider') }}</th>
                                        <th>{{ __('messages.Requested_Quantity') }}</th>
                                        <th>{{ __('messages.Responses') }}</th>
                                        <th>{{ __('messages.Created_Date') }}</th>
                                        <th>{{ __('messages.Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($bookRequests as $request)
                                        <tr>
                                            <td>{{ $request->id }}</td>
                                            <td>
                                                <strong>
                                                    {{ app()->getLocale() == 'ar' ? $request->product->name_ar : $request->product->name_en }}
                                                </strong>
                                            </td>
                                            <td>
                                                {{ $request->provider->name }}
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $request->requested_quantity }}</span>
                                            </td>
                                            <td>
                                                @php
                                                    $responseCount = $request->responses->count();
                                                @endphp
                                                <span class="badge bg-{{ $responseCount > 0 ? 'success' : 'warning' }}">
                                                    {{ $responseCount }}
                                                </span>
                                            </td>
                                            <td>
                                                {{ $request->created_at->format('Y-m-d H:i') }}
                                            </td>
                                            <td>
                                                <a href="{{ route('bookRequests.show', $request->id) }}"
                                                   class="btn btn-sm btn-info">
                                                    {{ __('messages.View') }}
                                                </a>
                                                <a href="{{ route('bookRequests.edit', $request->id) }}"
                                                   class="btn btn-sm btn-warning">
                                                    {{ __('messages.Edit') }}
                                                </a>
                                                <form action="{{ route('bookRequests.destroy', $request->id) }}"
                                                      method="POST"
                                                      style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger"
                                                            onclick="return confirm('{{ __('messages.Are_you_sure') }}')">
                                                        {{ __('messages.Delete') }}
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            {{ __('messages.No_Book_Requests_Found') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection