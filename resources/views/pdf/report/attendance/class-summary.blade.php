<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Presensi Kelas - Ringkasan</title>
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

        .text-bold {
            font-weight: bold;
        }

        .badge {
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            background-color: #e9ecef;
            color: #333;
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

        .summary-info {
            margin: 15px 0;
            padding: 10px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            font-size: 11px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>LAPORAN PRESENSI KELAS</h1>
        <p>Ringkasan Data</p>
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
                <td>Tipe Ringkasan:</td>
                <td colspan="3"><strong>{{ $summaryType == 'siswa' ? 'Per Siswa' : 'Per Kelas' }}</strong></td>
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
                    @if ($summaryType == 'siswa')
                        <th style="width: 40%;">Nama Siswa</th>
                        <th class="text-center" style="width: 20%;">Total Hadir</th>
                    @else
                        <th style="width: 30%;">Kelas</th>
                        @foreach ($statuses as $status)
                            @php $label = config('const.attendance_status')[$status]['label'] ?? ucwords($status); @endphp
                            <th class="text-center" style="width: 12%;">{{ ucwords($label) }}</th>
                        @endforeach
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $index => $row)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        @if ($summaryType == 'siswa')
                            <td>{{ $row->full_name }}</td>
                            <td class="text-center">
                                <span class="badge">{{ $row->total_hadir ?? 0 }}</span>
                            </td>
                        @else
                            <td class="text-bold">{{ strtoupper($row->name_class) }}</td>
                            @foreach ($statuses as $status)
                                <td class="text-center">{{ $row['count_' . $status] ?? 0 }}</td>
                            @endforeach
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="summary-info">
            <strong>Keterangan:</strong>
            @foreach ($statuses as $status)
                @php $label = config('const.attendance_status')[$status]['label'] ?? ucwords($status); @endphp
                <div>{{ ucwords($label) }}: {{ ucwords($status) }}</div>
            @endforeach
        </div>

        <div class="footer">
            <div>Total Data: <strong>{{ $data->count() }}</strong> {{ $summaryType == 'siswa' ? 'siswa' : 'kelas' }}
            </div>
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
