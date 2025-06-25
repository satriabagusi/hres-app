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
                                <button class="btn btn-primary btn-sm" id="btn-add-project-contract" type="button"
                                    data-bs-toggle="modal" data-bs-target="#modal-add-project-contract">
                                    <i class="ti ti-plus"></i> &nbsp;
                                    Tambah Kontrak Proyek
                                </button>
                                <div class="input-group input-group-flat w-auto">
                                    <span class="input-group-text">
                                        <!-- Download SVG icon from http://tabler.io/icons/icon/search -->
                                        <i class="ti ti-search"></i>
                                    </span>
                                    <input id="advanced-table-search" type="text" class="form-control"
                                        autocomplete="off" placeholder="Cari ... " wire:model.live='search' />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="advanced-table">
                    <div class="table-responsive">
                        <table class="table table-vcenter table-selectable">
                            <thead>
                                <tr>
                                    <th>NO</th>
                                    <th >Nama Proyek</th>
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
                                            <span class="text-body">{{ $project->project_name }}</span>
                                        </td>
                                        <td class="">
                                            {{-- show link to blank window --}}
                                            <a href="{{ asset('uploads/' . $project->memo_document) }}" target="_blank" class="text-decoration-none" rel="noopener noreferrer" onclick="window.open(this.href, 'new', 'popup'); return false;">
                                                {{ $project->memo_number }}
                                            </a>
                                        </td>
                                        <td class="">
                                            {{ \Carbon\Carbon::parse($project->start_date)->format('d-m-Y') }}</td>
                                        <td class="">
                                            {{ \Carbon\Carbon::parse($project->end_date)->format('d-m-Y') }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('contractor.list-employee', ['project_contract_id' => $project->id]) }}"
                                                class="btn btn-azure">Detail Pekerja</a>
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
                    <div class="card-footer d-flex align-items-center">
                        <div class="pagination m-0 ms-auto">
                            {{ $projects->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- modal add project contract --}}
    <div class="modal fade" id="modal-add-project-contract" tabindex="-1"
        aria-labelledby="modalAddProjectContractLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalAddProjectContractLabel">Tambah Kontrak Proyek</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="form-add-project-contract" wire:submit.prevent="addProjectContract">
                        <div class="mb-3">
                            <label for="project_name" class="form-label">Nama Proyek</label>
                            <input type="text" class="form-control" id="project_name" wire:model.defer="project_name"
                                required>
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
                            @error('employee_xls')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="start_date" class="form-label">Tanggal Mulai</label>
                            <div class="input-icon mb-2">
                                <input class="form-control" placeholder="Tanggal Mulai" id="start-date-picker-icon" />
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
                                <input class="form-control" placeholder="Tanggal Selesai" id="end-date-picker-icon" />
                                <span class="input-icon-addon">
                                    <i class="ti ti-calendar"></i>
                                </span>
                            </div>
                            @error('end_date')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary" form="form-add-project-contract">Simpan</button>
                </div>
            </div>
        </div>
    </div>
    {{-- end modal add project contract --}}

</div>


@push('scripts')
    <script>
        document.addEventListener('livewire:init', function() {
            var bootstrap = tabler.bootstrap;

            FilePond.registerPlugin(
                FilePondPluginFileValidateType,
                FilePondPluginFileValidateSize,
                FilePondPluginPdfPreview,
            );

            // Initialize litepicker for date input
            const modalAddProjectContract = new bootstrap.Modal('#modal-add-project-contract', {});
            const modalAddProjectContractElement = document.getElementById('modal-add-project-contract');

            modalAddProjectContractElement.addEventListener('shown.bs.modal', function() {
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
                    storeAsFile: true,
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
                                console.log('Start date selected:', formattedDate);
                                @this.set('start_date', formattedDate);
                            })
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
                                console.log('End date selected:', formattedDate);
                                @this.set('end_date', formattedDate);
                            })
                        }
                    });
                }
            });

            Livewire.on('successAddProjectContract', () => {
                modalAddProjectContract.hide();


                // Reset date pickers
                const startDatepicker = document.getElementById('start-date-picker-icon');
                if (startDatepicker) {
                    startDatepicker.value = '';
                }
                const endDatepicker = document.getElementById('end-date-picker-icon');
                if (endDatepicker) {
                    endDatepicker.value = '';
                }
                // find Filepond instance
                const inputElement = document.getElementById('contract-document-upload');
                const filepondInstance = FilePond.find(inputElement);
                if (filepondInstance) {
                    filepondInstance.removeFiles();
                }

            })

        });
    </script>
@endpush
