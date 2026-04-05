<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Rekap Presensi Siswa</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }

        .header p {
            margin: 5px 0;
            font-size: 12px;
        }

        .student-info {
            margin-bottom: 20px;
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 5px;
        }

        .student-info-row {
            display: flex;
            margin-bottom: 8px;
            font-size: 13px;
        }

        .student-info-label {
            width: 150px;
            font-weight: bold;
        }

        .student-info-value {
            flex: 1;
        }

        .summary-section {
            margin-bottom: 25px;
        }

        .summary-section h3 {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 5px;
        }

        .summary-cards {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }

        .summary-card {
            flex: 1;
            min-width: 120px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            text-align: center;
        }

        .summary-card .label {
            font-size: 11px;
            color: #666;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .summary-card .value {
            font-size: 24px;
            font-weight: bold;
        }

        .summary-card.hadir {
            background-color: #d4edda;
            border-color: #28a745;
        }

        .summary-card.hadir .value {
            color: #28a745;
        }

        .summary-card.alpa {
            background-color: #f8d7da;
            border-color: #dc3545;
        }

        .summary-card.alpa .value {
            color: #dc3545;
        }

        .summary-card.izin {
            background-color: #fff3cd;
            border-color: #ffc107;
        }

        .summary-card.izin .value {
            color: #856404;
        }

        .summary-card.sakit {
            background-color: #d1ecf1;
            border-color: #17a2b8;
        }

        .summary-card.sakit .value {
            color: #17a2b8;
        }

        .summary-card.total {
            background-color: #e7e7ff;
            border-color: #0056b3;
        }

        .summary-card.total .value {
            color: #0056b3;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 12px;
        }

        table thead {
            background-color: #f8f9fa;
        }

        table th {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
            font-weight: bold;
            font-size: 12px;
        }

        table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        table td:first-child {
            text-align: left;
        }

        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 3px;
            color: white;
            font-size: 11px;
            font-weight: bold;
        }

        .badge-hadir {
            background-color: #28a745;
        }

        .badge-alpa {
            background-color: #dc3545;
        }

        .badge-izin {
            background-color: #ffc107;
            color: #333;
        }

        .badge-sakit {
            background-color: #17a2b8;
        }

        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 12px;
        }

        .footer-row {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
        }

        .footer-col {
            text-align: center;
            width: 25%;
        }

        .footer-signature {
            border-top: 1px solid #333;
            margin-top: 50px;
            padding-top: 10px;
            font-size: 11px;
        }

        .date-range {
            font-size: 12px;
            color: #666;
            margin-bottom: 10px;
            padding: 10px;
            background-color: #f0f0f0;
            border-radius: 5px;
        }

        .list-section {
            margin-top: 20px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #fafafa;
        }

        .list-section h4 {
            margin: 0 0 10px 0;
            font-size: 12px;
            font-weight: bold;
            padding-bottom: 5px;
            border-bottom: 2px solid #ddd;
        }

        .list-section.izin {
            border-left: 4px solid #ffc107;
        }

        .list-section.izin h4 {
            color: #856404;
            border-bottom-color: #ffc107;
        }

        .list-section.alpa {
            border-left: 4px solid #dc3545;
        }

        .list-section.alpa h4 {
            color: #721c24;
            border-bottom-color: #dc3545;
        }

        .list-section.sakit {
            border-left: 4px solid #17a2b8;
        }

        .list-section.sakit h4 {
            color: #0c5460;
            border-bottom-color: #17a2b8;
        }

        .list-item {
            font-size: 11px;
            padding: 5px 0;
            line-height: 1.4;
        }

        .list-item-empty {
            font-size: 11px;
            color: #999;
            font-style: italic;
            padding: 5px 0;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>REKAP PRESENSI SISWA</h1>
        <p>{{ $classSchedule->subject_study->name_subject ?? 'Mata Pelajaran' }}</p>
        <p>Tanggal Cetak: {{ now()->format('d F Y H:i') }}</p>
    </div>

    <div class="student-info">
        <div class="student-info-row">
            <div class="student-info-label">Nama Siswa</div>
            <div class="student-info-value"><strong>{{ $student->full_name }}</strong></div>
        </div>
        <div class="student-info-row">
            <div class="student-info-label">NIM</div>
            <div class="student-info-value">{{ $student->nis }}</div>
        </div>
        <div class="student-info-row">
            <div class="student-info-label">Kelas</div>
            <div class="student-info-value">{{ $student->class_room->name_room ?? '-' }}</div>
        </div>
        <div class="student-info-row">
            <div class="student-info-label">Hari</div>
            <div class="student-info-value">{{ strtoupper($classSchedule->day_name) }}</div>
        </div>
        <div class="student-info-row">
            <div class="student-info-label">Jam Pelajaran</div>
            <div class="student-info-value">{{ $classSchedule->start_time }} - {{ $classSchedule->end_time }}</div>
        </div>
    </div>

    @if ($date_start || $date_end)
        <div class="date-range">
            <strong>Periode Presensi:</strong>
            @if ($date_start)
                Dari {{ \Carbon\Carbon::parse($date_start)->format('d F Y') }}
            @endif
            @if ($date_end)
                Hingga {{ \Carbon\Carbon::parse($date_end)->format('d F Y') }}
            @endif
        </div>
    @endif

    <div class="summary-section">
        <h3>RINGKASAN KEHADIRAN</h3>
        <div class="summary-cards">
            <div class="summary-card hadir">
                <div class="label">Hadir</div>
                <div class="value">{{ $summary['hadir'] }}</div>
            </div>
            <div class="summary-card alpa">
                <div class="label">Alpa</div>
                <div class="value">{{ $summary['alpa'] }}</div>
            </div>
            <div class="summary-card izin">
                <div class="label">Izin</div>
                <div class="value">{{ $summary['izin'] }}</div>
            </div>
            <div class="summary-card sakit">
                <div class="label">Sakit</div>
                <div class="value">{{ $summary['sakit'] }}</div>
            </div>
            <div class="summary-card total">
                <div class="label">Total Pertemuan</div>
                <div class="value">{{ $summary['total'] }}</div>
            </div>
        </div>
    </div>

    <div class="summary-section">
        <h3>DAFTAR DETAIL KETIDAKHADIRAN</h3>

        <div class="list-section izin">
            <h4>📋 IZIN ({{ $summary['izin'] }} Pertemuan)</h4>
            @php
                $izinList = $attendances->where('status_attendance', 'izin');
            @endphp
            @forelse($izinList as $item)
                <div class="list-item">
                    • {{ $item->class_attendance->created_at->format('d/m/Y') }} -
                    {{ $item->class_attendance->name_material ?? '-' }}
                </div>
            @empty
                <div class="list-item-empty">Tidak ada pertemuan dengan status izin</div>
            @endforelse
        </div>

        <div class="list-section alpa">
            <h4>⚠️ ALPA ({{ $summary['alpa'] }} Pertemuan)</h4>
            @php
                $alpaList = $attendances->where('status_attendance', 'alpa');
            @endphp
            @forelse($alpaList as $item)
                <div class="list-item">
                    • {{ $item->class_attendance->created_at->format('d/m/Y') }} -
                    {{ $item->class_attendance->name_material ?? '-' }}
                </div>
            @empty
                <div class="list-item-empty">Tidak ada pertemuan dengan status alpa</div>
            @endforelse
        </div>

        <div class="list-section sakit">
            <h4>🏥 SAKIT ({{ $summary['sakit'] }} Pertemuan)</h4>
            @php
                $sakitList = $attendances->where('status_attendance', 'sakit');
            @endphp
            @forelse($sakitList as $item)
                <div class="list-item">
                    • {{ $item->class_attendance->created_at->format('d/m/Y') }} -
                    {{ $item->class_attendance->name_material ?? '-' }}
                </div>
            @empty
                <div class="list-item-empty">Tidak ada pertemuan dengan status sakit</div>
            @endforelse
        </div>
    </div>

    <div class="summary-section">
        <h3>DETAIL PRESENSI</h3>
        <table>
            <thead>
                <tr>
                    <th width="15%">Tanggal</th>
                    <th width="40%">Materi</th>
                    <th width="45%">Status Kehadiran</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attendances as $attendance)
                    <tr>
                        <td>{{ $attendance->class_attendance->created_at->format('d/m/Y') }}</td>
                        <td>{{ $attendance->class_attendance->name_material ?? '-' }}</td>
                        <td>
                            <span class="badge badge-{{ $attendance->status_attendance }}">
                                {{ match ($attendance->status_attendance) {
                                    'hadir' => 'HADIR',
                                    'alpa' => 'ALPA',
                                    'izin' => 'IZIN',
                                    'sakit' => 'SAKIT',
                                    default => $attendance->status_attendance,
                                } }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="text-align: center; color: #666;">
                            Tidak ada data presensi untuk periode yang dipilih
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="footer">
        <div class="footer-row">
            <div class="footer-col">
                <div>Guru Mata Pelajaran</div>
                <div class="footer-signature">
                    ..............................<br>
                    {{ $classSchedule->teacher->full_name ?? 'Nama Guru' }}
                </div>
            </div>
            <div class="footer-col">
                <div>Wali Kelas</div>
                <div class="footer-signature">
                    ..............................
                </div>
            </div>
            <div class="footer-col">
                <div>Orang Tua/Wali</div>
                <div class="footer-signature">
                    ..............................<br>
                    {{ $student->full_name }}
                </div>
            </div>
        </div>
    </div>
</body>

</html>
