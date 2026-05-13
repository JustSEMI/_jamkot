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
<body onload="window.print()">
    <div class="no-print" style="background: #fff3cd; padding: 15px; margin-bottom: 20px; border: 1px solid #ffeeba; border-radius: 4px; text-align: center;">
        Kotak dialog cetak akan muncul otomatis. Pilih <strong>"Simpan sebagai PDF"</strong> pada tujuan pencetakan.
        <br>
        <button onclick="window.close()" style="margin-top: 10px; padding: 5px 15px; cursor: pointer;">Tutup Halaman</button>
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
