<div>
    <x-slot name="title">Jadwal Kelas</x-slot>

    <x-slot name="pageTitle">Jadwal Kelas</x-slot>

    <x-slot name="pagePretitle">Kelola Data Jadwal Kelas</x-slot>

    <x-slot name="button">
        <x-datatable.button.add name="Tambah Jadwal Kelas" :route="route('master.class-schedule.create')" />
    </x-slot>

    <x-alert />

    <x-modal.delete-confirmation />

    <x-modal :show="$showModalExcel" size="md">
        <form wire:submit.prevent="importExcel" autocomplete="off">
            <div class="modal-header">
                <h5 class="modal-title">Import File Excel Jadwal Kelas</h5>
                <button wire:click='closeModalExcel' type="button" class="btn-close" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <x-form.input wire:model.live.debounce.250ms="fileExcel" name="fileExcel" label="File Excel"
                    type="file" placeholder="Masukkan file excel"
                    accept=".xlsx,.xls,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel" />

                <p>unduh template import excel jadwal kelas <a
                        href="/template/jadwal-kelas-template-excel.xlsx">disini</a></p>
            </div>

            <div class="modal-footer">
                <div class="btn-list justify-content-end">
                    <button wire:click="resetForm" type="reset" class="btn">Reset</button>

                    <x-datatable.button.save target="importExcel" name="Import Excel" class="btn btn-green" />
                </div>
            </div>
        </form>
    </x-modal>

    <div class="row mb-3 align-items-center justify-content-between">
        <div class="col-12 col-lg-8 d-flex align-self-center">
            <div>
                <x-datatable.search placeholder="Cari nama kelas/guru/mapel..." />
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

                    <button class="dropdown-item" type="button" wire:click="openModalExcel">
                        <i class="las la-file-excel me-3"></i>

                        <span>Import Excel</span>
                    </button>

                    <button class="dropdown-item" type="button" wire:click="exportExcel">
                        <i class="las la-file-excel me-3"></i>

                        <span>Export Excel</span>
                    </button>
                </div>
            </x-datatable.bulk.dropdown>

            <button wire:click="muatUlang" class="btn py-1 ms-2"><span class="las la-redo-alt fs-1"></span></button>
        </div>
    </div>

    <div class="open-filter mb-3 card px-2">
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-lg-6">
                    <x-form.select wire:model.live="filters.class_room" name="filters.class_room" label="Ruang Kelas">
                        <option value="">SEMUA KELAS</option>
                        @foreach ($this->class_rooms as $class_room)
                            <option wire:key="{{ $class_room->id }}" value="{{ $class_room->id }}">
                                {{ $class_room->name_class }}</option>
                        @endforeach
                    </x-form.select>

                    <x-form.input wire:model.live="filters.start_time" name="filters.start_time" type="time"
                        label="Jam Awal" />
                </div>

                <div class="col-12 col-lg-6">
                    <x-form.select wire:model.live="filters.subject_study" name="filters.subject_study"
                        label="Mata Pelajaran">
                        <option value="">SEMUA MAPEL</option>
                        @foreach ($this->subject_studies as $subject_study)
                            <option wire:key="{{ $subject_study->id }}" value="{{ $subject_study->id }}">
                                {{ strtoupper($subject_study->name_subject) }}</option>
                        @endforeach
                    </x-form.select>

                    <x-form.input wire:model.live="filters.end_time" name="filters.end_time" type="time"
                        label="Jam Akhir" />
                </div>
            </div>
        </div>
    </div>

    <div class="card" wire:loading.class.delay="card-loading" wire:offline.class="card-loading">
        <div class="table-responsive mb-0">
            <table class="table card-table table-bordered datatable">
                <thead>
                    <tr>
                        <th class="w-1">
                            <x-datatable.bulk.check wire:model.lazy="selectPage" />
                        </th>

                        <th>Kelas</th>

                        <th>Guru Pengajar</th>

                        <th>Hari</th>

                        <th>Jam Mulai</th>

                        <th>Jam Selesai</th>

                        <th>Mata Pelajaran</th>

                        <th style="width: 10px"></th>
                    </tr>
                </thead>

                <tbody>
                    @if ($selectPage)
                        <tr>
                            <td colspan="10" class="bg-orange-lt rounded-0">
                                @if (!$selectAll)
                                    <div class="text-orange">
                                        <span>Anda telah memilih <strong>{{ $this->rows->total() }}</strong> jadwal
                                            kelas,
                                            apakah
                                            Anda mau memilih semua <strong>{{ $this->rows->total() }}</strong>
                                            jadwal kelas?</span>

                                        <button wire:click="selectedAll" class="btn btn-sm ms-2">
                                            Pilih Semua Data Jadwal Kelas
                                        </button>
                                    </div>
                                @else
                                    <span class="text-pink">Anda sekarang memilih semua
                                        <strong>{{ count($this->selected) }}</strong> jadwal kelas.
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

                            <td class="text-center">{{ $row->class_room->name_class ?? '-' }}</td>

                            <td>{{ $row->teacher->name ?? '-' }}</td>

                            <td>{{ strtoupper($row->day_name ?? '-') }}</td>

                            <td>{{ $row->start_time ?? '-' }}</td>

                            <td>{{ $row->end_time ?? '-' }}</td>

                            <td>{{ strtoupper($row->subject_study->name_subject ?? '-') }}</td>

                            <td>
                                <div class="d-flex">
                                    <div class="ms-auto">
                                        <a class="btn btn-sm"
                                            href="{{ route('master.class-schedule.edit', $row->id) }}">
                                            Sunting
                                        </a>
                                    </div>
                                </div>
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
