<div>
    <x-slot name="title">Laporan Absensi Anak</x-slot>

    <div class="row g-2 align-items-center mb-4">
        <div class="col">
            <div class="page-pretitle">Laporan Lengkap</div>
            <h2 class="page-title">Absensi: {{ $student->full_name }}</h2>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body border-bottom py-3">
            <div class="d-flex align-items-center justify-content-between">
                <div class="text-muted">
                    Filter Periode:
                </div>
                <div class="d-flex gap-2">
                    <select wire:model.live="month" class="form-select w-auto">
                        @foreach ($months as $m => $name)
                            <option value="{{ $m }}">{{ $name }}</option>
                        @endforeach
                    </select>
                    <select wire:model.live="year" class="form-select w-auto">
                        @foreach ($years as $y)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="card-body bg-light-soft">
            <div class="row g-3">
                <div class="col-md-6 col-xl-3">
                    <div class="card card-sm bg-primary-lt">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="bg-primary text-white avatar"><i class="las la-user-check fs-2"></i></span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium">Hadir (Kelas)</div>
                                    <div class="text-muted">{{ collect($classAttendance)->flatten()->where('status_attendance', 'hadir')->count() }} Kali</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card card-sm bg-green-lt">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="bg-green text-white avatar"><i class="las la-sign-in-alt fs-2"></i></span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium">Check-In Harian</div>
                                    <div class="text-muted">{{ collect($dailyAttendance)->whereNotNull('check_in')->count() }} Hari</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card card-sm bg-red-lt">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="bg-red text-white avatar"><i class="las la-user-times fs-2"></i></span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium">Alpa (Kelas)</div>
                                    <div class="text-muted">{{ collect($classAttendance)->flatten()->where('status_attendance', 'alpa')->count() }} Kali</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card card-sm bg-yellow-lt">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="bg-yellow text-white avatar"><i class="las la-notes-medical fs-2"></i></span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium">Izin/Sakit</div>
                                    <div class="text-muted">{{ collect($classAttendance)->flatten()->whereIn('status_attendance', ['izin', 'sakit'])->count() }} Kali</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Kalender Absensi {{ $months[$month] }} {{ $year }}</h3>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter table-bordered card-table">
                        <thead>
                            <tr>
                                <th style="width: 15%">Tanggal</th>
                                <th style="width: 25%">Harian (Masuk/Pulang)</th>
                                <th>Kehadiran Mata Pelajaran</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($dailyAttendance as $date => $daily)
                                @php 
                                    $d = \Carbon\Carbon::parse($date);
                                    $isWeekend = $d->isWeekend();
                                @endphp
                                <tr class="{{ $isWeekend ? 'bg-light-soft' : '' }}">
                                    <td>
                                        <div class="font-weight-medium">{{ $d->format('d M Y') }}</div>
                                        <div class="text-muted small">{{ $d->translatedDayName }}</div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column gap-1">
                                            @if($daily['check_in'])
                                                <span class="badge bg-green-lt">
                                                    <i class="las la-clock me-1"></i> Masuk: {{ $daily['check_in']->check_in_time }}
                                                </span>
                                            @else
                                                <span class="badge bg-secondary-lt">-</span>
                                            @endif

                                            @if($daily['check_out'])
                                                <span class="badge bg-blue-lt">
                                                    <i class="las la-clock me-1"></i> Pulang: {{ $daily['check_out']->check_out_time }}
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if(isset($classAttendance[$date]))
                                            <div class="d-flex flex-wrap gap-2">
                                                @foreach($classAttendance[$date] as $att)
                                                    <div class="card card-sm border shadow-none" style="min-width: 150px;">
                                                        <div class="card-body p-2">
                                                            <div class="d-flex align-items-center justify-content-between mb-1">
                                                                <span class="badge bg-{{ 
                                                                    $att->status_attendance == 'hadir' ? 'green' : 
                                                                    ($att->status_attendance == 'alpa' ? 'red' : 'yellow') 
                                                                }} text-white badge-sm">
                                                                    {{ strtoupper($att->status_attendance) }}
                                                                </span>
                                                                <span class="text-muted small">{{ $att->class_attendance->class_schedule->start_time }}</span>
                                                            </div>
                                                            <div class="font-weight-bold text-truncate" title="{{ $att->class_attendance->class_schedule->subject_study->name_subject }}">
                                                                {{ $att->class_attendance->class_schedule->subject_study->name_subject }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-muted small italic">Tidak ada jadwal / Data belum diisi</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
