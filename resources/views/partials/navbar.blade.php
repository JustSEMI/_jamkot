@php
    $username = auth()->user()->username ?? 'admin';
    $email = auth()->user()->email ?? '';
    $isAdmin = auth()->user()->canAccess('admin');
@endphp

<div class="top-navbar">
    <div class="navbar-right">
        <!-- Theme Toggle -->
        <button class="navbar-btn" id="theme-toggle-btn" onclick="toggleUiVersion()" title="Toggle UI Version">
            <i class="fa-solid fa-moon"></i>
        </button>

        <!-- User Dropdown Wrapper -->
        <div class="navbar-user-wrapper" id="navbar-user-wrapper">
            <!-- User Profile Badge -->
            <button class="navbar-user-badge" id="navbar-user-badge" title="Menu Pengguna">
                <div class="navbar-avatar">
                    <i class="fa-solid fa-user"></i>
                </div>
                <span class="navbar-username">{{ $username }}</span>
                <i class="fa-solid fa-chevron-down dropdown-arrow"></i>
            </button>

            <!-- Dropdown Menu -->
            <div class="navbar-dropdown-menu" id="navbar-dropdown-menu">
                <div class="dropdown-user-info">
                    <div class="dropdown-username">{{ $username }}</div>
                    <div class="dropdown-email">{{ $email }}</div>
                </div>
                <div class="dropdown-divider"></div>
                <a href="{{ route('profile.edit') }}" class="dropdown-item">
                    <i class="fa-solid fa-user-gear"></i>
                    <span>Edit Profil</span>
                </a>
                @if($isAdmin)
                    <a href="{{ route('admin.users') }}" class="dropdown-item">
                        <i class="fa-solid fa-users-gear"></i>
                        <span>Kelola User</span>
                    </a>
                @endif
                <div class="dropdown-divider"></div>
                <form action="{{ route('logout') }}" method="POST" id="navbar-logout-form">
                    @csrf
                    <button type="submit" class="dropdown-item logout-item">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Toggle Dropdown Menu
    document.addEventListener('DOMContentLoaded', () => {
        const wrapper = document.getElementById('navbar-user-wrapper');
        const badge = document.getElementById('navbar-user-badge');
        
        if (badge && wrapper) {
            badge.addEventListener('click', (e) => {
                e.stopPropagation();
                wrapper.classList.toggle('active');
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', (e) => {
                if (!wrapper.contains(e.target)) {
                    wrapper.classList.remove('active');
                }
            });
        }
    });

    function toggleUiVersion() {
        const currentUi = localStorage.getItem('jamkot-ui-version') || 'v2';
        const newUi = currentUi === 'v1' ? 'v2' : 'v1';
        
        const overlay = document.getElementById('page-transition-overlay');
        const panelContent = document.querySelector('.panel-content');
        
        if (panelContent) {
            panelContent.classList.remove('loaded');
        }
        if (overlay) {
            overlay.classList.remove('hidden');
        }
        
        setTimeout(() => {
            localStorage.setItem('jamkot-ui-version', newUi);
            document.documentElement.setAttribute('data-ui-version', newUi);
            
            // Dispatch dynamic change event
            window.dispatchEvent(new CustomEvent('ui-theme-changed', { detail: { version: newUi } }));
            
            // Reload the page to ensure complete synchronization of layouts and charts
            location.reload();
        }, 300);
    }

    // Set toggle button icon based on active theme
    document.addEventListener('DOMContentLoaded', () => {
        const currentUi = localStorage.getItem('jamkot-ui-version') || 'v2';
        const themeBtn = document.getElementById('theme-toggle-btn');
        if (themeBtn) {
            const icon = themeBtn.querySelector('i');
            if (currentUi === 'v1') {
                icon.className = 'fa-solid fa-palette';
                themeBtn.title = 'Switch to Minimalist Dark (UI V2)';
            } else {
                icon.className = 'fa-solid fa-moon';
                themeBtn.title = 'Switch to Material 3 (UI V1)';
            }
        }
    });
</script>
