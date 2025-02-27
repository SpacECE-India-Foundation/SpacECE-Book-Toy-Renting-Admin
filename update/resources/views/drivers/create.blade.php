@extends('layouts.app')

@section('content')
    <div class="container-fluid mt-5">
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0 mt-2 mt-sm-0 d-flex">
                <a href="{{ route('driver.index') }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Back</a>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-9 col-xxl-9 col-lg-9 mt-2 mx-auto ">
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title m-0">Add Driver</h2>
                    </div>
                    <div class="card-body">
                        <x-form route="driver.store" type="Submit">
                            <div class="row">
                                <div class="col-lg-6">
                                    <label class="mb-1"><b>First Name</b></label>
                                    <x-input name="first_name" type="text" placeholder="First name" />
                                </div>
                                <div class="col-lg-6">
                                    <label class="mb-1"><b>Last Name</b></label>
                                    <x-input name="last_name" type="text" placeholder="Last name" required/>
                                </div>
                                <div class="col-md-12">
                                    <label class="mb-1"><b>Email Address</b></label>
                                    <x-input name="email" type="email" placeholder="Email Address" required/>
                                </div>
                                <div class="col-md-12 mb-2">
                                    <label class="mb-1"><b>Mobile Number</b></label>
                                    <x-input name="mobile" type="number" placeholder="Mobile Number" required/>
                                </div>
                                <div class="col-lg-6 mb-2">
                                    <label class="mb-1"><b>Password</b></label>
                                    <x-input name="password" type="text" placeholder="Password" />
                                </div>
                                <div class="col-lg-6 mb-2">
                                    <label class="mb-1"><b>Confirm Password</b></label>
                                    <x-input name="password_confirmation" type="text" placeholder="Confirm Password" />
                                </div>
                            </div>
                        </x-form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
