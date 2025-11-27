@push('styles')
    <style>
        .custom-header {
            margin-bottom: 20px !important;
            margin-top: -68px;
        }
    </style>
@endpush

<div>
    <x-slot name="title">Mata Pelajaran</x-slot>

    <div class="row g-2 align-items-center mb-4">
        <div class="col">
            <div class="page-pretitle">
                Mata Pelajaran
            </div>
            <h2 class="page-title">
                Mata Pelajaran
            </h2>
        </div>

        <div class="col-auto ms-auto d-print-none">
            <div class="btn-list">
                @if ($modalCreate || $modalEdit)
                    <button wire:click="closeModal" class="btn d-sm-inline-block">
                        <i class="las la-times me-lg-1"></i>
                        <span class="d-none d-lg-inline">
                            Tutup Form
                        </span>
                    </button>
                @else
                    <button wire:click="openModalCreate" class="btn btn-blue d-sm-inline-block">
                        <i class="las la-plus me-lg-1"></i>
                        <span class="d-none d-lg-inline">
                            Tambah Mata Pelajaran
                        </span>
                    </button>
                @endif
            </div>
        </div>
    </div>

    <x-alert />

    <x-modal.delete-confirmation />

    <x-modal :show="$showModalExcel" size="md">
        <form wire:submit.prevent="importExcel" autocomplete="off">
            <div class="modal-header">
                <h5 class="modal-title">Import File Excel Mata Pelajaran</h5>
                <button wire:click='closeModalExcel' type="button" class="btn-close" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <x-form.input wire:model.live.debounce.250ms="fileExcel" name="fileExcel" label="File Excel"
                    type="file" placeholder="Masukkan file excel"
                    accept=".xlsx,.xls,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel" />

                <p>unduh template import excel mata pelajaran <a href="/template/mapel-template-excel.xlsx">disini</a>
                </p>
            </div>

            <div class="modal-footer">
                <div class="btn-list justify-content-end">
                    <button wire:click="resetForm" type="reset" class="btn">Reset</button>

                    <x-datatable.button.save target="importExcel" name="Import Excel" class="btn btn-green" />
                </div>
            </div>
        </form>
    </x-modal>

    <div class="row">
        @if ($modalCreate || $modalEdit)
            <div class="col-lg-4 col-12 mb-lg-0 mb-3">
                <form class="card" wire:submit.prevent="save" autocomplete="off">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>{{ $subjectStudyId ? 'Sunting' : 'Tambah' }} Data Mata Pelajaran</span>
                        <button type="button" wire:click="closeModal" class="btn-sm btn btn-red d-sm-inline-block">
                            <i class="fs-1 las la-times"></i>
                        </button>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <x-form.input wire:model="namaMataPelajaran" name="namaMataPelajaran"
                                    label="Nama Mata Pelajaran" placeholder="masukkan nama kelas" type="text"
                                    required autofocus />

                                <x-form.textarea wire:model="deskripsi" name="deskripsi"
                                    label="Deskripsi Mata Pelajaran"
                                    placeholder="masukkan deskripsi mata pelajaran / penjelasan tentang mata pelajaran"
                                    required />

                                <x-form.toggle label="Status Aktif Mapel" wire:model="statusAktif" name="statusAktif"
                                    label="Status" />
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="btn-list justify-content-end">
                            <button type="reset" class="btn">Reset</button>

                            <x-datatable.button.save target="save"
                                name="{{ $subjectStudyId ? 'Sunting' : 'Tambah' }}" />
                        </div>
                    </div>
                </form>
            </div>
        @endif

        <div class="{{ $modalCreate || $modalEdit ? 'col-lg-8' : '' }} col-12">
            <div class="row mb-3 align-items-center justify-content-between">
                <div class="col-12 col-lg-6 d-flex align-self-center">
                    <x-datatable.search placeholder="Cari nama kelas..." />
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

                            <button class="dropdown-item" type="button" wire:click="openModalExcel">
                                <i class="las la-file-excel me-3"></i>

                                <span>Import Excel</span>
                            </button>

                            <button class="dropdown-item" type="button" wire:click="exportExcel">
                                <i class="las la-file-excel me-3"></i>

                                <span>Export Excel</span>
                            </button>
                        </div>
                    </x-datatable.bulk.dropdown>

                    <button wire:click="muatUlang" class="btn py-1 ms-2"><span
                            class="las la-redo-alt fs-1"></span></button>
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

                                <th style="width: 200px">
                                    <x-datatable.column-sort name="Nama Mata Pelajaran"
                                        wire:click="sortBy('name_subject')" :direction="$sorts['name_subject'] ?? null" />
                                </th>

                                <th>
                                    <x-datatable.column-sort name="Description" wire:click="sortBy('description')"
                                        :direction="$sorts['description'] ?? null" />
                                </th>

                                <th class="text-center" style="width: 100px">
                                    <x-datatable.column-sort name="Status" wire:click="sortBy('status_active')"
                                        :direction="$sorts['status_active'] ?? null" />
                                </th>

                                <th style="width: 10px"></th>
                            </tr>
                        </thead>

                        <tbody>
                            @if ($selectPage)
                                <tr>
                                    <td colspan="10" class="bg-orange-lt rounded-0">
                                        @if (!$selectAll)
                                            <div class="text-orange">
                                                <span>Anda telah memilih <strong>{{ $this->rows->total() }}</strong>
                                                    mata pelajaran,
                                                    apakah
                                                    Anda mau memilih semua <strong>{{ $this->rows->total() }}</strong>
                                                    mata pelajaran?</span>

                                                <button wire:click="selectedAll" class="btn btn-sm ms-2">
                                                    Pilih Semua Data Mata Pelajara
                                                </button>
                                            </div>
                                        @else
                                            <span class="text-pink">Anda sekarang memilih semua
                                                <strong>{{ count($this->selected) }}</strong> mata pelajaran.
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endif

                            @forelse ($this->rows as $row)
                                <tr wire:key="row-{{ $row->id }}">
                                    <td>
                                        <x-datatable.bulk.check wire:model.lazy="selected"
                                            value="{{ $row->id }}" />
                                    </td>

                                    <td><b>{{ $row->name_subject ?? '-' }}</b></td>

                                    <td>{{ $row->description ?? '-' }}</td>

                                    <td class="text-center">
                                        <div class="d-flex justify-content-center">
                                            <x-form.toggle wire:change="changeStatusActive({{ $row->id }})"
                                                name="changeStatusActive" :checked="$row->status_active == 1 ? true : false" />
                                        </div>
                                    </td>

                                    <td>
                                        <div class="d-flex">
                                            <div class="ms-auto">
                                                <button class="btn btn-sm"
                                                    wire:click="openModalEdit({{ $row->id }})">
                                                    Sunting
                                                </button>
                                            </div>
                                        </div>
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
    </div>
</div>
