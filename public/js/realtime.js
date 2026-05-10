document.addEventListener('DOMContentLoaded', function () {
    // Polling interval 5 seconds
    setInterval(fetchRealtimeData, 5000);

    function fetchRealtimeData() {
        fetch('/panel/data/realtime?t=' + new Date().getTime())
            .then(response => response.json())
            .then(data => {
                updateCards(data.latest, data.targetKelembapan);
                updateTable(data.riwayatTabel);
                updateChart(data.riwayatGrafik);
            })
            .catch(error => console.error('Error fetching realtime data:', error));
    }

    function updateCards(latest, targetKelembapan) {
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

        const waktuLabels = riwayatGrafik.map(item => {
            let date = new Date(item.created_at);
            return date.getHours().toString().padStart(2, '0') + ':' + date.getMinutes().toString().padStart(2, '0');
        });

        const suhuSeries = riwayatGrafik.map(item => item.suhu);
        const kelembapanSeries = riwayatGrafik.map(item => item.kelembapan);

        window.chartArea.updateOptions({
            xaxis: {
                categories: waktuLabels
            }
        });

        window.chartArea.updateSeries([
            { name: 'Kelembapan (%)', data: kelembapanSeries },
            { name: 'Suhu (°C)', data: suhuSeries }
        ]);
    }
});
