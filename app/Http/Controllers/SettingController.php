<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class SettingController extends Controller
{
    public function index()
    {
        return view('settings.index');
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        return redirect()->route('settings.index')
            ->with('success', 'Profil bilgileri güncellendi.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        Auth::user()->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('settings.index')
            ->with('success', 'Şifre güncellendi.');
    }

    public function updateNotifications(Request $request)
    {
        $request->validate([
            'email_notifications' => ['boolean'],
            'backlink_status_notifications' => ['boolean'],
            'metric_change_notifications' => ['boolean'],
        ]);

        Auth::user()->update([
            'email_notifications' => $request->boolean('email_notifications'),
            'backlink_status_notifications' => $request->boolean('backlink_status_notifications'),
            'metric_change_notifications' => $request->boolean('metric_change_notifications'),
        ]);

        return redirect()->route('settings.index')
            ->with('success', 'Bildirim ayarları güncellendi.');
    }
}
