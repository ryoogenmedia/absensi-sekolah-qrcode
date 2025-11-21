<?php

namespace App\Exports;

use App\Models\SubjectStudy;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SubjectStudyExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return SubjectStudy::all();
    }

    public function headings(): array
    {
        return [
            'name_subject',
            'description',
            'status_active',
        ];
    }

    public function map($subject): array
    {
        return [
            $subject->name_subject,
            $subject->description,
            $subject->status_active ? 'aktif' : 'nonaktif',
        ];
    }
}
