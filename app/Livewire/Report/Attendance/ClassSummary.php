<?php

namespace App\Livewire\Report\Attendance;

use App\Models\ClassRoom;
use App\Models\Student;
use Carbon\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class ClassSummary extends Component
{
    public $filters = [
        'search' => '',
        'kelas' => '',
        'startDate' => '',
        'endDate' => '',
    ];

    public function mount($filters = [])
    {
        // Priority: session filters > passed filters > default filters
        $sessionFilters = session('attendance_class_filters', []);
        $this->filters = array_merge($this->filters, $sessionFilters ?: $filters);
    }

    public function placeholder()
    {
        return <<<'HTML'
            <div class="d-flex align-items-center justify-content-center" style="min-height: 300px;">
                <div class="text-center">
                    <div class="spinner-grow text-primary mb-3" role="status" style="width: 2rem; height: 2rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="text-muted">Memproses ringkasan data...</p>
                </div>
            </div>
        HTML;
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
            return Student::where('class_room_id', $this->filters['kelas'])
                ->withCount([
                    'student_attendances as total_hadir' => function ($query) use ($startDate, $endDate) {
                        $query->where('status_attendance', 'hadir')
                            ->when($startDate, fn($q) => $q->whereBetween('created_at', [$startDate, now()]))
                            ->when($endDate, fn($q) => $q->whereBetween('created_at', [now(), $endDate]));
                    }
                ])
                ->get();
        }

        $query = ClassRoom::query()
            ->select('class_rooms.id', 'class_rooms.name_class')
            ->leftJoin('students', 'students.class_room_id', '=', 'class_rooms.id')
            ->leftJoin('student_attendances', 'student_attendances.student_id', '=', 'students.id')
            ->leftJoin('class_attendances', 'class_attendances.id', '=', 'student_attendances.class_attendance_id')
            ->where('class_rooms.status_active', true)
            ->groupBy('class_rooms.id', 'class_rooms.name_class');

        if ($startDate && $endDate) {
            $query->whereBetween('class_attendances.created_at', [$startDate, $endDate]);
        }

        foreach ($statuses as $status) {
            $query->selectRaw("
            SUM(CASE WHEN student_attendances.status_attendance = ? THEN 1 ELSE 0 END) as count_{$status}
        ", [$status]);
        }

        return $query->get();
    }

    public function render()
    {
        return view('livewire.report.attendance.class-summary');
    }
}
