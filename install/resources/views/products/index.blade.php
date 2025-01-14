@extends('layouts.app')

@section('content')
    <div class="container-fluid my-4">
        <div class="row">
            <div class="col-lg-12">
                <div class="card rounded-12 shadow border-0">
                    <div class="card-header d-flex align-items-center flex-wrap justify-content-between">
                        <h2 class="card-title m-0">{{ __('Products') }}</h2>
                        <div>
                            <ul class="nav nav-pills justify-content-end">
                                @hasanyrole('store|admin|root')
                                    <li class="nav-item ml-2 mr-md-0">
                                        <a href="{{ route('product.create') }}" class="btn btn-primary">{{ __('Add_New_Product') }}</a>
                                    </li>
                                @endhasanyrole
                            </ul>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="myTable">
                                <thead>
                                    <tr>
                                        <th scope="col">{{ __('Name') }}</th>
                                        @role('root|admin')
                                            <th>{{ __('Store_Name') }}</th>
                                        @endrole
                                        <th scope="col">{{ __('Thumbnail') }}</th>
                                        <th scope="col">{{ __('Variant') }}</th>
                                        <th scope="col">{{ __('Discount_Price') }}</th>
                                        <th scope="col">{{ __('Price') }}</th>
                                        <th scope="col">{{ __('Description') }}</th>
                                        @can('product.status.toggle')
                                            <th scope="col">{{ __('Status') }}</th>
                                        @endcan
                                        @role('store')
                                            <th scope="col">{{ __('Action') }}</th>
                                        @endrole
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($products as $product)
                                        <tr>
                                            <td>{{ $product->name }}</td>
                                            @role('root|admin')
                                                <td>{{ $product->store->name }}</td>
                                            @endrole
                                            <td class="py-2">
                                                <div class="thumbnail">
                                                    <img width="100%" src="{{ $product->thumbnailPath }}" alt="">
                                                </div>
                                            </td>
                                            <td>{{ $product->variant->name }}</td>
                                            <td>
                                                @if ($product->discount_price)
                                                    {{ currencyPosition($product->discount_price) }}
                                                @else
                                                    <del>{{ currencyPosition('00') }}</del>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($product->discount_price)
                                                    <del>{{ currencyPosition($product->price ? $product->price : '00') }}</del>
                                                @else
                                                    {{ currencyPosition($product->price ? $product->price : '00') }}
                                                @endif
                                            </td>
                                            <td style="min-width: 180px">
                                                {{ Str::limit( $product->description, 60, '...') }}
                                            </td>
                                            @can('product.status.toggle')
                                                <td>
                                                    <label class="switch">
                                                        <a href="{{ route('product.status.toggle', $product->id) }}">
                                                            <input type="checkbox" {{ $product->is_active ? 'checked' : '' }}>
                                                            <span class="slider round"></span>
                                                        </a>
                                                    </label>
                                                </td>
                                            @endcan
                                            @hasanyrole('store|admin|root')
                                                <td>
                                                    <a href="{{ route('product.edit', $product->id) }}"
                                                        class="btn btn-sm btn-primary">
                                                        <i class="far fa-edit"></i>
                                                    </a>
                                                </td>
                                            @endhasanyrole
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
