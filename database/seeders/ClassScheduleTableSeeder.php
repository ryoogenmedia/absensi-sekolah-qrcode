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
        $subjectStudyIds  = SubjectStudy::pluck('id')->toArray();
        $days             = config('const.name_days_secound');
        $teachers         = Teacher::all();

        $timeSlots = [
            ['08:00:00', '09:30:00'],
            ['09:45:00', '11:15:00'],
            ['12:30:00', '14:00:00'],
            ['14:15:00', '15:45:00'],
        ];

        $usedSlots = [];

        foreach ($teachers as $teacher) {

            $subjectId = $faker->randomElement($subjectStudyIds);
            $teacher->update(['subject_study_id' => $subjectId]);

            $teachCount = $faker->numberBetween(1, 20);

            for ($i = 0; $i < $teachCount; $i++) {

                $availableSlots = [];

                foreach ($days as $day) {
                    foreach ($timeSlots as [$start, $end]) {
                        foreach ($classRoomIds as $roomId) {

                            $key = "{$roomId}_{$day}_{$start}_{$end}";

                            if (!isset($usedSlots[$key])) {
                                $availableSlots[] = [
                                    'day'          => $day,
                                    'start'        => $start,
                                    'end'          => $end,
                                    'class_room_id' => $roomId,
                                    'key'          => $key
                                ];
                            }
                        }
                    }
                }

                if (empty($availableSlots)) {
                    break;
                }

                $slot = $faker->randomElement($availableSlots);

                $usedSlots[$slot['key']] = true;

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
