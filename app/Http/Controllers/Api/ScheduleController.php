<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ScheduleResource;
use App\Models\Schedule;
use Illuminate\Http\JsonResponse;

class ScheduleController extends Controller
{
    /**
     * Get watering schedule.
     */
    public function getSchedule(): JsonResponse
    {
        $jadwal = Schedule::first();

        if (! $jadwal) {
            return response()->json([
                'status' => 'KOSONG',
                'pesan' => 'Belum ada konfigurasi jadwal di database.',
                'data' => null,
            ]);
        }

        return response()->json([
            'status' => 'SUCCESS',
            'pesan' => 'Jadwal penyiraman berhasil diambil.',
            'data' => new ScheduleResource($jadwal),
        ]);
    }

    /**
     * Get current pump status.
     */
    public function getPumpStatus(): JsonResponse
    {
        $jadwal = Schedule::first();

        if (! $jadwal) {
            return response()->json(['status' => 'AUTO']);
        }

        return response()->json([
            'status' => $jadwal->manual_pump_status ?? 'AUTO',
        ]);
    }
}
