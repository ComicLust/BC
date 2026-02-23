<?php

namespace App\Http\Controllers;

use App\Models\EmailSetting;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Database\Seeders\EmailTemplateSeeder;

class EmailSettingController extends Controller
{
    public function index()
    {
        $settings = EmailSetting::getSettings();
        
        // Eğer şablon yoksa oluştur
        if (EmailTemplate::count() === 0) {
            $seeder = new EmailTemplateSeeder();
            $seeder->run();
        }
        
        $templates = EmailTemplate::all();
        return view('settings.email', compact('settings', 'templates'));
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'mail_mailer' => 'required|string',
            'mail_host' => 'required|string',
            'mail_port' => 'required|string',
            'mail_username' => 'required|string',
            'mail_password' => 'required|string',
            'mail_encryption' => 'required|string',
            'mail_from_address' => 'required|email',
            'mail_from_name' => 'required|string',
        ]);

        $settings = EmailSetting::getSettings();
        $settings->update($validated);

        // Config dosyasını güncelle
        Config::set('mail.mailers.smtp.host', $validated['mail_host']);
        Config::set('mail.mailers.smtp.port', $validated['mail_port']);
        Config::set('mail.mailers.smtp.username', $validated['mail_username']);
        Config::set('mail.mailers.smtp.password', $validated['mail_password']);
        Config::set('mail.mailers.smtp.encryption', $validated['mail_encryption']);
        Config::set('mail.from.address', $validated['mail_from_address']);
        Config::set('mail.from.name', $validated['mail_from_name']);

        return redirect()->back()->with('success', 'E-posta ayarları başarıyla güncellendi.');
    }

    public function updateTemplate(Request $request, EmailTemplate $template)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'subject' => 'required|string',
            'body' => 'required|string',
        ]);

        $template->update($validated);

        return redirect()->back()->with('success', 'E-posta şablonu başarıyla güncellendi.');
    }

    public function testEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        try {
            $template = EmailTemplate::getTemplate('backlink_status');
            $data = [
                'project_name' => 'Test Projesi',
                'backlink_url' => 'https://example.com',
                'status' => 'Aktif',
                'details' => 'Test detayları',
                'app_name' => config('app.name'),
            ];

            \Mail::to($request->email)->send(new \App\Mail\TestEmail($template, $data));
            return redirect()->back()->with('success', 'Test e-postası başarıyla gönderildi.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Test e-postası gönderilemedi: ' . $e->getMessage());
        }
    }
}
