<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom mb-4">
    <div class="container-fluid">
        {{-- Brand / Dashboard --}}
        <a class="navbar-brand" href="{{ route('dashboard.index') }}">
            <x-application-logo class="block h-9" />
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"
                aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            {{-- Left side --}}
            @auth
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    {{-- Core modules (visible to authenticated users) --}}
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard.*') ? 'active fw-bold' : '' }}"
                           href="{{ route('dashboard.index') }}">
                            {{ __('messages.nav.dashboard') }}
                        </a>
                    </li>
                    {{-- <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('assignments.*') ? 'active fw-bold' : '' }}"
                           href="{{ route('assignments.index') }}">
                            {{ __('messages.nav.assignments') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('vehicles.*') ? 'active fw-bold' : '' }}"
                           href="{{ route('vehicles.index') }}">
                            {{ __('messages.nav.vehicles') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('devices.*') ? 'active fw-bold' : '' }}"
                           href="{{ route('devices.index') }}">
                            {{ __('messages.nav.devices') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('sims.*') ? 'active fw-bold' : '' }}"
                           href="{{ route('sims.index') }}">
                            {{ __('messages.nav.sims') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('sensors.*') ? 'active fw-bold' : '' }}"
                           href="{{ route('sensors.index') }}">
                            {{ __('messages.nav.sensors') }}
                        </a>
                    </li> --}}

                    {{-- Reference data (Carriers) – permission-gated --}}
                    {{-- @can('admin.reference.manage')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('carriers.*') ? 'active fw-bold' : '' }}"
                               href="{{ route('carriers.index') }}">
                                {{ __('messages.nav.carriers') }}
                            </a>
                        </li>
                    @endcan --}}

                    {{-- User management – Admin only (or anyone with users.view) --}}
                    @can('users.view')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active fw-bold' : '' }}"
                               href="{{ route('admin.users.index') }}">
                                {{ __('messages.nav.user_management') }}
                            </a>
                        </li>
                    @endcan

                    {{-- Clients --}}
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('clients.*') ? 'active fw-bold' : '' }}"
                           href="{{ route('clients.index') }}">
                            {{ __('messages.clients.title') }}
                        </a>
                    </li>

                    {{-- Imports (optional quick link) --}}
                    {{-- @can('assignments.view')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('imports.assignments.*') ? 'active fw-bold' : '' }}"
                               href="{{ route('imports.assignments.form') }}">
                                {{ __('messages.nav.imports') ?? 'Imports' }}
                            </a>
                        </li>
                    @endcan --}}
                </ul>
            @endauth

            {{-- Right side --}}
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center">
                {{-- Language switcher --}}
                <li class="nav-item d-flex align-items-center gap-2 me-3">
                    <a href="{{ route('locale.switch','ar') }}"
                       class="small text-decoration-underline {{ app()->getLocale()==='ar' ? 'fw-semibold' : '' }}">
                        العربية
                    </a>
                    <span class="text-muted">|</span>
                    <a href="{{ route('locale.switch','en') }}"
                       class="small text-decoration-underline {{ app()->getLocale()==='en' ? 'fw-semibold' : '' }}">
                        EN
                    </a>
                </li>

                {{-- Security Status --}}
                @auth
                    <li class="nav-item me-3">
                        <a href="{{ route('profile.security') }}" 
                           class="nav-link d-flex align-items-center {{ request()->routeIs('profile.security') ? 'active' : '' }}"
                           title="{{ auth()->user()->two_factor_secret ? __('messages.security.enabled') : __('messages.security.disabled') }}">
                            @if(auth()->user()->two_factor_secret)
                                <svg class="w-4 h-4 text-green-600 me-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="small text-green-600">{{ __('messages.security.enabled') }}</span>
                            @else
                                <svg class="w-4 h-4 text-gray-400 me-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.293l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13a1 1 0 102 0V9.414l1.293 1.293a1 1 0 001.414-1.414z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="small text-gray-500">{{ __('messages.security.disabled') }}</span>
                            @endif
                        </a>
                    </li>
                @endauth

                {{-- Profile --}}
                @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDrop" role="button"
                           data-bs-toggle="dropdown" aria-expanded="false">
                            {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDrop">
                            @if(Route::has('profile.edit'))
                                <li><a class="dropdown-item" href="{{ route('profile.edit') }}">{{ __('messages.nav.profile') }}</a></li>
                                <li><a class="dropdown-item" href="{{ route('profile.security') }}">{{ __('messages.nav.security') }}</a></li>
                                <li><hr class="dropdown-divider"></li>
                            @endif
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">{{ __('messages.nav.logout') }}</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>
