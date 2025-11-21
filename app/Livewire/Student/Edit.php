<?php

namespace App\Livewire\Student;

use App\Models\ClassRoom;
use App\Models\Student;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;

class Edit extends Component
{
    use WithFileUploads;

    public $namaLengkap;
    public $namaPanggilan;
    public $nis;
    public $kelas;
    public $jenisKelamin;
    public $kodePos;
    public $alamat;
    public $nomorPonsel;
    public $email;
    public $tempatLahir;
    public $tanggalLahir;
    public $agama;
    public $asalSekolah;
    public $tahunMasuk;
    public $namaAyah;
    public $namaIbu;
    public $pekerjaanAyah;
    public $pekerjaanIbu;

    public $kataSandi;
    public $konfirmasiKataSandi;
    public $fotoProfil;
    public $role = 'siswa';

    // IDENTITY
    public $userId;
    public $studentId;

    public function rules()
    {
        return [
            'namaPanggilan' => ['required', 'string', 'min:3', 'max:255'],
            'namaLengkap' => ['required', 'string', 'min:3', 'max:255'],
            'nis' => ['required', 'string', 'min:2', 'max:255', 'unique:students,nis,' . $this->studentId],
            'kelas' => ['required'],
            'jenisKelamin' => ['required', 'min:2', 'max:255', Rule::in(config('const.sex'))],
            'kodePos' => ['nullable', 'string', 'min:1', 'max:10'],
            'alamat' => ['nullable', 'string', 'min:2'],
            'nomorPonsel' => ['nullable', 'string', 'min:2', 'max:16'],
            'email' => ['required', 'string', 'min:2', 'max:255', 'email', 'unique:users,email,' . $this->userId],
            'tempatLahir' => ['nullable', 'string', 'min:2', 'max:255'],
            'tanggalLahir' => ['nullable', 'string', 'min:2', 'max:255'],
            'agama' => ['nullable', 'string', 'min:2', 'max:255', Rule::in(config('const.religions'))],
            'asalSekolah' => ['nullable', 'string', 'min:2', 'max:255'],
            'tahunMasuk' => ['nullable', 'string', 'min:2', 'max:4'],
            'namaAyah' => ['nullable', 'string', 'min:2', 'max:255'],
            'namaIbu' => ['nullable', 'string', 'min:2', 'max:255'],
            'pekerjaanAyah' => ['nullable', 'string', 'min:2', 'max:255'],
            'pekerjaanIbu' => ['nullable', 'string', 'min:2', 'max:255'],
            'kataSandi' => ['nullable', 'string', 'min:8', 'max:255', 'same:konfirmasiKataSandi', Password::default()],
            'fotoProfil' => ['nullable', 'image'],
        ];
    }

    public function edit()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            $user = User::findOrFail($this->userId);

            $user->update([
                'username' => ucwords($this->namaPanggilan),
                'role' => $this->role,
                'email' => strtolower($this->email),
                'email_verified_at' => now(),
            ]);

            if ($this->kataSandi) {
                $user->update([
                    'password' => bcrypt($this->kataSandi),
                ]);
            }

            $student = Student::findOrFail($this->studentId);

            $student->update([
                'user_id' => $user->id,
                'class_room_id' => $this->kelas,
                'full_name' => $this->namaLengkap,
                'call_name' => $this->namaPanggilan,
                'sex' => $this->jenisKelamin,
                'nis' => $this->nis,
                'phone' => $this->nomorPonsel,
                'religion' => $this->agama,
                'origin_school' => $this->asalSekolah,
                'birth_date' => $this->tanggalLahir,
                'place_of_birth' => $this->tempatLahir,
                'address' => $this->alamat,
                'postal_code' => $this->kodePos,
                'admission_year' => $this->tahunMasuk,
                'father_name' => $this->namaAyah,
                'mother_name' => $this->namaIbu,
                'father_job' => $this->pekerjaanAyah,
                'mother_job' => $this->pekerjaanIbu,
            ]);

            if ($this->fotoProfil) {
                if ($user->avatar) {
                    File::delete(public_path('storage/' . $user->avatar));

                    $user->update([
                        'avatar' => $this->fotoProfil->store('avatars', 'public')
                    ]);
                }

                if ($student->photo) {
                    File::delete(public_path('storage/' . $student->photo));

                    $student->update([
                        'photo' => $this->fotoProfil->storage('student-photos', 'public'),
                    ]);
                }
            }


            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            logger()->error(
                '[siswa] ' .
                    auth()->user()->username .
                    ' gagal menyunting siswa',
                [$e->getMessage()]
            );

            session()->flash('alert', [
                'type' => 'danger',
                'message' => 'Gagal!',
                'detail' => "Gagal menyunting data siswa.",
            ]);

            return redirect()->back();
        }

        session()->flash('alert', [
            'type' => 'success',
            'message' => 'Berhasil!',
            'detail' => "Berhasil menyunting data siswa.",
        ]);

        return redirect()->route('student.index');
    }

    #[Computed()]
    public function class_rooms()
    {
        return ClassRoom::all();
    }

    public function mount($id)
    {
        $student = Student::findOrFail($id);
        $user = User::findOrFail($student->user_id);

        $this->studentId = $student->id;
        $this->userId = $user->id;

        $this->namaLengkap = $student->full_name;
        $this->namaPanggilan = $student->call_name;
        $this->nis = $student->nis;
        $this->kelas = $student->class_room_id;
        $this->jenisKelamin = $student->sex;
        $this->kodePos = $student->postal_code;
        $this->alamat = $student->address;
        $this->nomorPonsel = $student->phone;
        $this->email = $student->user->email;
        $this->tempatLahir = $student->place_of_birth;
        $this->tanggalLahir = $student->birth_date;
        $this->agama = $student->religion;
        $this->asalSekolah = $student->origin_school;
        $this->tahunMasuk = $student->admission_year;
        $this->namaAyah = $student->father_name;
        $this->namaIbu = $student->mother_name;
        $this->pekerjaanAyah = $student->father_job;
        $this->pekerjaanIbu = $student->mother_job;
    }

    public function render()
    {
        return view('livewire.student.edit');
    }
}
