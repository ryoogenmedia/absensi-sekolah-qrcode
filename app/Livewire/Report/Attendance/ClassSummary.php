<?php

namespace App\Livewire\Report\Attendance;

use App\Models\ClassRoom;
use App\Models\Student;
use Carbon\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class ClassSummary extends Component
{
    public $filters = [
        'search' => '',
        'kelas' => '',
        'startDate' => '',
        'endDate' => '',
    ];

    public function mount($filters = null)
    {
        // If filters passed from parent, use them
        if ($filters && is_array($filters)) {
            $this->filters = $filters;
        } else {
            // Otherwise load filters dari session
            $sessionFilters = session('attendance_class_filters', []);
            if ($sessionFilters) {
                $this->filters = $sessionFilters;
            }
        }
    }

    #[Computed()]
    public function summaryData()
    {
        $statuses = collect(config('const.attendance_status'))
            ->map(fn($s) => is_array($s) ? $s['value'] : $s);

        $startDate = $this->filters['startDate']
            ? Carbon::parse($this->filters['startDate'])->startOfDay()
            : null;

        $endDate = $this->filters['endDate']
            ? Carbon::parse($this->filters['endDate'])->endOfDay()
            : null;

        if ($this->filters['kelas']) {
            // Optimized query dengan single SELECT
            $dateCondition = 'true';
            if ($startDate && $endDate) {
                $dateCondition = "class_attendances.created_at BETWEEN '{$startDate}' AND '{$endDate}'";
            } elseif ($startDate) {
                $dateCondition = "class_attendances.created_at >= '{$startDate}'";
            } elseif ($endDate) {
                $dateCondition = "class_attendances.created_at <= '{$endDate}'";
            }

            return Student::where('students.class_room_id', $this->filters['kelas'])
                ->leftJoin('student_attendances', 'student_attendances.student_id', '=', 'students.id')
                ->leftJoin('class_attendances', 'class_attendances.id', '=', 'student_attendances.class_attendance_id')
                ->select('students.id', 'students.full_name')
                ->selectRaw("SUM(CASE WHEN (student_attendances.status_attendance = 'hadir' AND {$dateCondition}) THEN 1 ELSE 0 END) as total_hadir")
                ->selectRaw("SUM(CASE WHEN (student_attendances.status_attendance = 'alpa' AND {$dateCondition}) THEN 1 ELSE 0 END) as total_alpa")
                ->selectRaw("SUM(CASE WHEN (student_attendances.status_attendance = 'izin' AND {$dateCondition}) THEN 1 ELSE 0 END) as total_izin")
                ->selectRaw("SUM(CASE WHEN (student_attendances.status_attendance = 'sakit' AND {$dateCondition}) THEN 1 ELSE 0 END) as total_sakit")
                ->groupBy('students.id', 'students.full_name')
                ->orderBy('students.full_name')
                ->get();
        }

        $dateCondition = 'true';
        if ($startDate && $endDate) {
            $dateCondition = "class_attendances.created_at BETWEEN '{$startDate}' AND '{$endDate}'";
        } elseif ($startDate) {
            $dateCondition = "class_attendances.created_at >= '{$startDate}'";
        } elseif ($endDate) {
            $dateCondition = "class_attendances.created_at <= '{$endDate}'";
        }

        $query = ClassRoom::query()
            ->select('class_rooms.id', 'class_rooms.name_class')
            ->leftJoin('students', 'students.class_room_id', '=', 'class_rooms.id')
            ->leftJoin('student_attendances', 'student_attendances.student_id', '=', 'students.id')
            ->leftJoin('class_attendances', 'class_attendances.id', '=', 'student_attendances.class_attendance_id')
            ->where('class_rooms.status_active', true)
            ->groupBy('class_rooms.id', 'class_rooms.name_class');

        foreach ($statuses as $status) {
            $query->selectRaw("SUM(CASE WHEN (student_attendances.status_attendance = '{$status}' AND {$dateCondition}) THEN 1 ELSE 0 END) as count_{$status}");
        }

        return $query->orderBy('class_rooms.name_class')->get();
    }

    #[On('filters-changed')]
    public function onFiltersChanged($filters)
    {
        if (is_array($filters)) {
            $this->filters = $filters;
        }
    }

    #[On('reset-filters')]
    public function onResetFilters()
    {
        $this->reset('filters');
    }

    public function render()
    {
        return view('livewire.report.attendance.class-summary');
    }
}
