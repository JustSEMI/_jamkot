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
    <title>PREDIKSI | JAMKOT</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/panel.css') }}">
    <link rel="stylesheet" href="{{ asset('css/mobile.css') }}">
    <link rel="stylesheet" href="{{ asset('css/material3.css') }}">
    @vite(['resources/js/app.js'])
    <style>
        .chat-container {
            display: flex;
            flex-direction: column;
            height: 600px;
            background: #1a221f;
            border-radius: 1.5rem;
            overflow: hidden;
            border: 1px solid #2d3532;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
            transition: all 0.3s ease;
            margin-top: 2rem;
        }

        .chat-messages {
            flex: 1;
            padding: 1.5rem;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
            scroll-behavior: smooth;
        }

        .message-wrapper {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            max-width: 75%;
            animation: slideUp 0.3s ease forwards;
        }

        .message-wrapper.user {
            align-self: flex-end;
            flex-direction: row-reverse;
        }

        .message-wrapper.ai {
            align-self: flex-start;
        }

        .avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            flex-shrink: 0;
        }

        .message-wrapper.user .avatar {
            background: #80dec5;
            color: #00382d;
        }

        .message-wrapper.ai .avatar {
            background: #2d3532;
            color: #80dec5;
            border: 1px solid #2d3532;
        }

        .message {
            padding: 1.25rem 1.5rem;
            border-radius: 1.25rem;
            line-height: 1.6;
            font-size: 0.95rem;
            position: relative;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .message-wrapper.user .message {
            background: linear-gradient(135deg, #80dec5 0%, #4fa390 100%);
            color: #00382d;
            border-bottom-right-radius: 0.25rem;
            font-weight: 500;
        }

        .message-wrapper.ai .message {
            background: #242c29;
            color: #e1e3e1;
            border-bottom-left-radius: 0.25rem;
            border: 1px solid #2d3532;
        }

        .chat-templates {
            padding: 1.5rem;
            background: #151a18;
            border-top: 1px solid #2d3532;
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .template-btn {
            background: #242c29;
            color: #80dec5;
            border: 1px solid #2d3532;
            padding: 0.75rem 1.25rem;
            border-radius: 2rem;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        .template-btn:hover:not(:disabled) {
            background: #80dec5;
            color: #00382d;
            border-color: #80dec5;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(128, 222, 197, 0.3);
        }

        .template-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none !important;
        }

        /* Typing Animation */
        .typing-indicator {
            display: flex;
            gap: 0.35rem;
            align-items: center;
            padding: 0.5rem 0.25rem;
        }

        .typing-indicator span {
            width: 8px;
            height: 8px;
            background: #80dec5;
            border-radius: 50%;
            display: inline-block;
            animation: bounce 1.4s infinite ease-in-out both;
        }

        .typing-indicator span:nth-child(1) { animation-delay: -0.32s; }
        .typing-indicator span:nth-child(2) { animation-delay: -0.16s; }

        @keyframes bounce {
            0%, 80%, 100% { transform: scale(0); }
            40% { transform: scale(1.0); }
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Material 3 Style overrides for chat */
        html[data-ui-version="v2"] .chat-container {
            background: #1f2937;
            border-color: #374151;
        }
        html[data-ui-version="v2"] .message-wrapper.ai .message {
            background: #111827;
            border-color: #374151;
        }
        html[data-ui-version="v2"] .message-wrapper.user .message {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
        }
        html[data-ui-version="v2"] .template-btn {
            background: #374151;
            color: #60a5fa;
            border-color: #4b5563;
        }
        html[data-ui-version="v2"] .template-btn:hover:not(:disabled) {
            background: #3b82f6;
            color: white;
            box-shadow: 0 5px 15px rgba(59, 130, 246, 0.3);
        }
        html[data-ui-version="v2"] .typing-indicator span {
            background: #60a5fa;
        }

        /* Responsive Fixes */
        @media (max-width: 768px) {
            .chat-container {
                height: 500px;
            }
            .chat-messages {
                padding: 1rem;
                gap: 1rem;
            }
            .message-wrapper {
                max-width: 85%;
            }
            .message {
                padding: 0.75rem 1rem;
                font-size: 0.9rem;
            }
            .chat-templates {
                padding: 1rem;
                gap: 0.5rem;
            }
            .template-btn {
                padding: 0.6rem 1rem;
                font-size: 0.85rem;
                width: 100%; /* Stack buttons on mobile */
            }
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
        </header>

        <div class="sidebar-overlay" id="sidebar-overlay"></div>

        <!-- SIDEBAR -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h2>JAMKOT</h2>
            </div>

            <nav class="sidebar-nav">
                @if(auth()->user()->canAccess('admin'))
                <a href="{{ route('admin.users') }}" class="nav-link {{ Route::is('admin.*') ? 'active' : '' }}">
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
                <a href="{{ route('flowchart') }}" class="nav-link {{ Route::is('flowchart') ? 'active' : '' }}">
                    <i class="fa-solid fa-project-diagram"></i>
                    <span>Flowchart</span>
                </a>
                <!-- ACTIVE PREDICTION MENU -->
                <a href="{{ route('prediction') }}" class="nav-link active">
                    <i class="fa-solid fa-robot"></i>
                    <span>Prediksi</span>
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
                    <h1>FITUR PREDIKSI AI</h1>
                    <p>Tanyakan prediksi atau analisis data sensor kepada AI.</p>
                </div>

                <!-- JAM -->
                <div class="datetime-widget">
                    <div id="realtime-clock" class="time-display">00:00:00</div>
                    <div id="realtime-date" class="date-display">Memuat...</div>
                </div>
            </header>

            <div class="chat-container">
                <div class="chat-messages" id="chat-messages">
                    <div class="chat-spacer" style="flex: 1;"></div>
                    <div class="message-wrapper ai">
                        <div class="avatar"><i class="fa-solid fa-robot"></i></div>
                        <div class="message">
                            Halo! Saya adalah asisten AI Jamkot. Silakan pilih salah satu pertanyaan di bawah untuk memulai.
                        </div>
                    </div>
                </div>

                <div class="chat-templates">
                    @foreach($templates as $key => $prompt)
                        <button class="template-btn" onclick="askGemini('{{ $key }}', '{{ $prompt }}')">
                            <i class="fa-solid fa-circle-question"></i>
                            {{ $prompt }}
                        </button>
                    @endforeach
                </div>
            </div>

        </main>

    </div>

    <script>
        function askGemini(key, promptText) {
            const chatMessages = document.getElementById('chat-messages');
            
            // Append user message
            const userWrapper = document.createElement('div');
            userWrapper.className = 'message-wrapper user';
            userWrapper.innerHTML = `
                <div class="avatar"><i class="fa-solid fa-user"></i></div>
                <div class="message">${promptText}</div>
            `;
            chatMessages.appendChild(userWrapper);
            
            // Create typing indicator
            const typingWrapper = document.createElement('div');
            typingWrapper.className = 'message-wrapper ai';
            typingWrapper.innerHTML = `
                <div class="avatar"><i class="fa-solid fa-robot"></i></div>
                <div class="message">
                    <div class="typing-indicator">
                        <span></span><span></span><span></span>
                    </div>
                </div>
            `;
            chatMessages.appendChild(typingWrapper);
            chatMessages.scrollTop = chatMessages.scrollHeight;

            // Disable buttons
            const buttons = document.querySelectorAll('.template-btn');
            buttons.forEach(btn => btn.disabled = true);

            fetch('{{ route("prediction.ask") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ template_key: key })
            })
            .then(response => {
                if (response.status === 429) {
                    return { error: 'Terlalu banyak permintaan. Silakan coba lagi dalam 1 menit.' };
                }
                return response.json();
            })
            .then(data => {
                // Remove typing indicator
                chatMessages.removeChild(typingWrapper);
                buttons.forEach(btn => btn.disabled = false);
                
                const aiWrapper = document.createElement('div');
                aiWrapper.className = 'message-wrapper ai';
                
                let messageContent = data.response;
                let isError = false;
                
                if (data.error) {
                    messageContent = 'Error: ' + data.error;
                    isError = true;
                } else if (messageContent) {
                    // Escape HTML to prevent XSS (Pentest prevention)
                    const tempDiv = document.createElement('div');
                    tempDiv.innerText = messageContent;
                    messageContent = tempDiv.innerHTML;

                    // Simple markdown parser for bold and italic
                    messageContent = messageContent.replace(/\*\*(.*?)\*\*/g, '<b>$1</b>');
                    messageContent = messageContent.replace(/\*(.*?)\*/g, '<i>$1</i>');
                    // Replace newlines with <br>
                    messageContent = messageContent.replace(/\n/g, '<br>');
                }
                
                aiWrapper.innerHTML = `
                    <div class="avatar"><i class="fa-solid fa-robot"></i></div>
                    <div class="message" style="${isError ? 'border-color: #ffb4ab;' : ''}">${messageContent}</div>
                `;
                
                chatMessages.appendChild(aiWrapper);
                chatMessages.scrollTop = chatMessages.scrollHeight;
            })
            .catch(error => {
                chatMessages.removeChild(typingWrapper);
                buttons.forEach(btn => btn.disabled = false);
                console.error('Error:', error);
                
                const aiWrapper = document.createElement('div');
                aiWrapper.className = 'message-wrapper ai';
                aiWrapper.innerHTML = `
                    <div class="avatar"><i class="fa-solid fa-robot"></i></div>
                    <div class="message" style="border-color: #ffb4ab;">Gagal menghubungi server.</div>
                `;
                chatMessages.appendChild(aiWrapper);
                chatMessages.scrollTop = chatMessages.scrollHeight;
            });
        }

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
        updateClock();
    </script>
    <script src="{{ asset('js/sidebar.js') }}"></script>
</body>

</html>
