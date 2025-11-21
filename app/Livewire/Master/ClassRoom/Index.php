<?php

namespace App\Livewire\Master\ClassRoom;

use App\Exports\ClassRoomExport;
use App\Imports\ClassRoomImport;
use App\Livewire\Traits\DataTable\WithBulkActions;
use App\Livewire\Traits\DataTable\WithCachedRows;
use App\Livewire\Traits\DataTable\WithPerPagePagination;
use App\Livewire\Traits\DataTable\WithSorting;
use App\Models\ClassRoom;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
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
    public $namaKelas;
    public $deskripsi;
    public $statusAktif = true;

    // MODAL INITIALIZATION
    public $modalCreate = false;
    public $modalEdit = false;

    // IDENTITY
    public $classRoomId;

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
            return Excel::download(new ClassRoomExport, 'data-kelas.xlsx');
        } catch (Exception $e) {

            logger()->error(
                '[export excel student] ' .
                    auth()->user()->username .
                    ' gagal export data kelas',
                [$e->getMessage()]
            );

            session()->flash('alert', [
                'type' => 'danger',
                'message' => 'Gagal!',
                'detail' => 'Export data kelas gagal dilakukan.',
            ]);

            $this->resetForm();
            return redirect()->back();
        }

        session()->flash('alert', [
            'type' => 'success',
            'message' => 'Berhasil!',
            'detail' => 'Export data kelas berhasil dilakukan.',
        ]);

        $this->resetForm();
        return redirect()->back();
    }


    public function importExcel()
    {
        try {
            DB::beginTransaction();

            Excel::queueImport(new ClassRoomImport, $this->fileExcel);

            DB::commit();
        } catch (Exception $e) {
            DB::beginTransaction();

            logger()->error(
                '[import excel class room] ' .
                    auth()->user()->username .
                    ' gagal import data kelas',
                [$e->getMessage()]
            );

            session()->flash('alert', [
                'type' => 'danger',
                'message' => 'Gagal.',
                'detail' => "import data kelas gagal dilakukan.",
            ]);

            $this->resetForm();
            return redirect()->back();
        }

        session()->flash('alert', [
            'type' => 'success',
            'message' => 'Berhasil.',
            'detail' => "import data kelas berhasil dilakukan.",
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
        $classRoom = ClassRoom::findOrFail($id);
        $classRoom->status_active = !$classRoom->status_active;
        $classRoom->save();

        session()->flash('alert', [
            'type' => 'success',
            'message' => 'Berhasil!',
            'detail' => "Berhasil mengubah status aktif ruang kelas.",
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
        $classRoom = ClassRoom::findOrFail($id);
        $this->classRoomId = $classRoom->id;
        $this->namaKelas = $classRoom->name_class;
        $this->deskripsi = $classRoom->description;
        $this->statusAktif = $classRoom->status_active;
        $this->modalEdit = true;
    }

    public function resetModal()
    {
        $this->reset([
            'modalCreate',
            'modalEdit',
            'namaKelas',
            'deskripsi',
            'statusAktif',
        ]);
    }

    // DELETE SELECTED
    public function deleteSelected()
    {
        $classRoom = ClassRoom::whereIn('id', $this->selected)->get();
        $deleteCount = $classRoom->count();

        foreach ($classRoom as $data) {
            $data->delete();
        }

        $this->reset();

        session()->flash('alert', [
            'type' => 'success',
            'message' => 'Berhasil!',
            'detail' => "Berhasil menghapus $deleteCount data ruang kelas.",
        ]);

        return redirect()->back();
    }

    // GET DATA
    #[On('muat-ulang')]
    #[Computed()]
    public function rows()
    {
        $query = ClassRoom::query()
            ->when(!$this->sorts, fn($query) => $query->first())
            ->when($this->filters['search'], function ($query, $search) {
                $query->where('name_class', 'LIKE', "%$search%");
            })->latest();

        return $this->applyPagination($query);
    }

    // SAVE DATA
    public function save()
    {
        $this->validate([
            'namaKelas' => ['required', 'string', 'min:2', 'max:255'],
            'deskripsi' => ['nullable', 'string', 'min:2'],
            'statusAktif' => ['boolean'],
        ]);

        try {
            DB::beginTransaction();

            if ($this->classRoomId) {
                $classRoom = ClassRoom::findOrFail($this->classRoomId);

                $classRoom->update([
                    'name_class' => $this->namaKelas,
                    'description' => $this->deskripsi,
                    'status_active' => $this->statusAktif,
                ]);
            } else {
                ClassRoom::create([
                    'name_class' => $this->namaKelas,
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
                    ' gagal menambahkan ruang kelas',
                [$e->getMessage()]
            );


            if ($this->classRoomId) {
                $message = "Gagal menyunting data ruang kelas.";
            } else {
                $message = "Gagal menambahkan data ruang kelas.";
            }

            session()->flash('alert', [
                'type' => 'danger',
                'message' => 'Gagal!',
                'detail' => $message,
            ]);

            return redirect()->back();
        }

        if ($this->classRoomId) {
            $message = "Berhasil menyunting data ruang kelas.";
        } else {
            $message = "Berhasil menambahkan data ruang kelas.";
        }

        session()->flash('alert', [
            'type' => 'success',
            'message' => 'Berhasil!',
            'detail' => $message,
        ]);

        return redirect()->route('master.classroom.index');
    }

    #[Computed()]
    public function allData()
    {
        return ClassRoom::all();
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
        return view('livewire.master.class-room.index');
    }
}
