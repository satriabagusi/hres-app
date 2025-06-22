<div class="">
    <div class="card card-md">
        <div class="card-body">
            <h2 class="h2 text-center mb-4">Buat Akun Perusahaan Anda</h2>
            <form wire:submit.prevent="save" autocomplete="off" novalidate enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Nama Perusahaan</label>
                    <input type="text" class="form-control @error('companyName') is-invalid @enderror"
                        placeholder="Nama Perusahaan" wire:model='companyName' autocomplete="off" />
                    @error('companyName')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Email address</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" placeholder="Email"
                        wire:model='email' autocomplete="off" />
                    @error('email')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-2">
                    <label class="form-label">
                        Password
                    </label>
                    <div class="input-group input-group-flat">
                        <input type="{{ $showPassword ? 'text' : 'password' }}"
                            class="form-control @error('password') is-invalid @enderror" placeholder="password"
                            wire:model='password' autocomplete="off" />
                        <span class="input-group-text">
                            <span wire:click="toggleShowPassword" class="link-secondary" style="cursor: pointer;">
                                <i class="{{ $showPassword ? 'ti ti-eye-off' : 'ti ti-eye' }}"></i>
                            </span>
                        </span>
                    </div>
                    @error('password')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                {{-- <div class="mb-3" wire:ignore>
                    <label class="form-label">Upload Form B (PDF)</label>
                    <input type="file" wire:model="formB" id="filepond-upload" class="filepond"
                        accept="application/pdf" multiple="false" />
                    @error('formB')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div> --}}

                {{-- show error --}}
                @if (session()->has('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif
                <div class="mb-2">
                    <div class="form-footer">
                        <button type="submit" class="btn btn-primary w-100">
                            <span class="spinner-border spinner-border-sm me-2" wire:loading wire:target="save"
                                role="status" aria-hidden="true"></span>
                                Daftar
                        </button>
                        <span class="d-block text-center mt-3">
                            Sudah punya akun? <a href="{{ route('login') }}" tabindex="-1">Login</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('livewire:init', () => {
            console.log('Livewire loaded, initializing FilePond...');

            // Registrasi semua plugin yang dibutuhkan
            FilePond.registerPlugin(
                FilePondPluginFileValidateType,
                FilePondPluginFileValidateSize,
                FilePondPluginImagePreview,
                FilePondPluginPdfPreview
            );

            const inputElement = document.getElementById('filepond-upload');

            const pond = FilePond.create(inputElement, {
                allowMultiple: false,
                maxFiles: 1,
                acceptedFileTypes: ['application/pdf'],
                fileValidateTypeLabelExpectedTypes: 'Hanya file PDF yang diperbolehkan',
                maxFileSize: '3MB',
                labelIdle: `<div class="text-center mb-2">
                    <i class="ti ti-upload fs-2 mb-3 text-muted"></i><br>
                    <strong>Drag & drop</strong> atau <span class="filepond--label-action">klik di sini</span> untuk upload
                </div>`,
                pdfPreviewHeight: 200,
                pdfPreviewMaxPages: 3,
                credits: false,
                storeAsFile: true,
                // invalid file type message
                labelFileTypeNotAllowed: 'Hanya file PDF yang diperbolehkan',
                // invalid file size message
                labelMaxFileSizeExceeded: 'Ukuran file terlalu besar (maksimal 3MB)',
                labelMaxFileSize: 'Maksimal ukuran file adalah 3MB',
                // file upload error message
                labelFileProcessingError: 'Terjadi kesalahan saat mengunggah file',
                // file processing message
                labelFileProcessing: 'Mengunggah file...',
                // file processing complete message
                labelFileProcessingComplete: 'File berhasil diunggah',
                // file processing error message
                labelFileProcessingAborted: 'Pengunggahan file dibatalkan',
            });

            let uploadedFileTmp = null;

            pond.on('addfile', (error, file) => {
                if (!error) {
                    inputElement.dispatchEvent(new Event('change', {
                        bubbles: true
                    }));

                    const fileData = file.file;
                    const element = document.querySelector('[wire\\:submit\\.prevent="save"]').closest(
                        'form').getAttribute('wire:id');

                    @this.upload('formB', fileData, (fileName) => {
                        console.log('File uploaded successfully:', fileName);
                        uploadedFileTmp = fileName;
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
                    @this.removeUpload('formB', uploadedFileTmp);
                    uploadedFileTmp = null;
                }
            });
        });
    </script>
@endpush
