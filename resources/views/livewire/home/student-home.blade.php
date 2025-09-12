<div>
    <x-alert />

    <div class="row">
        <div class="col-12 col-md-4 col-lg-3">
            <x-card.count-data title="Hadir" :total="$this->totalHadir" icon="address-card" color="green" />

            <x-card.count-data title="Alpa" :total="$this->totalAlpa" icon="address-card" color="red" />

            <x-card.count-data title="Izin" :total="$this->totalIzin" icon="address-card" color="yellow" />

            <x-card.count-data title="Sakit" :total="$this->totalSakit" icon="address-card" color="cyan" />
        </div>

        <div class="col-12 col-md-8 col-lg-9">
            <div class="card">
                <div class="card-body">
                    <h3>FILTER <span class="las la-filter fs-2 ms-2"></span></h3>
                    <div class="row">
                        <div class="col-12">
                            <x-form.select wire:model.lazy="classScheduleId" name="classScheduleId" form-group-class>
                                <option value="">SEMUA MAPEL</option>
                                @foreach ($this->class_schedules as $schedule)
                                    <option wire:key="{{ strtolower($schedule->id) }}" value="{{ $schedule->id }}">
                                        {{ strtoupper($schedule->subject_study->name_subject) }}</option>
                                @endforeach
                            </x-form.select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6 col-12">
                    <div class="card card-count-data mb-3 flex border border-green-lt mt-3">
                        <div class="card-body">
                            <div class="d-flex gap-3">
                                <div class="d-flex flex-column">
                                    <p class="mb-0 text-green mb-3">Informasi Anda</p>
                                    <div class="mt-2">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="me-2">
                                                <img src="{{ $studentPhoto }}" alt="Foto Siswa"
                                                    class="rounded-3 object-cover" width="58" height="68">
                                            </div>
                                            <div class="ms-3">
                                                {{ $studentName }}
                                                <span class="fw-bold d-block"></span>
                                                <span class="d-block">Kelas: {{ $studentClassRoom }} </span>
                                                <span class="d-block">Semester: Genap </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="col-lg-6 col-12">
                    <div class="card mt-3">
                        <div class="card-body py-1">
                            <div wire:ignore>
                                <div id="chart-mentions" total="{{ $this->totalPercentance }}"
                                    hadir="{{ $this->percentHadir }}" alpa="{{ $this->percentAlpa }}"
                                    izin="{{ $this->percentIzin }}" sakit="{{ $this->percentSakit }}" class="chart-lg">
                                </div>
                            </div>
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

            function renderDonutChart(hadir, alpa, izin, sakit, total) {
                if (!item) return;

                if (chart) chart.destroy();

                // Default values if all are NaN or zero
                let series = [hadir, alpa, izin, sakit];
                if (series.every(val => isNaN(val) || val === 0)) {
                    series = [1, 1, 1, 1];
                }

                chart = new ApexCharts(item, {
                    chart: {
                        type: "donut",
                        height: 300,
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
                            colors: '#fff' // kalau mau legend juga putih
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
                                size: '50%', // ukuran lubang donut
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

            // initial render
            renderDonutChart(
                parseFloat(item.getAttribute('hadir')),
                parseFloat(item.getAttribute('alpa')),
                parseFloat(item.getAttribute('izin')),
                parseFloat(item.getAttribute('sakit')),
                item.getAttribute('total'),
            );

            // listen update dari Livewire
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
