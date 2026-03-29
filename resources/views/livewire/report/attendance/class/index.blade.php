<div x-data="{ currentTab: @entangle('tab') }">
    <div class="row g-2 align-items-center mb-4">
        <div class="col">
            <div class="page-pretitle">Cetak Laporan Presensi Kelas</div>
            <h2 class="page-title">Laporan Presensi Kelas</h2>
        </div>

    </div>

    <x-alert />

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

    @livewire('report.attendance.class-filter', key('filter-' . json_encode($filters)))

    <div x-show="currentTab == 'list'">
        @livewire('report.attendance.class-list', ['filters' => $filters], key('list-' . json_encode($filters)))
    </div>

    <div x-show="currentTab == 'summary'">
        @livewire('report.attendance.class-summary', ['filters' => $filters], key('summary-' . json_encode($filters)))
    </div>
</div>
