<div>
    <x-alert />

    <form class="card" wire:submit.prevent="edit" autocomplete="off">
        <div class="card-header">Sunting Profile Guru</div>

        <div class="card-body">
            <div class="row">
                <div class="col-lg-6 col-12">
                    <x-form.input required wire:model="nama" name="nama" label="Nama" type="text"
                        placeholder="masukkan nama" autofocus />

                    <x-form.input required wire:model="nip" name="nip" label="NIP" type="text"
                        placeholder="masukkan nomor induk pegawai (nip)" />

                    <x-form.input required wire:model="nuptk" name="nuptk" label="NUPTK" type="text"
                        placeholder="masukkan nomor unik pendidik & tenaga kependidikan (nuptk)" />

                    <x-form.input required wire:model="nomorPonsel" name="nomorPonsel" label="Nomor Ponsel"
                        type="text" placeholder="masukkan nomor ponsel" />

                    <x-form.select required wire:model="agama" name="agama" label="Agama">
                        <option value="" selected>- Pilih Agama -</option>
                        @foreach (config('const.religions') as $agama)
                            <option value="{{ $agama }}">{{ $agama }}</option>
                        @endforeach
                    </x-form.select>

                    <div class="mb-3">
                        <label class="form-label" for="jenisKelamin">Jenis Kelamin <span
                                class="ms-1 text-red">*</span></label>

                        <div class="d-flex flex-row gap-3">
                            @foreach (config('const.sex') as $jenisKelamin)
                                <x-form.check required wire:model="jenisKelamin" name="jenisKelamin" type="radio"
                                    value="{{ $jenisKelamin }}" description="{{ $jenisKelamin }}" />
                            @endforeach
                        </div>
                    </div>

                    <div class="row">
                        <div
                            class="col-lg-2 align-self-center text-lg-start text-center mt-lg-0 mt-4 mb-lg-0 mb-2 mb-lg-3">
                            @if ($foto)
                                <span class="avatar avatar-md"
                                    style="background-image: url({{ $foto->temporaryUrl() }})"></span>
                            @else
                                <span class="avatar avatar-md"
                                    style="background-image: url({{ $fotoUrl }})"></span>
                            @endif
                        </div>

                        <div class="col-lg-9 col-auto mt-3 mt-lg-0 ms-lg-3">
                            <x-form.input wire:model.lazy="foto" name="foto" label="Foto Anda (Latar Merah)"
                                type="file" optional="Abaikan jika tidak ingin mengubah." />
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 col-12">
                    <x-form.input wire:model="tanggalLahir" name="tanggalLahir" label="Tanggal Lahir" type="date"
                        required />

                    <x-form.input wire:model="tempatLahir" name="tempatLahir" label="Tempat Lahir" type="text"
                        placeholder="masukkan tempat lahir" required />

                    <x-form.textarea wire:model="alamat" name="alamat" label="Alamat" placeholder="masukkan alamat" />

                    <div class="row">
                        <div class="col-lg-6 col-12">
                            <x-form.input wire:model="kodePos" name="kodePos" label="Kode Pos" type="text" />
                        </div>
                        <div class="col-lg-6 col-12">
                            <x-form.input wire:model="tanggalBergabung" name="tanggalBergabung"
                                label="Tanggal Bergabung" type="date" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer">
            <div class="btn-list justify-content-end">
                <button type="reset" class="btn">Reset</button>

                <x-datatable.button.save name="Simpan Perubahan" target="edit" />
            </div>
        </div>
    </form>
</div>
