<?php

namespace App\Http\Controllers;

use App\Models\SensorLog;
use Illuminate\Http\Request;

class PanelController extends Controller
{
    public function index()
    {
        $latest = SensorLog::latest()->first();
        $riwayatTabel = SensorLog::latest()->take(5)->get();
        $riwayatGrafik = SensorLog::latest()->take(20)->get()->reverse()->values();

        $targetKelembapan = 85;
        $kelembapanSekarang = $latest->kelembapan ?? 0;

        $persentaseTarget = ($kelembapanSekarang / $targetKelembapan) * 100;
        if ($persentaseTarget > 100) {
            $persentaseTarget = 100;
        }

        return view('panel', [
            'latest' => $latest,
            'riwayatTabel' => $riwayatTabel,
            'riwayatGrafik' => $riwayatGrafik,
            'persentaseTarget' => $persentaseTarget,
            'targetKelembapan' => $targetKelembapan
        ]);
    }
    public function analisis()
    {
        $stats = [
            'total_data' => SensorLog::count(),
            'avg_suhu' => SensorLog::avg('suhu'),
            'avg_kelembapan' => SensorLog::avg('kelembapan'),
            'max_suhu' => SensorLog::max('suhu'),
            'min_suhu' => SensorLog::min('suhu'),
            'max_kelembapan' => SensorLog::max('kelembapan'),
            'min_kelembapan' => SensorLog::min('kelembapan'),
        ];

        return view('analisis', compact('stats'));
    }
    public function exportCsv()
    {
        $fileName = 'Laporan_Sensor_JAMKOT_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');

            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputs($file, "sep=,\n");

            fputcsv($file, ['ID', 'WAKTU CATAT', 'SENSOR ID', 'SUHU (°C)', 'KELEMBAPAN (%)', 'STATUS POMPA']);

            \App\Models\SensorLog::orderBy('created_at', 'desc')->chunk(500, function ($logs) use ($file) {
                foreach ($logs as $log) {
                    fputcsv($file, [
                        $log->id,
                        $log->created_at->format('Y-m-d H:i:s'),
                        $log->sensor_id,
                        $log->suhu,
                        $log->kelembapan,
                        $log->pompa_status
                    ]);
                }
            });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}