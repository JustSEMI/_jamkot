<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SensorLog;

class SensorController extends Controller
{
    public function data(Request $request)
    {
        // GET DATA SENSOR
        if ($request->isMethod('get')) {
            $dataSensor = SensorLog::orderBy('created_at', 'desc')->limit(50)->get();

            return response()->json([
                'status' => 'SUCCESS',
                'pesan'  => 'DATA SENSOR BERHASIL DIAMBIL',
                'jumlah_data_ditampilkan' => $dataSensor->count(),
                'data'   => $dataSensor
            ]);
        }

        // POST DATA SENSOR
        $request->validate([
            'sensor_id'    => 'required|string',
            'suhu'         => 'required|numeric',
            'kelembapan'   => 'required|numeric',
            'cahaya'       => 'required|numeric',
            'pompa_status' => 'nullable|in:ON,OFF'
        ]);

        // SIMPAN KE DATABASE
        $log = SensorLog::create([
            'sensor_id'    => $request->sensor_id,
            'suhu'         => $request->suhu,
            'kelembapan'   => $request->kelembapan,
            'cahaya'       => $request->cahaya,
            'pompa_status' => $request->pompa_status ?? 'OFF',
        ]);
        
        // RESPONSE SUKSES
        return response()->json([
            'status'  => 'SUCCESS',
            'message' => 'DATA SENSOR BERHASIL DISIMPAN',
            'data'    => $log
        ], 201);
    }
}