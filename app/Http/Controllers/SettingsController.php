<?php

namespace App\Http\Controllers;

use App\Models\SensorLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingsController extends Controller
{
    /**
     * Display settings page.
     */
    public function index(): View
    {
        return view('settings.index');
    }

    /**
     * Reset/Truncate sensor logs.
     */
    public function resetData(): RedirectResponse
    {
        SensorLog::truncate();

        return back()->with('sukses', 'DATA BERHASIL DIHAPUS!');
    }
}
