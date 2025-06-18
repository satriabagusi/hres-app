<!doctype html>
<html lang="en">

<head>
    @include('layouts.partials.head-script')
</head>

<body class="layout-fluid">
    {{-- <script src="{{ asset('js/demo-theme.min.js') }}"></script> --}}
    <div class="page">
        {{-- @include('layouts.partials.navbar') --}}

        @include('layouts.partials.sidebar')

        <div class="page-wrapper">
            @include('layouts.partials.header')
            <div class="page-body">
                <div class="container-xl">
                    {{ $slot }}
                </div>
            </div>

            @include('layouts.partials.footer')

        </div>
    </div>

    {{-- env mode production --}}
    {{-- @if (config('app.env') == 'local')
        <div class="offcanvas offcanvas-bottom h-auto show" tabindex="-1" id="offcanvasBottom" aria-modal="true"
            role="dialog">
            <div class="offcanvas-body">
                <div class="container">
                    <div class="row align-items-center">
                        <div class="col">
                            <strong class="text-red">Perhatian!</strong> ⚠️ Aplikasi masih dalam tahap pengembangan. Silahkan Eksplor Aplikasi dan beritahu Developer terkait bug yang terjadi <a href="https://wa.me/6285974212652" target="_blank">Hubungi Developer</a>
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-primary" data-bs-dismiss="offcanvas">
                                Saya Mengerti
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif --}}

    @include('layouts.partials.footer-script')
</body>

</html>
