@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">{{ $project->name }} - Rapor</h1>
        <a href="{{ route('projects.show', $project) }}" class="btn btn-ghost">
            Projeye Dön
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Backlink İstatistikleri -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title">Backlink İstatistikleri</h2>
                <div class="stats stats-vertical shadow">
                    @foreach($backlinkStats as $stat)
                    <div class="stat">
                        <div class="stat-title">{{ ucfirst($stat->status) }}</div>
                        <div class="stat-value">{{ $stat->count }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Metrik Ortalamaları -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title">Metrik Ortalamaları</h2>
                <div class="stats stats-vertical shadow">
                    @foreach($metrics as $metric)
                    <div class="stat">
                        <div class="stat-title">{{ ucfirst(str_replace('_', ' ', $metric->type)) }}</div>
                        <div class="stat-value">{{ number_format($metric->average, 2) }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Son Aktiviteler -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title">Son Aktiviteler</h2>
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>URL</th>
                                <th>Durum</th>
                                <th>Tarih</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentActivity as $backlink)
                            <tr>
                                <td>{{ $backlink->url }}</td>
                                <td>
                                    <span class="badge badge-{{ $backlink->status === 'active' ? 'success' : 'error' }}">
                                        {{ $backlink->status }}
                                    </span>
                                </td>
                                <td>{{ $backlink->updated_at->format('d.m.Y H:i') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 