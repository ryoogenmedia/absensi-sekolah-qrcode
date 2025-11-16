<?php

namespace Database\Seeders;

use App\Models\ClassRoom;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StudentTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create('id_ID');
        $religions = config('const.religions');
        $classRoomIds = ClassRoom::pluck('id')->toArray();

        $students = [];
        $users = [];

        $dataStudent = [
            [
                'username'          => 'Nurhaliza Student',
                'email'             => 'nurhalizastudent@gmail.com',
                'email_verified_at' => now(),
                'password'          => bcrypt('student123'),
                'role'              => 'siswa',

                'class_room_id'     => $faker->randomElement($classRoomIds),
                'in_school'         => true,
                'in_school'         => true,
                'full_name'         => 'Nurhaliza Student',
                'call_name'         => 'Nurhaliza',
                'sex'               => 'laki-laki',
                'nis'               => $faker->unique()->numerify('19########'),
                'phone'             => $faker->phoneNumber,
                'religion'          => $faker->randomElement($religions),
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
            $examplePhoto = example_photo($student['sex'], $i);

            $user = DB::table('users')->insertGetId([
                'username'          => $student['username'],
                'email'             => $student['email'],
                'email_verified_at' => $student['email_verified_at'],
                'password'          => $student['password'],
                'role'              => $student['role'],
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);

            DB::table('students')->insert([
                'user_id'           => $user,
                'class_room_id'     => $student['class_room_id'],
                'in_school'         => $student['in_school'],
                'full_name'         => $student['full_name'],
                'call_name'         => $student['call_name'],
                'sex'               => $student['sex'],
                'nis'               => $student['nis'],
                'phone'             => $student['phone'],
                'religion'          => $student['religion'],
                'origin_school'     => $student['origin_school'],
                'birth_date'        => $student['birth_date'],
                'place_of_birth'    => $student['place_of_birth'],
                'address'           => $student['address'],
                'postal_code'       => $student['postal_code'],
                'admission_year'    => $student['admission_year'],
                'father_name'       => $student['father_name'],
                'mother_name'       => $student['mother_name'],
                'father_job'        => $student['father_job'],
                'mother_job'        => $student['mother_job'],
                'photo'             => $examplePhoto,
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);
        }

        $i = 1;
        foreach ($classRoomIds as $classRoomId) {
            for ($j = 0; $j < 10; $j++) {
                $sex = $faker->randomElement(config('const.sex'));
                $name = $faker->name($sex == 'laki-laki' ? 'male' : 'female');
                $callName = strtolower(explode(' ', trim($name))[0]);
                $examplePhoto = example_photo($sex, $i);

                $users[] = [
                    'username' => $callName . $i,
                    'email'    => $callName . $i . '@example.com',
                    'email_verified_at' => now(),
                    'password' => bcrypt('student123'),
                    'role'     => 'siswa',
                    'created_at' => now(),
                    'updated_at' => now(),
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
                    'photo'          => $examplePhoto,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ];

                $i++;
            }
        }

        DB::table('users')->insert($users);

        $userIds = DB::table('users')
            ->orderBy('id', 'desc')
            ->take(count($students))
            ->pluck('id')
            ->reverse()
            ->values();

        foreach ($students as $index => $student) {
            $students[$index]['user_id'] = $userIds[$index];
        }

        DB::table('students')->insert($students);
    }
}
