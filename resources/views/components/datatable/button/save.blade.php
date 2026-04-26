<button type="submit" class="btn {{ $class ?? 'btn-green' }}" wire:loading.attr="disabled">
    <span wire:loading.remove wire:target="{{ $target }}">{{ $name ?? 'Simpan' }}</span>

    <span wire:loading wire:target="{{ $target }}">Memuat</span>

    <span class="animated-dots" wire:loading wire:target="{{ $target }}"></span>
</button>
