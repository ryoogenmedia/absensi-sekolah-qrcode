<div>
    <x-slot name="title">Sunting Wali Siswa</x-slot>

    <x-slot name="pagePretitle">Menyunting Data Wali Siswa</x-slot>

    <x-slot name="pageTitle">Sunting Wali Siswa</x-slot>

    <x-slot name="button">
        <x-datatable.button.back name="Kembali" :route="route('guardian-student.index')" />
    </x-slot>

    <x-alert />

    <form class="card" wire:submit.prevent="save" autocomplete="off">
        <div class="card-header">
            Sunting data wali siswa
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-12 col-lg-6">
                    <x-form.input wire:model="namaWali" name="namaWali" label="Nama Wali"
                        placeholder="masukkan nama wali" type="text" required autofocus />

                    @php
                        $grouped = $this->students->groupBy(fn($student) => $student->class_room->name_class);
                    @endphp

                    <div wire:ignore>
                        <x-form.select wire:ignore.self wire:model="siswa" name="siswa" label="Siswa"
                            form-control-class="js-example-basic-single form-control">

                            <option value="">Cari Nama / NIS Siswa</option>

                            @foreach ($grouped as $className => $students)
                                <optgroup label="Kelas {{ $className }}">
                                    @foreach ($students as $student)
                                        <option value="{{ $student->id }}">
                                            {{ $student->nis }} - {{ $student->full_name }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach

                        </x-form.select>
                    </div>
                </div>

                <div class="col-12 col-lg-6">
                    <x-form.select wire:model="hubunganWali" name="hubunganWali" label="Hubungan Wali">
                        <option value="">- Pilih -</option>
                        @foreach (config('const.guardian_relationships') as $relationship)
                            <option wire:key="{{ $relationship }}" value="{{ $relationship }}">
                                {{ ucwords($relationship) }}</option>
                        @endforeach
                    </x-form.select>

                    <x-form.input wire:model="kontakWali" name="kontakWali" label="Kontak Wali"
                        placeholder="nomor ponsel / nomor whatsapp" type="text" required />
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-12 col-lg-6">
                    <x-form.input wire:model="email" name="email" label="Masukkan Email" placeholder="masukkan email"
                        type="text" required />

                    <div class="row">
                        @if ($this->avatar)
                            <div class="col-1 mb-lg-0 mb-2 mt-2 me-3 text-center">
                                <span class="avatar avatar-md"
                                    style="background-image: url({{ $this->avatar->temporaryUrl() }})"></span>
                            </div>
                        @else
                            <div class="col-1 mb-lg-0 mb-2 mt-2 me-3 text-center">
                                <span class="avatar avatar-md"
                                    style="background-image: url({{ $this->user->avatarUrl() }})"></span>
                            </div>
                        @endif

                        <div class="col-lg-10 col-9 ms-5 ps-2">
                            <x-form.input wire:model="avatar" name="avatar" label="Foto Profil (Avatar)"
                                placeholder="masukkan avatar" type="file"
                                optional="Abaikan jika tidak ingin mengubah." />
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-6">
                    <x-form.input wire:model="kataSandi" name="kataSandi" label="Kata Sandi (Password)"
                        placeholder="**********" type="password" optional="Kosongkan jika tidak ingin mengubah" />

                    <x-form.input wire:model="konfirmasiKataSandi" name="konfirmasiKataSandi"
                        label="Konfirmasi Kata Sandi (Password)" placeholder="**********" type="password"
                        optional="Kosongkan jika tidak ingin mengubah" />
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

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            const select = document.querySelector('.js-example-basic-single');

            $(select).select2();

            select.addEventListener('change', function() {
                @this.set('siswa', this.value);
            });

        });
    </script>
@endpush
