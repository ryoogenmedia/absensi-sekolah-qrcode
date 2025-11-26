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

        $schedules = ClassSchedule::get(['id', 'class_room_id', 'teacher_id']);

        $studentsByClassRoom = Student::get(['id', 'class_room_id'])
            ->groupBy('class_room_id');

        $attendanceStatus = config('const.attendance_status');

        $classAttendancesToInsert = [];
        $studentAttendancesToInsert = [];

        foreach ($schedules as $schedule) {

            $classRoomId = $schedule->class_room_id;

            $students = $studentsByClassRoom->get($classRoomId, collect());

            if ($students->isEmpty()) {
                continue;
            }

            for ($i = 0; $i < 5; $i++) {

                $createdAt = $faker->dateTimeBetween('-1 month', 'now');
                $updatedAt = $faker->dateTimeBetween('-1 month', 'now');

                $classAttendancesToInsert[] = [
                    'class_room_id'      => $classRoomId,
                    'class_schedule_id'  => $schedule->id,
                    'explanation_material' => $faker->sentence(),
                    'name_material'      => $faker->words(3, true),
                    'created_at'         => $createdAt,
                    'updated_at'         => $updatedAt,
                ];
            }
        }

        DB::table('class_attendances')->insert($classAttendancesToInsert);

        $allClassAttendances = ClassAttendance::orderBy('id', 'desc')
            ->take(count($classAttendancesToInsert))
            ->get();

        foreach ($allClassAttendances as $classAttendance) {

            $students = $studentsByClassRoom->get($classAttendance->class_room_id, collect());

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
