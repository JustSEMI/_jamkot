<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Sensor JAMKOT - {{ date('d/m/Y H:i') }}</title>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            color: #333;
            padding: 40px;
            margin: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 40px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            letter-spacing: 2px;
        }
        .header p {
            margin: 5px 0 0;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
            font-size: 12px;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            text-transform: uppercase;
        }
        tr:nth-child(even) {
            background-color: #fcfcfc;
        }
        .footer {
            margin-top: 50px;
            text-align: right;
            font-size: 12px;
            color: #777;
        }
        @media print {
            .no-print {
                display: none;
            }
            body {
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="no-print" style="background: rgba(16, 185, 129, 0.1); padding: 1.5rem; margin-bottom: 2rem; border: 1px solid rgba(16, 185, 129, 0.2); border-radius: 12px; text-align: center; font-family: 'Inter', sans-serif;">
        <p style="margin: 0 0 1rem 0; color: #10b981; font-weight: 500;">Pratinjau Laporan Berhasil Dibuat</p>
        <div style="display: flex; gap: 10px; justify-content: center;">
            <button onclick="window.print()" style="background: #10b981; color: #fff; border: none; padding: 8px 20px; border-radius: 6px; cursor: pointer; font-weight: 600; transition: opacity 0.2s;">
                <i class="fa-solid fa-print"></i> Cetak Laporan / Simpan PDF
            </button>
            <button onclick="window.close()" style="background: rgba(255,255,255,0.1); color: #666; border: 1px solid #ddd; padding: 8px 20px; border-radius: 6px; cursor: pointer; font-weight: 600;">
                Tutup
            </button>
        </div>
    </div>

    <div class="header">
        <h1>LAPORAN DATA SENSOR JAMKOT</h1>
        <p>Dicetak pada: {{ date('d F Y, H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Waktu Catat</th>
                <th>Device ID</th>
                <th>Suhu</th>
                <th>Kelembapan</th>
                <th>Status Pompa</th>
            </tr>
        </thead>
        <tbody>
            @foreach($logs as $index => $log)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                    <td>{{ $log->sensor_id }}</td>
                    <td>{{ $log->suhu }}°C</td>
                    <td>{{ $log->kelembapan }}%</td>
                    <td>{{ $log->pompa_status }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Sistem Pemantauan JAMKOT &copy; {{ date('Y') }}</p>
    </div>
</body>
</html>
