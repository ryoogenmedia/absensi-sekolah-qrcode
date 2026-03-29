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

    public function mount()
    {
        // Load filters dari session
        $sessionFilters = session('attendance_class_filters', []);
        if ($sessionFilters) {
            $this->filters = $sessionFilters;
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
        $query = StudentAttendance::query()
            ->with([
                'student:id,full_name',
                'class_attendance.class_room:id,name_class',
                'class_attendance.class_schedule.teacher:id,name',
                'class_attendance.class_schedule.subject_study:id,name_subject'
            ])
            ->when($this->filters['startDate'], function ($query) {
                $query->whereHas('class_attendance', fn($q) => $q->where('created_at', '>=', Carbon::parse($this->filters['startDate'])->startOfDay()));
            })
            ->when($this->filters['endDate'], function ($query) {
                $query->whereHas('class_attendance', fn($q) => $q->where('created_at', '<=', Carbon::parse($this->filters['endDate'])->endOfDay()));
            })
            ->when($this->filters['kelas'], function ($query, $kelas) {
                $query->whereHas('class_attendance', fn($q) => $q->where('class_room_id', $kelas));
            })
            ->when($this->filters['search'], function ($query, $search) {
                $query->whereHas('student', fn($q) => $q->where('full_name', 'LIKE', "%{$search}%"));
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
