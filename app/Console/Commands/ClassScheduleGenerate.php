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
    protected $description = 'Generate random class schedule dengan aturan sesi dan subject guru';

    public function handle()
    {
        $this->info("Menghapus jadwal lama...");
        ClassSchedule::truncate();

        $faker = Factory::create('id_ID');

        $classRooms       = ClassRoom::all();
        $teachers         = Teacher::whereNotNull('subject_study_id')->get();
        $days             = config('const.name_days_secound'); // Senin - Sabtu

        $classSessions = \App\Models\SchoolSession::getActiveSessions();
        $timeSlots = [];
        foreach ($classSessions as $key => $session) {
            $timeSlots[] = [$session['start'] . ':00', $session['end'] . ':00'];
        }

        $usedClassSlots = [];
        $usedTeacherSlots = [];

        $teacherLimit = [];
        $teacherHasSchedule = [];

        $this->info("Mulai generate jadwal tiap kelas...");

        foreach ($classRooms as $room) {
            $className = $room->name_class ?? $room->class_name ?? 'Kelas';
            $this->info("\n=== Kelas {$className} ===");

            $scheduleCount = 0;
            $maxSchedules  = 5; // keep it balanced to prevent lockups

            // Let's gather all possible options for this classroom to randomly draw from
            $possibleSlots = [];
            foreach ($days as $day) {
                foreach ($timeSlots as [$start, $end]) {
                    $possibleSlots[] = [$day, $start, $end];
                }
            }
            $faker->shuffle($possibleSlots);

            foreach ($possibleSlots as [$day, $start, $end]) {
                if ($scheduleCount >= $maxSchedules) {
                    break;
                }

                $classKey = "{$room->id}_{$day}_{$start}_{$end}";
                if (isset($usedClassSlots[$classKey])) {
                    continue;
                }

                // Filter teachers who are not busy at this day & time and have not exceeded limits
                $eligibleTeachers = $teachers->filter(function ($t) use ($teacherLimit, $room, $day, $start, $end, $usedTeacherSlots) {
                    $teacherKey = "{$t->id}_{$day}_{$start}_{$end}";
                    $limitOk = ($teacherLimit[$room->id][$t->id] ?? 0) < 2;
                    return $limitOk && !isset($usedTeacherSlots[$teacherKey]);
                });

                if ($eligibleTeachers->isEmpty()) {
                    continue;
                }

                $teacher = $eligibleTeachers->random();
                $subject = $teacher->subject_study_id;

                $teacherKey = "{$teacher->id}_{$day}_{$start}_{$end}";

                $usedClassSlots[$classKey] = true;
                $usedTeacherSlots[$teacherKey] = true;

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

                $this->info("✔ {$className}: Guru {$teacher->name} → {$day} {$start} - {$end}");
            }
        }

        $this->info("\nMengecek guru tanpa jadwal...");

        foreach ($teachers as $teacher) {
            if (!isset($teacherHasSchedule[$teacher->id])) {
                $this->warn("Guru {$teacher->name} belum punya jadwal → menambahkan...");

                // Find a free slot for this teacher in any classroom
                $found = false;
                foreach ($classRooms as $room) {
                    $className = $room->name_class ?? $room->class_name ?? 'Kelas';
                    foreach ($days as $day) {
                        foreach ($timeSlots as [$start, $end]) {
                            $classKey = "{$room->id}_{$day}_{$start}_{$end}";
                            $teacherKey = "{$teacher->id}_{$day}_{$start}_{$end}";

                            if (!isset($usedClassSlots[$classKey]) && !isset($usedTeacherSlots[$teacherKey])) {
                                $usedClassSlots[$classKey] = true;
                                $usedTeacherSlots[$teacherKey] = true;

                                ClassSchedule::create([
                                    'class_room_id'    => $room->id,
                                    'teacher_id'       => $teacher->id,
                                    'subject_study_id' => $teacher->subject_study_id,
                                    'day_name'         => $day,
                                    'start_time'       => $start,
                                    'end_time'         => $end,
                                    'description'      => "Tambahan supaya guru punya jadwal",
                                ]);

                                $this->info("✔ Tambahan jadwal untuk guru {$teacher->name} di {$className}");
                                $found = true;
                                break 3;
                            }
                        }
                    }
                }

                if (!$found) {
                    $this->warn("× Gagal mencari jadwal kosong untuk guru {$teacher->name} (semua slot bentrok)");
                }
            }
        }

        $this->info("\nSelesai generate class schedule!");
        return Command::SUCCESS;
    }
}
