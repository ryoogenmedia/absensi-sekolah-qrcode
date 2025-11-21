<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ClassRoomImport;
use App\Imports\StudentImport;
use App\Imports\SubjectStudyImport;
use App\Imports\TeacherImport;
use Symfony\Component\Console\Helper\ProgressBar;

class ExcelAutoImport extends Command
{
    protected $signature = 'excel:auto-import';
    protected $description = 'Excel Auto Import Data Dengan Loading';

    public function handle()
    {
        $this->info("=== MULAI PROSES IMPORT EXCEL ===");
        $this->line("");

        // Semua file menggunakan public_path
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
        $this->info("=== SELESAI IMPORT SEMUA FILE ===");

        return Command::SUCCESS;
    }

    private function processImport($label, $filePath, $importer)
    {
        $this->warn("→ {$label}");
        $this->line("   File: {$filePath}");

        if (!file_exists($filePath)) {
            $this->error("   ✖ File tidak ditemukan, skip...");
            return;
        }

        // Loading progress bar
        $progressBar = new ProgressBar($this->output, 100);
        $progressBar->start();

        // Visual loading (simulasi)
        for ($i = 0; $i < 100; $i++) {
            usleep(15000);
            $progressBar->advance();
        }

        // Jalankan import
        try {
            Excel::import($importer, $filePath);
        } catch (\Exception $e) {
            $progressBar->finish();
            $this->error("\n   ✖ Gagal import: " . $e->getMessage());
            return;
        }

        $progressBar->finish();
        $this->line("\n   ✔ Berhasil import {$label}");
        $this->line("");
    }
}
