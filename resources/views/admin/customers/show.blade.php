@extends('layouts.admin')

@section('title', __('messages.View_Customer'))

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('messages.View_Customer') ?? 'عرض بيانات العميل' }}</h1>
        <div>
            <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> {{ __('messages.Edit') }}
            </a>
            <a href="{{ route('customers.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> {{ __('messages.Back_to_List') }}
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <!-- Customer Profile -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('messages.Profile') }}</h6>
                </div>
                <div class="card-body text-center">
                    @if($customer->photo)
                    <img src="{{ asset('assets/admin/uploads/' . $customer->photo) }}" alt="{{ $customer->name }}" class="img-profile rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                    @else
                    <img src="{{ asset('assets/admin/img/undraw_profile.svg') }}" alt="No Image" class="img-profile rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                    @endif
                    <h4 class="font-weight-bold">{{ $customer->name }}</h4>
                    <p class="text-muted mb-1">{{ $customer->phone }}</p>
                    @if($customer->email)
                    <p class="text-muted mb-1">{{ $customer->email }}</p>
                    @endif
                    <div class="mt-3">
                        @if($customer->activate == 1)
                        <span class="badge badge-success px-3 py-2">{{ __('messages.Active') }}</span>
                        @else
                        <span class="badge badge-danger px-3 py-2">{{ __('messages.Inactive') }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <!-- Customer Details -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('messages.Customer_Details') ?? 'بيانات العميل' }}</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th width="30%">{{ __('messages.ID') }}</th>
                                    <td>{{ $customer->id }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('messages.Name') }}</th>
                                    <td>{{ $customer->name }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('messages.Phone') }}</th>
                                    <td>{{ $customer->phone }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('messages.Email') }}</th>
                                    <td>{{ $customer->email ?? __('messages.Not_Available') }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('messages.Status') }}</th>
                                    <td>
                                        @if($customer->activate == 1)
                                        <span class="badge badge-success">{{ __('messages.Active') }}</span>
                                        @else
                                        <span class="badge badge-danger">{{ __('messages.Inactive') }}</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>{{ __('messages.Created_At') }}</th>
                                    <td>{{ $customer->created_at->format('Y-m-d H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('messages.Updated_At') }}</th>
                                    <td>{{ $customer->updated_at->format('Y-m-d H:i:s') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
