<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\StudentGuardian;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StudentGuardianTableSeeder extends Seeder
{
    public function run(): void
    {
        $faker      = \Faker\Factory::create('id_ID');
        $now        = now();
        $relationships = config('const.guardian_relationships');

        $students   = Student::select('id', 'call_name')->get();
        $studentIds = $students->pluck('id')->toArray();

        $studentNur = $students->firstWhere('call_name', 'Nurhaliza');

        $usersData      = [];
        $guardiansData  = [];

        $usersData[] = [
            'username'          => 'WaliNurhaliza',
            'email'             => 'nurhalizawali@gmail.com',
            'email_verified_at' => $now,
            'password'          => bcrypt('wali123'),
            'role'              => 'wali siswa',
            'created_at'        => $now,
            'updated_at'        => $now,
        ];

        $guardiansData[] = [
            'student_id'            => $studentNur->id,
            'guardian_name'         => 'Wali Nurhaliza',
            'guardian_relationship' => 'ayah',
            'guardian_contact'      => '081234567890',
            'created_at'            => $now,
            'updated_at'            => $now,
        ];

        foreach ($studentIds as $index => $studentId) {

            $guardianUsername = $faker->unique()->userName;
            $guardianEmail    = $faker->unique()->safeEmail();

            $usersData[] = [
                'username'          => $guardianUsername,
                'email'             => $guardianEmail,
                'email_verified_at' => $now,
                'password'          => bcrypt('wali123'),
                'role'              => 'wali siswa',
                'created_at'        => $now,
                'updated_at'        => $now,
            ];

            $guardiansData[] = [
                'student_id'            => $studentId,
                'guardian_name'         => $faker->name(),
                'guardian_relationship' => $faker->randomElement($relationships),
                'guardian_contact'      => $faker->phoneNumber(),
                'created_at'            => $now,
                'updated_at'            => $now,
            ];
        }

        DB::table('users')->insert($usersData);

        $insertedUserIds = DB::table('users')
            ->orderBy('id', 'desc')
            ->take(count($guardiansData))
            ->pluck('id')
            ->reverse()
            ->values();

        foreach ($guardiansData as $i => $guardian) {
            $guardiansData[$i]['user_id'] = $insertedUserIds[$i];
        }

        DB::table('student_guardians')->insert($guardiansData);
    }
}
