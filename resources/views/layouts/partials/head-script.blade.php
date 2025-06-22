<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
<meta http-equiv="X-UA-Compatible" content="ie=edge" />
<meta name="referrer" content="no-referrer">
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>{{ $title }} - HSSE Review Employee System</title>

<link href="{{ asset('libs/tom-select/dist/css/tom-select.bootstrap5.min.css') }}" rel="stylesheet" />
<link href="{{ asset('css/tabler.css') }}" rel="stylesheet" />
<link href="{{ asset('css/tabler-flags.css') }}" rel="stylesheet" />
<link href="{{ asset('css/tabler-socials.css') }}" rel="stylesheet" />
<link href="{{ asset('css/tabler-payments.css') }}" rel="stylesheet" />
<link href="{{ asset('css/tabler-vendors.css') }}" rel="stylesheet" />
<link href="{{ asset('css/tabler-marketing.css') }}" rel="stylesheet" />
{{-- <link href="{{ asset('css/demo.css') }}" rel="stylesheet" /> --}}
<link rel="stylesheet" href="{{ asset('libs/dropzone/dist/dropzone.css') }}" />
<link rel="stylesheet" href="{{ asset('libs/filepond/dist/filepond.min.css') }}" />
<link rel="stylesheet"
    href="{{ asset('libs/filepond-plugin-pdf-preview/dist/filepond-plugin-pdf-preview.min.css') }}" />
<link rel="stylesheet"
    href="{{ asset('libs/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css') }}" />






<link rel="stylesheet" href="{{ asset('libs/litepicker/dist/css/litepicker.css') }}" />


<link rel="icon" href="{{ asset('img/hres-logo-transparent.png') }}" type="image/x-icon" />
<style>
    @import url('https://rsms.me/inter/inter.css');
</style>

<style>
    @import url('https://rsms.me/inter/inter.css');

    :root {
        --tblr-font-sans-serif: 'Inter Var', -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif;
    }

    body {
        font-feature-settings: "cv03", "cv04", "cv11";
    }

    .swal2-title {
        font-size: 16px !important;
    }

    .swal2-html-container {
        font-size: 14px !important;
    }

    .swal2-icon {
        font-size: 12px !important;
    }

    .swal2-container.swal2-center.swal2-backdrop-show {
        backdrop-filter: blur(4px) !important;
        -webkit-backdrop-filter: blur(4px) !important;
    }

    .btn-swal2-confirm {
        font-size: 14px !important;
        /* width: 100% !important; */
        flex: 1 0 0% !important;
        display: block !important;
        text-align: center !important;
        margin-top: 1rem !important;
        margin-left: 1rem !important;
    }

    .btn-swal2-cancel {
        font-size: 14px !important;
        /* width: 100% !important; */
        flex: 1 0 0% !important;
        display: block !important;
        text-align: center !important;
        margin-top: 1rem !important;
    }

    .swal2-actions {
        --sw2-gutter-y: 1rem !important;
        --sw2-gutter-x: .2rem !important;
        display: flex !important;
        flex-wrap: wrap !important;
        margin-top: calc(1* var(--sw2-gutter-y)) !important;
        margin-right: calc(-.1* var(--sw2-gutter-x)) !important;
        margin-left: calc(-.1* var(--sw2-gutter-x)) !important;
        justify-content: center !important;
        align-items: center !important;
        padding: 0 25px !important;
        /* border-top: 1px solid #dce1e7 !important;
        background-color: #f6f8fb !important; */
    }

    .swal2-popup .swal2-show .swal2-modal {
        width: 468px !important,
        overflow: visible !important;
    }

    .table-responsive .dropdown,
    .table-responsive .btn-group,
    .table-responsive .btn-group-vertical {
        position: static;
    }


    /* .swal-modal-tomselect {
        height: 360px !important;
        max-height: 560px !important;
        width: 600px !important;

    } */
</style>
<style>
    /* Warna border & background FilePond agar serasi dengan Tabler */
    .filepond--root {
        border: 1px solid #dce1e7;
        border-radius: 0.375rem;
        /* sama seperti .form-control di Tabler */
        background-color: #fff;
        font-family: var(--tblr-font-sans-serif);
        font-size: 0.875rem;
    }

    .filepond--panel-root {
        background-color: #f8f9fa;
        /* mirip dengan Tabler form bg */
    }

    .filepond--drop-label {
        color: #6c757d;
        /* abu-abu Tabler */
        padding: 1rem;
    }

    .filepond--label-action {
        color: #206bc4;
        /* primary Tabler */
        text-decoration: underline;
        cursor: pointer;
    }

    .filepond--item-panel {
        background-color: #e9ecef;
        /* untuk file preview box */
    }

    .filepond--file-info-main {
        color: #495057;
    }

    .filepond--file-status {
        font-size: 0.75rem;
        color: #868e96;
    }

    .filepond--credits {
        display: none !important;
    }
</style>


<link rel="stylesheet" href="{{ asset('libs/tabler-icons/tabler-icons.min.css') }}">

<link rel="stylesheet" href="{{ asset('libs/sweetalert2/css/sweetalert2.min.css') }}">


{{-- @routes() --}}
@livewireStyles
@vite('resources/js/app.js')
