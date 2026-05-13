// public/js/chart.js

document.addEventListener('DOMContentLoaded', function () {
    
    window.renderAllCharts = function () {
        // --- CLEANLY DESTROY EXISTING CHARTS IF ANY TO PREVENT GHOSTING ---
        if (window.chartArea && typeof window.chartArea.destroy === 'function') {
            try { window.chartArea.destroy(); } catch (e) {}
        }
        if (window.chartSuhu && typeof window.chartSuhu.destroy === 'function') {
            try { window.chartSuhu.destroy(); } catch (e) {}
        }
        if (window.chartKelembapan && typeof window.chartKelembapan.destroy === 'function') {
            try { window.chartKelembapan.destroy(); } catch (e) {}
        }
        if (window.chartAvgSuhu && typeof window.chartAvgSuhu.destroy === 'function') {
            try { window.chartAvgSuhu.destroy(); } catch (e) {}
        }
        if (window.chartAvgKelembapan && typeof window.chartAvgKelembapan.destroy === 'function') {
            try { window.chartAvgKelembapan.destroy(); } catch (e) {}
        }

        const rawData = window.dataJamkot;
        
        // --- DETERMINE UI PREFERENCE ---
        const isM3 = localStorage.getItem('jamkot-ui-version') === 'v1';
        const chartTextColors = isM3 ? '#a2aba7' : '#6b7280';
        const chartGridBorder = isM3 ? '#242c29' : '#1f1f1f';
        const chartThemeMode = 'dark';
        const gaugeTrackBg = isM3 ? '#242c29' : '#262626';
        const gaugeLabelColor = isM3 ? '#e1e3e1' : '#ededed';

        if (!rawData || rawData.length === 0) {
            if (document.querySelector("#chart-jamkot")) {
                document.querySelector("#chart-jamkot").innerHTML =
                    `<div style='text-align: center; color: ${chartTextColors}; padding: 2rem 0; font-size: 0.875rem;'>Belum ada data sensor untuk menampilkan grafik.</div>`;
            }
        } else {
            const chartData = rawData;
            const waktuLabels = chartData.map(item => {
                let date = new Date(item.created_at);
                return date.getHours().toString().padStart(2, '0') + ':' + date.getMinutes().toString().padStart(2, '0');
            });

            const suhuSeries = chartData.map(item => item.suhu);
            const kelembapanSeries = chartData.map(item => item.kelembapan);
            const warnaUtama = getComputedStyle(document.documentElement).getPropertyValue('--warna-utama').trim() || '#10b981';

            // Choose chart series colors dynamically
            const seriesColors = isM3 ? ['#80dec5', '#ffb68f'] : ['#10b981', '#06b6d4'];

            var optionsArea = {
                series: [
                    { name: 'Suhu (°C)', data: suhuSeries },
                    { name: 'Kelembapan (%)', data: kelembapanSeries }
                ],
                chart: {
                    height: 300,
                    type: 'area',
                    toolbar: { show: false },
                    background: 'transparent',
                    fontFamily: 'Outfit, Inter, sans-serif',
                    animations: {
                        enabled: true,
                        easing: 'easeinout',
                        speed: 900,
                        animateGradually: {
                            enabled: true,
                            delay: 120
                        },
                        dynamicAnimation: {
                            enabled: true,
                            speed: 450
                        }
                    }
                },
                theme: { mode: chartThemeMode },
                colors: seriesColors,
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 3 },
                xaxis: {
                    categories: waktuLabels,
                    labels: { style: { colors: chartTextColors, fontFamily: 'Outfit, sans-serif' } },
                    axisBorder: { color: chartGridBorder },
                    axisTicks: { color: chartGridBorder }
                },
                yaxis: {
                    labels: { style: { colors: chartTextColors, fontFamily: 'Outfit, sans-serif' } }
                },
                grid: {
                    borderColor: chartGridBorder,
                    strokeDashArray: 4,
                    yaxis: { lines: { show: true } }
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.35,
                        opacityTo: 0.05,
                        stops: [0, 100]
                    }
                },
                legend: { 
                    position: 'top', 
                    horizontalAlign: 'right',
                    labels: { colors: isM3 ? '#e1e3e1' : '#ededed' }
                }
            };

            if (document.querySelector("#chart-jamkot")) {
                window.chartArea = new ApexCharts(document.querySelector("#chart-jamkot"), optionsArea);
                window.chartArea.render().then(() => {
                    // Hapus skeleton loader dan tampilkan grafik (Doherty Threshold)
                    const skeleton = document.getElementById('chart-skeleton');
                    const chartDiv = document.getElementById('chart-jamkot');
                    if (skeleton) skeleton.style.display = 'none';
                    if (chartDiv) chartDiv.style.opacity = '1';
                });
            }
        }

        const currentSuhu = window.currentSuhu || 0;
        const currentKelembapan = window.currentKelembapan || 0;
        
        // Suhu Gauge (Current)
        if (document.querySelector("#gauge-suhu")) {
            var optionsGaugeSuhu = {
                series: [currentSuhu],
                chart: {
                    type: 'radialBar',
                    height: 360,
                    sparkline: { enabled: true }
                },
                colors: [isM3 ? '#80dec5' : '#10b981'],
                plotOptions: {
                    radialBar: {
                        startAngle: -90,
                        endAngle: 90,
                        hollow: { size: '75%' },
                        track: { background: gaugeTrackBg, strokeWidth: '100%' },
                        dataLabels: {
                            name: { show: false },
                            value: {
                                offsetY: -30,
                                fontSize: '40px',
                                fontWeight: 600,
                                color: gaugeLabelColor,
                                formatter: function (val) { return (Number(val).toFixed(1)) + "°C"; }
                            }
                        }
                    }
                },
                grid: { padding: { top: 0, bottom: -60, left: -20, right: -20 } },
                fill: {
                    type: 'gradient',
                    gradient: { 
                        shade: isM3 ? 'light' : 'dark', 
                        type: 'horizontal', 
                        gradientToColors: [isM3 ? '#ffb68f' : '#ef4444'], 
                        inverseColors: false, 
                        opacityFrom: 1, 
                        opacityTo: 1, 
                        stops: [0, 100] 
                    }
                },
                stroke: { lineCap: isM3 ? 'round' : 'square' }
            };
            window.chartSuhu = new ApexCharts(document.querySelector("#gauge-suhu"), optionsGaugeSuhu);
            window.chartSuhu.render();
        }

        // Kelembapan Gauge (Current)
        if (document.querySelector("#gauge-kelembapan")) {
            var optionsGaugeKelembapan = {
                series: [currentKelembapan],
                chart: {
                    type: 'radialBar',
                    height: 360,
                    sparkline: { enabled: true }
                },
                colors: [isM3 ? '#ffb68f' : '#ef4444'],
                plotOptions: {
                    radialBar: {
                        startAngle: -90,
                        endAngle: 90,
                        hollow: { size: '75%' },
                        track: { background: gaugeTrackBg, strokeWidth: '100%' },
                        dataLabels: {
                            name: { show: false },
                            value: {
                                offsetY: -30,
                                fontSize: '40px',
                                fontWeight: 600,
                                color: gaugeLabelColor,
                                formatter: function (val) { return (Number(val).toFixed(1)) + "%"; }
                            }
                        }
                    }
                },
                grid: { padding: { top: 0, bottom: -60, left: -20, right: -20 } },
                fill: {
                    type: 'gradient',
                    gradient: { 
                        shade: isM3 ? 'light' : 'dark', 
                        type: 'horizontal', 
                        gradientToColors: [isM3 ? '#80dec5' : '#3b82f6'], 
                        inverseColors: false, 
                        opacityFrom: 1, 
                        opacityTo: 1, 
                        stops: [0, 100] 
                    }
                },
                stroke: { lineCap: isM3 ? 'round' : 'square' }
            };
            window.chartKelembapan = new ApexCharts(document.querySelector("#gauge-kelembapan"), optionsGaugeKelembapan);
            window.chartKelembapan.render();
        }

        const avgSuhu = window.avgSuhu || 0;
        const avgKelembapan = window.avgKelembapan || 0;
        
        // Suhu Rata-rata (Avg) Gauge
        if (document.querySelector("#gauge-avg-suhu")) {
            var optionsAvgSuhu = {
                series: [avgSuhu],
                chart: {
                    type: 'radialBar',
                    height: 360,
                    sparkline: { enabled: true }
                },
                colors: [isM3 ? '#80dec5' : '#10b981'],
                plotOptions: {
                    radialBar: {
                        startAngle: -90,
                        endAngle: 90,
                        hollow: { size: '75%' },
                        track: { background: gaugeTrackBg, strokeWidth: '100%' },
                        dataLabels: {
                            name: { show: false },
                            value: { offsetY: -30, fontSize: '40px', fontWeight: 600, color: gaugeLabelColor, formatter: function (val) { return (Number(val).toFixed(1)) + "°C"; } }
                        }
                    }
                },
                grid: { padding: { top: 0, bottom: -60, left: -20, right: -20 } },
                fill: {
                    type: 'gradient',
                    gradient: { 
                        shade: isM3 ? 'light' : 'dark', 
                        type: 'horizontal', 
                        gradientToColors: [isM3 ? '#ffb68f' : '#ef4444'], 
                        inverseColors: false, 
                        opacityFrom: 1, 
                        opacityTo: 1, 
                        stops: [0, 100] 
                    }
                },
                stroke: { lineCap: 'round' }
            };
            window.chartAvgSuhu = new ApexCharts(document.querySelector("#gauge-avg-suhu"), optionsAvgSuhu);
            window.chartAvgSuhu.render();
        }

        // Kelembapan Rata-rata (Avg) Gauge
        if (document.querySelector("#gauge-avg-kelembapan")) {
            var optionsAvgKelembapan = {
                series: [avgKelembapan],
                chart: {
                    type: 'radialBar',
                    height: 360,
                    sparkline: { enabled: true }
                },
                colors: [isM3 ? '#ffb68f' : '#ef4444'],
                plotOptions: {
                    radialBar: {
                        startAngle: -90,
                        endAngle: 90,
                        hollow: { size: '75%' },
                        track: { background: gaugeTrackBg, strokeWidth: '100%' },
                        dataLabels: {
                            name: { show: false },
                            value: { offsetY: -30, fontSize: '40px', fontWeight: 600, color: gaugeLabelColor, formatter: function (val) { return (Number(val).toFixed(1)) + "%"; } }
                        }
                    }
                },
                grid: { padding: { top: 0, bottom: -60, left: -20, right: -20 } },
                fill: {
                    type: 'gradient',
                    gradient: { 
                        shade: isM3 ? 'light' : 'dark', 
                        type: 'horizontal', 
                        gradientToColors: [isM3 ? '#80dec5' : '#3b82f6'], 
                        inverseColors: false, 
                        opacityFrom: 1, 
                        opacityTo: 1, 
                        stops: [0, 100] 
                    }
                },
                stroke: { lineCap: 'round' }
            };
            window.chartAvgKelembapan = new ApexCharts(document.querySelector("#gauge-avg-kelembapan"), optionsAvgKelembapan);
            window.chartAvgKelembapan.render();
        }
    };

    // --- EXECUTE INITIAL PAINT ---
    window.renderAllCharts();

    // --- RE-PAINT CHARTS DYNAMICALLY ON THEME SWITCH EVENTS WITHOUT BROWSER RELOAD ---
    window.addEventListener('ui-theme-changed', function () {
        window.renderAllCharts();
    });

});
