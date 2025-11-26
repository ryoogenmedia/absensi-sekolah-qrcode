<div>
    <x-slot name="title">Wali Siswa</x-slot>

    <x-slot name="pageTitle">Wali Siswa</x-slot>

    <x-slot name="pagePretitle">Kelola Data Wali Siswa</x-slot>

    <x-slot name="button">
        <x-datatable.button.add name="Tambah Wali Siswa" :route="route('guardian-student.create')" />
    </x-slot>

    <x-alert />

    <x-modal.delete-confirmation />

    <x-modal :show="$showModalExcel" size="md">
        <form wire:submit.prevent="importExcel" autocomplete="off">
            <div class="modal-header">
                <h5 class="modal-title">Import File Excel Wali Siswa</h5>
                <button wire:click='closeModalExcel' type="button" class="btn-close" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <x-form.input wire:model.live.debounce.250ms="fileExcel" name="fileExcel" label="File Excel"
                    type="file" placeholder="Masukkan file excel"
                    accept=".xlsx,.xls,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel" />

                <p>unduh template import excel wali siswa <a href="/template/wali-siswa-template-excel.xlsx">disini</a>
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

    <div class="row mb-3 align-items-center justify-content-between">
        <div class="col-12 col-lg-8 d-flex align-self-center">
            <div>
                <x-datatable.search placeholder="Cari nama wali siswa / siswa..." />
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

    <div class="card" wire:loading.class.delay="card-loading" wire:offline.class="card-loading">
        <div class="table-responsive mb-0">
            <table class="table card-table table-bordered datatable">
                <thead>
                    <tr>
                        <th class="w-1">
                            <x-datatable.bulk.check wire:model.lazy="selectPage" />
                        </th>

                        <th>
                            <x-datatable.column-sort name="Wali Siswa" wire:click="sortBy('guardian_name')"
                                :direction="$sorts['guardian_name'] ?? null" />
                        </th>

                        <th>
                            <x-datatable.column-sort name="Nama Siswa" wire:click="sortBy('student.name')"
                                :direction="$sorts['student.name'] ?? null" />
                        </th>

                        <th>
                            <x-datatable.column-sort name="Hubungan" wire:click="sortBy('guardian_relationship')"
                                :direction="$sorts['guardian_relationship'] ?? null" />
                        </th>

                        <th>
                            <x-datatable.column-sort name="Kontak Wali" wire:click="sortBy('guardian_contact')"
                                :direction="$sorts['guardian_contact'] ?? null" />
                        </th>

                        <th>
                            <x-datatable.column-sort name="Akun" wire:click="sortBy('user.email')"
                                :direction="$sorts['user.email'] ?? null" />
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
                                        <span>Anda telah memilih <strong>{{ $this->rows->total() }}</strong> wali siswa,
                                            apakah
                                            Anda mau memilih semua <strong>{{ $this->rows->total() }}</strong>
                                            wali siswa?</span>

                                        <button wire:click="selectedAll" class="btn btn-sm ms-2">
                                            Pilih Semua Data Wali Siswa
                                        </button>
                                    </div>
                                @else
                                    <span class="text-pink">Anda sekarang memilih semua
                                        <strong>{{ count($this->selected) }}</strong> wali siswa.
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
                                {{ $row->guardian_name ?? '-' }}
                            </td>

                            <td>{{ $row->student->full_name ?? '-' }}</td>

                            <td>
                                {{ ucwords($row->guardian_relationship) ?? '-' }}
                            </td>

                            <td>
                                {{ $row->guardian_contact ?? '-' }}
                            </td>

                            <td>
                                {{ $row->user->email ?? '-' }}
                            </td>

                            <td>
                                <div class="d-flex">
                                    <div class="ms-auto">
                                        <a class="btn btn-sm" href="{{ route('guardian-student.edit', $row->id) }}">
                                            Sunting
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
