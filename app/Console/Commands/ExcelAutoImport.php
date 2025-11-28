<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ClassRoomImport;
use App\Imports\StudentImport;
use App\Imports\SubjectStudyImport;
use App\Imports\TeacherImport;

class ExcelAutoImport extends Command
{
    protected $signature = 'excel:auto-import';
    protected $description = 'Excel Auto Import Data Dengan Progress Bar';

    public function handle()
    {
        $this->info("=== MULAI PROSES IMPORT EXCEL ===\n");

        // Semua file menggunakan public_path
        $imports = [
            ['label' => 'Import Kelas', 'file' => public_path('template/data/data-kelas.xlsx'), 'importer' => new ClassRoomImport()],
            ['label' => 'Import Mapel', 'file' => public_path('template/data/data-mapel.xlsx'), 'importer' => new SubjectStudyImport()],
            ['label' => 'Import Guru', 'file' => public_path('template/data/data-guru.xlsx'), 'importer' => new TeacherImport()],
            ['label' => 'Import Siswa Kelas VII', 'file' => public_path('template/data/data-siswa-kelas-vii.xlsx'), 'importer' => new StudentImport()],
            ['label' => 'Import Siswa Kelas VIII', 'file' => public_path('template/data/data-siswa-kelas-viii.xlsx'), 'importer' => new StudentImport()],
            ['label' => 'Import Siswa Kelas IX', 'file' => public_path('template/data/data-siswa-kelas-ix.xlsx'), 'importer' => new StudentImport()],
        ];

        $totalFiles = count($imports);
        $currentFile = 0;

        // === Progress Bar Global ===
        $this->info("Proses Total File:");
        $this->output->progressStart($totalFiles);

        foreach ($imports as $item) {
            $currentFile++;
            $this->processImport($item['label'], $item['file'], $item['importer']);
            $this->output->progressAdvance(); // Update progress global
        }

        $this->output->progressFinish();
        $this->info("\n=== SELESAI IMPORT SEMUA FILE ===");

        return Command::SUCCESS;
    }

    private function processImport($label, $filePath, $importer)
    {
        $this->line("\n------------------------------");
        $this->warn("→ {$label}");
        $this->line("   File: {$filePath}");

        if (!file_exists($filePath)) {
            $this->error("   ✖ File tidak ditemukan, skip...");
            return;
        }

        // === Progress bar tiap file (0% → 100%) ===
        $this->line("   Proses:");
        $this->output->progressStart(100);

        try {
            // Import langsung (lebih cepat)
            Excel::import($importer, $filePath);

            // Simulasikan progress 100 step agar terlihat halus
            for ($i = 0; $i < 100; $i++) {
                usleep(5000); // 0.005 detik → smooth
                $this->output->progressAdvance();
            }

            $this->output->progressFinish();
            $this->info("   ✔ Import {$label} selesai");
        } catch (\Exception $e) {
            $this->output->progressFinish();
            $this->error("   ✖ Gagal import: " . $e->getMessage());
        }
    }
}
