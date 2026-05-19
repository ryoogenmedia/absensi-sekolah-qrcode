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

class Edit extends Component
{
    public $guru;
    public $kelas;
    public $mataPelajaran;
    public $hari;
    public $waktuMasuk;
    public $waktuKeluar;
    public $keterangan;
    public $sesi;

    // IDENTITY
    public $classScheduleId;

    // Property untuk menampilkan tabel di Blade
    public $previousSchedule = [];
    public $prevClassRoomSchedule = [];

    // Property untuk inline edit
    public $editingScheduleId;
    public $editingSchedule = [];

    public function mount($id)
    {
        $schedule = ClassSchedule::with(['subject_study', 'teacher', 'class_room'])->findOrFail($id);

        $this->classScheduleId = $schedule->id;
        $this->kelas = $schedule->class_room_id;
        $this->guru = $schedule->teacher_id;
        $this->mataPelajaran = $schedule->subject_study_id;
        $this->hari = $schedule->day_name;
        $this->waktuMasuk = $schedule->start_time;
        $this->waktuKeluar = $schedule->end_time;
        $this->keterangan = $schedule->description;

        // Prepopulate $sesi if matching
        $start = date('H:i', strtotime($schedule->start_time));
        $end = date('H:i', strtotime($schedule->end_time));
        foreach (config('const.class_sessions') as $key => $session) {
            if (date('H:i', strtotime($session['start'])) == $start && date('H:i', strtotime($session['end'])) == $end) {
                $this->sesi = $key;
                break;
            }
        }

        // Load daftar jadwal untuk kelas ini saat pertama load
        if ($this->kelas) {
            $this->prevClassRoomSchedule = ClassSchedule::where('class_room_id', $this->kelas)
                ->with(['subject_study', 'teacher'])
                ->get();
        }
    }

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

    public function updatedEditingSchedule($value, $key)
    {
        if ($key === 'sesi') {
            if ($value && array_key_exists($value, config('const.class_sessions'))) {
                $session = config('const.class_sessions')[$value];
                $this->editingSchedule['waktuMasuk'] = $session['start'];
                $this->editingSchedule['waktuKeluar'] = $session['end'];
            } else {
                $this->editingSchedule['waktuMasuk'] = null;
                $this->editingSchedule['waktuKeluar'] = null;
            }
        }
    }

    public function edit()
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
            ->where('id', '!=', $this->classScheduleId)
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
            ->where('id', '!=', $this->classScheduleId)
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

            $classSchedule = ClassSchedule::findOrFail($this->classScheduleId);

            $classSchedule->update([
                'class_room_id' => $this->kelas,
                'teacher_id' => $this->guru,
                'subject_study_id' => $this->mataPelajaran,
                'day_name' => $this->hari,
                'start_time' => $this->waktuMasuk,
                'end_time' => $this->waktuKeluar,
                'description' => $this->keterangan,
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            logger()->error(
                '[class schedule] ' .
                    auth()->user()->username .
                    ' gagal menyunting jadwal kelas',
                [$e->getMessage()]
            );

            session()->flash('alert', [
                'type' => 'danger',
                'message' => 'Gagal!',
                'detail' => "Gagal menyunting data jadwal kelas.",
            ]);

            return;
        }

        session()->flash('alert', [
            'type' => 'success',
            'message' => 'Berhasil!',
            'detail' => "Berhasil menyunting data jadwal kelas.",
        ]);

        return redirect()->route('master.class-schedule.index');
    }

    #[Computed()]
    public function teachers()
    {
        if (!$this->mataPelajaran) {
            return [];
        }
        return Teacher::where('subject_study_id', $this->mataPelajaran)->get(['id', 'name', 'nip']);
    }

