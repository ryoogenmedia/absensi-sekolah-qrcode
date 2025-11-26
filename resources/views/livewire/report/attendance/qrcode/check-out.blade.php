<div>
    <x-slot name="title">Laporan Presensi Keluar</x-slot>

    <div class="row g-2 align-items-center mb-4">
        <div class="col">
            <div class="page-pretitle">
                Cetak Laporan Prensensi Keluar
            </div>
            <h2 class="page-title">
                Laporan Prensensi Keluar
            </h2>
        </div>

        <div class="col-auto ms-auto d-print-none">
            <div class="btn-list">
                <a href="{{ route('print-report.attendance.qrcode.check-out', ['kelas' => $this->filters['kelas'] ?? '', 'date_start' => $this->filters['startDate'] ?? '', 'date_end' => $this->filters['endDate'] ?? '']) }}"
                    target="_blank" class="btn btn-danger" class="btn btn-danger"><span
                        class="las la-print fs-1 me-2"></span>Cetak
                    Laporan Prensensi Keluar</a>
            </div>
        </div>
    </div>

    <x-alert />

    <div class="row mb-3 align-items-center justify-content-between">
        <div class="col-12 col-lg-8 d-flex">
            <div class="w-100">
                <x-datatable.search placeholder="Cari nama siswa..." />
            </div>

            <div class="w-100 ms-2 d-flex justify-content-between gap-2">
                <x-form.input wire:model.live="filters.startDate" name="filters.startDate" type="date"
                    form-group-class />

                <x-form.input wire:model.live="filters.endDate" name="filters.endDate" type="date"
                    form-group-class />

                <x-form.select wire:model.live="filters.kelas" name="filters.kelas" form-group-class>
                    <option value="">Semua Kelas</option>
                    @foreach ($this->class_rooms as $class_room)
                        <option wire:key="{{ $class_room->id }}" value="{{ $class_room->id }}">
                            {{ strtoupper($class_room->name_class) }}</option>
                    @endforeach
                </x-form.select>
            </div>

            <div class="w-50 ms-2">
                <x-datatable.filter.button target="check-in-filter" />
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
                        <th>Nama Siswa</th>

                        <th>NIS</th>

                        <th>Kelas</th>

                        <th>Waktu Keluar</th>

                        <th>Tanggal Presensi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($this->rows as $row)
                        <tr wire:key="row-{{ $row->id }}">
                            <td>{{ $row->student->full_name ?? '-' }}</td>

                            <td>{{ $row->student->nis ?? '-' }}</td>

                            <td>{{ $row->student->class_room->name_class ?? '-' }}</td>

                            <td>{{ $row->check_out_time ?? '-' }}</td>

                            <td>{{ $row->attendance_date ?? '-' }}</td>
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
