<?php

namespace Database\Seeders;

use App\Models\ClassAttendance;
use App\Models\Student;
use App\Models\StudentAttendance;
use Faker\Factory;
use Illuminate\Database\Seeder;

class StudentAttendanceTableSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Factory::create('id_ID');
        $statusList = config('const.attendance_status');

        $classAttendances = ClassAttendance::all(['id', 'class_room_id']);

        $existing = StudentAttendance::select('class_attendance_id', 'student_id')
            ->get()
            ->mapWithKeys(fn($row) => [
                "{$row->class_attendance_id}:{$row->student_id}" => true
            ])
            ->toArray();

        foreach ($classAttendances as $ca) {

            $students = Student::where('class_room_id', $ca->class_room_id)->pluck('id');

            foreach ($students as $studentId) {

                $key = "{$ca->id}:{$studentId}";

                if (isset($existing[$key])) {
                    continue;
                }

                StudentAttendance::create([
                    'class_attendance_id' => $ca->id,
                    'student_id'          => $studentId,
                    'status_attendance'   => $faker->randomElement($statusList),
                ]);
            }
        }
    }
}
