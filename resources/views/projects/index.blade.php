@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-white tracking-tight">Projelerim</h1>
            <p class="text-gray-400 text-sm mt-1">Tüm backlink takip projelerinizi buradan yönetin.</p>
        </div>
        <a href="{{ route('projects.create') }}" class="btn btn-primary shadow-lg shadow-blue-500/20 gap-2">
            <i class="fas fa-plus"></i> Yeni Proje
        </a>
    </div>

    @if($projects->isEmpty())
        <!-- Empty State -->
        <div class="flex flex-col items-center justify-center py-20 bg-[#1e293b] border border-white/5 rounded-2xl border-dashed">
            <div class="w-20 h-20 bg-base-300 rounded-full flex items-center justify-center mb-6">
                <i class="fas fa-folder-open text-4xl text-gray-500"></i>
            </div>
            <h3 class="text-xl font-bold text-white mb-2">Henüz Bir Projeniz Yok</h3>
            <p class="text-gray-400 text-center max-w-md mb-8">
                Backlinklerinizi takip etmeye başlamak için ilk projenizi oluşturun.
            </p>
            <a href="{{ route('projects.create') }}" class="btn btn-primary px-8">
                <i class="fas fa-plus mr-2"></i> İlk Projeyi Oluştur
            </a>
        </div>
    @else
        <!-- Projeler Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($projects as $project)
                <div class="card bg-[#1e293b] border border-white/5 shadow-xl hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 group h-full flex flex-col">
                    <!-- Kart Üstü Gradient -->
                    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-500 to-purple-600 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    
                    <div class="card-body p-6 flex-1">
                        <!-- Header -->
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center gap-3 overflow-hidden">
                                <!-- Favicon -->
                                <div class="w-10 h-10 rounded-lg bg-base-300 flex items-center justify-center shrink-0 border border-white/5">
                                    <img src="https://www.google.com/s2/favicons?domain={{ $project->target_url }}&sz=32" alt="Favicon" class="w-5 h-5 opacity-80 group-hover:opacity-100 transition-opacity" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($project->name) }}&background=random&color=fff'">
                                </div>
                                <div>
                                    <h2 class="card-title text-white text-lg font-bold truncate group-hover:text-blue-400 transition-colors" title="{{ $project->name }}">
                                        {{ $project->name }}
                                    </h2>
                                    <a href="{{ $project->target_url }}" target="_blank" class="text-xs text-gray-500 hover:text-gray-300 flex items-center gap-1 truncate max-w-[200px]" title="{{ $project->target_url }}">
                                        <i class="fas fa-link text-[10px]"></i> {{ parse_url($project->target_url, PHP_URL_HOST) }}
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- İstatistikler -->
                        <div class="grid grid-cols-2 gap-3 mb-6">
                            <div class="bg-base-300/30 rounded-lg p-3 border border-white/5">
                                <div class="text-[10px] text-gray-500 uppercase font-bold tracking-wider mb-1">Backlink</div>
                                <div class="text-xl font-bold text-white">{{ $project->backlinks_count }}</div>
                            </div>
                            <div class="bg-base-300/30 rounded-lg p-3 border border-white/5">
                                <div class="text-[10px] text-gray-500 uppercase font-bold tracking-wider mb-1">Durum</div>
                                <div class="flex items-center gap-1.5">
                                    <div class="w-2 h-2 rounded-full bg-emerald-500 shadow-[0_0_5px_rgba(16,185,129,0.5)]"></div>
                                    <span class="text-sm font-medium text-gray-300">Aktif</span>
                                </div>
                            </div>
                        </div>

                        <!-- Footer & Actions -->
                        <div class="flex items-center justify-between mt-auto pt-4 border-t border-white/5">
                            <span class="text-[10px] text-gray-500" title="{{ $project->created_at->translatedFormat('d F Y H:i') }}">
                                <i class="far fa-clock mr-1"></i> {{ $project->created_at->diffForHumans() }}
                            </span>

                            <div class="flex items-center gap-1">
                                <a href="{{ route('projects.show', $project) }}" class="btn btn-sm btn-ghost btn-square text-blue-400 hover:bg-blue-500/10 hover:text-blue-300 tooltip tooltip-top" data-tip="Detaylar">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('projects.edit', $project) }}" class="btn btn-sm btn-ghost btn-square text-amber-400 hover:bg-amber-500/10 hover:text-amber-300 tooltip tooltip-top" data-tip="Düzenle">
                                    <i class="fas fa-pen"></i>
                                </a>
                                <form action="{{ route('projects.destroy', $project) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-ghost btn-square text-rose-400 hover:bg-rose-500/10 hover:text-rose-300 tooltip tooltip-top" data-tip="Sil" onclick="return confirm('Bu projeyi ve tüm backlinklerini silmek istediğinizden emin misiniz?')">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
