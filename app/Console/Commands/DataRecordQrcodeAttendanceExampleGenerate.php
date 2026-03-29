<?php

namespace App\Console\Commands;

use App\Models\CheckInRecord;
use App\Models\CheckOutRecord;
use App\Models\Student;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DataRecordQrcodeAttendanceExampleGenerate extends Command
{
    protected $signature = 'generate:data-record-qrcode-attendance-example';
    protected $description = 'Generate example data for QR code attendance (Check-in & Check-out) for 1 year 3 months';

    public function handle()
    {
        // 1. Opsi Hapus Data Lama
        if ($this->confirm('Hapus semua data Check-In dan Check-Out lama?', false)) {
            $this->warn("Membersihkan data record...");
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            CheckInRecord::truncate();
            CheckOutRecord::truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            $this->info("Data lama berhasil dibersihkan.");
        }

        // 2. Rentang Waktu (15 Bulan ke belakang sampai sekarang)
        $startDate = Carbon::now()->subMonths(15)->startOfMonth();
        $endDate = Carbon::now();
        $period = CarbonPeriod::create($startDate, $endDate);

        $students = Student::all();
        if ($students->isEmpty()) {
            $this->error("Data siswa tidak ditemukan!");
            return;
        }

        $this->info("Men-generate QR record untuk " . $students->count() . " siswa...");
        $bar = $this->output->createProgressBar(count($period));
        $bar->start();

        try {
            foreach ($period as $date) {
                // Lewati hari Minggu
                if ($date->isSunday()) {
                    $bar->advance();
                    continue;
                }

                $checkInBatch = [];
                $checkOutBatch = [];

                foreach ($students as $student) {
                    // Kita asumsikan 95% siswa rajin melakukan scan QR masuk/pulang
                    if (rand(1, 100) > 95) continue;

                    // Jam masuk acak antara 06:30 - 07:15
                    $checkInTime = $date->copy()->setTime(6, rand(30, 59), rand(0, 59));

                    // Jam pulang acak antara 14:00 - 15:30
                    $checkOutTime = $date->copy()->setTime(14, rand(0, 59), rand(0, 59));

                    $checkInBatch[] = [
                        'student_id'      => $student->id,
                        'check_in_time'   => $checkInTime->format('H:i:s'),
                        'attendance_date' => $date->format('Y-m-d'),
                        'remarks'         => 'Hadir (Scan QR)',
                        'created_at'      => $checkInTime,
                        'updated_at'      => $checkInTime,
                    ];

                    $checkOutBatch[] = [
                        'student_id'      => $student->id,
                        'check_out_time'  => $checkOutTime->format('H:i:s'),
                        'attendance_date' => $date->format('Y-m-d'),
                        'remarks'         => 'Pulang (Scan QR)',
                        'created_at'      => $checkOutTime,
                        'updated_at'      => $checkOutTime,
                    ];
                }

                // Simpan per hari untuk menghemat memory
                if (!empty($checkInBatch)) {
                    CheckInRecord::insert($checkInBatch);
                }
                if (!empty($checkOutBatch)) {
                    CheckOutRecord::insert($checkOutBatch);
                }

                $bar->advance();
            }

            $bar->finish();
            $this->newLine();
            $this->info("Berhasil! Data Check-In dan Check-Out telah digenerate.");
        } catch (\Exception $e) {
            $this->newLine();
            $this->error("Gagal: " . $e->getMessage());
        }
    }
}
