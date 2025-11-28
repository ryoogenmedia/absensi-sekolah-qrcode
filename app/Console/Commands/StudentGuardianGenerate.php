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

            $guardianName = $student->father_name ?? $student->mother_name ?? 'Orang Tua';
            $relationship = $student->father_name ? 'Ayah' : ($student->mother_name ? 'Ibu' : 'Wali');

            $username = "wali_{$student->id}";
            $email = "wali_{$student->id}@gmail.com";

            $user = User::where('username', $username)->first();

            if (!$user) {
                $user = User::create([
                    'username' => $username,
                    'email' => $email,
                    'role' => 'guardian',
                    'password' => Hash::make('password'),
                    'force_logout' => false,
                ]);

                $this->info("Created user for guardian: {$username}");
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

        $this->info("DONE! Total guardian generated: {$count}");
    }
}
