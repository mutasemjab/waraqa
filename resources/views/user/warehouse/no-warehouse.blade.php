@extends('layouts.user')

@section('title', __('messages.warehouse'))
@section('page-title', __('messages.warehouse'))

@section('content')
<div class="page-header">
    <h1 class="page-title">{{ __('messages.warehouse') }}</h1>
    <p class="page-subtitle">{{ __('messages.manage_your_warehouse') }}</p>
</div>

<div class="row">
    <div class="col-md-6 offset-md-3">
        <div class="card text-center">
            <div class="card-body py-5">
                <i class="fas fa-warehouse text-muted" style="font-size: 4rem; margin-bottom: 20px;"></i>
                <h4 class="card-title mb-3">{{ __('messages.no_warehouse') }}</h4>
                <p class="text-muted mb-4">
                    {{ __('messages.you_do_not_have_warehouse') }}
                </p>

                <form action="{{ route('user.warehouse.create') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-plus me-2"></i>{{ __('messages.create_warehouse') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
