<div>
    @if ($projectContractId)
        <div class="col-12 mb-3">
            @if (!$projectIsClosed)
                <a class="btn btn-cyan"
                    href="{{ route('contractor.list-draft-employee', ['project_contract_id' => $projectContractId]) }}"><i
                        class="ti ti-upload"></i> &nbsp;
                    Upload/Draft Pekerja</a>
            @else
                <span class="badge bg-dark text-dark-fg p-2">Project Closed - Upload Pekerja Dinonaktifkan</span>
            @endif
            <p class="text-muted mt-2">Jika data pekerja tidak muncul disini silahkan check pada Draft Data Pekerja (ada
                kemungkinan data belum di submit atau di reject terkait dokumen)</p>
        </div>
        <hr>
    @endif
    <!-- Dropdown Filter Status -->
    <div class="col-auto mb-3">
        <div class="dropdown">
            <button class="btn btn-outline-dark dropdown-toggle" data-bs-toggle="dropdown">
                <div wire:loading wire:target="statusSelected">
                    <div class="spinner-border spinner-border-sm text-dark mt-1" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>&nbsp;
                Status:
                @switch($statusSelected)
                    @case('submitted')
                        Diajukan
                    @break
                    @case('approved')
                        Tercetak
                    @break

                    @default
                        Semua
                @endswitch
            </button>
            <div class="dropdown-menu">
                <button type="button" class="dropdown-item" wire:click="$set('statusSelected', null)">Semua</button>
                <button type="button" class="dropdown-item"
                    wire:click="$set('statusSelected', 'submitted')">Diajukan</button>
                <button type="button" class="dropdown-item"
                    wire:click="$set('statusSelected', 'approved')">Tercetak</button>
            </div>
        </div>
    </div>


    <div class="col-12">
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
                                    <input id="advanced-table-search" type="text" class="form-control"
                                        autocomplete="off" placeholder="Cari ... " wire:model.live='search' />
                                </div>
                                <div class="dropdown">
                                    <a class="btn btn-outline-azure dropdown-toggle d-flex align-items-center"
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
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                            <table class="table table-vcenter table-selectable">
                                <thead>
                                    <tr>
                                        <th>NO</th>
                                        <th>Nama Pekerja</th>
                                        <th>USIA</th>
                                        <th>NIK</th>
                                        <th>Jabatan</th>
                                        <th>Status</th>
                                        <th>Verifikasi Medical</th>
                                        <th>Verifikasi Security</th>
                                        <th>Tempat Lahir</th>
                                        <th>Tanggal Lahir</th>
                                        <th>#</th>
                                    </tr>
                                </thead>
                                <tbody class="table-tbody">
                                    @foreach ($employees as $item)
                                        @php
                                            $medicalStatus = optional($item->medical_review)->status;
                                            $securityStatus = optional($item->security_review)->status;
                                        @endphp
                                        <tr class="{{ $item->is_blacklisted_active ? 'bg-dark text-white' : '' }}">
                                            <td width="50px" class="text-center">
                                                {{-- Show number based on current page --}}
                                                {{ $loop->iteration + ($employees->currentPage() - 1) * $employees->perPage() }}
                                            </td>
                                            <td>
                                                <span class="text-body">{{ $item->full_name }}</span>
                                            </td>
                                            <td class="">{{ \Carbon\Carbon::parse($item->birth_date)->age }}</td>
                                            <td class="">{{ $item->nik }}</td>
                                            <td class="">{{ $item->position }}</td>
                                            <td class="sort-status">
                                                @if ($item->is_blacklisted_active)
                                                    <span class="badge bg-dark text-dark-fg">Blacklisted</span>
                                                @elseif($item->status == 'approved')
                                                    <span class="badge bg-lime text-lime-fg">ID Badge Tercetak</span>
                                                @elseif($item->status == 'draft')
                                                    <span class="badge bg-orange text-orange-fg">Draft</span>
                                                @elseif($item->status == 'submitted')
                                                    <span class="badge bg-blue text-blue-fg">Diajukan</span>
                                                @elseif($item->status == 'rejected')
                                                    <button type="button"
                                                        class="badge bg-red text-red-fg border-0 btn-show-reject-reason"
                                                        data-source="Approval"
                                                        data-reason="{{ optional($item->medical_review)->notes ?: (optional($item->security_review)->notes ?: 'Pekerja ditolak pada proses verifikasi.') }}">
                                                        Ditolak
                                                    </button>
                                                @endif
                                            </td>
                                            <td class="sort-status">
                                                @if ($medicalStatus == 'on_review')
                                                    <span class="badge bg-yellow text-yellow-fg ">Sedang Di
                                                        Review</span>
                                                @elseif($medicalStatus == 'approved')
                                                    <span class="badge bg-lime text-lime-fg">Disetujui</span>
                                                @elseif($medicalStatus == 'rejected')
                                                    <button type="button"
                                                        class="badge bg-red text-red-fg border-0 btn-show-reject-reason"
                                                        data-source="Medical"
                                                        data-reason="{{ optional($item->medical_review)->notes ?: 'Tidak ada keterangan.' }}">
                                                        Ditolak
                                                    </button>
                                                    <br>
                                                    <smal class="text-muted" style="font-size: 9px;margin-top: 0px">
                                                        klik untuk lihat detail</smal>
                                                @else
                                                    <span class="badge bg-secondary text-secondary-fg">Belum Ada</span>
                                                @endif
                                            </td>
                                            <td class="sort-status">
                                                @if ($item->is_blacklisted_active)
                                                    <span class="badge bg-dark text-dark-fg">Blacklisted</span>
                                                @elseif($securityStatus == 'on_review')
                                                    <span class="badge bg-yellow text-yellow-fg">Sedang Di
                                                        Review</span>
                                                @elseif($securityStatus == 'approved')
                                                    <span class="badge bg-lime text-lime-fg">Disetujui</span>
                                                @elseif($securityStatus == 'rejected')
                                                    <button type="button"
                                                        class="badge bg-red text-red-fg border-0 btn-show-reject-reason"
                                                        data-source="Security"
                                                        data-reason="{{ optional($item->security_review)->notes ?: 'Tidak ada keterangan.' }}">
                                                        Ditolak
                                                    </button>
                                                @else
                                                    <span class="badge bg-secondary text-secondary-fg">Belum Ada</span>
                                                @endif
                                            </td>
                                            <td class="">{{ $item->birth_place }}</td>
                                            <td class="">
                                                {{ \Carbon\Carbon::parse($item->birth_date)->format('d-m-Y') }}
                                            </td>
                                            <td>

                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer d-flex align-items-center">
                        <div class="pagination m-0 ms-auto">
                            {{ $employees->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>


@push('scripts')
    <script>
        document.addEventListener('livewire:init', function() {
            document.addEventListener('click', function(e) {
                const rejectReasonBtn = e.target.closest('.btn-show-reject-reason');
                if (!rejectReasonBtn) {
                    return;
                }

                const source = rejectReasonBtn.dataset.source || 'Verifikasi';
                const reason = rejectReasonBtn.dataset.reason || 'Tidak ada keterangan.';

                Swal.fire({
                    title: 'Alasan Ditolak',
                    html: `${source}: <b>${reason}</b>`,
                    confirmButtonText: 'OK',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    confirmButtonColor: '#3085d6',
                });
            });

        });
    </script>
@endpush
