<?php

namespace App\Imports;

use App\Models\Teacher;
use App\Models\User;
use App\Models\SubjectStudy;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class TeacherImport implements ToModel, WithHeadingRow, WithChunkReading, ShouldQueue
{
    /**
     * Mapping baris Excel ke model Teacher
     */
    public function model(array $row)
    {
        // Abaikan baris kosong / header rusak
        if (!isset($row['nama']) || empty(trim($row['nama']))) {
            return null;
        }

        // ------------------------------
        // KONVERSI TANGGAL LAHIR
        // ------------------------------
        $tanggalLahir = null;

        if (!empty($row['tanggal_lahir'])) {
            $tanggalLahir = is_numeric($row['tanggal_lahir'])
                ? ExcelDate::excelToDateTimeObject($row['tanggal_lahir'])->format('Y-m-d')
                : date('Y-m-d', strtotime($row['tanggal_lahir']));
        }

        // ------------------------------
        // KONVERSI TANGGAL MASUK
        // ------------------------------
        $tanggalMasuk = null;

        if (!empty($row['tanggal_masuk'])) {
            $tanggalMasuk = is_numeric($row['tanggal_masuk'])
                ? ExcelDate::excelToDateTimeObject($row['tanggal_masuk'])->format('Y-m-d')
                : date('Y-m-d', strtotime($row['tanggal_masuk']));
        }

        // ------------------------------
        // CARI MAPEL
        // ------------------------------
        $subjectStudy = null;

        if (!empty($row['mapel'])) {
            $subjectStudy = SubjectStudy::where('name_subject', trim($row['mapel']))->first();
        }

        // ------------------------------
        // BUAT / UPDATE USER GURU
        // ------------------------------
        $user = User::updateOrCreate(
            [
                'email' => $row['email'] ?? null,
            ],
            [
                'username'          => $row['nama'] ?? 'Guru Baru',
                'password'          => Hash::make($row['kata_sandi'] ?? $row['nip']),
                'role'              => 'guru',
                'email_verified_at' => now(),
            ]
        );

        // ------------------------------
        // SIMPAN / UPDATE DATA GURU
        // ------------------------------
        return Teacher::updateOrCreate(
            [
                'nip' => $row['nip'] ?? null,
            ],
            [
                'user_id'          => $user->id,
                'subject_study_id' => $subjectStudy->id ?? null,

                'name'             => $row['nama'] ?? null,
                'sex'              => $row['jenis_kelamin'] ?? null,
                'nuptk'            => $row['nuptk'] ?? null,
                'phone'            => $row['nomor_ponsel'] ?? null,
                'religion'         => $row['agama'] ?? null,

                'birth_date'       => $tanggalLahir,
                'place_of_birth'   => $row['tempat_lahir'] ?? null,

                'address'          => $row['alamat'] ?? null,
                'postal_code'      => $row['kode_pos'] ?? null,

                'date_joined'      => $tanggalMasuk,
                'photo'            => null, // Foto upload terpisah
            ]
        );
    }

    /**
     * Import Excel per chunk agar tidak berat
     */
    public function chunkSize(): int
    {
        return 1000;
    }
}
