@if (isset($label))
    <x-form.select wire:model.lazy="perPage" name="perPage" label="Item">
        <option value="10">10</option>
        <option value="25">25</option>
        <option value="50">50</option>

        @if (isset($all))
            <option value="all">Semua</option>
        @endif
    </x-form.select>
@else
    <div class="d-flex align-items-center" id="perPageWrapper">
        <span class="me-2">Item</span>

        <select class="form-select me-2" id="perPage" wire:model.lazy="perPage">
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>

            @if (isset($all))
                <option value="all">Semua</option>
            @endif
        </select>
    </div>
@endif
