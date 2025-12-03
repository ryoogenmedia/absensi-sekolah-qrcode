<div class="nav-item dropdown">
    <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown" aria-label="Open user menu">
        <span class="avatar avatar-sm" style="background-image: url({{ auth()->user()->avatarUrl() }})"></span>

        <div class="d-none d-xl-block ps-2">
            @if (auth()->user()->role == 'siswa' && auth()->user()->student())
                <div>{{ strtolower(auth()->user()->student->full_name) }}</div>
            @else
                <div>{{ strtolower(auth()->user()->username) }}</div>
            @endif

            <div class="mt-1 small text-muted">
                {{ ucwords(str_replace('-', ' ', auth()->user()->role)) }}
            </div>
        </div>
    </a>

    <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
        <a href="{{ route('setting.profile.index') }}" class="dropdown-item">Profil</a>
        <a href="{{ route('setting.account.index') }}" class="dropdown-item">Akun</a>

        <div class="dropdown-divider"></div>

        <a href="{{ route('logout') }}"
            onclick="event.preventDefault();
        document.getElementById('logout-form').submit();"
            class="dropdown-item">Keluar</a>
    </div>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
</div>
