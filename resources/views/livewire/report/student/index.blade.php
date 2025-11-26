<div>
    <x-slot name="title">Laporan Siswa</x-slot>

    <div class="row g-2 align-items-center mb-4">
        <div class="col">
            <div class="page-pretitle">
                Cetak Laporan Siswa
            </div>
            <h2 class="page-title">
                Laporan Siswa
            </h2>
        </div>

        <div class="col-auto ms-auto d-print-none">
            <div class="btn-list">
                <a href="{{ route('print-report.student', ['kelas' => $this->filters['kelas'] ?? '']) }}" target="_blank"
                    class="btn btn-danger" class="btn btn-danger"><span class="las la-print fs-1 me-2"></span>Cetak
                    Laporan Siswa</a>
            </div>
        </div>
    </div>

    <x-alert />

    <div class="row mb-3 align-items-center justify-content-between">
        <div class="col-12 col-lg-5 d-flex">
            <div class="w-100">
                <x-datatable.search placeholder="Cari nama siswa..." />
            </div>
            <div class="w-50 ms-2">
                <x-form.select wire:model.live="filters.kelas" name="filters.kelas" form-group-class>
                    <option value="">Semua Kelas</option>
                    @foreach ($this->class_rooms as $class_room)
                        <option wire:key="{{ $class_room->id }}" value="{{ $class_room->id }}">
                            {{ strtoupper($class_room->name_class) }}</option>
                    @endforeach
                </x-form.select>
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
                        <th>
                            <x-datatable.column-sort name="Siswa" wire:click="sortBy('full_name')"
                                :direction="$sorts['full_name'] ?? null" />
                        </th>

                        <th>
                            <x-datatable.column-sort name="NIS" wire:click="sortBy('nis')" :direction="$sorts['nis'] ?? null" />
                        </th>

                        <th>
                            <x-datatable.column-sort name="Jenis Kelamin" wire:click="sortBy('sex')"
                                :direction="$sorts['sex'] ?? null" />
                        </th>

                        <th>
                            <x-datatable.column-sort name="Nomor Ponsel" wire:click="sortBy('phone')"
                                :direction="$sorts['phone'] ?? null" />
                        </th>

                        <th>
                            <x-datatable.column-sort name="Agama" wire:click="sortBy('religion')" :direction="$sorts['religion'] ?? null" />
                        </th>

                        <th>
                            <x-datatable.column-sort name="Alamat" wire:click="sortBy('address')" :direction="$sorts['address'] ?? null" />
                        </th>

                        <th>Kelas</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($this->rows as $row)
                        <tr wire:key="row-{{ $row->id }}">
                            <td>{{ ucwords($row->full_name) ?? '-' }}</td>

                            <td><b>{{ $row->nis ?? '-' }}</b></td>

                            <td>{{ ucwords($row->sex) ?? '-' }}</td>

                            <td>{{ $row->phone ?? '-' }}</td>

                            <td>{{ ucwords($row->religion) ?? '-' }}</td>

                            <td>{{ $row->address ?? '-' }}, {{ $row->postal_code ?? '' }}</td>

                            <td class="text-center"><b>{{ $row->class_room->name_class ?? '-' }}</b></td>
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
