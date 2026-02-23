@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">E-posta Ayarları</h1>

    @if(session('success'))
        <div class="alert alert-success mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- SMTP Ayarları -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title mb-4">SMTP Ayarları</h2>
                <form action="{{ route('settings.email.update') }}" method="POST">
                    @csrf
                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text">Mail Driver</span>
                        </label>
                        <input type="text" name="mail_mailer" value="{{ $settings->mail_mailer }}" class="input input-bordered" required>
                    </div>

                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text">SMTP Host</span>
                        </label>
                        <input type="text" name="mail_host" value="{{ $settings->mail_host }}" class="input input-bordered" required>
                    </div>

                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text">SMTP Port</span>
                        </label>
                        <input type="text" name="mail_port" value="{{ $settings->mail_port }}" class="input input-bordered" required>
                    </div>

                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text">SMTP Username</span>
                        </label>
                        <input type="text" name="mail_username" value="{{ $settings->mail_username }}" class="input input-bordered" required>
                    </div>

                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text">SMTP Password</span>
                        </label>
                        <input type="password" name="mail_password" value="{{ $settings->mail_password }}" class="input input-bordered" required>
                    </div>

                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text">Encryption</span>
                        </label>
                        <input type="text" name="mail_encryption" value="{{ $settings->mail_encryption }}" class="input input-bordered" required>
                    </div>

                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text">From Address</span>
                        </label>
                        <input type="email" name="mail_from_address" value="{{ $settings->mail_from_address }}" class="input input-bordered" required>
                    </div>

                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text">From Name</span>
                        </label>
                        <input type="text" name="mail_from_name" value="{{ $settings->mail_from_name }}" class="input input-bordered" required>
                    </div>

                    <div class="form-control">
                        <button type="submit" class="btn btn-primary">Ayarları Kaydet</button>
                    </div>
                </form>

                <!-- Test E-postası -->
                <div class="divider">Test E-postası</div>
                <form action="{{ route('settings.email.test') }}" method="POST" class="mt-4">
                    @csrf
                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text">Test E-posta Adresi</span>
                        </label>
                        <input type="email" name="email" class="input input-bordered" required>
                    </div>
                    <div class="form-control">
                        <button type="submit" class="btn btn-info">Test E-postası Gönder</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- E-posta Şablonları -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title mb-4">E-posta Şablonları</h2>
                @foreach($templates as $template)
                    <div class="collapse collapse-arrow bg-base-200 mb-4">
                        <input type="checkbox" /> 
                        <div class="collapse-title text-xl font-medium">
                            {{ $template->name }}
                        </div>
                        <div class="collapse-content">
                            <form action="{{ route('settings.email.template.update', $template) }}" method="POST">
                                @csrf
                                <div class="form-control mb-4">
                                    <label class="label">
                                        <span class="label-text">Şablon Adı</span>
                                    </label>
                                    <input type="text" name="name" value="{{ $template->name }}" class="input input-bordered" required>
                                </div>

                                <div class="form-control mb-4">
                                    <label class="label">
                                        <span class="label-text">Konu</span>
                                    </label>
                                    <input type="text" name="subject" value="{{ $template->subject }}" class="input input-bordered" required>
                                </div>

                                <div class="form-control mb-4">
                                    <label class="label">
                                        <span class="label-text">İçerik</span>
                                    </label>
                                    <textarea name="body" class="textarea textarea-bordered h-32" required>{{ $template->body }}</textarea>
                                    <label class="label">
                                        <span class="label-text-alt">Kullanılabilir değişkenler: {project_name}, {backlink_url}, {status}, {details}, {app_name}</span>
                                    </label>
                                </div>

                                <div class="form-control">
                                    <button type="submit" class="btn btn-primary">Şablonu Güncelle</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection 