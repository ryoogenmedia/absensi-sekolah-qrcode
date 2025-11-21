<?php

namespace App\Exports;

use App\Models\ClassRoom;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ClassRoomExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return ClassRoom::all();
    }

    public function headings(): array
    {
        return [
            'nama_kelas',
            'deskripsi',
            'status_aktif',
            'jumlah_siswa',
        ];
    }

    public function map($classRoom): array
    {
        return [
            $classRoom->name_class,
            $classRoom->description,
            $classRoom->status_active ? 'Aktif' : 'Tidak Aktif',
            $classRoom->students()->count(), // bonus: jumlah siswa
        ];
    }
}
