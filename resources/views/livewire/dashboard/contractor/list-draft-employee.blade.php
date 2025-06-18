<div>
    <div class="row mb-3 justify-content-center">
        @if($projectContractId)
        <div class="col-6">
            <div class="card card-body mb-3" wire:ignore>
                <form wire:submit.prevent="uploadEmployee" class="space-y-4 mb-4">
                    <div>
                        <label for="employee_xls">Upload data Pekerja menggunakan Excel</label>
                        <input type="file" wire:model="employee_xls" id="filepond-upload" class="filepond"
                            accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
                            multiple="false" />
                        {{-- Show error if file is not valid --}}
                        @error('employee_xls')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                        <span wire:loading>
                            <span class="spinner-border spinner-border-sm me-2" role="status"></span> Memproses File
                            ...
                        </span>
                        <span wire:loading.remove>
                            <i class="ti ti-upload"></i> Upload Data
                        </span>
                    </button>
                </form>

                @php
                    $companyName = Auth::user()->company_name;
                @endphp
                <p class="text-secondary text-muted text-center mb-2">Untuk format file, silakan download template di
                    bawah
                    ini.</p>
                <a class="btn btn-info" href="{{ route('contractor.download-template-pekerja') }}">
                    <i class="ti ti-download"></i> &nbsp; Download Template Pekerja
                </a>
            </div>
        </div>
        @endif
        <hr>
        <div class="col-12">
            <div class="card">
                <div class="card-table">
                    <div class="card-header">
                        <div class="row w-full">
                            <div class="col">
                                <h3 class="card-title mb-0">Draft Pekerja</h3>
                                <p class="text-secondary m-0 small">Data Draft Pekerja Kontraktor. (Data yang sudah di
                                    upload
                                    oleh anda akan muncul disini)</p>
                            </div>
                            <div class="col-md-auto col-sm-12">
                                <button class="btn btn-outline-green" id="btn-submit-all-employee">
                                    <i class="ti ti-circle-check"></i> &nbsp; Ajukan Semua Pekerja
                                </button>
                            </div>
                            <div class="col-md-auto col-sm-12">
                                <div class="dropdown">
                                    <a class="btn btn-outline-azure dropdown-toggle" data-bs-toggle="dropdown">
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
                                <div class="ms-auto d-flex flex-wrap btn-list">
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
                                        <th>Nama Pekerja</th>
                                        <th>USIA</th>
                                        <th>NIK</th>
                                        <th>Jabatan</th>
                                        <th>Status</th>
                                        <th>Tempat Lahir</th>
                                        <th>Tanggal Lahir</th>
                                        <th>#</th>
                                    </tr>
                                </thead>
                                <tbody class="table-tbody">
                                    @foreach ($employees as $item)
                                        <tr
                                            class={{ \Carbon\Carbon::parse($item->birth_date)->age > 56 ? 'bg-red-lt' : '' }}>
                                            {{-- Show number based on current page --}}
                                            <td width="50px" class="text-center">
                                                {{-- Show number based on current page --}}
                                                {{ $loop->iteration + ($employees->currentPage() - 1) * $employees->perPage() }}
                                            </td>
                                            <td>
                                                <span class="text-body">{{ $item->full_name }}</span>
                                                <span class="small fw-bold text-muted">
                                                    <br>
                                                    <i
                                                        class='{{ $item->photo ? 'ti ti-check text-green' : 'ti ti-x text-red' }}'>
                                                    </i>
                                                    Foto &nbsp; |
                                                    <i
                                                        class='{{ $item->ktp_document ? 'ti ti-check text-green' : 'ti ti-x text-red' }}'></i>
                                                    </i>
                                                    KTP &nbsp;
                                                    |
                                                    <i
                                                        class='{{ $item->form_b_document ? 'ti ti-check text-green' : 'ti ti-x text-red' }}'></i>
                                                    </i>
                                                    Form B
                                                    @if (\Carbon\Carbon::parse($item->birth_date)->age > 56)
                                                        | <i
                                                            class='{{ $item->age_justification_document ? 'ti ti-check text-green' : 'ti ti-x text-red' }}'></i>
                                                        Justifikasi Umur
                                                    @endif
                                                </span>

                                            </td>
                                            <td class="">{{ \Carbon\Carbon::parse($item->birth_date)->age }}</td>
                                            <td class="">{{ $item->nik }}</td>
                                            <td class="">{{ $item->position }}</td>
                                            <td class="sort-status">
                                                @if ($item->status == 'approved')
                                                    <span class="badge bg-lime text-lime-fg">Disetujui</span>
                                                @elseif($item->status == 'draft')
                                                    <span class="badge bg-orange text-orange-fg">Draft</span>
                                                @elseif($item->status == 'submitted')
                                                    <span class="badge bg-blue text-blue-fg">Dikirim</span>
                                                @elseif($item->status == 'rejected')
                                                    <span class="badge bg-red text-red-fg">Ditolak</span>
                                                @endif
                                            </td>
                                            <td class="">{{ $item->birth_place }}</td>
                                            <td class="">
                                                {{ \Carbon\Carbon::parse($item->birth_date)->format('d-m-Y') }}
                                            </td>
                                            <td>
                                                @if (
                                                    $item->photo &&
                                                        $item->ktp_document && $item->form_b_document &&
                                                        (!$item->age_justification_document || \Carbon\Carbon::parse($item->birth_date)->age <= 56))
                                                    <button class="btn btn-ghost-green" type="button">
                                                        <i class="ti ti-circle-check"></i>
                                                        Ajukan
                                                    </button>
                                                @endif
                                                <button class="btn btn-ghost-cyan" type="button"
                                                    wire:click="viewDocument('{{ $item->id }}')">
                                                    <i class="ti ti-file-type-pdf "></i>
                                                    Dokumen
                                                </button>
                                                <button class="btn btn-ghost-danger" type="button"
                                                    wire:click="deleteDraft('{{ $item->id }}')">
                                                    <i class="ti ti-trash"></i>
                                                    Hapus
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer d-flex align-items-center">

                            <div class="pagination m-0 ms-auto">
                                {{ $employees->links(data: ['scrollTo' => false]) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalDocument" tabindex="-1" wire:ignore.self>
            <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Modal title</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body" wire:ignore>
                        <form wire:submit.prevent="uploadDocument" class="space-y-4 mb-4">
                            <div class="row">

                                <div class="col-4">
                                    <label for="ktp_document">Upload File KTP</label>
                                    <input type="file" id="filepond-upload-ktp" class="filepond"
                                        accept="application/pdf" multiple="false" />
                                    {{-- Show error if file is not valid --}}
                                    @error('ktp_document')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-4">
                                    <label for="photo_document">Upload File Pas Foto</label>
                                    <input type="file" id="filepond-upload-pas-foto" class="filepond"
                                        accept="image/png, image/jpeg, image/jpg" multiple="false" />
                                    {{-- Show error if file is not valid --}}
                                    @error('photo_document')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-4">
                                    <label for="form_b_document">Upload File Form B</label>
                                    <input type="file" id="filepond-upload-form-b" class="filepond"
                                        accept="application/pdf" multiple="false" />
                                    {{-- Show error if file is not valid --}}
                                    @error('form_b_document')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-4" id="justifikasi-usia-element">
                                    <label for="age_justification_document">Upload File Justifikasi Usia</label>
                                    <input type="file" id="filepond-upload-justifikasi-usia" class="filepond"
                                        accept="application/pdf" multiple="false" />
                                    {{-- Show error if file is not valid --}}
                                    @error('age_justification_document')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                            </div>

                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                <span wire:loading>
                                    <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                                    Memproses File
                                    ...
                                </span>
                                <span wire:loading.remove>
                                    <i class="ti ti-upload"></i> Upload Data
                                </span>
                            </button>
                        </form>
                    </div>
                    @if ($photoUrl || $ktpUrl || $formBUrl || $ageJustificationUrl)
                        <div class="modal-body">
                            <p class="text-danger small"> <b>*PERHATIAN*</b> Jika upload dokumen yang sudah ada, maka
                                akan
                                menggantikan dokumen sebelumnya</p>
                            <h3>Dokumen yang sudah di upload :</h3>
                            @if ($ktpUrl)
                                <div class="mt-2">
                                    <a href="{{ $ktpUrl }}" target="_blank"
                                        class="btn btn-outline-primary btn-sm" onclick="window.open(this.href, 'new', 'popup'); return false;">
                                        <i class="ti ti-file"></i> Lihat Dokumen KTP
                                    </a>
                                </div>
                            @endif
                            @if ($photoUrl)
                                <div class="mt-2">
                                    <a href="{{ $photoUrl }}" target="_blank"
                                        class="btn btn-outline-primary btn-sm" onclick="window.open(this.href, 'new', 'popup'); return false;">
                                        <i class="ti ti-file"></i> Lihat Dokumen Pas Foto
                                    </a>
                                </div>
                            @endif
                            @if ($formBUrl)
                                <div class="mt-2">
                                    <a href="{{ $formBUrl }}" target="_blank"
                                        class="btn btn-outline-primary btn-sm" onclick="window.open(this.href, 'new', 'popup'); return false;">
                                        <i class="ti ti-file"></i> Lihat Dokumen Form B
                                    </a>
                                </div>
                            @endif
                            @if ($ageJustificationUrl)
                                <div class="mt-2">
                                    <a href="{{ $ageJustificationUrl }}" target="_blank"
                                        class="btn btn-outline-primary btn-sm" onclick="window.open(this.href, 'new', 'popup'); return false;">
                                        <i class="ti ti-file"></i> Lihat Dokumen Keterangan Umur
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endif
                    <div class="modal-footer">
                        <button type="button" class="btn me-auto" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>


@push('scripts')
    <script>
        document.addEventListener('livewire:init', () => {
            console.log('Livewire loaded, initializing FilePond...');

            var bootstrap = tabler.bootstrap;

            const modalDocument = new bootstrap.Modal('#modalDocument', {});

            // Registrasi semua plugin yang dibutuhkan
            FilePond.registerPlugin(
                FilePondPluginFileValidateType,
                FilePondPluginFileValidateSize,
                FilePondPluginPdfPreview,
                FilePondPluginImagePreview,
            );

            const inputElement = document.getElementById('filepond-upload');

            const pond = FilePond.create(inputElement, {
                allowMultiple: false,
                maxFiles: 1,
                // excel file allowed
                acceptedFileTypes: ['application/vndopenxmlformats-officedocument.spreadsheetml.sheet',
                    'application/vnd.ms-excel'
                ],
                fileValidateTypeLabelExpectedTypes: 'Hanya file Excel (.xlsx, .xls) yang diperbolehkan',
                maxFileSize: '2MB',
                labelIdle: `<div class="text-center mb-2"> <i class="ti ti-upload fs-2 mb-3 text-muted"></i><br><strong>Drag & drop</strong> atau <span class="filepond--label-action">klik di sini</span> untuk upload</div>`,
                credits: false,
                // storeAsFile: true,
                labelFileTypeNotAllowed: 'Hanya file Excel (.xlsx, .xls) yang diperbolehkan',
                labelMaxFileSizeExceeded: 'Ukuran file terlalu besar (maksimal 2MB)',
                labelMaxFileSize: 'Maksimal ukuran file adalah 2MB',
                labelFileProcessingError: 'Terjadi kesalahan saat mengunggah file',
                labelFileProcessing: 'Mengunggah file...',
                labelFileProcessingComplete: 'File berhasil diunggah',
                labelFileProcessingAborted: 'Pengunggahan file dibatalkan',
            });

            let uploadedFileTmp = null;

            pond.on('addfile', (error, file) => {
                if (!error) {
                    inputElement.dispatchEvent(new Event('change', {
                        bubbles: true
                    }));
                    const fileData = file.file;
                    @this.upload('employee_xls', fileData, (fileName) => {
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
                    @this.removeUpload('employee_xls', uploadedFileTmp);
                    uploadedFileTmp = null;
                }
            });


            Livewire.on('uploadSuccess', (fileName) => {
                console.log('Data Pekerja berhasil di upload:', fileName);
                // Reset the pond after successful upload
                pond.removeFile(pond.getFile(fileName));
            });

            let uploadedKtpTmp = null;
            let uploadedPhotoTmp = null;
            let uploadedFormBTemp = null;
            let uploadedAgeJustificationTmp = null;

            const inputKtpElement = document.getElementById('filepond-upload-ktp');
            const inputPhotoElement = document.getElementById('filepond-upload-pas-foto');
            const inputFormBElement = document.getElementById('filepond-upload-form-b');
            const inputAgeJustificationElement = document.getElementById('filepond-upload-justifikasi-usia');


            Livewire.on('viewDocumentModal', (e) => {
                console.log(e);
                // console.log('KTP URL: ', e.ktpUrl);
                // console.log('PHOTO URL: ', e.photoUrl);
                // console.log('Form B URL: ', e.formBurl);

                modalDocument.show();

                document.getElementById('modalDocument').addEventListener('shown.bs.modal', () => {
                    document.querySelector('.modal-title').textContent =
                        `Lengkapi Dokumen Pekerja: ${e.full_name}`;

                    if (!FilePond.find(inputKtpElement)) {
                        console.log('Creating FilePond instance for KTP upload');
                        const pondKtp = FilePond.create(inputKtpElement, {
                            allowMultiple: false,
                            maxFiles: 1,
                            acceptedFileTypes: ['application/pdf'],
                            fileValidateTypeLabelExpectedTypes: 'Hanya file PDF yang diperbolehkan',
                            maxFileSize: '2MB',
                            labelIdle: `<div class="text-center mb-2"> <i class="ti ti-upload fs-2 mb-3 text-muted"></i><br><strong>Drag & drop</strong> atau <span class="filepond--label-action">klik di sini</span> untuk upload KTP</div>`,
                            credits: false,
                            allowReplace: true,
                            allowRemove: true,
                            allowRevert: false,
                        });

                        pondKtp.on('addfile', (error, file) => {
                            if (!error) {
                                // Jangan upload file jika file berasal dari preload (type: 'local')
                                if (file.origin === FilePond.FileOrigin.LOCAL) {
                                    console.log(
                                        'File berasal dari preload, tidak perlu upload ulang.'
                                    );
                                    return;
                                }

                                inputKtpElement.dispatchEvent(new Event('change', {
                                    bubbles: true
                                }));

                                const fileData = file.file;

                                @this.upload('ktp_document', fileData, (fileName) => {
                                    console.log('KTP uploaded successfully:',
                                        fileName);
                                    uploadedKtpTmp = fileName;
                                }, (error) => {
                                    console.error('Error uploading KTP:', error);
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Upload Gagal',
                                        text: 'Terjadi kesalahan saat mengunggah KTP. Silakan coba lagi.',
                                        confirmButtonText: 'OK'
                                    });
                                });
                            }
                        });

                        pondKtp.on('removefile', (err, file) => {
                            console.log('File removed');
                            inputKtpElement.value = '';
                            inputKtpElement.dispatchEvent(new Event('change', {
                                bubbles: true
                            }));

                            if (uploadedKtpTmp) {
                                @this.removeUpload('ktp_document', uploadedKtpTmp);
                                uploadedKtpTmp = null;
                            }
                        });
                    }

                    if (!FilePond.find(inputPhotoElement)) {
                        const pondPhoto = FilePond.create(inputPhotoElement, {
                            allowMultiple: false,
                            maxFiles: 1,
                            acceptedFileTypes: ['image/png', 'image/jpeg', 'image/jpg', ],
                            fileValidateTypeLabelExpectedTypes: 'Hanya file Gambar (PNG, JPEG) yang diperbolehkan',
                            maxFileSize: '2MB',
                            labelIdle: `<div class="text-center mb-2"> <i class="ti ti-upload fs-2 mb-3 text-muted"></i><br><strong>Drag & drop</strong> atau <span class="filepond--label-action">klik di sini</span> untuk upload Pas Foto</div>`,
                            credits: false,
                            // storeAsFile: true,
                            allowReplace: true,
                            allowRemove: true,
                            allowRevert: false,
                        });

                        pondPhoto.on('addfile', (error, file) => {
                            if (!error) {
                                inputPhotoElement.dispatchEvent(new Event(
                                    'change', {
                                        bubbles: true
                                    }));
                                const fileData = file.file;
                                @this.upload('photo_document', fileData, (
                                    fileName) => {
                                    console.log(
                                        'Pas Foto uploaded successfully:',
                                        fileName);
                                    uploadedPhotoTmp = fileName;
                                }, (error) => {
                                    console.error(
                                        'Error uploading Pas Foto:',
                                        error);
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Upload Gagal',
                                        text: 'Terjadi kesalahan saat mengunggah Pas Foto. Silakan coba lagi.',
                                        confirmButtonText: 'OK'
                                    });
                                });
                            }
                        });

                        pondPhoto.on('removefile', (err, file) => {
                            console.log('File removed');
                            inputPhotoElement.value = '';
                            inputPhotoElement.dispatchEvent(new Event(
                                'change', {
                                    bubbles: true
                                }));

                            if (uploadedPhotoTmp) {
                                @this.removeUpload('photo_document',
                                    uploadedPhotoTmp);
                                uploadedPhotoTmp = null;
                            }
                        });
                    }

                    if (!FilePond.find(inputFormBElement)) {
                        const pondFormB = FilePond.create(inputFormBElement, {
                            allowMultiple: false,
                            maxFiles: 1,
                            acceptedFileTypes: ['application/pdf'],
                            fileValidateTypeLabelExpectedTypes: 'Hanya file PDF yang diperbolehkan',
                            maxFileSize: '2MB',
                            labelIdle: `<div class="text-center mb-2"> <i class="ti ti-upload fs-2 mb-3 text-muted"></i><br><strong>Drag & drop</strong> atau <span class="filepond--label-action">klik di sini</span> untuk upload Form B</div>`,
                            credits: false,
                            // storeAsFile: true,
                        });

                        pondFormB.on('addfile', (error, file) => {
                            if (!error) {
                                inputFormBElement.dispatchEvent(new Event(
                                    'change', {
                                        bubbles: true
                                    }));
                                const fileData = file.file;
                                @this.upload('form_b_document', fileData, (
                                    fileName) => {
                                    console.log(
                                        'Form B uploaded successfully:',
                                        fileName);
                                    uploadedFormBTemp = fileName;
                                }, (error) => {
                                    console.error('Error uploading Form B:',
                                        error);
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Upload Gagal',
                                        text: 'Terjadi kesalahan saat mengunggah Form B. Silakan coba lagi.',
                                        confirmButtonText: 'OK'
                                    });
                                });
                            }
                        });
                        pondFormB.on('removefile', (err, file) => {
                            console.log('File removed');
                            inputFormBElement.value = '';
                            inputFormBElement.dispatchEvent(new Event(
                                'change', {
                                    bubbles: true
                                }));

                            if (uploadedFormBTemp) {
                                @this.removeUpload('form_b_document',
                                    uploadedFormBTemp);
                                uploadedFormBTemp = null;
                            }
                        });
                    }

                    if (e.employeeAge > 55) {
                        document.getElementById('justifikasi-usia-element').style.display = 'block';
                        if (!FilePond.find(inputAgeJustificationElement)) {
                            const pondAgeJustification = FilePond.create(
                                inputAgeJustificationElement, {
                                    allowMultiple: false,
                                    maxFiles: 1,
                                    acceptedFileTypes: ['application/pdf'],
                                    fileValidateTypeLabelExpectedTypes: 'Hanya file PDF yang diperbolehkan',
                                    maxFileSize: '2MB',
                                    labelIdle: `<div class="text-center mb-2"> <i class="ti ti-upload fs-2 mb-3 text-muted"></i><br><strong>Drag & drop</strong> atau <span class="filepond--label-action">klik di sini</span> untuk upload Dokumen Justifikasi Usia</div>`,
                                    credits: false,
                                    // storeAsFile: true,
                                });

                            pondAgeJustification.on('addfile', (error,
                                file) => {
                                if (!error) {
                                    inputAgeJustificationElement.dispatchEvent(
                                        new Event('change', {
                                            bubbles: true
                                        }));
                                    const fileData = file.file;
                                    @this.upload('age_justification_document',
                                        fileData, (fileName) => {
                                            console.log(
                                                'Dokumen Justifikasi Usia uploaded successfully:',
                                                fileName);
                                            uploadedAgeJustificationTmp = fileName;
                                        }, (error) => {
                                            console.error(
                                                'Error uploading Dokumen Justifikasi Usia:',
                                                error);
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Upload Gagal',
                                                text: 'Terjadi kesalahan saat mengunggah Dokumen Justifikasi Usia. Silakan coba lagi.',
                                                confirmButtonText: 'OK'
                                            });
                                        });
                                }
                            });
                            pondAgeJustification.on('removefile', (err,
                                file) => {
                                console.log('File removed');
                                inputAgeJustificationElement.value = '';
                                inputAgeJustificationElement.dispatchEvent(
                                    new Event('change', {
                                        bubbles: true
                                    }));

                                if (uploadedAgeJustificationTmp) {
                                    @this.removeUpload(
                                        'age_justification_document',
                                        uploadedAgeJustificationTmp);
                                    uploadedAgeJustificationTmp = null;
                                }
                            });

                        }
                    }else{
                        uploadedAgeJustificationTmp = null;
                        document.getElementById('justifikasi-usia-element').style.display = 'none';
                    }
                });
            });

            document.getElementById('modalDocument').addEventListener('hidden.bs.modal', () => {
                console.log('Modal closed, resetting FilePond instances');
                // Reset all FilePond instances
                FilePond.find(document.getElementById('filepond-upload-ktp'))?.removeFile();
                FilePond.find(document.getElementById('filepond-upload-pas-foto'))?.removeFile();
                FilePond.find(document.getElementById('filepond-upload-form-b'))?.removeFile();
                FilePond.find(document.getElementById('filepond-upload-justifikasi-usia'))?.removeFile();

                FilePond.find(document.getElementById('filepond-upload-ktp'))?.destroy();
                FilePond.find(document.getElementById('filepond-upload-pas-foto'))?.destroy();
                FilePond.find(document.getElementById('filepond-upload-form-b'))?.destroy();
                FilePond.find(document.getElementById('filepond-upload-justifikasi-usia'))?.destroy();

                uploadedKtpTmp = null;
                uploadedPhotoTmp = null;
                uploadedFormBTemp = null;
                uploadedAgeJustificationTmp = null;
            });

            document.getElementById('btn-submit-all-employee').addEventListener('click', () => {
                Swal.fire({
                    title: 'Ajukan semua data Pekerja? ',
                    html: '<span class=" text-muted">Pekerja yang di ajukan hanya pekerja yang sudah di upload dokumen nya, jika belum makan tidak akan di ajukan secara otomatis</span>',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, ajukan!',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#388cda',
                    cancelButtonColor: '#dc3545',
                }).then((result) => {
                    if (result.isConfirmed) {
                        Livewire.dispatch('submitAllEmployee')
                    }
                });
            });

            Livewire.on('uploadSucceed', () => {
                console.log('Data Pekerja berhasil di upload');
                // Reset the pond after successful upload
                modalDocument.hide();
            })

        });
    </script>
@endpush
