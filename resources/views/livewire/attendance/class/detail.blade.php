@push('styles')
    <style>
        .custom-table {
            border-collapse: separate;
            border-spacing: 0 8px;
            width: 100%;
            background: #fff;
        }

        .custom-table tr {
            box-shadow: 0 1px 6px rgba(60, 72, 88, .07);
            border-radius: 8px;
            background: #004d99;
        }

        .custom-table tr td:first-child {
            border-top-left-radius: 12px;
            border-bottom-left-radius: 12px;
        }

        .custom-table tr td:last-child {
            border-top-right-radius: 12px;
            border-bottom-right-radius: 12px;
        }

        .custom-table td {
            padding: 12px 16px;
            vertical-align: middle;
            border: none;
            font-size: 15px;
            color: #fff;
        }

        .custom-table td:first-child {
            font-weight: 500;
            color: #fff;
            width: 40%;
        }

        .custom-table td.text-center {
            width: 5%;
            color: #adb5bd;
            font-weight: 400;
        }

        .custom-table tr:not(:last-child) td {
            border-bottom: 1px solid #e9ecef;
        }
    </style>
@endpush

<div>
    <x-slot name="title">Detail Presensi Kelas</x-slot>

    <x-slot name="pageTitle">Detail Presensi Kelas</x-slot>

    <x-slot name="pagePretitle">Melihat Detail Presensi Kelas</x-slot>

    <x-slot name="button">
        <x-datatable.button.back name="Kembali" :route="route('attendance.class.index')" />
    </x-slot>

    <x-alert />

    <div class="row mb-3 align-items-center justify-content-between">
        <div class="col-12 col-lg-8 d-flex align-self-center">
            <div>
                <x-datatable.search placeholder="Cari nama siswa..." />
            </div>
        </div>
    </div>

    <div class="row mb-5">
        <div class="col-lg-6 col-12 mb-lg-0 mb-3">
            <div class="card">
                <div class="card-body">
                    <table class="custom-table">
                        <tr>
                            <td><b>Nama Guru Pengajar</b></td>
                            <td class="text-center">:</td>
                            <td>{{ $classAttendance->class_schedule->teacher->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td><b>Tanggal Presensi</b></td>
                            <td class="text-center">:</td>
                            <td>{{ $classAttendance->updated_at->translatedFormat('l, d F Y') ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td><b>Jadwal Kelas</b></td>
                            <td class="text-center">:</td>
                            <td>{{ $classAttendance->class_schedule->start_time }} -
                                {{ $classAttendance->class_schedule->end_time }}</td>
                        </tr>
                        <tr>
                            <td><b>Jumlah Siswa Kelas</b></td>
                            <td class="text-center">:</td>
                            <td>{{ $classAttendance->student_attendances->count() ?? 0 }}</td>
                        </tr>
                        @foreach (config('const.attendance_status') as $status)
                            <tr>
                                <td><b>Jumlah {{ ucwords($status) }}</b></td>
                                <td class="text-center">:</td>
                                <td>{{ $classAttendance->student_attendances->where('status_attendance', $status)->count() ?? 0 }}
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-12">
            <div class="card" wire:loading.class.delay="card-loading" wire:offline.class="card-loading">
                <div class="table-responsive mb-0">
                    <table class="table card-table table-bordered datatable">
                        <thead>
                            <tr>
                                <th>Nama Siswa</th>

                                <th>NIS</th>

                                <th>Status</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($this->student_attendances as $attendance)
                                <tr wire:key="row-{{ $attendance->id }}">
                                    <td>{{ strtoupper($attendance->student->full_name ?? '-') }}</td>

                                    <td>{{ $attendance->student->nis ?? '-' }}</td>

                                    <td>{{ strtoupper($attendance->status_attendance ?? '-') }}</td>
                                </tr>
                            @empty
                                <x-datatable.empty colspan="10" />
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
