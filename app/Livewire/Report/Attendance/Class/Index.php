<?php

namespace App\Livewire\Report\Attendance\Class;

use App\Models\ClassRoom;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Laporan Presensi Kelas')]
class Index extends Component
{
    public $tab = 'list';

    public $filters = [
        'search' => '',
        'kelas' => '',
        'startDate' => '',
        'endDate' => '',
    ];

    public function mount()
    {
        // Load filters dari session jika ada
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

    public function setTab($tabName)
    {
        $this->tab = $tabName;
    }

    #[On('filters-changed')]
    public function onFiltersChanged($filters)
    {
        $this->filters = $filters;
    }

    public function resetFilters()
    {
        // Dispatch event ke ClassFilter component
        $this->dispatch('reset-filters');
    }

    public function render()
    {
        return view('livewire.report.attendance.class.index');
    }
}
