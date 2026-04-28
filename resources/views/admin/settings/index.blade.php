@extends('layouts.admin')

@section('title', __('messages.general_settings'))

@section('content')
<div class="container-fluid">
    @if ($message = Session::get('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
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
                    <h3 class="card-title">{{ __('messages.general_settings') }}</h3>
                </div>

                <div class="card-body">
                    @if ($data->count())
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>{{ __('messages.Key') }}</th>
                                        <th>{{ __('messages.Value') }}</th>
                                        <th>{{ __('messages.Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data as $setting)
                                        <tr>
                                            <td>
                                                <strong>{{ $setting->key }}</strong>
                                            </td>
                                            <td>
                                                <code>{{ Str::limit($setting->value, 50) }}</code>
                                            </td>
                                            <td>
                                                <a href="{{ route('settings.edit', $setting->id) }}"
                                                   class="btn btn-primary btn-sm">
                                                    <i class="fas fa-edit"></i> {{ __('messages.Edit') }}
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $data->links() }}
                        </div>
                    @else
                        <div class="alert alert-info">
                            {{ __('messages.No_Data') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
