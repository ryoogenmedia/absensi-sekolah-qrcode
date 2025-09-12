<?php

namespace App\Livewire\PersenceClassRoom;

use App\Livewire\Traits\DataTable\WithBulkActions;
use App\Livewire\Traits\DataTable\WithCachedRows;
use App\Livewire\Traits\DataTable\WithPerPagePagination;
use App\Livewire\Traits\DataTable\WithSorting;
use App\Models\ClassAttendance;
use App\Models\ClassSchedule;
use App\Models\Student;
use App\Models\StudentAttendance;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Index extends Component
{
    use WithBulkActions;
    use WithPerPagePagination;
    use WithCachedRows;
    use WithSorting;

    public $classScheduleId;
    public $studentId;
    public $classSchedule;
    public $classRoomId;
    public $totalMeeting = 0;
    public $totalPresence = 0;

    // custom method
    public function getTotalMeeting(){
        $this->totalMeeting = ClassAttendance::where('class_schedule_id', $this->classScheduleId)
            ->count();
    }

    public function getTotalPresence(){
        $this->totalPresence = StudentAttendance::whereHas('class_attendance', function($query) {
            $query->where('class_schedule_id', $this->classScheduleId);
        })
        ->where('student_id', $this->studentId)
        ->where('status_attendance', 'hadir')
        ->count();
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

    // lifecyle hooks
    public function updatedClassScheduleId($value){
        $this->classSchedule = ClassSchedule::find($value);
        $this->getTotalMeeting();
        $this->getTotalPresence();
    }

    public function updatedFilters()
    {
        $this->resetPage();
    }

    public function mount(){
        $user = auth()->user();
        $this->studentId = $user->student->id ?? null;
        $this->classRoomId = $user->student->class_room_id ?? null;
    }

    // computed properties
    #[Computed()]
    public function class_schedules(){
        return auth()->user()->student->class_room->class_schedules ?? null;
    }

    #[Computed()]
    public function students(){
        return Student::where('id', $this->studentId)->get();
    }

    #[Computed()]
    public function allData()
    {
        return ClassAttendance::all();
    }

    public function render()
    {
        return view('livewire.persence-class-room.index');
    }
}
