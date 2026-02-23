@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-white tracking-tight">Dashboard</h1>
        <a href="{{ route('projects.create') }}" class="btn btn-primary shadow-lg shadow-blue-500/20 gap-2">
            <i class="fas fa-plus"></i> Yeni Proje
        </a>
    </div>

    <!-- İstatistikler -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        <!-- Toplam Proje -->
        <div class="card bg-[#1e293b] border border-white/5 shadow-2xl relative overflow-hidden group hover:border-blue-500/30 transition-all duration-300">
            <div class="card-body p-6">
                <div class="flex justify-between items-start">
                    <div>
                        <h2 class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-1">Toplam Proje</h2>
                        <p class="text-3xl font-bold text-white drop-shadow-md group-hover:scale-105 transition-transform duration-300">{{ $stats['total_projects'] }}</p>
                    </div>
                    <div class="p-3 bg-blue-500/10 rounded-xl text-blue-400">
                        <i class="fas fa-folder text-xl"></i>
                    </div>
                </div>
                <div class="w-full bg-white/5 rounded-full h-1 mt-4">
                    <div class="bg-blue-500 h-1 rounded-full" style="width: 70%"></div>
                </div>
            </div>
        </div>

        <!-- Toplam Backlink -->
        <div class="card bg-[#1e293b] border border-white/5 shadow-2xl relative overflow-hidden group hover:border-purple-500/30 transition-all duration-300">
            <div class="card-body p-6">
                <div class="flex justify-between items-start">
                    <div>
                        <h2 class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-1">Toplam Backlink</h2>
                        <p class="text-3xl font-bold text-white drop-shadow-md group-hover:scale-105 transition-transform duration-300">{{ $stats['total_backlinks'] }}</p>
                    </div>
                    <div class="p-3 bg-purple-500/10 rounded-xl text-purple-400">
                        <i class="fas fa-link text-xl"></i>
                    </div>
                </div>
                <div class="w-full bg-white/5 rounded-full h-1 mt-4">
                    <div class="bg-purple-500 h-1 rounded-full" style="width: 100%"></div>
                </div>
            </div>
        </div>

        <!-- Aktif Backlink -->
        <div class="card bg-[#1e293b] border border-white/5 shadow-2xl relative overflow-hidden group hover:border-emerald-500/30 transition-all duration-300">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-emerald-500/10 rounded-full blur-2xl group-hover:bg-emerald-500/20 transition-all duration-500"></div>
            <div class="card-body p-6 relative z-10">
                <div class="flex justify-between items-start">
                    <div>
                        <h2 class="text-emerald-500/80 text-xs font-bold uppercase tracking-wider mb-1">Aktif Backlink</h2>
                        <p class="text-3xl font-bold text-white drop-shadow-[0_0_10px_rgba(52,211,153,0.5)] group-hover:scale-105 transition-transform duration-300">{{ $stats['active_backlinks'] }}</p>
                    </div>
                    <div class="p-3 bg-emerald-500/10 rounded-xl text-emerald-400">
                        <i class="fas fa-check-circle text-xl"></i>
                    </div>
                </div>
                <div class="w-full bg-white/5 rounded-full h-1 mt-4">
                    @php 
                        $activePercent = $stats['total_backlinks'] > 0 ? ($stats['active_backlinks'] / $stats['total_backlinks']) * 100 : 0;
                    @endphp
                    <div class="bg-emerald-500 h-1 rounded-full shadow-[0_0_10px_rgba(52,211,153,0.5)]" style="width: {{ $activePercent }}%"></div>
                </div>
            </div>
        </div>

        <!-- Kırık Backlink -->
        <div class="card bg-[#1e293b] border border-white/5 shadow-2xl relative overflow-hidden group hover:border-rose-500/30 transition-all duration-300">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-rose-500/10 rounded-full blur-2xl group-hover:bg-rose-500/20 transition-all duration-500"></div>
            <div class="card-body p-6 relative z-10">
                <div class="flex justify-between items-start">
                    <div>
                        <h2 class="text-rose-500/80 text-xs font-bold uppercase tracking-wider mb-1">Kırık Backlink</h2>
                        <p class="text-3xl font-bold text-white drop-shadow-[0_0_10px_rgba(244,63,94,0.5)] group-hover:scale-105 transition-transform duration-300">{{ $stats['broken_backlinks'] }}</p>
                    </div>
                    <div class="p-3 bg-rose-500/10 rounded-xl text-rose-400">
                        <i class="fas fa-exclamation-triangle text-xl"></i>
                    </div>
                </div>
                <div class="w-full bg-white/5 rounded-full h-1 mt-4">
                    @php 
                        $brokenPercent = $stats['total_backlinks'] > 0 ? ($stats['broken_backlinks'] / $stats['total_backlinks']) * 100 : 0;
                    @endphp
                    <div class="bg-rose-500 h-1 rounded-full shadow-[0_0_10px_rgba(244,63,94,0.5)]" style="width: {{ $brokenPercent }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Son Projeler -->
        <div class="card bg-[#1e293b] border border-white/5 shadow-2xl h-full">
            <div class="card-body p-0">
                <div class="p-6 border-b border-white/5 flex justify-between items-center">
                    <h2 class="card-title text-white text-sm uppercase tracking-wider">Son Projeler</h2>
                    <a href="{{ route('projects.index') }}" class="text-xs text-blue-400 hover:text-blue-300 font-medium">Tümünü Gör <i class="fas fa-arrow-right ml-1"></i></a>
                </div>
                <div class="overflow-x-auto">
                    <table class="table w-full">
                        <thead class="bg-base-200/50 text-white font-medium text-xs uppercase tracking-wider">
                            <tr>
                                <th class="py-4 pl-6 text-white">Proje</th>
                                <th class="py-4 text-white text-center">Backlink</th>
                                <th class="py-4 pr-6 text-white text-right">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse($recent_projects as $project)
                            <tr class="hover:bg-white/[0.02] transition-colors group">
                                <td class="pl-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded bg-blue-500/10 flex items-center justify-center text-blue-400">
                                            <i class="fas fa-folder text-xs"></i>
                                        </div>
                                        <div class="font-medium text-gray-200 group-hover:text-white transition-colors">{{ $project->name }}</div>
                                    </div>
                                </td>
                                <td class="py-4 text-center">
                                    <span class="badge bg-base-300 border-white/10 text-gray-300 text-xs font-mono">{{ $project->backlinks_count }}</span>
                                </td>
                                <td class="pr-6 py-4 text-right">
                                    <a href="{{ route('projects.show', $project) }}" class="btn btn-ghost btn-xs text-blue-400 hover:bg-blue-500/10 hover:text-blue-300 gap-1">
                                        Detay <i class="fas fa-chevron-right text-[10px]"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center py-8 text-gray-500">Henüz proje yok.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Son Backlinkler -->
        <div class="card bg-[#1e293b] border border-white/5 shadow-2xl h-full">
            <div class="card-body p-0">
                <div class="p-6 border-b border-white/5 flex justify-between items-center">
                    <h2 class="card-title text-white text-sm uppercase tracking-wider">Son Backlinkler</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="table w-full">
                        <thead class="bg-base-200/50 text-white font-medium text-xs uppercase tracking-wider">
                            <tr>
                                <th class="py-4 pl-6 text-white">URL</th>
                                <th class="py-4 text-white">Proje</th>
                                <th class="py-4 pr-6 text-white text-right">Durum</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse($recent_backlinks as $backlink)
                            <tr class="hover:bg-white/[0.02] transition-colors group">
                                <td class="pl-6 py-4">
                                    <div class="flex items-center gap-3" title="{{ $backlink->target_url }}">
                                        <div class="w-6 h-6 rounded bg-purple-500/10 flex items-center justify-center text-purple-400 shrink-0">
                                            <i class="fas fa-globe text-[10px]"></i>
                                        </div>
                                        <div class="font-medium text-gray-300 truncate max-w-[180px] sm:max-w-xs">{{ $backlink->target_url }}</div>
                                    </div>
                                </td>
                                <td class="py-4 text-gray-400 text-sm">{{ $backlink->project->name }}</td>
                                <td class="pr-6 py-4 text-right">
                                    @if($backlink->status === 'active')
                                        <div class="inline-flex items-center gap-1.5 text-emerald-400 text-xs font-bold uppercase tracking-wide">
                                            <div class="w-1.5 h-1.5 rounded-full bg-emerald-400 shadow-[0_0_5px_rgba(52,211,153,0.8)]"></div>
                                            Aktif
                                        </div>
                                    @elseif($backlink->status === 'broken')
                                        <div class="inline-flex items-center gap-1.5 text-rose-400 text-xs font-bold uppercase tracking-wide">
                                            <div class="w-1.5 h-1.5 rounded-full bg-rose-400 shadow-[0_0_5px_rgba(244,63,94,0.8)]"></div>
                                            Kırık
                                        </div>
                                    @else
                                        <div class="inline-flex items-center gap-1.5 text-amber-400 text-xs font-bold uppercase tracking-wide">
                                            <div class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></div>
                                            Beklemede
                                        </div>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center py-8 text-gray-500">Henüz backlink yok.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
