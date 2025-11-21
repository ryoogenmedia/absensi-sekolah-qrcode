<?php

namespace App\Exports;

use App\Models\Teacher;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TeacherExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Teacher::with('subject_study', 'user')->get();
    }

    public function headings(): array
    {
        return [
            'nama',
            'nip',
            'nuptk',
            'jenis_kelamin',
            'mapel',
            'email',
            'akun',
            'nomor_ponsel',
            'tempat_lahir',
            'tanggal_lahir',
            'agama',
            'alamat',
            'kode_pos',
            'tanggal_masuk',
        ];
    }

    public function map($teacher): array
    {
        return [
            $teacher->name,
            $teacher->nip,
            $teacher->nuptk,
            $teacher->sex,
            $teacher->subject_study->name ?? '-',

            $teacher->user->email ?? '',
            $teacher->user ? 'ada' : 'tidak ada',

            $teacher->phone,
            $teacher->place_of_birth,
            $teacher->birth_date,
            $teacher->religion,
            $teacher->address,
            $teacher->postal_code,
            $teacher->date_joined,
        ];
    }
}
