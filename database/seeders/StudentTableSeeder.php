<?php

namespace Database\Seeders;

use App\Models\ClassRoom;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StudentTableSeeder extends Seeder
{
    public function run(): void
    {
        $faker         = \Faker\Factory::create('id_ID');
        $religions     = config('const.religions');
        $sexOptions    = config('const.sex');
        $classRoomIds  = ClassRoom::pluck('id')->toArray();
        $now           = now();

        $users    = [];
        $students = [];

        $dataStudent = [
            [
                'username'          => 'Nurhaliza Student',
                'email'             => 'nurhalizastudent@gmail.com',
                'password'          => bcrypt('student123'),
                'role'              => 'siswa',

                'full_name'         => 'Nurhaliza Student',
                'call_name'         => 'Nurhaliza',
                'sex'               => 'perempuan',
                'in_school'         => true,

                'origin_school'     => 'SMPN 1 Jakarta',
                'birth_date'        => $faker->date('Y-m-d', '-15 years'),
                'place_of_birth'    => 'Jakarta',
                'address'           => $faker->address,
                'postal_code'       => $faker->postcode,
                'admission_year'    => $faker->year('-3 years'),
                'father_name'       => 'Budi Santoso',
                'mother_name'       => 'Siti Aminah',
                'father_job'        => 'Karyawan Swasta',
                'mother_job'        => 'Ibu Rumah Tangga',
            ],
        ];

        foreach ($dataStudent as $i => $student) {
            $users[] = [
                'username'          => $student['username'],
                'email'             => $student['email'],
                'email_verified_at' => $now,
                'password'          => $student['password'],
                'role'              => $student['role'],
                'created_at'        => $now,
                'updated_at'        => $now,
            ];

            $students[] = [
                'class_room_id'  => $faker->randomElement($classRoomIds),
                'full_name'      => $student['full_name'],
                'call_name'      => $student['call_name'],
                'sex'            => $student['sex'],
                'nis'            => $faker->unique()->numerify('19########'),
                'phone'          => $faker->phoneNumber,
                'religion'       => $faker->randomElement($religions),
                'origin_school'  => $student['origin_school'],
                'birth_date'     => $student['birth_date'],
                'place_of_birth' => $student['place_of_birth'],
                'address'        => $student['address'],
                'postal_code'    => $student['postal_code'],
                'admission_year' => $student['admission_year'],
                'father_name'    => $student['father_name'],
                'mother_name'    => $student['mother_name'],
                'father_job'     => $student['father_job'],
                'mother_job'     => $student['mother_job'],
                'photo'          => example_photo($student['sex'], $i),
                'created_at'     => $now,
                'updated_at'     => $now,
            ];
        }

        $counter = 1;
        foreach ($classRoomIds as $classRoomId) {
            for ($j = 0; $j < 5; $j++) {
                $sex      = $faker->randomElement($sexOptions);
                $name     = $faker->name($sex === 'laki-laki' ? 'male' : 'female');
                $callName = strtolower(explode(' ', $name)[0]);

                $users[] = [
                    'username'          => $callName . $counter,
                    'email'             => $callName . $counter . '@example.com',
                    'email_verified_at' => $now,
                    'password'          => bcrypt('student123'),
                    'role'              => 'siswa',
                    'created_at'        => $now,
                    'updated_at'        => $now,
                ];

                $students[] = [
                    'class_room_id'  => $classRoomId,
                    'full_name'      => $name,
                    'call_name'      => $callName,
                    'sex'            => $sex,
                    'nis'            => $faker->unique()->numerify('19########'),
                    'phone'          => $faker->phoneNumber,
                    'religion'       => $faker->randomElement($religions),
                    'origin_school'  => $faker->company . ' School',
                    'birth_date'     => $faker->date('Y-m-d', '-15 years'),
                    'place_of_birth' => $faker->city,
                    'address'        => $faker->address,
                    'postal_code'    => $faker->postcode,
                    'admission_year' => $faker->year('-3 years'),
                    'father_name'    => $faker->name('male'),
                    'mother_name'    => $faker->name('female'),
                    'father_job'     => $faker->jobTitle,
                    'mother_job'     => $faker->jobTitle,
                    'photo'          => example_photo($sex, $counter),
                    'created_at'     => $now,
                    'updated_at'     => $now,
                ];

                $counter++;
            }
        }

        DB::table('users')->insert($users);

        $userIds = DB::table('users')
            ->orderBy('id', 'desc')
            ->take(count($students))
            ->pluck('id')
            ->reverse()
            ->values();

        foreach ($students as $i => $student) {
            $students[$i]['user_id'] = $userIds[$i];
        }

        DB::table('students')->insert($students);
    }
}
