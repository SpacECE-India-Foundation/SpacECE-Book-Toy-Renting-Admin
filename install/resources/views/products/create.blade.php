@extends('layouts.app')

@section('content')
    <div class="container-fluid my-4">
        <div class="row page-titles mx-0">
            <div class="col-12 mb-3 mb-lg-0 p-0">
                <a href="{{ route('product.index') }}" class="btn btn-danger">
                    <i class="fa fa-arrow-left"></i> {{ __('Back') }}
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-7 col-xxl-7 col-lg-7 m-auto">
                <div class="card shadow rounded-12 border-0">
                    <div class="card-header bg-primary py-3">
                        <h2 class="card-title m-0 text-white">{{ __('Add_New_Product') }}</h2>
                    </div>
                    <div class="card-body">
                        <x-form route="product.store" type="Submit">
                            <label class="mb-1">{{ __('Name') }}</label>
                            <x-input name="name" type="text" placeholder="Product name" />

                            <div class="mb-3">
                                <label class="mb-1">{{ __('Price') }}</label>
                                <input name="price" type="text" class="form-control" placeholder="Product price"
                                    onkeypress="onlyNumber(event)" />
                                @error('price')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="mb-1">{{ __('Discount_Price') }}</label>
                                <input name="discount_price" type='text' class="form-control"
                                    placeholder="Discount Price" onkeypress="onlyNumber(event)" />
                                @error('discount_price')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <label class="mb-1 mt-3">{{ __('Description') }}</label>
                            <textarea name="description" class="form-control mb-3" placeholder="Product Description"></textarea>

                            {{-- <div class="mt-3">
                                <label class="mb-1">QrCode Text</label>
                                <x-input name="qrcode" type="text" placeholder="QrCode Message.."/>
                            </div> --}}

                            <input type="hidden" id="slug" name="slug" class="form-control input-default"
                                value="{{ old('slug') }}">
                                @if(auth()->user()->hasAnyRole(['admin', 'root']))
                                <div>
                                    <label class="mb-1">{{ __('Shop') }}</label>
                                    <x-select name="shop_id" id="shop_id">
                                        <option value="">{{ __('Select Shop') }}</option>
                                        @foreach ($shops as $shop)
                                            <option value="{{ $shop->id }}">{{ $shop->name }}</option>
                                        @endforeach
                                    </x-select>
                                </div>
                            @endif
                            
                            <div>
                                <label class="mb-1">{{ __('Service') }}</label>
                                <x-select name="service_id">
                                    @foreach ($services as $service)
                                        <option value="{{ $service->id }}">{{ $service->name }}</option>
                                    @endforeach
                                </x-select>
                            </div>

                            <label class="mb-1">{{ __('Variant') }}</label>
                            <x-select name="variant_id" />

                            <label class="mb-1">{{ __('Thumbnail') }}</label>
                            <x-input-file name="image" type="file" />

                            <div class="form-group">
                                <label for="active" class="mr-2">
                                    <input checked type="radio" id="active" name="active" value="1">
                                    {{ __('Active') }}
                                </label>

                                <label for="inActive">
                                    <input type="radio" id="inActive" name="active" value="0"> {{ __('Inactive') }}
                                </label>
                            </div>
                        </x-form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Function to allow only numbers in input fields
    function onlyNumber(evt) {
        var chars = String.fromCharCode(evt.which);
        if (!(/[0-9.]/.test(chars))) {
            evt.preventDefault();
        }
    }

    // Update slug based on name input
    $('#name').keyup(function() {
        $('#slug').val($(this).val().toLowerCase().split(',').join('').replace(/\s/g, "-"));
    });

    // Fetch services when a shop is selected
    $('select[name="shop_id"]').on('change', function() {
        var shopId = $(this).val();
        if (shopId) {
            $.ajax({
                url: `/shops/${shopId}/services`, // API to fetch services by shop
                type: "GET",
                dataType: "json",
                success: function(data) {
                    $('select[name="service_id"]').empty(); // Clear existing options
                    $('select[name="service_id"]').append('<option value="">{{ __("Select Service") }}</option>');
                    $.each(data, function(key, value) {
                        $('select[name="service_id"]').append('<option value="' + value.id + '">' + value.name + '</option>');
                    });
                }
            });
        } else {
            $('select[name="service_id"]').empty();
            $('select[name="service_id"]').append('<option value="">{{ __("Select Service") }}</option>');
        }
    });

    // Fetch variants when a service is selected
    $('select[name="service_id"]').on('change', function() {
        var serviceId = $(this).val();
        if (serviceId) {
            $.ajax({
                url: `/services/${serviceId}/variants`, // API to fetch variants by service
                type: "GET",
                dataType: "json",
                success: function(data) {
                    $('select[name="variant_id"]').empty(); // Clear existing options
                    $('select[name="variant_id"]').append('<option value="">{{ __("Select Variant") }}</option>');
                    $.each(data, function(key, value) {
                        $('select[name="variant_id"]').append('<option value="' + value.id + '">' + value.name + '</option>');
                    });
                }
            });
        } else {
            $('select[name="variant_id"]').empty();
            $('select[name="variant_id"]').append('<option value="">{{ __("Select Variant") }}</option>');
        }
    });
</script>
@endpush
