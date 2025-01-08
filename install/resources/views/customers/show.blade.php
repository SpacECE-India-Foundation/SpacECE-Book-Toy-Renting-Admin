@extends('layouts.app')

@section('content')
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-lg-12">
                <div class="card rounded-12 shadow">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2 py-3">
                        <h2 class="m-0">{{ __('Customer_Details') }}</h2>
                        <div>
                            @can('customer.change.password')
                                <a href="{{ route('customer.change.password', $customer->id) }}" class="btn btn-primary">{{ __('Change_Password') }}</a>
                            @endcan
                            <a class="btn btn-danger" href="{{ url()->previous() }}"> {{ __('Back') }} </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table-bordered table-striped verticle-middle table-responsive-sm table">
                                <thead>
                                    <tr>
                                        <th scope="col">{{ __('Title') }}</th>
                                        <th scope="col">{{ __('Details') }}</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <tr>
                                        <th>{{ __('Name') }}</th>
                                        <td>{{ $customer->user->first_name ? $customer->user->name : 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Profile_Photo') }}</th>
                                        <td>
                                            <div class="thumbnail">
                                                <img width="100%" src="{{ $customer->user->profilePhotoPath }}"
                                                    alt="{{ $customer->user->name }}">
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Email') }}</th>
                                        <td>
                                            {{ $customer->user->email }}
                                            @if ($customer->user->email_verified_at)
                                                <span
                                                    class="bg-success btn px-1 py-0">{{ $customer->user->email_verified_at->format('M d, Y') }}</span>
                                            @else
                                                <span class="bg-warning btn px-1 py-0">{{ __('Unverified') }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Mobile</th>
                                        <td>
                                            {{ $customer->user->mobile }}
                                            @if ($customer->user->mobile_verified_at)
                                                <span class="bg-success btn px-1 py-0">{{ __('verified') }}</span>
                                            @else
                                                <span class="bg-warning btn px-1 py-0">{{ __('Unverified') }}</span>
                                            @endif
                                        </td>
                                    </tr>

                                    {{-- <tr>
                                        <th>{{ __('Date_of_Birth') }}</th>
                                        <td>{{ Carbon\Carbon::parse($customer->user->dob)->format('M d, Y') }}</td>
                                    </tr> --}}

                                    @if (!$customer->addresses->isEmpty())
                                        <tr>
                                            <th>{{ __('Address') }}</th>
                                            <td>
                                                @foreach ($customer->addresses as $key => $address)
                                                    <div>
                                                        {!! $key == 0 ? ' <hr class="my-2">' : '' !!}

                                                        <span>{{ $address->address_name . ', ' . $address->address_line }}</span>

                                                        <a href="#address_show_{{ $address->id }}" data-toggle="modal"
                                                            class="btn btn-info ml-2 p-1 px-2">
                                                            <i class="fa fa-eye"></i>
                                                        </a>

                                                        <hr class="my-2">
                                                        <!-- Modal -->
                                                        <div class="modal fade" id="address_show_{{ $address->id }}">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="exampleModalLabel">
                                                                            {{ $address->address_line }}</h5>
                                                                        <button type="button" class="close"
                                                                            data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <div class="table-responsive">
                                                                            <table
                                                                                class="table-bordered table-striped verticle-middle table-responsive-sm table">
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <th scope="col">{{ __('Title') }}</th>
                                                                                        <th scope="col">{{ __('Details') }}</th>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>{{ __('Address_Name') }}</td>
                                                                                        <td>{{ $address->address_name }}</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>{{ __('Road_No') }}</td>
                                                                                        <td>{{ $address->road_no }}
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>{{ __('House_No') }}</td>
                                                                                        <td>{{ $address->house_no }}
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>{{ __('Flat_No') }}</td>
                                                                                        <td>{{ $address->flat_no }}
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>{{ __('Address_line') }}</td>
                                                                                        <td>{{ $address->address_line }}
                                                                                        </td>
                                                                                    </tr>
                                                                                </tbody>
                                                                            </table>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="button" class="btn btn-secondary"
                                                                                data-dismiss="modal">{{ __('Close') }}</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                @endforeach
                                            </td>
                                        </tr>
                                    @endif

                                    @if (!$customer->orders->isEmpty())
                                        <tr>
                                            <th>{{ __('Orders') }}</th>
                                            <td>
                                                <!-- Button trigger modal -->
                                                <button type="button" class="btn btn-primary" data-toggle="modal"
                                                    data-target="#staticBackdrop">
                                                    {{ __('Show_all_Orders') }}
                                                </button>

                                                <!-- Modal -->
                                                <div class="modal fade" id="staticBackdrop">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="staticBackdropLabel">
                                                                    {{ __('Orders') }}
                                                                </h5>
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                @foreach ($customer->orders as $key => $order)
                                                                    <div class="position-relative">
                                                                        {!! $key == 0 ? ' <hr class="my-2">' : '' !!}
                                                                        <span>{{ __('Delivery_Date') }}
                                                                            {{ Carbon\Carbon::parse($order->delivery_at)->format('M d, Y') }}</span>,
                                                                        <span>{{ __('Products') }}:
                                                                            {{ $order->products->count() }}</span>
                                                                        <a href="{{ route('order.show', $order->id) }}"
                                                                            class="btn btn-info position-absolute ml-2 p-1 px-2"
                                                                            style="right:0; bottom:5px">
                                                                            <i class="fa fa-eye"></i>
                                                                        </a>
                                                                        <hr class="my-2">
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-dark"
                                                                    data-dismiss="modal">{{ __('Close') }}</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endif

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
