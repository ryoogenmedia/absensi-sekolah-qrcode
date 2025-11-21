<?php

namespace App\Imports;

use App\Models\ClassRoom;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class ClassRoomImport implements ToModel, WithHeadingRow, ShouldQueue, WithChunkReading
{
    public function model(array $row)
    {
        if (!isset($row['nama_kelas'])) {
            return null;
        }

        return ClassRoom::updateOrCreate(
            [
                'name_class' => $row['nama_kelas'],
            ],
            [
                'description'   => $row['deskripsi'] ?? null,
                'status_active' => $row['status_aktif'] ?? 1,
            ]
        );
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
