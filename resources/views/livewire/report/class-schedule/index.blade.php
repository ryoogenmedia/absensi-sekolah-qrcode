<div>
    <x-slot name="title">Laporan Jadwal Kelas</x-slot>

    <div class="row g-2 align-items-center mb-4">
        <div class="col">
            <div class="page-pretitle">
                Cetak Laporan Jadwal Kelas
            </div>
            <h2 class="page-title">
                Laporan Jadwal Kelas
            </h2>
        </div>

        <div class="col-auto ms-auto d-print-none">
            <div class="btn-list">
                <a href="{{ route('print-report.class-schedule', ['kelas' => $this->filters['kelas'] ?? '', 'start_time' => $this->filters['start_time'] ?? '', 'end_time' => $this->filters['end_time'] ?? '']) }}"
                    target="_blank" class="btn btn-danger" class="btn btn-danger"><span
                        class="las la-print fs-1 me-2"></span>Cetak
                    Laporan Jadwal Kelas</a>
            </div>
        </div>
    </div>

    <x-alert />

    <div class="row mb-3 align-items-center justify-content-between">
        <div class="col-12 col-lg-8 d-flex align-self-center">
            <div class="w-50">
                <x-datatable.search placeholder="Cari nama kelas/guru/mapel..." />
            </div>

            <div class="d-flex w-100 ms-2 gap-2">
                <x-form.select wire:model.live="filters.class_room" name="filters.class_room" form-group-class>
                    <option value="">Semua Kelas</option>
                    @foreach ($this->class_rooms as $class_room)
                        <option wire:key="{{ $class_room->id }}" value="{{ $class_room->id }}">
                            {{ $class_room->name_class }}</option>
                    @endforeach
                </x-form.select>

                <x-form.input wire:model.live="filters.start_time" name="filters.start_time" type="time"
                    form-group-class />

                <x-form.input wire:model.live="filters.end_time" name="filters.end_time" type="time"
                    form-group-class />
            </div>
        </div>
        <div class="col-auto ms-auto d-flex mt-lg-0 mt-3">
            <x-datatable.items-per-page />
        </div>
    </div>

    <div class="card" wire:loading.class.delay="card-loading" wire:offline.class="card-loading">
        <div class="table-responsive mb-0">
            <table class="table card-table table-bordered datatable">
                <thead>
                    <tr>
                        <th>Kelas</th>

                        <th>Guru Pengajar</th>

                        <th>Hari</th>

                        <th>Jam Mulai</th>

                        <th>Jam Selesai</th>

                        <th>Mata Pelajaran</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($this->rows as $row)
                        <tr wire:key="row-{{ $row->id }}">
                            <td class="text-center">{{ $row->class_room->name_class ?? '-' }}</td>

                            <td>{{ $row->teacher->name ?? '-' }}</td>

                            <td>{{ strtoupper($row->day_name ?? '-') }}</td>

                            <td>{{ $row->start_time ?? '-' }}</td>

                            <td>{{ $row->end_time ?? '-' }}</td>

                            <td>{{ strtoupper($row->subject_study->name_subject ?? '-') }}</td>
                        </tr>
                    @empty
                        <x-datatable.empty colspan="10" />
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $this->rows->links() }}
    </div>
</div>
