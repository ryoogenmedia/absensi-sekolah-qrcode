<?php

namespace App\Livewire\Report\Student;

use App\Livewire\Traits\DataTable\WithBulkActions;
use App\Livewire\Traits\DataTable\WithCachedRows;
use App\Livewire\Traits\DataTable\WithPerPagePagination;
use App\Livewire\Traits\DataTable\WithSorting;
use App\Models\ClassRoom;
use App\Models\Student;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;

class Index extends Component
{
    use WithBulkActions;
    use WithPerPagePagination;
    use WithCachedRows;
    use WithSorting;
    use WithFileUploads;

    public $filters = [
        'search' => '',
        'nis' => '',
        'kelas' => '',
        'agama' => '',
        'jenisKelamin' => '',
    ];

    #[Computed()]
    public function class_rooms()
    {
        return ClassRoom::where('status_active', true)->get(['id', 'name_class']);
    }

    #[Computed()]
    public function rows()
    {
        $query = Student::query()
            ->when(!$this->sorts, fn($query) => $query->first())
            ->when($this->filters['kelas'], function ($query, $kelas) {
                $query->where('class_room_id', $kelas);
            })
            ->when($this->filters['nis'], function ($query, $nis) {
                $query->where('nis', $nis);
            })
            ->when($this->filters['agama'], function ($query, $agama) {
                $query->where('religion', $agama);
            })
            ->when($this->filters['jenisKelamin'], function ($query, $jenisKelamin) {
                $query->where('sex', $jenisKelamin);
            })
            ->when($this->filters['search'], function ($query, $search) {
                $query->where('full_name', 'LIKE', "%$search%")
                    ->orWhere('call_name', 'LIKE', "%$search%")
                    ->orWhere('nis', 'LIKE', "%$search%");
            })->latest();

        return $this->applyPagination($query);
    }

    #[Computed()]
    public function allData()
    {
        return Student::all();
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
        return view('livewire.report.student.index');
    }
}
