<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['sensor_id', 'suhu', 'kelembapan', 'cahaya', 'pompa_status'])]
class SensorLog extends Model
{
    protected $table = 'sensorlogs';
}