<div>
    <x-slot name="title">Tambah Jadwal kelas</x-slot>
    <x-slot name="pagePretitle">Menambah Jadwal Kelas</x-slot>
    <x-slot name="pageTitle">Tambah Jadwal Kelas</x-slot>
    <x-slot name="button">
        <x-datatable.button.back name="Kembali" :route="route('master.class-schedule.index')" />
    </x-slot>

    <x-alert />

    <form class="card" wire:submit.prevent="save" autocomplete="off">
        <div class="card-header">
            <h3 class="card-title">Form Tambah Jadwal</h3>
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
                            <option wire:key="class-{{ $class_room->id }}" value="{{ $class_room->id }}">
                                {{ strtoupper($class_room->name_class) }}
                            </option>
                        @endforeach
                    </x-form.select>

                    <x-form.select wire:model.live="mataPelajaran" name="mataPelajaran" label="Mata Pelajaran">
                        <option value="">- pilih mata pelajaran -</option>
                        @foreach ($this->subject_studies as $subject_study)
                            <option wire:key="subject-{{ $subject_study->id }}" value="{{ $subject_study->id }}">
                                {{ strtoupper($subject_study->name_subject) }}
                            </option>
                        @endforeach
                    </x-form.select>

                    <x-form.select wire:model="guru" name="guru" label="Guru">
                        <option value="">- pilih guru -</option>
                        @foreach ($this->teachers as $teacher)
                            <option wire:key="teacher-{{ $teacher->id }}" value="{{ $teacher->id }}">
                                {{ $teacher->name }} - {{ $teacher->nip }}
                            </option>
                        @endforeach
                    </x-form.select>

                    <x-form.select wire:model="hari" name="hari" label="Nama Hari">
                        <option value="">- pilih hari -</option>
                        @foreach (config('const.name_days_secound') as $name_day)
                            <option wire:key="day-{{ $name_day }}" value="{{ $name_day }}">
                                {{ strtoupper($name_day) }}
                            </option>
                        @endforeach
                    </x-form.select>
                </div>

                <div class="col-12 col-lg-6">
                    <x-form.select wire:model.live="sesi" name="sesi" label="Sesi Jadwal">
                        <option value="">- pilih sesi -</option>
                        @foreach (config('const.class_sessions') as $key => $session)
                            <option wire:key="session-{{ $key }}" value="{{ $key }}">
                                {{ $session['label'] }}
                            </option>
                        @endforeach
                    </x-form.select>

                    <div class="row">
                        <div class="col-lg-6 col-12">
                            <x-form.input wire:model="waktuMasuk" name="waktuMasuk" label="Waktu Masuk"
                                type="time" readonly />
                        </div>
                        <div class="col-lg-6 col-12">
                            <x-form.input wire:model="waktuKeluar" name="waktuKeluar" label="Waktu Keluar"
                                type="time" readonly />
                        </div>
                    </div>
                    <x-form.textarea wire:model="keterangan" name="keterangan" label="Keterangan"
                        style="height: 140px;" />
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
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($prevClassRoomSchedule as $schedule)
                                    <tr wire:key="prev-{{ $schedule->id }}">
                                        <td>{{ strtoupper($schedule->subject_study->name_subject) }}</td>
                                        <td>{{ $schedule->teacher->name }}</td>
                                        <td>{{ strtoupper($schedule->day_name) }}</td>
                                        <td>{{ $schedule->start_time }} - {{ $schedule->end_time }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        <div class="card-footer text-end">
            <button type="reset" class="btn btn-link link-secondary">Reset</button>
            <x-datatable.button.save target="save" />
        </div>
    </form>
</div>
