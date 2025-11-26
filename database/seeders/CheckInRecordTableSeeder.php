<?php

namespace Database\Seeders;

use App\Models\Student;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CheckInRecordTableSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Factory::create('id_ID');
        $studentIds = Student::pluck('id')->toArray();

        $records = [];

        $makeRecord = function ($date) use ($faker, $studentIds) {
            return [
                'student_id'      => $faker->randomElement($studentIds),
                'check_in_time'   => $faker->dateTimeBetween(
                    $date->format('Y-m-d') . ' 06:00:00',
                    $date->format('Y-m-d') . ' 09:00:00'
                ),
                'attendance_date' => $date->format('Y-m-d'),
                'remarks'         => $faker->optional()->sentence,
                'created_at'      => now(),
                'updated_at'      => now(),
            ];
        };

        foreach (range(1, 100) as $_) {
            $date = $faker->dateTimeBetween('-5 years', '-10 months');
            $records[] = $makeRecord($date);
        }

        foreach (range(1, 50) as $_) {
            $date = $faker->dateTimeBetween('-10 months', '-10 days');
            $records[] = $makeRecord($date);
        }

        foreach (range(1, 30) as $_) {
            $date = $faker->dateTimeBetween('-10 days', 'yesterday');
            $records[] = $makeRecord($date);
        }

        DB::table('check_in_records')->insert($records);
    }
}
