@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-white tracking-tight">{{ $project->name }}</h1>
        <div class="flex gap-3">
            <a href="{{ route('projects.edit', $project) }}" class="btn btn-outline btn-info btn-sm gap-2">
                <i class="fas fa-edit"></i> Düzenle
            </a>
            <form action="{{ route('projects.destroy', $project) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline btn-error btn-sm gap-2" onclick="return confirm('Emin misiniz?')">
                    <i class="fas fa-trash-alt"></i> Sil
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-10">
        <!-- Proje Detayları Kartı -->
        <div class="card bg-[#1e293b] border border-white/5 shadow-2xl rounded-2xl overflow-hidden">
            <div class="card-body p-6">
                <h2 class="card-title text-white text-sm uppercase tracking-wider mb-4 border-b border-white/5 pb-2">Proje Detayları</h2>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-white font-medium text-sm">Hedef URL</span>
                        <a href="{{ $project->target_url }}" target="_blank" class="text-blue-400 hover:text-white transition-colors font-semibold truncate max-w-[150px] text-sm">
                            {{ parse_url($project->target_url, PHP_URL_HOST) }} <i class="fas fa-external-link-alt text-[10px] ml-1"></i>
                        </a>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-white font-medium text-sm">Son Kontrol</span>
                        @if($project->last_checked_at)
                            <span class="text-gray-200 font-mono text-xs" title="{{ $project->last_checked_at->translatedFormat('d F Y H:i') }}">
                                {{ $project->last_checked_at->diffForHumans() }}
                            </span>
                        @else
                            <span class="text-gray-400 text-xs italic">Henüz yok</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Backlink İstatistikleri -->
        <div class="card bg-[#1e293b] border-l-4 border-emerald-500 shadow-2xl rounded-2xl overflow-hidden">
            <div class="card-body p-6">
                <h2 class="card-title text-white text-sm uppercase tracking-wider mb-4 border-b border-white/5 pb-2">Durum Özeti</h2>
                <div class="grid grid-cols-3 gap-2 text-center">
                    <div class="p-2 rounded-lg bg-base-300/30 border border-white/5">
                        <div class="text-xl font-bold text-white">{{ $project->backlinks()->count() }}</div>
                        <div class="text-[10px] text-gray-500 uppercase font-bold">Toplam</div>
                    </div>
                    <div class="p-2 rounded-lg bg-emerald-900/10 border border-emerald-500/20">
                        <div class="text-xl font-bold text-emerald-400">{{ $project->backlinks()->where('status', 'active')->count() }}</div>
                        <div class="text-[10px] text-emerald-500/80 uppercase font-bold">Aktif</div>
                    </div>
                    <div class="p-2 rounded-lg bg-rose-900/10 border border-rose-500/20">
                        <div class="text-xl font-bold text-rose-400">{{ $project->backlinks()->where('status', 'broken')->count() }}</div>
                        <div class="text-[10px] text-rose-500/80 uppercase font-bold">Kırık</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Anchor Text Analizi -->
        <div class="card bg-[#1e293b] border-l-4 border-purple-500 shadow-2xl rounded-2xl overflow-hidden">
            <div class="card-body p-6">
                <h2 class="card-title text-white text-sm uppercase tracking-wider mb-4 border-b border-white/5 pb-2">Anchor Dağılımı</h2>
                <div class="space-y-3">
                    <!-- Marka -->
                    <div>
                        <div class="flex justify-between text-xs mb-1">
                            <span class="text-gray-300">Marka İsmi</span>
                            <span class="text-purple-400 font-bold">%{{ $analysis['brand_percent'] }}</span>
                        </div>
                        <div class="w-full bg-white/5 rounded-full h-1.5">
                            <div class="bg-purple-500 h-1.5 rounded-full" style="width: {{ $analysis['brand_percent'] }}%"></div>
                        </div>
                    </div>
                    <!-- URL -->
                    <div>
                        <div class="flex justify-between text-xs mb-1">
                            <span class="text-gray-300">Çıplak URL</span>
                            <span class="text-blue-400 font-bold">%{{ $analysis['url_percent'] }}</span>
                        </div>
                        <div class="w-full bg-white/5 rounded-full h-1.5">
                            <div class="bg-blue-500 h-1.5 rounded-full" style="width: {{ $analysis['url_percent'] }}%"></div>
                        </div>
                    </div>
                    <!-- Keyword -->
                    <div>
                        <div class="flex justify-between text-xs mb-1">
                            <span class="text-gray-300">Anahtar Kelime</span>
                            <span class="text-amber-400 font-bold">%{{ $analysis['keyword_percent'] }}</span>
                        </div>
                        <div class="w-full bg-white/5 rounded-full h-1.5">
                            <div class="bg-amber-500 h-1.5 rounded-full" style="width: {{ $analysis['keyword_percent'] }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Backlinkler Bölümü -->
    <div class="card bg-[#1e293b] border border-white/5 shadow-2xl rounded-2xl overflow-hidden">
        <div class="card-body p-0">
            <!-- Toolbar & Filtreler -->
            <div class="p-6 border-b border-white/5 bg-[#1e293b]/50">
                <form action="{{ route('projects.show', $project) }}" method="GET" id="filterForm">
                    <div class="flex flex-col xl:flex-row justify-between items-start xl:items-center gap-6">
                        <div class="flex items-center gap-3">
                            <h2 class="text-xl font-bold text-white">Backlinkler</h2>
                            <span class="badge badge-neutral text-xs font-mono">{{ $backlinks->count() }} Kayıt</span>
                        </div>

                        <div class="flex flex-wrap items-center gap-3 w-full xl:w-auto">
                            <!-- Arama -->
                            <div class="relative w-full sm:w-48">
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="URL veya Anchor ara..." class="input input-sm input-bordered w-full bg-base-300 border-white/10 text-gray-200 focus:outline-none focus:border-blue-500 pr-8">
                                <button type="submit" class="absolute right-2 top-1.5 text-gray-500 hover:text-white">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>

                            <!-- Filtreler -->
                            <div class="flex gap-2 items-center">
                                <div class="tooltip tooltip-right z-[100]" data-tip="Sonuçları filtrelemek için bu menüleri kullanabilirsiniz.">
                                    <i class="fas fa-info-circle text-gray-500 cursor-help text-lg"></i>
                                </div>
                                <select name="status" class="select select-bordered bg-base-300 border-white/10 text-gray-200 focus:outline-none focus:border-blue-500 w-32 h-10 min-h-0 text-xs px-3 leading-tight" onchange="this.form.submit()">
                                    <option value="all" class="text-gray-200" {{ request('status') == 'all' ? 'selected' : '' }}>Tüm Durumlar</option>
                                    <option value="active" class="text-gray-200" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                                    <option value="broken" class="text-gray-200" {{ request('status') == 'broken' ? 'selected' : '' }}>Kırık</option>
                                    <option value="pending" class="text-gray-200" {{ request('status') == 'pending' ? 'selected' : '' }}>Beklemede</option>
                                </select>

                                <select name="rel" class="select select-bordered bg-base-300 border-white/10 text-gray-200 focus:outline-none focus:border-blue-500 w-36 h-10 min-h-0 text-xs px-3 leading-tight" onchange="this.form.submit()">
                                    <option value="all" class="text-gray-200" {{ request('rel') == 'all' ? 'selected' : '' }}>Tüm Rel Etiketleri</option>
                                    <option value="dofollow" class="text-gray-200" {{ request('rel') == 'dofollow' ? 'selected' : '' }}>Dofollow</option>
                                    <option value="nofollow" class="text-gray-200" {{ request('rel') == 'nofollow' ? 'selected' : '' }}>Nofollow</option>
                                    <option value="ugc" class="text-gray-200" {{ request('rel') == 'ugc' ? 'selected' : '' }}>UGC</option>
                                    <option value="sponsored" class="text-gray-200" {{ request('rel') == 'sponsored' ? 'selected' : '' }}>Sponsored</option>
                                </select>
                            </div>

                            @if(request()->anyFilled(['status', 'rel', 'search']))
                                <a href="{{ route('projects.show', $project) }}" class="btn btn-sm btn-ghost text-gray-400 hover:text-white" title="Filtreleri Temizle">
                                    <i class="fas fa-times"></i>
                                </a>
                            @endif
                        </div>

                        <div class="flex gap-2 w-full xl:w-auto justify-end">
                            <!-- Kontrol Butonu -->
                            <button type="button" id="bulkCheckButton" class="btn btn-primary btn-sm gap-2 shadow-lg shadow-blue-500/20">
                                <i class="fas fa-sync-alt animate-spin-hover"></i> Kontrol
                            </button>
                            
                            <!-- Export -->
                            <div class="dropdown dropdown-end">
                                <label tabindex="0" class="btn btn-outline btn-secondary btn-sm gap-2">
                                    <i class="fas fa-file-export"></i>
                                </label>
                                <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow-2xl bg-[#1e293b] border border-white/10 rounded-xl w-40 mt-2">
                                    <li><a href="{{ route('projects.backlinks.export', ['project' => $project, 'filter' => 'all']) }}" class="hover:bg-white/5 text-xs">Tümünü İndir</a></li>
                                    <li><a href="{{ route('projects.backlinks.export', ['project' => $project, 'filter' => 'active']) }}" class="text-emerald-400 hover:bg-emerald-900/20 text-xs">Aktifleri İndir</a></li>
                                    <li><a href="{{ route('projects.backlinks.export', ['project' => $project, 'filter' => 'broken']) }}" class="text-rose-400 hover:bg-rose-900/20 text-xs">Kırıkları İndir</a></li>
                                </ul>
                            </div>

                            <a href="{{ route('projects.backlinks.create', $project) }}" class="btn btn-sm btn-square btn-outline text-gray-400 hover:text-white hover:border-white">
                                <i class="fas fa-plus"></i>
                            </a>
                        </div>
                    </div>
                </form>
                
                <!-- Gizli Bulk Check Form -->
                <form id="bulkCheckForm" action="{{ route('projects.backlinks.bulk-check', $project) }}" method="POST" class="hidden">
                    @csrf
                    <input type="hidden" name="filter" value="all">
                </form>
            </div>

            <!-- Yükleme Göstergesi -->
            <div id="loadingIndicator" class="hidden p-8 border-b border-white/5 bg-base-200/30">
                <div class="flex flex-col items-center gap-4 max-w-lg mx-auto">
                    <div class="flex items-center gap-3 w-full">
                        <span class="loading loading-spinner loading-md text-primary"></span>
                        <div class="flex-1">
                            <div class="flex justify-between text-xs font-medium text-gray-400 mb-1">
                                <span>Kontrol Ediliyor...</span>
                                <span id="progressText">0/0</span>
                            </div>
                            <div class="w-full bg-gray-700/50 rounded-full h-2">
                                <div class="bg-gradient-to-r from-blue-500 to-purple-500 h-2 rounded-full transition-all duration-300 shadow-[0_0_10px_rgba(59,130,246,0.5)]" id="progressBar" style="width: 0%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="text-center space-y-1">
                        <p class="text-xs text-gray-400" id="estimatedTimeText">Tahmini Kalan Süre: Hesaplanıyor...</p>
                        <p class="text-[10px] text-gray-500 italic">İşlem arka planda devam ediyor, sayfadan ayrılabilirsiniz.</p>
                    </div>
                </div>
            </div>

            <!-- Tablo -->
            @if($backlinks->isEmpty())
                <div class="p-12 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-base-300/50 mb-4">
                        <i class="fas fa-search text-3xl text-gray-600"></i>
                    </div>
                    <h3 class="text-lg font-medium text-white mb-1">Sonuç bulunamadı</h3>
                    <p class="text-gray-500 mb-6">Filtreleme kriterlerinize uygun backlink yok.</p>
                    <a href="{{ route('projects.show', $project) }}" class="btn btn-outline btn-sm">Filtreleri Temizle</a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="table w-full">
                        <thead class="bg-base-200/50 text-white font-medium text-xs uppercase tracking-wider border-b border-white/5">
                            <tr>
                                <th class="py-4 pl-6 text-white w-1/3">Hedef URL</th>
                                <th class="py-4 text-white">Anchor Text</th>
                                <th class="py-4 text-white text-center">Rel</th>
                                <th class="py-4 text-white text-center">Durum</th>
                                <th class="py-4 text-white w-1/6">Detaylar</th>
                                <th class="py-4 pr-6 text-right text-white">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm divide-y divide-white/5">
                            @foreach($backlinks as $backlink)
                                <tr class="group hover:bg-white/[0.02] transition-colors">
                                    <td class="pl-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded bg-base-300 flex items-center justify-center text-gray-500 group-hover:text-white transition-colors shrink-0">
                                                <i class="fas fa-globe text-xs"></i>
                                            </div>
                                            <a href="{{ $backlink->target_url }}" target="_blank" class="font-medium text-white hover:text-blue-400 transition-colors truncate max-w-[250px] block break-all" title="{{ $backlink->target_url }}">
                                                {{ $backlink->target_url }}
                                            </a>
                                        </div>
                                    </td>
                                    <td class="py-4 text-gray-400">
                                        @if($backlink->anchor_text)
                                            <div class="tooltip tooltip-bottom" data-tip="{{ $backlink->anchor_text }}">
                                                <span class="inline-block px-2 py-1 bg-white/5 rounded text-xs font-mono text-gray-300 max-w-[150px] truncate cursor-help">
                                                    {{ $backlink->anchor_text }}
                                                </span>
                                            </div>
                                        @else
                                            <span class="text-gray-600 text-xs">-</span>
                                        @endif
                                    </td>
                                    <td class="py-4 text-center">
                                        @if($backlink->rel_attribute)
                                            @php
                                                $rels = explode(',', $backlink->rel_attribute);
                                            @endphp
                                            @foreach($rels as $rel)
                                                @php $rel = trim($rel); @endphp
                                                @if($rel === 'dofollow' || empty($rel))
                                                    <span class="badge badge-sm badge-success bg-emerald-500/10 text-emerald-400 border-emerald-500/20 text-[10px] mb-1">DoFollow</span>
                                                @else
                                                    <span class="badge badge-sm badge-warning bg-amber-500/10 text-amber-400 border-amber-500/20 text-[10px] mb-1">{{ ucfirst($rel) }}</span>
                                                @endif
                                            @endforeach
                                        @else
                                            <span class="text-gray-600 text-xs">-</span>
                                        @endif
                                    </td>
                                    <td class="py-4 text-center">
                                        @switch($backlink->status)
                                            @case('active')
                                                <span class="inline-flex items-center gap-1.5 text-emerald-400 text-xs font-bold uppercase tracking-wide">
                                                    <div class="w-1.5 h-1.5 rounded-full bg-emerald-400 shadow-[0_0_5px_rgba(52,211,153,0.8)]"></div> Aktif
                                                </span>
                                                @break
                                            @case('broken')
                                                <span class="inline-flex items-center gap-1.5 text-rose-400 text-xs font-bold uppercase tracking-wide">
                                                    <div class="w-1.5 h-1.5 rounded-full bg-rose-400 shadow-[0_0_5px_rgba(244,63,94,0.8)]"></div> Kırık
                                                </span>
                                                @break
                                            @default
                                                <span class="inline-flex items-center gap-1.5 text-amber-400 text-xs font-bold uppercase tracking-wide">
                                                    <div class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></div> Beklemede
                                                </span>
                                        @endswitch
                                    </td>
                                    <td class="py-4 text-xs text-gray-400 max-w-[200px] truncate">
                                        @if($backlink->status === 'broken' && $backlink->details)
                                            @php
                                                $details = json_decode($backlink->details, true);
                                                $error = $details['error_reason'] ?? $details['message'] ?? 'Bilinmeyen Hata';
                                            @endphp
                                            <span class="text-rose-400" title="{{ $error }}">{{ Str::limit($error, 30) }}</span>
                                        @elseif($backlink->status === 'active')
                                            <span class="text-emerald-500/50">Sorun Yok</span>
                                        @else
                                            <span class="text-gray-600">-</span>
                                        @endif
                                    </td>
                                    <td class="pr-6 py-4 text-right">
                                        <div class="flex justify-end gap-1">
                                            <form action="{{ route('projects.backlinks.check', [$project, $backlink]) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="btn btn-xs btn-square btn-ghost text-blue-400 hover:bg-blue-500/10" title="Kontrol Et">
                                                    <i class="fas fa-sync-alt"></i>
                                                </button>
                                            </form>
                                            <a href="{{ route('projects.backlinks.edit', [$project, $backlink]) }}" class="btn btn-xs btn-square btn-ghost text-amber-400 hover:bg-amber-500/10" title="Düzenle">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                            <form action="{{ route('projects.backlinks.destroy', [$project, $backlink]) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-xs btn-square btn-ghost text-rose-400 hover:bg-rose-500/10" title="Sil" onclick="return confirm('Emin misiniz?')">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Sonuçlar Modal -->
<dialog id="resultsModal" class="modal modal-bottom sm:modal-middle backdrop-blur-sm">
    <div class="modal-box w-11/12 max-w-4xl bg-[#1e293b] border border-white/10 shadow-2xl">
        <h3 class="font-bold text-xl text-white mb-6 flex items-center gap-3">
            <i class="fas fa-clipboard-check text-blue-500"></i>
            Kontrol Sonuçları
        </h3>
        
        <!-- Özet İstatistikler -->
        <div class="grid grid-cols-3 gap-4 mb-8">
            <div class="flex flex-col items-center p-4 rounded-xl bg-base-300/30 border border-white/5">
                <div class="text-xs text-gray-500 uppercase font-bold tracking-wider mb-1">Toplam</div>
                <div class="text-2xl font-bold text-white" id="resultTotal">0</div>
            </div>
            <div class="flex flex-col items-center p-4 rounded-xl bg-emerald-900/10 border border-emerald-500/20">
                <div class="text-xs text-emerald-500/80 uppercase font-bold tracking-wider mb-1">Başarılı</div>
                <div class="text-2xl font-bold text-emerald-400" id="resultActive">0</div>
            </div>
            <div class="flex flex-col items-center p-4 rounded-xl bg-rose-900/10 border border-rose-500/20">
                <div class="text-xs text-rose-500/80 uppercase font-bold tracking-wider mb-1">Başarısız</div>
                <div class="text-2xl font-bold text-rose-400" id="resultBroken">0</div>
            </div>
        </div>

        <div class="divider border-white/10 text-gray-500 text-xs uppercase">Detaylı Rapor</div>

        <div class="overflow-x-auto max-h-[400px] border border-white/5 rounded-lg bg-base-200/20">
            <table class="table table-pin-rows w-full table-fixed">
                <thead class="bg-[#1e293b] text-gray-400 text-xs uppercase">
                    <tr>
                        <th class="bg-[#1e293b] w-2/3">Hedef URL</th>
                        <th class="bg-[#1e293b] w-1/6">Durum</th>
                        <th class="bg-[#1e293b] w-1/6">Sonuç</th>
                    </tr>
                </thead>
                <tbody id="resultsTableBody" class="text-sm">
                </tbody>
            </table>
        </div>
        
        <div class="modal-action mt-6">
            <form method="dialog">
                <button class="btn btn-primary px-8" id="closeModalButton">Tamamla</button>
            </form>
        </div>
    </div>
</dialog>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const bulkCheckForm = document.getElementById('bulkCheckForm');
    const bulkCheckButton = document.getElementById('bulkCheckButton');
    const loadingIndicator = document.getElementById('loadingIndicator');
    const progressBar = document.getElementById('progressBar');
    const progressText = document.getElementById('progressText');
    const resultsModal = document.getElementById('resultsModal');
    const resultsTableBody = document.getElementById('resultsTableBody');
    const closeModalButton = document.getElementById('closeModalButton');
    
    // İstatistik elementleri
    const resultTotal = document.getElementById('resultTotal');
    const resultActive = document.getElementById('resultActive');
    const resultBroken = document.getElementById('resultBroken');
    const estimatedTimeText = document.getElementById('estimatedTimeText');

    let pollingInterval = null;
    let startTime = null;

    function startPolling(projectId, progressId) {
        if (bulkCheckButton) {
            bulkCheckButton.disabled = true;
            bulkCheckButton.innerHTML = '<span class="loading loading-spinner loading-xs"></span> İşleniyor...';
        }
        loadingIndicator.classList.remove('hidden');
        
        if (pollingInterval) clearInterval(pollingInterval);

        function poll() {
            fetch(`/projects/${projectId}/backlinks/progress/${progressId}`)
                .then(response => {
                    if (response.status === 404) {
                        throw new Error('NOT_FOUND');
                    }
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(progressData => {
                    const progress = progressData.total > 0 ? (progressData.checked / progressData.total) * 100 : 0;
                    progressBar.style.width = `${progress}%`;
                    progressText.textContent = `${progressData.checked}/${progressData.total}`;
                    
                    if (progressData.started_at) {
                        startTime = progressData.started_at;
                    }

                    if (startTime && progressData.checked > 0 && progressData.total > 0) {
                        const elapsedTime = (Date.now() - startTime) / 1000;
                        const itemsPerSecond = progressData.checked / elapsedTime;
                        const remainingItems = progressData.total - progressData.checked;
                        
                        if (itemsPerSecond > 0) {
                            const remainingSeconds = Math.ceil(remainingItems / itemsPerSecond);
                            let timeString = '';
                            
                            if (remainingSeconds < 60) {
                                timeString = `${remainingSeconds} saniye`;
                            } else {
                                const minutes = Math.floor(remainingSeconds / 60);
                                const seconds = remainingSeconds % 60;
                                timeString = `${minutes} dakika ${seconds} saniye`;
                            }
                            
                            if (estimatedTimeText) {
                                estimatedTimeText.textContent = `Tahmini Kalan Süre: ${timeString}`;
                            }
                        }
                    } else if (!startTime && progressData.total > 0) {
                         const totalEstimatedSeconds = Math.ceil(progressData.total * 1.5);
                         if (estimatedTimeText) {
                            estimatedTimeText.textContent = `Tahmini Süre: ~${Math.ceil(totalEstimatedSeconds / 60)} dakika`;
                         }
                    }

                    if (progressData.status === 'finished') {
                        clearInterval(pollingInterval);
                        showResults(progressData.result || []);
                    }
                })
                .catch(error => {
                    if (error.message === 'NOT_FOUND') {
                        console.log('Aktif işlem bulunamadı, durduruluyor.');
                    } else {
                        console.error('İlerleme kontrol hatası:', error);
                    }
                    clearInterval(pollingInterval);
                    loadingIndicator.classList.add('hidden');
                    resetButton();
                });
        }

        poll();
        pollingInterval = setInterval(poll, 2000);
    }

    function resetButton() {
        if (bulkCheckButton) {
            bulkCheckButton.disabled = false;
            bulkCheckButton.innerHTML = '<i class="fas fa-sync-alt"></i> Kontrol Et';
        }
    }

    function showResults(results) {
        resultsTableBody.innerHTML = '';
        
        let activeCount = 0;
        let brokenCount = 0;

        results.forEach(result => {
            if (result.status === 'active') activeCount++;
            else brokenCount++;

            const row = document.createElement('tr');
            row.className = 'hover:bg-white/[0.02] border-b border-white/5 last:border-0';
            
            const statusBadge = result.status === 'active' 
                ? '<span class="badge badge-success badge-sm bg-emerald-500/10 text-emerald-400 border-emerald-500/20">Aktif</span>' 
                : '<span class="badge badge-error badge-sm bg-rose-500/10 text-rose-400 border-rose-500/20">Kırık</span>';
            
            const message = result.message || (result.status === 'active' ? 'Bağlantı doğrulandı' : 'Bağlantı bulunamadı');
            const messageClass = result.status === 'active' ? 'text-gray-400' : 'text-rose-400';

            row.innerHTML = `
                <td class="font-mono text-gray-300 break-all" title="${result.target_url}">${result.target_url}</td>
                <td>${statusBadge}</td>
                <td class="${messageClass}">${message}</td>
            `;
            resultsTableBody.appendChild(row);
        });

        if (resultTotal) resultTotal.textContent = results.length;
        if (resultActive) resultActive.textContent = activeCount;
        if (resultBroken) resultBroken.textContent = brokenCount;

        loadingIndicator.classList.add('hidden');
        resetButton();
        resultsModal.showModal();
    }

    if (bulkCheckButton) {
        bulkCheckButton.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Butonu yükleniyor moduna al
            const originalContent = bulkCheckButton.innerHTML;
            bulkCheckButton.disabled = true;
            bulkCheckButton.innerHTML = '<span class="loading loading-spinner loading-xs"></span> Başlatılıyor...';
            
            // AJAX isteği gönder
            fetch("{{ route('projects.backlinks.bulk-check', $project) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    filter: "{{ request('status', 'all') }}" // Mevcut filtreye göre kontrol et
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.progress_id) {
                    startPolling({{ $project->id }}, data.progress_id);
                } else {
                    alert('Bir hata oluştu: ' + (data.message || 'Bilinmeyen hata'));
                    bulkCheckButton.innerHTML = originalContent;
                    bulkCheckButton.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Bir hata oluştu. Lütfen tekrar deneyin.');
                bulkCheckButton.innerHTML = originalContent;
                bulkCheckButton.disabled = false;
            });
        });
    }

    if (closeModalButton) {
        closeModalButton.addEventListener('click', function() {
            resultsModal.close();
            window.location.reload();
        });
    }

    @if(isset($activeProgress) && $activeProgress)
        startPolling({{ $project->id }}, {{ $activeProgress->id }});
    @endif
});
</script>
@endpush
@endsection
