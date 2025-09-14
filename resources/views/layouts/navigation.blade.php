<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom mb-4">
    <div class="container-fluid">
        {{-- Brand / Dashboard --}}
        <a class="navbar-brand" href="{{ route('dashboard') }}">
             <x-application-logo class="block h-9" />
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"
                aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            {{-- Left side menu --}}
            
            @role('Admin')
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('assignments.*') ? 'active fw-bold' : '' }}" href="{{ route('assignments.index') }}">{{ __('messages.nav.assignments') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('vehicles.*') ? 'active fw-bold' : '' }}" href="{{ route('vehicles.index') }}">{{ __('messages.nav.vehicles') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('devices.*') ? 'active fw-bold' : '' }}" href="{{ route('devices.index') }}">{{ __('messages.nav.devices') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('sims.*') ? 'active fw-bold' : '' }}" href="{{ route('sims.index') }}">{{ __('messages.nav.sims') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('sensors.*') ? 'active fw-bold' : '' }}" href="{{ route('sensors.index') }}">{{ __('messages.nav.sensors') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('carriers.*') ? 'active fw-bold' : '' }}" href="{{ route('carriers.index') }}">{{ __('messages.nav.carriers') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active fw-bold' : '' }}"
                        href="{{ route('admin.users.index') }}">
                            {{ __('messages.nav.user_management') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('clients.*') ? 'active fw-bold' : '' }}" href="{{ route('clients.index') }}">{{ __('messages.clients.title') }}</a>
                    </li>
                </ul>
            @endrole

            {{-- Right side menu --}}
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center">
                {{-- Language switcher --}}
                <li class="nav-item d-flex align-items-center gap-2 me-3">
                    <a href="{{ route('locale.switch','ar') }}"
                       class="small text-decoration-underline {{ app()->getLocale()==='ar' ? 'fw-semibold' : '' }}">العربية</a>
                    <span class="text-muted">|</span>
                    <a href="{{ route('locale.switch','en') }}"
                       class="small text-decoration-underline {{ app()->getLocale()==='en' ? 'fw-semibold' : '' }}">EN</a>
                </li>

                {{-- Profile dropdown --}}
                @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDrop" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDrop">
                            @if(Route::has('profile.edit'))
                                <li>
                                    <a class="dropdown-item" href="{{ route('profile.edit') }}">{{ __('messages.nav.profile') }}</a>
                                </li>
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
