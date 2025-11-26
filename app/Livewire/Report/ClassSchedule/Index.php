<?php

namespace App\Livewire\Report\ClassSchedule;

use App\Livewire\Traits\DataTable\WithBulkActions;
use App\Livewire\Traits\DataTable\WithCachedRows;
use App\Livewire\Traits\DataTable\WithPerPagePagination;
use App\Livewire\Traits\DataTable\WithSorting;
use App\Models\ClassRoom;
use App\Models\ClassSchedule;
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
        'class_room' => '',
        'start_time' => '',
        'end_time' => '',
    ];

    #[Computed()]
    public function class_rooms()
    {
        return ClassRoom::all(['id', 'name_class']);
    }

    #[Computed()]
    public function rows()
    {
        $query = ClassSchedule::query()
            ->when(!$this->sorts, function ($query) {
                $query->join('class_rooms', 'class_schedules.class_room_id', '=', 'class_rooms.id')
                    ->orderBy('class_rooms.name_class')
                    ->select('class_schedules.*');
            })
            ->when($this->filters['search'], function ($query, $search) {
                $query->whereHas('class_room', function ($query) use ($search) {
                    $query->where('name_class', 'LIKE', "%$search%");
                })->orWhereHas('teacher', function ($query) use ($search) {
                    $query->where('name', 'LIKE', "$search");
                });
            })
            ->when($this->filters['class_room'], function ($query, $classId) {
                $query->where('class_room_id', $classId);
            })
            ->when($this->filters['start_time'], function ($query, $startTime) {
                $query->whereTime('start_time', '>=', $startTime);
            })
            ->when($this->filters['end_time'], function ($query, $endTime) {
                $query->whereTime('end_time', '<=', $endTime);
            });

        return $this->applyPagination($query);
    }

    #[Computed()]
    public function allData()
    {
        return ClassSchedule::all();
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
        return view('livewire.report.class-schedule.index');
    }
}
