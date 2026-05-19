@push('styles')
    <style>
        .custom-header {
            margin-bottom: 20px !important;
            margin-top: -68px;
        }
    </style>
@endpush

<div>
    <x-slot name="title">Sesi Sekolah</x-slot>

    <div class="row g-2 align-items-center mb-4">
        <div class="col">
            <div class="page-pretitle">
                Sesi Sekolah
            </div>
            <h2 class="page-title">
                Sesi Sekolah
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
                            Tambah Sesi Sekolah
                        </span>
                    </button>
                @endif
            </div>
        </div>
    </div>

    <x-alert />

    <x-modal.delete-confirmation />

    <div class="row">
        @if ($modalCreate || $modalEdit)
            <div class="col-lg-4 col-12 mb-lg-0 mb-3">
                <form class="card" wire:submit.prevent="save" autocomplete="off">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>{{ $schoolSessionId ? 'Sunting' : 'Tambah' }} Data Sesi</span>
                        <button type="button" wire:click="closeModal" class="btn-sm btn btn-red d-sm-inline-block">
                            <i class="fs-1 las la-times"></i>
                        </button>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <x-form.input wire:model="sessionName" name="sessionName" label="Nama Sesi"
                                    placeholder="Contoh: Sesi 1" type="text" required autofocus />

                                <div class="row">
                                    <div class="col-6">
                                        <x-form.input wire:model="startTime" name="startTime" label="Jam Mulai"
                                            type="time" required />
                                    </div>
                                    <div class="col-6">
                                        <x-form.input wire:model="endTime" name="endTime" label="Jam Selesai"
                                            type="time" required />
                                    </div>
                                </div>

                                <x-form.textarea wire:model="description" name="description" label="Deskripsi Sesi"
                                    placeholder="Contoh: Sesi Pelajaran Pagi Pertama" required />

                                <x-form.toggle label="Status Aktif" wire:model="statusActive" name="statusActive"
                                    label="Status" />
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="btn-list justify-content-end">
                            <button type="reset" class="btn">Reset</button>

                            <x-datatable.button.save target="save" name="{{ $schoolSessionId ? 'Sunting' : 'Tambah' }}" />
                        </div>
                    </div>
                </form>
            </div>
        @endif

        <div class="{{ $modalCreate || $modalEdit ? 'col-lg-8' : '' }} col-12">
            <div class="row mb-3 align-items-center justify-content-between">
                <div class="col-12 col-lg-6 d-flex align-self-center">
                    <x-datatable.search placeholder="Cari nama sesi..." />
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

                                <th style="width: 150px">
                                    <x-datatable.column-sort name="Nama Sesi" wire:click="sortBy('session_name')"
                                        :direction="$sorts['session_name'] ?? null" />
                                </th>

                                <th style="width: 180px" class="text-center">
                                    <span>Jam Pelajaran</span>
                                </th>

                                <th>
                                    <x-datatable.column-sort name="Deskripsi" wire:click="sortBy('description')"
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
                                                    sesi sekolah,
                                                    apakah
                                                    Anda mau memilih semua <strong>{{ $this->rows->total() }}</strong>
                                                    sesi sekolah?</span>

                                                <button wire:click="selectedAll" class="btn btn-sm ms-2">
                                                    Pilih Semua Data Sesi Sekolah
                                                </button>
                                            </div>
                                        @else
                                            <span class="text-pink">Anda sekarang memilih semua
                                                <strong>{{ count($this->selected) }}</strong> sesi sekolah.
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

                                    <td class="text-center"><b>{{ $row->session_name ?? '-' }}</b></td>

                                    <td class="text-center">
                                        <span class="badge bg-blue-lt">
                                            {{ date('H:i', strtotime($row->start_time)) }} - {{ date('H:i', strtotime($row->end_time)) }}
                                        </span>
                                    </td>

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
