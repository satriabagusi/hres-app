<div>
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
                            </div>
                        </div>
                    </div>
                </div>
                <div id="advanced-table">
                    <div class="table-responsive">
                        <table class="table table-vcenter table-selectable">
                            <thead>
                                <tr>
                                    <th width="10px">NO</th>
                                    <th>Nama Pekerja</th>
                                    <th>Nama Perusahaan</th>
                                    <th>USIA</th>
                                    <th>NIK</th>
                                    <th>Jabatan</th>
                                    <th>Status</th>
                                    <th class="text-center">Verifikasi Medical</th>
                                    <th class="text-center">Verifikasi Security</th>
                                    <th class="text-center">Verifikasi HSE</th>
                                    <th>Tempat Lahir</th>
                                    <th>Tanggal Lahir</th>
                                    <th>#</th>
                                </tr>
                            </thead>
                            <tbody class="table-tbody">
                                @foreach ($employees as $item)
                                    <tr>
                                        <td width="50px" class="text-center">
                                            {{-- Show number based on current page --}}
                                            {{ $loop->iteration + ($employees->currentPage() - 1) * $employees->perPage() }}
                                        </td>
                                        <td>
                                            <span class="text-body">{{ $item->full_name }}</span>
                                        </td>
                                        <td>
                                            {{ $item->project_contractor->contractor->company_name }}
                                        </td>
                                        <td class="">
                                            {{ \Carbon\Carbon::parse($item->birth_date)->age }}

                                            @if (\Carbon\Carbon::parse($item->birth_date)->age >= 56)
                                                <a href="{{ 'uploads/employee_documents/' . $item->age_justification_document }}"
                                                    onclick="window.open(this.href, 'new', 'popup'); return false;"
                                                    class="text-decoration-none"> <i
                                                        class="ti ti-info-triangle text-red"></i> </a>
                                            @endif

                                        </td>

                                        <td class="">{{ $item->nik }}</td>
                                        <td class="">{{ $item->position }}</td>
                                        <td class="sort-status">
                                            @if ($item->status == 'approved')
                                                <span class="badge bg-lime text-lime-fg">Disetujui</span>
                                            @elseif($item->status == 'draft')
                                                <span class="badge bg-orange text-orange-fg">Draft</span>
                                            @elseif($item->status == 'submitted')
                                                <span class="badge bg-blue text-blue-fg">Diajukan</span>
                                            @elseif($item->status == 'rejected')
                                                <span class="badge bg-red text-red-fg">Ditolak</span>
                                            @endif
                                        </td>
                                        <td class="sort-status text-center">
                                            @if ($item->medical_review->status == 'on_review')
                                                <span class="badge bg-yellow text-yellow-fg ">On Review</span>
                                            @elseif($item->medical_review->status == 'approved')
                                                <a class="badge bg-lime text-lime-fg"
                                                    href="{{ asset('uploads/employee_documents/' . $item->medical_review->mcu_document) }}"
                                                    onclick="window.open(this.href, 'new', 'popup'); return false;">
                                                    Disetujui (Klik Untuk Melihat)
                                                </a>
                                            @elseif($item->medical_review->status == 'rejected')
                                                <span class="badge bg-red text-red-fg" wire:click='alasanRejectMcu({{ $item->id }})'>Ditolak</span>
                                            @endif
                                        </td>
                                        <td class="sort-status">
                                            @if ($item->security_review->status == 'on_review')
                                                <span class="badge bg-yellow text-yellow-fg">On Review</span>
                                                <br>
                                            @elseif($item->security_review->status == 'approved')
                                                <span class="badge bg-lime text-lime-fg">Disetujui</span>
                                            @elseif($item->security_review->status == 'rejected')
                                                <span class="badge bg-red text-red-fg">Ditolak</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($item->induction_card_number)
                                                <span class="badge bg-lime text-lime-fg">Disetujui</span>
                                            @else
                                                <span class="badge bg-yellow text-yellow-fg">On Review</span>
                                            @endif

                                        </td>
                                        <td class="">{{ $item->birth_place }}</td>
                                        <td class="">
                                            {{ \Carbon\Carbon::parse($item->birth_date)->format('d-m-Y') }}
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-blue"
                                                wire:click='detailData({{ $item->id }})'>Detail Data</button>
                                            <br>
                                            @if (Auth::user()->role == 'medical')
                                                <button class="btn btn-sm btn-outline-teal"
                                                    wire:click="modalUploadMCU({{ $item->id }})">Setujui Verifikasi
                                                    Medical</button>
                                                <br>
                                                <button class="btn btn-sm btn-outline-pink btn-decline-mcu"
                                                    data-id='{{ $item->id }}'>
                                                    Tolak Verfikasi
                                                </button>
                                            @endif
                                            @if (Auth::user()->role == 'security')
                                                <button class="btn btn-sm btn-outline-teal"
                                                    wire:click="modalVerificationSecurity({{ $item->id }})">Setujui
                                                    Verifikasi Security</button>
                                                <br>
                                                <button class="btn btn-sm btn-outline-pink btn-decline-security"
                                                    data-id='{{ $item->id }}'>
                                                    Tolak Verfikasi
                                                </button>
                                            @endif
                                            @if (Auth::user()->role == 'hse')
                                                <button class="btn btn-sm btn-outline-teal"
                                                    wire:click="modalVerificationHSE({{ $item->id }})">Setujui
                                                    Verifikasi HSE</button>
                                                <br>
                                                <button class="btn btn-sm btn-outline-pink btn-decline-hse"
                                                    data-id='{{ $item->id }}'>
                                                    Tolak Verfikasi
                                                </button>
                                            @endif

                                        </td>

                                    </tr>
                                @endforeach
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
                    <form wire:submit.prevent="uploadMCUFile">
                        <div class="mb-3 col-auto">
                            <label for="mcu_document">Upload File MCU</label>
                            <input type="file" id="filepond-upload-mcu" class="filepond" accept="application/pdf"
                                multiple="false" />
                            @error('mcu_document')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3 col-auto">
                            <label class="form-label">Hazard Status</label>
                            <select class="form-select" id="select-hazard-status">
                                <option value="low_risk" class="text-uppercase">Low Risk</option>
                                <option value="medium_risk" class="text-uppercase">Medium Risk</option>
                                <option value="high_risk" class="text-uppercase">High Risk</option>
                            </select>
                        </div>
                        <div class="mb-3 col-auto">
                            <label class="form-label">Fit Status</label>
                            <select class="form-select" id="select-fit-status">
                                <option value="unfit" class="text-red">Unfit</option>
                                <option value="fit_with_note" class="text-teal">Fit with Note</option>
                                <option value="follow_up" class="text-yellow">Follow Up</option>
                                <option value="fit" class="text-green">Fit</option>
                            </select>
                        </div>
                        <div class="mb-3 col-auto">
                            <label class="form-label">Catatan</label>
                            <textarea class="form-control" rows="2" placeholder="Catatan" wire:model="notes" id="notes"></textarea>
                        </div>
                    </form>

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" wire:click='uploadMCUFile'>upload</button>
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
                            <td id="selected_employee_name"> </td>
                        </tr>
                        <tr>
                            <td width="160px"> Tempat & Tgl Lahir </td>
                            <td width="10px"> : </td>
                            <td id="selected_employee_birth_place_date"> </td>
                        </tr>
                        <tr>
                            <td width="160px"> NIK </td>
                            <td width="10px"> : </td>
                            <td id="selected_employee_nik"> </td>
                        </tr>
                        <tr>
                            <td width="160px"> Jabatan </td>
                            <td width="10px"> : </td>
                            <td id="selected_employee_position"> </td>
                        </tr>
                        <tr>
                            <td width="160px"> Kode Hazard </td>
                            <td width="10px"> : </td>
                            <td id="selected_employee_risk_notes"> </td>
                        </tr>
                        <tr>
                            <td width="160px"> KTP </td>
                            <td width="10px"> : </td>
                            <td id="selected_employee_ktp_document"> </td>
                        </tr>
                        <tr>
                            <td width="160px"> Pas Foto </td>
                            <td width="10px"> : </td>
                            <td id="selected_employee_photo"> </td>
                        </tr>
                    </table>
                </div>
                <div class="modal-footer">
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

            FilePond.registerPlugin(
                FilePondPluginFileValidateType,
                FilePondPluginFileValidateSize,
                FilePondPluginPdfPreview,
            );

            const inputElement = document.getElementById('filepond-upload-mcu');
            let uploadedDocMcuTmp = null;

            const selectEl = document.getElementById('select-hazard-status');
            const selectFitStatusEl = document.getElementById('select-fit-status');

            Livewire.on('showModalUploadMCU', (id) => {
                modalDocumentMCU.show();
                console.log('Show modal upload mcu');

                document.getElementById('modalDocumentMCU').addEventListener('shown.bs.modal', () => {
                    const mcuPond = FilePond.create(inputElement, {
                        allowMultiple: false,
                        maxFiles: 1,
                        // excel file allowed
                        acceptedFileTypes: [
                            'application/pdf'
                        ],
                        fileValidateTypeLabelExpectedTypes: 'Hanya file PDF yang diperbolehkan',
                        maxFileSize: '2MB',
                        labelIdle: `<div class="text-center mb-2"> <i class="ti ti-upload fs-2 mb-3 text-muted"></i><br><strong>Drag & drop</strong> atau <span class="filepond--label-action">klik di sini</span> untuk upload</div>`,
                        credits: false,
                        storeAsFile: true,
                        labelFileTypeNotAllowed: 'Hanya file PDF yang diperbolehkan',
                        labelMaxFileSizeExceeded: 'Ukuran file terlalu besar (maksimal 2MB)',
                        labelMaxFileSize: 'Maksimal ukuran file adalah 2MB',
                        labelFileProcessingError: 'Terjadi kesalahan saat mengunggah file',
                        labelFileProcessing: 'Mengunggah file...',
                        labelFileProcessingComplete: 'File berhasil diunggah',
                        labelFileProcessingAborted: 'Pengunggahan file dibatalkan',
                    });

                    mcuPond.on('addfile', (error, file) => {
                        if (!error) {
                            inputElement.dispatchEvent(new Event('change', {
                                bubbles: true
                            }));
                            const fileData = file.file;
                            @this.upload('mcu_document', fileData, (fileName) => {
                                console.log('File uploaded successfully:',
                                    fileName);
                                uploadedDocMcuTmp = fileName;
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

                    mcuPond.on('removefile', (err, file) => {
                        console.log('File removed');
                        inputElement.value = '';
                        inputElement.dispatchEvent(new Event('change', {
                            bubbles: true
                        }));

                        if (uploadedDocMcuTmp) {
                            @this.removeUpload('mcu_document', uploadedDocMcuTmp);
                            uploadedDocMcuTmp = null;
                        }
                    });

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

                    document.getElementById('notes').addEventListener('change', (e) => {
                        @this.set('notes', e.target.value);
                    })
                });

                document.getElementById('modalDocumentMCU').addEventListener('hidden.bs.modal', () => {
                    // remove Filepond instance

                    if (Filepond.find(document.getElementById('filepond-upload-mcu'))) {
                        Filepond.find(document.getElementById('filepond-upload-mcu'))?.removeFile();
                        Filepond.find(document.getElementById('filepond-upload-mcu'))?.destroy();
                    }

                    uploadedDocMcuTmp = null;

                    // Reset date pickers
                    const startDatepicker = document.getElementById('start-date-picker-icon-mcu');
                    if (startDatepicker) {
                        startDatepicker.value = '';
                    }
                    const endDatepicker = document.getElementById('end-date-picker-icon-mcu');
                    if (endDatepicker) {
                        endDatepicker.value = '';
                    }
                })
            })

            Livewire.on('uploadMCUSuccess', (e) => {
                alert('ok')
                uploadedDocMcuTmp = null;

                modalDocumentMCU.hide();
                if (Filepond.find(document.getElementById('filepond-upload-mcu'))) {
                    Filepond.find(document.getElementById('filepond-upload-mcu'))?.removeFile();
                    Filepond.find(document.getElementById('filepond-upload-mcu'))?.destroy();
                }
            })

            document.querySelectorAll('.btn-decline-mcu').forEach(button => {
                button.addEventListener('click', () => {
                    let idEmp = button.getAttribute('data-id');
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
                                id: idEmp,
                                alasan: result.value.alasan,
                                keterangan: result.value.keterangan
                            });
                        }
                    });
                });
            });

            var detailDataFromLivewire = null;

            Livewire.on('showModalDetail', (e) => {
                detailDataFromLivewire = e.data;
                console.log(detailDataFromLivewire); // Debug, pastikan data masuk

                modalDetailData.show();
            })

            document.getElementById('modalDetailData').addEventListener('shown.bs.modal', (e) => {
                document.querySelector('.modal-detail-title').textContent =
                    `Detail Data Pekerja: ${detailDataFromLivewire.full_name} - ${detailDataFromLivewire.project_contractor.contractor.company_name}`;

                // Tampilkan teks biasa
                var companyName = detailDataFromLivewire.project_contractor.contractor.company_name || '-';
                var fullName = detailDataFromLivewire.full_name || '-';
                document.getElementById('selected_employee_name').textContent = fullName + ' (' +
                    companyName + ')' || '-';
                document.getElementById('selected_employee_nik').textContent = detailDataFromLivewire.nik ||
                    '-';
                document.getElementById('selected_employee_position').textContent = detailDataFromLivewire
                    .position || '-';

                // Format tanggal tanggal - bulan - tahun
                var birth_date = detailDataFromLivewire.birth_date ? new Date(detailDataFromLivewire
                    .birth_date).toLocaleDateString('id-ID').split('/').join('-') : '-';
                var birth_place = detailDataFromLivewire.birth_place ?? '-';

                document.getElementById('selected_employee_birth_place_date').textContent = birth_place +
                    ', ' + birth_date || '-';

                var classRisk = '';
                // using badge not text-color
                if (detailDataFromLivewire.medical_review.risk_notes == 'low_risk') {
                    classRisk = 'text-success';
                } else if (detailDataFromLivewire.medical_review.risk_notes == 'medium_risk') {
                    classRisk = 'text-warning';
                } else if (detailDataFromLivewire.medical_review.risk_notes == 'high_risk') {
                    classRisk = 'text-danger';
                }

                document.getElementById('selected_employee_risk_notes').textContent = detailDataFromLivewire
                    .medical_review.risk_notes ||
                    '-';
                document.getElementById('selected_employee_risk_notes').classList.add(classRisk);

                // Tampilkan link ke dokumen KTP
                const ktpDocEl = document.getElementById('selected_employee_ktp_document');
                if (detailDataFromLivewire.ktp_document) {
                    ktpDocEl.innerHTML =
                        `<a href="/uploads/employee_documents/${detailDataFromLivewire.ktp_document}" target="_blank" onclick="window.open(this.href, 'new', 'popup'); return false;">Lihat KTP</a>`;
                } else {
                    ktpDocEl.textContent = '-';
                }

                // Tampilkan Pas Foto
                const photoEl = document.getElementById('selected_employee_photo');
                if (detailDataFromLivewire.photo) {
                    photoEl.innerHTML =
                        `<img src="/uploads/employee_documents/${detailDataFromLivewire.photo}" alt="Pas Foto" style="max-height: 100px; border-radius: 4px;">`;
                } else {
                    photoEl.textContent = '-';
                }
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
                Swal.fire({
                    title: `Verifikasi Security `,
                    html: `<p>Anda yakin ingin melakukan verifikasi <br><b class='text-pink'>${e.data.full_name} (${e.data.project_contractor.contractor.company_name})</b> ?</p>
                            <div class="text-start mb-2">
                                <label>Nomor ID Security :</label>
                                <input type="text" id="no_id_security" class="form-control mb-3" placeholder="Nomor ID Security" value="${e.data.security_card_number ? e.data.security_card_number : ''}">
                            </div>
                    <small class="text-muted">Masukan Nomor ID Security Pekerja</small>`,
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Simpan',
                    cancelButtonText: 'Batal',
                    preConfirm: () => {
                        const inputValue = document.getElementById('no_id_security').value;
                        return {
                            no_id_security: inputValue
                        };
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        console.log('No Induction:', result.value.no_id_security);
                        Livewire.dispatch('submitVerificationSecurity', {
                            id: e.data.id,
                            no_id_security: result.value.no_id_security,
                        });
                    }
                });
            });

            Livewire.on('showModalAlasanRejectMcu', (e) => {
                var data = e.data;
                console.log(data);
                Swal.fire({
                    title: 'Alasan Reject',
                    html: `Alasan Reject MCU : <b>${data.medical_review.notes}</b>`,
                    showCancelButton: false,
                    confirmButtonText: 'OK',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    confirmButtonColor: '#3085d6',
                })
            })

        });
    </script>
@endpush
