<div>
    <x-alert />

    <div class="row mb-3 align-items-center justify-content-between">
        <div class="col-12 col-lg-8 d-flex align-self-center">
            <div>
                <x-datatable.search placeholder="Cari nama / nis siswa..." />
            </div>
        </div>

        <div class="col-auto d-flex mt-lg-0 mt-3">
            <x-datatable.items-per-page />

            <button wire:click="muatUlang" class="btn reload-button py-1 ms-2">
                <span class="las la-redo-alt fs-1"></span>
            </button>
        </div>
    </div>

    <div class="presensi-header-base presensi-header-out mb-3">
        <i class="las la-door-closed"></i>
        <h2 class="mb-0">Presensi Keluar</h2>
    </div>

    <div class="open-filter mb-3 card px-2 filter-card">
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-lg-6">
                    <x-form.input wire:model.live="filters.start_date" name="filters.start_date" label="Tanggal Mulai"
                        type="date" />
                </div>

                <div class="col-12 col-lg-6">
                    <x-form.input wire:model.live="filters.end_date" name="filters.end_date" label="Tanggal Selesai"
                        type="date" />
                </div>
            </div>

            <div class="text-end mt-2">
                <button wire:click="resetFilters" class="btn btn-sm px-3" type="button">
                    <i class="las la-broom me-1"></i> Reset Filter
                </button>
            </div>
        </div>
    </div>

    <div wire:poll.30000ms class="card" wire:loading.class.delay="card-loading" wire:offline.class="card-loading">
        <div class="table-responsive mb-0">
            <table class="table card-table table-bordered datatable">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>NIS</th>
                        <th>Kelas</th>
                        <th>Tanggal Presensi</th>
                        <th>Waktu Keluar</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($this->rows as $row)
                        <tr wire:key="row-{{ $row->id }}">
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="avatar avatar-sm px-3 me-3"
                                        style="background-image: url({{ $row->student->photo ? $row->student->photoUrl() : $row->student->user->avatarUrl() }})"></span>

                                    <span>{{ $row->student->full_name }}</span>
                                </div>
                            </td>

                            <td>{{ $row->student->nis ?? '-' }}</td>
                            <td>{{ $row->student->class_room->name_class ?? '-' }}</td>
                            <td>{{ $row->attendance_date ?? '-' }}</td>
                            <td>{{ $row->check_out_time ?? '-' }}</td>
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
