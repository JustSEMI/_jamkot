<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ScheduleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'pagi_mulai' => $this->pagi_mulai,
            'pagi_selesai' => $this->pagi_selesai,
            'siang_mulai' => $this->siang_mulai,
            'siang_selesai' => $this->siang_selesai,
            'sore_mulai' => $this->sore_mulai,
            'sore_selesai' => $this->sore_selesai,
            'batas_kelembapan' => (float) $this->batas_kelembapan,
        ];
    }
}
