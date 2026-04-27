<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>DATA LAPORAN KEHADIRAN QR CODE MASUK</title>

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

    <h4>DATA LAPORAN KEHADIRAN QR CODE MASUK</h4>

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
                <th>Nama Siswa</th>
                <th>NIS</th>
                <th>Kelas</th>
                <th>Waktu Masuk</th>
                <th>Tanggal Presensi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $check_in)
                <tr>
                    <td>{{ $check_in->student->full_name ?? '-' }}</td>
                    <td>{{ $check_in->student->nis ?? '-' }}</td>
                    <td>{{ $check_in->student->class_room->name_class ?? '-' }}</td>
                    <td>{{ $check_in->check_in_time ?? '-' }}</td>
                    <td>{{ $check_in->attendance_date ?? '-' }}</td>
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
