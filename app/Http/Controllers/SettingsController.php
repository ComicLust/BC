<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function updateSchedule(Request $request)
    {
        $request->validate([
            'backlink_check_frequency' => 'required|in:daily,weekly,monthly'
        ]);
        \App\Models\Setting::set('backlink_check_frequency', $request->backlink_check_frequency);
        return back()->with('success', 'Ayar kaydedildi.');
    }
} 