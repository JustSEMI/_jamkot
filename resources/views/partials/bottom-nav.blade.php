<!-- BOTTOM NAV FOR MOBILE (M3 Only) -->
<nav class="bottom-nav">
    @if(auth()->user()->canAccess('panel'))
    <a href="{{ route('panel') }}" class="bottom-nav-link {{ Route::is('panel') ? 'active' : '' }}">
        <div class="bottom-nav-icon-wrapper">
            <i class="fa-solid fa-gauge"></i>
        </div>
        <span>Panel</span>
    </a>
    @endif
    
    @if(auth()->user()->canAccess('analisis'))
    <a href="{{ route('analisis') }}" class="bottom-nav-link {{ Route::is('analisis') ? 'active' : '' }}">
        <div class="bottom-nav-icon-wrapper">
            <i class="fa-solid fa-chart-simple"></i>
        </div>
        <span>Analisis</span>
    </a>
    @endif

    <!-- Data Sensor Dropdown -->
    <div class="bottom-nav-link dropdown-container {{ Route::is('sensor.*') ? 'active' : '' }}" id="nav-dropdown-sensor" onclick="this.classList.toggle('open')">
        <div class="bottom-nav-icon-wrapper">
            <i class="fa-solid fa-layer-group"></i>
        </div>
        <span>Data Sensor</span>
        
        <div class="bottom-nav-dropdown-menu">
            <a href="{{ route('sensor.ldr') }}" class="{{ Route::is('sensor.ldr') ? 'active' : '' }}">LDR</a>
            <a href="{{ route('sensor.dht22') }}" class="{{ Route::is('sensor.dht22') ? 'active' : '' }}">DHT22</a>
        </div>
    </div>

    @if(auth()->user()->canAccess('schedule'))
    <a href="{{ route('schedule') }}" class="bottom-nav-link {{ Route::is('schedule') ? 'active' : '' }}">
        <div class="bottom-nav-icon-wrapper">
            <i class="fa-solid fa-clock"></i>
        </div>
        <span>Schedule</span>
    </a>
    @endif
    
    @if(auth()->user()->canAccess('admin'))
    <a href="{{ route('admin.users') }}" class="bottom-nav-link {{ Route::is('admin.*') ? 'active' : '' }}">
        <div class="bottom-nav-icon-wrapper">
            <i class="fa-solid fa-users-gear"></i>
        </div>
        <span>Admin</span>
    </a>
    @else
    <a href="{{ route('settings.index') }}" class="bottom-nav-link {{ Route::is('settings.*') ? 'active' : '' }}">
        <div class="bottom-nav-icon-wrapper">
            <i class="fa-solid fa-gear"></i>
        </div>
        <span>Settings</span>
    </a>
    @endif
</nav>
