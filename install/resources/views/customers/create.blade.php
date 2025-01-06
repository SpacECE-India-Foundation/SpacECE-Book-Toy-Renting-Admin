@extends('layouts.app')

@section('content')
    <div class="container-fluid my-4 my-md-0">
        <div class="row h-100vh">
            <div class="col-lg-8 col-md-10 m-auto">
                <div class="card rounded-12 border-0 shadow">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2 py-3">
                        <h2 class="m-0">{{ __('Add_New_Customer') }}</h2>
                        <a class="btn btn-danger" href="{{ route('customer.index') }}"> {{ __('Back') }} </a>
                    </div>
                    <div class="card-body">
                        <form @can('customer.store') action="{{ route('customer.store') }}" @endcan method="POST"
                            enctype="multipart/form-data"> @csrf
                            <div class="row">
                                <div class="col-12 col-md-6 mb-2">
                                    <label for="">{{ __('First_Name') }} <strong class="text-danger">*</strong></label>
                                    <input type="text" class="form-control" name="first_name"
                                        value="{{ old('first_name') }}" placeholder="First name">
                                    @error('first_name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-12 col-md-6 mb-2">
                                    <label for="">{{ __('Last_Name') }}</label>
                                    <input type="text" class="form-control" name="last_name"
                                        value="{{ old('last_name') }}" placeholder="Last name">
                                    @error('last_name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-12 col-md-6 mb-2">
                                    <label for="">{{ __('Mobile_number') }} <strong class="text-danger">*</strong></label>
                                    <input type="text" class="form-control" name="mobile" value="{{ old('mobile') }}"
                                        placeholder="Mobile">
                                    @error('mobile')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                    <input type="hidden" name="type" value="mobile">
                                </div>
                                <div class="col-12 col-md-6 mb-2">
                                    <label for="">{{ __('Email') }}</label>
                                    <input type="email" class="form-control" name="email" value="{{ old('email') }}"
                                        placeholder="Email">
                                    @error('email')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-12 col-md-6 mb-2">
                                    <label for="">{{ __('Password') }} <strong class="text-danger">*</strong></label>
                                    <div class="d-flex align-items-center inputBox">
                                        <div class="input w-100 position-relative">
                                            <input type="password" id="password" class="form-control" name="password"
                                                placeholder="******" pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#])[A-Za-z\d@$!%*?&#]{8,}$" 
                title="Password must be at least 8 characters long and include uppercase, lowercase, a number, and a special character." required>
                                            <span class="eye" onclick="myFunction()">
                                                <i class="fa fa-eye-slash"></i>
                                            </span>
                                        </div>
                                    </div>
                                    @error('password')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-12 col-md-6 mb-2">
                                    <label for="">{{ __('Confirm_Password') }}</label>
                                    <div class="d-flex align-items-center inputBox">
                                        <div class="input w-100 position-relative">
                                            <input type="password" class="form-control" name="password_confirmation"
                                                placeholder="******" id="confirmPassword">
                                            <span class="eye" onclick="confirmPassword()">
                                                <i class="fa fa-eye-slash"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 mb-2 py-2">
                                    <label for="">{{ __('Customer_Photo') }}</label>
                                    <input type="file" class="form-control-file" name="profile_photo">
                                </div>

                                @can('customer.store')
                                    <div class="col-12 col-md-6 mb-2 py-2">
                                        <label for=""></label>
                                        <button class="btn btn-primary w-100 mt-2">{{ __('Submit') }}</button>
                                    @endcan
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
        .eye {
            position: absolute;
            right: 8px;
            top: 11px;
            cursor: pointer;
        }
    </style>
@endsection
@push('scripts')
    <script>
        function myFunction() {
            var x = document.getElementById("password");
            if (x.type === "password") {
                x.type = "text";
            } else {
                x.type = "password";
            }
        }

        function confirmPassword() {
            var x = document.getElementById("confirmPassword");
            if (x.type === "password") {
                x.type = "text";
            } else {
                x.type = "password";
            }
        }
    </script>
@endpush
