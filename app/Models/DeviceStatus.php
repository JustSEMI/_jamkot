<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'device_id',
    'ip_address',
    'uptime_seconds',
    'dht_connected',
    'ldr_connected',
    'free_heap',
    'rssi',
    'esp_temp',
    'last_seen_at',
])]
class DeviceStatus extends Model
{
    protected $table = 'device_status';

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'dht_connected' => 'boolean',
        'ldr_connected' => 'boolean',
        'last_seen_at' => 'datetime',
        'uptime_seconds' => 'integer',
        'free_heap' => 'integer',
        'rssi' => 'integer',
        'esp_temp' => 'float',
    ];

    /**
     * Threshold (detik) tanpa heartbeat sebelum device dianggap offline.
     */
    const OFFLINE_THRESHOLD_SECONDS = 180;

    /**
     * Apakah perangkat sedang online (heartbeat < 3 menit yang lalu).
     */
    public function isOnline(): bool
    {
        return $this->last_seen_at
            && $this->last_seen_at->diffInSeconds(now()) < self::OFFLINE_THRESHOLD_SECONDS;
    }

    /**
     * Format uptime dari detik ke string "Xj Ym Zd".
     */
    public function formattedUptime(): string
    {
        $seconds = $this->uptime_seconds ?? 0;

        $days = intdiv($seconds, 86400);
        $hours = intdiv($seconds % 86400, 3600);
        $minutes = intdiv($seconds % 3600, 60);
        $secs = $seconds % 60;

        if ($days > 0) {
            return "{$days}h {$hours}j {$minutes}m";
        }

        if ($hours > 0) {
            return "{$hours}j {$minutes}m {$secs}d";
        }

        return "{$minutes}m {$secs}d";
    }

    /**
     * Free heap dalam kilobyte.
     */
    public function freeHeapKb(): string
    {
        return $this->free_heap ? round($this->free_heap / 1024, 1).' KB' : '--';
    }
}
