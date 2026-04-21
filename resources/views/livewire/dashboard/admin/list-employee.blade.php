<div>
    <div class="col-12">

        <div class="row mb-4 align-items-center">
            <div class="col-auto">
                <span class="fs-5">Filter berdasarkan:</span>
            </div>

            <!-- Dropdown Filter Utama -->
            <div class="col-auto">
                <div class="dropdown">
                    <button class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                        <div wire:loading wire:target="filter">
                            <div class="spinner-border spinner-border-sm text-primary mt-1" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>&nbsp;
                        Filter: {{ $filter }}
                    </button>
                    <div class="dropdown-menu">
                        <button type="button" class="dropdown-item" wire:click="$set('filter', 'All')">Semua</button>
                        <button type="button" class="dropdown-item"
                            wire:click="$set('filter', 'Perusahaan')">Perusahaan</button>
                        <button type="button" class="dropdown-item"
                            wire:click="$set('filter', 'Project')">Project</button>
                    </div>
                </div>
            </div>

            <!-- Dropdown Filter Selected -->
            @if ($filter !== 'All')
                <div class="col-auto" wire:key="filter-selected">
                    <div class="dropdown">
                        <button class="btn btn-outline-orange dropdown-toggle text-truncate" data-bs-toggle="dropdown"
                            style="max-width: 350px;">
                            <div wire:loading wire:target="filterSelected">
                                <div class="spinner-border spinner-border-sm text-primary mt-1" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>&nbsp;
                            {!! $filterSelected ? "<span class='text-truncate'>$listFilter[$filterSelected]</span>" ?? 'Pilih' : 'Pilih' !!}
                        </button>
                        <div class="dropdown-menu">
                            @forelse ($listFilter as $id => $name)
                                <button type="button" class="dropdown-item"
                                    wire:click="$set('filterSelected', {{ $id }})">
                                    {{ $name }}
                                </button>
                            @empty
                                <span class="dropdown-item text-muted">Tidak ada data</span>
                            @endforelse
                        </div>
                    </div>

                </div>
            @endif

            <!-- Dropdown Filter Status Pekerja -->
            @if ($filterSelected)
                <div class="col-auto" wire:key="filter-status">
                    <div class="dropdown">
                        <button class="btn btn-outline-success dropdown-toggle" data-bs-toggle="dropdown">
                            <div wire:loading wire:target="statusSelected">
                                <div class="spinner-border spinner-border-sm text-primary mt-1" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>&nbsp;
                            Status:

                            @if ($statusSelected === 'approved')
                                ID Badge Tercetak
                            @elseif($statusSelected === 'submitted')
                                Di Ajukan
                            @elseif($statusSelected == null)
                                Semua
                            @endif
                        </button>
                        <div class="dropdown-menu">
                            <button type="button" class="dropdown-item"
                                wire:click="$set('statusSelected', 'approved')">Tercetak</button>
                            <button type="button" class="dropdown-item"
                                wire:click="$set('statusSelected', 'submitted')">Diajukan</button>
                            <button type="button" class="dropdown-item" wire:click="$set('statusSelected', null)">Semua
                                Status</button>
                        </div>
                    </div>
                </div>
            @endif
        </div>



        <div class="card">
            <div class="card-table">
                <div class="card-header">
                    <div class="row w-full">
                        <div class="col">
                            <h3 class="card-title mb-0">List Pekerja</h3>
                            <p class="text-secondary m-0">Data Pekerja Kontraktor.</p>
                        </div>
                        <div class="col-md-auto col-sm-12">
                            <div class="ms-auto d-flex flex-wrap btn-list">
                                <div class="input-group input-group-flat w-auto">
                                    <span class="input-group-text">
                                        <!-- Download SVG icon from http://tabler.io/icons/icon/search -->
                                        <i class="ti ti-search"></i>
                                    </span>
                                    <input id="advanced-table-search" type="text"
                                        class="form-control form-control-sm" autocomplete="off" placeholder="Cari ... "
                                        wire:model.live='search' />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-auto col-sm-12">
                            <div class="dropdown">
                                <a class="btn btn-outline-azure dropdown-toggle d-flex align-items-center "
                                    data-bs-toggle="dropdown" wire:loading.delay wire:loading.attr="disabled"
                                    wire:target="totalPaginate, page">
                                    <!-- Spinner dalam button saat loading -->
                                    <div wire:loading.delay wire:target="totalPaginate"
                                        class="spinner-border spinner-border-sm text-primary me-2" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <span id="page-count" class="me-1">Tampilkan {{ $totalPaginate }}</span>
                                    <span>data</span>
                                </a>
                                <div class="dropdown-menu">
                                    <button type="button" class="dropdown-item"
                                        wire:click="$set('totalPaginate', 10)">10
                                        data</button>
                                    <button type="button" class="dropdown-item"
                                        wire:click="$set('totalPaginate', 20)">20
                                        data</button>
                                    <button type="button" class="dropdown-item"
                                        wire:click="$set('totalPaginate', 50)">50
                                        data</button>
                                    <button type="button" class="dropdown-item"
                                        wire:click="$set('totalPaginate', 100)">100
                                        data</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-auto col-sm-12">
                            <button class="btn btn-outline-orange " wire:loading.attr="disabled"
                                wire:target="exportExcel" wire:click="exportExcel">
                                <i class="ti ti-file-export"></i>
                                <span class="ms-1">Export Data to Excel(Filtered)</span>
                                <span wire:loading wire:target="exportExcel"
                                    class="spinner-border spinner-border-sm text-primary ms-2" role="status">
                                    <span class="visually-hidden">Loading...</span>
                            </button>
                        </div>
                    </div>
                </div>
                <div id="advanced-table">
                    <div class="table-responsive">
                        <div class="position-relative">
                            <div wire:loading.delay
                                wire:target="totalPaginate, search, page, approveDocument, rejectDocument"
                                class="position-absolute w-100 h-100 bg-white bg-opacity-75 table-loading-overlay"
                                style="top: 0; left: 0; z-index: 10;">
                                <div class="spinner-border text-primary" role="status"
                                    style="width: 4rem; height: 4rem; border-width: 0.5rem;">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>
                        <table class="table table-vcenter table-selectable">
                            <thead>
                                <tr>
                                    <th width="100px">NO</th>
                                    <th>Nama Pekerja</th>
                                    <th width="80px">Nama Perusahaan</th>
                                    <th>USIA</th>
                                    <th>NIK</th>
                                    <th width="80px">Jabatan</th>
                                    <th width="80px">Status</th>
                                    <th class="text-center">Verifikasi</th>
                                    <th>#</th>
                                </tr>
                            </thead>
                            <tbody class="table-tbody" wire:loading.class="bg-white">
                                @forelse ($employees as $item)
                                    @php
                                        $medicalStatus = optional($item->medical_review)->status;
                                        $securityStatus = optional($item->security_review)->status;
                                    @endphp
                                    <tr class="main-row {{ $item->is_blacklisted_active ? 'bg-dark text-white' : '' }}" data-id="{{ $item->id }}">
                                        <td class="text-center">
                                            <span class="d-flex align-items-center">
                                                <span class="me-1 fs-3 d-flex align-items-center">
                                                    {{ $loop->iteration + ($employees->currentPage() - 1) * $employees->perPage() }}
                                                    <span class="toggle-detail ms-1"
                                                        wire:click="toggleDetail({{ $item->id }})">
                                                        @if ($expandedRowId === $item->id)
                                                            <i class="ti ti-info-circle-filled text-orange"
                                                                style="cursor: pointer;"></i>
                                                        @else
                                                            <i class="ti ti-info-circle text-orange"
                                                                style="cursor: pointer;"></i>
                                                        @endif
                                                    </span>
                                                </span>
                                                <span class="fs-3 d-flex align-items-center">

                                                    @if (
                                                        !$item->is_blacklisted_active &&
                                                        (Auth::user()->role === 'administrator' || Auth::user()->role === 'security' || Auth::user()->role === 'hse') &&
                                                            $medicalStatus === 'approved' &&
                                                            $securityStatus === 'approved' &&
                                                            !empty($item->induction_card_number))
                                                        <i class="ti ti-printer text-indigo" style="cursor: pointer;"
                                                            wire:click='confirmPrintIdBadge({{ $item->id }})'>
                                                        </i>
                                                    @endif
                                                    @if (Auth::user()->role === 'administrator')
                                                        <i class="ti ti-transfer text-cyan ms-1"
                                                            title="Pindah PT/Project"
                                                            wire:click='modalTransferEmployee({{ $item->id }})'
                                                            style="cursor: pointer;">
                                                        </i>
                                                        <i class="ti ti-trash text-pink"
                                                            wire:click='deleteConfirmation({{ $item->id }})'
                                                            style="cursor: pointer;">
                                                        </i>
                                                    @endif
                                                </span>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-body">{{ Str::title($item->full_name) }}</span>
                                        </td>
                                        <td>
                                            {{ $item->project_contractor->contractor->company_name }}
                                        </td>
                                        <td class="">
                                            {{ \Carbon\Carbon::parse($item->birth_date)->age }}

                                            @if (\Carbon\Carbon::parse($item->birth_date)->age > 55)
                                                <a href="{{ 'uploads/employee_documents/' . $item->age_justification_document }}"
                                                    onclick="window.open(this.href, 'new', 'popup'); return false;"
                                                    class="text-decoration-none"> <i
                                                        class="ti ti-info-triangle text-red"></i> </a>
                                            @endif

                                        </td>

                                        <td class="">{{ $item->nik }}</td>
                                        <td class="">{{ $item->position }}</td>
                                        <td class="sort-status">
                                            @if ($item->is_blacklisted_active)
                                                <span class="badge bg-dark text-dark-fg">Blacklisted</span>
                                            @elseif($item->status == 'approved')
                                                <span class="badge bg-teal text-teal-fg">ID Badge Tercetak</span>
                                            @elseif($item->status == 'draft')
                                                <span class="badge bg-orange text-orange-fg">Draft</span>
                                            @elseif($item->status == 'submitted')
                                                <span class="badge bg-blue text-blue-fg">Diajukan</span>
                                            @elseif($item->status == 'rejected')
                                                <span class="badge bg-red text-red-fg">Ditolak</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="row text-muted small fw-bold text-start text-md-center">
                                                <div
                                                    class="col-12 col-md-4 d-flex flex-column flex-md-column align-items-start align-items-md-center mb-2 mb-md-0">
                                                    <div>Medical</div>
                                                    <div>
                                                        @if ($item->is_blacklisted_active)
                                                            <span class="badge bg-dark text-dark-fg">Blacklisted</span>
                                                        @elseif($medicalStatus == 'on_review')
                                                            <span class="badge bg-yellow text-yellow-fg">On
                                                                Review</span>
                                                        @elseif($medicalStatus == 'approved')
                                                            <span class="badge bg-teal text-teal-fg">Disetujui</span>
                                                        @elseif($medicalStatus == 'rejected')
                                                            <span class="badge bg-red text-red-fg"
                                                                wire:click='showModalAlasanRejectMcu({{ $item->id }})'>Ditolak</span>
                                                        @else
                                                            <span class="badge bg-secondary text-secondary-fg">Belum Ada</span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div
                                                    class="col-12 col-md-4 d-flex flex-column flex-md-column align-items-start align-items-md-center mb-2 mb-md-0">
                                                    <div>Security</div>
                                                    <div>
                                                        @if ($item->is_blacklisted_active)
                                                            <span class="badge bg-dark text-dark-fg">Blacklisted</span>
                                                        @elseif($securityStatus == 'on_review')
                                                            <span class="badge bg-yellow text-yellow-fg">On
                                                                Review</span>
                                                        @elseif($securityStatus == 'approved')
                                                            <span class="badge bg-teal text-teal-fg">Disetujui</span>
                                                        @elseif($securityStatus == 'rejected')
                                                            <span class="badge bg-red text-red-fg">Ditolak</span>
                                                        @else
                                                            <span class="badge bg-secondary text-secondary-fg">Belum Ada</span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div
                                                    class="col-12 col-md-4 d-flex flex-column flex-md-column align-items-start align-items-md-center">
                                                    <div>HSE</div>
                                                    <div>
                                                        @if ($item->is_blacklisted_active)
                                                            <span class="badge bg-dark text-dark-fg">Blacklisted</span>
                                                        @elseif($item->induction_card_number)
                                                            <span class="badge bg-teal text-teal-fg">Disetujui</span>
                                                        @else
                                                            <span class="badge bg-yellow text-yellow-fg">On
                                                                Review</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                        </td>
                                        <td>
                                            @if (!$item->is_blacklisted_active && Auth::user()->role == 'medical')
                                                <button class="btn btn-sm btn-outline-teal"
                                                    wire:click="modalUploadMCU({{ $item->id }})">Setujui
                                                    Verifikasi
                                                    Medical</button>
                                                <br>
                                                <button class="btn btn-sm btn-outline-pink btn-decline-mcu"
                                                    data-id='{{ $item->id }}'>
                                                    Tolak Verfikasi MCU
                                                </button>
                                            @endif
                                            @if (!$item->is_blacklisted_active && Auth::user()->role == 'security')
                                                @if ($securityStatus === 'approved' || !empty($item->security_card_number))
                                                    <span class="badge bg-lime text-lime-fg">Security Sudah Diverifikasi</span>
                                                @else
                                                    <button class="btn btn-sm btn-outline-teal"
                                                        wire:click="modalVerificationSecurity({{ $item->id }})">
                                                        <i class='ti ti-check'></i>
                                                        Setujui Verifikasi Security
                                                    </button>
                                                    <br>
                                                    <button class="btn btn-sm btn-outline-pink btn-decline-security"
                                                        data-id='{{ $item->id }}'>
                                                        <i class='ti ti-x'></i>
                                                        Tolak Verfikasi Security
                                                    </button>
                                                @endif
                                            @endif
                                            @if (!$item->is_blacklisted_active && Auth::user()->role == 'hse')
                                                <button class="btn btn-sm btn-outline-teal"
                                                    wire:click="modalVerificationHSE({{ $item->id }})">Setujui
                                                    Verifikasi HSE</button>
                                                <br>
                                                <button class="btn btn-sm btn-outline-pink btn-decline-hse"
                                                    data-id='{{ $item->id }}'>
                                                    Tolak Verfikasi HSE
                                                </button>
                                            @endif
                                            @if (Auth::user()->role == 'administrator' && !$item->is_blacklisted_active)
                                                {{-- Button Dropdown --}}
                                                <div class="dropdown">
                                                    <button
                                                        class="btn btn-sm btn-outline-orange text-decoration-none dropdown-toggle"
                                                        type="button" data-bs-toggle="dropdown"
                                                        aria-expanded="false">
                                                        <i class="ti ti-checkbox"></i>
                                                        Menu Verifikasi
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li class="text-teal">
                                                            <button class="dropdown-item"
                                                                wire:click='modalUploadMCU({{ $item->id }})'>
                                                                <i class="ti ti-check"></i>
                                                                Setujui Verifikasi Medical
                                                            </button>
                                                        </li>
                                                        <li class="text-pink">
                                                            <button class="dropdown-item btn-decline-mcu"
                                                                wire:click='modalRejectMcu({{ $item->id }})'>
                                                                <i class="ti ti-x"></i>
                                                                Tolak Verfikasi MCU
                                                            </button>
                                                        </li>
                                                        @if ($securityStatus === 'approved' || !empty($item->security_card_number))
                                                            <li>
                                                                <span class="dropdown-item text-muted">
                                                                    <i class="ti ti-lock"></i>
                                                                    Security sudah diverifikasi
                                                                </span>
                                                            </li>
                                                        @else
                                                            <li class="text-teal">
                                                                <a class="dropdown-item"
                                                                    wire:click="modalVerificationSecurity({{ $item->id }})">
                                                                    <i class="ti ti-check"></i>
                                                                    Setujui Verifikasi Security
                                                                </a>
                                                            </li>
                                                            <li class="text-pink">
                                                                <a class="dropdown-item btn-decline-security"
                                                                    data-id='{{ $item->id }}'>
                                                                    <i class="ti ti-x"></i>
                                                                    Tolak Verfikasi Security
                                                                </a>
                                                            </li>
                                                        @endif
                                                        <li class="text-teal">
                                                            <a class="dropdown-item"
                                                                wire:click="modalVerificationHSE({{ $item->id }})">
                                                                <i class="ti ti-check"></i>
                                                                Setujui Verifikasi HSE
                                                            </a>
                                                        </li>
                                                        <li class="text-pink">
                                                            <a class="dropdown-item btn-decline-hse"
                                                                data-id='{{ $item->id }}'>
                                                                <i class="ti ti-x"></i>
                                                                Tolak Verfikasi HSE
                                                            </a>
                                                        </li>
                                                        <li class="text-dark">
                                                            <button type="button" class="dropdown-item btn-blacklist-worker"
                                                                data-id='{{ $item->id }}'
                                                                data-fullname='{{ $item->full_name }}'
                                                                data-nik='{{ $item->nik }}'>
                                                                <i class="ti ti-ban"></i>
                                                                Blacklist Pekerja
                                                            </button>
                                                        </li>
                                                    </ul>
                                                </div>
                                            @elseif (Auth::user()->role == 'administrator' && $item->is_blacklisted_active)
                                                <span class="badge bg-dark text-dark-fg">Blacklisted</span>
                                            @endif



                                        </td>

                                    </tr>
                                    @if ($expandedRowId === $item->id)
                                        <tr class="detail-row" id="detail-{{ $item->id }}"
                                            style="background: #f8f9fa;">
                                            <td colspan="13">
                                                <div class="p-3">
                                                    <strong>Detail Pekerja:</strong><br>
                                                    {{-- Add Space between list --}}
                                                    <ul class="">
                                                        <li class="mt-1">Nama: {{ $item->full_name ?? '-' }}</li>
                                                        <li class="mt-1">NIK: {{ $item->nik ?? '-' }}</li>
                                                        <li class="mt-1">Jenis Kelamin:
                                                            {{ Str::title($item->jenis_kelamin) ?? '-' }}
                                                        <li class="mt-1">Tempat & Tanggal lahir:
                                                            {{ $item->birth_place ?? '-' }},
                                                            @if ($item->birth_date)
                                                                {{ \Carbon\Carbon::parse($item->birth_date)->format('d-m-Y') }}
                                                            @else
                                                                -
                                                            @endif
                                                        <li class="mt-1">Perusahaan:
                                                            {{ $item->project_contractor->contractor->company_name ?? '-' }}
                                                        </li>
                                                        <li class="mt-1">Domisili: {{ $item->domicile ?? '-' }}</li>
                                                        <li class="mt-1">Verifikasi Medical:

                                                            @if ($medicalStatus == 'on_review')
                                                                <span class="badge bg-yellow text-yellow-fg">On
                                                                    Review</span>
                                                            @elseif($medicalStatus == 'approved')
                                                                <span
                                                                    class="badge bg-teal text-teal-fg">Disetujui</span>
                                                                |
                                                                <span
                                                                    class="badge bg-info text-info-fg">{{ $item->medical_review->status_mcu }}</span>
                                                            @elseif($medicalStatus == 'rejected')
                                                                <span class="badge bg-red text-red-fg"
                                                                    wire:click='showModalAlasanRejectMcu({{ $item->id }})'>Ditolak</span>
                                                            @else
                                                                <span class="badge bg-secondary text-secondary-fg">Belum Ada</span>
                                                            @endif
                                                        </li>
                                                        <li class="mt-1">
                                                            Verifikasi HSE:
                                                            @if ($item->induction_card_number)
                                                                <span
                                                                    class="badge bg-teal text-teal-fg">Disetujui</span>
                                                            @else
                                                                <span class="badge bg-yellow text-yellow-fg">On
                                                                    Review</span>
                                                            @endif
                                                        </li>
                                                        <li class="mt-1">Verifikasi Security:
                                                            @if ($securityStatus == 'on_review')
                                                                <span class="badge bg-yellow text-yellow-fg">On
                                                                    Review</span>
                                                            @elseif($securityStatus == 'approved')
                                                                <span
                                                                    class="badge bg-teal text-teal-fg">Disetujui</span>
                                                            @elseif($securityStatus == 'rejected')
                                                                <span class="badge bg-red text-red-fg">Ditolak</span>
                                                            @else
                                                                <span class="badge bg-secondary text-secondary-fg">Belum Ada</span>
                                                            @endif
                                                        </li>
                                                        <li class="mt-1">
                                                            No.Induction:
                                                            @if ($item->induction_card_number)
                                                                <span
                                                                    class="text-dark fw-bold">{{ $item->induction_card_number }}</span>
                                                            @else
                                                                <span class="text-muted">-</span>
                                                            @endif
                                                        </li>
                                                        <li class="mt-1">
                                                            No.ID Security:
                                                            @if ($item->security_card_number)
                                                                <span
                                                                    class="text-dark fw-bold">{{ $item->security_card_number }}</span>
                                                            @else
                                                                <span class="text-muted">-</span>
                                                            @endif
                                                        </li>
                                                        <li class="mt-1">
                                                            Tanggal Cetak ID Badge:
                                                            @if ($item->security_card_number && $item->induction_card_number)
                                                                <span
                                                                    class="text-dark fw-bold">{{ \Carbon\Carbon::parse($item->updated_at)->format('d-m-Y H:i') }}</span>
                                                            @else
                                                                <span class="text-muted">-</span>
                                                            @endif
                                                        </li>
                                                        <li class="mt-1">
                                                            Area:
                                                            @if (optional($item->security_review)->area)
                                                                <span
                                                                    class="text-dark fw-bold">{{ Str::title(Str::replace('-', ' ', optional($item->security_review)->area)) }}</span>
                                                            @else
                                                                <span class="text-muted">-</span>
                                                            @endif
                                                        </li>


                                                        <li class="mt-1">Foto:
                                                            @if ($item->photo)
                                                                <a href="{{ asset('uploads/employee_documents/' . $item->photo) }}"
                                                                    target="_blank"
                                                                    onclick="window.open(this.href, 'new', 'popup'); return false;">
                                                                    <img src="{{ asset('uploads/employee_documents/' . $item->photo) }}"
                                                                        style="max-width: 70px;" alt="">
                                                                </a>
                                                            @else
                                                                <span class="text-muted">Belum tersedia</span>
                                                            @endif
                                                        </li>

                                                        <li class="mt-1">Dokumen KTP:
                                                            @if ($item->ktp_document)
                                                                <a href="{{ asset('uploads/employee_documents/' . $item->ktp_document) }}"
                                                                    target="_blank"
                                                                    onclick="window.open(this.href, 'new', 'popup'); return false;">
                                                                    <i class="ti ti-file-text"></i> Lihat KTP
                                                                </a>
                                                            @else
                                                                <span class="text-muted">Belum tersedia</span>
                                                            @endif
                                                        </li>

                                                        <li class="mt-1">Dokumen SKCK:
                                                            @if ($item->skck_document)
                                                                <a href="{{ asset('uploads/employee_documents/' . $item->skck_document) }}"
                                                                    target="_blank"
                                                                    onclick="window.open(this.href, 'new', 'popup'); return false;">
                                                                    <i class="ti ti-file-text"></i> Lihat SKCK
                                                                </a>
                                                            @else
                                                                <span class="text-muted">Belum tersedia</span>
                                                            @endif
                                                        </li>

                                                        <li class="mt-1">Dokumen MCU:
                                                            @if (optional($item->medical_review)->mcu_document)
                                                                <a href="{{ asset('uploads/employee_documents/' . optional($item->medical_review)->mcu_document) }}"
                                                                    target="_blank"
                                                                    onclick="window.open(this.href, 'new', 'popup'); return false;">
                                                                    <i class="ti ti-file-text"></i> Lihat MCU
                                                                </a>
                                                            @else
                                                                <span class="text-muted">Belum tersedia</span>
                                                            @endif
                                                        </li>

                                                        <li class="mt-1">Dokumen Form B:
                                                            @if ($item->form_b_document)
                                                                <a href="{{ asset('uploads/employee_documents/' . $item->form_b_document) }}"
                                                                    target="_blank"
                                                                    onclick="window.open(this.href, 'new', 'popup'); return false;">
                                                                    <i class="ti ti-file-text"></i> Lihat Form B
                                                                </a>
                                                            @else
                                                                <span class="text-muted">Belum tersedia</span>
                                                            @endif
                                                        </li>

                                                        <li class="mt-1">Dokumen Justifikasi Usia:
                                                            @if ($item->age_justification_document)
                                                                <a href="{{ asset('uploads/employee_documents/' . $item->age_justification_document) }}"
                                                                    target="_blank"
                                                                    onclick="window.open(this.href, 'new', 'popup'); return false;">
                                                                    <i class="ti ti-file-text"></i> Lihat Justifikasi
                                                                </a>
                                                            @else
                                                                <span class="text-muted">Belum tersedia</span>
                                                            @endif
                                                        </li>

                                                    </ul>
                                                </div>
                                                @if (Auth::user()->role == 'administrator' && !$item->is_blacklisted_active)
                                                    <button class="btn btn-sm btn-outline-azure text-decoration-none"
                                                        wire:click='detailData({{ $item->id }})'>
                                                        <i class="ti ti-pencil"></i>
                                                        Edit
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center">
                                            <span class="text-muted">Tidak ada data pekerja yang ditemukan.</span>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer d-flex align-items-center">
                        <div class="pagination m-0 ms-auto">
                            {{ $employees->links() }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                @if (Auth::user()->role == 'medical')
                    {{-- remove icon list --}}
                    <ul style="list-style-type: none">
                        <li>Medical mohon upload dokumen MCU untuk melanjutkan Verifikasi Medical</li>
                        <li class='text-danger'> <i class="ti ti-alert-triangle"></i> Tim Medical Harap Cek Dokumen
                            Justifikasi Usia</li>
                    </ul>
                @endif
                @if (Auth::user()->role == 'security')
                    <ul class="">
                        <li>Security diharapkan input Nomor ID Security untuk melanjutkan Verifikasi Security</li>
                    </ul>
                @endif
                @if (Auth::user()->role == 'hse')
                    <ul class="">
                        <li>Medical diharapkan upload dokumen MCU untuk melanjutkan Verifikasi Medical</li>
                        <li>Security diharapkan input Nomor ID Security untuk melanjutkan Verifikasi Security</li>
                        <li>HSE diharapkan input Nomor Induction untuk melanjutkan Verifikasi HSE</li>
                    </ul>
                @endif
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalDocumentMCU" wire:ignore.self tabindex="-1"
        aria-labelledby="modalDocumentMCULabel" aria-hidden="true" backdrop="static">
        <div class="modal-dialog modal-xl modal-dialog-centered " role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Verifikasi Medical - MCU</h5>
                </div>
                <div class="modal-body" wire:ignore>
                    <div class="alert alert-info">
                        <div><strong>Nama Pekerja:</strong> <span id="mcu-worker-name">-</span></div>
                        <div><strong>Nama PT/Perusahaan:</strong> <span id="mcu-company-name">-</span></div>
                        <div><strong>Nama Project:</strong> <span id="mcu-project-name">-</span></div>
                    </div>

                    <form wire:submit.prevent="uploadMCUFile">

                        <div class="row">
                            <div class="mb-3 col-6">
                                <div>
                                    <label class="form-label">Hazard Status</label>
                                    <select class="form-select" id="select-hazard-status">
                                        <option value="low_risk" class="text-uppercase">Low Risk</option>
                                        <option value="medium_risk" class="text-uppercase">Medium Risk</option>
                                        <option value="high_risk" class="text-uppercase">High Risk</option>
                                    </select>
                                    @error('hazard_status')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Fit Status</label>
                                    <select class="form-select" id="select-fit-status">
                                        <option value="unfit" class="text-red">Unfit</option>
                                        <option value="follow_up" class="text-yellow">Follow Up</option>
                                        <option value="fit" class="text-green">Fit</option>
                                    </select>
                                    @error('fit_status')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="mb-3 col-6">
                                <label class="form-label">Catatan</label>
                                <textarea class="form-control" rows="6" placeholder="Catatan" wire:model="notes" id="notes"></textarea>
                                @error('notes')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        {{-- <div class="row">
                            <div class="mb-3 col-6">
                                <label for="mcu_document">Upload File MCU</label>
                                <input type="file" id="filepond-upload-mcu" class="filepond"
                                    accept="application/pdf" multiple="false" />
                                @error('mcu_document')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div> --}}
                    </form>

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" wire:click='uploadMCUFile'>Simpan</button>
                    <button class="btn btn-outline-secondary float-end" data-bs-dismiss="modal"> Batal </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalDetailData" wire:ignore.self tabindex="-1"
        aria-labelledby="modalDetailDataLabel" aria-hidden="true" backdrop="static">
        <div class="modal-dialog modal-xl modal-dialog-centered " role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-detail-title"> Title </h3>
                </div>
                <div class="modal-body">
                    <table class="table table-striped">
                        <tr>
                            <td width="160px"> Nama </td>
                            <td width="10px"> : </td>
                            <td id="selected_employee_name">{{ $selected_employee->full_name ?? '-' }} </td>
                        </tr>
                        <tr>
                            <td width="160px"> Tempat & Tgl Lahir </td>
                            <td width="10px"> : </td>
                            <td id="selected_employee_birth_place_date">{{ $selected_employee->birth_place ?? '-' }}
                                @if ($selected_employee && $selected_employee->birth_date)
                                    ({{ \Carbon\Carbon::parse($selected_employee->birth_date)->format('d-m-Y') }})
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td width="160px"> NIK </td>
                            <td width="10px"> : </td>
                            <td id="selected_employee_nik">{{ $selected_employee->nik ?? '-' }} </td>
                        </tr>
                        <tr>
                            <td width="160px"> Jabatan </td>
                            <td width="10px"> : </td>
                            <td id="selected_employee_position">{{ $selected_employee->position ?? '-' }} </td>
                        </tr>
                        <tr>
                            <td width="160px"> Kode Hazard </td>
                            <td width="10px"> : </td>
                            <td id="selected_employee_risk_notes">
                                {{ $selected_employee->medical_review->risk_notes ?? '-' }} </td>
                        </tr>
                        <tr>
                            <td width="160px"> KTP </td>
                            <td width="10px"> : </td>
                            <td id="selected_employee_ktp_document">
                                @if ($selected_employee && $selected_employee->ktp_document)
                                    <a href="{{ asset('uploads/employee_documents/' . $selected_employee->ktp_document) }}"
                                        target="_blank" class="text-decoration-none"
                                        onclick="window.open(this.href, 'new', 'popup'); return false;">
                                        <i class="ti ti-file-text"></i> Lihat File
                                    </a>
                                @else
                                    <span class="text-muted">Belum tersedia</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td width="160px"> SKCK </td>
                            <td width="10px"> : </td>
                            <td id="selected_employee_skck_document">
                                @if ($selected_employee && $selected_employee->skck_document)
                                    <a href="{{ asset('uploads/employee_documents/' . $selected_employee->skck_document) }}"
                                        target="_blank" class="text-decoration-none"
                                        onclick="window.open(this.href, 'new', 'popup'); return false;">
                                        <i class="ti ti-file-text"></i> Lihat File
                                    </a>
                                @else
                                    <span class="text-muted">Belum tersedia</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td width="160px"> Form B </td>
                            <td width="10px"> : </td>
                            <td id="selected_employee_form_b_document">
                                @if ($selected_employee && $selected_employee->form_b_document)
                                    <a href="{{ asset('uploads/employee_documents/' . $selected_employee->form_b_document) }}"
                                        target="_blank" class="text-decoration-none"
                                        onclick="window.open(this.href, 'new', 'popup'); return false;">
                                        <i class="ti ti-file-text"></i> Lihat File
                                    </a>
                                @else
                                    <span class="text-muted">Belum tersedia</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td width="160px"> MCU </td>
                            <td width="10px"> : </td>
                            <td id="selected_employee_mcu_document">
                                @if ($selected_employee && optional($selected_employee->medical_review)->mcu_document)
                                    <a href="{{ asset('uploads/employee_documents/' . optional($selected_employee->medical_review)->mcu_document) }}"
                                        target="_blank" class="text-decoration-none"
                                        onclick="window.open(this.href, 'new', 'popup'); return false;">
                                        <i class="ti ti-file-text"></i> Lihat File
                                    </a>
                                @else
                                    <span class="text-muted">Belum tersedia</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td width="160px"> Justifikasi Usia </td>
                            <td width="10px"> : </td>
                            <td id="selected_employee_age_justification_document">
                                @if ($selected_employee && $selected_employee->age_justification_document)
                                    <a href="{{ asset('uploads/employee_documents/' . $selected_employee->age_justification_document) }}"
                                        target="_blank" class="text-decoration-none"
                                        onclick="window.open(this.href, 'new', 'popup'); return false;">
                                        <i class="ti ti-file-text"></i> Lihat File
                                    </a>
                                @else
                                    <span class="text-muted">Belum tersedia</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td width="160px"> Pas Foto </td>
                            <td width="10px"> : </td>
                            <td id="selected_employee_photo">
                                @if ($selected_employee && $selected_employee->photo)
                                    <a href="{{ asset('uploads/employee_documents/' . $selected_employee->photo) }}"
                                        target="_blank" class="text-decoration-none"
                                        onclick="window.open(this.href, 'new', 'popup'); return false;">
                                        <img src="{{ asset('uploads/employee_documents/' . $selected_employee->photo) }}"
                                            alt="Foto Pekerja" class="img-thumbnail" style="max-width: 150px;">
                                    </a>
                                    <br>
                                    <button class="btn btn-sm btn-outline-teal btn-edit-photo">
                                        <i class="ti ti-pencil"></i> Edit
                                    </button>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="modal-footer">
                    @if (Auth::user()->role == 'administrator')
                        <button class="btn btn-primary" wire:click='editEmployee'> <i class="ti ti-pencil"></i> Edit
                            Data</button>
                    @endif
                    <button class="btn btn-outline-secondary float-end" data-bs-dismiss="modal"> Tutup </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalTransferEmployee" wire:ignore.self tabindex="-1"
        aria-labelledby="modalTransferEmployeeLabel" aria-hidden="true" backdrop="static">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document" style="max-width: 1200px;">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h3 class="modal-detail-title mb-0"> Pindah PT / Project </h3>
                        <div class="text-secondary">Pindahkan pekerja ke PT/project lain tanpa mengubah histori data lama.</div>
                    </div>
                </div>
                <form wire:submit.prevent="submitTransferEmployee">
                    <div class="modal-body">
                        <div class="border rounded-3 p-3 bg-body-tertiary mb-3">
                            <div class="fw-semibold text-uppercase text-secondary small mb-1">Data Pekerja</div>
                            <div class="row g-2">
                                <div class="col-12">
                                    <div class="form-label text-secondary mb-1">NIK Pekerja</div>
                                    <div class="fw-bold fs-4">{{ $selected_employee->nik ?? '-' }}</div>
                                </div>
                                <div class="col-12">
                                    <div class="form-label text-secondary mb-1">Nama Pekerja</div>
                                    <div class="fw-bold fs-3">{{ $transfer_employee_name ?? '-' }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 align-items-stretch">
                            <div class="col-lg-5">
                                <div class="border rounded-3 h-100 p-3">
                                    <div class="fw-semibold text-uppercase text-secondary small mb-3">Asal</div>
                                    <div class="mb-3">
                                        <div class="text-secondary small mb-1">Perusahaan / Project</div>
                                        <div class="fw-semibold fs-5">{{ $transfer_employee_company ?? '-' }}</div>
                                        <div class="text-muted">{{ $transfer_employee_project ?? '-' }}</div>
                                    </div>
                                    <div>
                                        <div class="text-secondary small mb-1">Jabatan</div>
                                        <div class="badge bg-dark text-dark-fg fs-6 px-3 py-2">
                                            {{ $selected_employee->position ?? '-' }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-2 d-flex align-items-center justify-content-center">
                                <div class="transfer-switch-badge">
                                    <i class="ti ti-arrows-exchange fs-1"></i>
                                </div>
                            </div>

                            <div class="col-lg-5">
                                <div class="border rounded-3 h-100 p-3">
                                    <div class="fw-semibold text-uppercase text-secondary small mb-3">Tujuan</div>
                                    <div class="mb-3">
                                        <label class="form-label">PT / Project Tujuan</label>
                                        <div wire:ignore>
                                            <select id="transfer-project-select"
                                                class="form-select @error('transfer_project_contractor_id') is-invalid @enderror">
                                                <option value="">Pilih project tujuan</option>
                                                @foreach ($transferProjects as $project)
                                                    <option value="{{ $project->id }}"
                                                        data-company="{{ $project->contractor->company_name }}"
                                                        data-contract="{{ $project->memo_number }}"
                                                        data-end-date="{{ \Carbon\Carbon::parse($project->end_date)->format('d-m-Y') }}">
                                                        {{ $project->contractor->company_name }} - {{ $project->project_name }}
                                                        (Kontrak s/d {{ \Carbon\Carbon::parse($project->end_date)->format('d-m-Y') }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @error('transfer_project_contractor_id')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="border rounded-3 bg-body-secondary p-3 mb-3">
                                        <div class="small text-secondary mb-1">PT / Perusahaan Tujuan</div>
                                        <div class="fw-semibold" id="transfer-target-company">-</div>
                                        <div class="small text-secondary mt-2 mb-1">Kontrak Kerja</div>
                                        <div class="fw-semibold" id="transfer-target-contract">-</div>
                                        <div class="small text-secondary mt-2 mb-1">Tanggal Selesai Project</div>
                                        <div class="fw-semibold" id="transfer-target-end-date">-</div>
                                    </div>
                                    <div>
                                        <label class="form-label">Jabatan Baru</label>
                                        <input type="text"
                                            class="form-control @error('transfer_position') is-invalid @enderror"
                                            placeholder="Contoh: Welder Inspection" wire:model.defer="transfer_position">
                                        @error('transfer_position')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info mt-3 mb-0">
                            Tanggal berlaku badge dan ID card akan mengikuti <strong>tanggal selesai project tujuan</strong>.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-outline-secondary float-end" data-bs-dismiss="modal" type="button">Tutup</button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="submitTransferEmployee">Simpan Pindah</span>
                            <span wire:loading wire:target="submitTransferEmployee">Menyimpan...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="cropperModal" class="modal-cropper">
        <div class="modal-content-cropper">
            <div class="cropper-container">
                <img id="cropperImage" style="max-width:100%; max-height:100%;">
            </div>
            <div class="cropper-actions">
                <button id="cropConfirm" class="btn btn-primary">Potong & Simpan</button>
                <button id="cropCancel" class="btn btn-secondary">Cancel</button>
            </div>
        </div>
    </div>

    {{-- Modal Edit Employee --}}
    <div class="modal fade" id="modalEditEmployee" wire:ignore.self tabindex="-1"
        aria-labelledby="modalEditEmployeeLabel" aria-hidden="true" backdrop="static">
        <div class="modal-dialog modal-xl modal-dialog-centered " role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-detail-title"> Edit Data Pekerja </h3>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_employee_name"> Nama </label>
                                <input type="text" class="form-control" id="edit_employee_name"
                                    wire:model="edit_employee_name">
                            </div>
                            <div class="form-group">
                                <label for="edit_employee_nik"> NIK </label>
                                <input type="text" class="form-control" id="edit_employee_nik"
                                    wire:model="edit_employee_nik">
                            </div>
                            <div class="form-group">
                                <label for="edit_employee_position"> Jabatan </label>
                                <input type="text" class="form-control" id="edit_employee_position"
                                    wire:model="edit_employee_position">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" wire:click='updateEmployeeDetail'> <i class="ti ti-pencil"></i>
                        Simpan
                        Perubahan</button>
                    <button class="btn btn-outline-secondary float-end" data-bs-dismiss="modal"> Tutup </button>
                </div>
            </div>
        </div>
    </div>
</div>


@push('scripts')
    <script>
        document.addEventListener('livewire:init', function() {

            var bootstrap = tabler.bootstrap;

            const modalDocumentMCU = new bootstrap.Modal(document.getElementById('modalDocumentMCU'), {});

            const modalDetailData = new bootstrap.Modal(document.getElementById('modalDetailData'), {});
            const modalTransferEmployeeElement = document.getElementById('modalTransferEmployee');
            const modalTransferEmployee = new bootstrap.Modal(modalTransferEmployeeElement, {});
            const transferProjectSelect = document.getElementById('transfer-project-select');
            const transferTargetCompany = document.getElementById('transfer-target-company');
            const transferTargetContract = document.getElementById('transfer-target-contract');
            const transferTargetEndDate = document.getElementById('transfer-target-end-date');
            let transferProjectTomSelect = null;
            let pendingTransferSelectedValue = null;
            let transferTomStyleInjected = false;

            const ensureTransferTomStyle = () => {
                if (transferTomStyleInjected) {
                    return;
                }

                const style = document.createElement('style');
                style.innerHTML = `
                    .ts-dropdown {
                        background-color: #fff !important;
                        border: 1px solid #dee2e6;
                        border-radius: 0.375rem;
                        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15);
                        z-index: 2065 !important;
                        margin-top: 0.25rem;
                        padding: 0.25rem 0;
                    }
                    .ts-dropdown .ts-dropdown-content {
                        max-height: 300px;
                        overflow-y: auto;
                    }
                    .ts-wrapper,
                    .ts-wrapper.multi,
                    .ts-wrapper.single {
                        z-index: 1040;
                    }
                    .ts-dropdown .option:hover,
                    .ts-dropdown .option.active {
                        background-color: #f8f9fa !important;
                        color: #000 !important;
                    }
                    .ts-control {
                        border: 1px solid #dee2e6;
                        border-radius: 0.375rem;
                        min-height: calc(2.25rem + 2px);
                        box-shadow: none;
                        background-color: #fff;
                    }
                    .ts-control input {
                        color: inherit;
                    }
                    .ts-wrapper.form-select-sm .ts-control {
                        min-height: calc(1.8125rem + 2px);
                        font-size: 0.875rem;
                        padding-top: 0.125rem;
                        padding-bottom: 0.125rem;
                    }
                    .ts-control.focus {
                        border-color: #86b7fe;
                        box-shadow: 0 0 0 .25rem rgba(13,110,253,.25);
                    }
                    .ts-wrapper.invalid .ts-control {
                        border-color: #dc3545 !important;
                    }
                    .ts-option-custom {
                        padding: 0.4rem 0.75rem;
                        cursor: pointer;
                    }
                    .ts-item-custom {
                        padding: 0.25rem 0.5rem;
                        border-radius: 0.25rem;
                        background: #fff;
                    }
                    .ts-dropdown[data-bs-theme="dark"],
                    .ts-dropdown[data-bs-theme="dark"] .ts-dropdown-content,
                    .ts-dropdown.ts-dropdown-dark,
                    .ts-dropdown.ts-dropdown-dark .ts-dropdown-content {
                        background-color: #1f2937 !important;
                        border-color: #374151 !important;
                        color: #e5e7eb !important;
                    }
                    .ts-dropdown[data-bs-theme="dark"] .option,
                    .ts-dropdown.ts-dropdown-dark .option {
                        color: #e5e7eb !important;
                        background-color: transparent !important;
                    }
                    .ts-dropdown[data-bs-theme="dark"] .option:hover,
                    .ts-dropdown[data-bs-theme="dark"] .option.active,
                    .ts-dropdown.ts-dropdown-dark .option:hover,
                    .ts-dropdown.ts-dropdown-dark .option.active {
                        background-color: #334155 !important;
                        color: #fff !important;
                    }
                    [data-bs-theme="dark"] .ts-control,
                    html[data-bs-theme="dark"] .ts-control,
                    body[data-bs-theme="dark"] .ts-control {
                        background-color: var(--tblr-bg-surface, #111827) !important;
                        border-color: var(--tblr-border-color, #374151) !important;
                        color: var(--tblr-body-color, #e5e7eb) !important;
                    }
                    [data-bs-theme="dark"] .ts-control .item,
                    [data-bs-theme="dark"] .ts-control .input {
                        color: var(--tblr-body-color, #e5e7eb) !important;
                    }
                    [data-bs-theme="dark"] .ts-control .item.is-placeholder,
                    [data-bs-theme="dark"] .ts-control .placeholder {
                        background-color: transparent !important;
                        color: #9ca3af !important;
                    }
                    [data-bs-theme="dark"] .ts-control.focus {
                        border-color: #60a5fa !important;
                        box-shadow: 0 0 0 .25rem rgba(96,165,250,.35) !important;
                    }
                    [data-bs-theme="dark"] .ts-dropdown,
                    html[data-bs-theme="dark"] .ts-dropdown,
                    body[data-bs-theme="dark"] .ts-dropdown {
                        background-color: var(--tblr-bg-surface, #1f2937) !important;
                        border-color: var(--tblr-border-color, #374151) !important;
                        box-shadow: 0 .5rem 1rem rgba(0,0,0,.35) !important;
                        color: var(--tblr-body-color, #e5e7eb) !important;
                        z-index: 2065 !important;
                    }
                    [data-bs-theme="dark"] .ts-dropdown .ts-dropdown-content {
                        background-color: var(--tblr-bg-surface, #1f2937) !important;
                    }
                    [data-bs-theme="dark"] .ts-dropdown .option {
                        color: var(--tblr-body-color, #e5e7eb) !important;
                    }
                    [data-bs-theme="dark"] .ts-dropdown .option:hover,
                    [data-bs-theme="dark"] .ts-dropdown .option.active {
                        background-color: #334155 !important;
                        color: #fff !important;
                    }
                    [data-bs-theme="dark"] .ts-item-custom {
                        background: var(--tblr-bg-surface, #111827) !important;
                        color: var(--tblr-body-color, #e5e7eb) !important;
                    }
                `;

                document.head.appendChild(style);
                transferTomStyleInjected = true;
            };

            const initTransferProjectSelect = (selectedValue = null) => {
                if (!transferProjectSelect) {
                    return;
                }

                ensureTransferTomStyle();

                if (transferProjectTomSelect) {
                    transferProjectTomSelect.destroy();
                    transferProjectTomSelect = null;
                }

                transferProjectTomSelect = new TomSelect(transferProjectSelect, {
                    create: false,
                    allowEmptyOption: true,
                    placeholder: 'Pilih project tujuan',
                    dropdownParent: document.body,
                    closeAfterSelect: true,
                    openOnFocus: true,
                    searchField: ['text'],
                    maxOptions: null,
                    copyClassesToDropdown: false,
                    render: {
                        option: (data, escape) => `<div class="ts-option-custom">${escape(data.text)}</div>`,
                        item: (data, escape) => `<div class="ts-item-custom">${escape(data.text)}</div>`,
                    },
                    onChange: function(value) {
                        @this.set('transfer_project_contractor_id', value ? parseInt(value, 10) : null, false);
                        updateTransferTargetPreview(value);
                    }
                });

                transferProjectTomSelect.on('dropdown_open', () => {
                    if (!transferProjectTomSelect?.control || !transferProjectTomSelect?.dropdown) {
                        return;
                    }

                    const anchor = transferProjectTomSelect.wrapper ||
                        transferProjectTomSelect.control.closest('.ts-wrapper') ||
                        transferProjectTomSelect.control;

                    const rect = anchor.getBoundingClientRect();

                    transferProjectTomSelect.dropdown.style.width = `${rect.width}px`;
                    transferProjectTomSelect.dropdown.style.minWidth = `${rect.width}px`;
                    transferProjectTomSelect.dropdown.style.left = `${rect.left + window.scrollX}px`;
                    transferProjectTomSelect.dropdown.style.top = `${rect.bottom + window.scrollY}px`;
                    transferProjectTomSelect.dropdown.style.marginLeft = '0';

                    const theme =
                        document.documentElement.getAttribute('data-bs-theme') ||
                        document.body.getAttribute('data-bs-theme') ||
                        'light';

                    transferProjectTomSelect.dropdown.setAttribute('data-bs-theme', theme);
                    transferProjectTomSelect.dropdown.classList.toggle('ts-dropdown-dark', theme === 'dark');

                    if (modalTransferEmployeeElement?.classList.contains('show')) {
                        transferProjectTomSelect.dropdown.style.zIndex = '2065';
                    }
                });

                transferProjectTomSelect.setValue(selectedValue ? String(selectedValue) : '', true);

                updateTransferTargetPreview(selectedValue ? String(selectedValue) : '');
            };

            const updateTransferTargetPreview = (value) => {
                if (!transferProjectSelect) {
                    return;
                }

                const selectedOption = transferProjectSelect.querySelector(`option[value="${value}"]`);

                if (!selectedOption) {
                    if (transferTargetCompany) transferTargetCompany.textContent = '-';
                    if (transferTargetContract) transferTargetContract.textContent = '-';
                    if (transferTargetEndDate) transferTargetEndDate.textContent = '-';
                    return;
                }

                if (transferTargetCompany) {
                    transferTargetCompany.textContent = selectedOption.dataset.company || '-';
                }

                if (transferTargetContract) {
                    transferTargetContract.textContent = selectedOption.dataset.contract || '-';
                }

                if (transferTargetEndDate) {
                    transferTargetEndDate.textContent = selectedOption.dataset.endDate || '-';
                }
            };

            document.addEventListener('click', function(e) {
                const blacklistBtn = e.target.closest('.btn-blacklist-worker');
                if (!blacklistBtn) return;

                const workerId = Number(blacklistBtn.dataset.id);
                const fullname = blacklistBtn.dataset.fullname || '-';
                const nik = blacklistBtn.dataset.nik || '-';
                let selectedBlacklistUntil = null;

                Swal.fire({
                    title: 'Blacklist Pekerja',
                    html: `
                        <div class="text-start">
                            <div class="mb-2"><b>Nama:</b> ${fullname}</div>
                            <div class="mb-3"><b>NIK:</b> ${nik}</div>

                            <div class="mb-3">
                                <label class="form-label mb-1">Jenis Blacklist</label>
                                <select id="blacklistType" class="form-select">
                                    <option value="temporary">Sementara</option>
                                    <option value="permanent">Permanen</option>
                                </select>
                            </div>

                            <div class="mb-3" id="blacklistEndDateGroup">
                                <label class="form-label mb-1">Sampai Tanggal (untuk sementara)</label>
                                <div class="input-icon mb-2">
                                    <input class="form-control" placeholder="Sampai Tanggal"
                                        id="blacklist-end-date-picker-icon" autocomplete="off" />
                                    <span class="input-icon-addon">
                                        <i class="ti ti-calendar"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="mb-1">
                                <label class="form-label mb-1">Alasan</label>
                                <textarea id="blacklistReason" class="form-control" rows="4" placeholder="Masukkan alasan blacklist"></textarea>
                            </div>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Blacklist',
                    cancelButtonText: 'Batal',
                    didOpen: () => {
                        const typeElement = document.getElementById('blacklistType');
                        const endDateElement = document.getElementById('blacklist-end-date-picker-icon');
                        const endDateGroup = document.getElementById('blacklistEndDateGroup');

                        const tomType = new TomSelect(typeElement, {
                            create: false,
                            dropdownParent: document.querySelector('.swal2-popup')
                        });

                        const picker = new Litepicker({
                            element: endDateElement,
                            singleMode: true,
                            minDate: new Date(),
                            format: 'DD/MM/YYYY',
                            buttonText: {
                                previousMonth: `<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 6l-6 6l6 6" /></svg>`,
                                nextMonth: `<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 6l6 6l-6 6" /></svg>`,
                            },
                            dropdowns: {
                                minYear: new Date().getFullYear(),
                                maxYear: new Date().getFullYear() + 3,
                                months: true,
                                years: true,
                            },
                            setup: (litepicker) => {
                                litepicker.on('selected', (date1) => {
                                    selectedBlacklistUntil = date1 ? date1.format('YYYY-MM-DD') : null;
                                });
                            }
                        });

                        const toggleEndDateVisibility = (typeValue) => {
                            const isTemporary = typeValue === 'temporary';
                            endDateGroup.style.display = isTemporary ? 'block' : 'none';

                            if (!isTemporary) {
                                selectedBlacklistUntil = null;
                                endDateElement.value = '';
                                picker.clearSelection();
                            }
                        };

                        toggleEndDateVisibility(tomType.getValue());
                        tomType.on('change', toggleEndDateVisibility);
                    },
                    preConfirm: () => {
                        const type = document.getElementById('blacklistType')?.value;
                        const endDate = selectedBlacklistUntil;
                        const reason = document.getElementById('blacklistReason')?.value?.trim();

                        if (!reason) {
                            Swal.showValidationMessage('Alasan blacklist wajib diisi');
                            return false;
                        }

                        if (type === 'temporary' && !endDate) {
                            Swal.showValidationMessage('Tanggal akhir wajib diisi untuk blacklist sementara');
                            return false;
                        }

                        return {
                            type,
                            endDate,
                            reason
                        };
                    }
                }).then((result) => {
                    if (result.isConfirmed && result.value) {
                        Livewire.dispatch('submitBlacklistWorker', {
                            id: workerId,
                            type: result.value.type,
                            until: result.value.type === 'temporary' ? result.value.endDate : null,
                            reason: result.value.reason,
                        });
                    }
                });
            });

            // FilePond.registerPlugin(
            //     FilePondPluginFileValidateType,
            //     FilePondPluginFileValidateSize,
            //     FilePondPluginPdfPreview,
            // );

            // const inputElement = document.getElementById('filepond-upload-mcu');
            // let uploadedDocMcuTmp = null;

            const selectEl = document.getElementById('select-hazard-status');
            const selectFitStatusEl = document.getElementById('select-fit-status');
            const modalDocumentMCUElement = document.getElementById('modalDocumentMCU');

            Livewire.on('showModalUploadMCU', (payload) => {
                modalDocumentMCU.show();
                console.log('Show modal upload mcu');

                modalDocumentMCUElement.addEventListener('shown.bs.modal', () => {
                    const detail = payload?.data || {};
                    const workerName = detail?.full_name || '-';
                    const companyName = detail?.project_contractor?.contractor?.company_name || '-';
                    const projectName = detail?.project_contractor?.project_name || '-';

                    const workerNameEl = document.getElementById('mcu-worker-name');
                    const companyNameEl = document.getElementById('mcu-company-name');
                    const projectNameEl = document.getElementById('mcu-project-name');

                    if (workerNameEl) workerNameEl.textContent = workerName;
                    if (companyNameEl) companyNameEl.textContent = companyName;
                    if (projectNameEl) projectNameEl.textContent = projectName;

                    document.querySelector('#modalDocumentMCU .modal-title').textContent =
                        `Verifikasi Medical - MCU: ${workerName} | ${companyName} | ${projectName}`;

                    // const mcuPond = FilePond.create(inputElement, {
                    //     allowMultiple: false,
                    //     maxFiles: 1,
                    //     // excel file allowed
                    //     acceptedFileTypes: [
                    //         'application/pdf'
                    //     ],
                    //     fileValidateTypeLabelExpectedTypes: 'Hanya file PDF yang diperbolehkan',
                    //     maxFileSize: '2MB',
                    //     labelIdle: `<div class="text-center mb-2"> <i class="ti ti-upload fs-2 mb-3 text-muted"></i><br><strong>Drag & drop</strong> atau <span class="filepond--label-action">klik di sini</span> untuk upload</div>`,
                    //     credits: false,
                    //     storeAsFile: true,
                    //     labelFileTypeNotAllowed: 'Hanya file PDF yang diperbolehkan',
                    //     labelMaxFileSizeExceeded: 'Ukuran file terlalu besar (maksimal 2MB)',
                    //     labelMaxFileSize: 'Maksimal ukuran file adalah 2MB',
                    //     labelFileProcessingError: 'Terjadi kesalahan saat mengunggah file',
                    //     labelFileProcessing: 'Mengunggah file...',
                    //     labelFileProcessingComplete: 'File berhasil diunggah',
                    //     labelFileProcessingAborted: 'Pengunggahan file dibatalkan',
                    // });

                    // mcuPond.on('addfile', (error, file) => {
                    //     if (!error) {
                    //         inputElement.dispatchEvent(new Event('change', {
                    //             bubbles: true
                    //         }));
                    //         const fileData = file.file;
                    //         @this.upload('mcu_document', fileData, (fileName) => {
                    //             console.log('File uploaded successfully:',
                    //                 fileName);
                    //             uploadedDocMcuTmp = fileName;
                    //         }, (error) => {
                    //             console.error('Error uploading file:', error);
                    //             // Show error message to user
                    //             Swal.fire({
                    //                 icon: 'error',
                    //                 title: 'Upload Gagal',
                    //                 text: 'Terjadi kesalahan saat mengunggah file. Silakan coba lagi.',
                    //                 confirmButtonText: 'OK'
                    //             });
                    //         });
                    //     }
                    // });

                    // mcuPond.on('removefile', (err, file) => {
                    //     console.log('File removed');
                    //     inputElement.value = '';
                    //     inputElement.dispatchEvent(new Event('change', {
                    //         bubbles: true
                    //     }));

                    //     if (uploadedDocMcuTmp) {
                    //         @this.removeUpload('mcu_document', uploadedDocMcuTmp);
                    //         uploadedDocMcuTmp = null;
                    //     }
                    // });

                    if (!selectEl.tomselect) {
                        // TomSelect
                        new TomSelect(selectEl, {
                            copyClassesToDropdown: false,
                            // dropdownParent: "body",
                            controlInput: "<input>",
                            render: {
                                item: function(data, escape) {
                                    if (data.customProperties) {
                                        return '<div><span class="dropdown-item-indicator">' +
                                            data
                                            .customProperties + "</span>" + escape(data
                                                .text) +
                                            "</div>";
                                    }
                                    return "<div>" + escape(data.text) + "</div>";
                                },
                                option: function(data, escape) {
                                    if (data.customProperties) {
                                        return '<div><span class="dropdown-item-indicator">' +
                                            data
                                            .customProperties + "</span>" + escape(data
                                                .text) +
                                            "</div>";
                                    }
                                    return "<div>" + escape(data.text) + "</div>";
                                },
                            },
                            onChange: function(value) {
                                @this.set('hazard_status', value);
                            }
                        })
                    }

                    if (!selectFitStatusEl.tomselect) {
                        // TomSelect
                        new TomSelect(selectFitStatusEl, {
                            copyClassesToDropdown: false,
                            // dropdownParent: "body",
                            controlInput: "<input>",
                            render: {
                                item: function(data, escape) {
                                    if (data.customProperties) {
                                        return '<div><span class="dropdown-item-indicator">' +
                                            data
                                            .customProperties + "</span>" + escape(data
                                                .text) +
                                            "</div>";
                                    }
                                    return "<div>" + escape(data.text) + "</div>";
                                },
                                option: function(data, escape) {
                                    if (data.customProperties) {
                                        return '<div><span class="dropdown-item-indicator">' +
                                            data
                                            .customProperties + "</span>" + escape(data
                                                .text) +
                                            "</div>";
                                    }
                                    return "<div>" + escape(data.text) + "</div>";
                                },
                            },
                            onChange: function(value) {
                                @this.set('fit_status', value);
                            }
                        })
                    }

                    document.getElementById('notes').onchange = (e) => {
                        @this.set('notes', e.target.value);
                    }
                }, {
                    once: true
                });

                modalDocumentMCUElement.addEventListener('hidden.bs.modal', () => {
                    // remove Filepond instance

                    // if (Filepond.find(document.getElementById('filepond-upload-mcu'))) {
                    //     Filepond.find(document.getElementById('filepond-upload-mcu'))?.removeFile();
                    //     Filepond.find(document.getElementById('filepond-upload-mcu'))?.destroy();
                    // }

                    // uploadedDocMcuTmp = null;

                    // Reset date pickers
                    const startDatepicker = document.getElementById('start-date-picker-icon-mcu');
                    if (startDatepicker) {
                        startDatepicker.value = '';
                    }
                    const endDatepicker = document.getElementById('end-date-picker-icon-mcu');
                    if (endDatepicker) {
                        endDatepicker.value = '';
                    }
                }, {
                    once: true
                })
            })

            Livewire.on('showModalTransferEmployee', (payload) => {
                pendingTransferSelectedValue = payload?.selectedProjectId ?? null;
                modalTransferEmployee.show();
            });

            if (modalTransferEmployeeElement) {
                modalTransferEmployeeElement.addEventListener('shown.bs.modal', () => {
                    initTransferProjectSelect(pendingTransferSelectedValue);
                });
            }

            Livewire.on('hideModalTransferEmployee', () => {
                modalTransferEmployee.hide();
                pendingTransferSelectedValue = null;
                if (transferProjectTomSelect) {
                    transferProjectTomSelect.destroy();
                    transferProjectTomSelect = null;
                }
            });

            Livewire.on('uploadMCUSuccess', () => {
                modalDocumentMCU.hide();

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Verifikasi Medical MCU berhasil disimpan.',
                    showConfirmButton: true,
                    confirmButtonText: 'OK',
                    timer: 3000,
                    timerProgressBar: true,
                });
            });

            Livewire.on('showModalRejectMcu', (e) => {
                const data = e.data;
                // console.log(data)
                Swal.fire({
                        title: 'Tolak Verifikasi',
                        html: `<p>Anda yakin ingin menolak Verifikasi MCU Pekerja ini ?</p>
                        <div class="text-start mb-2">
                            <label>Alasan :</label>
                            <select id="alasan-select" class="form-control mb-3">
                                <option selected disabled readonly hidden>Pilih alasan penolakan</option>
                                <option value="unfit">UNFIT</option>
                                <option value="follow_up">Follow Up</option>
                            </select>
                        </div>
                        <div class="text-start mb-2">
                            <label>Catatan :</label>
                            <input type="text" id="alasan-lain" class="form-control mb-3" placeholder="Keterangan Catatan">
                        </div>
                <small class="text-muted">Silahkan pilih alasan penolakan dan tambahkan keterangan tambahan</small>
                <br>
                <small class="text-muted">Contoh - Alasan : Follow Up | Keterangan : Dokumen Justifikasi Tidak sesuai</small>`,
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Simpan',
                        cancelButtonText: 'Batal',
                        preConfirm: () => {
                            const selectValue = document.getElementById('alasan-select')
                                .value;
                            const inputValue = document.getElementById('alasan-lain')
                                .value;

                            if (!selectValue) {
                                Swal.showValidationMessage(
                                    'Silakan pilih alasan penolakan terlebih dahulu.'
                                );
                                return false;
                            }

                            return {
                                alasan: selectValue,
                                keterangan: inputValue
                            };
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            console.log('Alasan:', result.value.alasan);
                            console.log('Keterangan Tambahan:', result.value.keterangan);
                            Livewire.dispatch('rejectMcu', {
                                id: data.id,
                                alasan: result.value.alasan,
                                keterangan: result.value.keterangan
                            });
                        }
                    });
            })


            var detailDataFromLivewire = null;

            Livewire.on('showModalDetail', (e) => {
                detailDataFromLivewire = e.data;
                console.log(detailDataFromLivewire); // Debug, pastikan data masuk

                Swal.fire({
                    title: 'Loading',
                    html: 'Memuat detail data pekerja...',
                    didOpen: () => {
                        Swal.showLoading();
                    },
                    allowOutsideClick: false,
                    showConfirmButton: false,
                });

                modalDetailData.show();
            })

            document.getElementById('modalDetailData').addEventListener('shown.bs.modal', (e) => {
                Swal.close(); // Tutup Swal loading
                document.querySelector('.modal-detail-title').textContent =
                    `Detail Data Pekerja: ${detailDataFromLivewire.full_name} - ${detailDataFromLivewire.project_contractor.contractor.company_name}`;

                let cropper = null;
                let isCropping = false;
                let uploadPhotoTmp = null;
                if (cropper) cropper.destroy();

                var btnEditPhoto = document.querySelector('.btn-edit-photo');
                btnEditPhoto.onclick = () => {
                    const modal = document.getElementById('cropperModal');
                    const img = document.getElementById('cropperImage');
                    const srcImg = detailDataFromLivewire.photo ?
                        `{{ asset('uploads/employee_documents/') }}/${detailDataFromLivewire.photo}` :
                        '';

                    img.src = srcImg;

                    modal.style.display = 'flex';

                    if (cropper) cropper.destroy();

                    cropper = new Cropper(img, {
                        aspectRatio: 3 / 4,
                        viewMode: 1
                    });
                };

                var btnCropConfirm = document.querySelector('#cropConfirm');
                btnCropConfirm.onclick = () => {
                    if (!cropper) return;

                    cropper.getCroppedCanvas({
                        width: 300,
                        height: 400
                    }).toBlob((blob) => {
                        let newFilePhotoName = detailDataFromLivewire.photo +
                            '-cropped-' + new Date().getTime() + '.jpg';

                        console.log('Cropped file name:', newFilePhotoName);
                        console.log('Cropped file blob:', blob);

                        const file = new File([blob], newFilePhotoName, {
                            type: 'image/jpeg',
                            lastModified: new Date().getTime()
                        });

                        console.log('File to upload:', file);

                        Swal.fire({
                            title: 'Apakah Anda yakin ingin menyimpan perubahan ini?',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Ya, Simpan!',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                @this.upload('croppedImage', file, (fileName) => {
                                    console.log(
                                        'File cropped and uploaded successfully:',
                                        fileName);
                                    uploadPhotoTmp = fileName;

                                    Livewire.dispatch('updatePhoto', {
                                        id: detailDataFromLivewire
                                            .id,
                                        photo: uploadPhotoTmp
                                    });
                                }, (error) => {
                                    console.error(
                                        'Error uploading cropped file:',
                                        error);
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Upload Gagal',
                                        text: 'Terjadi kesalahan saat edit foto. Silakan coba lagi.',
                                        confirmButtonText: 'OK'
                                    });
                                    const modal = document.getElementById(
                                        'cropperModal');
                                    modal.style.display = 'none';
                                    if (cropper) cropper.destroy();
                                }, (evt) => {
                                    console.log('Upload progress:', evt);
                                    Swal.fire({
                                        title: 'Sedang Proses Crop & Upload',
                                        html: `Proses Crop & Upload ${evt.detail.progress}%`,
                                        didOpen: () => {
                                            Swal.showLoading();
                                        }
                                    });
                                });
                            }
                        })

                    }, 'image/jpeg');
                };

                var btnCloseCropper = document.querySelector('#cropCancel');
                btnCloseCropper.onclick = () => {
                    if (cropper) cropper.destroy();
                    cropper = null;
                    const modal = document.getElementById('cropperModal');
                    modal.style.display = 'none';

                    console.log('Cropper closed');
                    isCropping = false;
                    uploadPhotoTmp = null;
                };



            });

            Livewire.on('showModalVerificationHSE', (e) => {
                console.log(e);
                Swal.fire({
                    title: `Verifikasi HSE Induction `,
                    html: `<p>Anda yakin ingin melakukan verifikasi <br><b class='text-pink'>${e.data.full_name} (${e.data.project_contractor.contractor.company_name})</b> ?</p>
                            <div class="text-start mb-2">
                                <label>Nomor Induction :</label>
                                <input type="text" id="no_induction" class="form-control mb-3" placeholder="Nomor Induction" value="${e.data.induction_card_number ? e.data.induction_card_number : ''}">
                            </div>
                    <small class="text-muted">Masukan Nomor Induction Pekerja</small>`,
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Simpan',
                    cancelButtonText: 'Batal',
                    preConfirm: () => {
                        const inputValue = document.getElementById('no_induction').value;
                        return {
                            no_induction: inputValue
                        };
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        console.log('No Induction:', result.value.no_induction);
                        Livewire.dispatch('submitVerificationHSE', {
                            id: e.data.id,
                            no_induction: result.value.no_induction,
                        });
                    }
                });
            });

            Livewire.on('showModalVerificationSecurity', (e) => {
                console.log(e);
                const badgeNumber = e.securityBadgeNumber || '-';
                Swal.fire({
                    title: `Verifikasi Security `,
                    html: `<p>Anda yakin ingin melakukan verifikasi <br><b class='text-pink'>${e.data.full_name} (${e.data.project_contractor.contractor.company_name})</b> ?</p>
                            <div class="alert alert-info text-start py-2 px-3 mb-3">
                                <div><strong>Nomor ID Badge Security:</strong></div>
                                <div class="fw-bold">${badgeNumber}</div>
                            </div>
                            <div class="text-start mb-2">
                                <label>Nama Area :</label>
                                <input type="text" id="area_name" class="form-control mb-3" placeholder="Nama Area" value="${e.data.area ? e.data.area : ''}">
                            </div>
                            <div class="text-start mb-2">
                                <label>Pilih Zona :</label>
                                <select id="area_color_select" class="form-control">
                                    <option value="" selected disabled readonly>Pilih Area</option>
                                    <option value="red">MERAH</option>
                                    <option value="brown" disabled readonly>COKLAT</option>
                                    <option value="green" disabled readonly>HIJAU</option>
                                </select>
                            </div>
                            <small class="text-muted">Masukan Nomor ID Security Pekerja dan Pilih Area</small>`,
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Simpan',
                    cancelButtonText: 'Batal',
                    didOpen: () => {
                        // Inisialisasi TomSelect setelah SweetAlert2 terbuka
                        const tom = new TomSelect('#area_color_select', {
                            create: false,
                            sortField: {
                                field: "text",
                                direction: "asc"
                            },
                            dropdownParent: document.querySelector(".swal2-popup")
                        });

                        setTimeout(() => {
                            const dropdown = document.querySelector('.ts-dropdown');
                            if (dropdown) {
                                dropdown.style.zIndex = '11000';
                                dropdown.style.marginTop = '-30mm';
                                dropdown.style.marginLeft = '5mm';
                                dropdown.style.width = '125mm';
                            }
                        }, 10)
                    },
                    preConfirm: () => {
                        const area_color = document.getElementById('area_color_select').value;
                        const area_name = document.getElementById('area_name').value;

                        if (!area_color) {
                            Swal.showValidationMessage('Warna Area harus dipilih');
                            return false;
                        }

                        if (!area_name) {
                            Swal.showValidationMessage('Nama Area harus di isi');
                            return false;
                        }

                        return {
                            area: area_name,
                            area_color: area_color
                        };
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        Livewire.dispatch('submitVerificationSecurity', {
                            id: e.data.id,
                            area: result.value.area,
                            area_color: result.value.area_color,
                            securityBadgeNumber: badgeNumber
                        });
                    }
                });
            });

            Livewire.on('loadingAlasanRejectMcu', (e) => {
                Swal.fire({
                    title: 'Mengambil Data...',
                    html: 'Mohon tunggu sebentar',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading()
                    }
                });

                console.log(e);

                Livewire.dispatch('alasanRejectMcu', {
                    id: e.data
                });
            })

            Livewire.on('showModalAlasanRejectMcu', (e) => {
                Swal.close();
                var data = e.data;
                console.log(data);

                Swal.fire({
                    title: 'Alasan Reject',
                    html: `Alasan Reject MCU : <b>${data.medical_review.notes}</b>`,
                    confirmButtonText: 'OK',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    confirmButtonColor: '#3085d6',
                });
            })

            Livewire.on('showModalDeleteConfirmation', (e) => {
                var data = e.data;
                Swal.fire({
                    title: 'Hapus Data',
                    html: `<p>Anda yakin ingin menghapus pekerja <b class='text-pink'>${data.full_name}</b> ?</p> <p>
                            <small class="text-muted">Data yang telah dihapus tidak dapat dikembalikan.</small></p>`,
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Hapus',
                    cancelButtonText: 'Batal',
                }).then((result) => {
                    if (result.isConfirmed) {
                        @this.call('deleteEmployee', data.id);
                    }
                })
            });


            Livewire.on('showModalPrintIdBadge', (e) => {
                Swal.fire({
                    title: 'Loading...',
                    didOpen: () => {
                        Swal.showLoading();
                    },
                })
                Livewire.dispatch('printIdBadge', {
                    id: e.data.id
                });
                // Swal.fire({
                //     title: 'Cetak ID Badge',
                //     html: `<p>Anda yakin ingin mencetak ID Badge pekerja <b class='text-pink'>${e.data.full_name}</b> ?</p>`,
                //     showCancelButton: true,
                //     confirmButtonColor: '#3085d6',
                //     cancelButtonColor: '#d33',
                //     confirmButtonText: '<i class="ti ti-printer"></i>Cetak',
                //     cancelButtonText: 'Batal',
                // }).then((result) => {
                //     if (result.isConfirmed) {
                //         Livewire.dispatch('printIdBadge', {
                //             id: e.data.id
                //         });
                //     }
                // });
            });

            Livewire.on('printBadge', (e) => {
                Swal.close();
                window.open(e.url, '_blank').focus();
            })

            const modalEditEmployee = new bootstrap.Modal(document.getElementById('modalEditEmployee'), {});

            Livewire.on('showModalEditEmployee', (e) => {
                // change title
                document.querySelector('.modal-detail-title').textContent =
                    `Edit Data Pekerja: ${e.data.full_name} - ${e.data.project_contractor.contractor.company_name}`;
                // set data to input
                modalDetailData.hide();
                Swal.fire({
                    title: 'Loading...',
                    didOpen: () => {
                        Swal.showLoading();
                    },
                });
                modalEditEmployee.show();
                document.getElementById('modalEditEmployee').addEventListener('shown.bs.modal', () => {
                    Swal.close();
                });
            });

            Livewire.on('hideModalEditEmployee', () => {
                modalEditEmployee.hide();
            });


        });
    </script>
@endpush
