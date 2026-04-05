<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Laporan Presensi Kelas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 2px 3px;
            text-align: left;
        }

        th {
            font-weight: bold;
        }

        .center {
            text-align: center;
        }

        .header {
            text-align: center;
            margin-bottom: 5px;
        }
    </style>
</head>

<body>
    <div class="header">
        <small><strong>LAPORAN PRESENSI KELAS: {{ $kelas }}</strong></small><br>
        <small>Periode: {{ $startDate ? \Carbon\Carbon::parse($startDate)->format('d/m/Y') : '-' }} s/d
            {{ $endDate ? \Carbon\Carbon::parse($endDate)->format('d/m/Y') : '-' }}</small>
    </div>
    @if ($data->count() > 0)
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 10%;">Kelas</th>
                    <th style="width: 10%;">Tgl</th>
                    <th style="width: 20%;">Siswa</th>
                    <th style="width: 35%;">Mapel / Guru</th>
                    <th style="width: 10%;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $index => $row)
                    <tr>
                        <td class="center">{{ $index + 1 }}</td>
                        <td>{{ $row->class_attendance->class_room->name_class ?? '-' }}</td>
                        <td>{{ $row->class_attendance->created_at->format('d/m') }}</td>
                        <td>{{ $row->student->full_name ?? '-' }}</td>
                        <td>{{ strtoupper($row->class_attendance->class_schedule->subject_study->name_subject ?? '-') }}
                            / {{ $row->class_attendance->class_schedule->teacher->name ?? '-' }}</td>
                        <td class="center">{{ strtoupper(substr($row->status_attendance, 0, 1)) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>Tidak ada data</p>
    @endif
</body>

</html>
