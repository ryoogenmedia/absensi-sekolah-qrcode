<?php

namespace App\Imports;

use App\Models\ClassSchedule;
use App\Models\ClassRoom;
use App\Models\Teacher;
use App\Models\SubjectStudy;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class ClassScheduleImport implements
    ToModel,
    WithHeadingRow,
    ShouldQueue,
    WithChunkReading
{
    public function model(array $row)
    {
        if (
            !isset($row['kelas']) ||
            !isset($row['guru']) ||
            !isset($row['mapel']) ||
            !isset($row['hari']) ||
            !isset($row['jam_mulai']) ||
            !isset($row['jam_selesai'])
        ) {
            return null;
        }

        $classRoomId = ClassRoom::where('name_class', $row['kelas'])->value('id');

        // --- Ambil NIP guru jika ada ---
        $teacherId = null;

        // Jika kolom nip_guru ada & tidak kosong
        if (!empty($row['nip_guru'])) {
            $teacherId = Teacher::where('nip', trim($row['nip_guru']))->value('id');
        }

        // Jika tidak ditemukan berdasarkan NIP → fallback ke nama
        if (!$teacherId) {
            $teacherId = Teacher::where('name', trim($row['guru']))->value('id');
        }

        $subjectStudyId = SubjectStudy::where('name_subject', $row['mapel'])->value('id');

        if (!$classRoomId || !$teacherId || !$subjectStudyId) {
            return null;
        }

        return ClassSchedule::updateOrCreate(
            [
                'class_room_id'     => $classRoomId,
                'teacher_id'        => $teacherId,
                'subject_study_id'  => $subjectStudyId,
                'day_name'          => $row['hari'],
            ],
            [
                'start_time'        => $row['jam_mulai'],
                'end_time'          => $row['jam_selesai'],
                'description'       => $row['deskripsi'] ?? null,
            ]
        );
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
