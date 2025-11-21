<div>
    <x-slot name="title">Guru</x-slot>

    <x-slot name="pageTitle">Guru</x-slot>

    <x-slot name="pagePretitle">Kelola Data Guru</x-slot>

    <x-slot name="button">
        <x-datatable.button.add name="Tambah Guru" :route="route('teacher.create')" />
    </x-slot>

    <x-alert />

    <x-modal.delete-confirmation />

    <x-modal :show="$showModalExcel" size="md">
        <form wire:submit.prevent="importExcel" autocomplete="off">
            <div class="modal-header">
                <h5 class="modal-title">Import File Excel Guru</h5>
                <button wire:click='closeModalExcel' type="button" class="btn-close" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <x-form.input wire:model.live.debounce.250ms="fileExcel" name="fileExcel" label="File Excel"
                    type="file" placeholder="Masukkan file excel"
                    accept=".xlsx,.xls,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel" />

                <p>unduh template import excel guru <a href="/template/guru-template-excel.xlsx">disini</a></p>
            </div>

            <div class="modal-footer">
                <div class="btn-list justify-content-end">
                    <button wire:click="resetForm" type="reset" class="btn">Reset</button>

                    <x-datatable.button.save target="importExcel" name="Import Excel" class="btn btn-green" />
                </div>
            </div>
        </form>
    </x-modal>

    <div class="row mb-3 align-items-center justify-content-between">
        <div class="col-12 col-lg-5 d-flex">
            <div class="w-100">
                <x-datatable.search placeholder="Cari nama guru..." />
            </div>
            <div class="w-50 ms-2">
                <x-datatable.filter.button target="teacher-filter" />
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

            <button wire:click="muatUlang" class="btn py-1 ms-2"><span class="las la-redo-alt fs-1"></span></button>
        </div>
    </div>

    <x-datatable.filter.card id="teacher-filter">
        <div class="row">
            <div class="col-12 col-lg-6">
                <x-form.input wire:model.live="filters.nip" name="filters.nip" placeholder="Masukkan NIP Guru"
                    label="NIP" type="text" />

                <x-form.select wire:model.live="filters.jenisKelamin" name="filters.jenisKelamin" label="Jenis Kelamin">
                    <option value="">Semua Jenis Kelamin</option>
                    @foreach (config('const.sex') as $sex)
                        <option wire:key="{{ $sex }}" value="{{ $sex }}">
                            {{ ucwords($sex) }}</option>
                    @endforeach
                </x-form.select>
            </div>

            <div class="col-12 col-lg-6">
                <x-form.input wire:model.live="filters.email" name="filters.email" placeholder="Masukkan Email"
                    label="Email" type="text" />

                <x-form.select wire:model.live="filters.agama" name="filters.agama" label="Agama">
                    <option value="">Semua Agama</option>
                    @foreach (config('const.religions') as $religion)
                        <option wire:key="{{ $religion }}" value="{{ $religion }}">
                            {{ ucwords($religion) }}</option>
                    @endforeach
                </x-form.select>
            </div>
        </div>
    </x-datatable.filter.card>

    <div class="card" wire:loading.class.delay="card-loading" wire:offline.class="card-loading">
        <div class="table-responsive mb-0">
            <table class="table card-table table-bordered datatable">
                <thead>
                    <tr>
                        <th class="w-1">
                            <x-datatable.bulk.check wire:model.lazy="selectPage" />
                        </th>

                        <th style="width: 300px">
                            <x-datatable.column-sort name="Guru" wire:click="sortBy('name')" :direction="$sorts['name'] ?? null" />
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

                        <th style="width: 10px"></th>
                    </tr>
                </thead>

                <tbody>
                    @if ($selectPage)
                        <tr>
                            <td colspan="10" class="bg-orange-lt rounded-0">
                                @if (!$selectAll)
                                    <div class="text-orange">
                                        <span>Anda telah memilih <strong>{{ $this->rows->total() }}</strong> admin,
                                            apakah
                                            Anda mau memilih semua <strong>{{ $this->rows->total() }}</strong>
                                            admin?</span>

                                        <button wire:click="selectedAll" class="btn btn-sm ms-2">
                                            Pilih Semua Data Admin
                                        </button>
                                    </div>
                                @else
                                    <span class="text-pink">Anda sekarang memilih semua
                                        <strong>{{ count($this->selected) }}</strong> admin.
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endif

                    @forelse ($this->rows as $row)
                        <tr wire:key="row-{{ $row->id }}">
                            <td>
                                <x-datatable.bulk.check wire:model.lazy="selected" value="{{ $row->id }}" />
                            </td>

                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="avatar avatar-sm px-3 me-2"
                                        style="background-image: url({{ $row->photoUrl() ?? $row->user->avatarUrl() }})"></span>

                                    <span>{{ $row->name }}</span>
                                </div>
                            </td>

                            <td><b>{{ $row->nip ?? '-' }}</b></td>

                            <td>{{ $row->user->email ?? '-' }}</td>

                            <td>{{ $row->address ?? '-' }}, {{ $row->postal_code }}</td>

                            <td>{{ $row->sex ?? '-' }}</td>

                            <td>
                                <div class="d-flex">
                                    <div class="ms-auto">
                                        <a class="btn btn-sm" href="{{ route('teacher.edit', $row->id) }}">
                                            Sunting
                                        </a>

                                        <a class="btn btn-sm" href="{{ route('teacher.detail', $row->id) }}">
                                            Detail
                                        </a>
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
