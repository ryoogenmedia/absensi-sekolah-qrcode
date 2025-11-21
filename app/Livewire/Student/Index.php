<?php

namespace App\Livewire\Student;

use App\Livewire\Traits\DataTable\WithBulkActions;
use App\Livewire\Traits\DataTable\WithCachedRows;
use App\Livewire\Traits\DataTable\WithPerPagePagination;
use App\Livewire\Traits\DataTable\WithSorting;
use App\Models\ClassRoom;
use App\Models\Student;
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
        'nis' => '',
        'kelas' => '',
        'agama' => '',
        'jenisKelamin' => '',
    ];

    public function deleteSelected()
    {
        $student = Student::whereIn('id', $this->selected)->get();
        $deleteCount = $student->count();

        foreach ($student as $data) {
            if ($data->photo) {
                File::delete(public_path('storage/' . $data->photo));
            }

            if ($data->user->avatar) {
                File::delete(public_path('storage/' . $data->user->avatar));
                $data->user->delete();
            }

            $data->delete();
        }

        $this->reset();

        session()->flash('alert', [
            'type' => 'success',
            'message' => 'Berhasil!',
            'detail' => "Berhasil menghapus $deleteCount data siswa.",
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

    public function muatUlang()
    {
        $this->dispatch('muat-ulang');
        $this->reset();
    }

    public function render()
    {
        return view('livewire.student.index');
    }
}
