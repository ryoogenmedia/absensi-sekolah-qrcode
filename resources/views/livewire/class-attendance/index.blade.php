<div>
    <x-slot name="title">Presensi Kelas</x-slot>

    <x-slot name="pageTitle">Presensi Kelas</x-slot>

    <x-slot name="pagePretitle">Daftar Presensi Kelas</x-slot>

    <div class="row mb-3 align-items-center justify-content-between">
        <div class="col-12 col-lg-8 d-flex align-self-center">
            <div class="w-50">
                <x-form.select wire:model.lazy="filters.class_room" name="filters.class_room" form-group-class autofocus>
                    <option value="">SEMUA KELAS</option>

                    @foreach ($this->class_rooms as $class)
                        <option wire:key="{{ $class->id }}" value="{{ $class->id }}">{{ $class->name_class }}
                        </option>
                    @endforeach
                </x-form.select>
            </div>
            <div class="w-50 ms-2">
                <x-form.select wire:model.lazy="filters.name_day" name="filters.name_day" form-group-class>
                    <option value="">SEMUA HARI</option>

                    @foreach (config('const.name_days') as $name_day)
                        <option wire:key="{{ $name_day }}" value="{{ $name_day }}">{{ strtoupper($name_day) }}
                        </option>
                    @endforeach
                </x-form.select>
            </div>
        </div>
        <div class="col-auto ms-auto d-flex mt-lg-0 mt-3">
            <x-datatable.items-per-page />

            <button wire:click="muatUlang" class="btn py-1 ms-2"><span class="las la-redo-alt fs-1"></span></button>
        </div>
    </div>

    <div class="card" wire:loading.class.delay="card-loading" wire:offline.class="card-loading">
        <div class="table-responsive mb-0">
            <table class="table card-table table-bordered datatable">
                <thead>
                    <tr>
                        <th>Mata Pelajaran</th>

                        <th>Hari</th>

                        <th>Ruang Kelas</th>

                        <th>Jam Masuk</th>

                        <th>Jam Selesai</th>

                        <th style="width: 10px"></th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($this->rows as $row)
                        <tr wire:key="row-{{ $row->id }}">
                            <td>{{ $row->subject_study->name_subject ?? '-' }}</td>

                            <td>{{ strtoupper($row->day_name ?? '-') }}</td>

                            <td>{{ $row->class_room->name_class ?? '-' }}</td>

                            <td>{{ $row->start_time ?? '-' }}</td>

                            <td>{{ $row->end_time ?? '-' }}</td>

                            <td>
                                <a href="{{ route('class-attendance.detail', $row->id) }}" class="btn btn-green">Detail
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
