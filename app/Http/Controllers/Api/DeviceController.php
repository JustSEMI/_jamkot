<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeviceStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    /**
     * Menerima heartbeat status dari perangkat ESP32.
     *
     * Payload JSON yang diharapkan dari ESP32:
     * {
     *   "device_id":      "ESP32-JAMKOT",   // wajib
     *   "ip_address":     "192.168.1.100",  // opsional
     *   "uptime_seconds": 3600,             // wajib, detik sejak boot
     *   "dht_connected":  true,             // wajib
     *   "ldr_connected":  true,             // wajib
     *   "free_heap":      180000,           // opsional, bytes
     *   "rssi":           -65               // opsional, dBm
     * }
     */
    public function heartbeat(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'device_id' => ['required', 'string', 'max:64'],
            'ip_address' => ['nullable', 'string', 'max:45'],
            'uptime_seconds' => ['required', 'integer', 'min:0'],
            'dht_connected' => ['required', 'boolean'],
            'ldr_connected' => ['required', 'boolean'],
            'free_heap' => ['nullable', 'integer', 'min:0'],
            'rssi' => ['nullable', 'integer', 'min:-120', 'max:0'],
            'esp_temp' => ['nullable', 'numeric', 'min:-50', 'max:150'],
        ]);

        DeviceStatus::updateOrCreate(
            ['device_id' => $validated['device_id']],
            array_merge($validated, ['last_seen_at' => now()])
        );

        return response()->json(['status' => 'SUCCESS', 'message' => 'OK'], 200);
    }
}
