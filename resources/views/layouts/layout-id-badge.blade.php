<!DOCTYPE html>
<html>

<head>
    <title>ID Badge_{{ $employee->full_name . '(' . $employee->user->company_name . ')' }}_{{ \Carbon\Carbon::now() }}</title>
    <link href="{{ asset('css/tabler.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('libs/tabler-icons/tabler-icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap">

    <style>
        @font-face {
            font-family: 'NimbusSans';
            src: url('/fonts/NimbusSansL-Regular.otf') format('opentype');
            font-weight: normal;
            font-style: normal;
        }
        
        body, td, th, div, span, p {
            font-family: 'NimbusSans', sans-serif;
        }
        body {
            color: #000;
            font-size: 18px !important;
            /*font-family: 'Arial', sans-serif*/
        }

        .py-top {
            /* using px instead rem */
            padding-top: 0.3rem !important;
            padding-bottom: -0.5rem !important;
        }

        .info {
            padding-top: 0.1rem !important;
            padding-left: 1.25rem !important;
        }

        .top-bar {
            margin-bottom: 0.5rem;
            padding-left: 1.25rem !important;
            font-size: 12px !important;
        }

        .employee-number {
            color: white;
            font-weight: bold;
            font-size: 12px;
            margin-bottom: -0.4rem;
        }

        .employee-name {
            color: white;
            font-weight: bold;
            font-size: 20px;
            margin-bottom: 0.05rem;
        }

        .employee-company {
            color: white;
            font-weight: bold;
            font-size: 16px;
            margin-bottom: -0.2rem;
        }

        .expiry-box {
            color: white;
            border: 1px solid white;
            padding-right: 20px;
            height: 4rem !important;
        }

        .area-tangki {
            background-color: #9d562c;
        }

        .area-all-area {
            background-color: #dc0612;
        }

        .area-isbl-osbl {
            background-color: #a0c60f;
        }

        .photo-box {
            padding-left: 1.4rem !important;
            margin-right: 0.5rem !important;
            /* background-color:cyan; */
        }

        .img-box {
            margin-top: 3px;
            width: 23.7mm;
            height: 21.7mm;
        }

        .col-max {
            flex: 1 1 auto;
        }

        .hazard-box {
            min-width: 2.1rem !important;
            max-width: 2.1rem !important;
            color: white;
            text-align: center;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: bold;
        }

        .hazard-hr {
            background-color: #fe000d
        }

        .hazard-mr {
            background-color: #ffc000
        }

        .hazard-lr {
            background-color: #a0c60e
        }

        .back-id {
            padding-left: 1.25rem !important;
            padding-right: 0.25rem !important;
            padding-top: 0.5rem !important;
            padding-bottom: 0.5rem !important;
            font-size: 10px !important;
        }

        .table-bordered-custom td,
        .table-bordered-custom th {
            border: 1px solid #dee2e6;
            font-size: 10px !important;
            /* Custom border to match image if needed */
        }

        .bg-mcu {
            background-color: #FFC107 !important;
            /* Bootstrap yellow color */
        }

        .bg-safety {
            background-color: #0DCAF0 !important;
            /* Bootstrap info/cyan color */
        }

        .checkmark {
            font-size: 10xp;
            /* Adjust size as needed */
            font-weight: bold;
            display: block;
            text-align: center;
        }

        .bg-hazard-mr {
            background-color: #ffc000 !important;
            /* Bootstrap yellow color */
        }
    </style>
</head>

