<?php

namespace App\Livewire\StudentSchedule;

use App\Livewire\Traits\DataTable\WithBulkActions;
use App\Livewire\Traits\DataTable\WithCachedRows;
use App\Livewire\Traits\DataTable\WithPerPagePagination;
use App\Livewire\Traits\DataTable\WithSorting;
use App\Models\ClassSchedule;
use Illuminate\Support\Facades\Auth;
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
        'name_day' => '',
    ];

    public $studentId;

    #[On('muat-ulang')]
    #[Computed()]
    public function rows()
    {
        $query = ClassSchedule::query()
            ->when(!$this->sorts, fn($query) => $query->first())
            ->when($this->filters['name_day'], function($query, $nameDay){
                $query->where('day_name', $nameDay);
            })
            ->whereHas('class_room.students', function($query){
                $query->where('id', $this->studentId);
            })->latest();

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

    public function muatUlang()
    {
        $this->dispatch('muat-ulang');
    }

    public function mount(){
        $user = Auth::user();
        $this->studentId = $user->student->id;
    }

    public function render()
    {
        return view('livewire.student-schedule.index');
    }
}
