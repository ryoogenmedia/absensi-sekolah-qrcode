<?php

namespace App\Livewire\ScanQr;

use App\Models\CheckInRecord;
use App\Models\CheckOutRecord;
use App\Models\Student;
use App\Helpers\WhatsappBroadcast;
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

        // Send WhatsApp Notification
        $this->sendWhatsappNotification($student, 'MASUK');

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

        // Send WhatsApp Notification
        $this->sendWhatsappNotification($student, 'KELUAR');

        session()->flash('alert', [
            'type' => 'success',
            'message' => 'Berhasil',
            'detail' => "Anda telah terdaftar sebagai keluar.",
        ]);

        $this->dispatch('reload-check-out');

        return redirect()->route('scan-qr.index');
    }

    private function sendWhatsappNotification($student, $type)
    {
        try {
            $student->load('guardian');
            $guardian = $student->guardian;
            if (!$guardian || !$guardian->whatsapp_number || !$guardian->is_wa_active) {
                return;
            }

            $time = now()->format('H:i');
            $date = now()->translatedFormat('d F Y');
            $phoneNumber = format_number_indonesia($guardian->whatsapp_number);

            $message = "📢 *NOTIFIKASI PRESENSI {$type}*\n\n"
                     . "Halo Bapak/Ibu Wali dari *{$student->full_name}*,\n\n"
                     . "Menginfokan bahwa putra/putri Anda telah melakukan presensi *{$type}*:\n"
                     . "⏰ *Waktu:* {$time} WIB\n"
                     . "📅 *Tanggal:* {$date}\n\n"
                     . "Terima kasih.";

            $whatsapp = new WhatsappBroadcast();
            $whatsapp->sendText($phoneNumber, $message);
        } catch (\Exception $e) {
            logger()->error("Daily Attendance WA Error: " . $e->getMessage());
        }
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
