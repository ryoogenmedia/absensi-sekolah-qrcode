<div>
    <div class="card" wire:loading.class="opacity-50" style="transition: opacity 0.3s ease;">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Ringkasan Data Presensi</h3>
            <a href="{{ route('print-report.attendance.class.summary') }}?search={{ $filters['search'] }}&kelas={{ $filters['kelas'] }}&startDate={{ $filters['startDate'] }}&endDate={{ $filters['endDate'] }}"
                target="_blank" class="btn btn-danger btn-sm">
                <span class="las la-print me-2"></span>Cetak Ringkasan
            </a>
        </div>

        <div class="table-responsive mb-0">
            <table class="table card-table table-bordered datatable">
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
                <tbody>
                    @forelse ($this->summaryData as $data)
                        <tr wire:key="summary-{{ $loop->index }}">
                            @if ($filters['kelas'])
                                <td>{{ $data->full_name }}</td>
                                <td class="text-center"><span class="badge bg-primary">{{ $data->total_hadir }}</span>
                                </td>
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
            </table>
        </div>

        <div style="z-index: 9999; position: fixed; bottom: 20px; right: 20px;"
            class="d-flex flex-column align-items-end gap-2">

            <div class="btn btn-blue shadow-lg" wire:loading.delay wire:target="filters">
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
