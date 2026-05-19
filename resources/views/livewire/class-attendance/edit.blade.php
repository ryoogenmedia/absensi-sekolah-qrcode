<div>
    <x-slot name="title">Sunting Presensi Pertemuan</x-slot>

    <x-slot name="pagePretitle">Menyunting Data Presensi Pertemuan</x-slot>

    <x-slot name="pageTitle">Sunting Presensi Pertemuan</x-slot>

    <x-slot name="button">
        <x-datatable.button.back name="Kembali" :route="route('class-attendance.detail', $this->classScheduleId)" />
    </x-slot>

    <x-alert />

    <form wire:submit.prevent="edit" autocomplete="off">
        <div class="card">

            <div class="card-header">
                Sunting data presensi pertemuan
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-12 col-lg-6">
                        <x-form.input wire:model="namaMateri" name="namaMateri" label="Nama Materi"
                            placeholder="masukkan nama materi pelajaran" type="text" required autofocus />

                        <x-form.input wire:model="buktiPresensi" name="buktiPresensi" label="Bukti Presensi"
                            type="file" optional="Masukkan bukti foto kelas jika ada" />
                    </div>

                    <div class="col-12 col-lg-6">
                        <x-form.textarea wire:model="penjelasanMateri" name="penjelasanMateri" label="Penjelasan Materi"
                            placeholder="Jelaskan apa saja yang di ajarkan pada materi tersebut seperti point materi atau kegiatan pada materi..."
                            style="height: 120px;" />
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                Daftar siswa presensi
            </div>

            <div class="card-body">
                <div class="table-responsive mb-0">
                    <table class="table table-bordered datatable">
                        <thead>
                            <tr>
                                <th class="w-50">Nama Siswa</th>

                                <th class="w-35">Nis</th>

                                <th>Status Kehadiran</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($presensiSiswa as $id => $siswa)
                                <tr>
                                    <td>{{ $siswa['nama'] }}</td>
                                    <td>{{ $siswa['nis'] }}</td>
                                    <td>
                                        <x-form.select
                                            wire:model="presensiSiswa.{{ $id }}.status_kehadiran"
                                            name="presensiSiswa.{{ $id }}.status_kehadiran" form-group-class>
                                            @foreach (config('const.attendance_status') as $status)
                                                <option wire:key="{{ $status }}" value="{{ $status }}">
                                                    {{ strtoupper($status) }}</option>
                                            @endforeach
                                        </x-form.select>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card-footer">
                <div class="btn-list justify-content-end" class="w-full">
                    <x-datatable.button.save target="edit" name="Simpan Presensi" class="w-full" color="success" />
                </div>
            </div>
        </div>
    </form>
</div>