<body>
    <div class="page">

        <img src="{{ asset('img/static/bg-id-2.png') }}"
            style="position: absolute; z-index: -99; top: 0; left: -2px; height: 100%;">

        <div class="row bg-white py-top top-bar fw-bold">
            <di class="col-2 me-4">
                APLOS
            </di>
            <div class="col-7">
                No.KTP {{ $employee->nik }}
            </div>
            <div class="col-1" style="padding-left: 24px !important;font-weight: bold">
                KPB
            </div>
        </div>


        <div class="info">
            <div class="employee-number">{{ $employee->security_card_number }}</div>
            <div class="employee-name">{{ $employee->full_name }}</div>
            <div class="employee-company">{{ $employee->user->company_name }}</div>
            {{-- <div class="employee-company">{{ "PT Pratama Abadi Jaya" }}</div> --}}
        </div>


        <div class="row" style="margin-top: 3.5mm">
            <div class="col-auto photo-box">
                <img class="img-box" src="{{ asset('uploads/employee_documents/' . $employee->photo) }}" alt="Photo">
            </div>
            <div
                class="col d-flex flex-column text-center justify-content-center expiry-box {{ $employee->security_review->area }}">
                <p class="mb-0" style="font-size:14px;">

                    @if($employee->security_review->area == 'area-all-area')
                        ALL AREA KPB
                    @elseif($employee->security_review->area == 'area-isbl-osbl')
                        AREA ISBL/OSBL
                    @elseif($employee->security_review->area == 'area-tangki')
                        AREA TANGKI
                    @endif

                </p>
                <p class=" fs-2 fw-bold mt-1" style="margin-bottom: -1mm;">{{ \Carbon\Carbon::parse($employee->security_review->expiry_date)->format('d/m/Y') }}</p>
            </div>
        </div>

        <div class="row justify-content-end" style="margin-top: -16px;margin-right: -1mm;">
            <div class="col-5 fs-5 " style="color: white; font-size: 10px; margin-top: 2.6mm;margin-right: 5mm">
                Violation&nbsp;
                <strong class="fw-bold " style="font-size: 13px;letter-spacing: 2px">OOO</strong>
            </div>

            <div class="col-2 float-end mt-1">
                <div class="hazard-box hazard-{{ collect(explode('_', $employee->medical_review->risk_notes))->map(fn($word) => Str::substr($word, 0, 1))->implode('') }}">
                    {{ collect(explode('_', $employee->medical_review->risk_notes))->map(fn($word) => Str::upper(Str::substr($word, 0, 1)))->implode('') }}

                </div>

            </div>

        </div>


    </div>

    <div class="page back-id">
        <table style="width:100%; border-collapse: collapse; font-size:9px; font-weight:bold;">
            <tr>
                <td style="width: 28%; padding:0.5px; margin:2px; line-height:1;">No.SI</td>
                <td style="width: 1%; padding:0.5px; margin:2px; line-height:1;">:</td>
                <td style="padding:0.5px; margin:2px; line-height:1;">{{ $employee->security_card_number }}</td>
            </tr>
            <tr>
                <td style="padding:0.5px; margin:2px; line-height:1;">Hubungan</td>
                <td style="padding:0.5px; margin:2px; line-height:1;">:</td>
                <td style="padding:0.5px; margin:2px; line-height:1;">Construction</td>
            </tr>
            <tr>
                <td style="padding:0.5px; margin:2px; line-height:1;">Jabatan</td>
                <td style="padding:0.5px; margin:2px; line-height:1;">:</td>
                <td style="padding:0.5px; margin:2px; line-height:1;">{{ $employee->position }}</td>
            </tr>
            <tr>
                <td style="padding:0.5px; margin:2px; line-height:1;">Gol.Darah</td>
                <td style="padding:0.5px; margin:2px; line-height:1;">:</td>
                <td style="padding:0.5px; margin:2px; line-height:1;">-</td>
            </tr>
            <tr>
                <td style="padding:0.5px; margin:2px; line-height:1; vertical-align: top;">Pekerjaan</td>
                <td style="padding:0.5px; margin:2px; line-height:1; vertical-align: top;">:</td>
                <td style="padding:0.5px; margin:2px; line-height:1; vertical-align: top;">
                    {{ $employee->project_contractor->project_name }}
                </td>
            </tr>
        </table>

        <div class="row justify-content-between" style="">
            <div class="col-4 text-start">
                <img src="{{ asset('img/static/mcu-induction-check.png') }}" width="90px" style="height: 50px"
                    alt="">
                <p style="position: absolute; margin-top: -24px; margin-left: 18px; font-size: 18px; font-family: Calibri;">
                    @if ($employee->medical_review->status == 'approved')
                        √
                    @else
                        -
                    @endif
                </p>
                <p style="position: absolute; margin-top: -24px; margin-left: 60px; font-size: 18px; font-family: Calibri;">
                    @if ($employee->security_review->status == 'approved')
                        √
                    @else
                        -
                    @endif
                </p>
            </div>
            <div class="col-5">
                <div class="sign-section me-1" style="margin-top: 3.5mm">
                    <div style="line-height: 1; font-weight: bold; font-size: 8px !important">Security PT Kilang
                        Pertamina Balikpapan</div>
                    <img src="{{ asset('img/static/ttd-jemingan.png') }}" alt="Sign"
                        style="position: absolute;width: 60px;margin-top:-3px;z-index:-99;margin-left: -9.5px;height: 50px">
                    <div class="fw-bold" style="position:absolute;z-index:2;font-size: 9.5px;margin-top: 15mm;right: 23mm">Jemingan
                    </div>
                </div>
            </div>
        </div>

        <p style="position:absolute;z-index:2 ; font-size:8.5px;line-height:1;bottom: 0px;padding-right: 1px;">
            <img src="{{ asset('img/static/logo-pertamina.png') }}" style="width: 73px" alt="">
            <br>
            Kartu ini milik PT KPB, jika menemukan kartu ini harap dikembalikan ke Security
            PT KPB. Kantor UP, Jl. Yos Sudarso No 1 Balikpapan - 76111 Telp. 0542- &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 514329
        </p>
    </div>
</body>


</html>
