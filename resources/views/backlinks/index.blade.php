@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">{{ $project->name }} - Backlinkler</h1>
        <div class="flex space-x-4">
            <button class="btn btn-primary" onclick="document.getElementById('addBacklinkModal').showModal()">
                Yeni Backlink
            </button>
            <button class="btn btn-secondary" onclick="document.getElementById('bulkAddModal').showModal()">
                Toplu Ekle
            </button>
        </div>
    </div>

    <!-- Arama ve Filtreleme -->
    <div class="card bg-base-100 shadow-xl mb-6">
        <div class="card-body">
            <form action="{{ route('projects.backlinks.index', $project) }}" method="GET" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="form-control">
                        <input type="text" name="search" class="input input-bordered" placeholder="Ara..." value="{{ request('search') }}">
                    </div>
                    <div class="form-control">
                        <select name="status" class="select select-bordered">
                            <option value="">Tüm Durumlar</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="broken" {{ request('status') === 'broken' ? 'selected' : '' }}>Kırık</option>
                        </select>
                    </div>
                    <div class="form-control">
                        <input type="date" name="date" class="input input-bordered" value="{{ request('date') }}">
                    </div>
                    <div class="form-control">
                        <button type="submit" class="btn btn-primary">Filtrele</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Backlink Listesi -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'url', 'direction' => request('sort') === 'url' && request('direction') === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center space-x-1">
                                    <span>URL</span>
                                    @if(request('sort') === 'url')
                                        <span>{{ request('direction') === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </a>
                            </th>
                            <th>
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'source_url', 'direction' => request('sort') === 'source_url' && request('direction') === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center space-x-1">
                                    <span>Kaynak URL</span>
                                    @if(request('sort') === 'source_url')
                                        <span>{{ request('direction') === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </a>
                            </th>
                            <th>
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'anchor_text', 'direction' => request('sort') === 'anchor_text' && request('direction') === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center space-x-1">
                                    <span>Çapa Metni</span>
                                    @if(request('sort') === 'anchor_text')
                                        <span>{{ request('direction') === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </a>
                            </th>
                            <th>
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'status', 'direction' => request('sort') === 'status' && request('direction') === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center space-x-1">
                                    <span>Durum</span>
                                    @if(request('sort') === 'status')
                                        <span>{{ request('direction') === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </a>
                            </th>
                            <th>
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'direction' => request('sort') === 'created_at' && request('direction') === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center space-x-1">
                                    <span>Oluşturulma Tarihi</span>
                                    @if(request('sort') === 'created_at')
                                        <span>{{ request('direction') === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </a>
                            </th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($backlinks as $backlink)
                        <tr>
                            <td>{{ $backlink->url }}</td>
                            <td>{{ $backlink->source_url }}</td>
                            <td>{{ $backlink->anchor_text }}</td>
                            <td>
                                <span class="badge badge-{{ $backlink->status === 'active' ? 'success' : 'error' }}">
                                    {{ $backlink->status }}
                                </span>
                            </td>
                            <td>{{ $backlink->created_at->format('d.m.Y H:i') }}</td>
                            <td>
                                <div class="flex space-x-2">
                                    <form action="{{ route('projects.backlinks.check', [$project, $backlink]) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-primary">
                                            Kontrol Et
                                        </button>
                                    </form>
                                    <form action="{{ route('projects.backlinks.destroy', [$project, $backlink]) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-error" onclick="return confirm('Bu backlinki silmek istediğinizden emin misiniz?')">
                                            Sil
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $backlinks->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Yeni Backlink Modal -->
<dialog id="addBacklinkModal" class="modal">
    <div class="modal-box">
        <h3 class="font-bold text-lg mb-4">Yeni Backlink Ekle</h3>
        <form action="{{ route('projects.backlinks.store', $project) }}" method="POST">
            @csrf
            <div class="form-control">
                <label class="label">
                    <span class="label-text">URL</span>
                </label>
                <input type="url" name="url" class="input input-bordered" required>
            </div>
            <div class="form-control">
                <label class="label">
                    <span class="label-text">Kaynak URL</span>
                </label>
                <input type="url" name="source_url" class="input input-bordered" required>
            </div>
            <div class="form-control">
                <label class="label">
                    <span class="label-text">Çapa Metni</span>
                </label>
                <input type="text" name="anchor_text" class="input input-bordered" required>
            </div>
            <div class="modal-action">
                <button type="submit" class="btn btn-primary">Ekle</button>
                <button type="button" class="btn" onclick="document.getElementById('addBacklinkModal').close()">İptal</button>
            </div>
        </form>
    </div>
</dialog>

<!-- Toplu Ekleme Modal -->
<dialog id="bulkAddModal" class="modal">
    <div class="modal-box">
        <h3 class="font-bold text-lg mb-4">Toplu Backlink Ekle</h3>
        <form action="{{ route('projects.backlinks.bulk.store', $project) }}" method="POST">
            @csrf
            <div class="form-control">
                <label class="label">
                    <span class="label-text">Backlinkler (Her satıra bir backlink)</span>
                </label>
                <textarea name="backlinks" class="textarea textarea-bordered h-32" required placeholder="URL, Kaynak URL, Çapa Metni"></textarea>
            </div>
            <div class="modal-action">
                <button type="submit" class="btn btn-primary">Ekle</button>
                <button type="button" class="btn" onclick="document.getElementById('bulkAddModal').close()">İptal</button>
            </div>
        </form>
    </div>
</dialog>
@endsection 