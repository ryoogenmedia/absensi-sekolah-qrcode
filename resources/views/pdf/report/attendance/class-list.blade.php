<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Presensi Kelas - Data Record</title>
    <style>
        * {
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #000;
        }

        .container {
            padding: 15px;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
            border-bottom: 1px solid #000;
            padding-bottom: 8px;
        }

        .header h1 {
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .header p {
            font-size: 10px;
            margin: 2px 0;
        }

        .info {
            margin-bottom: 8px;
            font-size: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        th {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
            font-weight: bold;
            font-size: 10px;
        }

        td {
            border: 1px solid #000;
            padding: 3px 5px;
            font-size: 10px;
        }

        .text-center {
            text-align: center;
        }

        .no-data {
            text-align: center;
            padding: 20px;
            color: #666;
        }

        .footer {
            margin-top: 10px;
            font-size: 10px;
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
    <div class="container">
        <div class="header">
            <div class="logo-container">
                <img class="logo" src="{{ public_path('static/nurhaliza/logo/DARK.png') }}" alt="logo">
            </div>
            <h1>LAPORAN PRESENSI KELAS</h1>
            <p>Data Record / Daftar Kehadiran</p>
            <p>Kelas: <strong>{{ $kelas }}</strong></p>
        </div>

        <div class="info">
            <div>Tanggal: {{ $startDate ? \Carbon\Carbon::parse($startDate)->format('d/m/Y') : '-' }} s/d
                {{ $endDate ? \Carbon\Carbon::parse($endDate)->format('d/m/Y') : '-' }}</div>
            <div>Dicetak: {{ now()->format('d/m/Y H:i:s') }}</div>
        </div>

        @if ($data->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th class="text-center" style="width: 4%;">No</th>
                        <th style="width: 10%;">Kelas</th>
                        <th style="width: 10%;">Tanggal</th>
                        <th style="width: 18%;">Nama Siswa</th>
                        <th style="width: 30%;">Guru & Mapel</th>
                        <th class="text-center" style="width: 8%;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $index => $row)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $row->class_attendance->class_room->name_class ?? '-' }}</td>
                            <td>{{ $row->class_attendance->created_at->format('d/m/Y') }}</td>
                            <td>{{ $row->student->full_name ?? '-' }}</td>
                            <td>{{ strtoupper($row->class_attendance->class_schedule->subject_study->name_subject ?? '-') }}
                                / {{ $row->class_attendance->class_schedule->teacher->name ?? '-' }}</td>
                            <td class="text-center">{{ ucfirst($row->status_attendance) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="footer">
                <div>Total Data: <strong>{{ $data->count() }}</strong> record</div>
                <div class="footer-date">
                    <div>Makassar, {{ now()->translatedFormat('d F Y') }}</div>
                    <div style="margin-top: 50px;"><u>_____________________</u></div>
                </div>
            </div>
        @else
            <div class="no-data">
                <p>Tidak ada data presensi untuk periode yang dipilih.</p>
            </div>
        @endif
</body>

</html>
