<?php

namespace App\Http\Controllers;

use App\Models\Metric;
use App\Models\Project;
use Illuminate\Http\Request;

class MetricController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Project $project)
    {
        $metrics = $project->metrics()
            ->orderBy('date', 'desc')
            ->get()
            ->groupBy('type');

        return view('metrics.index', compact('project', 'metrics'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'type' => 'required|string',
            'value' => 'required|numeric',
            'date' => 'required|date',
        ]);

        $project->metrics()->create($validated);

        return redirect()->route('projects.metrics.index', $project)
            ->with('success', 'Metrik başarıyla eklendi.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project, Metric $metric)
    {
        $metric->delete();

        return redirect()->route('projects.metrics.index', $project)
            ->with('success', 'Metrik başarıyla silindi.');
    }
}
