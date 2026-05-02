<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SensorLog;

class SettingsController extends Controller
{
    // Fungsi buat nampilin halaman Setting
    public function index()
    {
        return view('settings');
    }

    public function resetData()
    {
        SensorLog::truncate(); 
        return back()->with('sukseshapus', 'DATA BERHASIL DIHAPUS!');
    }
}