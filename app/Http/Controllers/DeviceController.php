<?php

namespace App\Http\Controllers;

use App\Models\DeviceStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class DeviceController extends Controller
{
    /**
     * Display the device status monitoring page.
     */
    public function index(): View
    {
        $device = DeviceStatus::latest('last_seen_at')->first();

        return view('device.index', compact('device'));
    }

    /**
     * Return current device status as JSON (polled by the page every 15s).
     */
    public function status(): JsonResponse
    {
        $device = DeviceStatus::latest('last_seen_at')->first();

        if (! $device) {
            return response()->json([
                'found' => false,
                'is_online' => false,
            ]);
        }

        $isOnline = $device->isOnline();

        return response()->json([
            'found' => true,
            'device_id' => $device->device_id,
            'ip_address' => $device->ip_address,
            'is_online' => $isOnline,
            'status_label' => $isOnline ? 'Online' : 'Offline',
            'uptime_formatted' => $device->formattedUptime(),
            'uptime_seconds' => $device->uptime_seconds,
            'dht_connected' => $device->dht_connected,
            'ldr_connected' => $device->ldr_connected,
            'free_heap_kb' => $device->freeHeapKb(),
            'free_heap' => $device->free_heap,
            'rssi' => $device->rssi,
            'esp_temp' => $device->esp_temp,
            'esp_temp_formatted' => $device->esp_temp !== null ? number_format($device->esp_temp, 1).'°C' : '—',
            'last_seen_at' => $device->last_seen_at?->diffForHumans(),
            'last_seen_full' => $device->last_seen_at?->format('d M Y, H:i:s'),
        ])->header('Cache-Control', 'no-cache, no-store, must-revalidate');
    }
}
