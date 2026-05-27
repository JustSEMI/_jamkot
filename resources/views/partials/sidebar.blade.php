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
        <!-- Panel -->
        @if(auth()->user()->canAccess('panel'))
        <a href="{{ route('panel') }}" class="nav-link {{ Route::is('panel') ? 'active' : '' }}">
            <i class="fa-solid fa-gauge"></i>
            <span>Panel</span>
        </a>
        @endif
        
        <!-- Analisis -->
        @if(auth()->user()->canAccess('analisis'))
        <a href="{{ route('analisis') }}" class="nav-link {{ Route::is('analisis') ? 'active' : '' }}">
            <i class="fa-solid fa-chart-simple"></i>
            <span>Analisis</span>
        </a>
        @endif

        <!-- Data Sensor (Collapsible) -->
        <div class="nav-dropdown {{ Request::is('sensor/*') ? 'active' : '' }}">
            <div class="dropdown-header">
                <i class="fa-solid fa-cubes"></i>
                <span>Data Sensor</span>
                <i class="fa-solid fa-chevron-down arrow"></i>
            </div>
            <div class="dropdown-content">
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
        <a href="{{ route('admin.users') }}" class="nav-link nav-link-admin {{ Route::is('admin.*') ? 'active' : '' }}">
            <i class="fa-solid fa-users-gear"></i>
            <span>Kelola User</span>
        </a>
        @endif
    </nav>

    <!-- Footer (Logout) -->
    <div class="sidebar-footer">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn-logout-sidebar" title="Logout">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>Logout</span>
            </button>
        </form>
    </div>
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
            expand();
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
