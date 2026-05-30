document.addEventListener('DOMContentLoaded', function () {
    let lastLatestJson = '';
    let lastTargetKelembapan = null;
    let lastTableDataJson = '';
    let lastChartDataJson = '';

    fetchRealtimeData();
    setInterval(fetchRealtimeData, 5000);

    function fetchRealtimeData() {
        fetch('/panel/data/realtime?t=' + new Date().getTime())
            .then(response => response.json())
            .then(data => {
                updateCards(data.latest, data.targetKelembapan, data.manual_pump_status);
                
                // Only update table and chart dynamically if NO date or custom limit filter is active in the URL
                const urlParams = new URLSearchParams(window.location.search);
                const isFiltered = (urlParams.has('date') && urlParams.get('date') !== '') || urlParams.has('limit');
                
                if (!isFiltered) {
                    updateTable(data.riwayatTabel);
                    updateChart(data.riwayatGrafik);
                }
            })
            .catch(error => console.error('Error fetching realtime data:', error));
    }

    let lastManualPumpStatus = null;

    function updateCards(latest, targetKelembapan, manualPumpStatus) {
        if (!latest) return;

        const currentJson = JSON.stringify(latest);
        if (currentJson === lastLatestJson && targetKelembapan === lastTargetKelembapan && manualPumpStatus === lastManualPumpStatus) {
            return;
        }
        lastLatestJson = currentJson;
        lastTargetKelembapan = targetKelembapan;
        lastManualPumpStatus = manualPumpStatus;

        const btnText = document.getElementById('pump-btn-text');
        const stateLabel = document.getElementById('pump-state-label');
        const indicatorDot = document.getElementById('pump-indicator-dot');
        const toggleBtn = document.getElementById('btn-toggle-pump');

        if (btnText && stateLabel && indicatorDot) {
            isPumpOn = (manualPumpStatus === 'ON');

            if (isPumpOn) {
                btnText.innerText = "MATIKAN";
                stateLabel.innerText = "ON";
                stateLabel.style.color = "#10b981";
                indicatorDot.classList.remove('offline');
                indicatorDot.classList.add('online');
                if (toggleBtn) {
                    toggleBtn.classList.add('pump-active');
                }
            } else {
                btnText.innerText = "NYALAKAN";
                stateLabel.innerText = "OFF";
                stateLabel.style.color = "#ededed";
                indicatorDot.classList.remove('online');
                indicatorDot.classList.add('offline');
                if (toggleBtn) {
                    toggleBtn.classList.remove('pump-active');
                }
            }
        }

        if (document.getElementById('val-cahaya')) {
            document.getElementById('val-cahaya').innerText = latest.cahaya + ' Lux';
        }
        if (document.getElementById('val-suhu')) {
            document.getElementById('val-suhu').innerText = latest.suhu + '°C';
        }
        if (document.getElementById('val-kelembapan')) {
            document.getElementById('val-kelembapan').innerText = latest.kelembapan + '%';
        }

        if (document.getElementById('card-suhu')) {
            const meterSuhu = Math.min(Math.max((latest.suhu_raw || 0) / 40, 0), 1) * 180;
            document.getElementById('card-suhu').style.setProperty('--meter-angle', meterSuhu + 'deg');
        }

        if (document.getElementById('card-kelembapan')) {
            const meterKelembapan = Math.min(Math.max((latest.kelembapan_raw || 0) / 100, 0), 1) * 180;
            document.getElementById('card-kelembapan').style.setProperty('--meter-angle', meterKelembapan + 'deg');

            const descKelembapan = document.getElementById('desc-kelembapan');
            if (descKelembapan) {
                if ((latest.kelembapan_raw || 0) >= targetKelembapan) {
                    descKelembapan.classList.add('text-positive');
                } else {
                    descKelembapan.classList.remove('text-positive');
                }
            }
        }

        const statusDots = document.querySelectorAll('.status-dot');
        const statusClass = latest.is_online ? 'online' : 'offline';
        const removeClass = latest.is_online ? 'offline' : 'online';

        statusDots.forEach(dot => {
            dot.classList.remove(removeClass);
            dot.classList.add(statusClass);
        });
    }

    function updateTable(riwayatTabel) {
        const tbody = document.getElementById('table-body-log');
        if (!tbody) return;

        const currentJson = JSON.stringify(riwayatTabel);
        if (currentJson === lastTableDataJson) {
            return;
        }
        lastTableDataJson = currentJson;

        tbody.textContent = '';

        if (!riwayatTabel || riwayatTabel.length === 0) {
            const tr = document.createElement('tr');
            const td = document.createElement('td');
            td.colSpan = 5;
            td.className = 'text-muted';
            td.style.textAlign = 'center';
            td.style.padding = '2rem';
            td.textContent = 'Belum ada data sensor masuk.';
            tr.appendChild(td);
            tbody.appendChild(tr);
            return;
        }

        const isLDR = document.querySelector('th.text-right')?.innerText.includes('CAHAYA');
        const headerCount = document.querySelectorAll('.data-table thead th').length;

        riwayatTabel.forEach(log => {
            const tr = document.createElement('tr');

            const tdTime = document.createElement('td');
            tdTime.className = 'text-muted';
            
            const spanTime = document.createElement('span');
            spanTime.style.color = '#ededed';
            spanTime.textContent = log.time_formatted;
            
            const smallDiff = document.createElement('small');
            smallDiff.style.fontSize = '0.7rem';
            smallDiff.style.marginLeft = '0.5rem';
            smallDiff.style.opacity = '0.6';
            smallDiff.textContent = log.time_diff;
            
            tdTime.appendChild(spanTime);
            tdTime.appendChild(document.createTextNode(' '));
            tdTime.appendChild(smallDiff);
            tr.appendChild(tdTime);

            const tdSensor = document.createElement('td');
            tdSensor.textContent = log.sensor_id;
            tr.appendChild(tdSensor);

            const tdStatus = document.createElement('td');
            const spanBadge = document.createElement('span');
            spanBadge.className = 'badge success';
            spanBadge.textContent = 'Tercatat';
            tdStatus.appendChild(spanBadge);
            tr.appendChild(tdStatus);

            if (headerCount === 6) {
                const tdPump = document.createElement('td');
                const spanPump = document.createElement('span');
                const pompaClass = log.pompa_status === 'ON' ? 'text-blue' : 'text-muted';
                spanPump.className = 'fw-bold ' + pompaClass;
                spanPump.textContent = log.pompa_status;
                tdPump.appendChild(spanPump);
                tr.appendChild(tdPump);

                const tdLight = document.createElement('td');
                tdLight.textContent = (log.cahaya ?? '--') + ' Lux';
                tr.appendChild(tdLight);

                const tdValues = document.createElement('td');
                tdValues.className = 'text-right';
                tdValues.textContent = log.kelembapan + '% | ' + log.suhu + '°C';
                tr.appendChild(tdValues);
            } else if (isLDR) {
                const tdLight = document.createElement('td');
                tdLight.className = 'text-right';
                tdLight.textContent = (log.cahaya ?? '--') + ' Lux';
                tr.appendChild(tdLight);
            } else if (document.querySelector('th.text-right')?.innerText.includes('NILAI')) {
                const tdValues = document.createElement('td');
                tdValues.className = 'text-right';
                tdValues.textContent = log.kelembapan + '% | ' + log.suhu + '°C';
                tr.appendChild(tdValues);
            } else {
                const tdPump = document.createElement('td');
                const spanPump = document.createElement('span');
                const pompaClass = log.pompa_status === 'ON' ? 'text-blue' : 'text-muted';
                spanPump.className = 'fw-bold ' + pompaClass;
                spanPump.textContent = log.pompa_status;
                tdPump.appendChild(spanPump);
                tr.appendChild(tdPump);

                const tdValues = document.createElement('td');
                tdValues.className = 'text-right';
                tdValues.textContent = log.kelembapan + '% | ' + log.suhu + '°C';
                tr.appendChild(tdValues);
            }

            tbody.appendChild(tr);
        });
    }

    function updateChart(riwayatGrafik) {
        window.dataJamkot = riwayatGrafik;

        if (!window.chartArea) {
            if (riwayatGrafik && riwayatGrafik.length > 0) {
                window.renderAllCharts();
            }
            return;
        }

        if (!riwayatGrafik || riwayatGrafik.length === 0) {
            if (window.chartArea && typeof window.chartArea.destroy === 'function') {
                try { window.chartArea.destroy(); } catch (e) { }
            }
            window.chartArea = null;
            window.renderAllCharts();
            return;
        }

        const currentJson = JSON.stringify(riwayatGrafik);
        if (currentJson === lastChartDataJson) {
            return;
        }
        lastChartDataJson = currentJson;

        const waktuLabels = riwayatGrafik.map(item => {
            let date = new Date(item.created_at);
            return date.getHours().toString().padStart(2, '0') + ':' + date.getMinutes().toString().padStart(2, '0');
        });

        const suhuSeries = riwayatGrafik.map(item => item.suhu);
        const kelembapanSeries = riwayatGrafik.map(item => item.kelembapan);

        window.chartArea.updateOptions({
            xaxis: {
                categories: waktuLabels
            },
            series: [
                { name: 'Kelembapan (%)', data: kelembapanSeries },
                { name: 'Suhu (°C)', data: suhuSeries }
            ]
        }, false, true);
    }

    // ----------------------------------------
    // Device Status
    // ----------------------------------------

    fetchDeviceStatus();
    setInterval(fetchDeviceStatus, 30000);

    function fetchDeviceStatus() {
        fetch('/panel/device/status?t=' + new Date().getTime())
            .then(r => r.json())
            .then(updatePanelDeviceShortcut)
            .catch(() => setPanelDeviceOffline());
    }

    function updatePanelDeviceShortcut(data) {
        // Update DHT22 sensor connection badge if present
        const dhtDot = document.getElementById('sensor-dht-dot');
        const dhtText = document.getElementById('sensor-dht-text');
        if (dhtDot && dhtText) {
            if (data.found) {
                const connected = data.dht_connected;
                dhtDot.className = 'device-status-dot ' + (connected ? 'online' : 'offline');
                dhtText.textContent = connected ? 'Terhubung' : 'Tidak Terhubung';
                dhtText.style.color = connected ? '#34d399' : '#f87171';
            } else {
                dhtDot.className = 'device-status-dot offline';
                dhtText.textContent = 'Tidak Terhubung';
                dhtText.style.color = '#f87171';
            }
        }

        // Update LDR sensor connection badge if present
        const ldrDot = document.getElementById('sensor-ldr-dot');
        const ldrText = document.getElementById('sensor-ldr-text');
        if (ldrDot && ldrText) {
            if (data.found) {
                const connected = data.ldr_connected;
                ldrDot.className = 'device-status-dot ' + (connected ? 'online' : 'offline');
                ldrText.textContent = connected ? 'Terhubung' : 'Tidak Terhubung';
                ldrText.style.color = connected ? '#34d399' : '#f87171';
            } else {
                ldrDot.className = 'device-status-dot offline';
                ldrText.textContent = 'Tidak Terhubung';
                ldrText.style.color = '#f87171';
            }
        }

        const dot    = document.getElementById('panel-device-dot');
        const badge  = document.getElementById('panel-device-badge');
        const detail = document.getElementById('panel-device-detail');

        if (!dot) return;

        if (!data.found) {
            setPanelDeviceOffline();
            if (detail) detail.textContent = 'Belum ada data';
            return;
        }

        dot.className   = 'device-status-dot ' + (data.is_online ? 'online' : 'offline');
        badge.className = 'device-status-badge ' + (data.is_online ? 'online' : 'offline');
        badge.textContent = data.status_label;

        if (detail) {
            const parts = [];
            if (data.uptime_formatted) parts.push('Uptime: ' + data.uptime_formatted);
            if (data.rssi) parts.push('WiFi: ' + data.rssi + ' dBm');
            if (data.esp_temp !== null && data.esp_temp !== undefined) parts.push('Suhu ESP: ' + data.esp_temp_formatted);
            detail.textContent = parts.join('  •  ') || '—';
        }
    }

    function setPanelDeviceOffline() {
        const dot   = document.getElementById('panel-device-dot');
        const badge = document.getElementById('panel-device-badge');
        if (dot)   dot.className = 'device-status-dot offline';
        if (badge) { badge.className = 'device-status-badge offline'; badge.textContent = 'Offline'; }

        const dhtDot = document.getElementById('sensor-dht-dot');
        const dhtText = document.getElementById('sensor-dht-text');
        if (dhtDot && dhtText) {
            dhtDot.className = 'device-status-dot offline';
            dhtText.textContent = 'Tidak Terhubung';
            dhtText.style.color = '#f87171';
        }

        const ldrDot = document.getElementById('sensor-ldr-dot');
        const ldrText = document.getElementById('sensor-ldr-text');
        if (ldrDot && ldrText) {
            ldrDot.className = 'device-status-dot offline';
            ldrText.textContent = 'Tidak Terhubung';
            ldrText.style.color = '#f87171';
        }
    }
});

