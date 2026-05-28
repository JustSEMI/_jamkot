<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['sensor_id', 'suhu', 'kelembapan', 'cahaya', 'pompa_status'])]
class SensorLog extends Model
{
    protected $table = 'sensorlogs';
}
