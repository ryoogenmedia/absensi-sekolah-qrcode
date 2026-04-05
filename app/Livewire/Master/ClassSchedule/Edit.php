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

            'hari' => ['required', 'string', 'min:2', 'max:255', Rule::in(config('const.name_days'))],
            'waktuMasuk' => ['required', 'min:2', 'max:255'],
            'waktuKeluar' => ['required', 'string', 'min:2', 'max:255'],
            'keterangan' => ['nullable', 'string'],
        ];
    }

    public function edit()
    {
        $this->validate();

        // Cek Bentrok (exclude jadwal yang sedang diedit)
        $conflict = ClassSchedule::where('class_room_id', $this->kelas)
            ->where('day_name', $this->hari)
            ->where('id', '!=', $this->classScheduleId)
            ->where(function ($query) {
                $query->whereRaw("TIME(start_time) < TIME(?)", [$this->waktuKeluar])
                    ->whereRaw("TIME(end_time) > TIME(?)", [$this->waktuMasuk]);
            })->get();

        if ($conflict->count() > 0) {
            $this->previousSchedule = $conflict;

            $message = "Jadwal bentrok dengan mata pelajaran yang sudah ada!";
            $this->addError('waktuMasuk', $message);
            $this->addError('waktuKeluar', $message);

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
        $this->editingSchedule = [
            'id' => $schedule->id,
            'guru' => $schedule->teacher_id,
            'mataPelajaran' => $schedule->subject_study_id,
            'hari' => $schedule->day_name,
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
        try {
            DB::beginTransaction();

            $schedule = ClassSchedule::findOrFail($this->editingSchedule['id']);

            // Cek conflict
            $conflict = ClassSchedule::where('class_room_id', $schedule->class_room_id)
                ->where('day_name', $this->editingSchedule['hari'])
                ->where('id', '!=', $schedule->id)
                ->where(function ($query) {
                    $query->whereRaw("TIME(start_time) < TIME(?)", [$this->editingSchedule['waktuKeluar']])
                        ->whereRaw("TIME(end_time) > TIME(?)", [$this->editingSchedule['waktuMasuk']]);
                })->count();

            if ($conflict > 0) {
                session()->flash('alert', [
                    'type' => 'warning',
                    'message' => 'Peringatan!',
                    'detail' => 'Jadwal bentrok dengan mata pelajaran yang sudah ada!',
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
