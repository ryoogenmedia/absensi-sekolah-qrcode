<div x-data="{ printLoading: false }">
    <div class="card" wire:loading.class="opacity-50" style="transition: opacity 0.3s ease;">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Data Record / Daftar Kehadiran</h3>
            <button
                @click="async () => {
                printLoading = true;
                const url = await $wire.validateAndPrint();
                if (url) {
                    window.open(url, '_blank');
                }
                printLoading = false;
            }"
                :disabled="printLoading" class="btn btn-danger btn-sm">
                <template x-if="!printLoading">
                    <span><span class="las la-print me-2"></span>Cetak Data Record</span>
                </template>
                <template x-if="printLoading">
                    <span><span class="spinner-border spinner-border-sm me-2"></span>Validasi...</span>
                </template>
            </button>
        </div>

        <div class="table-responsive mb-0">
            <table class="table card-table table-bordered datatable">
                <thead>
                    <tr>
                        <th class="text-center">Kelas</th>
                        <th>Tanggal</th>
                        <th>Nama Siswa</th>
                        <th>Guru & Mapel</th>
                        <th>Status</th>
                    </tr>
                </thead>
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
            </table>
        </div>

        {{-- Footer dengan pagination --}}
        <div class="d-flex align-items-center">
            {{ $this->rows->links() }}
        </div>

        <div style="z-index: 9999; position: fixed; bottom: 20px; right: 20px;"
            class="d-flex flex-column align-items-end gap-2">

            <div class="btn btn-blue shadow-lg" wire:loading.delay wire:target="filters, search">
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
</div>
