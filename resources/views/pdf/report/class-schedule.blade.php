<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>DATA LAPORAN JADWAL KELAS</title>

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

    <h4>DATA LAPORAN JADWAL KELAS</h4>

    @if ($class_room)
        <h3>{{ $class_room }}</h3>
    @endif

    @if ($start_time || $end_time)
        <h4>
            {{ implode(' - ', array_filter([$start_time ? $start_time : null, $end_time ? $end_time : null])) }}
        </h4>
    @endif

    <table>
        <thead>
            <tr>
                <th>NO</th>
                <th>Kelas</th>
                <th>Tanggal Presensi</th>
                <th>Nama Presensi</th>
                <th>Guru Pengajar</th>
                <th>Mata Pelajaran</th>
                <th>Status Presensi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $schedule)
                <tr>
                    <td>{{ $schedule->class_room->name_class ?? '-' }}</td>

                    <td>{{ $schedule->teacher->name ?? '-' }}</td>

                    <td>{{ strtoupper($schedule->day_name ?? '-') }}</td>

                    <td>{{ $schedule->start_time ?? '-' }}</td>

                    <td>{{ $schedule->end_time ?? '-' }}</td>

                    <td>{{ strtoupper($schedule->subject_study->name_subject ?? '-') }}</td>
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
