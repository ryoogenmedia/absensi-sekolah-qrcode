<?php

namespace App\Livewire\ClassAttendance;

use App\Models\ClassAttendance;
use App\Models\ClassSchedule;
use App\Models\Student;
use App\Models\StudentAttendance;
use Exception;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class Create extends Component
{
    use WithFileUploads;

    public $classScheduleId;
    public $classRoomId;

    public $namaMateri;
    public $buktiPresensi;
    public $penjelasanMateri;
    public $presensiSiswa = [];
    public $isScannerOpen = false;

    public function rules(){
        return [
            'presensiSiswa.*.nama' => ['required'],
            'presensiSiswa.*.nis' => ['required'],
            'presensiSiswa.*.status_kehadiran' => ['required'],
            'namaMateri' => ['required','min:2','max:255'],
            'penjelasanMateri' => ['nullable','string','min:2','max:255'],
            'buktiPresensi' => ['nullable','image'],
        ];
    }

    #[On('scanned')]
    public function scanQr($code)
    {
        $studentFound = false;
        foreach ($this->presensiSiswa as $studentId => $data) {
            if ($data['nis'] == $code) {
                $this->presensiSiswa[$studentId]['status_kehadiran'] = 'hadir';
                $studentFound = true;
                break;
            }
        }

        if ($studentFound) {
            $this->dispatch('alert', [
                'type' => 'success',
                'message' => 'Berhasil!',
                'detail' => "Siswa dengan NIS $code berhasil di-scan.",
            ]);
        } else {
            $this->dispatch('alert', [
                'type' => 'warning',
                'message' => 'Tidak Ditemukan!',
                'detail' => "Siswa dengan NIS $code tidak ada dalam daftar kelas ini.",
            ]);
        }
    }

    public function toggleScanner()
    {
        $this->isScannerOpen = !$this->isScannerOpen;
    }

    public function save(){
        $this->validate();

        try{
            DB::beginTransaction();

            $classAttendance = ClassAttendance::create([
                'class_room_id' => $this->classRoomId,
                'class_schedule_id' => $this->classScheduleId,
                'explanation_material' => $this->penjelasanMateri,
                'name_material' => $this->namaMateri,
            ]);

            if($this->buktiPresensi){
                $classAttendance->update([
                    'picture_evidence' => $this->buktiPresensi->store('bukti-presensi', 'public'),
                ]);
            }

            foreach($this->presensiSiswa as $studentId => $presensi){
                StudentAttendance::updateOrCreate(
                    [
                        'class_attendance_id' => $classAttendance->id,
                        'student_id' => $studentId,
                    ],
                    [
                        'status_attendance' => $presensi['status_kehadiran'],
                    ]
                );
            }

            DB::commit();
        }catch(Exception $e){
            DB::rollBack();

            logger()->error(
                '[class attendance] ' .
                    auth()->user()->username .
                    ' gagal menambahkan presensi pertemuan',
                [$e->getMessage()]
            );

            session()->flash('alert', [
                'type' => 'danger',
                'message' => 'Gagal!',
                'detail' => "Gagal menambahkan data presensi pertemuan.",
            ]);

            return redirect()->back();
        }

        session()->flash('alert', [
            'type' => 'success',
            'message' => 'Berhasil!',
            'detail' => "Berhasil menambahkan data presensi pertemuan.",
        ]);

        return redirect()->route('class-attendance.detail', $this->classScheduleId);
    }

    public function mount($id){
        $classSchedule = ClassSchedule::findOrFail($id);
        $this->classScheduleId = $classSchedule->id;
        $this->classRoomId = $classSchedule->class_room->id;

        $students = Student::query()
            ->where('class_room_id', $this->classRoomId)
            ->get(['id','full_name','nis']);

        foreach ($students as $student) {
            $this->presensiSiswa[$student->id] = [
                'nama' => $student->full_name,
                'nis' => $student->nis,
                'status_kehadiran' => 'alpa', // default changed to alpa for scanning flow
            ];
        }
    }

    public function render()
    {
        return view('livewire.class-attendance.create');
    }
}
