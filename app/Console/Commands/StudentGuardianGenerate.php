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

                if (in_array($student->id, $existingGuardianStudentIds)) {
                    continue;
                }

                $hasFather = !empty($student->father_name);
                $hasMother = !empty($student->mother_name);

                if ($hasFather) {
                    $guardianName = $student->father_name;
                    $relationship = 'Ayah';
                } elseif ($hasMother) {
                    $guardianName = $student->mother_name;
                    $relationship = 'Ibu';
                } else {
                    $guardianName = "Wali " . $student->full_name;
                    $relationship = 'Wali';
                }

                $firstName = explode(" ", trim($student->full_name))[0];
                $emailName = preg_replace('/[^a-zA-Z0-9]/', '', strtolower($firstName));

                $email = "wali_{$emailName}@gmail.com";
                $username = "wali_{$student->id}";

                if (isset($existingUsernames[$username])) {
                    $userId = $existingUsernames[$username];
                } else {
                    $user = User::create([
                        'username' => $username,
                        'email' => $email,
                        'role' => 'guardian',
                        'password' => Hash::make('password'),
                        'force_logout' => false,
                    ]);

                    $userId = $user->id;
                }

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
}