    public function getTeachersForSubject($subjectId)
    {
        if (!$subjectId) {
            return [];
        }
        return Teacher::where('subject_study_id', $subjectId)->get(['id', 'name', 'nip']);
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

    // Load jadwal yang sudah ada berdasarkan kelas yang dipilih
    public function updatedKelas($value)
    {
        if ($value) {
            $this->prevClassRoomSchedule = ClassSchedule::where('class_room_id', $value)
                ->with(['subject_study', 'teacher'])
                ->get();
        } else {
            $this->prevClassRoomSchedule = [];
        }
    }

    // Reset guru saat mapel berubah
    public function updatedMataPelajaran($value)
    {
        $this->guru = null;
    }

    // Mulai inline edit
    public function startEdit($scheduleId)
    {
        $schedule = ClassSchedule::findOrFail($scheduleId);
        $this->editingScheduleId = $scheduleId;

        // Determine session
        $sesiVal = '';
        $start = date('H:i', strtotime($schedule->start_time));
        $end = date('H:i', strtotime($schedule->end_time));
        foreach (config('const.class_sessions') as $key => $session) {
            if (date('H:i', strtotime($session['start'])) == $start && date('H:i', strtotime($session['end'])) == $end) {
                $sesiVal = $key;
                break;
            }
        }

        $this->editingSchedule = [
            'id' => $schedule->id,
            'guru' => $schedule->teacher_id,
            'mataPelajaran' => $schedule->subject_study_id,
            'hari' => $schedule->day_name,
            'sesi' => $sesiVal,
            'waktuMasuk' => $schedule->start_time,
            'waktuKeluar' => $schedule->end_time,
            'keterangan' => $schedule->description,
        ];
    }

    // Batal edit
    public function cancelEdit()
    {
        $this->editingScheduleId = null;
        $this->editingSchedule = [];
    }

    // Save inline edit
    public function updateSchedule()
    {
        // 0. Pastikan Mapel Guru Sesuai
        $teacherObj = Teacher::find($this->editingSchedule['guru']);
        if (!$teacherObj || $teacherObj->subject_study_id != $this->editingSchedule['mataPelajaran']) {
            session()->flash('alert', [
                'type' => 'warning',
                'message' => 'Peringatan!',
                'detail' => 'Guru ini tidak mengajar mata pelajaran yang dipilih.',
            ]);
            return;
        }

        try {
            DB::beginTransaction();

            $schedule = ClassSchedule::findOrFail($this->editingSchedule['id']);

            // 1. Cek Bentrok Kelas
            $classConflict = ClassSchedule::where('class_room_id', $schedule->class_room_id)
                ->where('day_name', $this->editingSchedule['hari'])
                ->where('id', '!=', $schedule->id)
                ->where(function ($query) {
                    $query->whereRaw("TIME(start_time) < TIME(?)", [$this->editingSchedule['waktuKeluar']])
                        ->whereRaw("TIME(end_time) > TIME(?)", [$this->editingSchedule['waktuMasuk']]);
                })->count();

            if ($classConflict > 0) {
                session()->flash('alert', [
                    'type' => 'warning',
                    'message' => 'Peringatan!',
                    'detail' => 'Jadwal bentrok dengan mata pelajaran lain di kelas yang sama!',
                ]);
                return;
            }

            // 2. Cek Bentrok Guru
            $teacherConflict = ClassSchedule::where('teacher_id', $this->editingSchedule['guru'])
                ->where('day_name', $this->editingSchedule['hari'])
                ->where('id', '!=', $schedule->id)
                ->where(function ($query) {
                    $query->whereRaw("TIME(start_time) < TIME(?)", [$this->editingSchedule['waktuKeluar']])
                        ->whereRaw("TIME(end_time) > TIME(?)", [$this->editingSchedule['waktuMasuk']]);
                })->count();

            if ($teacherConflict > 0) {
                session()->flash('alert', [
                    'type' => 'warning',
                    'message' => 'Peringatan!',
                    'detail' => 'Jadwal bentrok! Guru tersebut sudah mengajar di kelas lain pada hari dan jam/sesi ini.',
                ]);
                return;
            }

            $schedule->update([
                'teacher_id' => $this->editingSchedule['guru'],
                'subject_study_id' => $this->editingSchedule['mataPelajaran'],
                'day_name' => $this->editingSchedule['hari'],
                'start_time' => $this->editingSchedule['waktuMasuk'],
                'end_time' => $this->editingSchedule['waktuKeluar'],
                'description' => $this->editingSchedule['keterangan'],
            ]);

            DB::commit();

            $this->prevClassRoomSchedule = ClassSchedule::where('class_room_id', $schedule->class_room_id)
                ->with(['subject_study', 'teacher'])
                ->get();
            $this->editingScheduleId = null;
            $this->editingSchedule = [];

            session()->flash('alert', [
                'type' => 'success',
                'message' => 'Berhasil!',
                'detail' => 'Jadwal berhasil diperbarui.',
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            logger()->error('[ClassSchedule] Update Error: ' . $e->getMessage());

            session()->flash('alert', [
                'type' => 'danger',
                'message' => 'Gagal!',
                'detail' => 'Terjadi kesalahan sistem: ' . $e->getMessage(),
            ]);
        }
    }

    public function render()
    {
        return view('livewire.master.class-schedule.edit');
    }
}
