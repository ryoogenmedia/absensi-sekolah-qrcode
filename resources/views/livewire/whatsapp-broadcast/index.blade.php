@push('styles')
    <style>
        .wa-status-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
            background: #f8fafc;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 8px 0 rgba(44, 62, 80, 0.04);
        }

        .wa-status-table td {
            padding: 12px 18px;
            font-size: 16px;
            border-bottom: 1px solid #e0e0e0;
            vertical-align: middle;
        }

        .wa-status-table tr:last-child td {
            border-bottom: none;
        }

        .wa-status-label {
            font-weight: 600;
            color: #009688;
            width: 140px;
        }

        .wa-status-value {
            font-weight: 600;
            padding: 4px 14px;
            border-radius: 6px;
            background: #e0f2f1;
            color: #388e3c;
            display: inline-block;
            margin-right: 8px;
        }

        .wa-status-value.status-error {
            background: #ffcdd2;
            color: #c62828;
        }

        .wa-status-value.status-waiting {
            background: #fff9c4;
            color: #fbc02d;

            p .wa-status-desc text-muted small mt-2 {
                color: #607d8b;
                font-size: 14px;
                margin-left: 2px;
            }

            .wa-status-actions {
                margin-top: 10px;
                gap: 10px;
            }
    </style>
@endpush

<div>
    <x-slot name="title">Whatsapp Broadcast</x-slot>

    <x-slot name="pageTitle">Whatsapp Broadcast</x-slot>

    <x-slot name="pagePretitle">Atur Whatsapp Broadcast</x-slot>

    <x-alert/>

    <div class="row">
        <div class="col-lg-6 col-12">
            <div class="card mb-3">
                <div class="card card-count-data flex border border-green-lt">
                    <div class="card-body">
                        <div class="d-flex gap-3">
                            <div class="align-self-center">
                                <div style="font-size: 60px" class="lab la-whatsapp text-white bg-green p-2 rounded-3">
                                </div>
                            </div>

                            <div class="d-flex flex-column">
                                <h2 class="my-1 text-black">WhatsApp Broadcast</h2>
                                <div class="d-flex justify-content-between gap-2">
                                    <button type="button"
                                        wire:click="getStatusDeviceWhatsapp"
                                        class="btn btn-green">Cek Status <span
                                            class="las la-sync ms-2 fs-2"></span></button>
                                    <button wire:click="testSendText" class="btn btn-cyan">Kirim<span
                                            class="lab la-telegram-plane ms-2 fs-2"></span></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <table class="wa-status-table">
                        <tr>
                            <td class="wa-status-label" style="width: 150px">Status Scan</td>
                            <td>:</td>
                            <td>
                                @if (isset($this->scanStatus['status']) && $this->scanStatus['status'])
                                    @switch($this->scanStatus['status'])
                                        @case('CONNECTED')
                                            <span class="wa-status-value">Terhubung</span>
                                            <p class="wa-status-desc text-muted small mt-2">QR code telah discan dan sesi aktif.
                                                </spa>
                                            @break

                                            @case('WAITING_SCAN')
                                                <span class="wa-status-value status-waiting">Menunggu Scan</span>
                                                <p class="wa-status-desc text-muted small mt-2">QR code telah dibuat, silakan scan menggunakan aplikasi WhatsApp Anda.</p>
                                                @if ($qrCodeImage)
                                                    <div class="mt-3 p-3 bg-white border rounded text-center shadow-sm" style="width: fit-content;">
                                                        <img src="{{ $qrCodeImage }}" alt="WhatsApp QR Code" style="max-width: 250px;">
                                                        <div class="mt-2">
                                                            <button type="button" wire:click="getStatusDeviceWhatsapp" class="btn btn-sm btn-ghost-primary">
                                                                <i class="las la-sync me-1"></i> Refresh QR
                                                            </button>
                                                        </div>
                                                    </div>
                                                @endif
                                            @break

                                            @case('NOT_READY')
                                                <span class="wa-status-value status-error">Belum Siap</span>
                                            <p class="wa-status-desc text-muted small mt-2">QR code belum dibuat.</spa>
                                            @break

                                            @default
                                                <span class="wa-status-value status-error">Error</span>
                                            <p class="wa-status-desc text-muted small mt-2">Terjadi kesalahan pada status
                                                Whatsapp.
                                                </spa>
                                        @endswitch
                                    @else
                                        <span class="wa-status-value status-error">Server Whatsapp Mati</span>
                                    <p class="wa-status-desc text-muted small mt-2">Terjadi kesalahan pada server
                                        whatsapp.
                                        </spa>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="wa-status-label" style="width: 150px">Status Server</td>
                            <td>:</td>
                            <td>
                                @if (isset($this->statusActive['error']) && $this->statusActive['error'])
                                    <span class="wa-status-value status-error">🔴 Tidak Terkoneksi</span>
                                    <p class="wa-status-desc text-muted small mt-2">Whatsapp belum terkoneksi, tidak ada
                                        device yang
                                        konek.</spa>
                                    @elseif (isset($this->statusActive['data']) && $this->statusActive['data'])
                                        <span class="wa-status-value">🟢 Berhasil Terkoneksi</span>
                                    @else
                                        <span class="wa-status-value status-waiting">Status Tidak Diketahui</span>
                                    <p class="wa-status-desc text-muted small mt-2">Status Whatsapp tidak diketahui.
                                        </spa>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="card-footer">
                    <div class="d-flex wa-status-actions gap-2">
                        <button type="button" wire:click="restartWhatsapp" class="btn btn-orange">
                            Reset Server <span class="las la-redo-alt fs-2 ms-2"></span>
                        </button>
                        <button
                            wire:confirm="Apakah anda yakin ingin keluar dari whatsapp anda? Jika ada melakukannya, maka anda wajib scan qr code terlebih dahulu untuk mengirim pesan"
                            wire:click="logoutWhatsapp" class="btn btn-red">
                            Keluar <span class="las la-times fs-2 ms-2"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-12">
            <form class="card" wire:submit.prevent="save" autocomplete="off">
                <div class="card-header">
                    Konfigurasi Whatsapp Broadcast
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <x-form.input wire:model="nomorWhatsapp" name="nomorWhatsapp" label="Nomor Whatsapp"
                                placeholder="masukkan nomor whatsapp" type="text" required autofocus />

                            <x-form.input wire:model="whatsappUrl" name="whatsappUrl" label="Whatsapp URL Konfigurasi"
                                placeholder="masukkan link server / konfigurasi whatsapp" type="text" required />

                            <x-form.input wire:model="whatsappUrl" name="whatsappUrl" label="Whatsapp PORT Konfigurasi"
                                placeholder="masukkan port link server" type="text" required />
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
    </div>
</div>

@push('scripts')
    <script>
        function openNewWindow(url) {
            let width = screen.width * 0.8;
            let height = screen.height * 0.8;
            let left = (screen.width - width) / 2;
            let top = (screen.height - height) / 2;

            window.open(url, '_blank',
                `width=${width},height=${height},left=${left},top=${top},resizable=yes,scrollbars=yes`);
        }
    </script>
@endpush
