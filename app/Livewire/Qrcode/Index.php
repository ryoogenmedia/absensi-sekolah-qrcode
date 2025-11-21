<?php

namespace App\Livewire\Qrcode;

use App\Livewire\Traits\DataTable\WithBulkActions;
use App\Livewire\Traits\DataTable\WithCachedRows;
use App\Livewire\Traits\DataTable\WithPerPagePagination;
use App\Livewire\Traits\DataTable\WithSorting;
use App\Models\ClassRoom;
use App\Models\Student;
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
        'nis' => '',
        'kelas' => '',
    ];

    #[Computed()]
    public function class_rooms()
    {
        return ClassRoom::where('status_active', true)->get(['id', 'name_class']);
    }

    #[On('muat-ulang')]
    #[Computed()]
    public function rows()
    {
        $query = Student::query()
            ->when(!$this->sorts, fn($query) => $query->first())
            ->when($this->filters['nis'], function ($q, $nis) {
                $q->where('nis', $nis);
            })
            ->when($this->filters['kelas'], function ($q, $kelas) {
                $q->where('class_room_id', $kelas);
            })
            ->when($this->filters['search'], function ($query, $search) {
                $query->where('full_name', 'LIKE', "%$search%")
                    ->orWhere('call_name', 'LIKE', "%$search%");
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

    public function muatUlang()
    {
        $this->dispatch('muat-ulang');
        $this->reset();
    }

    public function render()
    {
        return view('livewire.qrcode.index');
    }
}
