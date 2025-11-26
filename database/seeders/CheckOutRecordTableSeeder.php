<?php

namespace Database\Seeders;

use App\Models\Student;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CheckOutRecordTableSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Factory::create('id_ID');
        $now   = now();
        $studentIds = Student::pluck('id')->toArray();

        $records = [];

        $makeRecord = function ($date) use ($faker, $studentIds) {
            return [
                'student_id'      => $faker->randomElement($studentIds),
                'check_out_time'   => $faker->dateTimeBetween($date->format('Y-m-d') . ' 06:00:00', $date->format('Y-m-d') . ' 09:00:00'),
                'attendance_date' => $date->format('Y-m-d'),
                'remarks'         => $faker->optional()->sentence,
                'created_at'      => now(),
                'updated_at'      => now(),
            ];
        };

        foreach (range(1, 1) as $_) {
            $date = $faker->dateTimeBetween('-5 years', '-1 year');
            $records[] = $makeRecord($date);
        }

        foreach (range(1, 10) as $_) {
            $date = $faker->dateTimeBetween('-10 months', '-1 month');
            $records[] = $makeRecord($date);
        }

        foreach (range(1, 10) as $_) {
            $date = $faker->dateTimeBetween('-10 days', '-1 day');
            $records[] = $makeRecord($date);
        }

        foreach (range(1, 20) as $_) {
            $date = now();
            $records[] = $makeRecord($date);
        }

        DB::table('check_out_records')->insert($records);
    }
}
