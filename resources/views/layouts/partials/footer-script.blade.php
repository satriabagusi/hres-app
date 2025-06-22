<!-- Libs JS -->
<script src="{{ asset('libs/apexcharts/dist/apexcharts.min.js') }}"></script>
{{-- <script src="{{ asset('libs/jsvectormap/dist/js/jsvectormap.min.js') }}"></script> --}}
{{-- <script src="{{ asset('libs/jsvectormap/dist/maps/world.js') }}"></script> --}}
{{-- <script src="{{ asset('libs/jsvectormap/dist/maps/world-merc.js') }}"></script> --}}
<script src="{{ asset('libs/sweetalert2/js/sweetalert2.all.min.js') }}"></script>

<script src="{{ asset('libs/imask/dist/imask.js') }}"></script>
<script src="{{ asset('libs/litepicker/dist/litepicker.js') }}"></script>
<script src="{{ asset('libs/tom-select/dist/js/tom-select.complete.min.js') }}"></script>
<script src="{{ asset('libs/dropzone/dist/dropzone-min.js') }}"></script>

<script src="{{ asset('libs/filepond/dist/filepond.min.js') }}" ></script>
<script src="{{ asset('libs/filepond-plugin-pdf-preview/dist/filepond-plugin-pdf-preview.min.js') }}" ></script>
<script src="{{ asset('libs/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.js') }}" ></script>
<script src="{{ asset('libs/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.min.js') }}" ></script>
<script src="{{ asset('libs/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.min.js') }}" ></script>


<script  src="{{ asset('libs/filepond-plugin-file-encode/dist/filepond-plugin-file-encode.min.js') }}" ></script>

<script src="{{ asset('libs/litepicker/dist/litepicker.js') }}"></script>




<!-- Tabler Core -->
<script src="{{ asset('js/tabler.js') }}"></script>
{{-- <script src="{{ asset('js/demo.js') }}"></script> --}}

@livewireScripts



{{-- @if (session('alert')) --}}
<script>
    // Swal.fire({
    //     icon: 'success',
    //     title: 'test',
    //     showConfirmButton: false,
    //     timer: 4000
    // })
    Livewire.on('swal', (e) => {
        Swal.fire({
            icon: e.icon,
            title: e.title,
            html: e.text,
            showConfirmButton: false,
            timer: 4000
        })
    });

    Livewire.on('swal:confirm', (e) => {
        Swal.fire({
            title: e.title,
            html: e.text,
            icon: e.icon,
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: e.confirmText,
            cancelButtonText: e.cancelText,
            allowOutsideClick: false,
            allowEscapeKey: false
        }).then((result) => {
            if (result.isConfirmed) {
                Livewire.dispatch(e.action, {
                    id: e.id
                });
            }
        });
    });

    @if (session('success'))
        console.log('success');
        Swal.fire({
            icon: 'success',
            title: '{{ session('success') }}',
            showConfirmButton: false,
            timer: 4000
        })
    @endif

    @if (session('error'))
        console.log('error');
        Swal.fire({
            icon: 'error',
            title: '{{ session('error') }}',
            showConfirmButton: false,
            timer: 4000
        })
    @endif

    @if (session('warning'))
        console.log('warning');
        Swal.fire({
            icon: 'warning',
            title: '{{ session('warning') }}',
            showConfirmButton: false,
            timer: 4000
        })
    @endif
</script>

@stack('scripts')
{{-- @endif --}}
