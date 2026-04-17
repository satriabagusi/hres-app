{{-- <header class="navbar-expand-md">
    <div class="collapse navbar-collapse" id="navbar-menu">
        <div class="navbar">
            <div class="container-xl">
                <div class="row flex-fill align-items-center">
                    <div class="col">
                        <ul class="navbar-nav">
                            </ul>
                    </div>
                    <div class="col-2 d-none d-xxl-block">
                        <div class="my-2 my-md-0 flex-grow-1 flex-md-grow-0 order-first order-md-last">
                           </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header> --}}



<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <!-- Page pre-title -->
                <div class="page-pretitle">{{ $title }}</div>
                <h2 class="page-title"> {{ $subTitle }}</h2>
                {{-- breadcrumb --}}

                <ol class="breadcrumb breadcrumb-arrows" aria-label="breadcrumbs">
                    <li class="breadcrumb-item">
                        <a href="{{ route('home') }}">Dashboard</a>
                    </li>
                    @if (Request::segment(2) == 'list-project-contract')
                        <li class="breadcrumb-item active">
                            <a href="#">List Project
                                Contract</a>
                        </li>
                    @endif

                    @if (Request::segment(2) == 'list-employee')
                        <li class="breadcrumb-item">
                            <a href="{{ route('contractor.list-project-contract') }}">List Project Contract</a>
                        </li>
                        <li class="breadcrumb-item active">
                            <a href="#">List Pekerja</a>
                        </li>
                    @endif

                    @if (Request::segment(2) == 'list-draft-employee')
                        <li class="breadcrumb-item">
                            <a href="{{ route('contractor.list-project-contract') }}">List Project Contract</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ url()->previous() ?? route('contractor.list-project-contract') }}">List Pekerja</a>
                        </li>
                        <li class="breadcrumb-item active">
                            <a href="#">List Pekerja Draft/Upload</a>
                        </li>
                    @endif

                    @if(Request::segment(1) == 'list-employee')
                        <li class="breadcrumb-item active">
                            <a href="#">List Pekerja</a>
                        </li>
                    @endif
                    @if(Request::segment(1) == 'list-company')
                        <li class="breadcrumb-item active">
                            <a href="#">List Perusahaan</a>
                        </li>
                    @endif
                    @if(Request::segment(1) == 'list-project')
                        <li class="breadcrumb-item active">
                            <a href="#">List Project Contract</a>
                        </li>
                    @endif
                    @if(Request::segment(1) == 'user-account')
                        <li class="breadcrumb-item active">
                            <a href="#">List Akun User</a>
                        </li>
                    @endif
                    @if(Request::segment(1) == 'account-setting')
                        <li class="breadcrumb-item active">
                            <a href="#">Pengaturan Akun</a>
                        </li>
                    @endif
                </ol>
                <div class="hr-text">
                </div>
            </div>
        </div>
    </div>
