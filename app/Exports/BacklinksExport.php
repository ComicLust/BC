<?php

namespace App\Exports;

use App\Models\Backlink;
use App\Models\Project;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class BacklinksExport implements FromQuery, WithHeadings, WithMapping
{
    protected $project;
    protected $filter;

    public function __construct(Project $project, $filter = 'all')
    {
        $this->project = $project;
        $this->filter = $filter;
    }

    public function query()
    {
        $query = $this->project->backlinks();

        if ($this->filter === 'active') {
            $query->where('status', 'active');
        } elseif ($this->filter === 'broken') {
            $query->where('status', 'broken');
        }

        return $query->latest();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Kaynak URL (Proje)',
            'Hedef URL (Backlink)',
            'Durum',
            'Son Kontrol Tarihi',
            'Bulunan URL',
            'Anchor Text',
            'Hata Nedeni',
        ];
    }

    public function map($backlink): array
    {
        $details = $backlink->details ? json_decode($backlink->details, true) : [];

        return [
            $backlink->id,
            $backlink->source_url,
            $backlink->target_url,
            $backlink->status === 'active' ? 'Aktif' : ($backlink->status === 'broken' ? 'Kırık' : 'Bekliyor'),
            $backlink->last_checked_at ? $backlink->last_checked_at->format('d.m.Y H:i') : '-',
            $details['found_url'] ?? '-',
            $details['anchor_text'] ?? '-',
            $details['error_reason'] ?? '-',
        ];
    }
}
