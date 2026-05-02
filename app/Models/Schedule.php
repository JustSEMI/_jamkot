<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'pagi_mulai',
        'pagi_selesai',
        'siang_mulai',
        'siang_selesai',
        'sore_mulai',
        'sore_selesai',
        'batas_kelembapan',
    ];
}