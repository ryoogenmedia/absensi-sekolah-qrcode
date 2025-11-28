<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\User;
use App\Models\ClassRoom;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class StudentImport implements ToModel, WithHeadingRow, WithChunkReading, ShouldQueue
{
    private function generateUniqueEmail($name)
    {
        $base = strtolower(preg_replace('/[^a-z0-9]/', '', str_replace(' ', '', $name)));
        $domain = "siswa.com";

        $email = "{$base}@{$domain}";

        if (!User::where('email', $email)->exists()) {
            return $email;
        }

        $counter = 2;
        while (true) {
            $newEmail = "{$base}{$counter}@{$domain}";
            if (!User::where('email', $newEmail)->exists()) {
                return $newEmail;
            }
            $counter++;
        }
    }

    public function model(array $row)
    {
        if (!isset($row['nis']) || empty(trim($row['nis']))) {
            return null;
        }

        if (!isset($row['nama_lengkap']) || empty(trim($row['nama_lengkap']))) {
            return null;
        }

        $tanggalLahir = null;
        if (!empty($row['tanggal_lahir'])) {
            $tanggalLahir = is_numeric($row['tanggal_lahir'])
                ? ExcelDate::excelToDateTimeObject($row['tanggal_lahir'])->format('Y-m-d')
                : date('Y-m-d', strtotime($row['tanggal_lahir']));
        }

        $classRoomId = null;
        if (!empty($row['kelas'])) {
            $classRoom = ClassRoom::where('name_class', trim($row['kelas']))->first();
            $classRoomId = $classRoom->id ?? null;
        }

        if ($classRoomId === null) {
            Log::warning("❗ SKIP IMPORT SISWA — Kelas '{$row['kelas']}' tidak ditemukan. NIS: {$row['nis']}");
            return null;
        }

        $existingUser = User::where('username', $row['nis'])->first();

        if ($existingUser) {
            $emailToUse = $existingUser->email;
        } else {
            $emailFromExcel = $row['email'] ?? null;

            if (!$emailFromExcel || User::where('email', $emailFromExcel)->exists()) {
                $emailToUse = $this->generateUniqueEmail($row['nama_lengkap']);
            } else {
                $emailToUse = $emailFromExcel;
            }
        }

        $user = User::updateOrCreate(
            ['username' => $row['nis']],
            [
                'name'              => $row['nama_lengkap'],
                'email'             => $emailToUse,
                'password'          => Hash::make($row['kata_sandi'] ?? $row['nis']),
                'email_verified_at' => now(),
                'role'              => 'siswa',
            ]
        );

        return Student::updateOrCreate(
            ['nis' => $row['nis']],
            [
                'user_id'        => $user->id,
                'class_room_id'  => $classRoomId,
                'in_school'      => true,

                'full_name'      => $row['nama_lengkap'],
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

                'photo'          => null,
            ]
        );
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
