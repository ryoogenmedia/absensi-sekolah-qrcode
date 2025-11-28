<?php

namespace App\Livewire\Setting\Profile;

use App\Models\Student;
use App\Models\StudentGuardian;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;

class StudentGuardianProfile extends Component
{
    public $namaWali;
    public $hubunganWali;
    public $kontakWali;
    public $siswa;

    public $studentGuardianId;

    public function rules()
    {
        return [
            'namaWali' => ['required', 'string', 'max:255'],
            'hubunganWali' => ['required', 'string', 'max:100'],
            'kontakWali' => ['required', 'string', 'max:50'],
            'siswa' => ['required', 'exists:students,id'],
        ];
    }

    #[Computed()]
    public function students()
    {
        return Student::all(['id', 'nis', 'full_name', 'class_room_id'])->load('class_room');
    }

    public function edit()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            $user = User::find(auth()->user()->id);

            StudentGuardian::updateOrCreate(
                ['id' => $this->studentGuardianId],

                [
                    'student_id' => $this->siswa,
                    'user_id' => $user->id,
                    'guardian_name' => $this->namaWali,
                    'guardian_relationship' => $this->hubunganWali,
                    'guardian_contact' => $this->kontakWali,
                ]
            );

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            logger()->error(
                '[profil wali siswa] ' .
                    auth()->user()->username .
                    ' gagal menyunting profil wali siswa',
                [$e->getMessage()]
            );

            session()->flash('alert', [
                'type' => 'danger',
                'message' => 'Gagal!',
                'detail' => "Gagal menyunting data profil wali siswa.",
            ]);

            return redirect()->back();
        }

        session()->flash('alert', [
            'type' => 'success',
            'message' => 'Berhasil!',
            'detail' => "Berhasil menyunting data profil wali siswa.",
        ]);

        return redirect()->back();
    }

    public function mount()
    {
        $guardian = auth()->user()->student_guardian;
        $student = $guardian->student;

        if ($guardian) {
            $this->studentGuardianId = $guardian->id;
            $this->namaWali = $guardian->guardian_name;
            $this->hubunganWali = $guardian->guardian_relationship;
            $this->kontakWali = $guardian->guardian_contact;
            $this->siswa = $student->id;
        }
    }

    public function render()
    {
        return view('livewire.setting.profile.student-guardian-profile');
    }
}
