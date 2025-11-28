<div>
    <x-alert />

    <form class="card" wire:submit.prevent="edit" autocomplete="off">
        <div class="card-header">Sunting Profile Siswa</div>

        <div class="card-body">
            <div class="row">
                <div class="col-lg-6 col-12">
                    <x-form.input required wire:model="namaWali" name="namaWali" label="Nama Wali Siswa" type="text"
                        placeholder="masukkan nama wali siswa" autofocus />

                    @php
                        $grouped = $this->students->groupBy(fn($student) => $student->class_room->name_class);
                    @endphp

                    <div wire:ignore>
                        <x-form.select wire:model="siswa" name="siswa" label="Siswa"
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

                <div class="col-lg-6 col-12">
                    <x-form.select required wire:model="hubunganWali" name="hubunganWali" label="Hubungan dengan Siswa">
                        <option value="" selected>- Pilih -</option>
                        @foreach (config('const.guardian_relationships') as $hubungan)
                            <option value="{{ $hubungan }}">{{ ucwords($hubungan) }}</option>
                        @endforeach
                    </x-form.select>

                    <x-form.input wire:model="kontakWali" name="kontakWali" label="Kontak Wali"
                        placeholder="nomor ponsel / nomor whatsapp" type="text" required />
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

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            const select = document.querySelector('.js-example-basic-single');

            // Inisialisasi Select2
            $(select).select2();

            // Sinkron ke Livewire
            select.addEventListener('change', function() {
                @this.set('siswa', this.value);
            });

        });
    </script>
@endpush
