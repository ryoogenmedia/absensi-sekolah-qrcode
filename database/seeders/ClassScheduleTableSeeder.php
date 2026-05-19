<?php

namespace Database\Seeders;

use App\Models\ClassRoom;
use App\Models\ClassSchedule;
use App\Models\SubjectStudy;
use App\Models\Teacher;
use Faker\Factory;
use Illuminate\Database\Seeder;

class ClassScheduleTableSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Factory::create('id_ID');

        $classRoomIds     = ClassRoom::pluck('id')->toArray();
        $days             = config('const.name_days_secound'); // Senin - Sabtu
        $teachers         = Teacher::whereNotNull('subject_study_id')->get();

        $classSessions = \App\Models\SchoolSession::getActiveSessions();
        $timeSlots = [];
        foreach ($classSessions as $key => $session) {
            $timeSlots[] = [$session['start'] . ':00', $session['end'] . ':00'];
        }

        $usedClassSlots = [];
        $usedTeacherSlots = [];

        foreach ($teachers as $teacher) {
            $subjectId = $teacher->subject_study_id;

            $teachCount = $faker->numberBetween(1, 4);

            for ($i = 0; $i < $teachCount; $i++) {
                $availableSlots = [];

                foreach ($days as $day) {
                    foreach ($timeSlots as [$start, $end]) {
                        foreach ($classRoomIds as $roomId) {
                            $classKey   = "{$roomId}_{$day}_{$start}_{$end}";
                            $teacherKey = "{$teacher->id}_{$day}_{$start}_{$end}";

                            if (!isset($usedClassSlots[$classKey]) && !isset($usedTeacherSlots[$teacherKey])) {
                                $availableSlots[] = [
                                    'day'           => $day,
                                    'start'         => $start,
                                    'end'           => $end,
                                    'class_room_id' => $roomId,
                                    'class_key'     => $classKey,
                                    'teacher_key'   => $teacherKey,
                                ];
                            }
                        }
                    }
                }

                if (empty($availableSlots)) {
                    break;
                }

                $slot = $faker->randomElement($availableSlots);

                $usedClassSlots[$slot['class_key']]   = true;
                $usedTeacherSlots[$slot['teacher_key']] = true;

                ClassSchedule::create([
                    'class_room_id'    => $slot['class_room_id'],
                    'teacher_id'       => $teacher->id,
                    'subject_study_id' => $subjectId,
                    'day_name'         => $slot['day'],
                    'start_time'       => $slot['start'],
                    'end_time'         => $slot['end'],
                    'description'      => $faker->sentence(),
                ]);
            }
        }
    }
}
