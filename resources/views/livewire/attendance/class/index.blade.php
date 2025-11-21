<div>
    <x-slot name="title">Presensi Kelas</x-slot>

    <x-slot name="pageTitle">Presensi Kelas</x-slot>

    <x-slot name="pagePretitle">Kelola Data Presensi Kelas</x-slot>

    <x-alert />

    <x-modal.delete-confirmation />

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
                        <th class="w-1">
                            <x-datatable.bulk.check wire:model.lazy="selectPage" />
                        </th>

                        <th class="text-center">Kelas</th>

                        <th>Waktu Presensi</th>

                        @foreach (config('const.attendance_status') as $status)
                            <th class="text-center">{{ $status }}</th>
                        @endforeach

                        <th style="width: 10px"></th>
                    </tr>
                </thead>

                <tbody>
                    @if ($selectPage)
                        <tr>
                            <td colspan="10" class="bg-orange-lt rounded-0">
                                @if (!$selectAll)
                                    <div class="text-orange">
                                        <span>Anda telah memilih <strong>{{ $this->rows->total() }}</strong> presensi
                                            kelas,
                                            apakah
                                            Anda mau memilih semua <strong>{{ $this->rows->total() }}</strong>
                                            presensi kelas?</span>

                                        <button wire:click="selectedAll" class="btn btn-sm ms-2">
                                            Pilih Semua Data presensi kelas
                                        </button>
                                    </div>
                                @else
                                    <span class="text-pink">Anda sekarang memilih semua
                                        <strong>{{ count($this->selected) }}</strong> presensi kelas.
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

                            <td class="text-center"><b>{{ $row->class_room->name_class ?? '' }}</b></td>

                            <td>
                                {{ $row->created_at->translatedFormat('l, d F Y') ?? '-' }}
                            </td>

                            @foreach (config('const.attendance_status') as $status)
                                <td class="text-center">
                                    {{ $row->student_attendances->count() >= 1 ? $row->student_attendances->where('status_attendance', $status)->count() : 0 }}
                                </td>
                            @endforeach

                            <td>
                                <a href="{{ route('attendance.class.detail', $row->id) }}" class="btn">Lihat Detail
                                    Presensi</a>
                            </td>
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
