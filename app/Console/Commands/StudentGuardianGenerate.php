<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Student;
use App\Models\StudentGuardian;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class StudentGuardianGenerate extends Command
{
    protected $signature = 'generate:student-guardian';
    protected $description = 'Generate guardian + user login for students without guardians';

    public function handle()
    {
        $this->info("Generating guardian & user login...");

        $password = Hash::make('wali12345');

        // ==== Hitung total student untuk progress bar ====
        $totalStudents = Student::count();
        $this->output->progressStart($totalStudents);

        // Preload data (SUPER penting untuk kecepatan)
        $existingUsernames = User::pluck('id', 'username')->toArray();
        $existingGuardianStudentIds = StudentGuardian::pluck('student_id')->toArray();

        $count = 0;

        Student::chunk(10, function ($students) use (&$count, $existingUsernames, $existingGuardianStudentIds, $password) {

            foreach ($students as $student) {

                // Skip jika sudah ada guardian
                if (!in_array($student->id, $existingGuardianStudentIds)) {

                    // Tentukan wali
                    if ($student->father_name) {
                        $guardianName = $student->father_name;
                        $relationship = 'Ayah';
                    } elseif ($student->mother_name) {
                        $guardianName = $student->mother_name;
                        $relationship = 'Ibu';
                    } else {
                        $guardianName = "Wali " . $student->full_name;
                        $relationship = 'Wali';
                    }

                    // Username unik
                    $username = "wali_{$student->id}";

                    // Cek user sudah ada atau belum
                    if (isset($existingUsernames[$username])) {
                        $userId = $existingUsernames[$username];
                    } else {
                        $email = $this->shortEmail($student->id);

                        $user = User::create([
                            'username' => $username,
                            'email' => $email,
                            'role' => 'wali siswa',
                            'password' => $password,
                            'force_logout' => false,
                        ]);

                        $userId = $user->id;
                    }

                    // Buat data guardian
                    StudentGuardian::create([
                        'student_id' => $student->id,
                        'user_id' => $userId,
                        'guardian_name' => $guardianName,
                        'guardian_relationship' => $relationship,
                        'guardian_contact' => $student->phone ?? '-',
                    ]);

                    $count++;
                }

                // === Update progress bar ===
                $this->output->progressAdvance();
            }
        });

        // Tutup progress bar
        $this->output->progressFinish();

        $this->info("\nSELESAI! Total guardian baru: {$count}");
    }

    /**
     * Email pendek dan unik → w{ID}@g.com
     * Contoh: w40@g.com
     */
    private function shortEmail($studentId)
    {
        return "w{$studentId}@g.com";
    }
}
