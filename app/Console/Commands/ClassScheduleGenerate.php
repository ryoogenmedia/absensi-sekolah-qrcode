<?php

namespace App\Console\Commands;

use App\Models\ClassRoom;
use App\Models\ClassSchedule;
use App\Models\SubjectStudy;
use App\Models\Teacher;
use Faker\Factory;
use Illuminate\Console\Command;

class ClassScheduleGenerate extends Command
{
    protected $signature   = 'generate:class-schedule';
    protected $description = 'Generate random class schedule dengan aturan khusus';

    public function handle()
    {
        $this->info("Menghapus jadwal lama...");
        ClassSchedule::truncate();

        $faker = Factory::create('id_ID');

        $classRooms       = ClassRoom::all();
        $teachers         = Teacher::all();
        $subjects         = SubjectStudy::pluck('id')->toArray();
        $days             = config('const.name_days_secound');

        $timeSlots = [
            ['08:00:00', '09:30:00'],
            ['09:45:00', '11:15:00'],
            ['12:30:00', '14:00:00'],
            ['14:15:00', '15:45:00'],
        ];

        $usedSlots = [];

        $teacherLimit = [];

        $teacherHasSchedule = [];

        $this->info("Mulai generate jadwal tiap kelas...");

        foreach ($classRooms as $room) {

            $this->info("\n=== Kelas {$room->class_name} ===");

            $scheduleCount = 0;
            $maxSchedules  = 10;

            while ($scheduleCount < $maxSchedules) {

                $day           = $faker->randomElement($days);
                [$start, $end] = $faker->randomElement($timeSlots);

                $eligibleTeachers = $teachers->filter(function ($t) use ($teacherLimit, $room) {
                    return ($teacherLimit[$room->id][$t->id] ?? 0) < 2;
                });

                if ($eligibleTeachers->isEmpty()) {
                    $this->warn("Guru habis untuk kelas {$room->class_name}");
                    break;
                }

                $teacher = $eligibleTeachers->random();
                $subject = $faker->randomElement($subjects);

                $slotKey = "{$room->id}_{$day}_{$start}_{$end}";

                if (isset($usedSlots[$slotKey])) {
                    continue;
                }

                $usedSlots[$slotKey] = true;

                ClassSchedule::create([
                    'class_room_id'    => $room->id,
                    'teacher_id'       => $teacher->id,
                    'subject_study_id' => $subject,
                    'day_name'         => $day,
                    'start_time'       => $start,
                    'end_time'         => $end,
                    'description'      => $faker->sentence(),
                ]);

                $teacherHasSchedule[$teacher->id] = true;

                $teacherLimit[$room->id][$teacher->id] =
                    ($teacherLimit[$room->id][$teacher->id] ?? 0) + 1;

                $scheduleCount++;

                $this->info("✔ {$room->class_name}: Guru {$teacher->name} → {$day} {$start} - {$end}");
            }
        }

        $this->info("\nMengecek guru tanpa jadwal...");

        $room = ClassRoom::inRandomOrder()->first();

        foreach ($teachers as $teacher) {
            if (!isset($teacherHasSchedule[$teacher->id])) {

                $this->warn("Guru {$teacher->name} belum punya jadwal → menambahkan...");

                $day           = $faker->randomElement($days);
                [$start, $end] = $faker->randomElement($timeSlots);
                $subject       = $faker->randomElement($subjects);

                ClassSchedule::create([
                    'class_room_id'    => $room->id,
                    'teacher_id'       => $teacher->id,
                    'subject_study_id' => $subject,
                    'day_name'         => $day,
                    'start_time'       => $start,
                    'end_time'         => $end,
                    'description'      => "Tambahan supaya guru punya jadwal",
                ]);

                $this->info("✔ Tambahan jadwal untuk guru {$teacher->name}");
            }
        }

        $this->info("\nSelesai generate class schedule!");
        return Command::SUCCESS;
    }
}
