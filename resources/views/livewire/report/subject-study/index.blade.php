@push('styles')
    <style>
        .custom-header {
            margin-bottom: 20px !important;
            margin-top: -68px;
        }
    </style>
@endpush

<div>
    <x-slot name="title">Laporan Mata Pelajaran</x-slot>

    <div class="row g-2 align-items-center mb-4">
        <div class="col">
            <div class="page-pretitle">
                Cetak Laporan Mata Pelajaran
            </div>
            <h2 class="page-title">
                Laporan Mata Pelajaran
            </h2>
        </div>

        <div class="col-auto ms-auto d-print-none">
            <div class="btn-list">
                <a href="{{ route('print-report.subject-study') }}" target="_blank" class="btn btn-danger"
                    class="btn btn-danger"><span class="las la-print fs-1 me-2"></span>Cetak
                    Laporan Mapel</a>
            </div>
        </div>
    </div>

    <x-alert />

    <div class="row mb-3 align-items-center justify-content-between">
        <div class="col-12 col-lg-5 d-flex">
            <div class="w-50">
                <x-datatable.search placeholder="Cari nama mata pelajaran..." />
            </div>
        </div>
        <div class="col-auto ms-auto d-flex mt-lg-0 mt-3">
            <x-datatable.items-per-page />
        </div>
    </div>

    <div class="card" wire:loading.class.delay="card-loading" wire:offline.class="card-loading">
        <div class="table-responsive mb-0">
            <table class="table card-table table-bordered datatable">
                <thead>
                    <tr>
                        <th style="width: 200px">
                            <x-datatable.column-sort name="Nama Mata Pelajaran" wire:click="sortBy('name_subject')"
                                :direction="$sorts['name_subject'] ?? null" />
                        </th>

                        <th>
                            <x-datatable.column-sort name="Description" wire:click="sortBy('description')"
                                :direction="$sorts['description'] ?? null" />
                        </th>

                        <th class="text-center" style="width: 100px">
                            <x-datatable.column-sort name="Status" wire:click="sortBy('status_active')"
                                :direction="$sorts['status_active'] ?? null" />
                        </th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($this->rows as $row)
                        <tr wire:key="row-{{ $row->id }}">
                            <td><b>{{ $row->name_subject ?? '-' }}</b></td>

                            <td>{{ $row->description ?? '-' }}</td>

                            <td class="text-center">{{ $row->status_active ? 'Aktif' : 'Tidak Aktif' }}</td>
                        </tr>
                    @empty
                        <x-datatable.empty colspan="10" />
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $this->rows->links() }}
    </div>
</div>
