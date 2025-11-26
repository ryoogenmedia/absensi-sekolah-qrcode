<?php

namespace App\Livewire\ScanQr;

use App\Models\CheckInRecord;
use App\Models\CheckOutRecord;
use App\Models\Student;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\On;
use Livewire\Component;

class Index extends Component
{
    public $presensiType = 'check-in';

    public function updatedPresensiType($value)
    {
        Cache::delete('attendance-type');
        Cache::add('attendance-type', $value);
    }

    #[On('scanned')]
    public function scanQr($code)
    {
        $student = Student::where('nis', $code)->first();

        if ($student) {
            if ($this->presensiType == 'check-in') {
                $this->checkInRecord($student);
            }

            if ($this->presensiType == 'check-out') {
                $this->checkOutRecord($student);
            }
        } else {
            session()->flash('alert', [
                'type' => 'warning',
                'message' => 'Tidak terdaftar',
                'detail' => "Qr Code tidak terdaftar.",
            ]);

            return redirect()->route('scan-qr.index');
        }
    }

    public function checkInRecord($student)
    {
        $today = now()->toDateString();

        $alreadyCheckedIn = CheckInRecord::where('student_id', $student->id)
            ->whereDate('attendance_date', $today)
            ->exists();

        if ($alreadyCheckedIn) {
            session()->flash('alert', [
                'type' => 'info',
                'message' => 'Sudah Scan!',
                'detail' => "Anda telah scan sebagai masuk hari ini.",
            ]);

            return redirect()->route('scan-qr.index');
        }

        CheckInRecord::create([
            'student_id' => $student->id,
            'check_in_time' => now()->format('H:i:s'),
            'attendance_date' => $today,
        ]);

        session()->flash('alert', [
            'type' => 'success',
            'message' => 'Berhasil',
            'detail' => "Anda telah terdaftar sebagai masuk.",
        ]);

        $this->dispatch('reload-check-in');

        return redirect()->route('scan-qr.index');
    }

    public function checkOutRecord($student)
    {
        $today = now()->toDateString();

        $alreadyCheckedIn = CheckOutRecord::where('student_id', $student->id)
            ->whereDate('attendance_date', $today)
            ->exists();

        if ($alreadyCheckedIn) {
            session()->flash('alert', [
                'type' => 'info',
                'message' => 'Sudah Scan!',
                'detail' => "Anda telah scan sebagai keluar untuk hari ini.",
            ]);

            return redirect()->route('scan-qr.index');
        }

        CheckOutRecord::create([
            'student_id' => $student->id,
            'check_out_time' => now()->format('H:i:s'),
            'attendance_date' => $today,
        ]);

        session()->flash('alert', [
            'type' => 'success',
            'message' => 'Berhasil',
            'detail' => "Anda telah terdaftar sebagai keluar.",
        ]);

        $this->dispatch('reload-check-out');

        return redirect()->route('scan-qr.index');
    }

    public function mount()
    {
        $cache = Cache::get('attendance-type');

        if ($cache) {
            $this->presensiType = $cache;
        }
    }

    public function render()
    {
        return view('livewire.scan-qr.index');
    }
}
