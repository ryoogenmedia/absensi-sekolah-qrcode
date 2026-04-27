<div>
    <x-slot name="title">Tambah Guru</x-slot>

    <x-slot name="pagePretitle">Menambah Data Guru</x-slot>

    <x-slot name="pageTitle">Tambah Guru</x-slot>

    <x-slot name="button">
        <x-datatable.button.back name="Kembali" :route="route('teacher.index')" />
    </x-slot>

    <x-alert />

    <form class="card" wire:submit.prevent="save" autocomplete="off">
        <div class="card-header">
            Tambah data guru
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-12 col-lg-6">
                    <x-form.input wire:model="nama" name="nama" label="Nama" placeholder="masukkan nama"
                        type="text" required autofocus />

                    <div class="row">
                        <div class="col-lg-6 col-12">
                            <x-form.input wire:model="nip" name="nip" label="NIP" placeholder="masukkan nip"
                                type="text" required />
                        </div>

                        <div class="col-lg-6 col-12">
                            <x-form.input wire:model="nuptk" name="nuptk" label="NUPTK" placeholder="masukkan nuptk"
                                type="text" />
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
                        placeholder="masukkan nomor ponsel" type="text" required />

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

                    <x-form.select wire:model="agama" name="agama" label="Agama">
                        <option value="">- pilih agama -</option>
                        @foreach (config('const.religions') as $religion)
                            <option wire:key="{{ $religion }}" value="{{ $religion }}">{{ ucwords($religion) }}
                            </option>
                        @endforeach
                    </x-form.select>

                    <x-form.input wire:model="tanggalBergabung" name="tanggalBergabung" label="Tanggal Bergabung"
                        placeholder="masukkan tanggal bergabung" type="date" />
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-12 col-lg-6">
                    <x-form.input wire:model="kataSandi" name="kataSandi" label="Kata Sandi (Password)"
                        placeholder="**********" type="password" required />

                    <x-form.input wire:model="konfirmasiKataSandi" name="konfirmasiKataSandi"
                        label="Konfirmasi Kata Sandi (Password)" placeholder="**********" type="password" required />
                </div>

                <div class="col-12 col-lg-6">
                    <div class="row">
                        @if ($this->avatar)
                            <div class="col-lg-4 col-12 mb-lg-0 mb-2 text-center">
                                <label class="form-label" for="">Foto Profil</label>
                                <span class="avatar avatar-md"
                                    style="width: 120px;height:120px; object-fit:cover;background-image: url({{ $this->avatar->temporaryUrl() }})"></span>
                            </div>
                        @else
                            <div class="col-lg-4 col-12 mb-lg-0 mb-2 text-center">
                                <label class="form-label" for="">Foto Profil</label>
                                <span class="avatar avatar-md"
                                    style="width: 120px;height:120px; object-fit:cover;background-image: url({{ asset('static/ryoogen/default/NO-IMAGE.png') }})"></span>
                            </div>
                        @endif

                        <div class="col align-self-center mt-5 pt-3">
                            <x-form.input wire:model="avatar" name="avatar" placeholder="masukkan avatar"
                                type="file" optional="Abaikan jika tidak ingin mengubah." />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer">
            <div class="btn-list justify-content-end">
                <button type="reset" class="btn">Reset</button>

                <x-datatable.button.save target="save" />
            </div>
        </div>
    </form>
</div>
