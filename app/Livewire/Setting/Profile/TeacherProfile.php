<?php

namespace App\Livewire\Setting\Profile;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class TeacherProfile extends Component
{
    use WithFileUploads;

    public $nama;
    public $nip;
    public $nuptk;
    public $nomorPonsel;
    public $jenisKelamin;
    public $agama;
    public $tempatLahir;
    public $tanggalLahir;
    public $alamat;
    public $tanggalBergabung;
    public $kodePos;

    public $foto;
    public $fotoUrl;

    public function rules(){
        return [
            'nama'              => ['required','string','max:100'],
            'nip'               => ['required','string','max:25'],
            'nuptk'             => ['required','string','max:25'],
            'nomorPonsel'       => ['nullable','string','max:25'],
            'jenisKelamin'      => ['required', Rule::in(config('const.sex'))],
            'agama'             => ['required', Rule::in(config('const.religions'))],
            'tempatLahir'       => ['required','string','max:100'],
            'tanggalLahir'      => ['required','date'],
            'alamat'            => ['nullable','string','max:255'],
            'tanggalBergabung'  => ['nullable','date'],
            'kodePos'           => ['nullable','string','max:10'],
            'foto'              => ['nullable','image','max:1024'],
        ];
    }

    public function edit(){
        $this->validate();

        try {
            DB::beginTransaction();

            $teacher = auth()->user()->teacher;

            $teacher->name              = $this->nama;
            $teacher->nip               = $this->nip;
            $teacher->nuptk             = $this->nuptk;
            $teacher->phone             = $this->nomorPonsel;
            $teacher->sex               = $this->jenisKelamin;
            $teacher->religion          = $this->agama;
            $teacher->place_of_birth    = $this->tempatLahir;
            $teacher->birth_date        = $this->tanggalLahir;
            $teacher->address           = $this->alamat;
            $teacher->date_joined       = $this->tanggalBergabung;
            $teacher->postal_code       = $this->kodePos;

            if ($this->foto) {
                $path = $this->foto->store('teacher-photos', 'public');
                $teacher->photo = $path;
            }

            $teacher->save();

            session()->flash('alert', [
                'type' => 'success',
                'message' => 'Berhasil!',
                'detail' => "Berhasil menyunting data profil guru.",
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            logger()->error(
                '[profil guru] ' .
                    auth()->user()->username .
                    ' gagal menyunting profil guru',
                [$e->getMessage()]
            );

            session()->flash('alert', [
                'type' => 'danger',
                'message' => 'Gagal!',
                'detail' => "Gagal menyunting data profil guru.",
            ]);
        }

        return redirect()->back();
    }

    public function mount(){
        $teacher = auth()->user()->teacher;

        $this->nama             = $teacher->name;
        $this->nip              = $teacher->nip;
        $this->nuptk            = $teacher->nuptk;
        $this->nomorPonsel      = $teacher->phone;
        $this->jenisKelamin     = $teacher->sex;
        $this->agama            = $teacher->religion;
        $this->tempatLahir      = $teacher->place_of_birth;
        $this->tanggalLahir     = $teacher->birth_date;
        $this->alamat           = $teacher->address;
        $this->tanggalBergabung = $teacher->date_joined;
        $this->kodePos          = $teacher->postal_code;
        $this->fotoUrl          = $teacher->photoUrl();
    }

    public function render()
    {
        return view('livewire.setting.profile.teacher-profile');
    }
}
