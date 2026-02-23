<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Models\BacklinkCheckProgress;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Auth::user()->projects()->withCount('backlinks')->latest()->get();
        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        return view('projects.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'target_url' => 'required|url|max:255',
        ]);

        $project = Auth::user()->projects()->create($validated);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Proje başarıyla oluşturuldu.');
    }

    public function show(Request $request, Project $project)
    {
        Gate::authorize('view', $project);
        
        $query = $project->backlinks();

        // Filtreleme
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('rel') && $request->rel !== 'all') {
            $query->where('rel_attribute', 'like', "%{$request->rel}%");
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('target_url', 'like', "%{$search}%")
                  ->orWhere('anchor_text', 'like', "%{$search}%");
            });
        }

        $backlinks = $query->latest()->get();

        // Analiz İstatistikleri (Tüm veriler üzerinden)
        $allBacklinks = $project->backlinks;
        $totalCount = $allBacklinks->count();
        
        $analysis = [
            'brand' => 0,
            'url' => 0,
            'keyword' => 0,
            'empty' => 0,
        ];

        if ($totalCount > 0) {
            foreach ($allBacklinks as $bl) {
                $anchor = mb_strtolower($bl->anchor_text ?? '');
                $projectName = mb_strtolower($project->name);
                $targetUrl = mb_strtolower($project->target_url);
                $domain = parse_url($targetUrl, PHP_URL_HOST);

                if (empty($anchor)) {
                    $analysis['empty']++;
                } elseif (str_contains($anchor, $projectName)) {
                    $analysis['brand']++;
                } elseif (str_contains($anchor, $domain) || str_contains($anchor, 'http')) {
                    $analysis['url']++;
                } else {
                    $analysis['keyword']++;
                }
            }

            // Yüzdelik hesaplama
            $analysis['brand_percent'] = round(($analysis['brand'] / $totalCount) * 100);
            $analysis['url_percent'] = round(($analysis['url'] / $totalCount) * 100);
            $analysis['keyword_percent'] = round(($analysis['keyword'] / $totalCount) * 100);
            $analysis['empty_percent'] = round(($analysis['empty'] / $totalCount) * 100);
        } else {
            $analysis['brand_percent'] = 0;
            $analysis['url_percent'] = 0;
            $analysis['keyword_percent'] = 0;
            $analysis['empty_percent'] = 0;
        }

        $activeProgress = BacklinkCheckProgress::where('project_id', $project->id)
            ->where('status', 'running')
            ->where('updated_at', '>=', now()->subMinutes(5))
            ->latest()
            ->first();

        return view('projects.show', compact('project', 'backlinks', 'activeProgress', 'analysis'));
    }

    public function edit(Project $project)
    {
        Gate::authorize('update', $project);
        return view('projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        Gate::authorize('update', $project);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'target_url' => 'required|url|max:255',
        ]);

        $project->update($validated);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Proje başarıyla güncellendi.');
    }

    public function destroy(Project $project)
    {
        Gate::authorize('delete', $project);
        
        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'Proje başarıyla silindi.');
    }
}
