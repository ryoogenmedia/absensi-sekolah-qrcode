<div>
    <x-slot name="title">Presensi Anak Anda</x-slot>

    <x-slot name="pageTitle">Presensi Anak Anda</x-slot>

    <x-slot name="pagePretitle">Kelola Presensi Anak Anda</x-slot>
    <x-alert />

    <x-modal.delete-confirmation />

    <x-alert.info message="Pilih Mata Pelajaran untuk melihat data presensi Anda"
        detail="Data presensi yang ditampilkan adalah data presensi sesuai dengan mata pelajaran yang Anda pilih." />

    <div class="row mb-3 align-items-center justify-content-between">
        <div class="col-12 col-lg-6 d-flex align-self-center">
            <x-form.select wire:model.lazy="classScheduleId" name="classScheduleId" form-group-class>
                <option value="">pilih mapel</option>
                @foreach ($this->class_schedules as $schedule)
                    <option wire:key="{{ $schedule->id }}" value="{{ $schedule->id }}">
                        {{ strtoupper($schedule->subject_study->name_subject) }} - {{ strtoupper($schedule->day_name) }}
                    </option>
                @endforeach
            </x-form.select>
        </div>
    </div>

    @if (!$classSchedule && !$classScheduleId)
        <x-alert.warning message="Mata Pelajaran Belum Dipilih"
            detail="Silahkan pilih mata pelajaran untuk melihat data presensi Anda." />
    @else
        <div class="card mb-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-5 col-12 mb-lg-0 mb-2 align-self-center">
                        <table class="table table-bordered">
                            <tr>
                                <td>Nama Mata Pelajaran</td>
                                <td>{{ $classSchedule->subject_study->name_subject ?? '-' }}</td>
                            </tr>

                            <tr>
                                <td>Hari Mata Pelajaran</td>
                                <td>{{ strtoupper($classSchedule->day_name ?? '-') }}</td>
                            </tr>

                            <tr>
                                <td>Jam Matapelajaran Masuk</td>
                                <td>{{ $classSchedule->start_time ?? '-' }} - {{ $classSchedule->end_time ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>

                    <div class="col-lg-7 col-12 align-self-center">
                        <div class="d-flex flex-lg-row flex-column gap-3 w-full">
                            <div class="card bg-blue" style="width: 100%">
                                <div class="card-body">
                                    <div class="w-full">
                                        <p class="text-white" style="font-size: 18px; font-weight: 520">Jumlah Pertemuan
                                        </p>
                                        <h1 class="text-white" style="font-size: 30px">
                                            {{ $totalMeeting }}
                                        </h1>
                                    </div>
                                </div>
                            </div>

                            <div class="card bg-green" style="width: 100%">
                                <div class="card-body">
                                    <div class="w-full">
                                        <p class="text-white" style="font-size: 18px; font-weight: 520">Jumlah Kehadiran
                                        </p>
                                        <h1 class="text-white" style="font-size: 30px">
                                            {{ $totalPresence }}
                                        </h1>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="card mt-3" wire:loading.class.delay="card-loading" wire:offline.class="card-loading">
        <div class="table-responsive mb-0">
            <table class="table card-table table-bordered datatable">
                <thead>
                    <tr>
                        <th class="text-center" colspan="{{ $totalMeeting }}">
                            Jumlah Pertemuan</th>
                    </tr>

                    <tr>
                        @for ($i = 1; $i <= $totalMeeting; $i++)
                            <th class="text-center w-1">Ke-{{ $i }}</th>
                        @endfor
                    </tr>
                </thead>
                <tbody>
                    @forelse ($this->students as $row)
                        <tr wire:key="row-{{ $row->id }}">
                            @foreach ($row->student_attendances as $attendance)
                                @if ($attendance->class_attendance->class_schedule_id == $this->classScheduleId)
                                    <td class="text-center">
                                        <span style="width: 30px; height: 30px" @class([
                                            'badge fs-5 d-flex justify-content-center align-items-center rounded-md text-white',
                                            'bg-green' => $attendance->status_attendance === 'hadir',
                                            'bg-red' => $attendance->status_attendance === 'alpa',
                                            'bg-yellow' => $attendance->status_attendance === 'izin',
                                            'bg-cyan' => $attendance->status_attendance === 'sakit',
                                        ])>
                                            {{ match ($attendance->status_attendance) {
                                                'hadir' => 'H',
                                                'alpa' => 'A',
                                                'izin' => 'I',
                                                'sakit' => 'S',
                                                default => '',
                                            } }}
                                        </span>
                                    </td>
                                @endif
                            @endforeach

                            @php
                                $attendances = $row->student_attendances->filter(function ($attendance) {
                                    return $attendance->class_attendance->class_schedule_id == $this->classScheduleId;
                                });
                                $attendanceCount = $attendances->count();
                                $emptyCells = $totalPresence - $attendanceCount;
                            @endphp

                            @if ($attendanceCount == 0)
                                <x-datatable.empty colspan="10" />
                            @endif
                        </tr>
                    @empty
                        <x-datatable.empty colspan="10" />
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        Livewire.on('openDeleteModal', () => {
            const modal = new bootstrap.Modal(document.getElementById('delete-confirmation'));
            modal.show();
        });
    </script>
@endpush
