<?php

namespace App\Livewire\Setting\Profile;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class StudentProfile extends Component
{
    use WithFileUploads;

    public $namaPanggilan;
    public $namaLengkap;
    public $nis;
    public $nomorPonsel;
    public $jenisKelamin;
    public $agama;
    public $tempatLahir;
    public $tanggalLahir;
    public $asalSekolah;
    public $alamat;
    public $tahunMasuk;
    public $kodePos;
    public $namaAyah;
    public $pekerjaanAyah;
    public $namaIbu;
    public $pekerjaanIbu;

    public $foto;
    public $fotoUrl;

    public function rules(){
        return [
            'namaPanggilan'   => ['required','string','max:100'],
            'namaLengkap'    => ['required','string','max:100'],
            'nis'            => ['required','string','max:25'],
            'nomorPonsel'    => ['nullable','string','max:25'],
            'jenisKelamin'   => ['required', Rule::in(config('const.sex'))],
            'agama'          => ['required', Rule::in(config('const.religions'))],
            'tempatLahir'    => ['required','string','max:100'],
            'tanggalLahir'   => ['required','date'],
            'asalSekolah'    => ['nullable','string','max:150'],
            'alamat'         => ['nullable','string','max:255'],
            'tahunMasuk'     => ['nullable','digits:4'],
            'kodePos'        => ['nullable','string','max:10'],
            'namaAyah'       => ['nullable','string','max:100'],
            'pekerjaanAyah'  => ['nullable','string','max:100'],
            'namaIbu'        => ['nullable','string','max:100'],
            'pekerjaanIbu'   => ['nullable','string','max:100'],
            'foto'           => ['nullable','image','max:1024'],
        ];
    }

    public function edit(){
        $this->validate();

        try {
            DB::beginTransaction();

            $student = auth()->user()->student;

            $student->call_name         = $this->namaPanggilan;
            $student->full_name         = $this->namaLengkap;
            $student->nis               = $this->nis;
            $student->phone             = $this->nomorPonsel;
            $student->sex               = $this->jenisKelamin;
            $student->religion          = $this->agama;
            $student->place_of_birth    = $this->tempatLahir;
            $student->birth_date        = $this->tanggalLahir;
            $student->origin_school     = $this->asalSekolah;
            $student->address           = $this->alamat;
            $student->admission_year    = $this->tahunMasuk;
            $student->postal_code       = $this->kodePos;
            $student->father_name       = $this->namaAyah;
            $student->father_job        = $this->pekerjaanAyah;
            $student->mother_name       = $this->namaIbu;
            $student->mother_job        = $this->pekerjaanIbu;

            if ($this->foto) {
                $path = $this->foto->store('student-photos', 'public');
                $student->photo = $path;
            }

            $student->save();

            session()->flash('alert', [
                'type' => 'success',
                'message' => 'Berhasil!',
                'detail' => "Berhasil menyunting data profil siswa.",
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            logger()->error(
                '[profil siswa] ' .
                    auth()->user()->username .
                    ' gagal menyunting profil siswa',
                [$e->getMessage()]
            );

            session()->flash('alert', [
                'type' => 'danger',
                'message' => 'Gagal!',
                'detail' => "Gagal menyunting data profil siswa.",
            ]);
        }

        return redirect()->back();
    }

    public function mount(){
        $student = auth()->user()->student;

        $this->namaPanggilan = $student->call_name;
        $this->namaLengkap   = $student->full_name;
        $this->nis           = $student->nis;
        $this->nomorPonsel   = $student->phone;
        $this->jenisKelamin  = $student->sex;
        $this->agama         = $student->religion;
        $this->tempatLahir   = $student->place_of_birth;
        $this->tanggalLahir  = $student->birth_date;
        $this->asalSekolah   = $student->origin_school;
        $this->alamat        = $student->address;
        $this->tahunMasuk    = $student->admission_year;
        $this->kodePos       = $student->postal_code;
        $this->namaAyah      = $student->father_name;
        $this->pekerjaanAyah = $student->father_job;
        $this->namaIbu       = $student->mother_name;
        $this->pekerjaanIbu  = $student->mother_job;
        $this->fotoUrl       = $student->photoUrl();
    }

    public function render()
    {
        return view('livewire.setting.profile.student-profile');
    }
}
