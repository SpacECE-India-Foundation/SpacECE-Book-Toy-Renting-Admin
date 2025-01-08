@extends('layouts.app')

@section('content')
<div class="container-fluid my-4">
    <div class="row">
        <div class="col-lg-9 m-auto">
            <div class="card shadow rounded-12 border-0">
                <div class="card-header py-2 d-flex align-items-center justify-content-between">
                    <h2 class="card-title m-0">{{ __('Add_New_Shop') }}</h2>
                    <a href="{{ route('shop.index') }}" class="btn btn-secondary">
                        <i class="fa fa-arrow-left"></i> {{ __('Back') }}
                    </a>
                </div>

                <div class="card-body">
                    <form action="{{ route('shop.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="section">
                            <h2 class="title"> {{ __('Shop_Owner') }}</h2>
                            <div class="row px-2">
                                <div class="col-lg-6">
                                    <label class="mb-1">{{ __('First_Name') }}</label>
                                    <x-input type="text"  name="first_name" placeholder="First Name"/>
                                </div>

                                <div class="col-lg-6">
                                    <label fclass="mb-1">{{ __('Last_Name') }}</label>
                                    <x-input type="text" name="last_name" placeholder="Last Name"/>
                                </div>

                                <div class="col-lg-6">
                                    <label class="mb-1">{{ __('Email') }}</label>
                                    <x-input type="email" name="email" placeholder="Email Address"/>
                                </div>

                                <div class="col-lg-6">
                                    <label class="mb-1">{{ __('Gender') }}</label>
                                    <x-select name="gender">
                                        @foreach (config('enums.ganders') as $gender)
                                            <option value="{{ $gender }}" {{ $gender == old('gender') ? 'selected':'' }}>
                                                {{ $gender }}
                                            </option>
                                        @endforeach
                                    </x-select>
                                </div>

                                <div class="col-lg-6 mb-3">
                                    <label class="mb-1">{{ __('Phone_number') }}</label>
                                    <input type="text" onkeypress="onlyNumber(event)" value="{{ old('mobile') }}" name="mobile" class="form-control" placeholder="Phone number ..." pattern="^\d{10}$" title="Please enter exactly 10 digits." requird>
                                    @error('mobile')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-lg-6">
                                    <label class="mb-1">{{ __('Date_of_Birth') }}</label>
                                    <x-input type="date" name="date_of_birth"/>
                                </div>

                                <div class="col-lg-6">
                                    <label class="mb-1">{{ __('Password') }}</label>
                                    <x-input type="text" name="password" placeholder="Password ..."/>
                                </div>

                                <div class="col-lg-6">
                                    <label class="mb-1">{{ __('Confirm_Password') }}</label>
                                    <x-input type="text" name="password_confirmation" placeholder="Confirm Password ..."/>
                                </div>
                            </div>
                        </div>

                        {{-- Shop Section --}}
                        <div class="section">
                            <h2 class="title">{{ __('Shop') }}</h2>
                            <div class="row px-2">

                                <div class="col-lg-6">
                                    <label class="mb-1">{{ __('Shop_name') }}</label>
                                    <x-input type="text"  name="name" placeholder="Shop Name"/>
                                </div>

                                <div class="col-lg-6">
                                    <label class="mb-1">{{ __('Agreement_commission') }}</label>
                                    <x-input type="number" name="commission" placeholder="Agreement commission" />
                                </div>

                                <div class="col-lg-6">
                                    <label class="mb-1">{{ __('Logo') }}</label>
                                    <x-input-file  name="logo"/>
                                </div>

                                <div class="col-lg-6">
                                    <label class="mb-1">{{ __('Banner') }}</label>
                                    <x-input-file  name="banner"/>
                                </div>

                                <div class="col-lg-6">
                                    <label class="mb-1">{{ __('Description') }}</label>
                                    <x-textarea name="description" placehold="Description"></x-textarea>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end pt-3">
                            <button type="submit" class="btn btn-primary rounded-0 px-4">{{ __('Save_And_Update') }}</button>
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
