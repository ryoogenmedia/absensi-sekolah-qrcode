<?php

namespace App\Console\Commands;

use App\Models\ClassRoom;
use App\Models\ClassSchedule;
use App\Models\SubjectStudy;
use App\Models\Teacher;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DataClassRoomScheduleExampleGenerate extends Command
{
    protected $signature = 'generate:data-class-room-schedule-example';
    protected $description = 'Generate data class room schedule example for all classes under session rules';

    public function handle()
    {
        if ($this->confirm('Hapus semua jadwal yang ada sebelum generate baru?', false)) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            ClassSchedule::truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            $this->info("Data jadwal lama telah dibersihkan.");
        }

        $classRooms = ClassRoom::all();
        $teachers = Teacher::whereNotNull('subject_study_id')->get();
        $days = config('const.name_days_secound'); // Senin - Sabtu

        if ($classRooms->isEmpty() || $teachers->isEmpty()) {
            $this->error("Pastikan data Kelas dan Guru (dengan Subject) sudah terisi!");
            return;
        }

        $this->info("Memulai generate jadwal untuk " . $classRooms->count() . " kelas...");
        $bar = $this->output->createProgressBar($classRooms->count());

        $classSessions = \App\Models\SchoolSession::getActiveSessions();
        $timeSlots = [];
        foreach ($classSessions as $key => $session) {
            $timeSlots[] = [$session['start'] . ':00', $session['end'] . ':00'];
        }

        try {
            DB::beginTransaction();

            foreach ($classRooms as $class) {
                foreach ($days as $day) {
                    foreach ($timeSlots as $slot) {
                        // Gather teachers who aren't busy at this slot
                        $availableTeachers = $teachers->filter(function ($t) use ($day, $slot) {
                            return !ClassSchedule::where('teacher_id', $t->id)
                                ->where('day_name', $day)
                                ->where('start_time', $slot[0])
                                ->exists();
                        });

                        if ($availableTeachers->isEmpty()) {
                            continue;
                        }

                        // Check if classroom is already occupied
                        $isClassOccupied = ClassSchedule::where('class_room_id', $class->id)
                            ->where('day_name', $day)
                            ->where('start_time', $slot[0])
                            ->exists();

                        if ($isClassOccupied) {
                            continue;
                        }

                        $teacher = $availableTeachers->random();
                        $subjectId = $teacher->subject_study_id;

                        ClassSchedule::create([
                            'class_room_id'    => $class->id,
                            'teacher_id'       => $teacher->id,
                            'subject_study_id' => $subjectId,
                            'day_name'         => $day,
                            'start_time'       => $slot[0],
                            'end_time'         => $slot[1],
                            'description'      => 'Jadwal otomatis hasil generate sistem.',
                        ]);
                    }
                }
                $bar->advance();
            }

            DB::commit();
            $bar->finish();
            $this->newLine();
            $this->info("Berhasil generate jadwal kelas!");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("\nGagal: " . $e->getMessage());
        }
    }
}
