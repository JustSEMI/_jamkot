<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\SensorLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PanelController extends Controller
{
    /**
     * Display the main panel dashboard.
     */
    public function index(): View
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

        return view('panel.index', [
            'latest' => $latest,
            'riwayatTabel' => $riwayatTabel,
            'riwayatGrafik' => $riwayatGrafik,
            'persentaseTarget' => $persentaseTarget,
            'targetKelembapan' => $targetKelembapan,
        ]);
    }

    /**
     * Display light sensor (LDR) page.
     */
    public function ldr(): View
    {
        $latest = SensorLog::latest()->first();
        $riwayatTabel = SensorLog::latest()->take(5)->get();
        $riwayatGrafik = SensorLog::latest()->take(20)->get()->reverse()->values();

        return view('sensor.ldr', [
            'latest' => $latest,
            'riwayatTabel' => $riwayatTabel,
            'riwayatGrafik' => $riwayatGrafik,
        ]);
    }

    /**
     * Display DHT22 sensor (temperature & humidity) page.
     */
    public function dht22(): View
    {
        $latest = SensorLog::latest()->first();
        $riwayatTabel = SensorLog::latest()->take(5)->get();
        $riwayatGrafik = SensorLog::latest()->take(20)->get()->reverse()->values();

        $targetKelembapan = 85;

        return view('sensor.dht22', [
            'latest' => $latest,
            'riwayatTabel' => $riwayatTabel,
            'riwayatGrafik' => $riwayatGrafik,
            'targetKelembapan' => $targetKelembapan,
        ]);
    }

    /**
     * Fetch realtime sensor data.
     */
    public function realtimeData(): JsonResponse
    {
        $allRecent = SensorLog::latest()->take(20)->get();

        $latest = $allRecent->first();
        $riwayatGrafik = $allRecent->reverse()->values();
        $riwayatTabelRaw = $allRecent->take(5);

        $riwayatTabel = $riwayatTabelRaw->map(function ($log) {
            return [
                'time_diff' => $log->created_at->diffForHumans(),
                'sensor_id' => $log->sensor_id,
                'pompa_status' => $log->pompa_status,
                'kelembapan' => $log->kelembapan,
                'suhu' => $log->suhu,
                'cahaya' => $log->cahaya,
            ];
        });

        $jadwal = Schedule::first();
        $manualPumpStatus = $jadwal ? $jadwal->manual_pump_status : 'OFF';

        return response()->json([
            'latest' => [
                'cahaya' => $latest->cahaya ?? '--',
                'suhu' => number_format($latest->suhu ?? 0, 1),
                'kelembapan' => number_format($latest->kelembapan ?? 0, 1),
                'suhu_raw' => $latest->suhu ?? 0,
                'kelembapan_raw' => $latest->kelembapan ?? 0,
                'is_online' => (bool) $latest,
                'pompa_status' => $latest->pompa_status ?? 'OFF',
            ],
            'riwayatTabel' => $riwayatTabel,
            'riwayatGrafik' => $riwayatGrafik,
            'targetKelembapan' => 85,
            'manual_pump_status' => $manualPumpStatus,
        ])->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    /**
     * Toggle manual pump status.
     */
    public function togglePump(Request $request): JsonResponse
    {
        $jadwal = Schedule::first();
        if (! $jadwal) {
            $jadwal = new Schedule;
            $jadwal->save();
        }

        $request->validate([
            'status' => 'required|in:ON,OFF',
        ]);

        $jadwal->manual_pump_status = $request->status;
        $jadwal->save();

        return response()->json(['status' => 'success', 'pump_status' => $jadwal->manual_pump_status]);
    }

    /**
     * Display sensor log analysis page.
     */
    public function analisis(Request $request): View
    {
        $date = $request->get('date');
        $limit = $request->get('limit', 10);

        $stats = SensorLog::selectRaw('
            COUNT(*) as total_data,
            AVG(suhu) as avg_suhu,
            AVG(kelembapan) as avg_kelembapan,
            MAX(suhu) as max_suhu,
            MIN(suhu) as min_suhu,
            MAX(kelembapan) as max_kelembapan,
            MIN(kelembapan) as min_kelembapan
        ')->first()->toArray();

        $query = SensorLog::query();
        if ($date) {
            $query->whereDate('created_at', $date);
        }
        $logs = $query->latest()->take($limit)->get();

        return view('analisis.index', compact('stats', 'logs', 'date', 'limit'));
    }

    /**
     * Export sensor logs to CSV format.
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $date = $request->get('date');
        $fileName = 'Laporan_Sensor_JAMKOT_'.($date ? $date : date('Y-m-d')).'.csv';

        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($date) {
            $file = fopen('php://output', 'w');

            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fwrite($file, "sep=,\n");

            fputcsv($file, ['ID', 'WAKTU CATAT', 'SENSOR ID', 'SUHU (°C)', 'KELEMBAPAN (%)', 'STATUS POMPA']);

            $query = SensorLog::query();
            if ($date) {
                $query->whereDate('created_at', $date);
            }

            $query->orderBy('created_at', 'desc')->chunk(500, function ($logs) use ($file) {
                foreach ($logs as $log) {
                    fputcsv($file, [
                        $log->id,
                        $log->created_at->format('Y-m-d H:i:s'),
                        $log->sensor_id,
                        $log->suhu,
                        $log->kelembapan,
                        $log->pompa_status,
                    ]);
                }
            });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export sensor logs to PDF printable view.
     */
    public function exportPdf(Request $request): View
    {
        $date = $request->get('date');
        $query = SensorLog::query();

        if ($date) {
            $query->whereDate('created_at', $date);
        }

        $logs = $query->latest()->limit(500)->get();

        return view('exports.pdf', compact('logs'));
    }

    /**
     * Display 3D view page.
     */
    public function view3d(): View
    {
        return view('view3d.index');
    }

    /**
     * Display flowchart page.
     */
    public function flowchart(): View
    {
        return view('flowchart');
    }
}
