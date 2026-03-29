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
    protected $description = 'Generate data class room schedule example for all classes';

    public function handle()
    {
        if ($this->confirm('Hapus semua jadwal yang ada sebelum generate baru?', false)) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            ClassSchedule::truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            $this->info("Data jadwal lama telah dibersihkan.");
        }

        $classRooms = ClassRoom::all();
        $teachers = Teacher::all();
        $subjects = SubjectStudy::all();
        $days = config('const.name_days'); // Ambil dari config Anda

        if ($classRooms->isEmpty() || $teachers->isEmpty() || $subjects->isEmpty()) {
            $this->error("Pastikan data Kelas, Guru, dan Mata Pelajaran sudah terisi!");
            return;
        }

        $this->info("Memulai generate jadwal untuk " . $classRooms->count() . " kelas...");
        $bar = $this->output->createProgressBar($classRooms->count());

        try {
            DB::beginTransaction();

            foreach ($classRooms as $class) {
                foreach ($days as $day) {
                    // Skip hari sabtu/minggu jika tidak ada di config
                    if (in_array(strtolower($day), ['sabtu', 'minggu', 'saturday', 'sunday'])) continue;

                    // Tentukan slot waktu (Contoh: 3 sesi per hari)
                    $timeSlots = [
                        ['07:30', '09:00'],
                        ['09:15', '10:45'], // Jeda istirahat 15 menit
                        ['11:00', '12:30'],
                    ];

                    foreach ($timeSlots as $slot) {
                        // Ambil Guru & Mapel Acak
                        $teacher = $teachers->random();
                        $subject = $subjects->random();

                        // Cek apakah guru tersebut sudah mengajar di kelas lain pada jam yang sama
                        $isTeacherBusy = ClassSchedule::where('teacher_id', $teacher->id)
                            ->where('day_name', $day)
                            ->where('start_time', $slot[0])
                            ->exists();

                        if (!$isTeacherBusy) {
                            ClassSchedule::create([
                                'class_room_id'    => $class->id,
                                'teacher_id'       => $teacher->id,
                                'subject_study_id' => $subject->id,
                                'day_name'         => $day,
                                'start_time'       => $slot[0],
                                'end_time'         => $slot[1],
                                'description'      => 'Jadwal otomatis hasil generate sistem.',
                            ]);
                        }
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
