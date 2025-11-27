<?php

namespace App\Http\Controllers;

use App\Models\CheckInRecord;
use App\Models\CheckOutRecord;
use App\Models\ClassAttendance;
use App\Models\ClassRoom;
use App\Models\ClassSchedule;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\SubjectStudy;
use App\Models\Teacher;
use Illuminate\Http\Request;

class PrintReportController extends Controller
{
    public function generateReport(Request $request, $source, $view, $fileName, $extra = [])
    {
        $dataQuery = $source instanceof \Illuminate\Database\Eloquent\Builder
            ? $source
            : $source::query();

        $dateStart = $request->date_start ? $request->date_start . '-01' : null;
        $dateEnd   = $request->date_end
            ? date('Y-m-t', strtotime($request->date_end . '-01'))
            : null;

        if ($dateStart) {
            $dataQuery->whereDate('created_at', '>=', $dateStart);
        }

        if ($dateEnd) {
            $dataQuery->whereDate('created_at', '<=', $dateEnd);
        }

        $data = $dataQuery->get();

        if (!$dateStart && !$dateEnd) {
            $data = $dataQuery->get();
        }

        $payload = array_merge([
            'data'       => $data,
            'date_start' => $request->date_start,
            'date_end'   => $request->date_end,
        ], $extra);

        $pdf = \PDF::loadView($view, $payload)->setPaper('a4', 'portrait');

        $file = "cetak-data-{$fileName}";

        if ($request->date_start && $request->date_end) {
            $file .= "-[{$request->date_start}-{$request->date_end}]";
        } elseif ($request->date_start) {
            $file .= "-[{$request->date_start}]";
        } elseif ($request->date_end) {
            $file .= "-[{$request->date_end}]";
        }

        return $pdf->stream($file . ".pdf");
    }

    public function student(Request $request)
    {
        $kelas = ClassRoom::findOrFail($request->kelas);

        return $this->generateReport(
            $request,
            Student::query()->where('class_room_id', $kelas->id),
            'pdf.report.student',
            'laporan-siswa',
            ['kelas' => $kelas->name_class ?? 'SEMUA KELAS']
        );
    }

    public function teacher(Request $request)
    {
        $mapel = SubjectStudy::findOrFail($request->mapel);

        return $this->generateReport(
            $request,
            Teacher::query()->where('subject_study_id', $mapel->id),
            'pdf.report.teacher',
            'laporan-guru',
            ['mata_pelajaran' => $mapel->name_subject]
        );
    }

    public function subjectStudy(Request $request)
    {
        return $this->generateReport(
            $request,
            SubjectStudy::class,
            'pdf.report.subject-study',
            'laporan-mata-pelajaran'
        );
    }

    public function classSchedule(Request $request)
    {
        $kelas = ClassRoom::find($request->kelas);

        return $this->generateReport(
            $request,
            ClassSchedule::query()->when($kelas, function ($query) use ($kelas) {
                $query->where('class_room_id', $kelas->id);
            }),
            'pdf.report.class-schedule',
            'laporan-jadwal-kelas',
            [
                'class_room' => $kelas->name_class ?? 'SEMUA KELAS',
                'start_time' => $request->start_time,
                'end_time'   => $request->end_time,
            ]
        );
    }

    public function attendanceClass(Request $request)
    {
        $kelas = ClassRoom::find($request->kelas);

        return $this->generateReport(
            $request,
            StudentAttendance::query()->when($kelas, function ($query) use ($kelas) {
                $query->whereHas('class_attendance', function ($q) use ($kelas) {
                    $q->where('class_room_id', $kelas->id);
                });
            }),
            'pdf.report.attendance.class-report',
            'laporan-kehadiran-kelas',
            ['class_room' => $kelas->name_class ?? 'SEMUA KELAS']
        );
    }

    public function attendanceQrcodeCheckIn(Request $request)
    {
        return $this->generateReport(
            $request,
            CheckInRecord::class,
            'pdf.report.attendance.qrcode-check-in',
            'laporan-check-in'
        );
    }

    public function attendanceQrcodeCheckOut(Request $request)
    {
        return $this->generateReport(
            $request,
            CheckOutRecord::class,
            'pdf.report.attendance.qrcode-check-out',
            'laporan-check-out'
        );
    }
}
