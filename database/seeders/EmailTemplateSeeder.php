<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EmailTemplate;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Backlink Durum Değişikliği',
                'subject' => '[{app_name}] Backlink Durumu Değişti: {backlink_url}',
                'body' => "Merhaba {user_name},\n\n{project_name} projesindeki {backlink_url} backlink'inin durumu {status} olarak değişti.\n\nDetaylar:\n{details}\n\nBacklink Kontrol Paneli: {dashboard_url}\n\nSaygılarımızla,\n{app_name}",
                'type' => 'backlink_status'
            ],
            [
                'name' => 'Toplu Kontrol Tamamlandı',
                'subject' => '[{app_name}] Toplu Backlink Kontrolü Tamamlandı',
                'body' => "Merhaba {user_name},\n\n{project_name} projesi için toplu backlink kontrolü tamamlandı.\n\nSonuçlar:\n- Toplam Kontrol: {total_checked}\n- Aktif: {active_count}\n- Kırık: {broken_count}\n- Beklemede: {pending_count}\n\nDetaylı rapor için kontrol paneline göz atabilirsiniz: {dashboard_url}\n\nSaygılarımızla,\n{app_name}",
                'type' => 'bulk_check_completed'
            ],
            [
                'name' => 'Proje Oluşturuldu',
                'subject' => '[{app_name}] Yeni Proje Oluşturuldu: {project_name}',
                'body' => "Merhaba {user_name},\n\n{project_name} projesi başarıyla oluşturuldu.\n\nProje Detayları:\n- Hedef URL: {target_url}\n- Oluşturulma Tarihi: {created_at}\n\nProjeyi yönetmek için kontrol paneline göz atabilirsiniz: {dashboard_url}\n\nSaygılarımızla,\n{app_name}",
                'type' => 'project_created'
            ]
        ];

        foreach ($templates as $template) {
            EmailTemplate::updateOrCreate(
                ['type' => $template['type']],
                $template
            );
        }
    }
}
