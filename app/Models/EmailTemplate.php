<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    protected $fillable = [
        'name',
        'subject',
        'body',
        'type'
    ];

    public static function getTemplate($type)
    {
        $template = self::where('type', $type)->first();
        if (!$template) {
            $template = self::create([
                'name' => 'Backlink Durum Değişikliği',
                'subject' => 'Backlink Durumu Değişti',
                'body' => "Merhaba,\n\n{project_name} projesindeki {backlink_url} backlink'inin durumu {status} olarak değişti.\n\nDetaylar:\n{details}\n\nSaygılarımızla,\n{app_name}",
                'type' => $type
            ]);
        }
        return $template;
    }

    public function parseTemplate($data)
    {
        $body = $this->body;
        foreach ($data as $key => $value) {
            $body = str_replace('{' . $key . '}', $value, $body);
        }
        return $body;
    }
}
