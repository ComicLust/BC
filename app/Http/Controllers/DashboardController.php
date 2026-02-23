<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Backlink;
use App\Models\Metric;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $stats = [
            'total_projects' => $user->projects()->count(),
            'total_backlinks' => $user->projects()->withCount('backlinks')->get()->sum('backlinks_count'),
            'active_backlinks' => $user->projects()
                ->withCount(['backlinks' => function($query) {
                    $query->where('status', 'active');
                }])
                ->get()
                ->sum('backlinks_count'),
            'broken_backlinks' => $user->projects()
                ->withCount(['backlinks' => function($query) {
                    $query->where('status', 'broken');
                }])
                ->get()
                ->sum('backlinks_count'),
        ];

        $recent_projects = $user->projects()
            ->withCount('backlinks')
            ->latest()
            ->take(5)
            ->get();

        $recent_backlinks = Backlink::whereHas('project', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with('project')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact('stats', 'recent_projects', 'recent_backlinks'));
    }
}
