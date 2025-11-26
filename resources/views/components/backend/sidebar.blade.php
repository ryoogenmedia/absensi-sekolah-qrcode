<aside class="navbar navbar-vertical navbar-expand-lg" data-bs-theme="light">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu"
            aria-controls="sidebar-menu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <h1 class="navbar-brand navbar-brand-autodark d-lg-flex d-none">
            <a href="{{ route('home') }}" class="px-3">
                <img src="{{ asset('static/ryoogen/logo/DARK.png') }}" width="100%" height="100%" alt="Tabler">
            </a>
        </h1>

        <h1 class="navbar-brand navbar-brand-autodark d-lg-none d-flex">
            <a href="{{ route('home') }}" class="px-3">
                <img style="width: 150px" src="{{ asset('static/ryoogen/logo/DARK.png') }}" width="100%"
                    height="100%" alt="Tabler">
            </a>
        </h1>

        <div class="navbar-nav flex-row d-lg-none">
            <x-backend._partials.profile-dropdown />
        </div>

        <div class="collapse navbar-collapse" id="sidebar-menu">
            <ul class="navbar-nav pt-lg-3">
                @foreach (config('sidebar') as $item)
                    <li
                        class="nav-item {{ isset($item['sub-menus']) ? 'dropdown' : '' }} {{ Route::is($item['route-name']) || Route::is($item['is-active']) ? 'active border border-top-0 border-start-0 border-bottom-0 border-end-1 rounded-0' : '' }}">
                        @if (!isset($item['sub-menus']))
                            @if (in_array(auth()->user()->role, $item['roles']))
                                <a class="nav-link" href="{{ route($item['route-name']) }}"
                                    title="{{ $item['description'] }}">
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        @if (!isset($item['brand_icon']))
                                            <h2 class="las la-{{ $item['icon'] }} fs-2"></h2>
                                        @else
                                            <h2 class="lab la-{{ $item['icon'] }} fs-2"></h2>
                                        @endif
                                    </span>

                                    <span class="nav-link-title">
                                        {{ $item['title'] }}
                                    </span>
                                </a>
                            @endif
                        @else
                            @if (in_array(auth()->user()->role, $item['roles']))
                                <a class="nav-link dropdown-toggle {{ Route::is($item['route-name']) || Route::is($item['is-active']) ? 'border border-top-0 border-start-0 border-bottom-0 border-end-1 rounded-0' : '' }}"
                                    href="#sidebar-{{ Str::random(10) }}-{{ Str::slug($item['title']) }}"
                                    data-bs-toggle="dropdown" role="button" aria-expanded="false"
                                    title="{{ $item['description'] }}">
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        @if (!isset($item['brand_icon']))
                                            <h2 class="las la-{{ $item['icon'] }} fs-2"></h2>
                                        @else
                                            <h2 class="lab la-{{ $item['icon'] }} fs-2"></h2>
                                        @endif
                                    </span>

                                    <span class="nav-link-title">
                                        {{ $item['title'] }}
                                    </span>
                                </a>

                                <div
                                    class="dropdown-menu border border-top-0 border-start-0 border-bottom-0 border-end-1 rounded-0 {{ Route::is($item['route-name']) || Route::is($item['is-active']) ? 'show' : '' }}">
                                    <div class="dropdown-menu-columns">
                                        <div class="dropdown-menu-column">
                                            @foreach ($item['sub-menus'] as $item => $subMenu)
                                                @if (!isset($subMenu['sub-menus']))
                                                    <a class="dropdown-item {{ Route::is($subMenu['route-name']) || Route::is($subMenu['is-active']) ? 'active' : '' }}"
                                                        href="{{ route($subMenu['route-name']) }}">{{ $subMenu['title'] }}</a>
                                                @else
                                                    <div class="dropend">
                                                        <a class="dropdown-item dropdown-toggle"
                                                            href="#sidebar-{{ Str::random(10) }}-{{ Str::slug($subMenu['title']) }}"
                                                            data-bs-toggle="dropdown" role="button"
                                                            aria-expanded="false">{{ $subMenu['title'] }}</a>

                                                        <div
                                                            class="dropdown-menu {{ Route::is($subMenu['route-name']) || Route::is($subMenu['is-active']) ? 'show' : '' }}">
                                                            @foreach ($subMenu['sub-menus'] as $lastMenu)
                                                                <a href="{{ route($lastMenu['route-name']) }}"
                                                                    class="dropdown-item {{ Route::is($lastMenu['route-name']) || Route::is($lastMenu['is-active']) ? 'active' : '' }}">{{ $lastMenu['title'] }}</a>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</aside>

@push('scripts')
    <script src="{{ asset('js/sidebar.js') }}"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll(".dropdown-menu").forEach(function(menu) {
                menu.addEventListener("click", function(e) {
                    e.stopPropagation(); // mencegah dropdown menutup
                });
            });
        });
    </script>
@endpush
