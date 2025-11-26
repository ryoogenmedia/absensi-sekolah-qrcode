<?php

namespace App\Livewire\Master\ClassSchedule;

use App\Exports\ClassScheduleExport;
use App\Imports\ClassScheduleImport;
use App\Livewire\Traits\DataTable\WithBulkActions;
use App\Livewire\Traits\DataTable\WithCachedRows;
use App\Livewire\Traits\DataTable\WithPerPagePagination;
use App\Livewire\Traits\DataTable\WithSorting;
use App\Models\ClassRoom;
use App\Models\ClassSchedule;
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
    use WithFileUploads;
    use WithBulkActions;
    use WithPerPagePagination;
    use WithCachedRows;
    use WithSorting;

    public $filters = [
        'search' => '',
        'class_room' => '',
        'subject_study' => '',
        'start_time' => '',
        'end_time' => '',
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
            return Excel::download(new ClassScheduleExport, 'jadwal-kelas.xlsx');
        } catch (Exception $e) {

            logger()->error(
                '[export excel class schedule] ' .
                    auth()->user()->username .
                    ' gagal export data jadwal kelas',
                [$e->getMessage()]
            );

            session()->flash('alert', [
                'type' => 'danger',
                'message' => 'Gagal!',
                'detail' => 'Export data jadwal kelas gagal dilakukan.',
            ]);

            $this->resetForm();
            return redirect()->back();
        }

        session()->flash('alert', [
            'type' => 'success',
            'message' => 'Berhasil!',
            'detail' => 'Export data jadwal kelas berhasil dilakukan.',
        ]);

        $this->resetForm();
        return redirect()->back();
    }

    public function importExcel()
    {
        try {
            DB::beginTransaction();

            Excel::queueImport(new ClassScheduleImport, $this->fileExcel);

            DB::commit();
        } catch (Exception $e) {
            DB::beginTransaction();

            logger()->error(
                '[import excel class schedule] ' .
                    auth()->user()->username .
                    ' gagal import data jadwal kelas',
                [$e->getMessage()]
            );

            session()->flash('alert', [
                'type' => 'danger',
                'message' => 'Gagal.',
                'detail' => "import data jadwal kelas gagal dilakukan.",
            ]);

            $this->resetForm();
            return redirect()->back();
        }

        session()->flash('alert', [
            'type' => 'success',
            'message' => 'Berhasil.',
            'detail' => "import data jadwal kelas berhasil dilakukan.",
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

    #[Computed()]
    public function class_rooms()
    {
        return ClassRoom::all(['id', 'name_class']);
    }

    #[Computed()]
    public function subject_studies()
    {
        return SubjectStudy::all(['id', 'name_subject']);
    }

    public function deleteSelected()
    {
        $schedules = ClassSchedule::whereIn('id', $this->selected)->get();
        $deleteCount = $schedules->count();

        foreach ($schedules as $data) {
            $data->delete();
        }

        $this->reset();

        session()->flash('alert', [
            'type' => 'success',
            'message' => 'Berhasil!',
            'detail' => "Berhasil menghapus $deleteCount data jadwal kelas.",
        ]);

        return redirect()->back();
    }

    #[On('muat-ulang')]
    #[Computed()]
    public function rows()
    {
        $query = ClassSchedule::query()
            ->when(!$this->sorts, function ($query) {
                $query->join('class_rooms', 'class_schedules.class_room_id', '=', 'class_rooms.id')
                    ->orderBy('class_rooms.name_class')
                    ->select('class_schedules.*');
            })
            ->when($this->filters['search'], function ($query, $search) {
                $query->whereHas('class_room', function ($query) use ($search) {
                    $query->where('name_class', 'LIKE', "%$search%");
                })->orWhereHas('teacher', function ($query) use ($search) {
                    $query->where('name', 'LIKE', "$search");
                })->orWhereHas('subject_study', function ($query) use ($search) {
                    $query->where('name_subject', 'LIKE', "$search");
                });
            })
            ->when($this->filters['class_room'], function ($query, $classId) {
                $query->where('class_room_id', $classId);
            })
            ->when($this->filters['subject_study'], function ($query, $subjectStudyId) {
                $query->where('subject_study_id', $subjectStudyId);
            })
            ->when($this->filters['start_time'], function ($query, $startTime) {
                $query->whereTime('start_time', '>=', $startTime);
            })
            ->when($this->filters['end_time'], function ($query, $endTime) {
                $query->whereTime('end_time', '<=', $endTime);
            });

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
        $this->reset();
    }

    public function render()
    {
        return view('livewire.master.class-schedule.index');
    }
}
