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

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-10">
        <!-- Proje Detayları Kartı -->
        <div class="card bg-[#1e293b] border border-white/5 shadow-2xl rounded-2xl overflow-hidden">
            <div class="card-body p-8">
                <h2 class="card-title text-white text-sm uppercase tracking-wider mb-4 border-b border-white/5 pb-2">Proje Detayları</h2>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-white font-medium">Hedef URL</span>
                        <a href="{{ $project->target_url }}" target="_blank" class="text-blue-400 hover:text-white transition-colors font-semibold truncate max-w-[200px] sm:max-w-xs">
                            {{ $project->target_url }} <i class="fas fa-external-link-alt text-xs ml-1"></i>
                        </a>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-white font-medium">Oluşturulma</span>
                        <span class="text-gray-200 font-mono text-sm" title="{{ $project->created_at->translatedFormat('d F Y H:i') }}">
                            {{ $project->created_at->diffForHumans() }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-white font-medium">Son Güncelleme</span>
                        <span class="text-gray-200 font-mono text-sm" title="{{ $project->updated_at->translatedFormat('d F Y H:i') }}">
                            {{ $project->updated_at->diffForHumans() }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- İstatistik Kartı -->
        <div class="card bg-[#1e293b] border-l-4 border-emerald-500 shadow-2xl rounded-2xl overflow-hidden">
            <div class="card-body p-8">
                <h2 class="card-title text-white text-sm uppercase tracking-wider mb-6 border-b border-white/5 pb-2">Backlink İstatistikleri</h2>
                <div class="grid grid-cols-3 gap-4 text-center">
                    <!-- Toplam -->
                    <div class="flex flex-col items-center p-4 rounded-xl bg-base-300/30 hover:bg-base-300/50 transition-colors border-l-4 border-gray-500">
                        <div class="text-gray-400 mb-2"><i class="fas fa-link text-2xl"></i></div>
                        <div class="text-3xl font-bold text-white mb-1 drop-shadow-md">{{ $project->backlinks->count() }}</div>
                        <div class="text-xs text-gray-500 font-medium uppercase tracking-wide">Toplam</div>
                    </div>
                    
                    <!-- Aktif -->
                    <div class="flex flex-col items-center p-4 rounded-xl bg-emerald-900/10 border-l-4 border-emerald-500 hover:bg-emerald-900/20 transition-colors relative overflow-hidden group">
                        <div class="absolute inset-0 bg-emerald-500/5 blur-xl group-hover:bg-emerald-500/10 transition-all"></div>
                        <div class="text-emerald-400 mb-2 drop-shadow-[0_0_8px_rgba(52,211,153,0.6)]"><i class="fas fa-check-circle text-2xl"></i></div>
                        <div class="text-3xl font-bold text-white mb-1 drop-shadow-[0_0_10px_rgba(52,211,153,0.8)]">{{ $project->backlinks->where('status', 'active')->count() }}</div>
                        <div class="text-xs text-emerald-500/80 font-bold uppercase tracking-wide">Aktif</div>
                    </div>

                    <!-- Kırık -->
                    <div class="flex flex-col items-center p-4 rounded-xl bg-rose-900/10 border-l-4 border-rose-500 hover:bg-rose-900/20 transition-colors relative overflow-hidden group">
                        <div class="absolute inset-0 bg-rose-500/5 blur-xl group-hover:bg-rose-500/10 transition-all"></div>
                        <div class="text-rose-500 mb-2 drop-shadow-[0_0_8px_rgba(244,63,94,0.6)]"><i class="fas fa-exclamation-triangle text-2xl"></i></div>
                        <div class="text-3xl font-bold text-white mb-1 drop-shadow-[0_0_10px_rgba(244,63,94,0.8)]">{{ $project->backlinks->where('status', 'broken')->count() }}</div>
                        <div class="text-xs text-rose-500/80 font-bold uppercase tracking-wide">Kırık</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Backlinkler Bölümü -->
    <div class="card bg-[#1e293b] border border-white/5 shadow-2xl rounded-2xl overflow-hidden">
        <div class="card-body p-0">
            <!-- Header & Toolbar -->
            <div class="p-6 border-b border-white/5 bg-[#1e293b]/50">
                <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
                    <div class="flex items-center gap-3">
                        <h2 class="text-xl font-bold text-white">Backlinkler</h2>
                        <span class="badge badge-neutral text-xs font-mono">{{ $project->backlinks->count() }} Kayıt</span>
                    </div>

                    <div class="flex flex-wrap items-center gap-4 w-full lg:w-auto">
                        <!-- Kontrol Paneli -->
                        <form id="bulkCheckForm" action="{{ route('projects.backlinks.bulk-check', $project) }}" method="POST" class="flex-1 lg:flex-none">
                            @csrf
                            <div class="join w-full shadow-lg">
                                <select name="filter" class="select select-bordered select-sm join-item bg-base-300 border-white/10 text-gray-200 focus:outline-none focus:border-blue-500 min-w-[160px] pl-4 pr-10">
                                    <option value="all">Tüm Linkler</option>
                                    <option value="broken">Sadece Kırıklar</option>
                                    <option value="active">Sadece Aktifler</option>
                                </select>
                                <button type="submit" class="btn btn-primary btn-sm join-item gap-2 px-6" id="bulkCheckButton">
                                    <i class="fas fa-sync-alt animate-spin-hover"></i> Kontrol Et
                                </button>
                            </div>
                        </form>

                        <!-- Export Dropdown -->
                        <div class="dropdown dropdown-end">
                            <label tabindex="0" class="btn btn-outline btn-secondary btn-sm gap-2">
                                <i class="fas fa-file-export"></i> Dışa Aktar
                                <i class="fas fa-chevron-down text-xs"></i>
                            </label>
                            <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow-2xl bg-[#1e293b] border border-white/10 rounded-xl w-52 mt-2">
                                <li><a href="{{ route('projects.backlinks.export', ['project' => $project, 'filter' => 'all']) }}" class="hover:bg-white/5 rounded-lg text-gray-300">Tümünü İndir</a></li>
                                <li><a href="{{ route('projects.backlinks.export', ['project' => $project, 'filter' => 'active']) }}" class="text-emerald-400 hover:bg-emerald-900/20 rounded-lg">Aktifleri İndir</a></li>
                                <li><a href="{{ route('projects.backlinks.export', ['project' => $project, 'filter' => 'broken']) }}" class="text-rose-400 hover:bg-rose-900/20 rounded-lg">Kırıkları İndir</a></li>
                            </ul>
                        </div>

                        <a href="{{ route('projects.backlinks.create', $project) }}" class="btn btn-primary btn-sm gap-2 shadow-lg shadow-blue-500/20">
                            <i class="fas fa-plus"></i> Yeni Ekle
                        </a>
                    </div>
                </div>
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
            @if($project->backlinks->isEmpty())
                <div class="p-12 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-base-300/50 mb-4">
                        <i class="fas fa-link text-3xl text-gray-600"></i>
                    </div>
                    <h3 class="text-lg font-medium text-white mb-1">Henüz backlink eklenmemiş</h3>
                    <p class="text-gray-500 mb-6">Projenizi takip etmek için ilk backlinkinizi ekleyin.</p>
                    <a href="{{ route('projects.backlinks.create', $project) }}" class="btn btn-primary btn-sm">Backlink Ekle</a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="table w-full">
                        <thead class="bg-base-200/50 text-white font-medium text-xs uppercase tracking-wider border-b border-white/5">
                            <tr>
                                <th class="py-4 pl-8 text-white">Hedef URL</th>
                                <th class="py-4 text-white">Durum</th>
                                <th class="py-4 text-white">Detaylar</th>
                                <th class="py-4 pr-8 text-right text-white">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm divide-y divide-white/5">
                            @foreach($project->backlinks as $backlink)
                                <tr class="group hover:bg-white/[0.02] transition-colors">
                                    <td class="pl-8 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded bg-base-300 flex items-center justify-center text-gray-500 group-hover:text-white transition-colors">
                                                <i class="fas fa-globe text-xs"></i>
                                            </div>
                                            <a href="{{ $backlink->target_url }}" target="_blank" class="font-medium text-white hover:text-blue-400 transition-colors truncate max-w-md block" title="{{ $backlink->target_url }}">
                                                {{ $backlink->target_url }}
                                            </a>
                                        </div>
                                    </td>
                                    <td class="py-4">
                                        @switch($backlink->status)
                                            @case('active')
                                                <span class="badge badge-success badge-lg font-semibold bg-emerald-500/10 text-emerald-400 border-emerald-500/20 gap-2 pl-1.5 pr-3 h-8 rounded-full">
                                                    <div class="w-2 h-2 rounded-full bg-emerald-400 shadow-[0_0_8px_rgba(52,211,153,0.8)]"></div> AKTİF
                                                </span>
                                                @break
                                            @case('broken')
                                                <span class="badge badge-error badge-lg font-semibold bg-rose-500/10 text-rose-400 border-rose-500/20 gap-2 pl-1.5 pr-3 h-8 rounded-full">
                                                    <div class="w-2 h-2 rounded-full bg-rose-400 shadow-[0_0_8px_rgba(244,63,94,0.8)]"></div> KIRIK
                                                </span>
                                                @break
                                            @case('pending')
                                                <span class="badge badge-warning badge-lg font-semibold bg-amber-500/10 text-amber-400 border-amber-500/20 gap-2 pl-1.5 pr-3 h-8 rounded-full">
                                                    <div class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></div> BEKLEMEDE
                                                </span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td class="py-4 text-gray-500">
                                        @php 
                                            $details = $backlink->details ? json_decode($backlink->details, true) : []; 
                                        @endphp
                                        
                                        <div class="flex flex-col gap-1 text-xs">
                                            @if($backlink->status === 'active')
                                                @if(isset($details['anchor_text']))
                                                    <div class="flex items-center gap-1.5" title="Anchor Text">
                                                        <i class="fas fa-quote-left text-emerald-400"></i>
                                                        <span class="text-white font-medium">{{ Str::limit($details['anchor_text'], 30) }}</span>
                                                    </div>
                                                @else
                                                    <div class="flex items-center gap-1.5 text-emerald-400">
                                                        <i class="fas fa-check text-xs"></i>
                                                        <span>Link bulundu</span>
                                                    </div>
                                                @endif
                                            @elseif($backlink->status === 'broken')
                                                @if(isset($details['error_reason']))
                                                    <div class="flex items-center gap-1.5 text-rose-400 bg-rose-400/10 px-2 py-1 rounded border border-rose-400/20" title="Hata Nedeni">
                                                        <i class="fas fa-exclamation-circle text-xs"></i>
                                                        <span class="font-medium">{{ Str::limit($details['error_reason'], 40) }}</span>
                                                    </div>
                                                @else
                                                    <div class="flex items-center gap-1.5 text-rose-400" title="Detay yok">
                                                        <i class="fas fa-times-circle text-xs"></i>
                                                        <span>Bağlantı hatası</span>
                                                    </div>
                                                @endif
                                            @else
                                                <span class="text-gray-600">-</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="pr-8 py-4 text-right">
                                        <div class="flex justify-end gap-2">
                                            <form action="{{ route('projects.backlinks.check', [$project, $backlink]) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-square bg-blue-500/10 hover:bg-blue-500 hover:text-white text-blue-400 border-0 transition-all tooltip tooltip-left flex items-center justify-center" data-tip="Kontrol Et">
                                                    <i class="fas fa-sync-alt"></i>
                                                </button>
                                            </form>
                                            <a href="{{ route('projects.backlinks.edit', [$project, $backlink]) }}" class="btn btn-sm btn-square bg-amber-500/10 hover:bg-amber-500 hover:text-white text-amber-400 border-0 transition-all tooltip tooltip-left flex items-center justify-center" data-tip="Düzenle">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                            <form action="{{ route('projects.backlinks.destroy', [$project, $backlink]) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-square bg-rose-500/10 hover:bg-rose-500 hover:text-white text-rose-400 border-0 transition-all tooltip tooltip-left flex items-center justify-center" data-tip="Sil" onclick="return confirm('Emin misiniz?')">
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
            <table class="table table-pin-rows w-full">
                <thead class="bg-[#1e293b] text-gray-400 text-xs uppercase">
                    <tr>
                        <th class="bg-[#1e293b]">Hedef URL</th>
                        <th class="bg-[#1e293b]">Durum</th>
                        <th class="bg-[#1e293b]">Sonuç</th>
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
        bulkCheckButton.disabled = true;
        bulkCheckButton.innerHTML = '<span class="loading loading-spinner loading-xs"></span> İşleniyor...';
        loadingIndicator.classList.remove('hidden');
        
        // startTime'ı burada sıfırlamıyoruz, sunucudan veya form submit'ten gelecek

        // Eğer önceki bir interval varsa temizle
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
                    
                    // Başlangıç zamanını sunucudan al
                    if (progressData.started_at) {
                        startTime = progressData.started_at;
                    }

                    // Tahmini süre hesapla
                    if (startTime && progressData.checked > 0 && progressData.total > 0) {
                        const elapsedTime = (Date.now() - startTime) / 1000; // saniye cinsinden geçen süre
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
                         // Henüz başlamadıysa veya startTime yoksa, toplam tahmini göster (sabit varsayım)
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

        // İlk kontrolü hemen yap
        poll();
        // Sonra periyodik olarak devam et
        pollingInterval = setInterval(poll, 2000);
    }

    function resetButton() {
        bulkCheckButton.disabled = false;
        bulkCheckButton.innerHTML = '<i class="fas fa-sync-alt"></i> Kontrol Et';
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
                <td class="font-mono text-gray-300 max-w-xs truncate" title="${result.target_url}">${result.target_url}</td>
                <td>${statusBadge}</td>
                <td class="${messageClass}">${message}</td>
            `;
            resultsTableBody.appendChild(row);
        });

        // İstatistikleri güncelle
        if (resultTotal) resultTotal.textContent = results.length;
        if (resultActive) resultActive.textContent = activeCount;
        if (resultBroken) resultBroken.textContent = brokenCount;

        loadingIndicator.classList.add('hidden');
        resetButton();
        resultsModal.showModal();
    }

    if (bulkCheckForm) {
        bulkCheckForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            bulkCheckButton.disabled = true;
            loadingIndicator.classList.remove('hidden');
            progressBar.style.width = `0%`;
            progressText.textContent = `Başlatılıyor...`;
            resultsTableBody.innerHTML = '';
            
            // Zamanlayıcıları sıfırla
            startTime = null;
            if (estimatedTimeText) estimatedTimeText.textContent = 'Tahmini Kalan Süre: Hesaplanıyor...';

            // CSRF token'ı al
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            // Seçili filtreyi al
            const formData = new FormData(this);
            const filter = formData.get('filter');

            // Toplu kontrol isteği
            fetch(this.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ filter: filter })
            })
            .then(response => response.json())
            .then(data => {
                if (!data.progress_id) {
                    throw new Error('progress_id alınamadı');
                }
                startPolling(data.project_id, data.progress_id);
            })
            .catch(error => {
                console.error('Hata:', error);
                alert(error.message || 'Backlink kontrolü başlatılamadı.');
                resetButton();
                loadingIndicator.classList.add('hidden');
            });
        });
    }

    // Modal kapatma butonu
    if (closeModalButton) {
        closeModalButton.addEventListener('click', function() {
            resultsModal.close();
            window.location.reload();
        });
    }

    // Sayfa yüklendiğinde devam eden işlem varsa polling'i başlat
    @if(isset($activeProgress) && $activeProgress)
        startPolling({{ $project->id }}, {{ $activeProgress->id }});
    @endif
});
</script>
@endpush
@endsection
