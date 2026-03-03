<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\User;
use App\Models\ClassRoom;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class StudentImport implements
    ToModel,
    WithHeadingRow,
    WithChunkReading,
    WithBatchInserts,
    ShouldQueue
{
    // Pelacak agar tidak duplikat dalam satu file excel
    private $processedNis = [];
    private $processedEmails = [];

    public function model(array $row)
    {
        $nis = trim($row['nis'] ?? '');
        $nama = trim($row['nama_lengkap'] ?? '');
        $emailExcel = trim($row['email'] ?? '');

        // 1. Validasi Kolom Wajib
        if (empty($nis) || empty($nama)) {
            return null;
        }

        // 2. Cek Duplikasi NIS (Database & Local)
        if (in_array($nis, $this->processedNis) || Student::where('nis', $nis)->exists()) {
            Log::warning("Siswa dengan NIS {$nis} dilewati karena duplikat.");
            return null;
        }

        // 3. Penanganan Email (Mencegah Error 1062)
        // Jika email kosong atau sudah ada di DB atau sudah diproses di baris sebelumnya
        if (empty($emailExcel) || User::where('email', $emailExcel)->exists() || in_array($emailExcel, $this->processedEmails)) {
            $emailFinal = $this->generateUniqueEmail($nama);
        } else {
            $emailFinal = $emailExcel;
        }

        // Simpan ke daftar pelacak local
        $this->processedNis[] = $nis;
        $this->processedEmails[] = $emailFinal;

        /* --- Normalisasi Data Tambahan --- */
        $kelasExcel = strtoupper(trim($row['kelas'] ?? 'TANPA KELAS'));
        $classRoom = ClassRoom::firstOrCreate(['name_class' => $kelasExcel]);

        $inputSex = strtoupper(trim($row['jenis_kelamin'] ?? 'L'));
        $sex = str_starts_with($inputSex, 'P') ? 'P' : 'L';

        /* --- Eksekusi Database --- */
        try {
            // Gunakan updateOrCreate untuk User agar lebih aman
            $user = User::updateOrCreate(
                ['username' => $nis], // Cari berdasarkan username (NIS)
                [
                    'name'              => $nama,
                    'email'             => $emailFinal,
                    'password'          => Hash::make($row['kata_sandi'] ?? $nis),
                    'role'              => 'siswa',
                    'email_verified_at' => now(),
                ]
            );

            return new Student([
                'nis'           => $nis,
                'user_id'       => $user->id,
                'class_room_id' => $classRoom->id,
                'full_name'     => $nama,
                'sex'           => $sex,
                'birth_date'    => $this->transformDate($row['tanggal_lahir'] ?? null),
                'in_school'     => true,
            ]);
        } catch (\Exception $e) {
            Log::error("Gagal simpan siswa {$nama}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Helper untuk Generate Email Unik
     */
    private function generateUniqueEmail($name)
    {
        $base = strtolower(preg_replace('/[^a-z0-9]/', '', str_replace(' ', '', $name)));
        $email = $base . rand(100, 999) . "@siswa.com";

        // Pastikan benar-benar unik di DB
        while (User::where('email', $email)->exists() || in_array($email, $this->processedEmails)) {
            $email = $base . rand(1000, 9999) . "@siswa.com";
        }
        return $email;
    }

    /**
     * Helper untuk Format Tanggal
     */
    private function transformDate($value)
    {
        if (empty($value)) return null;
        try {
            return is_numeric($value)
                ? ExcelDate::excelToDateTimeObject($value)->format('Y-m-d')
                : date('Y-m-d', strtotime($value));
        } catch (\Exception $e) {
            return null;
        }
    }

    public function chunkSize(): int
    {
        return 100;
    } // Mengecilkan chunk agar lebih stabil
    public function batchSize(): int
    {
        return 100;
    }
}
