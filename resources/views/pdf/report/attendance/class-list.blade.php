<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Presensi Kelas - Data Record</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin: 20px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px solid #000;
            padding-bottom: 15px;
        }

        .header h1 {
            margin: 0;
            font-size: 16px;
            font-weight: bold;
        }

        .header p {
            margin: 5px 0;
            font-size: 12px;
        }

        .filter-info {
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f5f5f5;
            border-left: 3px solid #007bff;
            font-size: 11px;
        }

        .filter-info table {
            width: 100%;
            border-collapse: collapse;
        }

        .filter-info td {
            padding: 3px 10px;
        }

        .filter-info td:first-child {
            font-weight: bold;
            width: 20%;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        thead {
            background-color: #007bff;
            color: white;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            font-weight: bold;
            font-size: 11px;
        }

        td {
            font-size: 10px;
        }

        .text-center {
            text-align: center;
        }

        .badge {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }

        .badge-success {
            background-color: #28a745;
            color: white;
        }

        .badge-danger {
            background-color: #dc3545;
            color: white;
        }

        .no-data {
            text-align: center;
            padding: 20px;
            color: #999;
        }

        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 11px;
        }

        .footer-date {
            margin-top: 40px;
        }

        .page-break {
            page-break-after: always;
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
    <div class="header">
        <div class="logo-container">
            <img class="logo" src="{{ public_path('static/nurhaliza/logo/DARK.png') }}" alt="logo">
        </div>

        <h1>LAPORAN PRESENSI KELAS</h1>
        <p>Data Record / Daftar Kehadiran</p>
        <p>Kelas: <strong>{{ $kelas }}</strong></p>
    </div>

    <div class="filter-info">
        <table>
            <tr>
                <td>Tanggal Mulai:</td>
                <td>{{ $startDate ? \Carbon\Carbon::parse($startDate)->translatedFormat('l, d F Y') : 'Semua' }}
                </td>
                <td>Tanggal Selesai:</td>
                <td>{{ $endDate ? \Carbon\Carbon::parse($endDate)->translatedFormat('l, d F Y') : 'Semua' }}</td>
            </tr>
            <tr>
                <td>Tanggal Cetak:</td>
                <td colspan="3">{{ now()->translatedFormat('l, d F Y H:i:s') }}</td>
            </tr>
        </table>
    </div>

    @if ($data->count() > 0)
        <table>
            <thead>
                <tr>
                    <th class="text-center" style="width: 5%;">No</th>
                    <th style="width: 12%;">Kelas</th>
                    <th style="width: 12%;">Tanggal</th>
                    <th style="width: 20%;">Nama Siswa</th>
                    <th style="width: 28%;">Guru & Mapel</th>
                    <th class="text-center" style="width: 12%;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $index => $row)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td><strong>{{ $row->class_attendance->class_room->name_class ?? '-' }}</strong></td>
                        <td>{{ $row->class_attendance->created_at->translatedFormat('d/m/Y') }}</td>
                        <td>{{ $row->student->full_name ?? '-' }}</td>
                        <td>
                            <div>
                                <strong>{{ strtoupper($row->class_attendance->class_schedule->subject_study->name_subject ?? '-') }}</strong>
                            </div>
                            <div style="font-size: 9px; color: #666;">
                                {{ $row->class_attendance->class_schedule->teacher->name ?? '-' }}
                            </div>
                        </td>
                        <td class="text-center">
                            <span
                                class="badge {{ $row->status_attendance == 'hadir' ? 'badge-success' : 'badge-danger' }}">
                                {{ strtoupper($row->status_attendance) }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="footer">
            <div>Total Data: <strong>{{ $data->count() }}</strong> record</div>
            <div class="footer-date">
                <div>Jakarta, {{ now()->translatedFormat('d F Y') }}</div>
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
