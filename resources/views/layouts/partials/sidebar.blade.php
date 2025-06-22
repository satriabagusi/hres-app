<aside class="navbar navbar-vertical navbar-expand-lg" data-bs-theme="dark">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu"
            aria-controls="sidebar-menu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="navbar-brand text-center">
            <a href="{{ route('home') }}" class="fs-3 text-white text-decoration-none">
                <div class="row">
                    <div class="col-5">
                        <img src="{{ asset('img/hres-logo.png') }}" width="50" height="32" alt="Tabler"
                            class=" rounded-circle" />
                    </div>
                    <div class="col-6 text-start align-items-center justify-center">
                        <span class="text-wrap fs-3">
                            HSSE Review Employee System
                        </span>
                    </div>
                </div>
            </a>
        </div>
        <div class="navbar-nav flex-row d-lg-none">
            <div class="nav-item dropdown">
                <a class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown" aria-label="Open user menu">
                    <span class="avatar avatar-sm" style="background-image: url(./img/avatar.png)"></span>
                    <div class="d-none d-xl-block ps-2">
                        <div>{{ Auth::user()->name }}</div>
                        <div class="mt-1 small text-secondary">{{ Auth::user()->company_name }}</div>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <a href="{{ route('logout') }}" class="dropdown-item">Logout</a>
                </div>
            </div>
        </div>
        <div class="collapse navbar-collapse" id="sidebar-menu">
            <ul class="navbar-nav pt-lg-3">
                <li class="nav-item {{ $home_active ?? '' }}">
                    <a class="nav-link" href="{{ route('home') }}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <!-- Download SVG icon from http://tabler.io/icons/icon/home -->
                            <i class="ti ti-home fs-2"></i>
                        </span>
                        <span class="nav-link-title">Dashboard </span>
                    </a>
                </li>
                @if (Auth::user()->role == 'administrator' ||
                        Auth::user()->role == 'medical' ||
                        Auth::user()->role == 'hse' ||
                        Auth::user()->role == 'security')
                    <li class="nav-item {{ $contractor_data ?? '' }} dropdown">
                        <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" data-bs-auto-close="false"
                            role="button" aria-expanded="true">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <!-- Download SVG icon from http://tabler.io/icons/icon/layout-2 -->
                                <i class="ti ti-user-shield fs-2"></i>
                            </span>
                            <span class="nav-link-title me-1">Data Kontraktor</span>
                        </a>
                        <div class="dropdown-menu {{ isset($contractor_data) ? 'show' : '' }}">
                            <div class="dropdown-menu-columns">
                                <a class="dropdown-item {{ $employee_data_active ?? '' }}"
                                    href="{{ route('admin.list-employee') }}">
                                    <span class="nav-link-icon">
                                        <i class="ti ti-user-plus"></i>
                                    </span>
                                    <span class="nav-link-title">
                                        Data Pekerja
                                    </span>
                                </a>
                                <a class="dropdown-item {{ $company_data_active ?? '' }}"
                                    href="{{ route('admin.list-company') }}">
                                    <span class="nav-link-icon">
                                        <i class="ti ti-user-plus"></i>
                                    </span>
                                    <span class="nav-link-title">
                                        Data Perusahaan
                                    </span>
                                </a>
                            </div>
                        </div>
                    </li>
                @endif
                @if (Auth::user()->role == 'administrator')
                    <div class="hr-text text-lime">Admin Menu</div>
                    <li class="nav-item {{ $employee_account_active ?? '' }} dropdown">
                        <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" data-bs-auto-close="false"
                            role="button" aria-expanded="true">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <!-- Download SVG icon from http://tabler.io/icons/icon/layout-2 -->
                                <i class="ti ti-file-database"></i>
                            </span>
                            <span class="nav-link-title">Akun User</span>
                        </a>
                        <div class="dropdown-menu {{ isset($employee_account_active) ? 'show' : '' }}">
                            <div class="dropdown-menu-columns">
                                <a class="dropdown-item {{ $list_employee_active ?? '' }}"
                                    href="{{ route('admin.user-account') }}">
                                    <span class="nav-link-icon">
                                        <i class="ti ti-file-dollar"></i>
                                    </span>
                                    <span class="nav-link-title">
                                        List Akun
                                    </span>
                                </a>
                                <a class="dropdown-item {{ $add_employee_active ?? '' }}" href="#">
                                    <span class="nav-link-icon">
                                        <i class="ti ti-clipboard-data"></i>
                                    </span>
                                    <span class="nav-link-title">
                                        Tambah Akun
                                    </span>
                                </a>
                            </div>
                        </div>
                    </li>
                @endif
                @if (Auth::user()->role == 'contractor')
                    <li class="nav-item {{ $employee_active ?? '' }}">
                        <!--<a class="nav-link" href="{{ route('contractor.list-employee') }}">-->
                        <!--    <span class="nav-link-icon d-md-none d-lg-inline-block">-->
                                <!-- Download SVG icon from http://tabler.io/icons/icon/home -->
                        <!--        <i class="ti ti-users fs-2"></i>-->
                        <!--    </span>-->
                        <!--    <span class="nav-link-title">Data Pekerja</span>-->
                        <!--</a>-->
                    </li>
                    <li class="nav-item {{ $contract_active ?? '' }}">
                        <a class="nav-link" href="{{ route('contractor.list-project-contract') }}">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <!-- Download SVG icon from http://tabler.io/icons/icon/home -->
                                <i class="ti ti-users fs-2"></i>
                            </span>
                            <span class="nav-link-title">Data Kontrak Kerja</span>
                        </a>
                    </li>
                @endif
                <div class="hr-text text-pink">Akun</div>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('home') }}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <!-- Download SVG icon from http://tabler.io/icons/icon/home -->
                            <i class="ti ti-home fs-2"></i>
                        </span>
                        <span class="nav-link-title">Pengaturan Akun</span>
                    </a>
                </li>
                <li class="nav-item mt-auto">
                    <a class="nav-link" href="#">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <!-- Download SVG icon from http://tabler.io/icons/icon/home -->
                            <i class="ti ti-user fs-2"></i>
                        </span>
                        <span class="nav-link-title">Halo, {{Auth::user()->name }}</span>
                    </a>
                    <a class="nav-link" href="{{ route('logout') }}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <!-- Download SVG icon from http://tabler.io/icons/icon/home -->
                            <i class="ti ti-logout fs-2"></i>
                        </span>
                        <span class="nav-link-title">Keluar Aplikasi</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</aside>
