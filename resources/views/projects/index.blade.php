@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Projelerim</h1>
        <a href="{{ route('projects.create') }}" class="btn btn-primary">
            Yeni Proje
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($projects as $project)
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <h2 class="card-title">{{ $project->name }}</h2>
                    <p class="text-sm text-gray-500">{{ $project->target_url }}</p>
                    <div class="flex items-center mt-2">
                        <span class="badge badge-primary">{{ $project->backlinks_count }} Backlink</span>
                    </div>
                    <div class="card-actions justify-end mt-4">
                        <a href="{{ route('projects.show', $project) }}" class="btn btn-primary btn-sm">
                            Detay
                        </a>
                        <a href="{{ route('projects.edit', $project) }}" class="btn btn-ghost btn-sm">
                            Düzenle
                        </a>
                        <form action="{{ route('projects.destroy', $project) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-error btn-sm" onclick="return confirm('Bu projeyi silmek istediğinizden emin misiniz?')">
                                Sil
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div class="alert alert-info">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>Henüz proje oluşturmadınız.</span>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection 