<div x-data="{ currentTab: @entangle('tab') }">
    <div class="row g-2 align-items-center mb-4">
        <div class="col">
            <div class="page-pretitle">Cetak Laporan Presensi Kelas</div>
            <h2 class="page-title">Laporan Presensi Kelas</h2>
        </div>

        <div class="col-auto ms-auto d-print-none">
            <div class="btn-list">
                <a href="{{ route('print-report.attendance.class', $filters) }}" target="_blank" class="btn btn-danger">
                    <span class="las la-print fs-1 me-2"></span>Cetak Laporan
                </a>
            </div>
        </div>
    </div>

    {{-- Alert tetap di atas untuk menunjukkan pesan sukses/error --}}
    <div wire:loading.remove wire:target="filters, search, tab, kelas">
        <x-alert />
    </div>

    <div class="row mb-1 align-items-center justify-content-between">
        <div class="col-12 col-lg-8 d-flex">
            <div class="w-100">
                <x-datatable.search wire:model.live="filters.search" placeholder="Cari nama siswa..." />
            </div>

            <div class="w-100 ms-2">
                <x-form.select wire:model.live="filters.kelas" name="filters.kelas">
                    <option value="">Semua Kelas</option>
                    @foreach ($this->class_rooms as $class_room)
                        <option value="{{ $class_room->id }}">{{ strtoupper($class_room->name_class) }}</option>
                    @endforeach
                </x-form.select>
            </div>

            <div class="ms-2">
                <x-datatable.filter.button target="attendance-class-filters" />
            </div>
        </div>
        <div class="col-auto ms-auto d-flex mt-lg-0 mt-3">
            <x-datatable.items-per-page />
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-md-4">
            <div class="mb-3 mt-3 mt-md-0">
                <div class="btn-group w-100">
                    {{-- Navigasi Tab menggunakan AlpineJS agar instan --}}
                    <button type="button" @click="currentTab = 'list'"
                        :class="currentTab == 'list' ? 'btn-primary' : 'btn-outline-primary'"
                        class="btn">Record</button>
                    <button type="button" @click="currentTab = 'summary'"
                        :class="currentTab == 'summary' ? 'btn-primary' : 'btn-outline-primary'"
                        class="btn">Ringkasan</button>
                </div>
            </div>
        </div>
    </div>

    <x-datatable.filter.card id="attendance-class-filters">
        <div class="row">
            <div class="col-12 col-lg-6">
                <x-form.input wire:model.live="filters.startDate" name="startDate" label="Tanggal Mulai"
                    type="date" />
            </div>
            <div class="col-12 col-lg-6">
                <x-form.input wire:model.live="filters.endDate" name="endDate" label="Tanggal Selesai" type="date" />
            </div>
        </div>
    </x-datatable.filter.card>

    {{-- Card Tabel: wire:target memastikan loading hanya muncul saat filter berubah --}}
    <div class="card" wire:loading.class="opacity-50" style="transition: opacity 0.3s ease;">
        <div class="table-responsive mb-0">
            <table class="table card-table table-bordered datatable">
                {{-- TAB LIST --}}
                <template x-if="currentTab == 'list'">
                    <thead>
                        <tr>
                            <th class="text-center">Kelas</th>
                            <th>Tanggal</th>
                            <th>Nama Siswa</th>
                            <th>Guru & Mapel</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                </template>
                <template x-if="currentTab == 'list'">
                    <tbody>
                        @forelse ($this->rows as $row)
                            <tr wire:key="row-{{ $row->id }}">
                                <td class="text-center">
                                    <b>{{ $row->class_attendance->class_room->name_class ?? '' }}</b>
                                </td>
                                <td>{{ $row->class_attendance->created_at->translatedFormat('l, d/m/Y') }}</td>
                                <td>{{ $row->student->full_name ?? '-' }}</td>
                                <td>
                                    <div class="small text-muted">
                                        {{ $row->class_attendance->class_schedule->teacher->name ?? '-' }}</div>
                                    <div class="fw-bold">
                                        {{ strtoupper($row->class_attendance->class_schedule->subject_study->name_subject ?? '-') }}
                                    </div>
                                </td>
                                <td>
                                    <span
                                        class="badge {{ $row->status_attendance == 'hadir' ? 'bg-green-lt' : 'bg-red-lt' }}">
                                        {{ ucwords($row->status_attendance) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <x-datatable.empty colspan="5" />
                        @endforelse
                    </tbody>
                </template>

                {{-- TAB SUMMARY --}}
                <template x-if="currentTab == 'summary'">
                    <thead>
                        <tr>
                            @if ($filters['kelas'])
                                <th>Nama Siswa</th>
                                <th class="text-center">Total Hadir</th>
                            @else
                                <th>Kelas</th>
                                @foreach (config('const.attendance_status') as $status)
                                    @php $label = is_array($status) ? $status['label'] ?? $status['value'] : $status; @endphp
                                    <th class="text-center">{{ ucwords($label) }}</th>
                                @endforeach
                            @endif
                        </tr>
                    </thead>
                </template>
                <template x-if="currentTab == 'summary'">
                    <tbody>
                        @forelse ($this->summaryData as $data)
                            <tr wire:key="summary-{{ $loop->index }}">
                                @if ($filters['kelas'])
                                    <td>{{ $data->full_name }}</td>
                                    <td class="text-center"><span
                                            class="badge bg-primary">{{ $data->total_hadir }}</span></td>
                                @else
                                    <td><b>{{ strtoupper($data->name_class) }}</b></td>
                                    @foreach (config('const.attendance_status') as $status)
                                        @php $val = is_array($status) ? $status['value'] : $status; @endphp
                                        <td class="text-center">{{ $data["count_{$val}"] ?? 0 }}</td>
                                    @endforeach
                                @endif
                            </tr>
                        @empty
                            <x-datatable.empty colspan="10" />
                        @endforelse
                    </tbody>
                </template>
            </table>
        </div>

        {{-- Footer hanya tampil di tab list --}}
        <div x-show="currentTab == 'list'">
            {{ $this->rows->links() }}
        </div>

        <div style="z-index: 9999; position: fixed; bottom: 20px; right: 20px;"
            class="d-flex flex-column align-items-end gap-2">

            <div class="btn btn-blue shadow-lg" wire:loading.delay wire:target="tab, filters, search">
                <div class="d-flex align-items-center gap-2">
                    <div class="spinner-border spinner-border-sm" role="status"></div>
                    <span>Memproses Data...</span>
                </div>
            </div>

            <div class="btn btn-danger shadow-lg" wire:offline>
                <i class="las la-wifi-slash me-2"></i> Anda sedang offline.
            </div>
        </div>
    </div>

    {{-- CSS untuk Animasi (Opsional jika ingin pakai icon las la-sync-alt) --}}
    <style>
        .spin-animation {
            display: inline-block;
            animation: spin 1s linear infinite;
            font-size: 2.5rem;
            /* Membesarkan ikon */
            font-weight: bold;
            /* Membuat ikon lebih bold */
        }

        @keyframes spin {
            100% {
                transform: rotate(360deg);
            }
        }
    </style>
</div>
