@php
    $appSetting = App\Models\AppSetting::first();
@endphp
<!doctype html>
<html lang="en" dir="{{ $appSetting?->direction }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link rel="icon" type="image/png" href="{{ $appSetting?->websiteFaviconPath ?? asset('images/fev_icon.svg') }}">
    <title>{{ $appSetting->title ?? config('app.name') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Mooli&family=Poppins&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('web/css/all.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('web/css/bootstrap.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('web/css/custom.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('web/css/datatables.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('web/css/select2.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('web/apexcharts/apexcharts.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('web/css/style.css') }}" type="text/css">
</head>

<body>

    {{-- <div class="preload">
        <div class="flexbox">
            <div>
                <img src="{{ asset('images/loader/GoldStar-Loader.gif') }}" alt="">
            </div>
        </div>
    </div> --}}

    @include('layouts.partials.sidebar')

    <div class="main-content">

        @yield('content')

    </div>

    <script src="{{ asset('web/js/jquery.min.js') }}"></script>
    <script src="{{ asset('web/js/popper.js') }}"></script>
    <script src="{{ asset('web/js/sweet-alert.js') }}"></script>
    <script src="{{ asset('web/js/bootstrap.min.js') }}"></script>

    <script src="{{ asset('web/js/argon.js') }}"></script>
    <script src="{{ asset('web/js/main.js') }}"></script>
    <script src="{{ asset('web/js/datatables.min.js') }}"></script>
    <script src="{{ asset('web/js/select2.min.js') }}"></script>
    <script src="{{ asset('web/apexcharts/apexcharts.min.js') }}"></script>

    @if (session('visitor'))
        <script>
            Swal.fire(
                'You are visitor.',
                'Sorry, you can\'t anything create, update and delete.',
                'question'
            )
        </script>
    @endif

    @if (session('success'))
        <script>
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            Toast.fire({
                icon: 'success',
                title: '{{ session('success') }}'
            })
        </script>
    @endif

    @if (session('error'))
        <script>
            Swal.fire({
                position: 'top-end',
                icon: 'error',
                title: '{{ session('error') }}',
                showConfirmButton: false,
                timer: 2000
            })
        </script>
    @endif

    @stack('scripts')

    <script>
        $('.select2').select2();

        function onlyNumber(evt) {
            var chars = String.fromCharCode(evt.which);
            if (!(/[0-9.]/.test(chars))) {
                evt.preventDefault();
            }
        };

        $('#language').change(function() {
            var url = "{{ route('change.local') }}";
            var lan = $(this).val();
            window.location.href = url + '?ln=' + lan;
        });

        const lang = '{{ session()->get('local') }}';
        if (lang === 'bn') {
            $('#myTable').DataTable({
                language: {
                    'paginate': {
                        'previous': '<i class="fas fa-angle-double-left"></i>',
                        'next': '<i class="fas fa-angle-double-right"></i>'
                    },
                    "lengthMenu": "দেখাচ্ছে _MENU_ এন্ট্রি",
                    "zeroRecords": "কোনো রেকর্ড পাওয়া যায়নি",
                    "info": "দেখাচ্ছে _START_ থেকে _END_ এর _TOTAL_ এন্ট্রি",
                    "infoEmpty": "0 এন্ট্রির মধ্যে 0 থেকে 0 দেখানো হচ্ছে",
                    "infoFiltered": "(মোট _MAX_টি এন্ট্রি থেকে ফিল্টার করা হয়েছে)",
                    "search": "অনুসন্ধান",
                }
            });
        } else {
            $('#myTable').DataTable({
                language: {
                    'paginate': {
                        'previous': '<i class="fas fa-angle-double-left"></i>',
                        'next': '<i class="fas fa-angle-double-right"></i>'
                    }
                },
                "lengthMenu": [
                    [15, 25, 50, 100],
                    [15, 25, 50, 100]
                ]
            });
        }
    </script>
</body>

</html>
