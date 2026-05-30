<?php

use App\Http\Controllers\Api\DeviceController;
use App\Http\Controllers\Api\ScheduleController;
use App\Http\Controllers\Api\SensorController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/sensor/data', [SensorController::class, 'index']);
Route::post('/sensor/data', [SensorController::class, 'store']);
Route::get('/schedule', [ScheduleController::class, 'getSchedule']);
Route::get('/pump/status', [ScheduleController::class, 'getPumpStatus']);
Route::post('/device/status', [DeviceController::class, 'heartbeat']);
