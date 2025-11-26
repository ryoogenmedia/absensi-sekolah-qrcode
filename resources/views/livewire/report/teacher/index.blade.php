<div>
    <x-slot name="title">Laporan Guru</x-slot>

    <div class="row g-2 align-items-center mb-4">
        <div class="col">
            <div class="page-pretitle">
                Cetak Laporan Guru
            </div>
            <h2 class="page-title">
                Laporan Guru
            </h2>
        </div>

        <div class="col-auto ms-auto d-print-none">
            <div class="btn-list">
                <a href="{{ route('print-report.teacher', ['mapel' => $this->filters['mapel'] ?? '']) }}" target="_blank"
                    class="btn btn-danger" class="btn btn-danger"><span class="las la-print fs-1 me-2"></span>Cetak
                    Laporan Guru</a>
            </div>
        </div>
    </div>

    <x-alert />

    <div class="row mb-3 align-items-center justify-content-between">
        <div class="col-12 col-lg-5 d-flex">
            <div class="w-100">
                <x-datatable.search placeholder="Cari nama guru..." />
            </div>
            <div class="w-100 ms-2">
                <x-form.select wire:model.live="filters.mapel" name="filters.mapel" form-group-class>
                    <option value="">Semua Mata Pelajaran</option>
                    @foreach ($this->subject_studies as $subject_study)
                        <option wire:key="{{ $subject_study->id }}" value="{{ $subject_study->id }}">
                            {{ strtoupper($subject_study->name_subject) }}</option>
                    @endforeach
                </x-form.select>
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
                        <th style="width: 300px">
                            <x-datatable.column-sort name="Guru" wire:click="sortBy('name')" :direction="$sorts['name'] ?? null" />
                        </th>

                        <th style="width: 300px">
                            <x-datatable.column-sort name="Mapel" wire:click="sortBy('subject_study_id')"
                                :direction="$sorts['subject_study_id'] ?? null" />
                        </th>

                        <th>
                            <x-datatable.column-sort name="NIP" wire:click="sortBy('nip')" :direction="$sorts['nip'] ?? null" />
                        </th>

                        <th style="width: 40px">
                            <x-datatable.column-sort name="Email" wire:click="sortBy('email')" :direction="$sorts['email'] ?? null" />
                        </th>

                        <th style="width: 250px">
                            <x-datatable.column-sort name="Alamat" wire:click="sortBy('address')" :direction="$sorts['address'] ?? null" />
                        </th>

                        <th>
                            <x-datatable.column-sort name="Jenis Kelamin" wire:click="sortBy('sex')"
                                :direction="$sorts['sex'] ?? null" />
                        </th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($this->rows as $row)
                        <tr wire:key="row-{{ $row->id }}">
                            <td>{{ $row->name }}</td>

                            <td>{{ strtoupper($row->subject_study->name_subject ?? '-') }}</td>

                            <td><b>{{ $row->nip ?? '-' }}</b></td>

                            <td>{{ $row->user->email ?? '-' }}</td>

                            <td>{{ $row->address ?? '-' }}, {{ $row->postal_code }}</td>

                            <td>{{ $row->sex ?? '-' }}</td>
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
