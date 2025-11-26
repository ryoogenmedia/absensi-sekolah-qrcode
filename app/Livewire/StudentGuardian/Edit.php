<?php

namespace App\Livewire\StudentGuardian;

use App\Models\Student;
use App\Models\StudentGuardian;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;

class Edit extends Component
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

    public $userId;
    public $user;
    public $studentGuardianId;

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
            'email' => ['required', 'string', 'min:2', 'unique:users,email,' . $this->userId],
            'kataSandi' => ['nullable', 'string', 'same:konfirmasiKataSandi'],
            'avatar' => ['nullable', 'image', 'max:2048'],
        ];
    }

    public function save()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            $user = User::findOrFail($this->userId);

            $user->update([
                'username' => strtolower(str_replace(' ', '_', $this->namaWali)),
                'email' => strtolower($this->email),
                'role' => $this->role,
                'email_verified_at' => now(),
            ]);

            if ($this->kataSandi) {
                $user->update([
                    'password' => bcrypt($this->kataSandi),
                ]);
            }

            if ($this->avatar) {
                if ($user->avatar) {
                    Storage::disk('public')->delete($user->avatar);
                }

                $user->update([
                    'avatar' => $this->avatar->store('avatars', 'public'),
                ]);
            }

            $studentGuardian = StudentGuardian::findOrFail($this->studentGuardianId);

            $studentGuardian->update([
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
                    ' gagal menyunting wali siswa',
                [$e->getMessage()]
            );

            session()->flash('alert', [
                'type' => 'danger',
                'message' => 'Gagal!',
                'detail' => 'Gagal menyunting data wali siswa.',
            ]);

            return redirect()->back();
        }

        session()->flash('alert', [
            'type' => 'success',
            'message' => 'Berhasil!',
            'detail' => "Berhasil menyunting data wali siswa.",
        ]);

        return redirect()->route('guardian-student.index');
    }

    public function mount($id)
    {
        $studentGuardian = StudentGuardian::findOrFail($id);
        $this->user = User::findOrFail($studentGuardian->user_id);

        $this->studentGuardianId = $studentGuardian->id;
        $this->userId = $this->user->id;
        $this->namaWali = $studentGuardian->guardian_name;
        $this->siswa = $studentGuardian->student_id;
        $this->hubunganWali = $studentGuardian->guardian_relationship;
        $this->kontakWali = $studentGuardian->guardian_contact;
        $this->email = $this->user->email;
    }

    public function render()
    {
        return view('livewire.student-guardian.edit');
    }
}
