<?php

namespace App\Console\Commands;

use App\Models\ClassAttendance;
use App\Models\ClassSchedule;
use App\Models\Student;
use App\Models\StudentAttendance;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DataAttendanceStudentExampleGenerate extends Command
{
    protected $signature = 'generate:data-attendance-student-example';
    protected $description = 'Generate data class & student attendance for 1 year 3 months back';

    public function handle()
    {
        $this->info("Memulai generate data kehadiran...");

        // 1. Tentukan Range Tanggal (1 tahun 3 bulan = 15 bulan)
        $startDate = Carbon::now()->subMonths(15)->startOfMonth();
        $endDate = Carbon::now();
        $period = CarbonPeriod::create($startDate, $endDate);

        // 2. Ambil Jadwal yang tersedia
        $schedules = ClassSchedule::all();
        if ($schedules->isEmpty()) {
            $this->error("Jadwal (ClassSchedule) kosong! Isi jadwal terlebih dahulu.");
            return;
        }

        $bar = $this->output->createProgressBar(count($period));
        $bar->start();

        try {
            DB::beginTransaction();

            foreach ($period as $date) {
                // Lewati hari Minggu
                if ($date->isSunday()) {
                    $bar->advance();
                    continue;
                }

                // Cari jadwal yang sesuai dengan nama hari ini (LOWERCASE sesuai config)
                $dayName = strtolower($date->translatedFormat('l'));
                $todaySchedules = $schedules->where('day_name', $dayName);

                foreach ($todaySchedules as $schedule) {
                    // a. Buat Induk Kehadiran Kelas
                    $classAttendance = ClassAttendance::create([
                        'class_room_id' => $schedule->class_room_id,
                        'class_schedule_id' => $schedule->id,
                        'name_material' => Str::limit("Materi " . fake()->sentence(3), 200),
                        'explanation_material' => Str::limit(fake()->paragraph(2), 200),
                        'picture_evidence' => null, // Default null
                        'created_at' => $date->setTimeFromTimeString($schedule->start_time),
                        'updated_at' => $date->setTimeFromTimeString($schedule->start_time),
                    ]);

                    // b. Ambil semua siswa di kelas tersebut
                    $students = Student::where('class_room_id', $schedule->class_room_id)->get();

                    foreach ($students as $student) {
                        StudentAttendance::create([
                            'class_attendance_id' => $classAttendance->id,
                            'student_id' => $student->id,
                            'status' => $this->getRandomStatus(),
                            'notes' => null,
                        ]);
                    }
                }
                $bar->advance();
            }

            DB::commit();
            $bar->finish();
            $this->newLine();
            $this->info("Berhasil generate data kehadiran 1 tahun 3 bulan.");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("\nKesalahan: " . $e->getMessage());
        }
    }

    /**
     * Probabilitas status kehadiran
     */
    private function getRandomStatus()
    {
        $rand = rand(1, 100);
        if ($rand <= 90) return 'present';    // 90% Hadir
        if ($rand <= 94) return 'sick';       // 4% Sakit
        if ($rand <= 97) return 'permission'; // 3% Izin
        return 'absent';                      // 3% Alfa
    }
}
