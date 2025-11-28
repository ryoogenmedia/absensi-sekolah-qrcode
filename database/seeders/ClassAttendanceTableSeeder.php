<?php

namespace Database\Seeders;

use App\Models\ClassAttendance;
use App\Models\ClassSchedule;
use App\Models\Student;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClassAttendanceTableSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Factory::create('id_ID');

        $schedules = ClassSchedule::get(['id', 'class_room_id']);
        $studentsByClassRoom = Student::get(['id', 'class_room_id'])
            ->groupBy('class_room_id');

        $attendanceStatus = config('const.attendance_status');

        $classAttendancesToInsert = [];
        $studentAttendancesToInsert = [];

        foreach ($schedules as $schedule) {

            $students = $studentsByClassRoom->get($schedule->class_room_id);

            if (!$students || $students->isEmpty()) {
                continue;
            }

            for ($i = 0; $i < 20; $i++) {

                $createdAt = $faker->dateTimeBetween('-1 month', 'now');
                $updatedAt = $faker->dateTimeBetween('-1 month', 'now');

                $classAttendancesToInsert[] = [
                    'class_room_id'        => $schedule->class_room_id,
                    'class_schedule_id'    => $schedule->id,
                    'explanation_material' => $faker->sentence(),
                    'name_material'        => $faker->words(3, true),
                    'created_at'           => $createdAt,
                    'updated_at'           => $updatedAt,
                ];
            }
        }

        DB::table('class_attendances')->insert($classAttendancesToInsert);

        $classAttendances = ClassAttendance::orderBy('id')->get();

        foreach ($classAttendances as $classAttendance) {

            $students = $studentsByClassRoom->get($classAttendance->class_room_id);

            if (!$students || $students->isEmpty()) continue;

            foreach ($students as $student) {
                $studentAttendancesToInsert[] = [
                    'class_attendance_id' => $classAttendance->id,
                    'student_id'          => $student->id,
                    'status_attendance'   => $faker->randomElement($attendanceStatus),
                ];
            }
        }

        DB::table('student_attendances')->insert($studentAttendancesToInsert);
    }
}
