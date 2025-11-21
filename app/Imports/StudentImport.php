<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\User;
use App\Models\ClassRoom;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class StudentImport implements ToModel, WithHeadingRow, WithChunkReading
{
    public function model(array $row)
    {
        // -----------------------------------
        // ABAIKAN BARIS KOSONG
        // -----------------------------------
        if (!isset($row['nama_lengkap']) || empty(trim($row['nama_lengkap']))) {
            return null;
        }

        // -----------------------------------
        // KONVERSI TANGGAL LAHIR
        // -----------------------------------
        $tanggalLahir = null;

        if (!empty($row['tanggal_lahir'])) {
            $tanggalLahir = is_numeric($row['tanggal_lahir'])
                ? ExcelDate::excelToDateTimeObject($row['tanggal_lahir'])->format('Y-m-d')
                : date('Y-m-d', strtotime($row['tanggal_lahir']));
        }

        // -----------------------------------
        // CARI CLASS ROOM
        // -----------------------------------
        $classRoom = null;

        if (!empty($row['kelas'])) {
            $classRoom = ClassRoom::where('name_class', trim($row['kelas']))->first();
        }

        // -----------------------------------
        // BUAT / UPDATE USER SISWA
        // -----------------------------------
        $user = User::updateOrCreate(
            [
                'email' => $row['email'] ?? null, // kolom unik
            ],
            [
                'username'          => $row['nama_lengkap'] ?? 'Siswa Baru',
                'password'          => Hash::make($row['kata_sandi'] ?? $row['nis']),
                'email_verified_at' => now(),
                'role'              => 'siswa',
            ]
        );

        // -----------------------------------
        // SIMPAN / UPDATE SISWA
        // -----------------------------------
        return Student::updateOrCreate(
            [
                'nis' => $row['nis'] ?? null, // kolom unik
            ],
            [
                'user_id'        => $user->id,
                'class_room_id'  => $classRoom->id ?? null,
                'in_school'      => true,

                'full_name'      => $row['nama_lengkap'] ?? null,
                'call_name'      => $row['nama_panggilan'] ?? null,
                'sex'            => $row['jenis_kelamin'] ?? null,

                'phone'          => $row['nomor_ponsel'] ?? null,
                'religion'       => $row['agama'] ?? null,

                'origin_school'  => $row['asal_sekolah'] ?? null,

                'birth_date'     => $tanggalLahir,
                'place_of_birth' => $row['tempat_lahir'] ?? null,

                'address'        => $row['alamat'] ?? null,
                'postal_code'    => $row['kode_pos'] ?? null,
                'admission_year' => $row['tahun_masuk'] ?? null,

                'father_name'    => $row['nama_ayah'] ?? null,
                'mother_name'    => $row['nama_ibu'] ?? null,
                'father_job'     => $row['pekerjaan_ayah'] ?? null,
                'mother_job'     => $row['pekerjaan_ibu'] ?? null,

                'photo'          => null, // Upload foto lain
            ]
        );
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
