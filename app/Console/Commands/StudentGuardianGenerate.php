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
        $this->info("Generating guardian users...");
        $count = 0;

        $existingUsernames = User::pluck('id', 'username')->toArray();
        $existingGuardianStudentIds = StudentGuardian::pluck('student_id')->toArray();

        Student::chunk(300, function ($students) use (&$count, $existingUsernames, $existingGuardianStudentIds) {

            foreach ($students as $student) {

                // Skip jika guardian sudah ada
                if (in_array($student->id, $existingGuardianStudentIds)) {
                    continue;
                }

                // Tentukan nama wali
                if (!empty($student->father_name)) {
                    $guardianName = $student->father_name;
                    $relationship = 'Ayah';
                } elseif (!empty($student->mother_name)) {
                    $guardianName = $student->mother_name;
                    $relationship = 'Ibu';
                } else {
                    $guardianName = "Wali " . $student->full_name;
                    $relationship = 'Wali';
                }

                // Username fix → selalu pakai ID siswa
                $username = "wali_{$student->id}";

                // Jika user sudah ada → skip buat ulang email
                if (isset($existingUsernames[$username])) {
                    $userId = $existingUsernames[$username];
                } else {

                    // === EMAIL UNIK ===
                    $email = $this->generateUniqueGuardianEmail($student);

                    $user = User::create([
                        'username' => $username,
                        'email' => $email,
                        'role' => 'guardian',
                        'password' => Hash::make('password'),
                        'force_logout' => false,
                    ]);

                    $userId = $user->id;
                }

                // Simpan StudentGuardian
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

        $this->info("DONE! Total guardians generated: {$count}");
    }


    /**
     * Generate unique guardian email
     */
    private function generateUniqueGuardianEmail($student)
    {
        $firstName = explode(" ", trim($student->full_name))[0];
        $cleanName = preg_replace('/[^a-zA-Z0-9]/', '', strtolower($firstName));

        // Timestamp + student ID = dijamin unik selamanya
        $uniquePart = time() . '_' . $student->id;

        return "wali_{$cleanName}_{$uniquePart}@gmail.com";
    }
}
