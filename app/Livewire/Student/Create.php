<?php

namespace App\Livewire\Student;

use App\Models\ClassRoom;
use App\Models\Student;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;

class Create extends Component
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

    public function rules()
    {
        return [
            'namaPanggilan' => ['required', 'string', 'min:3', 'max:255'],
            'namaLengkap' => ['required', 'string', 'min:3', 'max:255'],
            'nis' => ['required', 'string', 'min:2', 'max:255', 'unique:students,nis'],
            'kelas' => ['required'],
            'jenisKelamin' => ['required', 'min:2', 'max:255', Rule::in(config('const.sex'))],
            'kodePos' => ['nullable', 'string', 'min:1', 'max:10'],
            'alamat' => ['nullable', 'string', 'min:2'],
            'nomorPonsel' => ['nullable', 'string', 'min:2', 'max:16'],
            'email' => ['required', 'string', 'min:2', 'max:255', 'email', 'unique:users,email'],
            'tempatLahir' => ['nullable', 'string', 'min:2', 'max:255'],
            'tanggalLahir' => ['nullable', 'string', 'min:2', 'max:255'],
            'agama' => ['required', 'string', 'min:2', 'max:255', Rule::in(config('const.religions'))],
            'asalSekolah' => ['nullable', 'string', 'min:2', 'max:255'],
            'tahunMasuk' => ['nullable', 'string', 'min:2', 'max:4'],
            'namaAyah' => ['nullable', 'string', 'min:2', 'max:255'],
            'namaIbu' => ['nullable', 'string', 'min:2', 'max:255'],
            'pekerjaanAyah' => ['nullable', 'string', 'min:2', 'max:255'],
            'pekerjaanIbu' => ['nullable', 'string', 'min:2', 'max:255'],
            'kataSandi' => ['required', 'string', 'min:8', 'max:255', 'same:konfirmasiKataSandi', Password::default()],
            'fotoProfil' => ['nullable', 'image'],
        ];
    }

    public function save()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            $user = User::create([
                'username' => ucwords($this->namaPanggilan),
                'avatar' => $this->fotoProfil ? $this->fotoProfil->store('avatars', 'public') : null,
                'role' => $this->role,
                'email' => strtolower($this->email),
                'password' => bcrypt($this->kataSandi),
                'email_verified_at' => now(),
            ]);

            Student::create([
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
                'photo' => $this->fotoProfil ? $this->fotoProfil->store('student-photos', 'public') : null,
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            logger()->error(
                '[siswa] ' .
                    auth()->user()->username .
                    ' gagal menambahkan siswa',
                [$e->getMessage()]
            );

            session()->flash('alert', [
                'type' => 'danger',
                'message' => 'Gagal!',
                'detail' => "Gagal menambahkan data siswa.",
            ]);

            return redirect()->back();
        }

        session()->flash('alert', [
            'type' => 'success',
            'message' => 'Berhasil!',
            'detail' => "Berhasil menambahkan data siswa.",
        ]);

        return redirect()->route('student.index');
    }

    #[Computed()]
    public function class_rooms()
    {
        return ClassRoom::all();
    }

    public function render()
    {
        return view('livewire.student.create');
    }
}
