@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">Ayarlar</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Profil Ayarları -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title mb-4">Profil Ayarları</h2>
                <form action="{{ route('settings.profile') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text">Ad Soyad</span>
                        </label>
                        <input type="text" name="name" value="{{ auth()->user()->name }}" class="input input-bordered" required>
                    </div>

                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text">E-posta</span>
                        </label>
                        <input type="email" name="email" value="{{ auth()->user()->email }}" class="input input-bordered" required>
                    </div>

                    <div class="form-control">
                        <button type="submit" class="btn btn-primary">Profili Güncelle</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Şifre Değiştirme -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title mb-4">Şifre Değiştirme</h2>
                <form action="{{ route('settings.password') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text">Mevcut Şifre</span>
                        </label>
                        <input type="password" name="current_password" class="input input-bordered" required>
                    </div>

                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text">Yeni Şifre</span>
                        </label>
                        <input type="password" name="password" class="input input-bordered" required>
                    </div>

                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text">Yeni Şifre (Tekrar)</span>
                        </label>
                        <input type="password" name="password_confirmation" class="input input-bordered" required>
                    </div>

                    <div class="form-control">
                        <button type="submit" class="btn btn-primary">Şifreyi Değiştir</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Bildirim Ayarları -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title mb-4">Bildirim Ayarları</h2>
                <form action="{{ route('settings.notifications') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-control mb-4">
                        <label class="label cursor-pointer">
                            <span class="label-text">E-posta Bildirimleri</span>
                            <input type="checkbox" name="email_notifications" class="toggle toggle-primary" {{ auth()->user()->email_notifications ? 'checked' : '' }}>
                        </label>
                    </div>

                    <div class="form-control">
                        <button type="submit" class="btn btn-primary">Bildirimleri Güncelle</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- E-posta Ayarları -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title mb-4">E-posta Ayarları</h2>
                <p class="mb-4">SMTP ayarlarını yapılandırın ve e-posta şablonlarını düzenleyin.</p>
                <a href="{{ route('settings.email') }}" class="btn btn-primary">E-posta Ayarlarını Düzenle</a>
            </div>
        </div>
    </div>

    <div class="card bg-base-100 shadow-xl mt-6">
        <div class="card-body">
            <h2 class="card-title mb-4">Otomatik Backlink Kontrol Sıklığı</h2>
            <form action="{{ route('settings.updateSchedule') }}" method="POST">
                @csrf
                <label class="label">Backlink Kontrol Sıklığı:</label>
                <select name="backlink_check_frequency" class="input input-bordered mb-4">
                    <option value="daily" {{ (\App\Models\Setting::get('backlink_check_frequency', 'weekly') == 'daily') ? 'selected' : '' }}>Günlük</option>
                    <option value="weekly" {{ (\App\Models\Setting::get('backlink_check_frequency', 'weekly') == 'weekly') ? 'selected' : '' }}>Haftalık</option>
                    <option value="monthly" {{ (\App\Models\Setting::get('backlink_check_frequency', 'weekly') == 'monthly') ? 'selected' : '' }}>Aylık</option>
                </select>
                <button type="submit" class="btn btn-primary">Kaydet</button>
            </form>
        </div>
    </div>
</div>
@endsection 