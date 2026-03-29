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

class DataAttendanceStudentExampleGenerate extends Command
{
    protected $signature = 'generate:data-attendance-student-example';
    protected $description = 'Generate data class & student attendance dengan status variatif';

    public function handle()
    {
        if ($this->confirm('Hapus semua data kehadiran lama?', false)) {
            $this->warn("Membersihkan data...");
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            StudentAttendance::truncate();
            ClassAttendance::truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        $startDate = Carbon::now()->subMonths(15)->startOfMonth();
        $endDate = Carbon::now();
        $period = CarbonPeriod::create($startDate, $endDate);
        $schedules = ClassSchedule::all();

        $this->info("Generating data...");
        $bar = $this->output->createProgressBar(count($period));
        $bar->start();

        foreach ($period as $date) {
            if ($date->isSunday()) {
                $bar->advance();
                continue;
            }

            $dayName = strtolower($date->translatedFormat('l'));
            $todaySchedules = $schedules->where('day_name', $dayName);

            DB::transaction(function () use ($todaySchedules, $date) {
                foreach ($todaySchedules as $schedule) {
                    $classAttendance = ClassAttendance::create([
                        'class_room_id' => $schedule->class_room_id,
                        'class_schedule_id' => $schedule->id,
                        'name_material' => "Materi " . fake()->sentence(2),
                        'explanation_material' => fake()->paragraph(1),
                        'created_at' => $date->copy()->setTimeFromTimeString($schedule->start_time),
                        'updated_at' => $date->copy()->setTimeFromTimeString($schedule->start_time),
                    ]);

                    $studentIds = Student::where('class_room_id', $schedule->class_room_id)->pluck('id');

                    $attendanceData = [];
                    foreach ($studentIds as $id) {
                        $attendanceData[] = [
                            'class_attendance_id' => $classAttendance->id,
                            'student_id' => $id,
                            'status_attendance' => $this->getRandomStatus(),
                            'created_at' => $classAttendance->created_at,
                            'updated_at' => $classAttendance->updated_at,
                        ];
                    }

                    if (!empty($attendanceData)) {
                        StudentAttendance::insert($attendanceData);
                    }
                }
            });

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Selesai!");
    }

    private function getRandomStatus()
    {
        $statuses = config('const.attendance_status');
        $rand = rand(1, 100);

        // Logic Probabilitas: Hadir(85%), Sakit(5%), Izin(5%), Alpa(5%)
        if ($rand <= 85) return $statuses[0] ?? 'hadir';
        if ($rand <= 90) return $statuses[3] ?? 'sakit';
        if ($rand <= 95) return $statuses[2] ?? 'izin';
        return $statuses[1] ?? 'alpa';
    }
}
