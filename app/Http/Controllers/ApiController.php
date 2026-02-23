<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Backlink;
use App\Models\Metric;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ApiController extends Controller
{
    public function checkBacklink(Request $request)
    {
        $request->validate([
            'url' => 'required|url',
        ]);

        try {
            $response = Http::get($request->url);
            $status = $response->successful() ? 'active' : 'broken';

            return response()->json([
                'status' => $status,
                'status_code' => $response->status(),
                'headers' => $response->headers(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getMetrics(Request $request)
    {
        $request->validate([
            'url' => 'required|url',
        ]);

        try {
            // Burada gerçek bir API entegrasyonu yapılacak
            // Şimdilik örnek veriler döndürüyoruz
            return response()->json([
                'domain_authority' => rand(1, 100),
                'page_authority' => rand(1, 100),
                'spam_score' => rand(1, 17),
                'moz_rank' => rand(1, 10),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function syncProject(Project $project)
    {
        try {
            $backlinks = $project->backlinks;
            $metrics = [];

            foreach ($backlinks as $backlink) {
                // Backlink durumunu kontrol et
                $response = Http::get($backlink->url);
                $status = $response->successful() ? 'active' : 'broken';
                $backlink->update(['status' => $status]);

                // Metrikleri al
                $metricResponse = $this->getMetrics(new Request(['url' => $backlink->url]));
                $metrics[] = $metricResponse->getData();
            }

            return response()->json([
                'message' => 'Proje başarıyla senkronize edildi',
                'backlinks_updated' => $backlinks->count(),
                'metrics' => $metrics,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
