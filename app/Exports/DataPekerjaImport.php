<?php

namespace App\Exports;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class DataPekerjaImport implements ToCollection, WithHeadingRow
{

    // tampung data yang sudah di-parse
    protected array $parsedData = [];

    public function headingRow(): int
    {
        return 8; // Baris ke-4 adalah baris judul
    }
    
    /**
     * Helper untuk cek apakah suatu nilai dianggap kosong
     * Hati-hati jika nilai null, trim tidak bisa langsung dipanggil
     */
    private function isEmptyValue($value): bool
    {
        return $value === null || trim((string) $value) === '';
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection(Collection $rows)
    {
        //
        foreach ($rows as $index => $row) {
            $barisExcel = $index + 9;


            // Normalisasi nilai: trim dan ubah tanda kutip tunggal menjadi kosong
            foreach ($row as $key => $value) {
                if (is_string($value)) {
                    $row[$key] = trim($value) === "'" ? '' : trim($value);
                }
            }
        
            // Cek apakah semua kolom penting kosong (skip baris ini jika kosong)
            if (
                ($row['nama_lengkap'] ?? '') === '' &&
                ($row['nik'] ?? '') === '' &&
                ($row['passport'] ?? '') === '' &&
                ($row['tempat_lahir'] ?? '') === '' &&
                ($row['jenis_kelamin'] ?? '') === '' &&
                ($row['jabatan'] ?? '') === '' &&
                ($row['domisili'] ?? '') === ''
            ) {
                break; // Keluar dari loop, karena baris selanjutnya dianggap tidak diisi
            }
        
            Log::info("Cek baris : " . json_encode($row));
            

            try {
                if (is_numeric($row['tanggal_lahir'])) {
                    $tglLahir = Date::excelToDateTimeObject($row['tanggal_lahir']);
                } else {
                    $tglLahir = Carbon::createFromFormat('d/m/Y', $row['tanggal_lahir']);
                }

                $formattedTanggalLahir = $tglLahir->format('Y-m-d');
            } catch (\Exception $e) {
                throw new Exception("Error pada Baris $barisExcel: Format tanggal lahir tidak valid. Gunakan format dd/mm/yyyy atau format tanggal Excel.");
            }

            $data = [
                'no' => $row['no'],
                'nama_lengkap' => $row['nama_lengkap'],
                'nik' => preg_replace('/[^0-9]/', '', trim($row['nik'])),
                'passport' => trim($row['passport']),
                'tempat_lahir' => $row['tempat_lahir'],
                'tanggal_lahir' => $formattedTanggalLahir,
                'jenis_kelamin' => $row['jenis_kelamin'],
                'jabatan' => $row['jabatan'],
                'domisili' => $row['domisili'],
                'justifikasi' => $row['justifikasi'] ?? null, // Justifikasi bisa null
            ];

            $umur = Carbon::parse($tglLahir)->age;
            $justifikasi = $row['justifikasi'] ?? null;

            Log::info("Baris $barisExcel: Tanggal Lahir={$formattedTanggalLahir}, Umur=$umur");

            Validator::make($data, [
                'no' => 'required|integer',
                'nama_lengkap' => 'required|string|max:255',
                'nik' => ['required', 'string', 'regex:/^\d{8,20}$/', 'unique:workers,nik'], // string of digits 8-20 chars
                'passport' => 'nullable|string|max:20|unique:workers,nik',
                'tempat_lahir' => 'required|string|max:150',
                'tanggal_lahir' => 'required|date|before:today',
                'jenis_kelamin' => 'required|in:laki-laki,perempuan',
                'jabatan' => 'required|string|max:100',
                'domisili' => 'required|string|max:255',
                'justifikasi' => 'nullable|string|max:255', // Justifikasi optional
            ], [
                'nik.required' => "Error pada Baris $barisExcel: NIK harus diisi.",
                'nik.regex' => "Error pada Baris $barisExcel: NIK harus berupa angka 8-20 digit.",
                'nik.unique' => "Error pada Baris $barisExcel: NIK sudah terdaftar.",
                'passport.unique' => "Error pada Baris $barisExcel: Passport sudah terdaftar.",
                'tempat_lahir.required' => "Error pada Baris $barisExcel: Tempat lahir harus diisi.",
                'tanggal_lahir.required' => "Error pada Baris $barisExcel: Tanggal lahir harus diisi.",
                'tanggal_lahir.date' => "Error pada Baris $barisExcel: Tanggal lahir harus berupa tanggal yang valid.",
                'tanggal_lahir.before' => "Error pada Baris $barisExcel: Tanggal lahir tidak boleh hari ini.",
                'jenis_kelamin.required' => "Error pada Baris $barisExcel: Jenis kelamin harus diisi.",
                'jenis_kelamin.in' => "Error pada Baris $barisExcel: Jenis kelamin harus 'laki-laki' atau 'perempuan'.",
                'jabatan.required' => "Error pada Baris $barisExcel: Jabatan harus diisi.",
                'jabatan.string' => "Error pada Baris $barisExcel: Jabatan harus berupa teks.",
                'jabatan.max' => "Error pada Baris $barisExcel: Jabatan maksimal 100 karakter.",
                'domisili.required' => "Error pada Baris $barisExcel: Domisili harus diisi.",
                'domisili.string' => "Error pada Baris $barisExcel: Domisili harus berupa teks.",
                'domisili.max' => "Error pada Baris $barisExcel: Domisili maksimal 255 karakter.",
                'justifikasi.string' => "Error pada Baris $barisExcel: Justifikasi harus berupa teks.",
                'justifikasi.max' => "Error pada Baris $barisExcel: Justifikasi maksimal 255 karakter.",

            ])->after(function ($validator) use ($umur, $justifikasi, $barisExcel) {
                if ($umur < 18) {
                    $validator->errors()->add('tanggal_lahir', "Error pada Baris $barisExcel: Usia Pekerja minimal <b>18 tahun</b>.");
                }

                if ($umur > 56 && $justifikasi !== 'Ya') {
                    $validator->errors()->add('tanggal_lahir', "Error pada Baris $barisExcel: Usia Pekerja maksimal <b>56 tahun</b>. Jika usia melebihi 56 tahun, silakan isi kolom Justifikasi dengan 'Ya'.");
                }
            })->validate();

            $this->parsedData[] = $data;
        }
    }

    public function getData(): array
    {
        return $this->parsedData;
    }
}
