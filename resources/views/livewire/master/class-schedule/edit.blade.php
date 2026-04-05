<div>
    <x-slot name="title">Sunting Jadwal kelas</x-slot>

    <x-slot name="pagePretitle">Menyunting Jadwal Kelas</x-slot>

    <x-slot name="pageTitle">Sunting Jadwal Kelas</x-slot>

    <x-slot name="button">
        <x-datatable.button.back name="Kembali" :route="route('master.class-schedule.index')" />
    </x-slot>

    <x-alert />

    <form class="card" wire:submit.prevent="edit" autocomplete="off">
        <div class="card-header">
            Sunting jadwal kelas
        </div>

        @if (!empty($previousSchedule) && count($previousSchedule) > 0)
            <div class="card-body">
                <div class="alert alert-important alert-warning shadow-sm">
                    <div class="fw-bold">Perhatian: Waktu yang Anda masukkan bentrok dengan jadwal berikut:</div>
                </div>
                <div class="table-responsive border rounded">
                    <table class="table card-table table-bordered datatable">
                        <thead>
                            <tr>
                                <th>Mata Pelajaran</th>
                                <th>Guru</th>
                                <th>Hari</th>
                                <th>Waktu</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($previousSchedule as $schedule)
                                <tr wire:key="conflict-{{ $schedule->id }}">
                                    <td>{{ strtoupper($schedule->subject_study->name_subject) }}</td>
                                    <td>{{ $schedule->teacher->name }}</td>
                                    <td>{{ strtoupper($schedule->day_name) }}</td>
                                    <td class="text-danger fw-bold">{{ $schedule->start_time }} -
                                        {{ $schedule->end_time }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <div class="card-body">
            <div class="row">
                <div class="col-12 col-lg-6">
                    <x-form.select wire:model.live="kelas" name="kelas" label="Kelas">
                        <option value="">- pilih kelas -</option>
                        @foreach ($this->class_rooms as $class_room)
                            <option wire:key="{{ $class_room->id }}" value="{{ $class_room->id }}">
                                {{ strtoupper($class_room->name_class) }}</option>
                        @endforeach
                    </x-form.select>

                    <x-form.select wire:model.live="mataPelajaran" name="mataPelajaran" label="Mata Pelajaran">
                        <option value="">- pilih mata pelajaran -</option>
                        @foreach ($this->subject_studies as $subject_study)
                            <option wire:key="{{ $subject_study->id }}" value="{{ $subject_study->id }}">
                                {{ strtoupper($subject_study->name_subject) }}</option>
                        @endforeach
                    </x-form.select>

                    <x-form.select wire:model="guru" name="guru" label="Guru">
                        <option value="">- pilih guru -</option>
                        @foreach ($this->teachers as $teacher)
                            <option wire:key="{{ $teacher->id }}" value="{{ $teacher->id }}">{{ $teacher->name }} -
                                {{ $teacher->nip }}
                            </option>
                        @endforeach
                    </x-form.select>

                    <x-form.select wire:model="hari" name="hari" label="Nama Hari">
                        <option value="">- pilih hari -</option>
                        @foreach (config('const.name_days') as $name_day)
                            <option wire:key="{{ $name_day }}" value="{{ $name_day }}">
                                {{ strtoupper($name_day) }}</option>
                        @endforeach
                    </x-form.select>
                </div>

                <div class="col-12 col-lg-6">
                    <div class="row">
                        <div class="col-lg-6 col-12">
                            <x-form.input wire:model="waktuMasuk" name="waktuMasuk" label="Waktu Masuk / Mulai"
                                type="time" />
                        </div>

                        <div class="col-lg-6 col-12">
                            <x-form.input wire:model="waktuKeluar" name="waktuKeluar" label="Waktu Keluar / Selesai"
                                type="time" />
                        </div>
                    </div>

                    <x-form.textarea wire:model="keterangan" name="keterangan" label="Keterangan" style="height: 210px;"
                        placeholder="Masukkan keterangan seperti informasi / terkait materi dll..." />
                </div>
            </div>
        </div>

        @if (!empty($prevClassRoomSchedule) && count($prevClassRoomSchedule) > 0)
            <div class="card-body">
                <div class="card bg-light">
                    <div class="card-header bg-white">
                        <div class="bg-primary text-white px-3 py-2 rounded">
                            <h3 class="card-title">Daftar Jadwal di Kelas:
                                <b>{{ $this->class_rooms->find($this->kelas)->name_class ?? '' }}</b>
                            </h3>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table card-table table-bordered datatable">
                            <thead>
                                <tr>
                                    <th>Mata Pelajaran</th>
                                    <th>Guru</th>
                                    <th>Hari</th>
                                    <th>Waktu</th>
                                    <th style="width: 100px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($prevClassRoomSchedule as $schedule)
                                    @if ($editingScheduleId === $schedule->id)
                                        {{-- Mode Edit --}}
                                        <tr wire:key="edit-{{ $schedule->id }}">
                                            <td>
                                                <select wire:model="editingSchedule.mataPelajaran"
                                                    class="form-control form-control-sm">
                                                    @foreach ($this->subject_studies as $subject)
                                                        <option value="{{ $subject->id }}">
                                                            {{ strtoupper($subject->name_subject) }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <select wire:model="editingSchedule.guru"
                                                    class="form-control form-control-sm">
                                                    @foreach ($this->teachers as $teacher)
                                                        <option value="{{ $teacher->id }}">{{ $teacher->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <select wire:model="editingSchedule.hari"
                                                    class="form-control form-control-sm">
                                                    @foreach (config('const.name_days') as $day)
                                                        <option value="{{ $day }}">{{ strtoupper($day) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    <input type="time" wire:model="editingSchedule.waktuMasuk"
                                                        class="form-control form-control-sm">
                                                    <input type="time" wire:model="editingSchedule.waktuKeluar"
                                                        class="form-control form-control-sm">
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    <button type="button" wire:click="updateSchedule"
                                                        class="btn btn-xs btn-success">
                                                        <span class="las la-check"></span>
                                                    </button>
                                                    <button type="button" wire:click="cancelEdit"
                                                        class="btn btn-xs btn-secondary">
                                                        <span class="las la-times"></span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @else
                                        {{-- Mode View --}}
                                        <tr wire:key="prev-{{ $schedule->id }}">
                                            <td>{{ strtoupper($schedule->subject_study->name_subject) }}</td>
                                            <td>{{ $schedule->teacher->name }}</td>
                                            <td>{{ strtoupper($schedule->day_name) }}</td>
                                            <td>{{ $schedule->start_time }} - {{ $schedule->end_time }}</td>
                                            <td>
                                                <button type="button" wire:click="startEdit({{ $schedule->id }})"
                                                    class="btn btn-xs btn-primary">
                                                    <span class="las la-edit"></span>
                                                </button>
                                            </td>
                                        </tr>
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">Tidak ada jadwal lain untuk
                                            kelas ini</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        <div class="card-footer">
            <div class="btn-list justify-content-end">
                <button type="reset" class="btn">Reset</button>

                <x-datatable.button.save target="edit" />
            </div>
        </div>
    </form>
</div>
