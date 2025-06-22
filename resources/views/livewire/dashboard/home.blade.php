<div class="container-xl">
    <div class="row row-deck row-cards">

        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="row gy-3">
                        <div class="col-12 col-sm d-flex flex-column">
                            <h3 class="h2">
                                Selamat
                                @if (\Carbon\Carbon::now()->hour < 12)
                                    Pagi
                                @elseif (\Carbon\Carbon::now()->hour < 15)
                                    Siang
                                @elseif (\Carbon\Carbon::now()->hour < 18)
                                    Sore
                                @else
                                    Malam
                                @endif
                                , {{ Auth::user()->name . ' (' . Auth::user()->company_name . ')' }}

                            </h3>
                            @if (Auth::user()->role === 'administrator')
                                <p class="text-muted">Ada {{ $total_contractor_pending }} Perusahaan yang menunggu
                                    verifikasi</p>
                                <div class="row g-5 mt-auto">
                                    <div class="col-auto">
                                        <a href="{{ route('admin.list-company') }}" class="btn btn-primary">Lihat
                                            Perusahaan</a>
                                    </div>
                                    <div class="col-auto">
                                        <button class="btn btn-secondary">Lihat Semua</button>
                                    </div>
                                </div>
                            @elseif(Auth::user()->role === 'contractor')
                                <p class="text-muted">Selamat datang di HRES (HSSE Review Employee System). Silahkan
                                    Upload Data Pekerja Anda.</p>
                            @endif
                        </div>
                        <div class="col-12 col-sm-auto d-flex justify-content-center">
                            <img src="{{ asset('img/welcome-abroad.png') }}" alt="" srcset=""
                                class="img-fluid" style="max-width: 300px; max-height: 300px;" />
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="col-12">
            <div class="row row-cards">
                <div class="col-sm-6 col-lg-3">
                    <div class="card card-sm bg-green-lt">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="bg-green text-white avatar">
                                        <i class="ti ti-users"></i>
                                    </span>
                                </div>
                                <div class="col">
                                    @if (Auth::user()->role === 'contractor')
                                        <div class="font-weight-medium"><b>{{ $total_worker_submitted }}</b> Pekerja
                                            diajukan</div>
                                        <div class="text-secondary">mohon menunggu approval</div>
                                    @elseif (Auth::user()->role === 'administrator' ||
                                            Auth::user()->role === 'manager' ||
                                            Auth::user()->role === 'hse' ||
                                            Auth::user()->role === 'medical' ||
                                            Auth::user()->role === 'security')
                                        <div class="font-weight-medium"><b>{{ $total_worker_submitted }}</b> Pekerja
                                            diajukan</div>
                                        <div class="text-secondary">mohon di verifikasi</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card card-sm bg-cyan-lt">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="bg-cyan text-white avatar">
                                        <i class="ti ti-file-text"></i>
                                    </span>
                                </div>
                                <div class="col">

                                    @if (Auth::user()->role === 'contractor')
                                        <div class="font-weight-medium">{{ $total_worker_draft }} Pekerja sudah diupload
                                        </div>
                                        <div class="text-secondary">mohon lengkapi dokumen nya</div>
                                    @elseif (Auth::user()->role === 'administrator' ||
                                            Auth::user()->role === 'manager' ||
                                            Auth::user()->role === 'hse' ||
                                            Auth::user()->role === 'medical' ||
                                            Auth::user()->role === 'security')
                                        <div class="font-weight-medium">{{ $total_worker_draft }} Pekerja sudah diupload
                                        </div>
                                        <div class="text-secondary">mohon di verifikasi</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card card-sm">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span
                                        class="bg-x text-white avatar"><!-- Download SVG icon from http://tabler.io/icons/icon/brand-x -->
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" class="icon icon-1">
                                            <path d="M4 4l11.733 16h4.267l-11.733 -16z" />
                                            <path d="M4 20l6.768 -6.768m2.46 -2.46l6.772 -6.772" />
                                        </svg></span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium">623 Shares</div>
                                    <div class="text-secondary">16 today</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card card-sm">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span
                                        class="bg-facebook text-white avatar"><!-- Download SVG icon from http://tabler.io/icons/icon/brand-facebook -->
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" class="icon icon-1">
                                            <path
                                                d="M7 10v4h3v7h4v-7h3l1 -4h-4v-2a1 1 0 0 1 1 -1h3v-4h-3a5 5 0 0 0 -5 5v2h-3" />
                                        </svg></span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium">132 Likes</div>
                                    <div class="text-secondary">21 today</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
