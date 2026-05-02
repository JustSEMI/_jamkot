<?php

namespace App\Http\Controllers;

use App\Models\SensorLog;
use Illuminate\Http\Request;

class PanelController extends Controller
{
    public function index()
    {

        $latest = SensorLog::latest()->first();

        $riwayat = SensorLog::latest()->take(5)->get();

        $targetKelembapan = 85;
        $kelembapanSekarang = $latest->kelembapan ?? 0;
        
        $persentaseTarget = ($kelembapanSekarang / $targetKelembapan) * 100;
        if ($persentaseTarget > 100) $persentaseTarget = 100;

        //$refreshInterval = \App\Models\Setting::where('key', 'refresh_interval')->value('value') ?? 30;

        return view('panel', [
            'latest' => $latest,
            'riwayat' => $riwayat,
            'persentaseTarget' => $persentaseTarget,
            'targetKelembapan' => $targetKelembapan
            //'refreshInterval' => $refreshInterval
        ]);
    }
}