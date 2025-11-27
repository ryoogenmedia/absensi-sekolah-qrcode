<?php

namespace App\Http\Controllers;

use App\Models\CheckInRecord;
use App\Models\CheckOutRecord;
use App\Models\ClassAttendance;
use App\Models\ClassSchedule;
use App\Models\Student;
use App\Models\SubjectStudy;
use App\Models\Teacher;
use Illuminate\Http\Request;

class PrintReportController extends Controller
{

    public function generateReport(Request $request, $model, $view, $fileName, $extra = [])
    {
        $dataQuery = $model::query();

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
            $data = $model::all();
        }

        $payload = array_merge([
            'data'       => $data,
            'date_start' => $request->date_start,
            'date_end'   => $request->date_end,
        ], $extra);

        $pdf = \PDF::loadView($view, $payload)
            ->setPaper('a4', 'portrait');

        $file = "cetak-data-{$fileName}";
        if ($dateStart && $dateEnd) {
            $file .= "-[$request->date_start-$request->date_end]";
        } elseif ($dateStart) {
            $file .= "-[$request->date_start]";
        } elseif ($dateEnd) {
            $file .= "-[$request->date_end]";
        }
        $file .= ".pdf";

        return $pdf->stream($file);
    }

    public function student(Request $request)
    {
        return $this->generateReport(
            $request,
            Student::class,
            'print.student',
            'laporan-siswa',
            [
                'kelas' => $request->kelas
            ]
        );
    }

    public function teacher(Request $request)
    {
        return $this->generateReport(
            $request,
            Teacher::class,
            'print.teacher',
            'laporan-guru',
            [
                'mata_pelajaran' => $request->mata_pelajaran
            ]
        );
    }

    public function subjectStudy(Request $request)
    {
        return $this->generateReport(
            $request,
            SubjectStudy::class,
            'print.subject-study',
            'laporan-mata-pelajaran'
        );
    }

    public function classSchedule(Request $request)
    {
        return $this->generateReport(
            $request,
            ClassSchedule::class,
            'print.class-schedule',
            'laporan-jadwal-kelas',
            [
                'class_room' => $request->class_room,
                'start_time' => $request->start_time,
                'end_time'   => $request->end_time,
            ]
        );
    }

    public function attendanceClass(Request $request)
    {
        return $this->generateReport(
            $request,
            ClassAttendance::class,
            'print.class-attendance',
            'laporan-kehadiran-kelas',
            [
                'class_room' => $request->class_room
            ]
        );
    }

    public function attendanceQrcodeCheckIn(Request $request)
    {
        return $this->generateReport(
            $request,
            CheckOutRecord::class,
            'print.qr-checkin',
            'laporan-check-in'
        );
    }

    public function attendanceQrcodeCheckOut(Request $request)
    {
        return $this->generateReport(
            $request,
            CheckInRecord::class,
            'print.qr-checkout',
            'laporan-check-out'
        );
    }
}
