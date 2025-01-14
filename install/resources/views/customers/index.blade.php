@extends('layouts.app')

@section('content')
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow border-0 rounded-12">
                    <div class="card-header d-flex align-items-center py-3 justify-content-between">
                        <h2 class="card-title m-0">All Customers</h2>
                        <div>
                            <form action="{{ route('customer.index') }}" method="GET">
                                <ul class=" nav d-flex justify-content-end">
                                    <li class="nav-item ml-2 mr-md-0">
                                        <input type="text" name='search' placeholder="Search"
                                            value="{{ request('search') }}" class="form-control" style="height: 43px"/>
                                    </li>
                                    <li class="nav-item ml-2 mr-md-0">
                                        <button type="submit" class="btn btn-info">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </li>
                                    @can('customer.create')
                                    <li class="nav-item ml-2 mr-md-0">
                                        <a href="{{ route('customer.create') }}" class="btn btn-primary">
                                            <i class="fa fa-plus"></i> {{ __('Add_New_Customer') }}
                                        </a>
                                    </li>
                                    @endcan
                                </ul>
                            </form>
                        </div>
                    </div>
                    <div class="card-body pt-2">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="myTable">
                                <thead>
                                    <tr>
                                        <th scope="col">{{ __('Name') }}</th>
                                        <th scope="col">{{ __('Image') }}</th>
                                        <th scope="col">{{ __('Email') }}</th>
                                        <th scope="col">{{ __('Mobile') }}</th>
                                        @canany(['customer.show', 'customer.edit'])
                                        <th scope="col">{{ __('Action') }}</th>
                                        @endcanany
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($customers as $customer)
                                        <tr>
                                            <td>{{ $customer->user->name }}</td>
                                            <td class="py-2">
                                                <div class="thumbnail">
                                                    <img width="100%" src="{{ asset($customer->profilePhotoPath) }}"
                                                        alt="">
                                                </div>
                                            </td>
                                            <td>
                                                {{ $customer->user->email }}
                                            </td>
                                            <td>
                                                {{ $customer->user->mobile }}
                                            </td>
                                            @canany(['customer.show', 'customer.edit'])
                                            <td>
                                                @can('customer.show')
                                                <a href="{{ route('customer.show', $customer->id) }}"
                                                    class="btn btn-primary py-1 px-2">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                @endcan
                                                @can('customer.edit')
                                                <a href="{{ route('customer.edit', $customer->id) }}"
                                                    class="btn btn-info py-1 px-2">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                @endcan
                                            </td>
                                            @endcanany
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
    <style>
        td {
            padding: 5px 10px !important;
        }
    </style>
@endsection
@push('scripts')
    <script>
        $('.delete-confirm').on('click', function(e) {
            e.preventDefault();
            const url = $(this).attr('href');
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#00B894',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            })
        });
    </script>
@endpush
