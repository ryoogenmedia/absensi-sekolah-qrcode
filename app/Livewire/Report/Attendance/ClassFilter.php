<?php

namespace App\Livewire\Report\Attendance;

use App\Models\ClassRoom;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class ClassFilter extends Component
{
    public $filters = [
        'search' => '',
        'kelas' => '',
        'startDate' => '',
        'endDate' => '',
    ];

    public function mount()
    {
        // Load filters dari session jika ada
        $savedFilters = session('attendance_class_filters');
        if ($savedFilters) {
            $this->filters = $savedFilters;
            // Dispatch event ke parent dengan filters yang di-load dari session
            $this->dispatch('filters-changed', filters: $this->filters);
        }
    }

    #[Computed()]
    public function class_rooms()
    {
        return ClassRoom::where('status_active', true)->get(['id', 'name_class']);
    }

    public function updatedFilters()
    {
        // Simpan filters ke session setiap kali ada perubahan
        session()->put('attendance_class_filters', $this->filters);

        // Notify parent tentang perubahan filter
        $this->dispatch('filters-changed', filters: $this->filters);
    }

    #[On('reset-filters')]
    public function resetFilters()
    {
        // Hapus filters dari session
        session()->forget('attendance_class_filters');

        // Reset filters di component
        $this->reset('filters');

        // Notify parent tentang reset
        $this->dispatch('filters-changed', filters: $this->filters);

        // Show alert
        $this->dispatch('alert', [
            'type' => 'success',
            'message' => 'Filter telah dibersihkan.'
        ]);
    }

    public function render()
    {
        return view('livewire.report.attendance.class-filter');
    }
}
