<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSensorLogRequest;
use App\Http\Resources\SensorLogResource;
use App\Models\SensorLog;
use Illuminate\Http\JsonResponse;

class SensorController extends Controller
{
    /**
     * Get recent sensor logs.
     */
    public function index(): JsonResponse
    {
        $dataSensor = SensorLog::orderBy('created_at', 'desc')->limit(50)->get();

        return response()->json([
            'status' => 'SUCCESS',
            'pesan' => 'DATA SENSOR BERHASIL DIAMBIL',
            'jumlah_data_ditampilkan' => $dataSensor->count(),
            'data' => SensorLogResource::collection($dataSensor),
        ]);
    }

    /**
     * Store a new sensor log.
     */
    public function store(StoreSensorLogRequest $request): JsonResponse
    {
        SensorLog::create([
            'sensor_id' => $request->sensor_id,
            'suhu' => $request->suhu,
            'kelembapan' => $request->kelembapan,
            'cahaya' => $request->cahaya,
            'pompa_status' => $request->pompa_status ?? 'OFF',
        ]);

        return response()->json([
            'status' => 'SUCCESS',
            'message' => 'OK',
        ], 201);
    }
}
