<div>
    <x-slot name="title">Tambah Presensi Pertemuan</x-slot>

    <x-slot name="pagePretitle">Menambah Data Presensi Pertemuan</x-slot>

    <x-slot name="pageTitle">Tambah Presensi Pertemuan</x-slot>

    <x-slot name="button">
        <div class="d-print-none">
            <x-datatable.button.back name="Kembali" :route="route('class-attendance.detail', $this->classScheduleId)" />
        </div>
    </x-slot>

    <x-alert />

    <div class="row">
        <div class="col-12 {{ $isScannerOpen ? 'col-lg-8' : 'col-lg-12' }} transition-all">
            <form wire:submit.prevent="save" autocomplete="off">
        <div class="card d-print-none">

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
                <div class="d-flex gap-2">
                    <a href="{{ route('print-pdf.card', ['kelas' => $this->classRoomId]) }}" target="_blank" class="btn btn-green btn-sm d-print-none">
                        <i class="las la-id-card me-2"></i> Cetak Semua QR
                    </a>
                    <a href="{{ route('print-report.attendance.class.summary', ['kelas' => $this->classRoomId]) }}" target="_blank" class="btn btn-secondary btn-sm d-print-none">
                        <i class="las la-print me-2"></i> Cetak Ringkasan
                    </a>
                    <button type="button" wire:click="toggleScanner" class="btn btn-{{ $isScannerOpen ? 'danger' : 'primary' }} btn-sm d-print-none">
                        <i class="las la-qrcode me-2"></i>
                        {{ $isScannerOpen ? 'Tutup Scanner' : 'Buka Scanner' }}
                    </button>
                </div>
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
                                                <div class="mt-2 small text-muted mb-2">{{ $siswa['nis'] }}</div>
                                                <a href="{{ route('print-pdf.card', ['card_id' => $siswa['nis']]) }}" target="_blank" class="btn btn-primary btn-sm w-100">
                                                    <i class="las la-print me-1"></i> Cetak QR
                                                </a>
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

            <div class="card-footer d-print-none">
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
                        <div id="reader" wire:ignore style="width: 100%; min-height: 250px; background: #f8f9fa;"></div>
                        <div class="mt-3 text-center d-flex flex-column gap-2">
                            <button type="button" onclick="retryScanner()" class="btn btn-outline-primary btn-sm">
                                <i class="las la-sync me-1"></i> Inisialisasi Ulang Kamera
                            </button>
                            <small class="text-muted"><i class="las la-info-circle me-1"></i> Pastikan izin kamera telah diberikan.</small>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"
        integrity="sha512-r6rDA7W6ZeQhvl8S7yRVQUKVHdexq+GAlNkNNqVC7YyIV+NwqCTJe2hDWCiffTyRNOeGEzRRJ9ifvRm/HCzGYg=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script type="text/javascript">
        document.addEventListener('livewire:init', () => {
            let html5QrcodeScanner = null;

            function startScanner() {
                // Wait a bit to ensure the #reader div is rendered by Livewire and layout transitions are done
                setTimeout(() => {
                    const readerElem = document.getElementById('reader');
                    if (readerElem) {
                        if (!html5QrcodeScanner) {
                            html5QrcodeScanner = new Html5QrcodeScanner(
                                "reader", {
                                    fps: 10,
                                    qrbox: { width: 250, height: 250 }
                                });
                            html5QrcodeScanner.render((decodedText) => {
                                Livewire.dispatch('scanned', {
                                    code: decodedText
                                });
                            });
                            console.log("Scanner initialized.");
                        }
                    } else {
                        console.log("Reader element not found yet.");
                    }
                }, 500);
            }

            function stopScanner() {
                if (html5QrcodeScanner) {
                    html5QrcodeScanner.clear().then(() => {
                        html5QrcodeScanner = null;
                        console.log("Scanner stopped.");
                    }).catch(error => {
                        console.error("Failed to clear scanner: ", error);
                        html5QrcodeScanner = null;
                    });
                }
            }

            Livewire.on('init-scanner', () => {
                startScanner();
            });

            Livewire.on('close-scanner', () => {
                stopScanner();
            });

            window.retryScanner = () => {
                stopScanner();
                startScanner();
            };
            
            // Handle browser refresh if scanner was open (though unlikely with default false)
            if (document.getElementById('reader')) {
                startScanner();
            }
        });
    </script>
@endpush
