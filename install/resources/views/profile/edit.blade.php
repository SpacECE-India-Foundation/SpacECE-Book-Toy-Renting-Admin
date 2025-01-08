@extends('layouts.app')

@section('content')
    <div class="container-fluid my-4 my-md-0">
        <div class="row h-100vh align-items-center">
            <div class="col-md-8 m-auto">
                <form @can('profile.update') action="{{ route('profile.update') }}" @endcan method="POST" enctype="multipart/form-data">
                    @csrf
                <div class="card shadow rounded-12 border-0">
                    <div class="card-header py-3">
                        <h3 class="m-0">{{ __('Edit_Personal_Information') }}</h3>
                    </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('First_Name') }}</label>
                                        <input class="form-control" type="text" name="first_name"
                                            value="{{ $user->first_name }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('Last_Name') }}</label>
                                        <input class="form-control" type="text" name="last_name"
                                            value="{{ $user->last_name }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('Mobile') }}</label>
                                        <input class="form-control" type="text" name="phone"
                                            value="{{ $user->mobile }}" required pattern="^\d{10}$" title="Please enter exactly 10 digits." >
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('Email_Address') }}</label>
                                        <input class="form-control" type="text" name="email"
                                            value="{{ $user->email }}">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>{{ __('Profile_Photo') }}</label>
                                        <input class="form-control-file" type="file" name="profile_photo">
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-between py-3">
                            <a href="{{ url()->previous() }}" class="btn btn-danger">{{ __('Back') }}</a>
                            @can('profile.update')
                            <button class="btn btn-primary" type="submit">{{ __('Update') }}</button>
                            @endcan
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
@endsection
