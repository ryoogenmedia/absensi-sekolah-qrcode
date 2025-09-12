<?php

namespace Database\Seeders;

use App\Models\ClassAttendance;
use App\Models\ClassSchedule;
use App\Models\Student;
use App\Models\StudentAttendance;
use Faker\Factory;
use Illuminate\Database\Seeder;

class ClassAttendanceTableSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Factory::create('id_ID');

        // Ambil semua jadwal, termasuk data relasi guru dan kelas
        $schedules = ClassSchedule::with(['teacher:id', 'class_room:id'])
            ->get(['id', 'teacher_id', 'class_room_id']);

        // Kelompokkan berdasarkan guru
        $schedulesByTeacher = $schedules->groupBy('teacher_id');

        foreach ($schedulesByTeacher as $teacherId => $teacherSchedules) {
            foreach ($teacherSchedules as $schedule) {
                // Gunakan class_room_id dari schedule yang sedang diproses
                $classRoomId = $schedule->class_room_id;

                // Buat 20 record class_attendance untuk setiap jadwal
                for ($i = 0; $i < 20; $i++) {
                    $classAttendance = ClassAttendance::create([
                        'class_room_id' => $classRoomId,
                        'class_schedule_id' => $schedule->id,
                        'explanation_material' => $faker->sentence(),
                        'name_material' => $faker->words(3, true),
                        'created_at' => $faker->dateTimeBetween('-1 month', 'now'),
                        'updated_at' => $faker->dateTimeBetween('-1 month', 'now'),
                    ]);

                    // Ambil semua siswa dari kelas tersebut
                    $students = Student::where('class_room_id', $classRoomId)->get(['id']);

                    foreach ($students as $student) {
                        StudentAttendance::create([
                            'class_attendance_id' => $classAttendance->id,
                            'student_id' => $student->id,
                            'status_attendance' => $faker->randomElement(config('const.attendance_status')),
                        ]);
                    }
                }
            }
        }
    }
}
