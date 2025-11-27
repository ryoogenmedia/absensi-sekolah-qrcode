<?php

namespace App\Livewire\StudentGuardian;

use App\Exports\StudentGuardianExport;
use App\Imports\StudentGuardianImport;
use App\Livewire\Traits\DataTable\WithBulkActions;
use App\Livewire\Traits\DataTable\WithCachedRows;
use App\Livewire\Traits\DataTable\WithPerPagePagination;
use App\Livewire\Traits\DataTable\WithSorting;
use App\Models\StudentGuardian;
use Exception;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

class Index extends Component
{
    use WithBulkActions;
    use WithPerPagePagination;
    use WithCachedRows;
    use WithSorting;

    public $filters = [
        'search' => '',
        'nis_student' => '',
        'student_name' => '',
    ];

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
            return Excel::download(new StudentGuardianExport, 'data-wali-siswa.xlsx');
        } catch (Exception $e) {

            logger()->error(
                '[export excel student] ' .
                    auth()->user()->username .
                    ' gagal export data wali siswa',
                [$e->getMessage()]
            );

            session()->flash('alert', [
                'type' => 'danger',
                'message' => 'Gagal!',
                'detail' => 'Export data wali siswa gagal dilakukan.',
            ]);

            $this->resetForm();
            return redirect()->back();
        }

        session()->flash('alert', [
            'type' => 'success',
            'message' => 'Berhasil!',
            'detail' => 'Export data wali siswa berhasil dilakukan.',
        ]);

        $this->resetForm();
        return redirect()->back();
    }

    public function importExcel()
    {
        try {
            DB::beginTransaction();

            Excel::queueImport(new StudentGuardianImport, $this->fileExcel);

            DB::commit();
        } catch (Exception $e) {
            DB::beginTransaction();

            logger()->error(
                '[import excel student guardian] ' .
                    auth()->user()->username .
                    ' gagal import data wali siswa',
                [$e->getMessage()]
            );

            session()->flash('alert', [
                'type' => 'danger',
                'message' => 'Gagal.',
                'detail' => "import data wali siswa gagal dilakukan.",
            ]);

            $this->resetForm();
            return redirect()->back();
        }

        session()->flash('alert', [
            'type' => 'success',
            'message' => 'Berhasil.',
            'detail' => "import data wali siswa berhasil dilakukan.",
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

    public function deleteSelected()
    {
        $studentGuardian = StudentGuardian::whereIn('id', $this->selected)->get();
        $deleteCount = $studentGuardian->count();

        foreach ($studentGuardian as $data) {
            $data->delete();
        }

        $this->reset();

        session()->flash('alert', [
            'type' => 'success',
            'message' => 'Berhasil!',
            'detail' => "Berhasil menghapus $deleteCount data wali siswa.",
        ]);

        return redirect()->back();
    }

    #[On('muat-ulang')]
    #[Computed()]
    public function rows()
    {
        $query = StudentGuardian::query()
            ->when(!$this->sorts, fn($query) => $query->first())
            ->when($this->filters['search'], function ($query, $search) {
                $query->where('guardian_name', 'LIKE', "%$search%")
                    ->orWhereHas('student', function ($query) use ($search) {
                        $query->where('full_name', 'LIKE', "%$search%")
                            ->orWhere('call_name', 'LIKE', "%$search%")
                            ->orWhere('nis', 'LIKE', "%$search%");
                    });
            })->latest();

        return $this->applyPagination($query);
    }

    #[Computed()]
    public function allData()
    {
        return StudentGuardian::all();
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
        return view('livewire.student-guardian.index');
    }
}
