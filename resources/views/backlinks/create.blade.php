@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-12 flex justify-center items-center min-h-[80vh]">
    <div class="w-full max-w-xl">
        <!-- Geri Dön Butonu -->
        <div class="mb-6">
            <a href="{{ route('projects.show', $project) }}" class="inline-flex items-center text-gray-400 hover:text-white transition-colors group">
                <i class="fas fa-arrow-left mr-2 group-hover:-translate-x-1 transition-transform"></i>
                <span class="text-sm font-medium">Projeye Dön</span>
            </a>
        </div>

        <div class="card bg-[#1e293b] border border-white/5 shadow-2xl overflow-hidden relative group">
            <!-- Glow Efekti -->
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500"></div>
            
            <div class="card-body p-8">
                <div class="mb-8 text-center">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500/20 to-purple-500/20 text-blue-400 mb-4 border border-white/5 shadow-lg shadow-blue-500/10">
                        <i class="fas fa-link text-xl"></i>
                    </div>
                    <h1 class="text-2xl font-bold text-white mb-2">Yeni Backlink Ekle</h1>
                    <p class="text-gray-400 text-sm">Takip edilecek bağlantıları aşağıya ekleyin.</p>
                </div>

                <form action="{{ route('projects.backlinks.store', $project) }}" method="POST">
                    @csrf

                    <!-- Proje URL (Read Only) -->
                    <div class="form-control mb-6 group/input">
                        <label class="label pl-0 pt-0 pb-2">
                            <span class="label-text text-gray-300 font-medium text-sm flex items-center gap-2">
                                <i class="fas fa-bullseye text-blue-400"></i> Hedef Proje URL
                            </span>
                        </label>
                        <div class="relative">
                            <input type="text" 
                                class="input w-full bg-[#0f172a] border border-white/10 text-gray-400 focus:outline-none cursor-not-allowed pl-10 h-12 text-sm font-mono" 
                                value="{{ $project->target_url }}" 
                                disabled>
                            <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-600">
                                <i class="fas fa-lock text-xs"></i>
                            </div>
                        </div>
                        <span class="text-xs text-gray-500 mt-2 block pl-1">
                            <i class="fas fa-info-circle mr-1"></i> Bu backlinklerin işaret edeceği proje adresidir.
                        </span>
                    </div>

                    <!-- Hedef URL'ler (Textarea) -->
                    <div class="form-control mb-8">
                        <label class="label pl-0 pt-0 pb-2 justify-between items-end">
                            <span class="label-text text-gray-300 font-medium text-sm flex items-center gap-2">
                                <i class="fas fa-list-ul text-purple-400"></i> Backlink Kaynakları
                            </span>
                            <span class="text-[10px] text-gray-500 bg-white/5 px-2 py-1 rounded border border-white/5">
                                Her satıra 1 link
                            </span>
                        </label>
                        
                        <div class="relative group/textarea">
                            <div class="absolute left-0 top-0 bottom-0 w-10 bg-[#0f172a] border-r border-white/5 rounded-l-lg flex flex-col items-center pt-3 gap-[2px] overflow-hidden text-[10px] font-mono text-gray-600 select-none z-10" id="lineNumbers">
                                1
                            </div>
                            <textarea name="target_urls" 
                                id="urlsTextarea"
                                class="textarea w-full h-48 bg-[#1e293b] border border-white/10 text-white focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500/50 pl-12 pt-3 text-sm font-mono leading-[1.3rem] resize-none transition-all placeholder:text-gray-600" 
                                required
                                placeholder="https://ornek-site.com/makale-1&#10;https://baska-site.com/haber-2&#10;https://blog-sitesi.com/yazi-3"
                                oninput="updateLineNumbers(this)"></textarea>
                        </div>
                        
                        @error('target_urls')
                            <div class="flex items-center gap-2 mt-2 text-rose-400 text-xs bg-rose-500/10 p-2 rounded border border-rose-500/20">
                                <i class="fas fa-exclamation-triangle"></i>
                                <span>{{ $message }}</span>
                            </div>
                        @enderror

                        <div class="flex items-start gap-3 mt-3 p-3 bg-blue-500/5 rounded-lg border border-blue-500/10">
                            <i class="fas fa-lightbulb text-blue-400 text-sm mt-0.5"></i>
                            <div class="text-xs text-gray-400 leading-relaxed">
                                <strong class="text-gray-300 block mb-1">İpucu</strong>
                                Aynı anda birden fazla link ekleyebilirsiniz. Linkleri kopyalayıp buraya yapıştırmanız yeterlidir. Sistem otomatik olarak satırları ayıracaktır.
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary w-full h-12 text-base font-medium shadow-lg shadow-blue-500/25 hover:shadow-blue-500/40 border-none bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-500 hover:to-purple-500 transition-all transform hover:-translate-y-0.5">
                        <i class="fas fa-plus-circle mr-2"></i> Backlinkleri Sisteme Ekle
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function updateLineNumbers(textarea) {
        const lines = textarea.value.split('\n').length;
        const lineNumbersEle = document.getElementById('lineNumbers');
        const currentLines = lineNumbersEle.children.length; // Basitçe satır sayısını metin olarak değil element olarak saymıyoruz ama burada metin olarak güncelleyeceğiz.
        
        // Daha performanslı bir yöntem: HTML string oluşturup basmak
        let html = '';
        // En az textarea yüksekliği kadar satır numarası gösterelim (örneğin 15 satır)
        const minLines = Math.max(lines, 15);
        
        for (let i = 1; i <= minLines; i++) {
            html += `<div class="h-[1.3rem] leading-[1.3rem] w-full text-center ${i > lines ? 'text-transparent' : ''}">${i}</div>`;
        }
        lineNumbersEle.innerHTML = html;
    }
    
    // Sayfa yüklendiğinde çalıştır
    document.addEventListener('DOMContentLoaded', function() {
        updateLineNumbers(document.getElementById('urlsTextarea'));
    });
</script>
@endpush
@endsection 