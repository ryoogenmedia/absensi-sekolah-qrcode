<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ClassRoomImport;
use App\Imports\StudentImport;
use App\Imports\SubjectStudyImport;
use App\Imports\TeacherImport;

class ExcelAutoImport extends Command
{
    protected $signature = 'excel:auto-import';
    protected $description = 'Excel Auto Import Data dengan penanganan bentrok dan generator jadwal otomatis';

    public function handle()
    {
        $this->info("=== MULAI PROSES AUTO IMPORT EXCEL ===");
        $this->line("");

        // 1. Force queue to be sync so all imports run instantly and synchronously
        config(['queue.default' => 'sync']);

        // 2. Clean up existing data to prevent collision
        $this->warn("Membersihkan data lama untuk menghindari bentrok...");
        
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \App\Models\ClassSchedule::truncate();
        \App\Models\Student::truncate();
        \App\Models\Teacher::truncate();
        \App\Models\ClassRoom::truncate();
        \App\Models\SubjectStudy::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->info("✔ Data lama berhasil dibersihkan.");
        $this->line("");

        // 3. Process each spreadsheet in correct order (Class & Subject -> Teachers -> Students)
        $imports = [
            [
                'label' => 'Import Kelas',
                'file' => public_path('template/data/data-kelas.xlsx'),
                'importer' => new ClassRoomImport(),
            ],
            [
                'label' => 'Import Mapel',
                'file' => public_path('template/data/data-mapel.xlsx'),
                'importer' => new SubjectStudyImport(),
            ],
            [
                'label' => 'Import Guru',
                'file' => public_path('template/data/data-guru.xlsx'),
                'importer' => new TeacherImport(),
            ],
            [
                'label' => 'Import Siswa Kelas VII',
                'file' => public_path('template/data/data-siswa-kelas-vii.xlsx'),
                'importer' => new StudentImport(),
            ],
            [
                'label' => 'Import Siswa Kelas VIII',
                'file' => public_path('template/data/data-siswa-kelas-viii.xlsx'),
                'importer' => new StudentImport(),
            ],
            [
                'label' => 'Import Siswa Kelas IX',
                'file' => public_path('template/data/data-siswa-kelas-ix.xlsx'),
                'importer' => new StudentImport(),
            ],
        ];

        foreach ($imports as $item) {
            $this->processImport($item['label'], $item['file'], $item['importer']);
        }

        $this->line("");
        $this->info("=== PROSES GENERATE JADWAL OTOMATIS ===");
        
        // 4. Call the schedule generator command which is 100% collision-free and subject aligned!
        $this->call('generate:class-schedule');

        $this->line("");
        $this->info("=== SELESAI IMPORT DAN GENERATE JADWAL ===");

        return Command::SUCCESS;
    }

    private function processImport($label, $filePath, $importer)
    {
        $this->warn("→ Menjalankan {$label}...");
        $this->line("   File: {$filePath}");

        if (!file_exists($filePath)) {
            $this->error("   ✖ File tidak ditemukan, skip...");
            return;
        }

        try {
            // Calling queueImport is forced to run synchronously due to sync queue driver!
            Excel::queueImport($importer, $filePath);
            $this->info("   ✔ {$label} selesai diproses dengan sukses!");
        } catch (\Exception $e) {
            $this->error("   ✖ Gagal memproses import: " . $e->getMessage());
        }

        $this->line("");
    }
}
