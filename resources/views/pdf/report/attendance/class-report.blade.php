<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>DATA LAPORAN KEHADIRAN KELAS</title>

    <style>
        * {
            font-family: Arial, Helvetica, sans-serif;
        }

        table {
            font-size: 14px;
            margin: 40px auto 0;
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            padding: 5px;
            border: 1px solid black;
        }

        thead {
            background-color: rgb(226, 226, 226);
        }

        h4,
        h3 {
            text-align: center;
            margin: 10px 0;
        }

        .logo-container {
            padding: 5px 0;
            text-align: center;
        }

        .logo {
            width: 300px;
            height: 100px;
        }
    </style>
</head>

<body>
    <div class="logo-container">
        <img class="logo" src="{{ public_path('static/ryoogen/logo/DARK.png') }}" alt="logo">
    </div>

    <h4>DATA LAPORAN KEHADIRAN KELAS</h4>

    @if ($class_room)
        <h3>{{ $class_room }}</h3>
    @endif

    @if ($date_start || $date_end)
        <h3>
            PERIODE
            {{ implode(
                ' - ',
                array_filter([
                    $date_start ? \Carbon\Carbon::parse($date_start)->translatedFormat('M Y') : null,
                    $date_end ? \Carbon\Carbon::parse($date_end)->translatedFormat('M Y') : null,
                ]),
            ) }}
        </h3>
    @endif

    <table>
        <thead>
            <tr>
                <th>NO</th>
                <th class="text-center">Kelas</th>

                <th>Tanggal Presensi</th>

                <th>Nama Presensi</th>

                <th>Guru Pengajar</th>

                <th>Mata Pelajaran</th>

                <th>Status Presensi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $attendance)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>

                    <td class="text-center"><b>{{ $attendance->class_attendance->class_room->name_class ?? '' }}</b>
                    </td>

                    <td>
                        {{ $attendance->class_attendance->created_at->translatedFormat('l, d F Y') ?? '-' }}
                    </td>

                    <td>{{ $attendance->student->full_name ?? '-' }}</td>

                    <td>{{ $attendance->class_attendance->class_schedule->teacher->name ?? '-' }}</td>

                    <td>{{ strtoupper($attendance->class_attendance->class_schedule->subject_study->name_subject ?? '-') }}
                    </td>

                    <td>{{ ucwords($attendance->status_attendance) ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 10px;">
                        <p style="color: #929292; margin-top: 10px; padding-bottom: 0px"><b>Data Belum Tersedia</b></p>
                        <p style="color: #c4c4c4; margin-top: 0px;">Sesuaikan filter anda untuk mencari data yang
                            tersedia.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>

</html>
