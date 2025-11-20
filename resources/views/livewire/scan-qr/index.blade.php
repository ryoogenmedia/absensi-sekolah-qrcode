@push('styles')
    <style>
        #reader #reader__scan_region {
            transform: scaleX(-1)
        }
    </style>
@endpush

@push('styles')
    <style>
        .presensi-toggle {
            display: flex;
            gap: 15px;
            margin-bottom: 1rem;
        }

        .presensi-option {
            flex: 1;
            padding: 18px 20px;
            border-radius: 14px;
            border: 2px solid #dcdcdc;
            cursor: pointer;
            text-align: center;
            transition: all .25s ease;
            background: #fafafa;
            font-weight: 600;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.05);
        }

        .presensi-option i {
            font-size: 26px;
            display: block;
            margin-bottom: 5px;
        }

        .presensi-option:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 14px rgba(0, 0, 0, 0.1);
        }

        /* Saat aktif */
        .presensi-option.active {
            background: linear-gradient(135deg, #167ef2, #0052d9);
            color: white !important;
            border-color: transparent;
            box-shadow: 0 5px 18px rgba(0, 82, 217, 0.35);
        }

        .presensi-option.active.danger {
            background: linear-gradient(135deg, #ff4d4d, #d93636);
            box-shadow: 0 5px 18px rgba(255, 77, 77, 0.35);
        }

        /* Hidden radio */
        .presensi-input {
            display: none;
        }
    </style>
@endpush

@push('styles')
    <style>
        .presensi-header-base {
            padding: 18px 24px;
            border-radius: 14px;
            color: white;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.18);
        }

        .presensi-header-in {
            background: linear-gradient(135deg, #167ef2, #0052d9);
        }

        .presensi-header-out {
            background: linear-gradient(135deg, #ff4d4d, #d93636);
        }

        .presensi-header-base i {
            font-size: 32px;
        }

        /* Filter Card */
        .filter-card {
            border-radius: 14px;
            border: 1px solid #e5e5e5;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .reload-button {
            border-radius: 10px;
            background: #f0f4ff;
            transition: .25s;
        }

        .reload-button:hover {
            background: #dce7ff;
            transform: rotate(180deg);
        }

        table.card-table th {
            background: #f6f8fc;
            font-weight: 700;
        }

        .avatar {
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.12);
        }
    </style>
@endpush



<div>
    <x-slot name="title">Scan Presensi Siswa</x-slot>

    <x-slot name="pageTitle">Scan Presensi Siswa</x-slot>

    <x-slot name="pagePretitle">Kelola Data Presensi Siswa</x-slot>

    <div class="row">
        <div class="col-lg-5 col-12">
            <div class="card">
                <div class="card-header">
                    Scan Presensi Disini
                    <span class="las la-camera ms-2 fs-2"></span>
                </div>

                <div class="card-body">

                    <div class="mb-3">
                        <label class="form-label fw-bold">Pilih Jenis Presensi</label>

                        <div class="presensi-toggle">

                            <input type="radio" id="presensiIn" class="presensi-input" value="check-in"
                                wire:model.lazy="presensiType">
                            <label for="presensiIn"
                                class="presensi-option {{ $presensiType == 'check-in' ? 'active' : '' }}">
                                <i class="las la-sign-in-alt"></i>
                                PRESENSI DATANG
                            </label>

                            <input type="radio" id="presensiOut" class="presensi-input" value="check-out"
                                wire:model.lazy="presensiType">
                            <label for="presensiOut"
                                class="presensi-option danger {{ $presensiType == 'check-out' ? 'active' : '' }}">
                                <i class="las la-sign-out-alt"></i>
                                PRESENSI PULANG
                            </label>

                        </div>
                    </div>

                    <div wire:ignore id="reader"></div>
                </div>
            </div>
        </div>

        <div class="col-lg-7 col-12">
            @if ($this->presensiType == 'check-in')
                <livewire:scan-qr.check-in-record />
            @elseif($this->presensiType == 'check-out')
                <livewire:scan-qr.check-out-record />
            @endif
        </div>
    </div>
</div>

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"
        integrity="sha512-r6rDA7W6ZeQhvl8S7yRVQUKVHdexq+GAlNkNNqVC7YyIV+NwqCTJe2hDWCiffTyRNOeGEzRRJ9ifvRm/HCzGYg=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script type="text/javascript">
        document.addEventListener('livewire:init', () => {
            function onScanSuccess(decodedText, decodedResult) {
                Livewire.dispatch('scanned', {
                    code: decodedText
                });
            }

            var html5QrcodeScanner = new Html5QrcodeScanner(
                "reader", {
                    fps: 10,
                    qrbox: 250
                });
            html5QrcodeScanner.render(onScanSuccess);
        });
    </script>
@endpush
