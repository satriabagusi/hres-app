<div class="container-xl">
    <div class="row row-deck row-cards">

        <div class=@if (Auth::user()->role === 'administrator') col-6 @else col-12 col-lg-6 @endif>
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
                                <div class="row g-5">
                                    <div class="col-auto">
                                        <a href="{{ route('admin.list-company') }}" class="btn btn-primary">Lihat
                                            Perusahaan</a>
                                    </div>
                                </div>
                            @elseif(Auth::user()->role === 'contractor')
                                <p class="text-muted">Selamat datang di HRES (HSSE Review Employee System). Silahkan
                                    Upload Data Pekerja Anda.</p>
                            @endif
                        </div>
                        <div class="col-12 col-sm-auto d-flex justify-content-center">
                            <img src="{{ asset('img/welcome-abroad.png') }}" alt="" srcset=""
                                class="img-fluid" style="max-width: 300px; max-height: 150px;" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6">
            <div class="row row-cards">
                {{-- Medical Approved --}}
                <div class="col-sm-6 col-lg-6">
                    <div class="card card-sm bg-teal-lt">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="bg-teal text-white avatar">
                                        <i class="ti ti-thumb-up"></i>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium">
                                        <b>{{ $total_medical_fit_to_work }}</b> Fit to Work
                                    </div>
                                    <div class="text-secondary">Lolos verifikasi medical</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Medical On Review --}}
                <div class="col-sm-6 col-lg-6">
                    <div class="card card-sm bg-yellow-lt">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="bg-yellow text-white avatar">
                                        <i class="ti ti-heart-rate-monitor"></i>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium">
                                        <b>{{ $total_medical_on_review }}</b> Medical On Review
                                    </div>
                                    <div class="text-secondary">Proses verifikasi medical</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Medical Follow Up --}}
                <div class="col-sm-6 col-lg-6">
                    <div class="card card-sm bg-yellow-lt">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="bg-yellow text-white avatar">
                                        <i class="ti ti-heart-up"></i>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium">
                                        <b>{{ $total_medical_follow_up }}</b> Medical Follow Up
                                    </div>
                                    <div class="text-secondary">Proses verifikasi medical</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Medical Unfit --}}
                <div class="col-sm-6 col-lg-6">
                    <div class="card card-sm bg-pink-lt">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="bg-pink text-white avatar">
                                        <i class="ti ti-heart-off"></i>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium">
                                        <b>{{ $total_medical_unfit }}</b> Medical Unfit
                                    </div>
                                    <div class="text-secondary">Proses verifikasi medical</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Security Approved --}}
                <div class="col-sm-6 col-lg-6">
                    <div class="card card-sm bg-blue-lt">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="bg-blue text-white avatar">
                                        <i class="ti ti-id-badge-2"></i>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium">
                                        <b>{{ $total_id_badge_printed }}</b> ID Card Tercetak
                                    </div>
                                    <div class="text-secondary">ID Badge sudah tercetak</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Before Induction --}}
                <div class="col-sm-6 col-lg-6">
                    <div class="card card-sm bg-orange-lt">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="bg-orange text-white avatar">
                                        <i class="ti ti-chalkboard"></i>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium">
                                        <b>{{ $total_before_induction }}</b> Belum Induction
                                    </div>
                                    <div class="text-secondary">jumlah Pekerja belum Induction</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        @if (Auth::user()->role === 'contractor')
            <div class="col">
                <div class="card">
                    <div class="card-body py-4" style="height:auto">
                        <div id="chart-worker-status-pie" style="height: 300px;"></div>
                    </div>
                </div>
            </div>
            @push('scripts')
                <script>
                    document.addEventListener('livewire:init', function() {

                        const series = [{{ $total_medical_fit_to_work }}, {{ $total_medical_on_review }}, {{ $total_medical_follow_up }}, {{ $total_medical_unfit }}, {{ $total_before_induction }}, {{ $total_id_badge_printed }}];

                        const labels = ["Fit to Work", "Medical On Review", "Medical Follow Up", "Medical Unfit", "Belum Induction", "ID Card Tercetak", ];

                        new ApexCharts(document.querySelector("#chart-worker-status-pie"), {
                            chart: {
                                type: "donut",
                                fontFamily: "inherit",
                                height: 300,
                                animations: {
                                    enabled: false
                                }
                            },
                            series: series,
                            labels: labels,
                            colors: [
                                "#1cc88a",
                                "#f76707",
                                "#f59f00",
                                "#d6336c",
                                "#ae3ec9",
                                "#4299e1"
                            ],
                            tooltip: {
                                fillSeriesColor: false,
                                theme: "light"
                            },
                            legend: {
                                show: true,
                                position: "bottom",
                                offsetY: 12,
                                markers: {
                                    width: 10,
                                    height: 10,
                                    radius: 100
                                },
                                itemMargin: {
                                    horizontal: 8,
                                    vertical: 8
                                }
                            }
                        }).render();
                    })
                </script>
            @endpush
        @endif


        <div class="col-12">
            <div class="row row-cards">
                {{-- Pekerja diajukan --}}
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
                                        <div class="text-secondary">Mohon menunggu verifikasi</div>
                                    @elseif (Auth::user()->role === 'administrator' ||
                                            Auth::user()->role === 'manager' ||
                                            Auth::user()->role === 'hse' ||
                                            Auth::user()->role === 'medical' ||
                                            Auth::user()->role === 'security')
                                        <div class="font-weight-medium"><b>{{ $total_worker_submitted }}</b> Pekerja
                                            diajukan</div>
                                        <div class="text-secondary">Segera Verifikasi</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Pekerja Belum di Ajukan --}}
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
                                        <div class="font-weight-medium">{{ $total_worker_draft }} Pekerja belum
                                            diajukan
                                        </div>
                                        <div class="text-secondary">Segera Lengkapi Persyaratan</div>
                                    @elseif (Auth::user()->role === 'administrator' ||
                                            Auth::user()->role === 'manager' ||
                                            Auth::user()->role === 'hse' ||
                                            Auth::user()->role === 'medical' ||
                                            Auth::user()->role === 'security')
                                        <div class="font-weight-medium">{{ $total_worker_draft }} Pekerja belum
                                            diajukan
                                        </div>
                                        <div class="text-secondary">Segera Lengkapi Persyaratan</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Belum Induction --}}

                @if (Auth::user()->role === 'administrator' || Auth::user()->role === 'manager')
                    <div class="card">
                        <div class="card-body">
                            <h2>Grafik Progress Pekerja Kontraktor</h2>
                            <hr>
                            <div id="chart-company-workers" style="height: 400px;"></div>
                        </div>
                    </div>
                    @push('scripts')
                        <script>
                            document.addEventListener('livewire:init', function() {

                                const categories = @json($list_company_chart->pluck('company_name'));
                                const fitData = @json($list_company_chart->pluck('fit_to_work'));
                                const reviewData = @json($list_company_chart->pluck('medical_on_review'));
                                const followUpData = @json($list_company_chart->pluck('medical_follow_up'));
                                const unfitData = @json($list_company_chart->pluck('medical_unfit'));
                                const idCardData = @json($list_company_chart->pluck('id_card_printed'));
                                const belumInductionData = @json($list_company_chart->pluck('belum_induction'));

                                new ApexCharts(document.querySelector("#chart-company-workers"), {
                                    chart: {
                                        type: 'bar',
                                        height: 400,
                                        stacked: true,
                                        toolbar: {
                                            show: true
                                        }
                                    },
                                    series: [{
                                            name: 'Fit to Work',
                                            data: fitData
                                        },
                                        {
                                            name: 'Medical On Review',
                                            data: reviewData
                                        },
                                        {
                                            name: 'Medical Follow Up',
                                            data: followUpData
                                        },
                                        {
                                            name: 'Medical Unfit',
                                            data: unfitData
                                        },
                                        {
                                            name: 'ID Card Tercetak',
                                            data: idCardData
                                        },
                                        {
                                            name: 'Belum Induction',
                                            data: belumInductionData
                                        },
                                    ],
                                    xaxis: {
                                        categories: categories,
                                        labels: {
                                            rotate: -45
                                        }
                                    },
                                    colors: [
                                        '#28a745', // green fit
                                        '#ffc107', // yellow on review
                                        '#ffd902', // cyan follow up
                                        '#dc3545', // red unfit
                                        '#007bff', // blue id card
                                        '#6c757d' // gray belum induction
                                    ],
                                    dataLabels: {
                                        enabled: true,
                                        style: {
                                            fontSize: '12px',
                                            colors: ['#333']
                                        },
                                        offsetY: -6
                                    },
                                    fill: {
                                        opacity: 1,
                                        type: 'solid'
                                    },
                                    stroke: {
                                        show: true,
                                        width: 0.85,
                                        colors: ['#fff']
                                    },
                                    legend: {
                                        position: 'bottom',
                                        offsetY: 10,
                                        markers: {
                                            radius: 12,
                                            width: 12,
                                            height: 12
                                        }
                                    },
                                    tooltip: {
                                        shared: true,
                                        intersect: false
                                    }
                                }).render();
                            })
                        </script>
                    @endpush
                @endif
            </div>
        </div>
    </div>
</div>
