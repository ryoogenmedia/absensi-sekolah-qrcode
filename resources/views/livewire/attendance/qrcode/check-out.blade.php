<div>
    <x-slot name="title">Presensi Keluar</x-slot>

    <x-slot name="pageTitle">Presensi Keluar</x-slot>

    <x-slot name="pagePretitle">Kelola Data Presensi Keluar</x-slot>

    <x-alert />

    <x-modal.delete-confirmation />

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
            </div>

            <div class="w-50 ms-2">
                <x-datatable.filter.button target="check-out-filter" />
            </div>
        </div>
        <div class="col-auto ms-auto d-flex mt-lg-0 mt-3">
            <x-datatable.items-per-page />

            <x-datatable.bulk.dropdown>
                <div class="dropdown-menu dropdown-menu-end datatable-dropdown">
                    <button data-bs-toggle="modal" data-bs-target="#delete-confirmation" class="dropdown-item"
                        type="button">
                        <i class="las la-trash me-3"></i>

                        <span>Hapus</span>
                    </button>
                </div>
            </x-datatable.bulk.dropdown>

            <button wire:click="muatUlang" class="btn py-1 ms-2"><span class="las la-redo-alt fs-1"></span></button>
        </div>
    </div>

    <x-datatable.filter.card id="check-out-filter">
        <div class="row">
            <div class="col-12 col-lg-4">
                <x-form.input wire:model.live="filters.waktuKeluar" name="filters.waktuKeluar" label="Waktu Keluar"
                    type="time" />
            </div>

            <div class="col-12 col-lg-4">
                <x-form.input wire:model.live="filters.nis" name="filters.nis" label="NIS" type="text"
                    placeholder="Masukkan NIS Siswa" />
            </div>

            <div class="col-12 col-lg-4">
                <x-form.select wire:model.live="filters.kelas" name="filters.kelas" label="Ruang Kelas">
                    <option value="">Semua Kelas</option>
                    @foreach ($this->class_rooms as $class_room)
                        <option wire:key="{{ $class_room->id }}" value="{{ $class_room->id }}">
                            {{ strtoupper($class_room->name_class) }}</option>
                    @endforeach
                </x-form.select>
            </div>
        </div>
    </x-datatable.filter.card>

    <div class="card" wire:loading.class.delay="card-loading" wire:offline.class="card-loading">
        <div class="table-responsive mb-0">
            <table class="table card-table table-bordered datatable">
                <thead>
                    <tr>
                        <th class="w-1">
                            <x-datatable.bulk.check wire:model.lazy="selectPage" />
                        </th>

                        <th>Nama Siswa</th>

                        <th>NIS</th>

                        <th>Kelas</th>

                        <th>Waktu Keluar</th>

                        <th>Tanggal Presensi</th>
                    </tr>
                </thead>

                <tbody>
                    @if ($selectPage)
                        <tr>
                            <td colspan="10" class="bg-orange-lt rounded-0">
                                @if (!$selectAll)
                                    <div class="text-orange">
                                        <span>Anda telah memilih <strong>{{ $this->rows->total() }}</strong> presensi
                                            keluar,
                                            apakah
                                            Anda mau memilih semua <strong>{{ $this->rows->total() }}</strong>
                                            presensi keluar?</span>

                                        <button wire:click="selectedAll" class="btn btn-sm ms-2">
                                            Pilih Semua Data presensi keluar
                                        </button>
                                    </div>
                                @else
                                    <span class="text-pink">Anda sekarang memilih semua
                                        <strong>{{ count($this->selected) }}</strong> presensi keluar.
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endif

                    @forelse ($this->rows as $row)
                        <tr wire:key="row-{{ $row->id }}">
                            <td>
                                <x-datatable.bulk.check wire:model.lazy="selected" value="{{ $row->id }}" />
                            </td>

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
