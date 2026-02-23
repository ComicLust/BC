@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Yeni Proje</h1>
            <a href="{{ route('projects.index') }}" class="btn btn-ghost">
                Geri Dön
            </a>
        </div>

        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <form action="{{ route('projects.store') }}" method="POST">
                    @csrf

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Proje Adı</span>
                        </label>
                        <input type="text" name="name" class="input input-bordered @error('name') input-error @enderror" value="{{ old('name') }}" required>
                        @error('name')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>

                    <div class="form-control mt-4">
                        <label class="label">
                            <span class="label-text">Hedef URL</span>
                        </label>
                        <input type="url" name="target_url" class="input input-bordered @error('target_url') input-error @enderror" value="{{ old('target_url') }}" required>
                        @error('target_url')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>

                    <div class="form-control mt-6">
                        <button type="submit" class="btn btn-primary">Proje Oluştur</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 