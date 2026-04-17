<div>
    <div class="col-12">
        <div class="card">
            <div class="card-table">
                <div class="card-header">
                    <div class="row w-full">
                        <div class="col">
                            <h3 class="card-title mb-0">List Kontrak Proyek</h3>
                            <p class="text-secondary m-0">Data Kontrak Proyek.</p>
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
                                    <a class="btn btn-outline-dark dropdown-toggle d-flex align-items-center"
                                        data-bs-toggle="dropdown" wire:loading.delay wire:loading.attr="disabled"
                                        wire:target="projectStatusSelected">
                                        <span>Status:
                                            @if ($projectStatusSelected === 'active')
                                                Active
                                            @elseif($projectStatusSelected === 'closed')
                                                Closed
                                            @else
                                                Semua
                                            @endif
                                        </span>
                                    </a>
                                    <div class="dropdown-menu">
                                        <button type="button" class="dropdown-item"
                                            wire:click="$set('projectStatusSelected', null)">Semua</button>
                                        <button type="button" class="dropdown-item"
                                            wire:click="$set('projectStatusSelected', 'active')">Active</button>
                                        <button type="button" class="dropdown-item"
                                            wire:click="$set('projectStatusSelected', 'closed')">Closed</button>
                                    </div>
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
                    <div class="table-responsive" style="overflow: visible;">
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
                                        <th width="30%">Nama Proyek</th>
                                        <th>Nama Perusahaan</th>
                                        <th>No. Kontrak/Memo</th>
                                        <th>Tanggal Mulai</th>
                                        <th>Tanggal Selesai</th>
                                        <th class="text-center" width="200px">#</th>
                                    </tr>
                                </thead>
                                <tbody class="table-tbody">
                                    @forelse ($projects as $project)
                                        <tr>
                                            <td width="50px" class="text-center">
                                                {{-- Show number based on current page --}}
                                                {{ $loop->iteration + ($projects->currentPage() - 1) * $projects->perPage() }}
                                            </td>
                                            <td width="40%">
                                                <span class="text-body">{{ $project->project_name }}
                                                    @if ($project->is_closed)
                                                        <span class="badge bg-dark text-white">Closed</span>
                                                    @elseif($project->status === 1)
                                                        <span class="badge bg-teal text-white">Aktif</span>
                                                    @else
                                                        <span class="badge bg-pink text-white">Non Aktif</span>
                                                    @endif
                                                </span>
                                            </td>
                                            <td class="">
                                                {{ $project->contractor->company_name ?? '-' }}</td>
                                            {{-- show link to blank window --}}

                                            <td class="">
                                                {{-- show link to blank window --}}
                                                <a href="{{ asset('uploads/' . $project->memo_document) }}"
                                                    target="_blank" class="text-decoration-none"
                                                    rel="noopener noreferrer"
                                                    onclick="window.open(this.href, 'new', 'popup'); return false;">
                                                    {{ $project->memo_number }}
                                                </a>
                                            </td>
                                            <td class="">
                                                {{ \Carbon\Carbon::parse($project->start_date)->format('d-m-Y') }}</td>
                                            <td class="">
                                                {{ \Carbon\Carbon::parse($project->end_date)->format('d-m-Y') }}</td>
                                            <td class="">
                                                {{-- Using Dropdown --}}

                                                <div class="dropdown">
                                                    <button
                                                        class="btn btn-sm btn-outline-orange text-decoration-none dropdown-toggle"
                                                        type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="ti ti-info-circle"></i>
                                                        Menu Project
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li class="">
                                                            <button type="button" class="dropdown-item"
                                                                wire:click='detailProjectContract({{ $project->id }})'>
                                                                <i class="ti ti-check"></i>
                                                                Detail Proyek
                                                            </button>
                                                        </li>
                                                        @if (!$project->is_closed && $project->status === 1)
                                                            <li class="">
                                                                <button type="button" class="dropdown-item"
                                                                    wire:click='closeProject({{ $project->id }})'>
                                                                    <i class="ti ti-x"></i>
                                                                    Close Project
                                                                </button>
                                                            </li>
                                                            <li class="">
                                                                <button type="button" class="dropdown-item"
                                                                    wire:click='editProjectContract({{ $project->id }})'>
                                                                    <i class="ti ti-pencil"></i>
                                                                    Edit Proyek
                                                                </button>
                                                            </li>
                                                        @endif


                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">Tidak ada data proyek kontrak.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer d-flex align-items-center">
                        <div class="pagination m-0 ms-auto">
                            {{ $projects->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- <pre>{{ json_encode($this->only(['project_name', 'contract_number', 'start_date', 'end_date', 'contract_document']), JSON_PRETTY_PRINT) }}</pre> --}}


    {{-- modal detail project contract --}}
    <div class="modal fade" id="modalDetailProject" tabindex="-1" aria-labelledby="modalDetailProject"
        aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Detail Proyek Kontrak</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="table-responsive">
                        <div class="position-relative">
                            <div wire:loading.delay wire:target="selectedProject"
                                class="position-absolute w-100 h-100 bg-white bg-opacity-75 table-loading-overlay"
                                style="top: 0; left: 0; z-index: 10;">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                            <table class="table table-vcenter table-selectable">
                                <tr>
                                    <td width="20%" class="text-bold align-text-top align-top">Nama Proyek</td>
                                    <td width="1%" class="align-text-top align-top">:</td>
                                    <td id="selected_project_name" class="align-text-top align-top">
                                        {{ $selectedProject->project_name ?? '-' }} </td>
                                </tr>
                                <tr>
                                    <td width="20%">Nama PT</td>
                                    <td width="1%">:</td>
                                    <td id="selected_project_company">
                                        {{ $selectedProject->contractor->company_name ?? '-' }} </td>
                                </tr>
                                <tr>
                                    <td width="20%">No. Kontrak/Memo</td>
                                    <td width="1%">:</td>
                                    <td id="selected_project_memo_number">{{ $selectedProject->memo_number ?? '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td width="20%">Tanggal Mulai</td>
                                    <td width="1%">:</td>
                                    <td id="selected_project_start_date">
                                        {{ $selectedProject ? \Carbon\Carbon::parse($selectedProject->start_date)->format('d-m-Y') : '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td width="20%">Tanggal Selesai</td>
                                    <td width="1%">:</td>
                                    <td id="selected_project_end_date">
                                        {{ $selectedProject ? \Carbon\Carbon::parse($selectedProject->end_date)->format('d-m-Y') : '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td width="20%">Status</td>
                                    <td width="1%">:</td>
                                    <td id="selected_project_status">
                                        @if ($selectedProject && $selectedProject->is_closed)
                                            <span class='text-dark fw-bold'>Closed</span>
                                        @elseif ($selectedProject && $selectedProject->status === 1)
                                            <span class='text-teal fw-bold'>Sedang Berjalan</span>
                                        @else
                                            <span class='text-pink fw-bold'>Non Aktif</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td width="20%">Total Pekerja Diajukan</td>
                                    <td width="1%">:</td>
                                    <td id="selected_project_total_worker">
                                        {{ $selectedProject->workers_count ?? '0' }} Orang</td>
                                </tr>
                                <tr>
                                    <td width="20%">Total Pekerja Di setujui</td>
                                    <td width="1%">:</td>
                                    <td id="selected_project_total_worker_active">
                                        {{ $selectedProject->submitted_and_approved_workers ?? '0' }} Orang
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- modal edit project contract --}}
    <div class="modal fade" id="modalEditProjectContract" tabindex="-1" aria-labelledby="modalEditProjectContract"
        aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Edit Proyek Kontrak</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <form id="form-add-project-contract" wire:submit.prevent="updateProjectContract">
                        <div class="mb-3">
                            <label for="project_name" class="form-label">Nama Proyek</label>
                            <input type="text" class="form-control" id="project_name"
                                wire:model.defer="project_name" required>
                            @error('project_name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="contract_number" class="form-label">No. Kontrak/Memo</label>
                            <input type="text" class="form-control" id="contract_number"
                                wire:model.defer="contract_number" required>
                            @error('contract_number')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3" wire:ignore>
                            <label for="contract_document" class="form-label">Dokumen Kontrak (PDF)</label>
                            {{-- Filepond input for contract document --}}
                            <input type="file" wire:model="contract_document" id="contract-document-upload"
                                class="filepond" accept="application/pdf" multiple="false" />
                            {{-- Show error if file is not valid --}}
                            <small>*File baru yang di upload akan menghapus file lama didatabase</small>
                        </div>
                        @error('contract_document')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                        <div class="mb-3">
                            <label for="start_date" class="form-label">Tanggal Mulai</label>
                            <div class="input-icon mb-2">
                                <input class="form-control" placeholder="Tanggal Mulai"
                                    id="start-date-picker-icon" />
                                <span class="input-icon-addon">
                                    <i class="ti ti-calendar"></i>
                                </span>
                            </div>
                            @error('start_date')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror

                        </div>
                        <div class="mb-3">
                            <label for="end_date" class="form-label">Tanggal Selesai</label>
                            <div class="input-icon mb-2">
                                <input class="form-control" placeholder="Tanggal Selesai"
                                    id="end-date-picker-icon" />
                                <span class="input-icon-addon">
                                    <i class="ti ti-calendar"></i>
                                </span>
                            </div>
                            @error('end_date')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror

                        </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" wire:submit.prevent="updateProjectContract">Simpan
                        Perubahan</button>
                </div>

                </form>
            </div>
        </div>

    </div>
</div>


@push('scripts')
    <script>
        document.addEventListener('livewire:init', function() {

            var bootstrap = tabler.bootstrap;

            // Initialize litepicker for date input
            const modalDetailProject = new bootstrap.Modal('#modalDetailProject', {
                keyboard: false,
                backdrop: 'static',
            });

            const modalDetailProjectElement = document.getElementById('modalDetailProject');

            Livewire.on('showModalDetailProjectContract', (e) => {
                console.log("SelectedId : ", e.selectedId);
                modalDetailProject.show();
                console.log(e);
            });

            Livewire.on('closeProjectConfirmation', (e) => {
                console.log("Close Project Confirmation: ", e);
                Swal.fire({
                    title: 'Konfirmasi',
                    html: `Apakah Anda yakin ingin menutup proyek <br><b>${e.data.project_name}</b> <br>Pelaksana : <b>${e.data.contractor.company_name}</b>?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Close Project!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    customClass: {
                        confirmButton: 'btn btn-red',
                        cancelButton: 'btn btn-vk'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        Livewire.dispatch('confirmCloseProject', {
                            projectId: e.data.id
                        });
                    }
                })
            });

            const modalEditProjectContract = new bootstrap.Modal('#modalEditProjectContract', {
                keyboard: false,
                backdrop: 'static',
            });

            const modalEditProjectContractElement = document.getElementById('modalEditProjectContract');

            modalEditProjectContractElement.addEventListener('shown.bs.modal', function() {
                let uploadedFileTmp = null;
                const inputElement = document.getElementById('contract-document-upload');

                const pond = FilePond.create(inputElement, {
                    allowMultiple: false,
                    maxFiles: 1,
                    // excel file allowed
                    acceptedFileTypes: ['application/pdf'],
                    fileValidateTypeLabelExpectedTypes: 'Hanya file PDF yang diperbolehkan',
                    maxFileSize: '10MB',
                    labelIdle: `<div class="text-center mb-2"> <i class="ti ti-upload fs-2 mb-3 text-muted"></i><br><strong>Drag & drop</strong> atau <span class="filepond--label-action">klik di sini</span> untuk upload</div>`,
                    credits: false,
                    labelFileTypeNotAllowed: 'Hanya file PDF yang diperbolehkan',
                    labelMaxFileSizeExceeded: 'Ukuran file terlalu besar (maksimal 10MB)',
                    labelMaxFileSize: 'Maksimal ukuran file adalah 10MB',
                    labelFileProcessingError: 'Terjadi kesalahan saat mengunggah file',
                    labelFileProcessing: 'Mengunggah file...',
                    labelFileProcessingComplete: 'File berhasil diunggah',
                    labelFileProcessingAborted: 'Pengunggahan file dibatalkan',
                });


                pond.on('addfile', (error, file) => {
                    if (!error) {
                        inputElement.dispatchEvent(new Event('change', {
                            bubbles: true
                        }));
                        const fileData = file.file;
                        @this.upload('contract_document', fileData, (fileName) => {
                            console.log('File uploaded successfully:', fileName);
                            uploadedFileTmp = fileName;
                        }, (error) => {
                            console.error('Error uploading file:', error);
                            // Show error message to user
                            Swal.fire({
                                icon: 'error',
                                title: 'Upload Gagal',
                                text: 'Terjadi kesalahan saat mengunggah file. Silakan coba lagi.',
                                confirmButtonText: 'OK'
                            });
                        });
                    }
                });

                pond.on('removefile', (err, file) => {
                    console.log('File removed');
                    inputElement.value = '';
                    inputElement.dispatchEvent(new Event('change', {
                        bubbles: true
                    }));

                    if (uploadedFileTmp) {
                        @this.removeUpload('contract_document', uploadedFileTmp);
                        uploadedFileTmp = null;
                    }
                });

                const startDatepickerIcon = document.getElementById('start-date-picker-icon');
                if (startDatepickerIcon) {
                    new Litepicker({
                        element: startDatepickerIcon,
                        format: 'DD-MM-YYYY',
                        buttonText: {
                            previousMonth: '<i class="ti ti-chevron-left"></i>',
                            nextMonth: '<i class="ti ti-chevron-right"></i>',
                        },
                        singleMode: true,
                        autoApply: true,
                        setup(picker) {
                            picker.on('selected', (date) => {
                                const formattedDate = date.format('YYYY-MM-DD');
                                // console.log('Start date selected:', formattedDate);
                                @this.set('start_date', formattedDate);
                            })
                            picker.setDate(@this.get('start_date') || @js($start_date));
                        }
                    });
                }
                const endDatepicker = document.getElementById('end-date-picker-icon');
                if (endDatepicker) {
                    new Litepicker({
                        element: endDatepicker,
                        format: 'DD-MM-YYYY',
                        buttonText: {
                            previousMonth: '<i class="ti ti-chevron-left"></i>',
                            nextMonth: '<i class="ti ti-chevron-right"></i>',
                        },
                        singleMode: true,
                        autoApply: true,
                        setup(picker) {
                            picker.on('selected', (date) => {
                                const formattedDate = date.format('YYYY-MM-DD');
                                // console.log('End date selected:', formattedDate);
                                @this.set('end_date', formattedDate);
                            })
                            picker.setDate(@this.get('end_date') || @js($end_date));
                        }
                    });
                }
            });

            Livewire.on('confirmationUpdateProjectContract', (e) => {
                Swal.fire({
                    title: 'Konfirmasi',
                    html: `Apakah Anda yakin ingin mengupdate proyek <br><b>${e.data.project_name}</b> <br>Pelaksana : <b>${e.data.contractor.company_name}</b>?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Update Proyek!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    customClass: {
                        confirmButton: 'btn btn-red',
                        cancelButton: 'btn btn-vk'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        Livewire.dispatch('confirmUpdateProjectContract', {
                            projectId: e.selectedId
                        });
                    }
                })
            });

            Livewire.on('showModalEditProjectContract', (e) => {
                // console.log("SelectedId : ", e.selectedId);
                modalEditProjectContract.show();
                console.log(e);
            });

            Livewire.on('successUpdateProjectContract', (e) => {
                modalEditProjectContract.hide();
            });


        });
    </script>
@endpush
