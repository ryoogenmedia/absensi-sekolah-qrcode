@push('styles')
    @push('styles')
        <style>
            body {
                background: #f7faff;
            }

            .card-soft {
                border: none !important;
                border-radius: 18px !important;
                background: #ffffff !important;
                box-shadow:
                    0 4px 20px rgba(0, 0, 0, 0.05),
                    0 1px 2px rgba(0, 0, 0, 0.04) !important;
                transition: transform 0.2s ease, box-shadow 0.2s ease;
            }

            .card-soft:hover {
                transform: translateY(-4px);
                box-shadow:
                    0 10px 25px rgba(0, 0, 0, 0.08),
                    0 2px 4px rgba(0, 0, 0, 0.05) !important;
            }

            .glass {
                backdrop-filter: blur(16px);
                background: rgba(255, 255, 255, 0.20) !important;
                border: 1px solid rgba(255, 255, 255, 0.28) !important;
                border-radius: 18px;
                box-shadow: 0 8px 20px rgba(255, 255, 255, 0.1);
            }

            .badge-status {
                padding: 6px 14px;
                font-size: 13px;
                border-radius: 30px;
                font-weight: 600;
                letter-spacing: 0.3px;
                color: #fff;
                text-transform: uppercase;
            }

            table.table-hover tbody tr:hover {
                background: #f1f5f9 !important;
                transition: 0.2s ease;
            }

            table thead th {
                font-size: 14px;
                font-weight: 700;
                color: #334155;
                border-bottom: 2px solid #e2e8f0 !important;
            }

            .hero-card {
                border-radius: 20px;
                background: linear-gradient(135deg, #2563eb, #1e40af);
                box-shadow: 0 10px 25px rgba(29, 78, 216, 0.3);
            }

            .hero-card h2 {
                color: #fff;
                letter-spacing: 0.5px;
            }

            .hero-card p {
                color: rgba(255, 255, 255, 0.85);
            }

            .student-photo {
                width: 90px;
                height: 110px;
                object-fit: cover;
                border-radius: 12px;
                border: 2px solid #e2e8f0;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            }

            select.form-select {
                border-radius: 12px;
                padding: 10px 14px;
                border-color: #cbd5e1;
                transition: 0.2s ease;
            }

            select.form-select:focus {
                border-color: #2563eb;
                box-shadow: 0 0 0 0.15rem rgba(37, 99, 235, 0.25);
            }

            .empty-state {
                height: 350px;
                vertical-align: middle !important;
                font-size: 15px;
                color: #94a3b8;
            }
        </style>
    @endpush
@endpush

<div>
    <x-alert />

    <div class="card bg-blue text-white mb-4">
        <div class="card-body py-4 px-4 d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold mb-1">Dashboard Wali Siswa</h2>
                <p class="mb-0">Pantau aktivitas & kehadiran siswa secara real-time</p>
            </div>
            <div class="text-end opacity-75">
                <span class="las la-graduation-cap" style="font-size: 100px; font-weight: bold"></span>
            </div>
        </div>
    </div>

    <div class="row">

        {{-- LEFT SUMMARY --}}
        <div class="col-12 col-md-4 col-lg-3">
            <x-card.count-data title="Hadir" :total="$totalHadir" icon="check-circle" color="green" />
            <x-card.count-data title="Alpa" :total="$totalAlpa" icon="times-circle" color="red" />
            <x-card.count-data title="Izin" :total="$totalIzin" icon="question-circle" color="yellow" />
            <x-card.count-data title="Sakit" :total="$totalSakit" icon="medkit" color="cyan" />
        </div>

        {{-- RIGHT PANEL --}}
        <div class="col-12 col-md-8 col-lg-9">

            {{-- FILTER --}}
            <div class="card card-soft mb-3">
                <div class="card-body">
                    <h4 class="fw-bold mb-3">
                        Filter Kehadiran
                        <span class="las la-filter"></span>
                    </h4>

                    <x-form.select wire:model.lazy="classScheduleId" name="classScheduleId" form-group-class>
                        <option value="">SEMUA MAPEL</option>

                        @foreach ($this->class_schedules as $schedule)
                            <option value="{{ $schedule->id }}">
                                {{ strtoupper($schedule->subject_study->name_subject) }}
                            </option>
                        @endforeach
                    </x-form.select>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6">

                    <div class="card card-soft mb-3">
                        <div class="card-body d-flex">
                            <img src="{{ $studentPhoto }}" width="80" height="100"
                                class="rounded-3 border me-3 object-cover shadow-sm">

                            <div>
                                <h5 class="fw-bold mb-1">{{ $studentName }}</h5>
                                <small class="d-block mt-2">NIS: {{ $studentNis }}</small>
                                <small class="d-block">Kelas: {{ $studentClassRoom }}</small>
                                <small class="d-block">Kontak: {{ $studentContact }}</small>
                                <small class="d-block">Agama: {{ $studentReligion }}</small>
                            </div>
                        </div>
                    </div>

                    <div class="card card-soft mt-3 py-5">
                        <div class="card-body">
                            <div wire:ignore>
                                <div id="chart-mentions" hadir="{{ $percentHadir }}" alpa="{{ $percentAlpa }}"
                                    izin="{{ $percentIzin }}" sakit="{{ $percentSakit }}"
                                    total="{{ $totalPercentance }}" style="min-height: 300px;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card card-soft">
                        <div class="card-header bg-light fw-bold">
                            10 Riwayat Terakhir Kehadiran Siswa
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Mapel</th>
                                        <th>Jam</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse($this->student_attendances as $attendance)
                                        <tr>
                                            <td>{{ $attendance->class_attendance->created_at->format('d M Y') }}</td>
                                            <td>{{ $attendance->class_attendance->class_schedule->subject_study->name_subject }}
                                            </td>
                                            <td>{{ $attendance->class_attendance->class_schedule->time_start }}</td>

                                            <td>
                                                <span
                                                    class="badge
                                                    @if ($attendance->status_attendance == 'hadir') bg-success-lt
                                                    @elseif($attendance->status_attendance == 'alpa') bg-danger-lt
                                                    @elseif($attendance->status_attendance == 'izin') bg-warning-lt
                                                    @else bg-info-lt @endif">
                                                    {{ strtoupper($attendance->status_attendance) }}
                                                </span>
                                            </td>
                                        </tr>

                                    @empty
                                        <tr style="height: 390px">
                                            <td colspan="4" class="text-center text-muted py-5">Tidak ada data
                                                kehadiran.</td>
                                        </tr>
                                    @endforelse
                                </tbody>

                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-soft mt-4">
                <div class="card-header d-flex justify-content-between align-items-center bg-light fw-bold">
                    <span>Riwayat Presensi {{ $qrType == 'in' ? 'Masuk' : 'Keluar' }}</span>

                    <select class="form-select w-auto" wire:model.live="qrType" name="qrType">
                        <option value="in">Presensi Masuk</option>
                        <option value="out">Presensi Keluar</option>
                    </select>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Jam {{ $qrType == 'in' ? 'Masuk' : 'Keluar' }}</th>
                            </tr>
                        </thead>

                        <tbody>
                            @php
                                $qrData = [];

                                if ($qrType == 'in') {
                                    $qrData = $checkInRecords;
                                } elseif ($qrType == 'out') {
                                    $qrData = $checkOutRecords;
                                } else {
                                    $qrData = $checkInRecords->merge($checkOutRecords)->sortByDesc('attendance_time');
                                }
                            @endphp

                            @forelse($qrData as $qr)
                                <tr>
                                    <td>{{ $qr->attendance_date ?? '-' }}</td>

                                    @if ($qrType == 'in')
                                        <td>{{ $qr->check_in_time ?? '-' }}</td>
                                    @else
                                        <td>{{ $qr->check_out_time ?? '-' }}</td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">Belum ada data presensi QR.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let chart;
            const item = document.getElementById('chart-mentions');

            function renderDonutChart(hadir, alpa, izin, sakit, total) {
                if (!item) return;

                if (chart) chart.destroy();

                let series = [hadir, alpa, izin, sakit];
                if (series.every(val => isNaN(val) || val === 0)) {
                    series = [1, 1, 1, 1];
                }

                chart = new ApexCharts(item, {
                    chart: {
                        type: "donut",
                        height: 320,
                        toolbar: {
                            show: false
                        }
                    },
                    labels: ["HADIR", "ALPA", "IZIN", "SAKIT"],
                    series: series,
                    colors: ["#4ade80", "#f87171", "#facc15", "#06b6d4"],
                    legend: {
                        show: true,
                        position: 'bottom',
                        labels: {
                            colors: '#fff'
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        formatter: function(val, opts) {
                            return opts.w.config.series[opts.seriesIndex] + '%';
                        }
                    },
                    plotOptions: {
                        pie: {
                            expandOnClick: true,
                            dataLabels: {
                                offset: -10,
                                minAngleToShowLabel: 10,
                                style: {
                                    colors: ['#fff']
                                }
                            },
                            donut: {
                                size: '50%',
                                labels: {
                                    show: true,
                                    name: {
                                        show: true,
                                        fontSize: '15px',
                                        fontFamily: 'Arial, sans-serif',
                                        fontWeight: 500,
                                        offsetY: -5
                                    },
                                    value: {
                                        show: true,
                                        fontSize: '20px',
                                        fontFamily: 'Arial, sans-serif',
                                        fontWeight: 700,
                                        offsetY: 10,
                                        formatter: function(val) {
                                            return total * val / 100;
                                        }
                                    },
                                    total: {
                                        show: true,
                                        label: 'TOTAL',
                                        fontSize: '15px',
                                        fontWeight: 500,
                                        formatter: function(w) {
                                            return total;
                                        }
                                    }
                                }
                            }
                        }
                    }
                });
                chart.render();
            }

            renderDonutChart(
                parseFloat(item.getAttribute('hadir')),
                parseFloat(item.getAttribute('alpa')),
                parseFloat(item.getAttribute('izin')),
                parseFloat(item.getAttribute('sakit')),
                item.getAttribute('total'),
            );

            Livewire.on('updateChartStudentPercent', (data) => {
                renderDonutChart(
                    parseFloat(data[0].hadir),
                    parseFloat(data[0].alpa),
                    parseFloat(data[0].izin),
                    parseFloat(data[0].sakit),
                    data[0].total,
                );
            });
        });
    </script>
@endpush
