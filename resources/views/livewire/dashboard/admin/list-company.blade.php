<div>
    <div class="col-12">
        <div class="card">
            <div class="card-table">
                <div class="card-header">
                    <div class="row w-full">
                        <div class="col">
                            <h3 class="card-title mb-0">List Kontraktor</h3>
                            <p class="text-secondary m-0">Data Perusahaan Kontraktor.</p>
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
                                <div class="col-md-auto col-sm-12">
                                    <div class="dropdown">
                                        <a class="btn btn-outline-azure dropdown-toggle d-flex align-items-center"
                                            data-bs-toggle="dropdown" wire:loading.delay wire:loading.attr="disabled"
                                            wire:target="totalPaginate, page">
                                            <!-- Spinner dalam button saat loading -->
                                            <div wire:loading.delay wire:target="totalPaginate"
                                                class="spinner-border spinner-border-sm text-primary me-2"
                                                role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                            <span id="page-count" class="me-1">Tampilkan {{ $totalPaginate }}</span>
                                            <span>data</span>
                                        </a>
                                        <div class="dropdown-menu">
                                            <button type="button" class="dropdown-item"
                                                wire:click="$set('totalPaginate', 10)">10 data</button>
                                            <button type="button" class="dropdown-item"
                                                wire:click="$set('totalPaginate', 20)">20 data</button>
                                            <button type="button" class="dropdown-item"
                                                wire:click="$set('totalPaginate', 50)">50 data</button>
                                            <button type="button" class="dropdown-item"
                                                wire:click="$set('totalPaginate', 100)">100 data</button>
                                        </div>
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
                                        <th>Nama Perusahaan</th>
                                        <th>Email</th>
                                        <th>Status</th>
                                        <th>Tgl. Daftar</th>
                                        <th>#</th>
                                    </tr>
                                </thead>
                                <tbody class="table-tbody">
                                    @foreach ($companies as $item)
                                        <tr>
                                            <td class="sort-name">
                                                <span class="text-body">{{ $item->company_name }}</span>
                                            </td>
                                            <td class="">{{ $item->email }}</td>
                                            <td class="sort-status">
                                                @if ($item->status == 'approved')
                                                    <span class="badge bg-lime text-lime-fg">Approved</span>
                                                @elseif($item->status == 'pending')
                                                    <span class="badge bg-orange text-orange-fg">Pending</span>
                                                @elseif($item->status == 'rejected')
                                                    <span class="badge bg-red text-red-fg">Rejected</span>
                                                @endif
                                            </td>
                                            <td class="">
                                                {{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('d M Y') }}
                                            </td>
                                            <td class="sort-category py-0">
                                                <button class="btn btn-sm btn-primary"
                                                    wire:click="detailModal({{ $item->id }})" type="button">Detail
                                                </button>
                                                <button class="btn btn-sm btn-teal"
                                                    wire:click="approveModal({{ $item->id }})"
                                                    type="button">Approve</button>
                                                <button class="btn btn-sm btn-pink"
                                                    wire:click="rejectModal({{ $item->id }})"
                                                    type="button">Reject</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer d-flex align-items-center">
                        <div class="dropdown">
                            <a class="btn dropdown-toggle" data-bs-toggle="dropdown">
                                <span id="page-count" class="me-1">20</span>
                                <span>records</span>
                            </a>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" onclick="setPageListItems(event)" data-value="10">10
                                    records</a>
                                <a class="dropdown-item" onclick="setPageListItems(event)" data-value="20">20
                                    records</a>
                                <a class="dropdown-item" onclick="setPageListItems(event)" data-value="50">50
                                    records</a>
                                <a class="dropdown-item" onclick="setPageListItems(event)" data-value="100">100
                                    records</a>
                            </div>
                        </div>
                        <div class="pagination m-0 ms-auto">
                            {{ $companies->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- <div class="modal fade" id="modalViewDocument" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-xl modal-dialog-centered " role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <h4 class="modal-title text-center">{{ 'Dokumen Form B - ' . $selectedCompanyName }}</h4>
                    <iframe src="{{ asset($pdfFileName) }}?page=hsn#toolbar=0" width="100%" height="800px"
                        frameborder="0" style="margin: 0 auto;"></iframe>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-outline-secondary float-end" data-bs-dismiss="modal"
                        wire:click="$set('pdfFileName', null)"> Tutup </button>
                </div>
            </div>
        </div>
    </div>  --}}
    <div class="modal fade" id="modalDetailCompany" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-xl modal-dialog-centered " role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <table class="table table-striped">
                        <tr>
                            <td width="160px"> Nama Perusahaan </td>
                            <td width="10px"> : </td>
                            <td id="selected_company_name">{{ $selectedCompany->company_name ?? '-' }} </td>
                        </tr>
                        <tr>
                            <td width="160px"> Nama Perusahaan </td>
                            <td width="10px"> : </td>
                            <td id="selected_company_create_date">
                                {{ $selectedCompany ? \Carbon\Carbon::parse($selectedCompany->created_at)->format('d-m-Y') : '-' }}
                            </td>
                        </tr>
                        <tr>
                            <td width="160px"> Jumlah Kontrak Project diajukan </td>
                            <td width="10px"> : </td>
                            <td id="selected_company_total_project">{{ $selectedCompany->total_projects ?? '-' }}
                            </td>
                        </tr>
                        <tr>
                            <td width="160px"> List Kontrak Project di ajukan: </td>
                            <td width="10px"> : </td>
                            <td>
                                @if ($selectedCompany && $selectedCompany->project_contractors->count() > 0)
                                    <ul class="list-group list-group-numbered">
                                        @foreach ($selectedCompany->project_contractors as $project)
                                            <li class="list-group-item">
                                                {{ $project->project_name }}
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <span class="text-secondary">Belum ada kontrak project yang diajukan.</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-outline-secondary float-end" data-bs-dismiss="modal"
                        wire:click="$set('pdfFileName', null)"> Tutup </button>
                </div>
            </div>
        </div>
    </div>
</div>


@push('scripts')
    <script>
        document.addEventListener('livewire:init', function() {

            var bootstrap = tabler.bootstrap;

            var modalDetailCompany = new bootstrap.Modal(document.getElementById('modalDetailCompany'), {
                backdrop: 'static',
                keyboard: false
            });

            Livewire.on('showDetailCompanyModal', (company) => {
                modalDetailCompany.show();
            });

            Livewire.on('approvalDocumentModal', (e) => {
                Swal.fire({
                    title: e.title,
                    html: e.text,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: e.confirmText,
                    cancelButtonText: e.cancelText,
                    allowOutsideClick: false,
                    allowEscapeKey: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        Livewire.dispatch('approveDocument', {
                            id: e.id
                        });
                    }
                });
            });

            Livewire.on('rejectDocumentModal', (e) => {
                Swal.fire({
                    title: e.title,
                    html: e.text,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: e.confirmText,
                    cancelButtonText: e.cancelText,
                    allowOutsideClick: false,
                    allowEscapeKey: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        Livewire.dispatch('rejectDocument', {
                            id: e.id
                        });
                    }
                });
            });

        });
    </script>
@endpush
