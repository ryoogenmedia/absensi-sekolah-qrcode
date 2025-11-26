<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Student;
use App\Models\StudentGuardian;
use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class StudentGuardianImport implements
    ToModel,
    WithHeadingRow,
    ShouldQueue,
    WithChunkReading
{
    public function model(array $row)
    {
        if (!isset($row['nama_wali']) || trim($row['nama_wali']) === '') {
            return null;
        }

        $studentId = null;

        if (!empty($row['nis'])) {
            $studentId = Student::where('nis', trim($row['nis']))->value('id');
        }

        if (!$studentId && !empty($row['nama_siswa'])) {
            $studentId = Student::where('full_name', trim($row['nama_siswa']))->value('id');
        }

        if (!$studentId) {
            return null;
        }

        if (empty($row['email']) || empty($row['password'])) {
            return null;
        }

        $username = strtolower(str_replace(' ', '_', trim($row['nama_wali'])));

        $user = User::firstOrCreate(
            [
                'email' => trim($row['email'])
            ],
            [
                'username' => $username,
                'password' => Hash::make($row['password']),
                'role'     => 'guardian',
            ]
        );

        return StudentGuardian::updateOrCreate(
            [
                'student_id' => $studentId,
            ],
            [
                'user_id'               => $user->id,
                'guardian_name'         => $row['nama_wali'],
                'guardian_relationship' => $row['hubungan_wali'] ?? null,
                'guardian_contact'      => $row['kontak_wali'] ?? null,
            ]
        );
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
