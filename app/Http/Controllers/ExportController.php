<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ExportController extends Controller
{
    public function exportBacklinks(Project $project)
    {
        $backlinks = $project->backlinks()
            ->select('url', 'source_url', 'anchor_text', 'status', 'created_at')
            ->get();

        $csv = $this->generateCsv($backlinks);

        return Response::streamDownload(function () use ($csv) {
            echo $csv;
        }, 'backlinks.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function exportMetrics(Project $project)
    {
        $metrics = $project->metrics()
            ->select('type', 'value', 'date', 'created_at')
            ->get();

        $csv = $this->generateCsv($metrics);

        return Response::streamDownload(function () use ($csv) {
            echo $csv;
        }, 'metrics.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function exportBrokenBacklinks(Project $project)
    {
        $brokenBacklinks = $project->backlinks()
            ->where('status', 'broken')
            ->pluck('target_url');

        $content = implode("\n", $brokenBacklinks->toArray());

        $filename = 'kirik_backlinkler_' . $project->id . '_' . now()->format('Y-m-d_H-i-s') . '.txt';

        return response($content)
            ->header('Content-Type', 'text/plain')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    private function generateCsv($data)
    {
        if ($data->isEmpty()) {
            return '';
        }

        $headers = array_keys($data->first()->toArray());
        $csv = implode(',', $headers) . "\n";

        foreach ($data as $row) {
            $csv .= implode(',', array_map(function ($value) {
                return '"' . str_replace('"', '""', $value) . '"';
            }, $row->toArray())) . "\n";
        }

        return $csv;
    }
}
