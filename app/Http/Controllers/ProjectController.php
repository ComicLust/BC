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

    public function show(Project $project)
    {
        Gate::authorize('view', $project);
        
        $project->load(['backlinks' => function($query) {
            $query->latest();
        }]);

        $activeProgress = BacklinkCheckProgress::where('project_id', $project->id)
            ->where('status', 'running')
            ->where('updated_at', '>=', now()->subMinutes(5))
            ->latest()
            ->first();

        return view('projects.show', compact('project', 'activeProgress'));
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
