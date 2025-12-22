@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h4>{{ __('messages.Products') }}</h4>
                    <a href="{{ route('products.create') }}" class="btn btn-primary">
                        {{ __('messages.Add_Product') }}
                    </a>
                </div>
                <div class="card-body">

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>{{ __('messages.Image') }}</th>
                                    <th>{{ __('messages.Name') }}</th>
                                    <th>{{ __('messages.SKU') }}</th>
                                    <th>{{ __('messages.Category') }}</th>
                                    <th>{{ __('messages.provider') }}</th>
                                    <th>{{ __('messages.Price') }}</th>
                                    <th>{{ __('messages.Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($products as $product)
                                    <tr>
                                        <td>
                                            @if($product->photo)
                                                <img src="{{ asset('assets/admin/uploads/' . $product->photo) }}" 
                                                     alt="{{ $product->name_en }}" 
                                                     class="img-thumbnail" 
                                                     style="width: 60px; height: 60px; object-fit: cover;">
                                            @else
                                                <div class="bg-light d-flex align-items-center justify-content-center" 
                                                     style="width: 60px; height: 60px;">
                                                    <small class="text-muted">{{ __('messages.No_Image') }}</small>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ app()->getLocale() == 'ar' ? $product->name_ar : $product->name_en }}</strong>
                                        </td>
                                        <td>
                                            {{ $product->sku ?? '-' }}
                                        </td>
                                        <td>
                                            @if($product->category)
                                                <strong>{{ app()->getLocale() == 'ar' ? $product->category->name_ar : $product->category->name_en }}</strong>
                                            @else
                                                <span class="text-muted">{{ __('messages.No_Category') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($product->provider)
                                                <strong>{{ $product->provider->name }}</strong>
                                            @else
                                                <span class="text-muted">{{ __('messages.No_Provider') }}</span>
                                            @endif
                                        </td>
                                       
                                        <td>{{ number_format($product->selling_price, 2) }} </td>
                                      
                                      
                                        <td>
                                            <a href="{{ route('products.edit', $product->id) }}" 
                                               class="btn btn-sm btn-warning">
                                                {{ __('messages.Edit') }}
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">
                                            {{ __('messages.No_Products_Found') }}
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