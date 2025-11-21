<?php

namespace App\Livewire\Attendance\Class;

use App\Livewire\Traits\DataTable\WithBulkActions;
use App\Livewire\Traits\DataTable\WithCachedRows;
use App\Livewire\Traits\DataTable\WithPerPagePagination;
use App\Livewire\Traits\DataTable\WithSorting;
use App\Models\ClassAttendance;
use App\Models\ClassRoom;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
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

    public function deleteSelected()
    {
        $classAttendance = ClassAttendance::whereIn('id', $this->selected)->get();
        $deleteCount = $classAttendance->count();

        foreach ($classAttendance as $data) {
            if ($data->picture_evidence) {
                File::delete(public_path('storage/' . $data->picture_evidence));
            }

            foreach ($data->student_attendances as $student_attendance) {
                $student_attendance->delete();
            }

            $data->delete();
        }

        $this->reset();

        session()->flash('alert', [
            'type' => 'success',
            'message' => 'Berhasil!',
            'detail' => "Berhasil $deleteCount data presensi berhasil di hapus.",
        ]);

        return redirect()->back();
    }

    #[Computed()]
    public function class_rooms()
    {
        return ClassRoom::where('status_active', true)->get(['id', 'name_class']);
    }

    #[On('muat-ulang')]
    #[Computed()]
    public function rows()
    {
        $query = ClassAttendance::query()
            ->when($this->filters['startDate'] && $this->filters['endDate'], function ($query) {
                $start = Carbon::parse($this->filters['startDate'])->startOfDay();
                $end   = Carbon::parse($this->filters['endDate'])->endOfDay();

                $query->whereBetween('created_at', [$start, $end]);
            })
            ->when($this->filters['startDate'] && !$this->filters['endDate'], function ($query) {
                $start = Carbon::parse($this->filters['startDate'])->startOfDay();
                $query->where('created_at', '>=', $start);
            })
            ->when(!$this->filters['startDate'] && $this->filters['endDate'], function ($query) {
                $end = Carbon::parse($this->filters['endDate'])->endOfDay();
                $query->where('created_at', '<=', $end);
            })

            ->when($this->filters['kelas'], function ($query, $kelas) {
                $query->where('class_room_id', $kelas);
            })
            ->when($this->filters['search'], function ($query, $search) {
                $query->whereHas('class_room', function ($query) use ($search) {
                    $query->where('name_class', 'LIKE', "%$search%");
                });
            })->latest();

        return $this->applyPagination($query);
    }

    #[Computed()]
    public function allData()
    {
        return ClassAttendance::all();
    }

    public function updatedFilters()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset('filters');
    }

    public function muatUlang()
    {
        $this->dispatch('muat-ulang');
        $this->reset();
    }

    public function render()
    {
        return view('livewire.attendance.class.index');
    }
}
