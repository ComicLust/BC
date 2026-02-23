<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Project $project)
    {
        $backlinkStats = $project->backlinks()
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        $metrics = $project->metrics()
            ->select('type', DB::raw('AVG(value) as average'))
            ->groupBy('type')
            ->get();

        $recentActivity = $project->backlinks()
            ->with('project')
            ->orderBy('updated_at', 'desc')
            ->take(10)
            ->get();

        return view('reports.index', compact('project', 'backlinkStats', 'metrics', 'recentActivity'));
    }
}
