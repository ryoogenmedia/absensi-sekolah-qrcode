<?php

namespace App\Livewire\Report\Attendance\Class;

use App\Livewire\Traits\DataTable\WithBulkActions;
use App\Livewire\Traits\DataTable\WithCachedRows;
use App\Livewire\Traits\DataTable\WithPerPagePagination;
use App\Livewire\Traits\DataTable\WithSorting;
use App\Models\ClassAttendance;
use App\Models\ClassRoom;
use App\Models\StudentAttendance;
use Carbon\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Index extends Component
{
    use WithBulkActions;
    use WithPerPagePagination;
    use WithCachedRows;
    use WithSorting;

    public $filters = [
        'search' => '',
        'kelas' => '',
        'startDate' => '',
        'endDate' => '',
    ];

    #[Computed()]
    public function class_rooms()
    {
        return ClassRoom::where('status_active', true)->get(['id', 'name_class']);
    }

    #[Computed()]
    public function rows()
    {
        $query = StudentAttendance::query()

            // Filter tanggal dengan relasi class_attendance
            ->when($this->filters['startDate'] && $this->filters['endDate'], function ($query) {
                $start = Carbon::parse($this->filters['startDate'])->startOfDay();
                $end   = Carbon::parse($this->filters['endDate'])->endOfDay();

                $query->whereHas('class_attendance', function ($q) use ($start, $end) {
                    $q->whereBetween('created_at', [$start, $end]);
                });
            })

            ->when($this->filters['startDate'] && !$this->filters['endDate'], function ($query) {
                $start = Carbon::parse($this->filters['startDate'])->startOfDay();

                $query->whereHas('class_attendance', function ($q) use ($start) {
                    $q->where('created_at', '>=', $start);
                });
            })

            ->when(!$this->filters['startDate'] && $this->filters['endDate'], function ($query) {
                $end = Carbon::parse($this->filters['endDate'])->endOfDay();

                $query->whereHas('class_attendance', function ($q) use ($end) {
                    $q->where('created_at', '<=', $end);
                });
            })

            // Filter berdasarkan kelas
            ->when($this->filters['kelas'], function ($query, $kelas) {
                $query->whereHas('class_attendance', function ($q) use ($kelas) {
                    $q->where('class_room_id', $kelas);
                });
            })

            // Filter pencarian nama siswa
            ->when($this->filters['search'], function ($query, $search) {
                $query->whereHas('student', function ($q) use ($search) {
                    $q->where('full_name', 'LIKE', "%{$search}%");
                });
            })

            ->latest();

        return $this->applyPagination($query);
    }


    #[Computed()]
    public function allData()
    {
        return StudentAttendance::all();
    }

    public function updatedFilters()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset('filters');
    }

    public function render()
    {
        return view('livewire.report.attendance.class.index');
    }
}
