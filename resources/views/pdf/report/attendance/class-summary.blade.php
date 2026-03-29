<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Presensi Kelas - Ringkasan</title>
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
            <p>Ringkasan Data</p>
            <p>Kelas: <strong>{{ $kelas }}</strong></p>
        </div>

        <div class="info">
            <div>Tanggal: {{ $startDate ? \Carbon\Carbon::parse($startDate)->format('d/m/Y') : '-' }} s/d
                {{ $endDate ? \Carbon\Carbon::parse($endDate)->format('d/m/Y') : '-' }}</div>
            <div>Tipe: {{ $summaryType == 'siswa' ? 'Per Siswa' : 'Per Kelas' }} | Dicetak:
                {{ now()->format('d/m/Y H:i:s') }}</div>
        </div>

        @if ($data->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th class="text-center" style="width: 5%;">No</th>
                        @if ($summaryType == 'siswa')
                            <th style="width: 70%;">Nama Siswa</th>
                            <th class="text-center" style="width: 15%;">Total Hadir</th>
                        @else
                            <th style="width: 20%;">Kelas</th>
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
                                <td class="text-center">{{ $row->total_hadir ?? 0 }}</td>
                            @else
                                <td>{{ strtoupper($row->name_class) }}</td>
                                @foreach ($statuses as $status)
                                    <td class="text-center">{{ $row['count_' . $status] ?? 0 }}</td>
                                @endforeach
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="footer">
                <div>Total: <strong>{{ $data->count() }}</strong> {{ $summaryType == 'siswa' ? 'siswa' : 'kelas' }} |
                    Makassar, {{ now()->format('d/m/Y') }}</div>
            </div>
        @else
            <div class="no-data">
                <p>Tidak ada data presensi untuk periode yang dipilih.</p>
            </div>
        @endif
    </div>
</body>

</html>
