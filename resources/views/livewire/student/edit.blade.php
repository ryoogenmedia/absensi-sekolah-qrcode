<div>
    <x-slot name="title">Sunting Siswa</x-slot>

    <x-slot name="pagePretitle">Menyunting Data Siswa</x-slot>

    <x-slot name="pageTitle">Sunting Siswa</x-slot>

    <x-slot name="button">
        <x-datatable.button.back name="Kembali" :route="route('student.index')" />
    </x-slot>

    <x-alert />

    <form class="card" wire:submit.prevent="edit" autocomplete="off">
        <div class="card-header">
            Sunting data siswa
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-12 col-lg-6">
                    <div class="row">
                        <div class="col-lg-6 col-12">
                            <x-form.input wire:model="namaLengkap" name="namaLengkap" label="Nama Lengkap"
                                placeholder="masukkan nama lengkap" type="text" required autofocus />
                        </div>

                        <div class="col-lg-6 col-12">
                            <x-form.input wire:model="namaPanggilan" name="namaPanggilan" label="Nama Panggilan"
                                placeholder="masukkan nama panggilan" type="text" required />
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6 col-12">
                            <x-form.input wire:model="nis" name="nis" label="NIS" placeholder="masukkan nis"
                                type="text" required />
                        </div>

                        <div class="col-lg-6 col-12">
                            <x-form.select wire:model="kelas" name="kelas" label="Kelas / Ruang Kelas" required>
                                <option value="">- pilih ruang kelas -</option>
                                @foreach ($this->class_rooms as $class_room)
                                    <option wire:key="{{ $class_room->id }}" value="{{ $class_room->id }}">
                                        {{ strtoupper($class_room->name_class) }}</option>
                                @endforeach
                            </x-form.select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6 col-12">
                            <div class="mb-4">
                                <label class="mb-2" for="jenisKelamin">Jenis Kelamin <span
                                        class="ms-1 text-red">*</span></label>
                                <div class="d-flex gap-3">
                                    @foreach (config('const.sex') as $sex)
                                        <x-form.check type="radio" wire:model="jenisKelamin" name="jenisKelamin"
                                            description="{{ ucwords($sex) }}" value="{{ $sex }}" required />
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6 col-12">
                            <x-form.input wire:model="kodePos" name="kodePos" label="Kode Pos" placeholder="kode pos"
                                type="text" />
                        </div>
                    </div>

                    <x-form.textarea wire:model="alamat" name="alamat" label="Alamat"
                        placeholder="masukkan alamat lengkap" style="height: 120px" />
                </div>

                <div class="col-12 col-lg-6">
                    <x-form.input wire:model="nomorPonsel" name="nomorPonsel" label="Nomor Ponsel"
                        placeholder="masukkan nomor ponsel" type="text" />

                    <x-form.input wire:model="email" name="email" label="Masukkan Email" placeholder="masukkan email"
                        type="text" required />

                    <div class="row">
                        <div class="col-lg-6 col-12">
                            <x-form.input wire:model="tempatLahir" name="tempatLahir" label="Tempat Lahir"
                                placeholder="masukkan tempat lahir" type="text" />
                        </div>

                        <div class="col-lg-6 col-12">
                            <x-form.input wire:model="tanggalLahir" name="tanggalLahir" label="Tanggal Lahir"
                                placeholder="masukkan tanggal lahir" type="date" />
                        </div>
                    </div>

                    <x-form.select wire:model="agama" name="agama" label="Agama" required>
                        <option value="">- pilih agama -</option>
                        @foreach (config('const.religions') as $religion)
                            <option wire:key="{{ $religion }}" value="{{ $religion }}">
                                {{ ucwords($religion) }}
                            </option>
                        @endforeach
                    </x-form.select>

                    <div class="row">
                        <div class="col-lg-6 col-12">
                            <x-form.input wire:model="asalSekolah" name="asalSekolah" label="Asal Sekolah"
                                placeholder="masukkan asal sekolah" type="text" />
                        </div>

                        <div class="col-lg-6 col-12">
                            <x-form.input wire:model="tahunMasuk" name="tahunMasuk" label="Tahun Masuk"
                                placeholder="masukkan tahun masuk" type="text" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-lg-6 col-12">
                    <x-form.input wire:model="namaAyah" name="namaAyah" label="Nama Ayah"
                        placeholder="masukkan nama ayah" type="text" />

                    <x-form.input wire:model="namaIbu" name="namaIbu" label="Nama Ibu"
                        placeholder="masukkan nama ibu" type="text" />
                </div>

                <div class="col-lg-6 col-12">
                    <x-form.input wire:model="pekerjaanAyah" name="pekerjaanAyah" label="Pekerjaan Ayah"
                        placeholder="masukkan pekerjaan ayah" type="text" />

                    <x-form.input wire:model="pekerjaanIbu" name="pekerjaanIbu" label="Pekerjaan Ibu"
                        placeholder="masukkan pekerjaan ibu" type="text" />
                </div>
            </div>
        </div>

        <div class="card-body">
            Identitas Orang Tua
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-12 col-lg-6">
                    <x-form.input wire:model="kataSandi" name="kataSandi" label="Kata Sandi (Password)"
                        placeholder="**********" type="password" optional="Kosongkan jika tidak ingin mengubah." />

                    <x-form.input wire:model="konfirmasiKataSandi" name="konfirmasiKataSandi"
                        label="Konfirmasi Kata Sandi (Password)" placeholder="**********" type="password"
                        optional="Kosongkan jika tidak ingin mengubah." />
                </div>

                <div class="col-12 col-lg-6">
                    <div class="row">
                        @if ($this->fotoProfil)
                            <div class="col-lg-4 col-12 mb-lg-0 mb-2 text-center">
                                <label class="form-label" for="">Foto Profil</label>
                                <span class="avatar avatar-md"
                                    style="width: 120px;height:120px; object-fit:cover;background-image: url({{ $this->fotoProfil->temporaryUrl() }})"></span>
                            </div>
                        @else
                            <div class="col-lg-4 col-12 mb-lg-0 mb-2 text-center">
                                <label class="form-label" for="">Foto Profil</label>
                                <span class="avatar avatar-md"
                                    style="width: 120px;height:120px; object-fit:cover;background-image: url({{ asset('static/ryoogen/default/NO-IMAGE.png') }})"></span>
                            </div>
                        @endif

                        <div class="col align-self-center mt-5 pt-3">
                            <x-form.input wire:model="fotoProfil" name="fotoProfil"
                                placeholder="masukkan foto profil" type="file"
                                optional="Abaikan jika tidak ingin mengubah." />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer">
            <div class="btn-list justify-content-end">
                <button type="reset" class="btn">Reset</button>

                <x-datatable.button.save target="edit" />
            </div>
        </div>
    </form>
</div>
