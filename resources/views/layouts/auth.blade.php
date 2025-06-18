<!doctype html>
<html lang="en">

<head>
    @include('layouts.partials.head-script')
</head>

<body class=" d-flex flex-column bg-white" style="background: linear-gradient(180deg, rgba(152, 160, 204, 0.5), rgba(152, 160, 204, 0.5)), url({{ asset('img/bg-kpb.png') }}); background-size: cover; background-repeat: no-repeat; background-position: center;">
    <div class="page page-center" style="top: -3rem;">
        <div class="container container-tight py-4">
            <div class="text-center" style="margin-bottom: -4rem;">
                <!-- BEGIN NAVBAR LOGO -->
                <a href="." aria-label="Tabler"
                    class="navbar-brand navbar-brand-autodark">
                    <img src="{{ asset('img/hres-logo-transparent.png') }}" alt="" class="img-fluid" width="250">
                </a>
                <!-- END NAVBAR LOGO -->
            </div>
            {{ $slot }}
        </div>
    </div>


    @include('layouts.partials.footer-script')
</body>
