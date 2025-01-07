@extends('layouts.app')

@section('content')
<div class="container-fluid my-4 my-md-0">
    <div class="row h-100vh">
        <div class="col-lg-8 col-md-10 m-auto">
            <div class="card shadow rounded-12 border-0">
                <div class="card-header py-3 d-flex align-items-center justify-content-between flex-wrap">
                    <h3 class="card-title m-0">{{ __('Edit') }} {{ $user->name }}</h3>
                    <a href="{{ route('admin.index') }}" class="btn btn-danger">{{ __('Back') }}</a>
                </div>

                <div class="card-body">
                    <form action="{{ route('admin.update', $user->id) }}" method="POST"> @csrf @method('put')
                        <div class="row">
                            <div class="mb-3 col-6">
                                <label for="">{{ __('First_Name') }}</label>
                                <input type="text" value="{{ $user->first_name }}" name="first_name" class="form-control @error('first_name') is-invalid @enderror" placeholder="First Name ...">
                                @error('first_name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <input type="hidden" name="userId" value="{{ $user->id }}">
                            <div class="mb-3 col-6">
                                <label for="">{{ __('Last_Name') }}</label>
                                <input type="text" value="{{ $user->last_name }}" name="last_name" class="form-control @error('last_name') is-invalid @enderror" placeholder="Last Name ...">
                                @error('last_name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-3 col-6">
                                <label for="">{{ __('Email') }}</label>
                                <input type="email" value="{{ $user->email }}" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="Email ...">
                                @error('email')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-3 col-6">
                                <label for="">{{ __('Gender') }}</label>
                                <select name="gender" class="form-control">
                                    <option value="">{{ __('Select') }}</option>
                                    @foreach (config('enums.ganders') as $gender)
                                    <option value="{{ $gender }}" {{ $user->gender == $gender ? 'selected':'' }}>{{ $gender }}</option>
                                    @endforeach
                                </select>
                                @error('gender')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-3 col-6">
                                <label for="">{{ __('Phone_number') }}</label>
                                <input type="text" value="{{ $user->mobile }}" onkeypress="onlyNumber(event)" name="phone" class="form-control @error('phone') is-invalid @enderror" placeholder="Phone number ..." pattern="^\d{10}$" title="Please enter exactly 10 digits." required>
                                @error('phone')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-3 col-6">
                                <label for="">{{ __('Password') }}</label>
                                <input type="text" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Password ...">
                                @error('password')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-3 col-6">
                                <label for="">{{ __('Confirm_Password') }}</label>
                                <input type="text" name="password_confirmation" class="form-control" placeholder="Confirm Password ...">
                            </div>

                            <div class="mb-3 col-6">
                                <label for="" class="mb-1"></label>
                                <button type="submit" class="btn mt-1 btn-primary w-100">{{ __('Submit') }}</button>
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
