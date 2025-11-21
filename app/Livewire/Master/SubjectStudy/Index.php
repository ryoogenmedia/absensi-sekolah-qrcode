<?php

namespace App\Livewire\Master\SubjectStudy;

use App\Exports\SubjectStudyExport;
use App\Imports\SubjectStudyImport;
use App\Livewire\Traits\DataTable\WithBulkActions;
use App\Livewire\Traits\DataTable\WithCachedRows;
use App\Livewire\Traits\DataTable\WithPerPagePagination;
use App\Livewire\Traits\DataTable\WithSorting;
use App\Models\SubjectStudy;
use Exception;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

class Index extends Component
{
    use WithBulkActions;
    use WithPerPagePagination;
    use WithCachedRows;
    use WithSorting;
    use WithFileUploads;

    public $filters = [
        'search' => '',
    ];
    // FORM DATA
    public $namaMataPelajaran;
    public $deskripsi;
    public $statusAktif = true;

    // MODAL INITIALIZATION
    public $modalCreate = false;
    public $modalEdit = false;

    // IDENTITY
    public $subjectStudyId;

    public $showModalExcel = false;
    public $fileExcel;

    public function closeModalExcel()
    {
        $this->showModalExcel = false;
    }

    public function openModalExcel()
    {
        $this->showModalExcel = true;
    }

    public function exportExcel()
    {
        try {
            return Excel::download(new SubjectStudyExport, 'data-mapel.xlsx');
        } catch (Exception $e) {

            logger()->error(
                '[export excel subject study] ' .
                    auth()->user()->username .
                    ' gagal export data mapel',
                [$e->getMessage()]
            );

            session()->flash('alert', [
                'type' => 'danger',
                'message' => 'Gagal!',
                'detail' => 'Export data mapel gagal dilakukan.',
            ]);

            $this->resetForm();
            return redirect()->back();
        }

        session()->flash('alert', [
            'type' => 'success',
            'message' => 'Berhasil!',
            'detail' => 'Export data mapel berhasil dilakukan.',
        ]);

        $this->resetForm();
        return redirect()->back();
    }


    public function importExcel()
    {
        try {
            DB::beginTransaction();

            Excel::queueImport(new SubjectStudyImport, $this->fileExcel);

            DB::commit();
        } catch (Exception $e) {
            DB::beginTransaction();

            logger()->error(
                '[import excel subject study] ' .
                    auth()->user()->username .
                    ' gagal import data guru',
                [$e->getMessage()]
            );

            session()->flash('alert', [
                'type' => 'danger',
                'message' => 'Gagal.',
                'detail' => "import data guru gagal dilakukan.",
            ]);

            $this->resetForm();
            return redirect()->back();
        }

        session()->flash('alert', [
            'type' => 'success',
            'message' => 'Berhasil.',
            'detail' => "import data guru berhasil dilakukan.",
        ]);

        $this->resetForm();
        return redirect()->back();
    }

    public function resetForm()
    {
        $this->reset([
            'showModalExcel',
            'fileExcel',
        ]);
    }

    // TOGGLE STATUS ACTIVE
    public function changeStatusActive($id)
    {
        $subjectStudy = SubjectStudy::findOrFail($id);
        $subjectStudy->status_active = !$subjectStudy->status_active;
        $subjectStudy->save();

        session()->flash('alert', [
            'type' => 'success',
            'message' => 'Berhasil!',
            'detail' => "Berhasil mengubah status aktif mata pelajaran.",
        ]);

        return redirect()->back();
    }

    // MODAL HANDLERS
    public function closeModal()
    {
        $this->resetModal();
    }

    public function openModalCreate()
    {
        $this->resetModal();
        $this->modalCreate = true;
    }

    public function openModalEdit($id)
    {
        $this->resetModal();
        $subjectStudy = SubjectStudy::findOrFail($id);
        $this->subjectStudyId = $subjectStudy->id;
        $this->namaMataPelajaran = $subjectStudy->name_subject;
        $this->deskripsi = $subjectStudy->description;
        $this->statusAktif = $subjectStudy->status_active;
        $this->modalEdit = true;
    }

    public function resetModal()
    {
        $this->reset([
            'modalCreate',
            'modalEdit',
            'namaMataPelajaran',
            'deskripsi',
            'statusAktif',
        ]);
    }

    // DELETE SELECTED
    public function deleteSelected()
    {
        $subjectStudy = SubjectStudy::whereIn('id', $this->selected)->get();
        $deleteCount = $subjectStudy->count();

        foreach ($subjectStudy as $data) {
            $data->delete();
        }

        $this->reset();

        session()->flash('alert', [
            'type' => 'success',
            'message' => 'Berhasil!',
            'detail' => "Berhasil menghapus $deleteCount data mata pelajaran.",
        ]);

        return redirect()->back();
    }

    // GET DATA
    #[On('muat-ulang')]
    #[Computed()]
    public function rows()
    {
        $query = SubjectStudy::query()
            ->when(!$this->sorts, fn($query) => $query->first())
            ->when($this->filters['search'], function ($query, $search) {
                $query->where('name_subject', 'LIKE', "%$search%");
            })->latest();

        return $this->applyPagination($query);
    }

    // SAVE DATA
    public function save()
    {
        $this->validate([
            'namaMataPelajaran' => ['required', 'string', 'min:2', 'max:255'],
            'deskripsi' => ['nullable', 'string', 'min:2'],
            'statusAktif' => ['boolean'],
        ]);

        try {
            DB::beginTransaction();

            if ($this->subjectStudyId) {
                $subjectStudy = SubjectStudy::findOrFail($this->subjectStudyId);

                $subjectStudy->update([
                    'name_subject' => $this->namaMataPelajaran,
                    'description' => $this->deskripsi,
                    'status_active' => $this->statusAktif,
                ]);
            } else {
                SubjectStudy::create([
                    'name_subject' => $this->namaMataPelajaran,
                    'description' => $this->deskripsi,
                    'status_active' => $this->statusAktif,
                ]);
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            logger()->error(
                '[pengguna] ' .
                    auth()->user()->username .
                    ' gagal menambahkan mata pelajaran',
                [$e->getMessage()]
            );


            if ($this->subjectStudyId) {
                $message = "Gagal menyunting data mata pelajaran.";
            } else {
                $message = "Gagal menambahkan data mata pelajaran.";
            }

            session()->flash('alert', [
                'type' => 'danger',
                'message' => 'Gagal!',
                'detail' => $message,
            ]);

            return redirect()->back();
        }

        if ($this->subjectStudyId) {
            $message = "Berhasil menyunting data mata pelajaran.";
        } else {
            $message = "Berhasil menambahkan data mata pelajaran.";
        }

        session()->flash('alert', [
            'type' => 'success',
            'message' => 'Berhasil!',
            'detail' => $message,
        ]);

        return redirect()->route('master.subject-study.index');
    }

    #[Computed()]
    public function allData()
    {
        return SubjectStudy::all();
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
        return view('livewire.master.subject-study.index');
    }
}
