<div x-data="{
    currentTab: @entangle('tab'),
    alerts: [],
    showAlert(alert) {
        this.alerts.push(alert);
        setTimeout(() => this.alerts.shift(), 5000);
    }
}" @show-alert.window="showAlert($event.detail)">
    <div class="row g-2 align-items-center mb-4">
        <div class="col">
            <div class="page-pretitle">Cetak Laporan Presensi Kelas</div>
            <h2 class="page-title">Laporan Presensi Kelas</h2>
        </div>

    </div>

    <x-alert />

    {{-- Dynamic Alert Display --}}
    <div style="z-index: 10000; position: fixed; top: 20px; right: 20px;" class="d-flex flex-column gap-2">
        <template x-for="alert in alerts" :key="Math.random()">
            <div class="alert" :class="'alert-' + alert.type" role="alert" style="min-width: 400px;">
                <div class="d-flex">
                    <div class="me-3">
                        <h1 class="mb-0"
                            :class="alert.type === 'warning' ? 'text-warning las la-exclamation-triangle' :
                                alert.type === 'danger' ? 'text-danger las la-times-circle' :
                                alert.type === 'success' ? 'text-success las la-check-circle' :
                                'text-info las la-info-circle'">
                        </h1>
                    </div>
                    <div>
                        <h4 class="alert-title" x-text="alert.message"></h4>
                        <div class="text-muted" x-text="alert.detail"></div>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('show-alert', (event) => {
                let alertData = event || {};
                let alertEvent = new CustomEvent('show-alert', {
                    detail: {
                        type: alertData.type || 'info',
                        message: alertData.message || 'Informasi',
                        detail: alertData.detail || '',
                    }
                });
                window.dispatchEvent(alertEvent);
            });
        });
    </script>

    <div class="row">
        <div class="col-12 col-md-4">
            <div class="mb-3 mt-3 mt-md-0">
                <div class="btn-group w-100">
                    {{-- Navigasi Tab menggunakan AlpineJS agar instan --}}
                    <button type="button" @click="currentTab = 'list'; $wire.setTab('list')"
                        :class="currentTab == 'list' ? 'btn-primary' : 'btn-outline-primary'"
                        class="btn">Record</button>
                    <button type="button" @click="currentTab = 'summary'; $wire.setTab('summary')"
                        :class="currentTab == 'summary' ? 'btn-primary' : 'btn-outline-primary'"
                        class="btn">Ringkasan</button>
                </div>
            </div>
        </div>
    </div>

    @livewire('report.attendance.class-filter')

    <div x-show="currentTab == 'list'">
        @livewire('report.attendance.class-list', ['filters' => $filters])
    </div>

    <div x-show="currentTab == 'summary'">
        @livewire('report.attendance.class-summary', ['filters' => $filters])
    </div>
</div>
