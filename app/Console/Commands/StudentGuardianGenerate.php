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

        $students = Student::all();
        $count = 0;

        foreach ($students as $student) {

            if ($student->guardian && $student->guardian->exists) {
                $this->line("SKIP guardian: Exists for student ID {$student->id}");
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

            $user = User::where('username', $username)->first();

            if (!$user) {
                $user = User::create([
                    'username' => $username,
                    'email' => $email,
                    'role' => 'guardian',
                    'password' => Hash::make('password'),
                    'force_logout' => false,
                ]);

                $this->info("Created user: {$username} ({$email})");
            } else {
                $this->line("SKIP user: Already exists ({$username})");
            }

            StudentGuardian::create([
                'student_id' => $student->id,
                'user_id' => $user->id,
                'guardian_name' => $guardianName,
                'guardian_relationship' => $relationship,
                'guardian_contact' => $student->phone ?? '-',
            ]);

            $this->info("Generated guardian for student ID {$student->id}");

            $count++;
        }

        $this->info("DONE! Total guardians generated: {$count}");
    }
}
