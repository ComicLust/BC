@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Kullanıcı Düzenle</h1>
        <a href="{{ route('users.index') }}" class="btn btn-ghost">
            Geri Dön
        </a>
    </div>

    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <form action="{{ route('users.update', $user) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Ad</span>
                    </label>
                    <input type="text" name="name" class="input input-bordered" value="{{ $user->name }}" required>
                </div>
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">E-posta</span>
                    </label>
                    <input type="email" name="email" class="input input-bordered" value="{{ $user->email }}" required>
                </div>
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Şifre (Boş bırakılırsa değişmez)</span>
                    </label>
                    <input type="password" name="password" class="input input-bordered">
                </div>
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Şifre Tekrar</span>
                    </label>
                    <input type="password" name="password_confirmation" class="input input-bordered">
                </div>
                <div class="form-control">
                    <label class="label cursor-pointer">
                        <span class="label-text">Admin</span>
                        <input type="checkbox" name="is_admin" class="checkbox" {{ $user->is_admin ? 'checked' : '' }}>
                    </label>
                </div>
                <div class="form-control mt-6">
                    <button type="submit" class="btn btn-primary">Güncelle</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 