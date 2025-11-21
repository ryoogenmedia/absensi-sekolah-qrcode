<?php

namespace App\Exports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class StudentExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Student::with('class_room', 'user')->get();
    }

    public function headings(): array
    {
        return [
            'nama_lengkap',
            'nama_panggilan',
            'nis',
            'kelas',
            'jenis_kelamin',
            'email',
            'kata_sandi',
            'akun', // <-- tambahan
            'nomor_ponsel',
            'tempat_lahir',
            'tanggal_lahir',
            'agama',
            'asal_sekolah',
            'tahun_masuk',
            'alamat',
            'kode_pos',
            'nama_ayah',
            'pekerjaan_ayah',
            'nama_ibu',
            'pekerjaan_ibu',
        ];
    }

    public function map($student): array
    {
        return [
            $student->full_name ?? '',
            $student->call_name ?? '',
            $student->nis ?? '',
            $student->class_room->name_class ?? '-',
            $student->sex ?? '',
            $student->user->email ?? '',
            '', // password tidak di-export

            // -----------------------------
            // Informasi Akun
            // -----------------------------
            $student->user ? 'ada' : 'tidak ada',

            $student->phone ?? '',
            $student->place_of_birth ?? '',
            $student->birth_date ?? '',
            $student->religion ?? '',
            $student->origin_school ?? '',
            $student->admission_year ?? '',
            $student->address ?? '',
            $student->postal_code ?? '',
            $student->father_name ?? '',
            $student->father_job ?? '',
            $student->mother_name ?? '',
            $student->mother_job ?? '',
        ];
    }
}
