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
    protected $description = 'Generate data class & student attendance for all classes (1 year 3 months)';

    public function handle()
    {
        // 1. Konfirmasi Hapus Data
        if ($this->confirm('Apakah Anda ingin menghapus semua data kehadiran lama untuk SEMUA kelas?', false)) {
            $this->warn("Membersihkan tabel kehadiran...");
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            StudentAttendance::truncate();
            ClassAttendance::truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            $this->info("Data lama telah dibersihkan.");
        }

        // 2. Rentang Waktu: 1 Tahun 3 Bulan + Bulan Ini
        $startDate = Carbon::now()->subMonths(15)->startOfMonth();
        $endDate = Carbon::now();
        $period = CarbonPeriod::create($startDate, $endDate);

        // 3. Ambil Semua Jadwal
        // Kita gunakan eager load 'class_room' jika diperlukan
        $schedules = ClassSchedule::all();

        if ($schedules->isEmpty()) {
            $this->error("Tidak ada jadwal (ClassSchedule) yang ditemukan di database!");
            return;
        }

        $this->info("Memulai generate data untuk " . $schedules->pluck('class_room_id')->unique()->count() . " kelas...");

        $bar = $this->output->createProgressBar(count($period));
        $bar->start();

        try {
            foreach ($period as $date) {
                // Lewati hari Minggu (atau sesuaikan dengan hari libur sekolah)
                if ($date->isSunday()) {
                    $bar->advance();
                    continue;
                }

                // Ambil semua jadwal untuk hari ini (misal: 'senin', 'selasa', dst)
                $dayName = strtolower($date->translatedFormat('l'));
                $todaySchedules = $schedules->where('day_name', $dayName);

                // Gunakan Database Transaction per hari agar tidak terlalu berat di memory
                DB::transaction(function () use ($todaySchedules, $date) {
                    foreach ($todaySchedules as $schedule) {

                        // a. Buat Induk Kehadiran Kelas
                        $classAttendance = ClassAttendance::create([
                            'class_room_id' => $schedule->class_room_id,
                            'class_schedule_id' => $schedule->id,
                            'name_material' => Str::limit("Materi " . fake()->sentence(3), 200),
                            'explanation_material' => Str::limit(fake()->paragraph(2), 200),
                            'picture_evidence' => null,
                            'created_at' => $date->copy()->setTimeFromTimeString($schedule->start_time),
                            'updated_at' => $date->copy()->setTimeFromTimeString($schedule->start_time),
                        ]);

                        // b. Ambil siswa berdasarkan kelas pada jadwal tersebut
                        // Kita tarik langsung ID siswanya saja untuk efisiensi
                        $studentIds = Student::where('class_room_id', $schedule->class_room_id)->pluck('id');

                        $attendanceData = [];
                        foreach ($studentIds as $studentId) {
                            $attendanceData[] = [
                                'class_attendance_id' => $classAttendance->id,
                                'student_id' => $studentId,
                                'status_attendance' => $this->getRandomStatus(),
                                'created_at' => $classAttendance->created_at,
                                'updated_at' => $classAttendance->updated_at,
                            ];
                        }

                        // c. Insert Massal (Bulk Insert) untuk mempercepat proses
                        if (!empty($attendanceData)) {
                            StudentAttendance::insert($attendanceData);
                        }
                    }
                });

                $bar->advance();
            }

            $bar->finish();
            $this->newLine();
            $this->info("Berhasil! Data kehadiran untuk semua kelas telah digenerate.");
        } catch (\Exception $e) {
            $this->newLine();
            $this->error("Terjadi Kesalahan: " . $e->getMessage());
        }
    }

    /**
     * Probabilitas status kehadiran berdasarkan config
     */
    private function getRandomStatus()
    {
        $rand = rand(1, 100);
        $cumulativeProbability = 0;
        $attendanceStatus = config('const.attendance_status');

        if (is_string($attendanceStatus)) {
            return $attendanceStatus;
        }

        foreach ($attendanceStatus as $status) {
            if (is_array($status)) {
                $cumulativeProbability += $status['probability'] ?? 0;
                if ($rand <= $cumulativeProbability) {
                    return $status['value'] ?? 'hadir';
                }
            }
        }

        return is_array($attendanceStatus) && isset($attendanceStatus[0]['value'])
            ? $attendanceStatus[0]['value']
            : 'hadir';
    }
}
