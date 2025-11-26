<div>
    <x-slot name="title">Laporan Presensi Kelas</x-slot>

    <div class="row g-2 align-items-center mb-4">
        <div class="col">
            <div class="page-pretitle">
                Cetak Laporan Presensi Kelas
            </div>
            <h2 class="page-title">
                Laporan Presensi Kelas
            </h2>
        </div>

        <div class="col-auto ms-auto d-print-none">
            <div class="btn-list">
                <a href="{{ route('print-report.attendance.class', ['kelas' => $this->filters['kelas'] ?? '', 'start_date' => $this->filters['startDate'] ?? '', 'end_date' => $this->filters['endDate'] ?? '']) }}"
                    target="_blank" class="btn btn-danger" class="btn btn-danger"><span
                        class="las la-print fs-1 me-2"></span>Cetak
                    Laporan Presensi Kelas</a>
            </div>
        </div>
    </div>

    <x-alert />

    <div class="row mb-3 align-items-center justify-content-between">
        <div class="col-12 col-lg-7 d-flex">
            <div class="w-100">
                <x-datatable.search placeholder="Cari nama kelas..." />
            </div>

            <div class="w-100 ms-2">
                <x-form.select wire:model.live="filters.kelas" name="filters.kelas" form-group-class>
                    <option value="">Semua Kelas</option>
                    @foreach ($this->class_rooms as $class_room)
                        <option wire:key="{{ $class_room->id }}" value="{{ $class_room->id }}">
                            {{ strtoupper($class_room->name_class) }}</option>
                    @endforeach
                </x-form.select>
            </div>

            <div class="w-35 ms-2">
                <x-datatable.filter.button target="attendance-class-filters" />
            </div>
        </div>
        <div class="col-auto ms-auto d-flex mt-lg-0 mt-3">
            <x-datatable.items-per-page />
        </div>
    </div>

    <x-datatable.filter.card id="attendance-class-filters">
        <div class="row">
            <div class="col-12 col-lg-6">
                <x-form.input wire:model.live="filters.startDate" name="filters.startDate" label="Tanggal Mulai"
                    type="date" />
            </div>
            <div class="col-12 col-lg-6">
                <x-form.input wire:model.live="filters.endDate" name="filters.endDate" label="Tanggal Seleai"
                    type="date" />
            </div>
        </div>
    </x-datatable.filter.card>

    <div class="card" wire:loading.class.delay="card-loading" wire:offline.class="card-loading">
        <div class="table-responsive mb-0">
            <table class="table card-table table-bordered datatable">
                <thead>
                    <tr>
                        <th class="text-center">Kelas</th>

                        <th>Tanggal Presensi</th>

                        <th>Nama Presensi</th>

                        <th>Guru Pengajar</th>

                        <th>Mata Pelajaran</th>

                        <th>Status Presensi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($this->rows as $row)
                        <tr wire:key="row-{{ $row->id }}">
                            <td class="text-center"><b>{{ $row->class_attendance->class_room->name_class ?? '' }}</b>
                            </td>

                            <td>
                                {{ $row->class_attendance->created_at->translatedFormat('l, d F Y') ?? '-' }}
                            </td>

                            <td>{{ $row->student->full_name ?? '-' }}</td>

                            <td>{{ $row->class_attendance->class_schedule->teacher->name ?? '-' }}</td>

                            <td>{{ strtoupper($row->class_attendance->class_schedule->subject_study->name_subject ?? '-') }}
                            </td>

                            <td>{{ ucwords($row->status_attendance) ?? '-' }}</td>
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
