@extends('layouts.app')

<!-- Prevents the browser (e.g., Google Chrome) from showing the Google Translate popup on this webpage. -->
<meta name="google" content="notranslate">

@section('content')
    <div class="container-fluid mt-4">
        <div class="row">
            @role('store')
                <div class="col-lg-6">
                    <div class="card shadow border-0 rounded-12">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <h3 class="m-0">{{ __('My_Services') }}</h3>
                            <a href="{{ route('additional.index') }}" class="btn btn-info my-md-0 my-1">
                                {{ __('Additional_Service') }}
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th class="py-2">{{ __('Name') }}</th>
                                            <th class="py-2">{{ __('Thumbnail') }}</th>
                                            <th class="py-2">{{ __('Description') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach (auth()->user()->store->services as $service)
                                            <tr>
                                                <td class="py-2">{{ $service->name }}</td>
                                                <td class="py-2">
                                                    <div class="thumbnail">
                                                        <img width="100%" src="{{ asset($service->thumbnailPath) }}" alt="">
                                                    </div>
                                                </td>
                                                <td class="py-2">
                                                    {!! Str::limit($service->description, 30, '...') !!}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endrole

            @role('store')
                <div class="col-lg-6">
            @else
                <div class="col-lg-12">
            @endrole
                    <div class="card shadow border-0 rounded-12">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center flex-wrap"
                            style="gap: 8px">
                            <h2 class="card-title float-left m-0">{{ __('All_Services') }}</h2>
                            <div>
                                @can('service.create')
                                    <a href="{{ route('service.create') }}" class="btn btn-primary my-md-0 my-1">{{ __('Add_New_Service') }}</a>
                                @endcan
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="myTable">
                                    <thead>
                                        <tr>
                                            <th class="py-2">{{ __('Name') }}</th>
                                            <th class="py-2">{{ __('Thumbnail') }}</th>
                                            <th class="py-2">{{ __('Description') }}</th>
                                            @can('service.status.toggle')
                                                <th class="py-2">{{ __('Status') }}</th>
                                            @endcan
                                            @canany(['service.edit'])
                                                <th style="min-width: 130px" class="py-2">{{ __('Action') }}</th>
                                            @endcanany
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($services as $service)
                                            <tr>
                                                <td class="py-2">{{ $service->name }}</td>
                                                <td class="py-2">
                                                    <div class="thumbnail">
                                                        <img width="100%" src="{{ asset($service->thumbnailPath) }}" alt="">
                                                    </div>
                                                </td>
                                                <td class="py-2">
                                                    {!! Str::limit($service->description, 30, '...') !!}
                                                </td>
                                                @can('service.status.toggle')
                                                    <td class="py-2">
                                                        <label class="switch">
                                                            <a href="{{ route('service.status.toggle', $service->id) }}">
                                                                <input {{ $service->is_active ? 'checked' : '' }}
                                                                    type="checkbox">
                                                                <span class="slider round"></span>
                                                            </a>
                                                        </label>
                                                    </td>
                                                @endcan

                                                @can('service.edit')
                                                    <td>
                                                        <a href="{{ route('service.edit', $service->id) }}"
                                                            class="btn btn-sm btn-primary">
                                                            <i class="far fa-edit"></i>
                                                        </a>
                                                    </td>
                                                @endcan
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
