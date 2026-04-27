<div>
    <x-slot name="title">Tambah Admin</x-slot>

    <x-slot name="pagePretitle">Menambah Data Admin</x-slot>

    <x-slot name="pageTitle">Tambah Admin</x-slot>

    <x-slot name="button">
        <x-datatable.button.back name="Kembali" :route="route('master.admin.index')" />
    </x-slot>

    <x-alert />

    <form class="card" wire:submit.prevent="save" autocomplete="off">
        <div class="card-header">
            Tambah data admin
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-12 col-lg-6">
                    <x-form.input wire:model="username" name="username" label="Username Akun"
                        placeholder="masukkan username" type="text" required autofocus />

                    <x-form.input wire:model="kataSandi" name="kataSandi" label="Kata Sandi (Password)"
                        placeholder="**********" type="password" required />
                </div>

                <div class="col-12 col-lg-6">
                    <x-form.input wire:model="email" name="email" label="Masukkan Email" placeholder="masukkan email"
                        type="text" required />

                    <x-form.input wire:model="konfirmasiKataSandi" name="konfirmasiKataSandi"
                        label="Konfirmasi Kata Sandi (Password)" placeholder="**********" type="password" required />
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="col-6">
                <div class="row">
                    @if ($this->avatar)
                        <div class="col-lg-2 col-12 mb-lg-0 mb-2 mt-2 text-center">
                            <span class="avatar avatar-md"
                                style="background-image: url({{ $this->avatar->temporaryUrl() }})"></span>
                        </div>
                    @else
                        <div class="col-lg-2 col-12 mb-lg-0 mb-2 mt-2 text-center">
                            <span class="avatar avatar-md"
                                style="background-image: url({{ asset('static/ryoogen/default/NO-IMAGE.png') }})"></span>
                        </div>
                    @endif

                    <div class="col">
                        <x-form.input wire:model="avatar" name="avatar" label="Foto Profil (Avatar)"
                            placeholder="masukkan avatar" type="file"
                            optional="Abaikan jika tidak ingin mengubah." />
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
