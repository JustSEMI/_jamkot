@php
    $username = auth()->user()->username ?? 'admin';
    $initials = strtoupper(substr($username, 0, 2));
    $isAdmin = auth()->user()->canAccess('admin');
@endphp

<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <h2>JAMKOT</h2>
    </div>
    <!-- User Profile Card -->
    <div class="user-profile-card">
        <div class="avatar">
            {{ $initials }}
        </div>
        <div class="user-info">
            <span class="username">{{ $username }}</span>
            <div class="role-badge">
                <span class="status-dot online"></span>
                <span>{{ auth()->user()->isAdmin() ? 'ADMIN' : strtoupper(auth()->user()->role ?? 'user') }}</span>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="sidebar-nav">
        {{-- Panel --}}
        @if(auth()->user()->canAccess('panel'))
        <a href="{{ route('panel') }}" class="nav-link {{ Route::is('panel') ? 'active' : '' }}">
            <i class="fa-solid fa-gauge"></i>
            <span>Panel</span>
        </a>

        {{-- Device Status --}}
        <a href="{{ route('device') }}" class="nav-link {{ Route::is('device*') ? 'active' : '' }}">
            <svg viewBox="0 0 64 64" width="20" height="20" xmlns="http://www.w3.org/2000/svg" style="fill: none; stroke: currentColor; stroke-width: 4; stroke-linecap: round; stroke-linejoin: round; width: 20px; height: 20px; flex-shrink: 0;">
                <!-- Antenna/PCB trace -->
                <path d="M 24 8 L 40 8 L 40 14 L 36 14 L 36 11 L 28 11 L 28 14 L 24 14 Z" />
                <!-- Chip body -->
                <rect x="20" y="16" width="24" height="28" rx="2" />
                <!-- Pins Left -->
                <line x1="14" y1="22" x2="20" y2="22" />
                <line x1="14" y1="28" x2="20" y2="28" />
                <line x1="14" y1="34" x2="20" y2="34" />
                <line x1="14" y1="40" x2="20" y2="40" />
                <!-- Pins Right -->
                <line x1="44" y1="22" x2="50" y2="22" />
                <line x1="44" y1="28" x2="50" y2="28" />
                <line x1="44" y1="34" x2="50" y2="34" />
                <line x1="44" y1="40" x2="50" y2="40" />
            </svg>
            <span>Status Perangkat</span>
        </a>
        @endif

        {{-- Analisis --}}
        @if(auth()->user()->canAccess('analisis'))
        <a href="{{ route('analisis') }}" class="nav-link {{ Route::is('analisis') ? 'active' : '' }}">
            <i class="fa-solid fa-chart-simple"></i>
            <span>Analisis</span>
        </a>
        @endif

        <!-- Data Sensor (Collapsible) -->
        @php
            $isSensorActive = Request::is('sensor/*');
        @endphp
        <div class="nav-dropdown {{ $isSensorActive ? 'active expanded' : '' }}">
            <div class="dropdown-header">
                <i class="fa-solid fa-cubes"></i>
                <span>Data Sensor</span>
                <i class="fa-solid fa-chevron-down arrow" style="{{ $isSensorActive ? 'transform: rotate(180deg);' : '' }}"></i>
            </div>
            <div class="dropdown-content" style="{{ $isSensorActive ? 'max-height: 100px;' : '' }}">
                <a href="{{ route('sensor.ldr') }}" class="sub-link {{ Route::is('sensor.ldr') ? 'active' : '' }}">
                    <i class="fa-solid fa-sun"></i>
                    <span>Sensor 1 — LDR</span>
                </a>
                <a href="{{ route('sensor.dht22') }}" class="sub-link {{ Route::is('sensor.dht22') ? 'active' : '' }}">
                    <i class="fa-solid fa-droplet"></i>
                    <span>Sensor 2 — DHT22</span>
                </a>
            </div>
        </div>

        <!-- Schedules -->
        @if(auth()->user()->canAccess('schedule'))
        <a href="{{ route('schedule') }}" class="nav-link {{ Route::is('schedule') ? 'active' : '' }}">
            <i class="fa-solid fa-clock"></i>
            <span>Schedules</span>
        </a>
        @endif

        <!-- 3D View -->
        @if(auth()->user()->canAccess('view3d'))
        <a href="{{ route('view3d') }}" class="nav-link {{ Route::is('view3d') ? 'active' : '' }}">
            <i class="fa-solid fa-cube"></i>
            <span>3D View</span>
        </a>
        @endif

        <!-- Settings -->
        @if(auth()->user()->canAccess('settings'))
        <a href="{{ route('settings.index') }}" class="nav-link {{ Route::is('settings.*') ? 'active' : '' }}">
            <i class="fa-solid fa-gear"></i>
            <span>Settings</span>
        </a>
        @endif

        <!-- ADMIN Section -->
        @if($isAdmin)
        <div class="section-title">ADMIN</div>
        <a href="{{ route('admin.users') }}" class="nav-link nav-link-admin {{ Route::is('admin.users*') ? 'active' : '' }}">
            <i class="fa-solid fa-users-gear"></i>
            <span>Kelola User</span>
        </a>
        <a href="{{ route('admin.reset-data') }}" class="nav-link nav-link-admin {{ Route::is('admin.reset-data*') ? 'active' : '' }}">
            <i class="fa-solid fa-trash-can"></i>
            <span>Reset Data</span>
        </a>
        @endif
    </nav>
</aside>

<script>
    // Handle Dropdown Toggle
    document.addEventListener('DOMContentLoaded', function() {
        const dropdown = document.querySelector('.nav-dropdown');
        const header = dropdown.querySelector('.dropdown-header');
        const content = dropdown.querySelector('.dropdown-content');
        const arrow = dropdown.querySelector('.arrow');

        // Function to expand
        function expand() {
            content.style.maxHeight = content.scrollHeight + "px";
            arrow.style.transform = "rotate(180deg)";
            dropdown.classList.add('expanded');
        }

        // Function to collapse
        function collapse() {
            content.style.maxHeight = "0";
            arrow.style.transform = "rotate(0deg)";
            dropdown.classList.remove('expanded');
        }

        if (dropdown.classList.contains('active') || {{ Request::is('sensor/*') ? 'true' : 'false' }}) {
            // Disable transition temporarily to prevent initial load animation/flicker
            const prevContentTransition = content.style.transition;
            const prevArrowTransition = arrow.style.transition;
            content.style.transition = 'none';
            arrow.style.transition = 'none';
            
            expand();
            
            // Force layout reflow
            content.offsetHeight;
            arrow.offsetHeight;
            
            // Restore transition
            content.style.transition = prevContentTransition;
            arrow.style.transition = prevArrowTransition;
        }

        header.addEventListener('click', function() {
            if (content.style.maxHeight === "0px" || !content.style.maxHeight) {
                expand();
            } else {
                collapse();
            }
        });
    });
</script>
