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
use Illuminate\Database\Eloquent\Builder;

class PrintReportController extends Controller
{
    public function generateReport(Request $request, $source, $view, $fileName, $extra = [])
    {
        $dataQuery = $source instanceof Builder
            ? $source
            : (is_string($source) && class_exists($source) ? $source::query() : null);

        if ($dataQuery === null) {
            $dataQuery = Student::query();
        }

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
        $kelas = $request->kelas ? ClassRoom::find($request->kelas) : null;

        $query = Student::query();

        if ($kelas) {
            $query->where('class_room_id', $kelas->id);
        }

        $kelasName = $kelas->name_class ?? 'SEMUA KELAS';

        return $this->generateReport(
            $request,
            $query,
            'pdf.report.student',
            'laporan-siswa',
            ['kelas' => $kelasName]
        );
    }

    public function teacher(Request $request)
    {
        $mapel = $request->mapel ? SubjectStudy::find($request->mapel) : null;

        $query = Teacher::query();

        if ($mapel) {
            $query->where('subject_study_id', $mapel->id);
        }

        $mapelName = $mapel->name_subject ?? 'SEMUA MATA PELAJARAN';

        return $this->generateReport(
            $request,
            $query,
            'pdf.report.teacher',
            'laporan-guru',
            ['mata_pelajaran' => $mapelName]
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
        $kelas = $request->kelas ? ClassRoom::find($request->kelas) : null;

        $query = ClassSchedule::query();

        $query->when($kelas, function (Builder $q) use ($kelas) {
            $q->where('class_room_id', $kelas->id);
        });

        $kelasName = $kelas->name_class ?? 'SEMUA KELAS';

        return $this->generateReport(
            $request,
            $query,
            'pdf.report.class-schedule',
            'laporan-jadwal-kelas',
            [
                'class_room' => $kelasName,
                'start_time' => $request->start_time,
                'end_time'   => $request->end_time,
            ]
        );
    }

    public function attendanceClass(Request $request)
    {
        $kelas = $request->kelas ? ClassRoom::find($request->kelas) : null;

        $query = StudentAttendance::query();

        $query->when($kelas, function (Builder $q) use ($kelas) {
            $q->whereHas('class_attendance', function ($subQuery) use ($kelas) {
                $subQuery->where('class_room_id', $kelas->id);
            });
        });

        $kelasName = $kelas->name_class ?? 'SEMUA KELAS';

        return $this->generateReport(
            $request,
            $query,
            'pdf.report.attendance.class-report',
            'laporan-kehadiran-kelas',
            ['class_room' => $kelasName]
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

    public function attendanceClassList(Request $request)
    {
        $kelas = $request->kelas ? ClassRoom::find($request->kelas) : null;
        $startDate = $request->startDate;
        $endDate = $request->endDate;

        $query = StudentAttendance::query()
            ->with([
                'student:id,full_name',
                'class_attendance.class_room:id,name_class',
                'class_attendance.class_schedule.teacher:id,name',
                'class_attendance.class_schedule.subject_study:id,name_subject'
            ])
            ->when($startDate, function (Builder $q) use ($startDate) {
                $q->whereHas('class_attendance', fn($subQ) => $subQ->where('created_at', '>=', $startDate . ' 00:00:00'));
            })
            ->when($endDate, function (Builder $q) use ($endDate) {
                $q->whereHas('class_attendance', fn($subQ) => $subQ->where('created_at', '<=', $endDate . ' 23:59:59'));
            })
            ->when($kelas, function (Builder $q) use ($kelas) {
                $q->whereHas('class_attendance', fn($subQ) => $subQ->where('class_room_id', $kelas->id));
            })
            ->when($request->search, function (Builder $q) use ($request) {
                $q->whereHas('student', fn($subQ) => $subQ->where('full_name', 'LIKE', "%{$request->search}%"));
            });

        $data = $query->get();
        $kelasName = $kelas->name_class ?? 'SEMUA KELAS';

        $pdf = \PDF::loadView('pdf.report.attendance.class-list', [
            'data'       => $data,
            'kelas'      => $kelasName,
            'startDate'  => $startDate,
            'endDate'    => $endDate,
        ])->setPaper('a4', 'landscape');

        $fileName = "laporan-presensi-list-{$kelasName}";
        if ($startDate && $endDate) {
            $fileName .= "-[{$startDate}_{$endDate}]";
        }

        return $pdf->stream($fileName . ".pdf");
    }

    public function attendanceClassSummary(Request $request)
    {
        $kelas = $request->kelas ? ClassRoom::find($request->kelas) : null;
        $startDate = $request->startDate;
        $endDate = $request->endDate;

        $statuses = collect(config('const.attendance_status'))
            ->map(fn($s) => is_array($s) ? $s['value'] : $s);

        if ($kelas) {
            $data = Student::where('class_room_id', $kelas->id)
                ->withCount([
                    'student_attendances as total_hadir' => function ($query) use ($startDate, $endDate) {
                        $query->where('status_attendance', 'hadir')
                            ->when($startDate, fn($q) => $q->whereDate('created_at', '>=', $startDate))
                            ->when($endDate, fn($q) => $q->whereDate('created_at', '<=', $endDate));
                    }
                ])
                ->get();
            $summaryType = 'siswa';
        } else {
            $query = ClassRoom::query()
                ->select('class_rooms.id', 'class_rooms.name_class')
                ->leftJoin('students', 'students.class_room_id', '=', 'class_rooms.id')
                ->leftJoin('student_attendances', 'student_attendances.student_id', '=', 'students.id')
                ->leftJoin('class_attendances', 'class_attendances.id', '=', 'student_attendances.class_attendance_id')
                ->where('class_rooms.status_active', true)
                ->groupBy('class_rooms.id', 'class_rooms.name_class');

            if ($startDate && $endDate) {
                $query->whereDate('class_attendances.created_at', '>=', $startDate)
                    ->whereDate('class_attendances.created_at', '<=', $endDate);
            }

            foreach ($statuses as $status) {
                $query->selectRaw("SUM(CASE WHEN student_attendances.status_attendance = '{$status}' THEN 1 ELSE 0 END) as count_{$status}");
            }

            $data = $query->get();
            $summaryType = 'kelas';
        }

        $kelasName = $kelas->name_class ?? 'SEMUA KELAS';

        $pdf = \PDF::loadView('pdf.report.attendance.class-summary', [
            'data'        => $data,
            'kelas'       => $kelasName,
            'startDate'   => $startDate,
            'endDate'     => $endDate,
            'summaryType' => $summaryType,
            'statuses'    => $statuses,
        ])->setPaper('a4', 'landscape');

        $fileName = "laporan-presensi-ringkasan-{$kelasName}";
        if ($startDate && $endDate) {
            $fileName .= "-[{$startDate}_{$endDate}]";
        }

        return $pdf->stream($fileName . ".pdf");
    }
}
