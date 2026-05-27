@extends('layouts.app')

@section('title', 'Pengaturan')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/settings.css') }}">
@endpush

@section('content')
    <header class="content-header-flex">
        <div>
            <h1>PENGATURAN</h1>
            <p>Manajemen data dan sistem JAMKOT.</p>
        </div>
    </header>

    <div class="settings-container">
        <div class="glow-card settings-card">
            <h2 class="section-title" style="margin: 0 0 0.5rem 0; color: #ededed;">Manajemen Data Sensor</h2>
            <p class="text-muted" style="margin-bottom: 2rem;">Kontrol riwayat pembacaan sensor pada sistem database MariaDB Anda.</p>

            <div class="danger-zone">
                <div class="danger-header">
                    <span class="danger-icon material-symbols-rounded">warning</span>
                    <h3>Zona Berbahaya</h3>
                </div>
                <p>Tindakan ini akan menghapus permanen seluruh riwayat suhu, kelembapan, dan status pompa dari database. Aksi ini tidak dapat dibatalkan.</p>

                <form id="resetForm" action="{{ route('settings.reset') }}" method="POST">
                    @csrf
                    <button type="button" class="btn-danger" onclick="bukaModal()">Reset Semua Data Sensor</button>
                </form>
            </div>
        </div>

        <!-- PILIHAN DESAIN ANTARMUKA -->
        <div class="glow-card settings-card" style="margin-top: 2rem;">
            <h2 class="section-title" style="margin: 0 0 0.5rem 0; color: #ededed;">Desain Antarmuka (UI Version)</h2>
            <p class="text-muted" style="margin-bottom: 2rem;">Pilih gaya visual antarmuka sistem JAMKOT yang paling cocok dengan preferensi Anda.</p>
            
            <div class="ui-version-selector-grid">
                <!-- Card UI V2 (Minimalist Dark) -->
                <div class="ui-version-card" id="ui-card-v2" onclick="setUiVersion('v2')">
                    <div class="ui-preview-icon glow-v2">
                        <i class="fa-solid fa-moon"></i>
                    </div>
                    <div class="ui-version-info">
                        <h3>UI V2: Minimalist Dark</h3>
                        <p>Desain gelap yang elegan, bersih, minimalis, dan berfokus pada kejelasan informasi dengan pendaran cahaya minimal.</p>
                    </div>
                    <div class="ui-select-indicator">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                </div>

                <!-- Card UI V1 (Material 3 Expressive) -->
                <div class="ui-version-card" id="ui-card-v1" onclick="setUiVersion('v1')">
                    <div class="ui-preview-icon m3-v1">
                        <i class="fa-solid fa-palette"></i>
                    </div>
                    <div class="ui-version-info">
                        <h3>UI V1: Material 3 Expressive</h3>
                        <p>Desain premium berbasis Google Material Design 3 dengan lekukan ekspresif, warna tonal pastel, dan tata letak dinamis.</p>
                    </div>
                    <div class="ui-select-indicator">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('modals')
    <!-- MODAL -->
    <div id="modalReset" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-icon-wrapper">
                <i class="fa-solid fa-trash-can"></i>
            </div>
            <h3 class="modal-title">Reset Semua Data Sensor?</h3>
            <p class="modal-subtitle">Tindakan ini akan menghapus permanen seluruh record sensor — suhu, kelembapan, intensitas cahaya, dan status pompa.</p>
            
            <div class="modal-badges">
                <span class="modal-badge"><i class="fa-solid fa-chart-line"></i> Grafik akan kosong</span>
                <span class="modal-badge"><i class="fa-solid fa-table"></i> Tabel akan kosong</span>
                <span class="modal-badge"><i class="fa-solid fa-circle-xmark"></i> Tidak dapat dikembalikan</span>
            </div>

            <div class="modal-input-group">
                <label for="confirm-reset">Ketik <strong>RESET</strong> untuk konfirmasi:</label>
                <input type="text" id="confirm-reset" placeholder="Ketik RESET di sini..." oninput="checkResetInput(this.value)">
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="tutupModal()">Batalkan</button>
                <button type="button" class="btn-danger" id="btn-confirm-reset" onclick="gasReset()" disabled>Hapus Permanen</button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function bukaModal() {
            document.getElementById('modalReset').classList.add('active');
        }
        function tutupModal() {
            document.getElementById('modalReset').classList.remove('active');
            document.getElementById('confirm-reset').value = '';
            document.getElementById('btn-confirm-reset').setAttribute('disabled', 'true');
            document.getElementById('btn-confirm-reset').classList.remove('danger-active');
        }
        function checkResetInput(val) {
            const btn = document.getElementById('btn-confirm-reset');
            if (val === 'RESET') {
                btn.removeAttribute('disabled');
                btn.classList.add('danger-active');
            } else {
                btn.setAttribute('disabled', 'true');
                btn.classList.remove('danger-active');
            }
        }
        function gasReset() {
            document.getElementById('resetForm').submit();
        }

        window.addEventListener('click', function(e) {
            const dropdown = document.getElementById('nav-dropdown-sensor');
            if (dropdown && !dropdown.contains(e.target)) {
                dropdown.classList.remove('open');
            }
        });

        // --- UI VERSION CONTROLLERS ---
        function setUiVersion(version) {
            if (localStorage.getItem('jamkot-ui-version') === version) return;
            
            const overlay = document.getElementById('page-transition-overlay');
            const panelContent = document.querySelector('.panel-content');
            
            if (panelContent) {
                panelContent.classList.remove('loaded');
            }
            if (overlay) {
                overlay.classList.remove('hidden');
            }
            
            setTimeout(() => {
                localStorage.setItem('jamkot-ui-version', version);
                document.documentElement.setAttribute('data-ui-version', version);
                updateUiCards(version);
                
                window.dispatchEvent(new CustomEvent('ui-theme-changed', { detail: { version } }));
                
                setTimeout(() => {
                    if (panelContent) {
                        panelContent.classList.add('loaded');
                    }
                    if (overlay) {
                        overlay.classList.add('hidden');
                    }
    }, 400);
            }, 300);
        }

        function updateUiCards(activeVersion) {
            const cardV1 = document.getElementById('ui-card-v1');
            const cardV2 = document.getElementById('ui-card-v2');
            
            if (cardV1 && cardV2) {
                if (activeVersion === 'v1') {
                    cardV1.classList.add('active');
                    cardV2.classList.remove('active');
                } else {
                    cardV2.classList.add('active');
                    cardV1.classList.remove('active');
                }
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const currentUi = localStorage.getItem('jamkot-ui-version') || 'v1';
            updateUiCards(currentUi);
        });
    </script>
@endpush
