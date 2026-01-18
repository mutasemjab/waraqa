@extends('layouts.admin')

@section('title', __('messages.View_Seller'))

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('messages.View_Seller') ?? 'عرض بيانات البائع' }}</h1>
        <div>
            <a href="{{ route('sellers.edit', $seller->id) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> {{ __('messages.Edit') }}
            </a>
            <a href="{{ route('sellers.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> {{ __('messages.Back_to_List') }}
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <!-- Seller Profile -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('messages.Profile') }}</h6>
                </div>
                <div class="card-body text-center">
                    @if($seller->photo)
                    <img src="{{ asset('assets/admin/uploads/' . $seller->photo) }}" alt="{{ $seller->name }}" class="img-profile rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                    @else
                    <img src="{{ asset('assets/admin/img/undraw_profile.svg') }}" alt="No Image" class="img-profile rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                    @endif
                    <h4 class="font-weight-bold">{{ $seller->name }}</h4>
                    <p class="text-muted mb-1">{{ $seller->phone }}</p>
                    @if($seller->email)
                    <p class="text-muted mb-1">{{ $seller->email }}</p>
                    @endif
                    <div class="mt-3">
                        @if($seller->activate == 1)
                        <span class="badge badge-success px-3 py-2">{{ __('messages.Active') }}</span>
                        @else
                        <span class="badge badge-danger px-3 py-2">{{ __('messages.Inactive') }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <!-- Seller Details -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('messages.Seller_Details') ?? 'بيانات البائع' }}</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th width="30%">{{ __('messages.ID') }}</th>
                                    <td>{{ $seller->id }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('messages.Name') }}</th>
                                    <td>{{ $seller->name }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('messages.Phone') }}</th>
                                    <td>{{ $seller->phone }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('messages.Email') }}</th>
                                    <td>{{ $seller->email ?? __('messages.Not_Available') }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('messages.Status') }}</th>
                                    <td>
                                        @if($seller->activate == 1)
                                        <span class="badge badge-success">{{ __('messages.Active') }}</span>
                                        @else
                                        <span class="badge badge-danger">{{ __('messages.Inactive') }}</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>{{ __('messages.Default_Commission_Percentage') }}</th>
                                    <td>
                                        @if($seller->commission_percentage)
                                        <span class="badge badge-primary">{{ $seller->commission_percentage }}%</span>
                                        @else
                                        <span class="text-muted">{{ __('messages.Not_Available') }}</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>{{ __('messages.Created_At') }}</th>
                                    <td>{{ $seller->created_at->format('Y-m-d H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('messages.Updated_At') }}</th>
                                    <td>{{ $seller->updated_at->format('Y-m-d H:i:s') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Seller Events -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('messages.Events') ?? 'الفعاليات' }}</h6>
        </div>
        <div class="card-body">
            @if($seller->events && $seller->events->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>{{ __('messages.Event_Name') ?? 'اسم الفعالية' }}</th>
                            <th>{{ __('messages.Start_Date') ?? 'تاريخ البدء' }}</th>
                            <th>{{ __('messages.End_Date') ?? 'تاريخ الانتهاء' }}</th>
                            <th>{{ __('messages.Commission_Percentage') ?? 'نسبة العمولة' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($seller->events as $event)
                        <tr>
                            <td>{{ $event->name }}</td>
                            <td>{{ \Carbon\Carbon::parse($event->start_date)->format('Y-m-d H:i') }}</td>
                            <td>{{ \Carbon\Carbon::parse($event->end_date)->format('Y-m-d H:i') }}</td>
                            <td>{{ $event->commission_percentage }}%</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-center text-muted">{{ __('messages.No_Events_Found') ?? 'لا توجد فعاليات مضافة لهذا البائع' }}</p>
            @endif
        </div>
    </div>
</div>
@endsection