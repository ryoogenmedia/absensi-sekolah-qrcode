<div>
    <x-alert />

    <div class="row">
        <div class="col-12">
            <div class="card bg-blue text-white mb-3">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4 col-12 mt-lg-5 mt-2">
                            <h2 class="mb-0">Presensi QR Code Hari Ini</h2>
                            <p>Daftar siswa yang masuk dan keluar hari ini</p>
                        </div>

                        <div class="col-lg-8 col-12">
                            <div class="row">
                                <div class="col-lg-6 col-12 mb-lg-0 mb-3 align-self-center">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="d-flex flex-column">
                                                <p class="text-blue" style="font-size: 18px; font-weight: 500">Presensi
                                                    Masuk</p>
                                                <h1 class="text-blue" style="font-size: 30px">{{ $checkInToday ?? 0 }}
                                                </h1>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6 col-12 mb-lg-0 mb-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="d-flex flex-column">
                                                <p class="text-blue" style="font-size: 18px; font-weight: 500">Presensi
                                                    Keluar</p>
                                                <h1 class="text-blue" style="font-size: 30px">{{ $checkOutToday ?? 0 }}
                                                    </h2>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-md-4 col-lg-3">
            <x-card.count-data title="Siswa" :period="$this->period" :total="$this->totalStudent" icon="graduation-cap"
                color="blue" />
            <x-card.count-data title="Guru" :period="$this->period" :total="$this->totalTeacher" icon="user-tie" color="red" />
            <x-card.count-data title="Admin" :period="$this->period" :total="$this->totalAdmin" icon="database" color="purple" />
            <x-card.count-data title="Jadwal Kelas" :period="$this->period" :total="$this->totalJadwalKelas" icon="calendar"
                color="green" />
            <x-card.count-data title="Mata Pelajaran" :period="$this->period" :total="$this->totalMataPelajaran" icon="book"
                color="orange" />
            <x-card.count-data title="Kelas" :period="$this->period" :total="$this->totalKelas" icon="building" color="teal" />
        </div>

        <div class="col-12 col-md-8 col-lg-9">
            <div class="card h-100 mb-3 w-100 d-flex">
                <div class="card-header text-center d-flex justify-content-between">
                    <h4 class="mb-0 pb-0 align-self-center">
                        Data Presensi
                        @switch($this->period)
                            @case('daily')
                                10 Hari Terkahir
                            @break

                            @case('weekly')
                                10 Minggu Terakhir
                            @break

                            @case('monthly')
                                10 Bulan Terakhir
                            @break

                            @case('yearly')
                                10 Tahun Terakhir
                            @break
                        @endswitch
                    </h4>

                    <x-form.select wire:model.live="period" name="period" form-group-class>
                        <option value=""></option>
                        @foreach (config('const.periods') as $period)
                            <option wire:key="{{ $period }}" value="{{ $period }}">
                                {{ match ($period) {
                                    'daily' => '10 HARI TERAKHIR',
                                    'weekly' => '10 MINGGU TERAKHIR',
                                    'monthly' => '10 BULAN TERAKHIR',
                                    'yearly' => '10 TAHUN TERAKHIR',
                                    default => '',
                                } }}
                            </option>
                        @endforeach
                    </x-form.select>
                </div>

                <div class="card-body py-2">
                    <div wire:ignore>
                        <div hadir="{{ json_encode($this->attendanceHadir['data']) }}"
                            alpa="{{ json_encode($this->attendanceAlpa['data']) }}"
                            izin="{{ json_encode($this->attendanceIzin['data']) }}"
                            sakit="{{ json_encode($this->attendanceSakit['data']) }}"
                            date="{{ json_encode($this->attendanceHadir['date']) }}" id="chart-mentions"
                            class="chart-lg">
                        </div>
                    </div>
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

            function renderChart(hadir, alpa, izin, sakit, date) {
                if (!item) {
                    console.error("ELEMENT ID #chart-mentions TIDAK DITEMUKAN!");
                    return;
                }

                if (chart) {
                    chart.destroy();
                }

                chart = new ApexCharts(item, {
                    chart: {
                        type: "bar",
                        stacked: true,
                        height: 600,
                        parentHeightOffset: 0,
                        toolbar: {
                            show: false
                        },
                        animations: {
                            enabled: true
                        }
                    },
                    stroke: {
                        show: true,
                        width: 1,
                        colors: ['#fff']
                    },
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            dataLabels: {
                                total: {
                                    enabled: true,
                                    style: {
                                        fontSize: '13px',
                                        fontWeight: 900
                                    }
                                }
                            }
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    series: [{
                            name: "Hadir",
                            data: hadir
                        },
                        {
                            name: "Alpa",
                            data: alpa
                        },
                        {
                            name: "Izin",
                            data: izin
                        },
                        {
                            name: "Sakit",
                            data: sakit
                        },
                    ],
                    xaxis: {
                        categories: date,
                        labels: {
                            style: {
                                fontSize: '12px'
                            }
                        }
                    },
                    yaxis: {
                        labels: {
                            style: {
                                fontSize: '12px'
                            }
                        }
                    },
                    colors: ["#4ade80", "#fc9f13", "#3b82f6", "#f43f5e"],
                    legend: {
                        show: true
                    }
                });

                chart.render();
            }

            Livewire.on('updateChart', (data) => {
                let hadir = data[0].hadir;
                let alpa = data[0].alpa;
                let izin = data[0].izin;
                let sakit = data[0].sakit;
                let date = data[0].date;

                renderChart(hadir, alpa, izin, sakit, date);
            });

            renderChart(
                JSON.parse(item.getAttribute('hadir')),
                JSON.parse(item.getAttribute('alpa')),
                JSON.parse(item.getAttribute('izin')),
                JSON.parse(item.getAttribute('sakit')),
                JSON.parse(item.getAttribute('date'))
            );
        });
    </script>
@endpush
