<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SensorLog;

class SensorController extends Controller
{
    public function store(Request $request)
    {
        // VALIDASI DATA MASUK
        $request->validate([
            'sensor_id'    => 'required|string',
            'suhu'         => 'required|numeric',
            'kelembapan'   => 'required|numeric',
            'cahaya'       => 'required|numeric',
            'pompa_status' => 'nullable|in:ON,OFF'
        ]);

        // DATABASE
        $log = SensorLog::create([
            'sensor_id'    => $request->sensor_id,
            'suhu'         => $request->suhu,
            'kelembapan'   => $request->kelembapan,
            'cahaya'       => $request->cahaya,
            'pompa_status' => $request->pompa_status ?? 'OFF',
        ]);

        // RESPONSE SUKSES
        return response()->json([
            'status'  => 'success',
            'message' => 'Data JAMKOT berhasil mendarat di server',
            'data'    => $log
        ], 201);
    }
}