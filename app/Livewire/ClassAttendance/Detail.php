<?php

namespace App\Livewire\ClassAttendance;

use App\Livewire\Traits\DataTable\WithBulkActions;
use App\Livewire\Traits\DataTable\WithCachedRows;
use App\Livewire\Traits\DataTable\WithPerPagePagination;
use App\Livewire\Traits\DataTable\WithSorting;
use App\Models\ClassAttendance;
use App\Models\ClassSchedule;
use App\Models\Student;
use Illuminate\Support\Facades\File;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Detail extends Component
{
    use WithBulkActions;
    use WithPerPagePagination;
    use WithCachedRows;
    use WithSorting;

    public $filters = [
        'search' => '',
        'search_student' => '',
    ];

    public $classScheduleId;
    public $classSchedule;
    public $totalPresence = 0;
    public $show = false;

    public $pictureEvidence;

    public function deleteSelected()
    {
        $classAttendances = ClassAttendance::whereIn('id', $this->selected)->get();
        $deleteCount = $classAttendances->count();

        foreach ($classAttendances as $data) {
            if ($data->picture_evidence) {
                File::delete(public_path('storage/' . $data->picture_evidence));
            }
            $data->delete();
        }

        session()->flash('alert', [
            'type' => 'success',
            'message' => 'Berhasil!',
            'detail' => "Berhasil menghapus $deleteCount data presensi kelas.",
        ]);

        return redirect()->back();
    }

    public function openModal($id){
        $classAttendance = ClassAttendance::findOrFail($id);
        $this->pictureEvidence = $classAttendance->pictureEvidenceUrl();
        $this->show = true;
    }

    public function closeModal(){
        $this->show = false;
        $this->pictureEvidence = null;
    }

    public function getTotalPresence(){
        $this->totalPresence = ClassAttendance::where('class_schedule_id', $this->classScheduleId)
            ->count();
    }

    #[Computed()]
    public function class_attendances(){
        $query = ClassAttendance::query()
            ->when($this->filters['search'], function ($query, $search) {
                $query->where('name_material', 'LIKE', "%$search%");
            })->where('class_schedule_id', $this->classScheduleId);

        return $this->applyPagination($query);
    }

    #[Computed()]
    public function students(){
        $query = Student::query()
            ->when($this->filters['search_student'], function($query, $search){
                $query->where('full_name', 'LIKE', "%$search%")
                    ->orWhere('nis', 'LIKE', "%$search%");
            })->whereHas('student_attendances', function($query){
                $query->whereHas('class_attendance', function($query){
                    $query->where('class_schedule_id', $this->classScheduleId);
                });
            });

        return $query->get();
    }

    #[Computed()]
    public function allData()
    {
        return ClassAttendance::all();
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

    public function mount($id){
        $this->classSchedule = ClassSchedule::findOrFail($id);
        $this->classScheduleId = $this->classSchedule->id;
        $this->getTotalPresence();
    }

    public function render()
    {
        return view('livewire.class-attendance.detail');
    }
}
