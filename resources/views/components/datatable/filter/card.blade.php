<div id="{{ $id }}" class="collapse bg-green-lt mb-3 card px-2" wire:ignore>
    <div class="card-body">
        {{ $slot }}

        <div class="text-end">
            <button wire:click="resetFilters" class="btn btn-sm" type="button">Reset Filter</button>
        </div>
    </div>
</div>
