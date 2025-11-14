@push('styles')
    <style>
        .spin-animation {
            display: inline-block;
            animation: spin 1s linear infinite;
            font-size: 2.5rem;
            /* Membesarkan ikon */
            font-weight: bold;
            /* Membuat ikon lebih bold */
        }

        @keyframes spin {
            100% {
                transform: rotate(360deg);
            }
        }
    </style>
@endpush

<div class="d-print-none">
    <div style="z-index: 9999; position: fixed; bottom: 20px; right: 20px;"
        class="d-flex flex-column align-items-end gap-2">

        <div class="btn btn-blue" id="loading-indicator" wire:loading.delay>
            <i class="las la-sync-alt spin-animation"></i>
        </div>

        <span class="btn btn-red" id="loading-indicator" wire:offline>
            <i class="fs-1 las la-plane p-2"></i> Anda sedang offline.
        </span>
    </div>

    @if (session('alert'))
        <div class="alert alert-{{ $type }} alert-dismissible bg-white" role="alert">
            <div class="d-flex">
                <div class="me-3">
                    <h1 class="text-{{ $type }} las la-{{ $icon }}"></h1>
                </div>

                <div>
                    <h4 class="alert-title">{{ $message }}</h4>
                    <div class="text-muted">{{ $detail }}</div>
                </div>
            </div>

            <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
        </div>
    @endif
</div>