// Doherty Threshold: Optimistic UI for Pump Toggle
let isPumpOn = false;

function togglePumpOptimistic() {
    const btnText = document.getElementById('pump-btn-text');
    const stateLabel = document.getElementById('pump-state-label');
    const indicatorDot = document.getElementById('pump-indicator-dot');
    const toggleBtn = document.getElementById('btn-toggle-pump');

    isPumpOn = !isPumpOn;

    if (isPumpOn) {
        btnText.innerText = "MATIKAN";
        stateLabel.innerText = "ON";
        stateLabel.style.color = "#10b981";
        indicatorDot.classList.remove('offline');
        indicatorDot.classList.add('online');
        if (toggleBtn) {
            toggleBtn.classList.add('pump-active');
        }
    } else {
        btnText.innerText = "NYALAKAN";
        stateLabel.innerText = "OFF";
        stateLabel.style.color = "#ededed";
        indicatorDot.classList.remove('online');
        indicatorDot.classList.add('offline');
        if (toggleBtn) {
            toggleBtn.classList.remove('pump-active');
        }
    }

    fetch('/panel/pump/toggle', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ status: isPumpOn ? 'ON' : 'OFF' })
    }).then(response => {
        if (!response.ok) throw new Error('Network response was not ok');
        return response.json();
    }).then(data => {
        if (data.status !== 'success') throw new Error(data.message || 'Error from server');
    }).catch(error => {
        console.error("Failed to toggle pump:", error);
        JKModal.alert({
            type: 'error',
            title: 'Gagal Toggle Pompa',
            message: 'Gagal menyimpan status kontrol manual pompa. Halaman akan dimuat ulang.',
            onOk: function () { window.location.reload(); }
        });
    });
}
