// public/js/chart.js

document.addEventListener('DOMContentLoaded', function () {
    const rawData = window.dataJamkot;
    if (!rawData || rawData.length === 0) {
        if (document.querySelector("#chart-jamkot")) {
            document.querySelector("#chart-jamkot").innerHTML =
                "<div style='text-align: center; color: #6b7280; padding: 2rem 0; font-size: 0.875rem;'>Belum ada data sensor untuk menampilkan grafik.</div>";
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

        var optionsArea = {
            series: [
                { name: 'Kelembapan (%)', data: kelembapanSeries },
                { name: 'Suhu (°C)', data: suhuSeries }
            ],
            chart: {
                height: 300,
                type: 'area',
                toolbar: { show: false },
                background: 'transparent',
                fontFamily: 'Inter, sans-serif',
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
            theme: { mode: 'dark' },
            colors: [warnaUtama, '#ef4444'],
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 2 },
            xaxis: {
                categories: waktuLabels,
                labels: { style: { colors: '#6b7280' } },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                labels: { style: { colors: '#6b7280' } }
            },
            grid: {
                borderColor: '#1f1f1f',
                strokeDashArray: 4,
                yaxis: { lines: { show: true } }
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.3,
                    opacityTo: 0.05,
                    stops: [0, 100]
                }
            },
            legend: { position: 'top', horizontalAlign: 'right' }
        };

        if (document.querySelector("#chart-jamkot")) {
            var chartArea = new ApexCharts(document.querySelector("#chart-jamkot"), optionsArea);
            chartArea.render();
        }
    }

    const currentSuhu = window.currentSuhu || 0;
    const currentKelembapan = window.currentKelembapan || 0;
    if (document.querySelector("#gauge-suhu")) {
        var optionsGaugeSuhu = {
            series: [currentSuhu],
            chart: {
                type: 'radialBar',
                height: 360,
                sparkline: { enabled: true }
            },
            colors: ['#10b981'],
            plotOptions: {
                radialBar: {
                    startAngle: -90,
                    endAngle: 90,
                    hollow: { size: '75%' },
                    track: { background: '#262626', strokeWidth: '100%' },
                    dataLabels: {
                        name: { show: false },
                        value: {
                            offsetY: -30,
                            fontSize: '40px',
                            fontWeight: 600,
                            color: '#ededed',
                            formatter: function (val) { return (Number(val).toFixed(1)) + "°C"; }
                        }
                    }
                }
            },
            grid: { padding: { top: 0, bottom: -60, left: -20, right: -20 } },
            fill: {
                type: 'gradient',
                gradient: { shade: 'dark', type: 'horizontal', gradientToColors: ['#ef4444'], inverseColors: false, opacityFrom: 1, opacityTo: 1, stops: [0, 100] }
            },
            stroke: { lineCap: 'square' }
        };
        new ApexCharts(document.querySelector("#gauge-suhu"), optionsGaugeSuhu).render();
    }

    if (document.querySelector("#gauge-kelembapan")) {
        var optionsGaugeKelembapan = {
            series: [currentKelembapan],
            chart: {
                type: 'radialBar',
                height: 360,
                sparkline: { enabled: true }
            },
            colors: ['#ef4444'],
            plotOptions: {
                radialBar: {
                    startAngle: -90,
                    endAngle: 90,
                    hollow: { size: '75%' },
                    track: { background: '#262626', strokeWidth: '100%' },
                    dataLabels: {
                        name: { show: false },
                        value: {
                            offsetY: -30,
                            fontSize: '40px',
                            fontWeight: 600,
                            color: '#ededed',
                            formatter: function (val) { return (Number(val).toFixed(1)) + "%"; }
                        }
                    }
                }
            },
            grid: { padding: { top: 0, bottom: -60, left: -20, right: -20 } },
            fill: {
                type: 'gradient',
                gradient: { shade: 'dark', type: 'horizontal', gradientToColors: ['#3b82f6'], inverseColors: false, opacityFrom: 1, opacityTo: 1, stops: [0, 100] }
            },
            stroke: { lineCap: 'square' }
        };
        new ApexCharts(document.querySelector("#gauge-kelembapan"), optionsGaugeKelembapan).render();
    }

    const avgSuhu = window.avgSuhu || 0;
    const avgKelembapan = window.avgKelembapan || 0;
    if (document.querySelector("#gauge-avg-suhu")) {
        var optionsAvgSuhu = {
            series: [avgSuhu],
            chart: {
                type: 'radialBar',
                height: 360,
                sparkline: { enabled: true }
            },
            colors: ['#10b981'],
            plotOptions: {
                radialBar: {
                    startAngle: -90,
                    endAngle: 90,
                    hollow: { size: '75%' },
                    track: { background: '#262626', strokeWidth: '100%' },
                    dataLabels: {
                        name: { show: false },
                        value: { offsetY: -30, fontSize: '40px', fontWeight: 600, color: '#ededed', formatter: function (val) { return (Number(val).toFixed(1)) + "°C"; } }
                    }
                }
            },
            grid: { padding: { top: 0, bottom: -60, left: -20, right: -20 } },
            fill: {
                type: 'gradient',
                gradient: { shade: 'dark', type: 'horizontal', gradientToColors: ['#ef4444'], inverseColors: false, opacityFrom: 1, opacityTo: 1, stops: [0, 100] }
            },
            stroke: { lineCap: 'round' }
        };
        new ApexCharts(document.querySelector("#gauge-avg-suhu"), optionsAvgSuhu).render();
    }

    if (document.querySelector("#gauge-avg-kelembapan")) {
        var optionsAvgKelembapan = {
            series: [avgKelembapan],
            chart: {
                type: 'radialBar',
                height: 360,
                sparkline: { enabled: true }
            },
            colors: ['#ef4444'],
            plotOptions: {
                radialBar: {
                    startAngle: -90,
                    endAngle: 90,
                    hollow: { size: '75%' },
                    track: { background: '#262626', strokeWidth: '100%' },
                    dataLabels: {
                        name: { show: false },
                        value: { offsetY: -30, fontSize: '40px', fontWeight: 600, color: '#ededed', formatter: function (val) { return (Number(val).toFixed(1)) + "%"; } }
                    }
                }
            },
            grid: { padding: { top: 0, bottom: -60, left: -20, right: -20 } },
            fill: {
                type: 'gradient',
                gradient: { shade: 'dark', type: 'horizontal', gradientToColors: ['#3b82f6'], inverseColors: false, opacityFrom: 1, opacityTo: 1, stops: [0, 100] }
            },
            stroke: { lineCap: 'round' }
        };
        new ApexCharts(document.querySelector("#gauge-avg-kelembapan"), optionsAvgKelembapan).render();
    }

});
