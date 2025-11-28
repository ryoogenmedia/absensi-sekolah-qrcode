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
        $count = 0;

        // Preload data (sangat penting untuk kecepatan)
        $existingUsernames = User::pluck('id', 'username')->toArray();
        $existingGuardianStudentIds = StudentGuardian::pluck('student_id')->toArray();

        Student::chunk(500, function ($students) use (&$count, $existingUsernames, $existingGuardianStudentIds, $password) {

            foreach ($students as $student) {

                // Skip jika sudah memiliki guardian
                if (in_array($student->id, $existingGuardianStudentIds)) continue;

                // Tentukan nama wali
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

                // Username (pasti unik)
                $username = "wali_{$student->id}";

                // Jika user sudah ada, tidak membuat ulang
                if (isset($existingUsernames[$username])) {
                    $userId = $existingUsernames[$username];
                } else {

                    // EMAIL SANGAT PENDEK → w123@g.com
                    $email = $this->shortEmail($student->id);

                    $user = User::create([
                        'username' => $username,
                        'email' => $email,
                        'role' => 'guardian',
                        'password' => $password,
                        'force_logout' => false,
                    ]);

                    $userId = $user->id;
                }

                // Insert Student Guardian
                StudentGuardian::create([
                    'student_id' => $student->id,
                    'user_id' => $userId,
                    'guardian_name' => $guardianName,
                    'guardian_relationship' => $relationship,
                    'guardian_contact' => $student->phone ?? '-',
                ]);

                $count++;
            }
        });

        $this->info("SELESAI! Total guardian baru: {$count}");
    }

    /**
     * Email pendek dan unik, format: w{ID}@g.com
     * Contoh: w40@g.com
     */
    private function shortEmail($studentId)
    {
        return "w{$studentId}@g.com";
    }
}
