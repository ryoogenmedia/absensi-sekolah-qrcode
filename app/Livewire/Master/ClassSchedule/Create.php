<?php

namespace App\Livewire\Master\ClassSchedule;

use App\Models\ClassRoom;
use App\Models\ClassSchedule;
use App\Models\SubjectStudy;
use App\Models\Teacher;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Create extends Component
{
    public $guru;
    public $kelas;
    public $mataPelajaran;
    public $hari;
    public $waktuMasuk;
    public $waktuKeluar;
    public $keterangan;
    public $sesi;

    // Property untuk menampilkan tabel di Blade
    public $previousSchedule = [];
    public $prevClassRoomSchedule = [];

    public function rules()
    {
        return [
            'guru' => ['required'],
            'kelas' => ['required'],
            'mataPelajaran' => ['required'],
            'hari' => ['required', 'string', Rule::in(config('const.name_days_secound'))],
            'sesi' => ['required', 'string'],
            'waktuMasuk' => ['required'],
            'waktuKeluar' => ['required', 'after:waktuMasuk'],
            'keterangan' => ['nullable', 'string'],
        ];
    }

    public function updatedSesi($value)
    {
        if ($value && array_key_exists($value, config('const.class_sessions'))) {
            $session = config('const.class_sessions')[$value];
            $this->waktuMasuk = $session['start'];
            $this->waktuKeluar = $session['end'];
        } else {
            $this->waktuMasuk = null;
            $this->waktuKeluar = null;
        }
    }

    // Mengambil jadwal yang sudah ada berdasarkan kelas yang dipilih
    public function updatedKelas($value)
    {
        if ($value) {
            $this->prevClassRoomSchedule = ClassSchedule::where('class_room_id', $value)->get();
        } else {
            $this->prevClassRoomSchedule = [];
        }
    }

    // Reset guru saat mapel berubah
    public function updatedMataPelajaran($value)
    {
        $this->guru = null;
    }

    public function save()
    {
        $this->validate();

        // 0. Pastikan Mapel Guru Sesuai
        $teacherObj = Teacher::find($this->guru);
        if (!$teacherObj || $teacherObj->subject_study_id != $this->mataPelajaran) {
            $this->addError('guru', 'Guru ini tidak mengajar mata pelajaran yang dipilih.');
            session()->flash('alert', [
                'type' => 'warning',
                'message' => 'Peringatan!',
                'detail' => 'Guru ini tidak mengajar mata pelajaran yang dipilih.',
            ]);
            return;
        }

        // 1. Cek Bentrok Kelas
        $classConflict = ClassSchedule::where('class_room_id', $this->kelas)
            ->where('day_name', $this->hari)
            ->where(function ($query) {
                $query->whereRaw("TIME(start_time) < TIME(?)", [$this->waktuKeluar])
                    ->whereRaw("TIME(end_time) > TIME(?)", [$this->waktuMasuk]);
            })->get();

        if ($classConflict->count() > 0) {
            $this->previousSchedule = $classConflict;

            $message = "Jadwal bentrok dengan mata pelajaran lain di kelas yang sama!";
            $this->addError('sesi', $message);
            $this->addError('waktuMasuk', $message);
            $this->addError('waktuKeluar', $message);

            session()->flash('alert', [
                'type' => 'warning',
                'message' => 'Peringatan!',
                'detail' => $message,
            ]);
            return;
        }

        // 2. Cek Bentrok Guru
        $teacherConflict = ClassSchedule::where('teacher_id', $this->guru)
            ->where('day_name', $this->hari)
            ->where(function ($query) {
                $query->whereRaw("TIME(start_time) < TIME(?)", [$this->waktuKeluar])
                    ->whereRaw("TIME(end_time) > TIME(?)", [$this->waktuMasuk]);
            })->get();

        if ($teacherConflict->count() > 0) {
            $this->previousSchedule = $teacherConflict;

            $message = "Jadwal bentrok! Guru tersebut sudah mengajar di kelas lain pada hari dan jam/sesi ini.";
            $this->addError('guru', $message);

            session()->flash('alert', [
                'type' => 'warning',
                'message' => 'Peringatan!',
                'detail' => $message,
            ]);
            return;
        }

        try {
            DB::beginTransaction();

            ClassSchedule::create([
                'class_room_id' => $this->kelas,
                'teacher_id' => $this->guru,
                'subject_study_id' => $this->mataPelajaran,
                'day_name' => $this->hari,
                'start_time' => $this->waktuMasuk,
                'end_time' => $this->waktuKeluar,
                'description' => $this->keterangan,
            ]);

            DB::commit();

            session()->flash('alert', [
                'type' => 'success',
                'message' => 'Berhasil!',
                'detail' => "Data jadwal berhasil disimpan.",
            ]);

            return redirect()->route('master.class-schedule.index');
        } catch (Exception $e) {
            DB::rollBack();
            logger()->error('[ClassSchedule] Create Error: ' . $e->getMessage());

            session()->flash('alert', [
                'type' => 'danger',
                'message' => 'Gagal!',
                'detail' => "Terjadi kesalahan sistem saat menyimpan data.",
            ]);
        }
    }

    #[Computed()]
    public function teachers()
    {
        if (!$this->mataPelajaran) {
            return [];
        }
        return Teacher::where('subject_study_id', $this->mataPelajaran)->get(['id', 'name', 'nip']);
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

    public function render()
    {
        return view('livewire.master.class-schedule.create');
    }
}
