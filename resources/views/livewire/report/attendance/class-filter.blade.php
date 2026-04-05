<div>
    <div class="row mb-1 align-items-center justify-content-between">
        <div class="col-12 col-lg-8 d-flex">
            <div class="w-100">
                <x-datatable.search wire:model.debounce-750ms="filters.search" placeholder="Cari nama siswa..." />
            </div>

            <div class="w-100 ms-2">
                <x-form.select wire:model.live="filters.kelas" name="filters.kelas">
                    <option value="">Semua Kelas</option>
                    @foreach ($this->class_rooms as $class_room)
                        <option value="{{ $class_room->id }}">{{ strtoupper($class_room->name_class) }}</option>
                    @endforeach
                </x-form.select>
            </div>

            <div class="ms-2">
                <x-datatable.filter.button target="attendance-class-filters" />
            </div>

        </div>
        <div class="col-auto ms-auto d-flex mt-lg-0 mt-3">
            <x-datatable.items-per-page />
        </div>
    </div>

    <x-datatable.filter.card id="attendance-class-filters">
        <div class="row">
            <div class="col-12 col-lg-6">
                <x-form.input wire:model.live="filters.startDate" name="startDate" label="Tanggal Mulai"
                    type="date" />
            </div>
            <div class="col-12 col-lg-6">
                <x-form.input wire:model.live="filters.endDate" name="endDate" label="Tanggal Selesai" type="date" />
            </div>
        </div>
    </x-datatable.filter.card>
</div>
