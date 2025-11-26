<?php

namespace App\Exports;

use App\Models\StudentGuardian;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StudentGuardianExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return StudentGuardian::with(['student', 'student.class_room'])
            ->get()
            ->map(function ($item) {
                return [
                    'nama_wali'         => $item->guardian_name,
                    'hubungan'          => $item->guardian_relationship,
                    'kontak_wali'       => $item->guardian_contact,

                    'nama_siswa'        => $item->student?->full_name,
                    'nis'               => $item->student?->nis,
                    'kelas'             => $item->student?->class_room?->name_class,
                    'kontak_siswa'      => $item->student?->phone,
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Nama Wali',
            'Hubungan Wali',
            'Kontak Wali',
            'Nama Siswa',
            'NIS',
            'Kelas',
            'Kontak Siswa',
        ];
    }
}
