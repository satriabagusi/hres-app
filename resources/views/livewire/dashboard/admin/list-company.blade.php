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
                                {{-- <div class="dropdown">
                                    <a href="#" class="btn dropdown-toggle" data-bs-toggle="dropdown">Download</a>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="#">Action</a>
                                        <a class="dropdown-item" href="#">Another action</a>
                                        <a class="dropdown-item" href="#">Third action</a>
                                    </div>
                                </div> --}}
                            </div>
                        </div>
                    </div>
                </div>
                <div id="advanced-table">
                    <div class="table-responsive">
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

                                            {{-- <button class="btn btn-sm btn-ghost-cyan" type="button"
                                                data-bs-toggle="modal" data-bs-target="#modalViewDocument"
                                                wire:click="viewDocument('{{ $item->id }}')">
                                                <i class="ti ti-file-type-pdf "></i>
                                                Lihat Dokumen
                                            </button> --}}
                                            {{-- <a href="{{ asset('uploads/contractor_documents/' . $item->document_contractor) }}" class="btn btn-sm btn-ghost-cyan" target="_blank" onclick="window.open(this.href, 'new', 'popup'); return false;">Lihat Dokumen</a> --}}
                                            <button class="btn btn-sm btn-ghost-green " wire:click="approveModal({{ $item->id }})" type="button">Approve</button>
                                            <button class="btn btn-sm btn-ghost-red " wire:click="rejectModal({{ $item->id }})" type="button">Reject</button>
                                        </td>
                                    </tr>
                                @endforeach


                            </tbody>
                        </table>
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
    <div class="modal fade" id="modalViewDocument" data-bs-backdrop="static" data-bs-keyboard="false">
        {{-- centered vertically modal --}}
        <div class="modal-dialog modal-xl modal-dialog-centered " role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <h4 class="modal-title text-center">{{ 'Dokumen Form B - ' . $selectedCompanyName }}</h4>
                    {{-- pdf iframe --}}
                    <iframe src="{{ asset($pdfFileName) }}?page=hsn#toolbar=0" width="100%" height="800px"
                        frameborder="0" style="margin: 0 auto;"></iframe>
                    {{-- end pdf iframe --}}
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
