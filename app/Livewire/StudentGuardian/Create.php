<?php

namespace App\Livewire\StudentGuardian;

use App\Models\Student;
use App\Models\StudentGuardian;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;

class Create extends Component
{
    use WithFileUploads;

    public $namaWali;
    public $siswa;
    public $hubunganWali;
    public $kontakWali;

    public $email;
    public $avatar;
    public $kataSandi;
    public $konfirmasiKataSandi;

    public $role = 'wali siswa';

    #[Computed()]
    public function students()
    {
        return Student::all(['id', 'nis', 'full_name', 'class_room_id'])->load('class_room');
    }

    public function rules()
    {
        return [
            'namaWali' => ['required', 'string', 'min:2', 'max:255'],
            'siswa' => ['required', 'exists:students,id'],
            'hubunganWali' => ['required', 'string', 'min:2', 'max:255'],
            'kontakWali' => ['required', 'string', 'min:2', 'max:20'],
            'email' => ['required', 'string', 'min:2', 'unique:users,email'],
            'kataSandi' => ['required', 'string', 'same:konfirmasiKataSandi'],
            'avatar' => ['nullable', 'image', 'max:2048'],
        ];
    }

    public function save()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            $user = User::create([
                'username' => strtolower(str_replace(' ', '_', $this->namaWali)),
                'email' => strtolower($this->email),
                'password' => bcrypt($this->kataSandi),
                'role' => $this->role,
                'email_verified_at' => now(),
            ]);

            if ($this->avatar) {
                $user->update([
                    'avatar' => $this->avatar->store('avatars', 'public'),
                ]);
            }

            StudentGuardian::create([
                'user_id' => $user->id,
                'student_id' => $this->siswa,
                'guardian_name' => $this->namaWali,
                'guardian_relationship' => $this->hubunganWali,
                'guardian_contact' => $this->kontakWali,
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            logger()->error(
                '[wali siswa] ' .
                    auth()->user()->username .
                    ' gagal menambahkan wali siswa',
                [$e->getMessage()]
            );

            session()->flash('alert', [
                'type' => 'danger',
                'message' => 'Gagal!',
                'detail' => 'Gagal menambahkan data wali siswa.',
            ]);

            return redirect()->back();
        }

        session()->flash('alert', [
            'type' => 'success',
            'message' => 'Berhasil!',
            'detail' => "Berhasil menambahkan data wali siswa.",
        ]);

        return redirect()->route('guardian-student.index');
    }

    public function render()
    {
        return view('livewire.student-guardian.create');
    }
}
