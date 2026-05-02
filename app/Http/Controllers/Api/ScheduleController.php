<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Schedule;

class ScheduleController extends Controller
{
    public function getSchedule(Request $request)
    {
        $jadwal = Schedule::first();

        if (!$jadwal) {
            return response()->json([
                'status' => 'KOSONG',
                'pesan'  => 'Belum ada konfigurasi jadwal di database.',
                'data'   => null
            ]);
        }

        return response()->json([
            'status' => 'SUCCESS',
            'pesan'  => 'Jadwal penyiraman berhasil diambil.',
            'data'   => [
                'pagi_mulai'       => $jadwal->pagi_mulai,
                'pagi_selesai'     => $jadwal->pagi_selesai,
                'siang_mulai'      => $jadwal->siang_mulai,
                'siang_selesai'    => $jadwal->siang_selesai,
                'sore_mulai'       => $jadwal->sore_mulai,
                'sore_selesai'     => $jadwal->sore_selesai,
                'batas_kelembapan' => $jadwal->batas_kelembapan,
            ]
        ]);
    }
}