<?php

namespace App\Livewire\Home;

use App\Models\ClassSchedule;
use App\Models\Student;
use Livewire\Attributes\Computed;
use Livewire\Component;

class StudentGuardianHome extends Component
{
    public $totalHadir = 0;
    public $totalAlpa = 0;
    public $totalIzin = 0;
    public $totalSakit = 0;
    public $totalPercentance = 0;

    public $percentHadir;
    public $percentAlpa;
    public $percentIzin;
    public $percentSakit;

    public $studentId;
    public $classScheduleId;

    public $studentPhoto;
    public $studentClassRoom;
    public $studentContact;
    public $studentNis;
    public $studentName;
    public $studentReligion;

    public $checkInRecords;
    public $checkOutRecords;

    public $qrType = 'in';

    // custom method
    public function studentInformation()
    {
        $student = Student::findOrFail($this->studentId);

        $this->studentPhoto = $student->photoUrl();
        $this->studentNis = $student->nis;
        $this->studentClassRoom = $student->class_room->name_class;
        $this->studentName = $student->full_name;
        $this->studentContact = $student->phone;
        $this->studentReligion = $student->religion;
    }


    #[Computed()]
    public function student_attendances()
    {
        return $this->getStudentAttendance()->limit(10)->latest()->get();
    }

    public function getStudentAttendance()
    {
        $user = auth()->user();
        $student = $user->student_guardian->student;

        return $student->student_attendances()
            ->when($this->classScheduleId, function ($query) {
                $query->whereHas('class_attendance.class_schedule', function ($q) {
                    $q->where('id', $this->classScheduleId);
                });
            });
    }

    public function getDataChart()
    {
        $statuses = ['hadir', 'alpa', 'izin', 'sakit'];

        $total = $this->totalPercentance;

        foreach ($statuses as $status) {
            $property = 'percent' . ucfirst($status);
            $totalProperty = 'total' . ucfirst($status);

            if ($total > 0) {
                $this->$property = round(($this->$totalProperty / $total) * 100, 2);
            } else {
                $this->$property = 0;
            }
        }
    }

    public function getDataCount()
    {
        $studentAttendance = $this->getStudentAttendance();

        $this->totalPercentance = $studentAttendance
            ->where('student_id', $this->studentId)->count();

        $statusAttendance = config('const.attendance_status');

        foreach ($statusAttendance as $status) {
            $count = (clone $studentAttendance) // clone for query reuse if loop
                ->where('student_id', $this->studentId)
                ->where('status_attendance', $status)
                ->count();

            switch ($status) {
                case 'hadir':
                    $this->totalHadir = $count;
                    break;
                case 'alpa':
                    $this->totalAlpa = $count;
                    break;
                case 'izin':
                    $this->totalIzin = $count;
                    break;
                case 'sakit':
                    $this->totalSakit = $count;
                    break;
            }
        }
    }

    // lifecycle hooks
    public function updatedClassScheduleId()
    {
        $this->getDataCount();
        $this->getDataChart();

        $this->dispatch('updateChartStudentPercent', [
            'hadir' => $this->percentHadir,
            'alpa' => $this->percentAlpa,
            'izin' => $this->percentIzin,
            'sakit' => $this->percentSakit,
            'total' => $this->totalPercentance,
        ]);
    }

    public function getDataScanQrCode()
    {
        $this->checkInRecords = Student::find($this->studentId)
            ->check_in_records()
            ->latest()
            ->limit(10)
            ->get();

        $this->checkOutRecords = Student::find($this->studentId)
            ->check_out_records()
            ->latest()
            ->limit(10)
            ->get();
    }

    public function mount()
    {
        $user = auth()->user();

        $student = Student::findOrFail($user->student_guardian->student->id);
        $this->studentId = $student->id;
        $this->getDataCount();
        $this->getDataChart();
        $this->studentInformation();
        $this->getDataScanQrCode();
    }

    // computed properties
    #[Computed()]
    public function class_schedules()
    {
        return ClassSchedule::whereHas('class_room.students', function ($query) {
            $query->where('id', $this->studentId);
        })->get();
    }

    public function render()
    {
        return view('livewire.home.student-guardian-home');
    }
}
