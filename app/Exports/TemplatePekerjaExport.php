<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TemplatePekerjaExport implements FromArray, WithStyles, WithTitle, WithColumnFormatting, WithColumnWidths, WithEvents
{
    protected string $companyName;

    public function __construct(string $companyName)
    {
        $this->companyName = $companyName;
    }

    public function array(): array
    {
        // Header kolom data
        $header = ['NO', 'Nama Lengkap', 'NIK', 'Passport', 'Tempat Lahir', 'Tanggal Lahir', 'Jenis Kelamin', 'Jabatan', 'Domisili', 'Justifikasi'];

        // 20 baris data kosong
        $rows = [];
        for ($i = 1; $i <= 50; $i++) {
            $rows[] =
                [
                    $i,
                    "",
                    "'",
                    "'",
                    "",
                    "",
                    "",
                    "",
                    "",
                    "",
                ];
        }

        // Susun struktur array Excel
        return [
            ['Formulir Data Pekerja'],     // Baris 1: Judul
            ['Data Pekerja - ' . $this->companyName], // Baris 2: Nama perusahaan
            ['Catatan: '], // Baris 3: Kosong (jeda)
            ['1. Untuk Kolom NIK dan Passport diisi salah satu dan mohon masukkan NIK atau Passport sebagai teks (contoh: \'32121112233445). '], // Baris 3: Kosong (jeda)
            ['2. Isi NIK jika WNI dan Passport jika WNA.'],     // Baris 4: Kosong (jeda)
            ['3. Untuk format tanggal lahir: dd/mm/yyyy (contoh: 15/06/1990).'], // Baris 5: Kosong (jeda)
            ['4. Mohon isi form Justifikasi jika umur Pekerja Melebihi 56 Tahun. Setelah itu Upload Dokumen Justifikasi di menu Upload/Draft Pekerja.'], // Baris 6: Kosong (jeda)
            $header,                        // Baris 4: Header kolom
            ...$rows                        // Baris 5–24: Data pekerja kosong
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        // Merge sel untuk header dan nama perusahaan
        $sheet->mergeCells('A1:J1');
        $sheet->mergeCells('A2:J2');
        $sheet->mergeCells('A3:J3');
        $sheet->mergeCells('A4:J4');
        $sheet->mergeCells('A5:J5');
        $sheet->mergeCells('A6:J6');
        $sheet->mergeCells('A7:J7');

        // Style Judul (Baris 1)
        $sheet->getStyle('A1')->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'font' => [
                'bold' => true,
                'size' => 14,
            ],
        ]);

        // Style Nama Perusahaan (Baris 2)
        $sheet->getStyle('A2')->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
            'font' => [
                'italic' => true,
                'size' => 12,
            ],
        ]);

        $sheet->getStyle('A3')->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
            ],
            'font' => [
                'italic' => true,
                'size' => 10,
                'color' => ['rgb' => '808080'],
            ],
        ]);

        $sheet->getStyle('A4')->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
            ],
            'font' => [
                'italic' => true,
                'size' => 10,
                'color' => ['rgb' => '808080'],
            ],
        ]);

        $sheet->getStyle('A5')->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
            ],
            'font' => [
                'italic' => true,
                'size' => 10,
                'color' => ['rgb' => '808080'],
            ],
        ]);

        $sheet->getStyle('A6')->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
            ],
            'font' => [
                'italic' => true,
                'size' => 10,
                'color' => ['rgb' => '808080'],
            ],
        ]);

        $sheet->getStyle('A7')->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
            ],
            'font' => [
                'italic' => true,
                'size' => 10,
                'color' => ['rgb' => '808080'],
            ],
        ]);

        $sheet->getStyle('A8:J8')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
            ],
        ]);

        $sheet->getStyle('F8:F58')->getNumberFormat()->setFormatCode('dd/mm/yyyy');



        // Range data mulai dari A4:G24 (20 pekerja + header)
        $dataRange = 'A8:J58';

        // All border tipis
        $sheet->getStyle($dataRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Border tebal di luar
        $sheet->getStyle($dataRange)->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THICK);

        // Kembalikan array kosong karena style sudah diterapkan manual
        return [];
    }

    public function columnFormats(): array
    {
        return [
            'A' => '@', // Format teks untuk kolom NO
            'B' => '@', // Format teks untuk kolom Nama Lengkap
            'C' => '@', // Format teks untuk kolom NIK
            'D' => '@', // Format teks untuk kolom Passport
            'E' => '@', // Format teks untuk kolom Tempat Lahir
            'F' => NumberFormat::FORMAT_DATE_DDMMYYYY, // Format tanggal untuk kolom Tanggal Lahir
            'G' => '@', // Format teks untuk kolom Jenis Kelamin
            'H' => '@', // Format teks untuk kolom Jabatan
            'I' => '@', // Format teks untuk kolom Domisili
            'J' => '@', // Format teks untuk kolom Justifikasi
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,   // Lebar kolom NO
            'B' => 25,  // Lebar kolom Nama Lengkap
            'C' => 15,  // Lebar kolom NIK
            'D' => 15,  // Lebar kolom Passport
            'E' => 20,  // Lebar kolom Tempat Lahir
            'F' => 15,  // Lebar kolom Tanggal Lahir
            'G' => 15,  // Lebar kolom Jenis Kelamin
            'H' => 15,  // Lebar kolom Jabatan
            'I' => 18,  // Lebar kolom Domisili
            'J' => 20,  // Lebar kolom Justifikasi
        ];
    }

    public function title(): string
    {
        return 'Template Pekerja';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Dropdown "Jenis Kelamin" di kolom G8:58
                for ($row = 8; $row <= 58; $row++) {
                    $cell = 'G' . $row;
                    $validation = $event->sheet->getCell($cell)->getDataValidation();
                    $validation->setType(DataValidation::TYPE_LIST);
                    $validation->setErrorStyle(DataValidation::STYLE_STOP);
                    $validation->setAllowBlank(false);
                    $validation->setShowInputMessage(true);
                    $validation->setShowErrorMessage(true);
                    $validation->setShowDropDown(true);
                    $validation->setFormula1('"laki-laki,perempuan"'); // list value
                    $validation->setErrorTitle('Input tidak valid');
                    $validation->setError('Silakan pilih antara "laki-laki" atau "perempuan".');
                    $validation->setPromptTitle('Pilih Jenis Kelamin');
                    $validation->setPrompt('Pilih antara "laki-laki" atau "perempuan" dari dropdown.');

                    $cell = 'J' . $row;
                    $validation = $event->sheet->getCell($cell)->getDataValidation();
                    $validation->setType(DataValidation::TYPE_LIST);
                    $validation->setErrorStyle(DataValidation::STYLE_STOP);
                    $validation->setAllowBlank(true);
                    $validation->setShowInputMessage(true);
                    $validation->setShowErrorMessage(true);
                    $validation->setShowDropDown(true);
                    $validation->setFormula1('"Ya,Tidak"'); // list value
                    $validation->setErrorTitle('Input tidak valid');
                    $validation->setError('Silakan pilih "Ya" jika umur pekerja melebihi 56 tahun.');
                    $validation->setPromptTitle('Pilih Ya/Tidak');
                    $validation->setPrompt('Pilih "Ya" jika umur pekerja melebihi 56 tahun.');
                }


            }
        ];
    }
}
