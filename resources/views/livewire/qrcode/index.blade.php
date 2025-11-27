@push('styles')
    <style>
        .content-button {
            margin-top: -75px;
        }
    </style>
@endpush
<div>
    <x-slot name="title">Qrcode Siswa</x-slot>

    <div class="row g-2 align-items-center mb-4">
        <div class="col">
            <div class="page-pretitle">
                Qrcode Siswa
            </div>
            <h2 class="page-title">
                Qrcode Siswa
            </h2>
        </div>

        <div class="col-auto ms-auto d-print-none">
            <div class="btn-list">
                <a href="{{ route('print-pdf.card', ['kelas' => $filters['kelas'] ?? '']) }}" target="_blank"
                    class="btn btn-blue">
                    <span class="las la-id-card fs-1 me-lg-2 me-0"></span>
                    <span class="d-lg-inline d-none">Cetak Semua Kartu Siswa</span>
                </a>
            </div>
        </div>
    </div>

    <x-alert />

    <x-modal.delete-confirmation />

    <div class="row mb-3 align-items-center justify-content-between">
        <div class="col-12 col-lg-8 d-flex">
            <div class="w-50">
                <x-datatable.search placeholder="Cari nama siswa..." />
            </div>

            <div class="w-100 ms-2 d-flex gap-2">
                <x-form.input wire:model.live="filters.nis" name="filters.nis" placeholder="Masukkan NIS Siswa"
                    type="text" form-group-class />
                <x-form.select wire:model.live="filters.kelas" name="filters.kelas" form-group-class>
                    <option value="">Semua Kelas</option>
                    @foreach ($this->class_rooms as $class_room)
                        <option wire:key="{{ $class_room->id }}" value="{{ $class_room->id }}">
                            {{ strtoupper($class_room->name_class) }}</option>
                    @endforeach
                </x-form.select>
            </div>
        </div>
        <div class="col-auto ms-auto d-flex mt-lg-0 mt-3">
            <x-datatable.items-per-page />

            <x-datatable.bulk.dropdown>
                <div class="dropdown-menu dropdown-menu-end datatable-dropdown">
                    <button data-bs-toggle="modal" data-bs-target="#delete-confirmation" class="dropdown-item"
                        type="button">
                        <i class="las la-trash me-3"></i>

                        <span>Hapus</span>
                    </button>
                </div>
            </x-datatable.bulk.dropdown>

            <button wire:click="muatUlang" class="btn py-1 ms-2"><span class="las la-redo-alt fs-1"></span></button>
        </div>
    </div>

    <div class="card" wire:loading.class.delay="card-loading" wire:offline.class="card-loading">
        <div class="table-responsive mb-0">
            <table class="table card-table table-bordered datatable">
                <thead>
                    <tr>
                        <th class="w-1">
                            <x-datatable.bulk.check wire:model.lazy="selectPage" />
                        </th>

                        <th style="width: 20px">QR Code</th>

                        <th>NIS</th>

                        <th>Nama Siswa</th>

                        <th>Kelas</th>

                        <th style="width: 10px"></th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($this->rows as $row)
                        <tr wire:key="row-{{ $row->id }}">
                            <td>
                                <x-datatable.bulk.check wire:model.lazy="selected" value="{{ $row->id }}" />
                            </td>

                            <td class="text-center">{!! DNS2D::getBarcodeHTML("$row->nis", 'QRCODE', 4, 4) !!}</td>

                            <td><b>{{ $row->nis ?? '-' }}</b></td>

                            <td>{{ $row->full_name ?? '-' }}</td>

                            <td class="text-center"><b>{{ $row->class_room->name_class ?? '-' }}</b></td>

                            <td>
                                <a href="{{ route('print-pdf.card', ['card_id' => $row->nis ?? '']) }}" target="_blank"
                                    class="btn btn-blue btn-sm"><span class="las la-id-card fs-1"></span></a>
                            </td>
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
