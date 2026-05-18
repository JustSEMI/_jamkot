<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- UI Theme Setup -->
    <script>
        (function() {
            const uiVersion = localStorage.getItem('jamkot-ui-version') || 'v1';
            document.documentElement.setAttribute('data-ui-version', uiVersion);
        })();
    </script>
    <title>FLOWCHART | JAMKOT</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/panel.css') }}">
    <link rel="stylesheet" href="{{ asset('css/mobile.css') }}">
    <link rel="stylesheet" href="{{ asset('css/material3.css') }}">
    @vite(['resources/js/app.js', 'resources/js/flowchart.js'])
    <style>
        .flowchart-wrapper {
            margin-top: 1rem;
            background: radial-gradient(circle at center, #1a1b24 0%, #06070a 100%);
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
            border: 1px solid #262626;
            position: relative;
        }
        
        .flowchart-container {
            width: 100%;
            height: 600px;
        }

        .flowchart-container .flow-container {
            background-color: transparent !important;
            --flow-bg-color: transparent !important;
            --flow-bg-pattern-color: rgba(255, 255, 255, 0.08) !important;
            --flow-node-bg: transparent !important;
            --flow-node-border: none !important;
            --flow-node-border-top: none !important;
            --flow-node-shadow: none !important;
            --flow-node-padding: 0 !important;
        }

        .flowchart-container .flow-container .flow-viewport {
            opacity: 1 !important;
        }
        
        /* Tema Gelap untuk ArtisanFlow */
        html[data-ui-version="v2"] .flowchart-container {
            --af-bg: #111827;
            --af-node-bg: #1f2937;
            --af-node-color: #f3f4f6;
            --af-node-border: #374151;
            --af-edge-stroke: #9ca3af;
            --af-node-primary: #38bdf8;
            --af-node-desc: #94a3b8;
        }

        /* Tema Material 3 (UI V1) */
        html[data-ui-version="v1"] .flowchart-container {
            --af-bg: #111413;
            --af-node-bg: #1b221f;
            --af-node-color: #e1e3e1;
            --af-node-border: #2d3532;
            --af-edge-stroke: #80dec5;
            --af-node-primary: #80dec5;
            --af-node-desc: #a2aba7;
        }
        


        html[data-ui-version="v1"] .flowchart-wrapper {
            background: radial-gradient(circle at center, #1a221f 0%, #111413 100%) !important;
            border-color: #2d3532 !important;
        }

        html[data-ui-version="v1"] .flowchart-description {
            background: #1a221f !important;
            border-color: #2d3532 !important;
            color: #e1e3e1 !important;
        }
        
        html[data-ui-version="v1"] .flowchart-description h3 {
            color: #80dec5 !important;
        }

        .flowchart-description {
            padding: 1.5rem;
            background: var(--card-bg);
            border-radius: 12px;
            margin-top: 1.5rem;
            border: 1px solid var(--border-color);
            color: var(--text-color);
            line-height: 1.6;
        }

        .flowchart-description h3 {
            margin-top: 0;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }
        
        .flowchart-description ul {
            padding-left: 1.5rem;
            margin-bottom: 1rem;
        }
        
        .flowchart-description li {
            margin-bottom: 0.5rem;
        }
    </style>
</head>

<body class="{{ auth()->user()->isAdmin() ? 'admin-mode' : '' }}">

    <div class="panel-layout">

        <!-- NAVBAR MOBILE -->
        <header class="mobile-top-nav">
            <div class="mobile-logo">JAMKOT</div>
            <button class="btn-toggle-sidebar" id="sidebar-toggle">
                <i class="fa-solid fa-bars"></i>
            </button>
            <div class="mobile-top-actions">
                @if(auth()->user()->canAccess('admin'))
                    <a href="{{ route('settings.index') }}" class="btn-mobile-settings" title="Settings">
                        <i class="fa-solid fa-gear"></i>
                    </a>
                @endif
                <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn-mobile-logout" title="Logout">
                        <i class="fa-solid fa-right-from-bracket"></i>
                    </button>
                </form>
            </div>
        </header>

        <div class="sidebar-overlay" id="sidebar-overlay"></div>

        <!-- SIDEBAR -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h2>JAMKOT</h2>
            </div>

            <nav class="sidebar-nav">
                @if(auth()->user()->canAccess('admin'))
                <a href="{{ route('admin.users') }}" class="nav-link nav-link-admin {{ Route::is('admin.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-users-gear"></i>
                    <span>Admin</span>
                </a>
                @endif
                @if(auth()->user()->canAccess('panel'))
                <a href="{{ route('panel') }}" class="nav-link {{ Route::is('panel') ? 'active' : '' }}">
                    <i class="fa-solid fa-gauge"></i>
                    <span>Panel Utama</span>
                </a>
                @endif
                @if(auth()->user()->canAccess('analisis'))
                <a href="{{ route('analisis') }}" class="nav-link {{ Route::is('analisis') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-simple"></i>
                    <span>Analisis</span>
                </a>
                @endif
                @if(auth()->user()->canAccess('schedule'))
                <a href="{{ route('schedule') }}" class="nav-link {{ Route::is('schedule') ? 'active' : '' }}">
                    <i class="fa-solid fa-clock"></i>
                    <span>Schedules</span>
                </a>
                @endif
                @if(auth()->user()->canAccess('settings'))
                <a href="{{ route('settings.index') }}" class="nav-link {{ Route::is('settings.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-gear"></i>
                    <span>Settings</span>
                </a>
                @endif
                @if(auth()->user()->canAccess('view3d'))
                <a href="{{ route('view3d') }}" class="nav-link {{ Route::is('view3d') ? 'active' : '' }}">
                    <i class="fa-solid fa-cube"></i>
                    <span>3D View</span>
                </a>
                @endif
                <!-- NEW FLOWCHART MENU -->
                <a href="{{ route('flowchart') }}" class="nav-link {{ Route::is('flowchart') ? 'active' : '' }}">
                    <i class="fa-solid fa-project-diagram"></i>
                    <span>Flowchart</span>
                </a>

            </nav>

            <div class="sidebar-footer">
                <span class="user-greeting">Halo, {{ auth()->user()->username ?? 'admin' }}</span>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-logout-sidebar" title="Logout">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- MAIN-CONTENT -->
        <main class="panel-content">

            <header class="content-header-flex">
                <div>
                    <h1>FLOWCHART SISTEM</h1>
                    <p>Alur arsitektur IoT Jamkot dari Hardware ESP32 hingga antarmuka Web Dashboard.</p>
                </div>

                <!-- JAM -->
                <div class="datetime-widget">
                    <div id="realtime-clock" class="time-display">00:00:00</div>
                    <div id="realtime-date" class="date-display">Memuat...</div>
                </div>
            </header>

            @php
                // Node Configuration for ArtisanFlow
                $nodes = [
                    ['id' => 'esp', 'type' => 'default', 'position' => ['x' => 50, 'y' => 200], 'data' => ['label' => 'Hardware (ESP32)', 'desc' => 'Mikrokontroler & Relay', 'icon' => 'fa-microchip', 'status' => 'online']],
                    ['id' => 'sensor', 'type' => 'default', 'position' => ['x' => 50, 'y' => 50], 'data' => ['label' => 'Sensor DHT22 & LDR', 'desc' => 'Suhu, Kelembapan, Cahaya', 'icon' => 'fa-temperature-half', 'status' => 'online']],
                    ['id' => 'pump', 'type' => 'default', 'position' => ['x' => 50, 'y' => 350], 'data' => ['label' => 'Aktuator Pompa', 'desc' => 'Kipas & Penyiram Air', 'icon' => 'fa-fan', 'status' => 'online']],
                    
                    ['id' => 'api_post', 'type' => 'default', 'position' => ['x' => 320, 'y' => 120], 'data' => ['label' => 'POST /api/sensor/data', 'desc' => 'Endpoint API Penerima Data', 'icon' => 'fa-cloud-arrow-up', 'status' => 'online']],
                    ['id' => 'api_get', 'type' => 'default', 'position' => ['x' => 320, 'y' => 280], 'data' => ['label' => 'GET /api/pump/status', 'desc' => 'Endpoint Polling Perintah', 'icon' => 'fa-cloud-arrow-down', 'status' => 'online']],
                    
                    ['id' => 'laravel', 'type' => 'default', 'position' => ['x' => 600, 'y' => 200], 'data' => ['label' => 'Laravel Backend', 'desc' => 'Logika Bisnis & Controller', 'icon' => 'fa-server', 'status' => 'online']],
                    ['id' => 'db', 'type' => 'default', 'position' => ['x' => 600, 'y' => 50], 'data' => ['label' => 'MySQL Database', 'desc' => 'Penyimpanan Data Sensor', 'icon' => 'fa-database', 'status' => 'online']],
                    
                    ['id' => 'web_view', 'type' => 'default', 'position' => ['x' => 880, 'y' => 120], 'data' => ['label' => 'Web UI Dashboard', 'desc' => 'Tampilan Grafik Realtime', 'icon' => 'fa-desktop', 'status' => 'online']],
                    ['id' => 'web_action', 'type' => 'default', 'position' => ['x' => 880, 'y' => 280], 'data' => ['label' => 'Aksi Tombol Panel', 'desc' => 'Pengguna Menekan ON/OFF', 'icon' => 'fa-hand-pointer', 'status' => 'online'] ],
                ];

                $edges = [
                    // Hardware layer
                    ['id' => 'e1', 'source' => 'sensor', 'target' => 'esp', 'label' => 'Baca Sensor', 'animated' => false],
                    ['id' => 'e2', 'source' => 'esp', 'target' => 'pump', 'label' => 'Relay Control', 'animated' => false],
                    
                    // ESP to API
                    ['id' => 'e3', 'source' => 'esp', 'target' => 'api_post', 'label' => 'Kirim 60s', 'animated' => true],
                    ['id' => 'e4', 'source' => 'api_post', 'target' => 'laravel', 'animated' => true],
                    
                    // API to ESP (Polling Status Pompa)
                    ['id' => 'e5', 'source' => 'esp', 'target' => 'api_get', 'label' => 'Polling 5s', 'animated' => true],
                    ['id' => 'e6', 'source' => 'api_get', 'target' => 'laravel', 'animated' => true],
                    
                    // Laravel to DB
                    ['id' => 'e7', 'source' => 'laravel', 'target' => 'db', 'label' => 'Simpan Data', 'animated' => false],
                    
                    // Web to Laravel
                    ['id' => 'e8', 'source' => 'laravel', 'target' => 'web_view', 'label' => 'AJAX Realtime', 'animated' => true],
                    ['id' => 'e9', 'source' => 'web_action', 'target' => 'laravel', 'label' => 'Toggle Action', 'animated' => true],
                ];
            @endphp

            <div class="flowchart-wrapper">
                <div class="flowchart-container">
                    <x-flow 
                        :nodes="$nodes" 
                        :edges="$edges" 
                        :fitView="false"
                        :controls="true"
                        :minimap="false"
                        :interactive="true"
                        background="dots"
                        style="height: 100%; width: 100%;"
                    >
                        <x-slot:node>
                            <div class="wireflow-node-card" style="padding: 16px; width: 220px; background: var(--af-node-bg, #1e293b); border: 1px solid var(--af-node-border, #334155); border-radius: 12px; color: var(--af-node-color, #f8fafc); text-align: left; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.3); position: relative;">
                                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                                    <div style="display: flex; align-items: center; gap: 8px; font-weight: 600; font-size: 0.9rem; color: var(--af-node-primary, #38bdf8);">
                                        <i :class="'fa-solid ' + (node.data.icon || 'fa-cube')"></i>
                                        <span x-text="node.data.label"></span>
                                    </div>
                                    <div style="width: 8px; height: 8px; border-radius: 50%;" :style="node.data.status === 'online' ? 'background: #10b981;' : 'background: #64748b;'"></div>
                                </div>
                                <div style="font-size: 0.75rem; color: var(--af-node-desc, #94a3b8); line-height: 1.4;" x-text="node.data.desc"></div>
                                
                                <x-flow-handle type="target" position="top" style="background: var(--af-node-primary, #38bdf8); border: 2px solid var(--af-node-bg, #0f172a); width: 12px; height: 12px;" />
                                <x-flow-handle type="source" position="bottom" style="background: var(--af-node-primary, #38bdf8); border: 2px solid var(--af-node-bg, #0f172a); width: 12px; height: 12px;" />
                            </div>
                        </x-slot:node>
                    </x-flow>
                </div>
            </div>

            <!-- DESKRIPSI ALUR -->
            <div class="flowchart-description">
                <h3>📖 Penjelasan Alur Sistem End-to-End</h3>
                <p>Flowchart di atas menggambarkan bagaimana perangkat keras (ESP32) berkomunikasi dengan aplikasi web Laravel secara real-time:</p>
                <ul>
                    <li><strong>Pengumpulan Data:</strong> Mikrokontroler ESP32 membaca data suhu dan kelembapan dari sensor DHT22 serta intensitas cahaya dari sensor LDR.</li>
                    <li><strong>Pengiriman ke Server:</strong> Setiap 60 detik, ESP32 mengirimkan data sensor tersebut menggunakan metode <code>HTTP POST</code> ke endpoint <code>/api/sensor/data</code>. Laravel kemudian memproses dan menyimpannya ke dalam MySQL Database.</li>
                    <li><strong>Pemantauan Real-time:</strong> Antarmuka web (Panel UI) melakukan *polling* menggunakan AJAX setiap 5 detik ke server Laravel untuk mengambil data terbaru dan memperbarui grafik serta angka di layar tanpa perlu *refresh* halaman.</li>
                    <li><strong>Kontrol Aktuator (Pompa):</strong> Saat pengguna menekan tombol ON/OFF pompa di web, web mengirimkan perintah via <code>POST /panel/pump/toggle</code> ke Laravel untuk mengubah status manual pompa di database.</li>
                    <li><strong>Eksekusi Perintah:</strong> ESP32 selalu memonitor perintah dengan melakukan *polling* setiap 5 detik ke endpoint <code>GET /api/pump/status</code>. Jika mendeteksi perubahan status menjadi "ON" atau "OFF", ESP32 akan langsung mengubah kondisi kelistrikan Relay yang terhubung ke pompa air/kipas.</li>
                </ul>
            </div>

        </main>

    </div>

    <!-- BOTTOM NAV FOR MOBILE (M3 Only) -->
    <nav class="bottom-nav">
        @if(auth()->user()->canAccess('panel'))
        <a href="{{ route('panel') }}" class="bottom-nav-link {{ Route::is('panel') ? 'active' : '' }}">
            <div class="bottom-nav-icon-wrapper"><i class="fa-solid fa-gauge"></i></div>
            <span>Panel</span>
        </a>
        @endif
        @if(auth()->user()->canAccess('analisis'))
        <a href="{{ route('analisis') }}" class="bottom-nav-link {{ Route::is('analisis') ? 'active' : '' }}">
            <div class="bottom-nav-icon-wrapper"><i class="fa-solid fa-chart-simple"></i></div>
            <span>Analisis</span>
        </a>
        @endif
        @if(auth()->user()->canAccess('schedule'))
        <a href="{{ route('schedule') }}" class="bottom-nav-link {{ Route::is('schedule') ? 'active' : '' }}">
            <div class="bottom-nav-icon-wrapper"><i class="fa-solid fa-clock"></i></div>
            <span>Schedule</span>
        </a>
        @endif
        @if(auth()->user()->canAccess('admin'))
        <a href="{{ route('admin.users') }}" class="bottom-nav-link {{ Route::is('admin.*') ? 'active' : '' }}">
            <div class="bottom-nav-icon-wrapper"><i class="fa-solid fa-users-gear"></i></div>
            <span>Admin</span>
        </a>
        @else
        <a href="{{ route('settings.index') }}" class="bottom-nav-link {{ Route::is('settings.*') ? 'active' : '' }}">
            <div class="bottom-nav-icon-wrapper"><i class="fa-solid fa-gear"></i></div>
            <span>Settings</span>
        </a>
        @endif
        @if(auth()->user()->canAccess('view3d'))
        <a href="{{ route('view3d') }}" class="bottom-nav-link {{ Route::is('view3d') ? 'active' : '' }}">
            <div class="bottom-nav-icon-wrapper"><i class="fa-solid fa-cube"></i></div>
            <span>3D View</span>
        </a>
        @endif
        <!-- NEW FLOWCHART MENU -->
        <a href="{{ route('flowchart') }}" class="bottom-nav-link {{ Route::is('flowchart') ? 'active' : '' }}">
            <div class="bottom-nav-icon-wrapper"><i class="fa-solid fa-project-diagram"></i></div>
            <span>Flowchart</span>
        </a>
    </nav>

    <script>
        // Realtime Clock Logic
        function updateClock() {
            const now = new Date();
            const timeStr = String(now.getHours()).padStart(2, '0') + ':' + 
                            String(now.getMinutes()).padStart(2, '0') + ':' + 
                            String(now.getSeconds()).padStart(2, '0');
            document.getElementById('realtime-clock').innerText = timeStr;
            
            const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
            const dateStr = days[now.getDay()] + ', ' + now.getDate() + ' ' + months[now.getMonth()] + ' ' + now.getFullYear();
            document.getElementById('realtime-date').innerText = dateStr;
        }
        setInterval(updateClock, 1000);
    </script>
    <script src="{{ asset('js/sidebar.js') }}"></script>
</body>

</html>
