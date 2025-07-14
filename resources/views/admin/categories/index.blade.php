@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h4>{{ __('messages.Categories') }}</h4>
                    <a href="{{ route('categories.create') }}" class="btn btn-primary">
                        {{ __('messages.Add_Category') }}
                    </a>
                </div>
                <div class="card-body">
                 

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>{{ __('messages.ID') }}</th>
                                    <th>{{ __('messages.Name_English') }}</th>
                                    <th>{{ __('messages.Name_Arabic') }}</th>
                                    <th>{{ __('messages.Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($categories as $category)
                                    <tr>
                                        <td>{{ $category->id }}</td>
                                        <td>
                                            @if($category->category_id)
                                                <span class="text-muted">└─</span>
                                            @endif
                                            {{ $category->name_en }}
                                        </td>
                                        <td>
                                            @if($category->category_id)
                                                <span class="text-muted">└─</span>
                                            @endif
                                            {{ $category->name_ar }}
                                        </td>
                                      
                                        <td>
                                            <a href="{{ route('categories.edit', $category->id) }}" 
                                               class="btn btn-sm btn-warning">
                                                {{ __('messages.Edit') }}
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">
                                            {{ __('messages.No_Categories_Found') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection