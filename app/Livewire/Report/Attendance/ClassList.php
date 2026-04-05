<?php

namespace App\Livewire\Report\Attendance;

use App\Livewire\Traits\DataTable\WithBulkActions;
use App\Livewire\Traits\DataTable\WithCachedRows;
use App\Livewire\Traits\DataTable\WithPerPagePagination;
use App\Livewire\Traits\DataTable\WithSorting;
use App\Models\ClassRoom;
use App\Models\StudentAttendance;
use Carbon\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class ClassList extends Component
{
    use WithBulkActions, WithPerPagePagination, WithCachedRows, WithSorting;

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
    public function class_rooms()
    {
        return ClassRoom::where('status_active', true)->get(['id', 'name_class']);
    }

    #[Computed()]
    public function rows()
    {
        $startDate = $this->filters['startDate']
            ? Carbon::parse($this->filters['startDate'])->startOfDay()
            : null;
        $endDate = $this->filters['endDate']
            ? Carbon::parse($this->filters['endDate'])->endOfDay()
            : null;

        $query = StudentAttendance::query()
            ->with([
                'student:id,full_name',
                'class_attendance:id,class_room_id,class_schedule_id,created_at,name_material',
                'class_attendance.class_room:id,name_class',
                'class_attendance.class_schedule:id,teacher_id,subject_study_id',
                'class_attendance.class_schedule.teacher:id,name',
                'class_attendance.class_schedule.subject_study:id,name_subject'
            ])->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $query->whereHas('class_attendance', fn($q) => $q->whereBetween('created_at', [$startDate, $endDate]));
            })->when($startDate && !$endDate, function ($query) use ($startDate) {
                $query->whereHas('class_attendance', fn($q) => $q->where('created_at', '>=', $startDate));
            })->when(!$startDate && $endDate, function ($query) use ($endDate) {
                $query->whereHas('class_attendance', fn($q) => $q->where('created_at', '<=', $endDate));
            })->when($this->filters['kelas'], function ($query) {
                $query->whereHas('class_attendance', fn($q) => $q->where('class_room_id', $this->filters['kelas']));
            })->when($this->filters['search'], function ($query) {
                $query->whereHas('student', fn($q) => $q->where('full_name', 'LIKE', "%{$this->filters['search']}%"));
            });

        return $this->applyPagination($query);
    }

    public function updatedFilters()
    {
        $this->resetPage();
    }

    #[On('filters-changed')]
    public function onFiltersChanged($filters)
    {
        $this->filters = $filters;
        $this->resetPage();
    }

    #[On('reset-filters')]
    public function onResetFilters()
    {
        $this->reset('filters');
        $this->resetPage();
    }

    public function validateAndPrint()
    {
        // Validasi kelas dan tanggal harus diisi
        if (empty($this->filters['kelas'])) {
            $this->dispatch('show-alert', [
                'type' => 'warning',
                'message' => 'Perhatian!',
                'detail' => "Data kelas wajib di isi sebelum mencetak.",
            ]);
            return '';
        }

        if (empty($this->filters['startDate']) || empty($this->filters['endDate'])) {
            $this->dispatch('show-alert', [
                'type' => 'warning',
                'message' => 'Perhatian!',
                'detail' => "Tanggal mulai dan selesai wajib diisi sebelum mencetak.",
            ]);
            return '';
        }

        // Jika valid, buat URL dan return
        $url = route('print-report.attendance.class.list') . '?' . http_build_query([
            'search' => $this->filters['search'],
            'kelas' => $this->filters['kelas'],
            'startDate' => $this->filters['startDate'],
            'endDate' => $this->filters['endDate'],
        ]);

        return $url;
    }

    public function render()
    {
        return view('livewire.report.attendance.class-list');
    }
}
