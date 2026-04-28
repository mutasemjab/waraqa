@extends('layouts.admin')

@section('title', __('messages.Edit') . ' ' . $data->key)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('messages.Edit') }}: <strong>{{ $data->key }}</strong></h3>
                    <div class="card-tools">
                        <a href="{{ route('settings.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> {{ __('messages.Back_to_List') }}
                        </a>
                    </div>
                </div>

                <form action="{{ route('settings.update', $data->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="card-body">
                        <div class="form-group">
                            <label>{{ __('messages.Key') }}</label>
                            <input type="text" class="form-control" value="{{ $data->key }}" disabled>
                            <small class="form-text text-muted">{{ __('messages.this_field_cannot_be_changed') }}</small>
                        </div>

                        <div class="form-group">
                            <label>{{ __('messages.Value') }} *</label>
                            <textarea
                                name="value"
                                class="form-control @error('value') is-invalid @enderror"
                                rows="6"
                                required>{{ $data->value }}</textarea>
                            @error('value')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>{{ __('messages.Last_Updated') }}</label>
                            <input type="text" class="form-control"
                                   value="{{ $data->updated_at?->format('Y-m-d H:i:s') ?? __('messages.not_updated') }}"
                                   disabled>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> {{ __('messages.Save') }}
                        </button>
                        <a href="{{ route('settings.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> {{ __('messages.Cancel') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
