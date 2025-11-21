<?php

namespace App\Livewire\TeacherSubject;

use App\Livewire\Traits\DataTable\WithBulkActions;
use App\Livewire\Traits\DataTable\WithCachedRows;
use App\Livewire\Traits\DataTable\WithPerPagePagination;
use App\Livewire\Traits\DataTable\WithSorting;
use App\Models\SubjectStudy;
use App\Models\Teacher;
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
        'nip' => '',
        'mataPelajaran' => '',
        'jenisKelamin' => '',
    ];

    public $showModal = false;
    public $teacherId;
    public $mataPelajaran;

    public function openModal($id)
    {
        $this->showModal = true;
        $teacher = Teacher::findOrFail($id);
        $this->teacherId = $teacher->id;
        $this->mataPelajaran = $teacher->subject_study_id ?? null;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->teacherId = null;
        $this->mataPelajaran = null;
    }

    public function changeSubjectStudyTeacher()
    {
        $teacher = Teacher::findOrFail($this->teacherId);

        $this->validate([
            'mataPelajaran' => ['required'],
        ]);

        $teacher->subject_study_id = $this->mataPelajaran;
        $teacher->save();

        session()->flash('alert', [
            'type' => 'success',
            'message' => 'Berhasil!',
            'detail' => "Mata pelajaran guru berhasil di ubah.",
        ]);

        return redirect()->route('subject-teacher.index');
    }

    #[Computed()]
    public function subject_studies()
    {
        return SubjectStudy::where('status_active', true)->get(['id', 'name_subject']);
    }

    #[On('muat-ulang')]
    #[Computed()]
    public function rows()
    {
        $query = Teacher::query()
            ->when(!$this->sorts, fn($query) => $query->first())
            ->when($this->filters['jenisKelamin'], function ($query, $jenisKelamin) {
                $query->where('sex', $jenisKelamin);
            })
            ->when($this->filters['mataPelajaran'], function ($query, $mapel) {
                $query->where('subject_study_id', $mapel);
            })
            ->when($this->filters['nip'], function ($query, $nip) {
                $query->where('nip', $nip);
            })
            ->when($this->filters['search'], function ($query, $search) {
                $query->where('name', 'LIKE', "%$search%");
            })->latest();

        return $this->applyPagination($query);
    }

    #[Computed()]
    public function allData()
    {
        return Teacher::all();
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
        return view('livewire.teacher-subject.index');
    }
}
