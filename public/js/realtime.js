document.addEventListener('DOMContentLoaded', function () {
    // Cache to prevent unnecessary DOM updates and flickering
    let lastLatestJson = '';
    let lastTargetKelembapan = null;
    let lastTableDataJson = '';
    let lastChartDataJson = '';

    // Polling interval 5 seconds
    setInterval(fetchRealtimeData, 5000);

    function fetchRealtimeData() {
        fetch('/panel/data/realtime?t=' + new Date().getTime())
            .then(response => response.json())
            .then(data => {
                updateCards(data.latest, data.targetKelembapan, data.manual_pump_status);
                updateTable(data.riwayatTabel);
                updateChart(data.riwayatGrafik);
            })
            .catch(error => console.error('Error fetching realtime data:', error));
    }

    let lastManualPumpStatus = null;

    function updateCards(latest, targetKelembapan, manualPumpStatus) {
        if (!latest) return;

        // Check if data actually changed to avoid redundant layout shifts / animations
        const currentJson = JSON.stringify(latest);
        if (currentJson === lastLatestJson && targetKelembapan === lastTargetKelembapan && manualPumpStatus === lastManualPumpStatus) {
            return;
        }
        lastLatestJson = currentJson;
        lastTargetKelembapan = targetKelembapan;
        lastManualPumpStatus = manualPumpStatus;

        // Sync Pump UI based on server manual_pump_status
        const btnText = document.getElementById('pump-btn-text');
        const stateLabel = document.getElementById('pump-state-label');
        const indicatorDot = document.getElementById('pump-indicator-dot');
        
        if (btnText && stateLabel && indicatorDot) {
            isPumpOn = (manualPumpStatus === 'ON'); // Update global variable

            if (isPumpOn) {
                btnText.innerText = "MATIKAN";
                stateLabel.innerText = "ON";
                stateLabel.style.color = "#10b981";
                indicatorDot.classList.remove('offline');
                indicatorDot.classList.add('online');
            } else {
                btnText.innerText = "NYALAKAN";
                stateLabel.innerText = "OFF";
                stateLabel.style.color = "#ededed";
                indicatorDot.classList.remove('online');
                indicatorDot.classList.add('offline');
            }
        }

        // Update Values
        if (document.getElementById('val-cahaya')) {
            document.getElementById('val-cahaya').innerText = latest.cahaya + ' Lux';
        }
        if (document.getElementById('val-suhu')) {
            document.getElementById('val-suhu').innerText = latest.suhu + '°C';
        }
        if (document.getElementById('val-kelembapan')) {
            document.getElementById('val-kelembapan').innerText = latest.kelembapan + '%';
        }

        // Update meter angles
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

        // Update status dots
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

        // Check if table data changed to avoid unnecessary DOM writes
        const currentJson = JSON.stringify(riwayatTabel);
        if (currentJson === lastTableDataJson) {
            return;
        }
        lastTableDataJson = currentJson;

        if (!riwayatTabel || riwayatTabel.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-muted" style="text-align: center; padding: 2rem;">Belum ada data sensor masuk.</td></tr>';
            return;
        }

        let html = '';
        riwayatTabel.forEach(log => {
            const pompaClass = log.pompa_status === 'ON' ? 'text-blue' : 'text-muted';
            html += `
                <tr>
                    <td class="text-muted">${log.time_diff}</td>
                    <td>${log.sensor_id}</td>
                    <td><span class="badge success">Tercatat</span></td>
                    <td>
                        <span class="fw-bold ${pompaClass}">
                            ${log.pompa_status}
                        </span>
                    </td>
                    <td class="text-right">${log.kelembapan}% | ${log.suhu}°C</td>
                </tr>
            `;
        });
        
        tbody.innerHTML = html;
    }

    function updateChart(riwayatGrafik) {
        if (!window.chartArea) return;
        if (!riwayatGrafik || riwayatGrafik.length === 0) return;

        // Check if chart data actually changed to avoid unnecessary redraws/flickering
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

        // Update options and series in ONE atomic call to prevent double render cycles/flickering
        window.chartArea.updateOptions({
            xaxis: {
                categories: waktuLabels
            },
            series: [
                { name: 'Kelembapan (%)', data: kelembapanSeries },
                { name: 'Suhu (°C)', data: suhuSeries }
            ]
        }, false, true); // redrawPaths = false, animate = true for smooth transitions
    }
});

// Doherty Threshold: Optimistic UI for Pump Toggle
let isPumpOn = false;

function togglePumpOptimistic() {
    const btnText = document.getElementById('pump-btn-text');
    const stateLabel = document.getElementById('pump-state-label');
    const indicatorDot = document.getElementById('pump-indicator-dot');
    
    // 1. OPTIMISTIC UPDATE (Instant Feedback < 50ms)
    isPumpOn = !isPumpOn;
    
    if (isPumpOn) {
        btnText.innerText = "MATIKAN";
        stateLabel.innerText = "ON";
        stateLabel.style.color = "#10b981";
        indicatorDot.classList.remove('offline');
        indicatorDot.classList.add('online');
    } else {
        btnText.innerText = "NYALAKAN";
        stateLabel.innerText = "OFF";
        stateLabel.style.color = "#ededed";
        indicatorDot.classList.remove('online');
        indicatorDot.classList.add('offline');
    }

    // 2. BACKGROUND REQUEST
    fetch('/panel/pump/toggle', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ status: isPumpOn ? 'ON' : 'AUTO' })
    }).then(response => {
        if (!response.ok) throw new Error('Network response was not ok');
        return response.json();
    }).then(data => {
        if (data.status !== 'success') throw new Error(data.message || 'Error from server');
    }).catch(error => {
        // 3. REVERT ON FAILURE
        console.error("Failed to toggle pump:", error);
        alert("Gagal menyimpan status kontrol manual pompa.");
        window.location.reload(); // Revert ke state sebenarnya dari server
    });
}
