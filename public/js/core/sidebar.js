
document.addEventListener('DOMContentLoaded', () => {
    // --- SIDEBAR TOGGLE LOGIC ---
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const sidebarOverlay = document.getElementById('sidebar-overlay');

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('show');
        });
    }

    if (sidebarOverlay && sidebar) {
        sidebarOverlay.addEventListener('click', () => {
            sidebar.classList.remove('show');
        });
    }

    // --- SEAMLESS SKELETON TRANSITION LOGIC ---
    const panelContent = document.querySelector('.panel-content');
    
    // Resolve custom skeleton template based on path
    const getSkeletonTemplateForPath = (url) => {
        let path = '';
        try {
            const parsedUrl = new URL(url, window.location.origin);
            path = parsedUrl.pathname;
        } catch (e) {
            path = url;
        }

        if (path.endsWith('/') && path.length > 1) {
            path = path.slice(0, -1);
        }

        if (path === '/panel' || path === '/dashboard' || path === '/home') {
            return `
                <div class="skeleton-header">
                    <div class="skeleton-bone skeleton-title"></div>
                    <div class="skeleton-bone skeleton-subtitle"></div>
                </div>
                <h3 class="section-title skeleton-bone" style="width: 160px; margin-top: 1.5rem; margin-bottom: 1rem; height: 18px;"></h3>
                <div class="summary-grid" style="margin-bottom: 2rem;">
                    <div class="glow-card skeleton-card">
                        <div class="skeleton-bone skeleton-card-title"></div>
                        <div class="skeleton-bone skeleton-card-value"></div>
                        <div class="skeleton-bone skeleton-card-desc"></div>
                    </div>
                    <div class="glow-card sensor-meter-card sensor-meter-temperature skeleton-card">
                        <div class="skeleton-bone skeleton-card-title"></div>
                        <div class="skeleton-gauge-container">
                            <div class="skeleton-gauge-arc"></div>
                            <div class="skeleton-bone skeleton-gauge-value"></div>
                        </div>
                        <div class="skeleton-bone skeleton-card-desc"></div>
                    </div>
                    <div class="glow-card sensor-meter-card sensor-meter-humidity skeleton-card">
                        <div class="skeleton-bone skeleton-card-title"></div>
                        <div class="skeleton-gauge-container">
                            <div class="skeleton-gauge-arc"></div>
                            <div class="skeleton-bone skeleton-gauge-value"></div>
                        </div>
                        <div class="skeleton-bone skeleton-card-desc"></div>
                    </div>
                </div>
                <h3 class="section-title skeleton-bone" style="width: 130px; margin-bottom: 1rem; height: 18px;"></h3>
                <div class="summary-grid" style="margin-bottom: 2.5rem; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));">
                    <div class="glow-card actuator-card skeleton-card" style="display: flex; justify-content: space-between; align-items: center; padding: 1.5rem;">
                        <div class="skeleton-actuator-info">
                            <div class="skeleton-bone skeleton-card-title" style="width: 150px;"></div>
                            <div class="skeleton-bone skeleton-card-desc" style="width: 220px;"></div>
                            <div class="skeleton-bone skeleton-card-status" style="width: 80px; height: 14px;"></div>
                        </div>
                        <div class="skeleton-bone skeleton-btn-pill"></div>
                    </div>
                </div>
                <div class="glow-card chart-wrapper skeleton-card" style="position: relative; min-height: 350px;">
                    <div class="skeleton-bone skeleton-large-title"></div>
                    <div class="skeleton-chart-placeholder">
                        <div class="skeleton-chart-line"></div>
                        <div class="skeleton-chart-line delay-1"></div>
                        <div class="skeleton-chart-line delay-2"></div>
                    </div>
                </div>
                <div class="glow-card table-wrapper skeleton-card" style="margin-top: 1.5rem;">
                    <div class="skeleton-bone skeleton-large-title" style="width: 160px; margin-bottom: 1.5rem;"></div>
                    <div class="skeleton-table">
                        <div class="skeleton-table-row skeleton-table-head cols-6">
                            <div class="skeleton-bone cell"></div>
                            <div class="skeleton-bone cell"></div>
                            <div class="skeleton-bone cell"></div>
                            <div class="skeleton-bone cell"></div>
                            <div class="skeleton-bone cell"></div>
                            <div class="skeleton-bone cell"></div>
                        </div>
                        <div class="skeleton-table-row cols-6">
                            <div class="skeleton-bone cell"></div>
                            <div class="skeleton-bone cell"></div>
                            <div class="skeleton-bone cell"></div>
                            <div class="skeleton-bone cell"></div>
                            <div class="skeleton-bone cell"></div>
                            <div class="skeleton-bone cell"></div>
                        </div>
                        <div class="skeleton-table-row cols-6">
                            <div class="skeleton-bone cell"></div>
                            <div class="skeleton-bone cell"></div>
                            <div class="skeleton-bone cell"></div>
                            <div class="skeleton-bone cell"></div>
                            <div class="skeleton-bone cell"></div>
                            <div class="skeleton-bone cell"></div>
                        </div>
                    </div>
                </div>
            `;
        }

        if (path === '/analisis') {
            return `
                <div class="skeleton-header">
                    <div class="skeleton-bone skeleton-title"></div>
                    <div class="skeleton-bone skeleton-subtitle"></div>
                </div>
                <div class="summary-grid" style="margin-top: 1rem;">
                    <div class="glow-card stat-card meter-card meter-card-temperature skeleton-card">
                        <div class="skeleton-bone skeleton-card-title"></div>
                        <div class="skeleton-bone skeleton-card-value" style="width: 80px; height: 36px;"></div>
                        <div class="skeleton-bone skeleton-card-desc"></div>
                    </div>
                    <div class="glow-card stat-card meter-card meter-card-humidity skeleton-card">
                        <div class="skeleton-bone skeleton-card-title"></div>
                        <div class="skeleton-bone skeleton-card-value" style="width: 80px; height: 36px;"></div>
                        <div class="skeleton-bone skeleton-card-desc"></div>
                    </div>
                    <div class="glow-card stat-card total-log-card skeleton-card">
                        <div class="skeleton-bone skeleton-card-title"></div>
                        <div class="skeleton-total-log-layout" style="display: flex; align-items: center; gap: 1rem; margin: 0.5rem 0;">
                            <div class="skeleton-bone skeleton-log-icon" style="width: 40px; height: 40px; border-radius: 8px;"></div>
                            <div class="skeleton-bone skeleton-card-value" style="width: 100px; margin: 0;"></div>
                        </div>
                        <div class="skeleton-bone skeleton-card-desc"></div>
                    </div>
                </div>
                <div class="analysis-row" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; margin-top: 1.5rem;">
                    <div class="glow-card record-card high skeleton-card" style="min-height: 140px;">
                        <div class="skeleton-record-header" style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem;">
                            <div class="skeleton-bone" style="width: 24px; height: 24px; border-radius: 50%;"></div>
                            <div class="skeleton-bone" style="width: 120px; height: 16px;"></div>
                        </div>
                        <div class="skeleton-record-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div class="skeleton-record-item">
                                <div class="skeleton-bone" style="width: 40px; height: 10px; margin-bottom: 0.5rem;"></div>
                                <div class="skeleton-bone" style="width: 60px; height: 20px;"></div>
                            </div>
                            <div class="skeleton-record-item">
                                <div class="skeleton-bone" style="width: 60px; height: 10px; margin-bottom: 0.5rem;"></div>
                                <div class="skeleton-bone" style="width: 50px; height: 20px;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="glow-card record-card low skeleton-card" style="min-height: 140px;">
                        <div class="skeleton-record-header" style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem;">
                            <div class="skeleton-bone" style="width: 24px; height: 24px; border-radius: 50%;"></div>
                            <div class="skeleton-bone" style="width: 120px; height: 16px;"></div>
                        </div>
                        <div class="skeleton-record-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div class="skeleton-record-item">
                                <div class="skeleton-bone" style="width: 40px; height: 10px; margin-bottom: 0.5rem;"></div>
                                <div class="skeleton-bone" style="width: 60px; height: 20px;"></div>
                            </div>
                            <div class="skeleton-record-item">
                                <div class="skeleton-bone" style="width: 60px; height: 10px; margin-bottom: 0.5rem;"></div>
                                <div class="skeleton-bone" style="width: 50px; height: 20px;"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="glow-card filter-card skeleton-card" style="min-height: 80px; margin-top: 1.5rem; display: flex; flex-direction: row; flex-wrap: wrap; gap: 1.5rem; align-items: center; justify-content: space-between;">
                    <div class="skeleton-filter-groups" style="display: flex; gap: 1.5rem; flex: 1;">
                        <div class="skeleton-filter-group" style="display: flex; flex-direction: column; gap: 0.4rem; flex: 1; min-width: 120px;">
                            <div class="skeleton-bone" style="width: 80px; height: 10px;"></div>
                            <div class="skeleton-bone" style="width: 100%; height: 38px; border-radius: 8px;"></div>
                        </div>
                        <div class="skeleton-filter-group" style="display: flex; flex-direction: column; gap: 0.4rem; flex: 1; min-width: 120px;">
                            <div class="skeleton-bone" style="width: 70px; height: 10px;"></div>
                            <div class="skeleton-bone" style="width: 100%; height: 38px; border-radius: 8px;"></div>
                        </div>
                    </div>
                    <div class="skeleton-bone" style="width: 120px; height: 38px; border-radius: 100px; margin-top: 14px;"></div>
                </div>
                <div class="glow-card table-wrapper skeleton-card" style="margin-top: 1.5rem; margin-bottom: 3rem;">
                    <div class="skeleton-bone skeleton-large-title" style="width: 160px; margin-bottom: 1.5rem;"></div>
                    <div class="skeleton-table">
                        <div class="skeleton-table-row skeleton-table-head cols-6">
                            <div class="skeleton-bone cell"></div>
                            <div class="skeleton-bone cell"></div>
                            <div class="skeleton-bone cell"></div>
                            <div class="skeleton-bone cell"></div>
                            <div class="skeleton-bone cell"></div>
                            <div class="skeleton-bone cell"></div>
                        </div>
                        <div class="skeleton-table-row cols-6">
                            <div class="skeleton-bone cell"></div>
                            <div class="skeleton-bone cell"></div>
                            <div class="skeleton-bone cell"></div>
                            <div class="skeleton-bone cell"></div>
                            <div class="skeleton-bone cell"></div>
                            <div class="skeleton-bone cell"></div>
                        </div>
                        <div class="skeleton-table-row cols-6">
                            <div class="skeleton-bone cell"></div>
                            <div class="skeleton-bone cell"></div>
                            <div class="skeleton-bone cell"></div>
                            <div class="skeleton-bone cell"></div>
                            <div class="skeleton-bone cell"></div>
                            <div class="skeleton-bone cell"></div>
                        </div>
                    </div>
                </div>
            `;
        }

        if (path === '/sensor/ldr') {
            return `
                <div class="skeleton-header">
                    <div class="skeleton-bone skeleton-title"></div>
                    <div class="skeleton-bone skeleton-subtitle"></div>
                </div>
                <h3 class="section-title skeleton-bone" style="width: 130px; margin-top: 1.5rem; margin-bottom: 1rem; height: 18px;"></h3>
                <div class="summary-grid" style="margin-bottom: 2rem;">
                    <div class="glow-card skeleton-card" style="grid-column: span 3; min-height: 160px;">
                        <div class="skeleton-bone skeleton-card-title" style="width: 200px;"></div>
                        <div class="skeleton-bone skeleton-card-value" style="width: 250px; height: 50px; margin: 1rem 0;"></div>
                        <div class="skeleton-bone skeleton-card-desc" style="width: 120px;"></div>
                    </div>
                </div>
                <div class="glow-card chart-wrapper skeleton-card" style="position: relative; min-height: 350px;">
                    <div class="skeleton-bone skeleton-large-title"></div>
                    <div class="skeleton-chart-placeholder">
                        <div class="skeleton-chart-line"></div>
                        <div class="skeleton-chart-line delay-1"></div>
                        <div class="skeleton-chart-line delay-2"></div>
                    </div>
                </div>
                <div class="glow-card table-wrapper skeleton-card" style="margin-top: 2rem;">
                    <div class="skeleton-bone skeleton-large-title" style="width: 160px; margin-bottom: 1.5rem;"></div>
                    <div class="skeleton-table">
                        <div class="skeleton-table-row skeleton-table-head cols-4">
                            <div class="skeleton-bone cell"></div>
                            <div class="skeleton-bone cell"></div>
                            <div class="skeleton-bone cell"></div>
                            <div class="skeleton-bone cell"></div>
                        </div>
                        <div class="skeleton-table-row cols-4">
                            <div class="skeleton-bone cell"></div>
                            <div class="skeleton-bone cell"></div>
                            <div class="skeleton-bone cell"></div>
                            <div class="skeleton-bone cell"></div>
                        </div>
                        <div class="skeleton-table-row cols-4">
                            <div class="skeleton-bone cell"></div>
                            <div class="skeleton-bone cell"></div>
                            <div class="skeleton-bone cell"></div>
                            <div class="skeleton-bone cell"></div>
                        </div>
                    </div>
                </div>
            `;
        }

        if (path === '/sensor/dht22') {
            return `
                <div class="skeleton-header">
                    <div class="skeleton-bone skeleton-title"></div>
                    <div class="skeleton-bone skeleton-subtitle"></div>
                </div>
                <h3 class="section-title skeleton-bone" style="width: 220px; margin-top: 1.5rem; margin-bottom: 1rem; height: 18px;"></h3>
                <div class="summary-grid" style="margin-bottom: 2rem;">
                    <div class="glow-card sensor-meter-card sensor-meter-temperature skeleton-card">
                        <div class="skeleton-bone skeleton-card-title"></div>
                        <div class="skeleton-gauge-container">
                            <div class="skeleton-gauge-arc"></div>
                            <div class="skeleton-bone skeleton-gauge-value"></div>
                        </div>
                        <div class="skeleton-bone skeleton-card-desc"></div>
                    </div>
                    <div class="glow-card sensor-meter-card sensor-meter-humidity skeleton-card">
                        <div class="skeleton-bone skeleton-card-title"></div>
                        <div class="skeleton-gauge-container">
                            <div class="skeleton-gauge-arc"></div>
                            <div class="skeleton-bone skeleton-gauge-value"></div>
                        </div>
                        <div class="skeleton-bone skeleton-card-desc"></div>
                    </div>
                </div>
                <div class="glow-card chart-wrapper skeleton-card" style="position: relative; min-height: 350px;">
                    <div class="skeleton-bone skeleton-large-title"></div>
                    <div class="skeleton-chart-placeholder">
                        <div class="skeleton-chart-line"></div>
                        <div class="skeleton-chart-line delay-1"></div>
                        <div class="skeleton-chart-line delay-2"></div>
                    </div>
                </div>
                <div class="glow-card table-wrapper skeleton-card" style="margin-top: 2rem;">
                    <div class="skeleton-bone skeleton-large-title" style="width: 160px; margin-bottom: 1.5rem;"></div>
                    <div class="skeleton-table">
                        <div class="skeleton-table-row skeleton-table-head cols-4">
                            <div class="skeleton-bone cell"></div>
                            <div class="skeleton-bone cell"></div>
                            <div class="skeleton-bone cell"></div>
                            <div class="skeleton-bone cell"></div>
                        </div>
                        <div class="skeleton-table-row cols-4">
                            <div class="skeleton-bone cell"></div>
                            <div class="skeleton-bone cell"></div>
                            <div class="skeleton-bone cell"></div>
                            <div class="skeleton-bone cell"></div>
                        </div>
                        <div class="skeleton-table-row cols-4">
                            <div class="skeleton-bone cell"></div>
                            <div class="skeleton-bone cell"></div>
                            <div class="skeleton-bone cell"></div>
                            <div class="skeleton-bone cell"></div>
                        </div>
                    </div>
                </div>
            `;
        }

        if (path === '/schedule') {
            return `
                <div class="skeleton-header">
                    <div class="skeleton-bone skeleton-title"></div>
                    <div class="skeleton-bone skeleton-subtitle"></div>
                </div>
                <div class="summary-grid" style="margin-top: 2rem;">
                    <div class="schedule-card skeleton-card" style="min-height: 180px;">
                        <div class="skeleton-schedule-header" style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 0.75rem; margin-bottom: 0.75rem;">
                            <div class="skeleton-bone" style="width: 80px; height: 14px;"></div>
                            <div class="skeleton-bone" style="width: 10px; height: 10px; border-radius: 50%;"></div>
                        </div>
                        <div class="skeleton-schedule-inputs" style="display: flex; flex-direction: column; gap: 0.75rem;">
                            <div class="skeleton-schedule-input-group" style="display: flex; justify-content: space-between; align-items: center;">
                                <div class="skeleton-bone" style="width: 80px; height: 10px;"></div>
                                <div class="skeleton-bone" style="width: 100px; height: 32px; border-radius: 6px;"></div>
                            </div>
                            <div class="skeleton-schedule-input-group" style="display: flex; justify-content: space-between; align-items: center;">
                                <div class="skeleton-bone" style="width: 90px; height: 10px;"></div>
                                <div class="skeleton-bone" style="width: 100px; height: 32px; border-radius: 6px;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="schedule-card skeleton-card" style="min-height: 180px;">
                        <div class="skeleton-schedule-header" style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 0.75rem; margin-bottom: 0.75rem;">
                            <div class="skeleton-bone" style="width: 80px; height: 14px;"></div>
                            <div class="skeleton-bone" style="width: 10px; height: 10px; border-radius: 50%;"></div>
                        </div>
                        <div class="skeleton-schedule-inputs" style="display: flex; flex-direction: column; gap: 0.75rem;">
                            <div class="skeleton-schedule-input-group" style="display: flex; justify-content: space-between; align-items: center;">
                                <div class="skeleton-bone" style="width: 80px; height: 10px;"></div>
                                <div class="skeleton-bone" style="width: 100px; height: 32px; border-radius: 6px;"></div>
                            </div>
                            <div class="skeleton-schedule-input-group" style="display: flex; justify-content: space-between; align-items: center;">
                                <div class="skeleton-bone" style="width: 90px; height: 10px;"></div>
                                <div class="skeleton-bone" style="width: 100px; height: 32px; border-radius: 6px;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="schedule-card skeleton-card" style="min-height: 180px;">
                        <div class="skeleton-schedule-header" style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 0.75rem; margin-bottom: 0.75rem;">
                            <div class="skeleton-bone" style="width: 80px; height: 14px;"></div>
                            <div class="skeleton-bone" style="width: 10px; height: 10px; border-radius: 50%;"></div>
                        </div>
                        <div class="skeleton-schedule-inputs" style="display: flex; flex-direction: column; gap: 0.75rem;">
                            <div class="skeleton-schedule-input-group" style="display: flex; justify-content: space-between; align-items: center;">
                                <div class="skeleton-bone" style="width: 80px; height: 10px;"></div>
                                <div class="skeleton-bone" style="width: 100px; height: 32px; border-radius: 6px;"></div>
                            </div>
                            <div class="skeleton-schedule-input-group" style="display: flex; justify-content: space-between; align-items: center;">
                                <div class="skeleton-bone" style="width: 90px; height: 10px;"></div>
                                <div class="skeleton-bone" style="width: 100px; height: 32px; border-radius: 6px;"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="schedule-card smart-backup-card skeleton-card" style="margin-top: 1.5rem; display: flex; flex-direction: row; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 1.5rem; min-height: 120px; padding: 1.5rem 2rem;">
                    <div class="skeleton-smart-backup-info" style="display: flex; flex-direction: column; gap: 0.5rem; flex: 2; min-width: 250px;">
                        <div class="skeleton-smart-backup-header" style="display: flex; align-items: center; gap: 0.75rem;">
                            <div class="skeleton-bone" style="width: 120px; height: 16px;"></div>
                            <div class="skeleton-bone" style="width: 10px; height: 10px; border-radius: 50%;"></div>
                        </div>
                        <div class="skeleton-bone" style="width: 90%; height: 12px;"></div>
                        <div class="skeleton-bone" style="width: 70%; height: 12px;"></div>
                    </div>
                    <div class="skeleton-smart-backup-control" style="display: flex; flex-direction: column; gap: 0.5rem; flex: 1; min-width: 200px; align-items: flex-end;">
                        <div class="skeleton-bone" style="width: 140px; height: 10px;"></div>
                        <div class="skeleton-bone" style="width: 120px; height: 36px; border-radius: 8px;"></div>
                    </div>
                </div>
                <div class="action-row" style="display: flex; justify-content: flex-end; margin-top: 1.5rem;">
                    <div class="skeleton-bone" style="width: 180px; height: 44px; border-radius: 100px;"></div>
                </div>
            `;
        }

        if (path === '/settings') {
            return `
                <div class="skeleton-header">
                    <div class="skeleton-bone skeleton-title"></div>
                    <div class="skeleton-bone skeleton-subtitle"></div>
                </div>
                <div class="settings-container" style="display: flex; flex-direction: column; gap: 1.5rem; margin-top: 2rem;">
                    <div class="glow-card settings-card skeleton-card" style="min-height: 220px;">
                        <div class="skeleton-bone" style="width: 220px; height: 20px; margin-bottom: 0.5rem;"></div>
                        <div class="skeleton-bone" style="width: 320px; height: 12px; margin-bottom: 1.5rem;"></div>
                        <div class="skeleton-danger-zone" style="border: 1px dashed rgba(239, 68, 68, 0.2); padding: 1.25rem; border-radius: 12px; background: rgba(239, 68, 68, 0.02); display: flex; flex-direction: column; gap: 0.75rem;">
                            <div class="skeleton-danger-header" style="display: flex; align-items: center; gap: 0.5rem;">
                                <div class="skeleton-bone" style="width: 20px; height: 20px; border-radius: 4px; background: rgba(239, 68, 68, 0.2);"></div>
                                <div class="skeleton-bone" style="width: 120px; height: 14px; background: rgba(239, 68, 68, 0.2);"></div>
                            </div>
                            <div class="skeleton-bone" style="width: 90%; height: 12px;"></div>
                            <div class="skeleton-bone" style="width: 160px; height: 38px; border-radius: 8px; margin-top: 0.5rem; background: rgba(239, 68, 68, 0.15);"></div>
                        </div>
                    </div>
                    <div class="glow-card settings-card skeleton-card" style="min-height: 260px;">
                        <div class="skeleton-bone" style="width: 240px; height: 20px; margin-bottom: 0.5rem;"></div>
                        <div class="skeleton-bone" style="width: 380px; height: 12px; margin-bottom: 1.5rem;"></div>
                        <div class="skeleton-ui-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.5rem;">
                            <div class="skeleton-ui-card" style="border: 1px solid rgba(255,255,255,0.05); border-radius: 12px; padding: 1.25rem; display: flex; align-items: center; gap: 1rem; min-height: 80px;">
                                <div class="skeleton-bone" style="width: 44px; height: 44px; border-radius: 50%;"></div>
                                <div style="flex: 1; display: flex; flex-direction: column; gap: 0.4rem;">
                                    <div class="skeleton-bone" style="width: 120px; height: 14px;"></div>
                                    <div class="skeleton-bone" style="width: 80px; height: 10px;"></div>
                                </div>
                            </div>
                            <div class="skeleton-ui-card" style="border: 1px solid rgba(255,255,255,0.05); border-radius: 12px; padding: 1.25rem; display: flex; align-items: center; gap: 1rem; min-height: 80px;">
                                <div class="skeleton-bone" style="width: 44px; height: 44px; border-radius: 50%;"></div>
                                <div style="flex: 1; display: flex; flex-direction: column; gap: 0.4rem;">
                                    <div class="skeleton-bone" style="width: 120px; height: 14px;"></div>
                                    <div class="skeleton-bone" style="width: 80px; height: 10px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        if (path === '/admin/users') {
            return `
                <div class="skeleton-header">
                    <div class="skeleton-bone skeleton-title"></div>
                    <div class="skeleton-bone skeleton-subtitle"></div>
                </div>
                <div class="settings-container" style="margin-top: 2rem;">
                    <div class="glow-card settings-card skeleton-card" style="min-height: 300px;">
                        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
                            <div class="skeleton-bone" style="width: 24px; height: 24px; border-radius: 4px;"></div>
                            <div class="skeleton-bone" style="width: 160px; height: 20px;"></div>
                        </div>
                        <div class="skeleton-bone" style="width: 380px; height: 12px; margin-bottom: 2rem;"></div>
                        
                        <div class="user-cards-container">
                            <div class="user-accordion-card skeleton-card" style="display: flex; justify-content: space-between; align-items: center; min-height: 70px;">
                                <div style="display: flex; flex-direction: column; gap: 0.4rem;">
                                    <div class="skeleton-bone" style="width: 100px; height: 16px;"></div>
                                    <div class="skeleton-bone" style="width: 140px; height: 12px;"></div>
                                </div>
                                <div class="skeleton-bone" style="width: 110px; height: 32px; border-radius: 8px;"></div>
                            </div>
                            <div class="user-accordion-card skeleton-card" style="display: flex; justify-content: space-between; align-items: center; min-height: 70px;">
                                <div style="display: flex; flex-direction: column; gap: 0.4rem;">
                                    <div class="skeleton-bone" style="width: 80px; height: 16px;"></div>
                                    <div class="skeleton-bone" style="width: 160px; height: 12px;"></div>
                                </div>
                                <div class="skeleton-bone" style="width: 110px; height: 32px; border-radius: 8px;"></div>
                            </div>
                            <div class="user-accordion-card skeleton-card" style="display: flex; justify-content: space-between; align-items: center; min-height: 70px;">
                                <div style="display: flex; flex-direction: column; gap: 0.4rem;">
                                    <div class="skeleton-bone" style="width: 120px; height: 16px;"></div>
                                    <div class="skeleton-bone" style="width: 130px; height: 12px;"></div>
                                </div>
                                <div class="skeleton-bone" style="width: 110px; height: 32px; border-radius: 8px;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        if (path === '/view3d') {
            return `
                <div class="skeleton-header">
                    <div class="skeleton-bone skeleton-title"></div>
                    <div class="skeleton-bone skeleton-subtitle"></div>
                </div>
                <div class="view3d-container" style="margin-top: 2rem;">
                    <div class="canvas-wrapper skeleton-card" style="display: flex; align-items: center; justify-content: center; position: relative;">
                        <div class="skeleton-bone" style="width: 150px; height: 150px; border-radius: 12px; background: radial-gradient(circle, rgba(16, 185, 129, 0.15) 0%, transparent 70%);"></div>
                        <div class="skeleton-bone" style="position: absolute; bottom: 20px; left: 20px; width: 140px; height: 10px;"></div>
                    </div>
                    <div class="control-sidebar">
                        <div class="panel-card skeleton-card" style="min-height: 140px; gap: 0.75rem;">
                            <div class="skeleton-bone" style="width: 110px; height: 14px;"></div>
                            <div style="display: grid; grid-template-columns: 1fr; gap: 0.5rem; margin-top: 0.5rem;">
                                <div class="skeleton-bone" style="width: 100%; height: 32px; border-radius: 6px;"></div>
                                <div class="skeleton-bone" style="width: 100%; height: 32px; border-radius: 6px;"></div>
                            </div>
                        </div>
                        <div class="panel-card skeleton-card" style="min-height: 120px; gap: 0.75rem;">
                            <div class="skeleton-bone" style="width: 120px; height: 14px;"></div>
                            <div style="display: flex; flex-direction: column; gap: 0.5rem; margin-top: 0.5rem;">
                                <div class="skeleton-bone" style="width: 90%; height: 10px;"></div>
                                <div class="skeleton-bone" style="width: 80%; height: 10px;"></div>
                            </div>
                        </div>
                        <div class="panel-card skeleton-card" style="min-height: 160px; gap: 0.75rem;">
                            <div class="skeleton-bone" style="width: 130px; height: 14px;"></div>
                            <div style="display: flex; flex-direction: column; gap: 0.6rem; margin-top: 0.75rem;">
                                <div style="display: flex; justify-content: space-between;">
                                    <div class="skeleton-bone" style="width: 80px; height: 10px;"></div>
                                    <div class="skeleton-bone" style="width: 60px; height: 10px;"></div>
                                </div>
                                <div style="display: flex; justify-content: space-between;">
                                    <div class="skeleton-bone" style="width: 100px; height: 10px;"></div>
                                    <div class="skeleton-bone" style="width: 50px; height: 10px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        return `
            <div class="skeleton-header">
                <div class="skeleton-bone skeleton-title"></div>
                <div class="skeleton-bone skeleton-subtitle"></div>
            </div>
            <div class="skeleton-grid">
                <div class="skeleton-card">
                    <div class="skeleton-bone skeleton-card-title"></div>
                    <div class="skeleton-bone skeleton-card-value"></div>
                    <div class="skeleton-bone skeleton-card-desc"></div>
                </div>
                <div class="skeleton-card">
                    <div class="skeleton-bone skeleton-card-title"></div>
                    <div class="skeleton-bone skeleton-card-value"></div>
                    <div class="skeleton-bone skeleton-card-desc"></div>
                </div>
                <div class="skeleton-card">
                    <div class="skeleton-bone skeleton-card-title"></div>
                    <div class="skeleton-bone skeleton-card-value"></div>
                    <div class="skeleton-bone skeleton-card-desc"></div>
                </div>
            </div>
            <div class="skeleton-body-row">
                <div class="skeleton-card skeleton-large-card">
                    <div class="skeleton-bone skeleton-large-title"></div>
                    <div class="skeleton-bone skeleton-large-content"></div>
                </div>
            </div>
        `;
    };

    // 1. Inject the skeleton structure dynamically if it doesn't exist
    const injectSkeletonLoader = (urlPath = window.location.pathname) => {
        if (!panelContent) return;
        
        let skeletonContainer = panelContent.querySelector('.skeleton-loader-container');
        if (!skeletonContainer) {
            skeletonContainer = document.createElement('div');
            skeletonContainer.className = 'skeleton-loader-container';
            panelContent.insertBefore(skeletonContainer, panelContent.firstChild);
        }
        
        skeletonContainer.innerHTML = getSkeletonTemplateForPath(urlPath);
    };

    injectSkeletonLoader();

    // 2. Create the blurred loading overlay dynamically (starts hidden, used only for settings theme switch)
    let transitionOverlay = document.getElementById('page-transition-overlay');
    if (!transitionOverlay) {
        transitionOverlay = document.createElement('div');
        transitionOverlay.id = 'page-transition-overlay';
        transitionOverlay.className = 'page-transition-overlay hidden';
        transitionOverlay.innerHTML = '<div class="transition-spinner"></div>';
        document.body.appendChild(transitionOverlay);
    }

    // 3. Initial Page Entrance Animation (Fade-In / Hide Overlay)
    const completeEntrance = () => {
        if (panelContent) {
            panelContent.classList.add('loaded');
        }
        transitionOverlay.classList.add('hidden');
    };

    if (document.readyState === 'complete') {
        completeEntrance();
    } else {
        const forceLoadTimeout = setTimeout(completeEntrance, 1200);
        window.addEventListener('load', () => {
            clearTimeout(forceLoadTimeout);
            completeEntrance();
        });
    }

    // 4. Link Click Interception (Page Exit Transition)
    const allLinks = document.querySelectorAll('a');
    allLinks.forEach(link => {
        const href = link.getAttribute('href');
        
        if (!href) return;
        
        const isInternal = href.startsWith('/') || href.startsWith(window.location.origin);
        const isHash = href.startsWith('#');
        const isJavascript = href.startsWith('javascript:');
        const isLogout = href.includes('logout') || link.closest('form[action*="logout"]');
        const isDownload = href.includes('export') || link.hasAttribute('download');

        if (isInternal && !isHash && !isJavascript && !isLogout && !isDownload) {
            link.addEventListener('click', (e) => {
                if (e.metaKey || e.ctrlKey || e.shiftKey || e.button !== 0) return;

                e.preventDefault();
                const targetUrl = link.href;

                if (sidebar) {
                    sidebar.classList.remove('show');
                }

                injectSkeletonLoader(targetUrl);

                if (panelContent) {
                    panelContent.classList.remove('loaded');
                }

                setTimeout(() => {
                    window.location.href = targetUrl;
                }, 220);
            });
        }
    });
});