<div>
    <x-slot name="title">Guru Mata Pelajaran</x-slot>

    <x-slot name="pageTitle">Guru Mata Pelajaran</x-slot>

    <x-slot name="pagePretitle">Kelola Data Mata Pelajaran Guru</x-slot>

    <x-alert />

    <x-modal.delete-confirmation />

    <x-modal :show="$this->showModal" size="md">
        <form wire:submit='changeSubjectStudyTeacher'>
            <div class="modal-header">
                <h5 class="modal-title">Ubah Mata Pelajaran Guru</h5>
                <button wire:click='closeModal' type="button" class="btn-close" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="row">
                    <x-form.select wire:model="mataPelajaran" name="mataPelajaran" label="Mata Pelajaran">
                        <option value="">- pilih mata pelajaran -</option>
                        @foreach ($this->subject_studies as $subject_study)
                            <option wire:key="{{ $subject_study->id }}" value="{{ $subject_study->id }}">
                                {{ strtoupper($subject_study->name_subject) }}</option>
                        @endforeach
                    </x-form.select>
                </div>
            </div>

            <div class="modal-footer">
                <div class="btn-list justify-content-end">
                    <button type="reset" class="btn">Reset</button>

                    <x-datatable.button.save name="Ubah Mata Pelajaran Guru" class="btn btn-blue"
                        target="changeSubjectStudyTeacher" />
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
                <x-datatable.filter.button target="subject-teacher-filter" />
            </div>
        </div>
        <div class="col-auto ms-auto d-flex mt-lg-0 mt-3">
            <x-datatable.items-per-page />

            <button wire:click="muatUlang" class="btn py-1 ms-2"><span class="las la-redo-alt fs-1"></span></button>
        </div>
    </div>

    <x-datatable.filter.card id="subject-teacher-filter">
        <div class="row">
            <div class="col-12 col-lg-4">
                <x-form.input wire:model.live="filters.nip" name="filters.nip" placeholder="Masukkan NIP Guru"
                    label="NIP" type="text" />
            </div>

            <div class="col-12 col-lg-4">
                <x-form.select wire:model.live="filters.mataPelajaran" name="filters.mataPelajaran"
                    label="Mata Pelajaran">
                    <option value="">Semua Mata Pelajaran</option>
                    @foreach ($this->subject_studies as $subject_study)
                        <option wire:key="{{ $subject_study->id }}" value="{{ $subject_study->id }}">
                            {{ ucwords($subject_study->name_subject) }}</option>
                    @endforeach
                </x-form.select>
            </div>

            <div class="col-12 col-lg-4">
                <x-form.select wire:model.live="filters.jenisKelamin" name="filters.jenisKelamin" label="Jenis Kelamin">
                    <option value="">Semua Jenis Kelamin</option>
                    @foreach (config('const.sex') as $sex)
                        <option wire:key="{{ $sex }}" value="{{ $sex }}">
                            {{ ucwords($sex) }}</option>
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

                        <th>
                            <x-datatable.column-sort name="Guru" wire:click="sortBy('name')" :direction="$sorts['name'] ?? null" />
                        </th>

                        <th>
                            <x-datatable.column-sort name="NIP" wire:click="sortBy('nip')" :direction="$sorts['nip'] ?? null" />
                        </th>

                        <th>
                            <x-datatable.column-sort name="Jenis Kelamin" wire:click="sortBy('sex')"
                                :direction="$sorts['sex'] ?? null" />
                        </th>

                        <th style="width: 10px">Guru Mata Pelajaran</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($this->rows as $row)
                        <tr wire:key="row-{{ $row->id }}">
                            <td>
                                <x-datatable.bulk.check wire:model.lazy="selected" value="{{ $row->id }}" />
                            </td>

                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="avatar avatar-sm px-3 me-3"
                                        style="background-image: url({{ $row->photoUrl() ?? $row->user->avatarUrl() }})"></span>

                                    @if (is_online($row->id))
                                        <span class="badge bg-success me-1"></span>
                                    @else
                                        <span class="badge bg-secondary me-1"
                                            title="{{ $row->user->last_seen_time }}"></span>
                                    @endif

                                    <span>{{ $row->name }}</span>
                                </div>
                            </td>

                            <td>{{ $row->nip ?? '-' }}</td>

                            <td>{{ $row->sex ?? '-' }}</td>

                            <td style="width: 300px">
                                <div class="d-flex justify-content-between">
                                    @if ($row->subject_study->name_subject)
                                        <span
                                            class="me-2 align-self-center">{{ strtoupper($row->subject_study->name_subject ?? '') }}</span>
                                    @else
                                        <span class="me-2 badge bg-red-lt align-self-center">Belum Diatur</span>
                                    @endif
                                    <span><button wire:click="openModal({{ $row->id }})" type="button"
                                            class="btn btn-sm btn-green"><span
                                                class="las la-redo-alt fs-1"></span></button></span>
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
