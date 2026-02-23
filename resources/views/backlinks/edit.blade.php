@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Backlink Düzenle</h1>
            <a href="{{ route('projects.show', $project) }}" class="btn btn-ghost btn-sm">
                Geri Dön
            </a>
        </div>

        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <form action="{{ route('projects.backlinks.update', [$project, $backlink]) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text">Kaynak URL</span>
                        </label>
                        <input type="url" name="source_url" class="input input-bordered" required
                            placeholder="https://example.com/page" value="{{ old('source_url', $backlink->source_url) }}">
                        @error('source_url')
                            <span class="text-error text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text">Hedef URL</span>
                        </label>
                        <input type="url" name="target_url" class="input input-bordered" required
                            placeholder="https://example.com/target" value="{{ old('target_url', $backlink->target_url) }}">
                        @error('target_url')
                            <span class="text-error text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text">Durum</span>
                        </label>
                        <select name="status" class="select select-bordered" required>
                            <option value="active" {{ old('status', $backlink->status) == 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="broken" {{ old('status', $backlink->status) == 'broken' ? 'selected' : '' }}>Kırık</option>
                            <option value="pending" {{ old('status', $backlink->status) == 'pending' ? 'selected' : '' }}>Beklemede</option>
                        </select>
                        @error('status')
                            <span class="text-error text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="btn btn-primary">Backlink Güncelle</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 