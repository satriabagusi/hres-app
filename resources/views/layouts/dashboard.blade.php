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

    @include('layouts.partials.footer-script')
</body>

</html>
