@extends('layouts.app')

@section('content')
    <div class="container-fluid my-4 my-md-0">
        <div class="row h-100vh">
            <div class="col-lg-8 col-md-10 m-auto">
                <div class="card rounded-12 border-0 shadow">
                    <div
                        class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2 py-3">
                        <h2 class="card-title m-0">{{ __('Create').' '. __('Admin') }}</h2>
                        <a href="{{ route('admin.index') }}" class="btn btn-danger">{{ __('Back') }}</a>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('admin.store') }}" method="POST"> @csrf
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <label for="">{{ __('First_Name') }}</label>
                                    <input type="text" name="first_name"
                                        class="form-control @error('first_name') is-invalid @enderror"
                                        placeholder="First Name ...">
                                    @error('first_name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-6 mb-3">
                                    <label for="">{{ __('Last_Name') }}</label>
                                    <input type="text" name="last_name"
                                        class="form-control @error('last_name') is-invalid @enderror"
                                        placeholder="Last Name ...">
                                    @error('last_name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-6 mb-3">
                                    <label for="">{{ __('Email') }}</label>
                                    <input type="email" name="email"
                                        class="form-control @error('email') is-invalid @enderror" placeholder="Email ...">
                                    @error('email')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-6 mb-3">
                                    <label for="">{{ __('Gender') }}</label>
                                    <select name="gender" class="form-control">
                                        <option value="">{{ __('Select') }}</option>
                                        @foreach (config('enums.ganders') as $gender)
                                            <option value="{{ $gender }}">{{ $gender }}</option>
                                        @endforeach
                                    </select>
                                    @error('gender')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-6 mb-3">
                                <label for="">{{ __('Phone_number') }}</label>
                                <input type="text" 
                                       onkeypress="onlyNumber(event)" 
                                       name="phone" 
                                       class="form-control @error('phone') is-invalid @enderror"
                                       placeholder="Phone number ..." 
                                       minlength="10"
                                       maxlength="10" 
                                       oninput="validateLength(this)">
                                @error('phone')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                                <div class="col-6 mb-3">
                                    <label for="">{{ __('Password') }}</label>
                                    <input type="text" name="password"
                                        class="form-control @error('password') is-invalid @enderror"
                                        placeholder="Password ...">
                                    @error('password')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-6 mb-3">
                                    <label for="">{{ __('Confirm_Password') }}</label>
                                    <input type="text" name="password_confirmation" class="form-control"
                                        placeholder="Confirm Password ...">
                                </div>

                                <div class="col-6 mb-3">
                                    <label for="" class="mb-1"></label>
                                    <button type="submit" class="btn btn-primary w-100 mt-1">{{ __('Submit') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function onlyNumber(evt) {
            var chars = String.fromCharCode(evt.which);
            if (!(/[0-9]/.test(chars))) {
                evt.preventDefault();
            }
        }
    </script>
@endpush
