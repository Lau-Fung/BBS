<nav class="navbar navbar-expand-lg navbar-light mb-4" style="background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); border-bottom: 2px solid #1e40af;">
    <div class="container-fluid">
        {{-- Brand / Dashboard --}}
        <a class="navbar-brand d-flex align-items-center px-3 py-2 rounded-lg" 
           href="{{ route('dashboard.index') }}" 
           style="text-decoration: none; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); transition: all 0.3s ease;"
           onmouseover="this.style.background='rgba(255,255,255,0.2)'; this.style.borderColor='rgba(255,255,255,0.3)'"
           onmouseout="this.style.background='rgba(255,255,255,0.1)'; this.style.borderColor='rgba(255,255,255,0.2)'">
            <x-application-logo class="block h-12 me-3" style="filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));" />
            <div class="d-flex flex-column">
                <span class="text-white fw-bold fs-3" style="text-shadow: 0 2px 4px rgba(0,0,0,0.2); line-height: 1.1; letter-spacing: 0.5px;">ESTLUZ</span>
                <small class="text-white-50 fw-medium" style="font-size: 0.8rem; line-height: 1; letter-spacing: 0.3px;">Business Management System</small>
            </div>
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
                        <a class="nav-link text-white {{ request()->routeIs('dashboard.*') ? 'active fw-bold' : '' }}"
                           href="{{ route('dashboard.index') }}"
                           style="{{ request()->routeIs('dashboard.*') ? 'background: rgba(255,255,255,0.2); border-radius: 8px;' : '' }}">
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
                            <a class="nav-link text-white {{ request()->routeIs('admin.users.*') ? 'active fw-bold' : '' }}"
                               href="{{ route('admin.users.index') }}"
                               style="{{ request()->routeIs('admin.users.*') ? 'background: rgba(255,255,255,0.2); border-radius: 8px;' : '' }}">
                                {{ __('messages.nav.user_management') }}
                            </a>
                        </li>
                    @endcan

                    {{-- Activity Logs – Permission-gated --}}
                    @can('activity_logs.view')
                        <li class="nav-item">
                            <a class="nav-link text-white {{ request()->routeIs('activity-logs.*') ? 'active fw-bold' : '' }}"
                               href="{{ route('activity-logs.index') }}"
                               style="{{ request()->routeIs('activity-logs.*') ? 'background: rgba(255,255,255,0.2); border-radius: 8px;' : '' }}">
                                {{ __('messages.nav.activity_logs') ?? 'Activity Logs' }}
                            </a>
                        </li>
                    @endcan

                    {{-- Clients --}}
                    <li class="nav-item">
                        <a class="nav-link text-white {{ request()->routeIs('clients.*') ? 'active fw-bold' : '' }}"
                           href="{{ route('clients.index') }}"
                           style="{{ request()->routeIs('clients.*') ? 'background: rgba(255,255,255,0.2); border-radius: 8px;' : '' }}">
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
                       class="small text-decoration-underline text-white {{ app()->getLocale()==='ar' ? 'fw-semibold' : '' }}"
                       style="{{ app()->getLocale()==='ar' ? 'background: rgba(255,255,255,0.2); padding: 4px 8px; border-radius: 4px;' : '' }}">
                        العربية
                    </a>
                    <span class="text-white">|</span>
                    <a href="{{ route('locale.switch','en') }}"
                       class="small text-decoration-underline text-white {{ app()->getLocale()==='en' ? 'fw-semibold' : '' }}"
                       style="{{ app()->getLocale()==='en' ? 'background: rgba(255,255,255,0.2); padding: 4px 8px; border-radius: 4px;' : '' }}">
                        EN
                    </a>
                </li>

                {{-- Security Status --}}
                @auth
                    <li class="nav-item me-3">
                        <a href="{{ route('profile.security') }}" 
                           class="nav-link d-flex align-items-center text-white {{ request()->routeIs('profile.security') ? 'active' : '' }}"
                           title="{{ auth()->user()->two_factor_secret ? __('messages.security.enabled') : __('messages.security.disabled') }}"
                           style="{{ request()->routeIs('profile.security') ? 'background: rgba(255,255,255,0.2); border-radius: 8px;' : '' }}">
                            @if(auth()->user()->two_factor_secret)
                                <svg class="w-4 h-4 text-green-300 me-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="small text-green-300">{{ __('messages.security.enabled') }}</span>
                            @else
                                <svg class="w-4 h-4 text-gray-300 me-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.293l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13a1 1 0 102 0V9.414l1.293 1.293a1 1 0 001.414-1.414z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="small text-gray-300">{{ __('messages.security.disabled') }}</span>
                            @endif
                        </a>
                    </li>
                @endauth

                {{-- Profile --}}
                @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-white" href="#" id="userDrop" role="button"
                           data-bs-toggle="dropdown" aria-expanded="false"
                           style="background: rgba(255,255,255,0.1); border-radius: 8px; padding: 8px 12px;">
                            {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg" aria-labelledby="userDrop" 
                            style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); min-width: 200px; padding: 8px 0;">
                            @if(Route::has('profile.edit'))
                                <li>
                                    <a class="dropdown-item d-flex align-items-center px-4 py-3 text-gray-700 hover:bg-gray-50 transition-colors duration-200" 
                                       href="{{ route('profile.edit') }}"
                                       style="border-radius: 8px; margin: 2px 8px;">
                                        <svg class="w-4 h-4 me-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        {{ __('messages.nav.profile') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center px-4 py-3 text-gray-700 hover:bg-gray-50 transition-colors duration-200" 
                                       href="{{ route('profile.security') }}"
                                       style="border-radius: 8px; margin: 2px 8px;">
                                        <svg class="w-4 h-4 me-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                        </svg>
                                        {{ __('messages.nav.security') }}
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider my-2" style="border-color: #e5e7eb; margin: 8px 16px;"></li>
                            @endif
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item d-flex align-items-center px-4 py-3 text-red-600 hover:bg-red-50 transition-colors duration-200 w-100 border-0"
                                            style="border-radius: 8px; margin: 2px 8px; background: none;">
                                        <svg class="w-4 h-4 me-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                        </svg>
                                        {{ __('messages.nav.logout') }}
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>
