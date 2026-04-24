<div>
    <x-slot name="title">Tambah Presensi Pertemuan</x-slot>

    <x-slot name="pagePretitle">Menambah Data Presensi Pertemuan</x-slot>

    <x-slot name="pageTitle">Tambah Presensi Pertemuan</x-slot>

    <x-slot name="button">
        <x-datatable.button.back name="Kembali" :route="route('class-attendance.detail', $this->classScheduleId)" />
    </x-slot>

    <x-alert />

    <div class="row">
        <div class="col-12 {{ $isScannerOpen ? 'col-lg-8' : 'col-lg-12' }} transition-all">
            <form wire:submit.prevent="save" autocomplete="off">
        <div class="card">

            <div class="card-header">
                Tambah data presensi pertemuan
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
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>Daftar siswa presensi</div>
                <button type="button" wire:click="toggleScanner" class="btn btn-{{ $isScannerOpen ? 'danger' : 'primary' }} btn-sm">
                    <i class="las la-qrcode me-2"></i>
                    {{ $isScannerOpen ? 'Tutup Scanner' : 'Buka Scanner' }}
                </button>
            </div>

            <div class="card-body">
                <div class="table-responsive mb-0">
                    <table class="table table-bordered datatable">
                        <thead>
                            <tr>
                                <th class="w-1">No</th>

                                <th class="w-1">QR</th>

                                <th>Nama Siswa</th>

                                <th>Nis</th>

                                <th>Status Kehadiran</th>
                            </tr>
                        </thead>

                        <tbody>
                            @php $index = 1; @endphp
                            @foreach ($presensiSiswa as $id => $siswa)
                                <tr wire:key="row-{{ $id }}" class="{{ $siswa['status_kehadiran'] == 'hadir' ? 'table-success' : '' }}">
                                    <td>{{ $index++ }}</td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-ghost-primary btn-icon btn-sm" data-bs-toggle="dropdown">
                                                <i class="las la-qrcode fs-2"></i>
                                            </button>
                                            <div class="dropdown-menu p-3 text-center" style="min-width: 150px;">
                                                <div class="mb-2 fw-bold">{{ $siswa['nama'] }}</div>
                                                {!! DNS2D::getBarcodeHTML($siswa['nis'], 'QRCODE', 4, 4) !!}
                                                <div class="mt-2 small text-muted">{{ $siswa['nis'] }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $siswa['nama'] }}</td>
                                    <td>{{ $siswa['nis'] }}</td>
                                    <td>
                                        <x-form.select
                                            wire:model.lazy="presensiSiswa.{{ $id }}.status_kehadiran"
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
                    <x-datatable.button.save target="save" name="Simpan Presensi" class="w-full" color="success" />
                </div>
            </div>
        </div>
            </form>
        </div>

        @if ($isScannerOpen)
            <div class="col-12 col-lg-4">
                <div class="card sticky-top" style="top: 10px;">
                    <div class="card-header">
                        <h3 class="card-title">Scan QR Siswa</h3>
                    </div>
                    <div class="card-body">
                        <div id="reader" wire:ignore></div>
                        <div class="mt-3 text-muted text-center">
                            <small><i class="las la-info-circle me-1"></i> Arahkan QR Code siswa ke kamera untuk presensi.</small>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

@push('scripts')
    @if ($isScannerOpen)
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"
            integrity="sha512-r6rDA7W6ZeQhvl8S7yRVQUKVHdexq+GAlNkNNqVC7YyIV+NwqCTJe2hDWCiffTyRNOeGEzRRJ9ifvRm/HCzGYg=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>

        <script type="text/javascript">
            document.addEventListener('livewire:init', () => {
                function onScanSuccess(decodedText, decodedResult) {
                    Livewire.dispatch('scanned', {
                        code: decodedText
                    });
                    
                    // Optional: play success sound
                    // new Audio('/assets/sounds/success.mp3').play();
                }

                let html5QrcodeScanner = new Html5QrcodeScanner(
                    "reader", {
                        fps: 10,
                        qrbox: 250
                    });
                html5QrcodeScanner.render(onScanSuccess);
                
                // Cleanup on component destroy
                Livewire.on('close-scanner', () => {
                    html5QrcodeScanner.clear();
                });
            });
        </script>
    @endif
@endpush
