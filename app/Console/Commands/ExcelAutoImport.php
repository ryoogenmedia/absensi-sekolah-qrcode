<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

use App\Imports\ClassRoomImport;
use App\Imports\StudentImport;
use App\Imports\SubjectStudyImport;
use App\Imports\TeacherImport;

class ExcelAutoImport extends Command
{
    protected $signature = 'excel:auto-import';
    protected $description = 'Excel Auto Import Data Dengan Loading %';

    public function handle()
    {
        $this->info("=== MULAI PROSES IMPORT EXCEL ===\n");

        $imports = [
            ['label' => 'Import Kelas', 'file' => 'template/data/data-kelas.xlsx', 'importer' => new ClassRoomImport()],
            ['label' => 'Import Mapel', 'file' => 'template/data/data-mapel.xlsx', 'importer' => new SubjectStudyImport()],
            ['label' => 'Import Guru', 'file' => 'template/data/data-guru.xlsx', 'importer' => new TeacherImport()],
            ['label' => 'Import Siswa VII', 'file' => 'template/data/data-siswa-kelas-vii.xlsx', 'importer' => new StudentImport()],
            ['label' => 'Import Siswa VIII', 'file' => 'template/data/data-siswa-kelas-viii.xlsx', 'importer' => new StudentImport()],
            ['label' => 'Import Siswa IX', 'file' => 'template/data/data-siswa-kelas-ix.xlsx', 'importer' => new StudentImport()],
        ];

        foreach ($imports as $item) {
            $this->processImport($item['label'], public_path($item['file']), $item['importer']);
        }

        $this->info("\n=== SELESAI IMPORT SEMUA FILE ===");
    }

    private function processImport($label, $filePath, $importer)
    {
        $this->warn("→ {$label}");
        $this->line("   File: {$filePath}");

        if (!file_exists($filePath)) {
            $this->error("   ✖ File tidak ditemukan, skip...\n");
            return;
        }

        // ===== Ambil jumlah baris untuk progress % =====
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rowCount = $sheet->getHighestDataRow() - 1; // minus header

        if ($rowCount <= 0) {
            $this->error("   ✖ Tidak ada data untuk diimport.\n");
            return;
        }

        $this->info("   Jumlah data: {$rowCount}");
        $this->output->progressStart($rowCount);

        try {
            // Import manual per baris → progress bisa dihitung
            $data = $sheet->toArray();

            foreach ($data as $index => $row) {
                if ($index === 0) continue; // skip header
                $importer->model($row);

                $this->output->progressAdvance();
            }

            $this->output->progressFinish();
            $this->info("   ✔ {$label} selesai diproses\n");
        } catch (\Exception $e) {
            $this->error("   ✖ Error: " . $e->getMessage() . "\n");
        }
    }
}
