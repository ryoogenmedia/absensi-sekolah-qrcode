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

    // Property untuk menampilkan tabel di Blade
    public $previousSchedule = [];
    public $prevClassRoomSchedule = [];

    public function rules()
    {
        return [
            'guru' => ['required'],
            'kelas' => ['required'],
            'mataPelajaran' => ['required'],
            'hari' => ['required', 'string', Rule::in(config('const.name_days'))],
            'waktuMasuk' => ['required'],
            'waktuKeluar' => ['required', 'after:waktuMasuk'],
            'keterangan' => ['nullable', 'string'],
        ];
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

        // Cek Bentrok
        $conflict = ClassSchedule::where('class_room_id', $this->kelas)
            ->where('day_name', $this->hari)
            ->where(function ($query) {
                $query->whereRaw("TIME(start_time) < TIME(?)", [$this->waktuKeluar])
                    ->whereRaw("TIME(end_time) > TIME(?)", [$this->waktuMasuk]);
            })->get();

        if ($conflict->count() > 0) {
            $this->previousSchedule = $conflict; // Isi property agar tabel muncul di Blade

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
