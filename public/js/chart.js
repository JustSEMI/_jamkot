// public/js/chart.js

document.addEventListener('DOMContentLoaded', function () {
    const rawData = window.dataJamkot;

    if (!rawData || rawData.length === 0) {
        document.querySelector("#chart-jamkot").innerHTML =
            "<div style='text-align: center; color: #6b7280; padding: 2rem 0; font-size: 0.875rem;'>Belum ada data sensor untuk menampilkan grafik.</div>";
        return;
    }

    const chartData = rawData; 

    const waktuLabels = chartData.map(item => {
        let date = new Date(item.created_at);
        return date.getHours().toString().padStart(2, '0') + ':' + date.getMinutes().toString().padStart(2, '0');
    });

    const suhuSeries = chartData.map(item => item.suhu);
    const kelembapanSeries = chartData.map(item => item.kelembapan);

    // Biar warna tema lu sinkron (ambil dari CSS variable)
    const warnaUtama = getComputedStyle(document.documentElement).getPropertyValue('--warna-utama').trim() || '#10b981';

    var options = {
        series: [
            { name: 'Kelembapan (%)', data: kelembapanSeries },
            { name: 'Suhu (°C)', data: suhuSeries }
        ],
        chart: {
            height: 300,
            type: 'area',
            toolbar: { show: false },
            background: 'transparent',
            fontFamily: 'Inter, sans-serif'
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

    var chart = new ApexCharts(document.querySelector("#chart-jamkot"), options);
    chart.render();

});