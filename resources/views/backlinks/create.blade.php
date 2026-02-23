@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Yeni Backlink Ekle</h1>
            <a href="{{ route('projects.show', $project) }}" class="btn btn-ghost btn-sm">
                Geri Dön
            </a>
        </div>

        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <form action="{{ route('projects.backlinks.store', $project) }}" method="POST">
                    @csrf

                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text">Kaynak URL</span>
                        </label>
                        <input type="text" class="input input-bordered" value="{{ $project->target_url }}" disabled>
                        <span class="text-sm text-gray-500 mt-1">Bu, projenizin hedef URL'sidir.</span>
                    </div>

                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text">Hedef URL'ler</span>
                        </label>
                        <textarea name="target_urls" class="textarea textarea-bordered h-32" required
                            placeholder="Her satıra bir URL gelecek şekilde hedef URL'leri girin&#10;Örnek:&#10;https://example.com/page1&#10;https://example.com/page2"></textarea>
                        @error('target_urls')
                            <span class="text-error text-sm mt-1">{{ $message }}</span>
                        @enderror
                        <span class="text-sm text-gray-500 mt-1">Her satıra bir URL gelecek şekilde hedef URL'leri girin.</span>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="btn btn-primary">Backlinkleri Ekle</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 