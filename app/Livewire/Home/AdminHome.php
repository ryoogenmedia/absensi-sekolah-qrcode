<?php

namespace App\Livewire\Home;

use App\Helpers\HomeChart;
use App\Models\CheckInRecord;
use App\Models\CheckOutRecord;
use App\Models\ClassRoom;
use App\Models\ClassSchedule;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\SubjectStudy;
use App\Models\Teacher;
use App\Models\User;
use Livewire\Component;

class AdminHome extends Component
{
    public $totalStudent = 0;
    public $totalTeacher = 0;
    public $totalAdmin = 0;
    public $totalJadwalKelas = 0;
    public $totalMataPelajaran = 0;
    public $totalKelas = 0;

    public $period = 'daily';

    public $attendanceHadir;
    public $attendanceAlpa;
    public $attendanceIzin;
    public $attendanceSakit;

    public $checkInToday = 0;
    public $checkOutToday = 0;

    public function getDataCount()
    {
        $this->totalStudent = Student::count();
        $this->totalTeacher = Teacher::count();
        $this->totalJadwalKelas = ClassSchedule::count();
        $this->totalMataPelajaran = SubjectStudy::count();
        $this->totalKelas = ClassRoom::count();
        $this->totalAdmin = User::where('role', 'admin')->count();
    }

    public function getDataChart()
    {
        $this->attendanceHadir = HomeChart::CHART_DATA(StudentAttendance::query()->where('status_attendance', 'hadir'), $this->period);

        $this->attendanceAlpa = HomeChart::CHART_DATA(StudentAttendance::query()->where('status_attendance', 'alpa'), $this->period);

        $this->attendanceIzin = HomeChart::CHART_DATA(StudentAttendance::query()->where('status_attendance', 'izin'), $this->period);

        $this->attendanceSakit = HomeChart::CHART_DATA(StudentAttendance::query()->where('status_attendance', 'sakit'), $this->period);
    }

    public function mount()
    {
        $this->getDataCount();
        $this->getDataChart();

        $this->checkInToday = CheckInRecord::whereDate('attendance_date', now()->toDateString())
            ->count();

        $this->checkOutToday = CheckOutRecord::whereDate('attendance_date', now()->toDateString())
            ->count();
    }

    public function updatedPeriod()
    {
        $this->getDataChart();

        $date = $this->attendanceHadir['date'];
        $attendanceHadir = $this->attendanceHadir['data'];
        $attendanceAlpa = $this->attendanceAlpa['data'];
        $attendanceIzin = $this->attendanceIzin['data'];
        $attendanceSakit = $this->attendanceSakit['data'];

        $this->dispatch('updateChart', [
            'hadir' => $attendanceHadir,
            'alpa' => $attendanceAlpa,
            'izin' => $attendanceIzin,
            'sakit' => $attendanceSakit,
            'date' => $date,
        ]);
    }

    public function render()
    {
        return view('livewire.home.admin-home');
    }
}
