@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>{{ __('messages.countries') }}</h4>
                    <a href="{{ route('countries.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> {{ __('messages.add_country') }}
                    </a>
                </div>

                <div class="card-body">
                   

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>{{ __('messages.id') }}</th>
                                    <th>{{ __('messages.country_name') }}</th>
                                    <th>{{ __('messages.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($countries as $country)
                                    <tr>
                                        <td>{{ $country->id }}</td>
                                        <td>
                                            <strong>{{ $country->name_ar }}</strong>
                                        </td>
                                   
                                        <td>
                                            <div class="btn-group" role="group">
                                               
                                                <a href="{{ route('countries.edit', $country) }}" 
                                                   class="btn btn-sm btn-warning" title="{{ __('messages.edit') }}">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                               
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">{{ __('messages.no_countries_found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $countries->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection