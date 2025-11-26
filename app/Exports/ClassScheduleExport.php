<?php

namespace App\Exports;

use App\Models\ClassSchedule;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ClassScheduleExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    public function collection()
    {
        return ClassSchedule::with(['class_room', 'teacher', 'subject_study'])
            ->get()
            ->map(function ($schedule) {

                return [
                    'kelas'        => $schedule->class_room->name_class,
                    'guru'         => $schedule->teacher->nip ?: $schedule->teacher->name,
                    'mapel'        => $schedule->subject_study->name_subject,
                    'hari'         => $schedule->day_name,
                    'jam_mulai'    => $schedule->start_time,
                    'jam_selesai'  => $schedule->end_time,
                    'deskripsi'    => $schedule->description,
                ];
            });
    }

    public function headings(): array
    {
        return [
            'kelas',
            'guru',
            'mapel',
            'hari',
            'jam_mulai',
            'jam_selesai',
            'deskripsi',
        ];
    }
}
