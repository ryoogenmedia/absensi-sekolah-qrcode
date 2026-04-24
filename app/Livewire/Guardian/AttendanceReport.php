<?php

namespace App\Livewire\Guardian;

use App\Models\CheckInRecord;
use App\Models\CheckOutRecord;
use App\Models\StudentAttendance;
use App\Models\ClassAttendance;
use Livewire\Component;

class AttendanceReport extends Component
{
    public $student;
    public $dailyAttendance = [];
    public $classAttendance = [];
    public $month;
    public $year;

    public function mount()
    {
        $this->student = auth()->user()->student_guardian->student;
        $this->month = now()->month;
        $this->year = now()->year;
        $this->loadAttendance();
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['month', 'year'])) {
            $this->loadAttendance();
        }
    }

    public function loadAttendance()
    {
        if (!$this->student) return;

        // Load daily check-in/out
        $checkIns = CheckInRecord::where('student_id', $this->student->id)
            ->whereYear('attendance_date', $this->year)
            ->whereMonth('attendance_date', $this->month)
            ->get();

        $checkOuts = CheckOutRecord::where('student_id', $this->student->id)
            ->whereYear('attendance_date', $this->year)
            ->whereMonth('attendance_date', $this->month)
            ->get();

        $this->dailyAttendance = [];
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $this->month, $this->year);

        for ($i = 1; $i <= $daysInMonth; $i++) {
            $date = sprintf('%04d-%02d-%02d', $this->year, $this->month, $i);
            $this->dailyAttendance[$date] = [
                'check_in' => $checkIns->firstWhere('attendance_date', $date),
                'check_out' => $checkOuts->firstWhere('attendance_date', $date),
            ];
        }

        // Load per-class attendance
        $this->classAttendance = StudentAttendance::with(['class_attendance.class_schedule.subject_study'])
            ->where('student_id', $this->student->id)
            ->whereHas('class_attendance', function ($q) {
                $q->whereYear('created_at', $this->year)
                  ->whereMonth('created_at', $this->month);
            })
            ->get()
            ->groupBy(function($item) {
                return $item->class_attendance->created_at->format('Y-m-d');
            });
    }

    public function render()
    {
        return view('livewire.guardian.attendance-report', [
            'months' => [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ],
            'years' => range(now()->year, now()->year - 5)
        ]);
    }
}
