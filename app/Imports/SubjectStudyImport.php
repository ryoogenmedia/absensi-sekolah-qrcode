<?php

namespace App\Imports;

use App\Models\SubjectStudy;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SubjectStudyImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        if (!isset($row['nama_mapel'])) {
            return null;
        }

        return SubjectStudy::updateOrCreate(
            [
                'name_subject' => $row['nama_mapel'],
            ],
            [
                'description'   => $row['deskripsi'] ?? null,
                'status_active' => $this->convertStatus($row['status_aktif'] ?? true),
            ]
        );
    }

    /**
     * Mengubah teks atau angka menjadi boolean status
     */
    private function convertStatus($value)
    {
        if (is_null($value)) return true;

        $value = strtolower(trim($value));

        return in_array($value, ['1', 'true', 'aktif', 'yes', 'ya'], true);
    }
}
