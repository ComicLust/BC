@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">API Dokümantasyonu</h1>

    <div class="space-y-8">
        <!-- Kimlik Doğrulama -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title">Kimlik Doğrulama</h2>
                <p class="mb-4">API'yi kullanmak için önce bir API token'ı almanız gerekiyor. Token'ı almak için:</p>
                <div class="bg-base-200 p-4 rounded-lg">
                    <pre><code>POST /api/tokens
{
    "email": "your@email.com",
    "password": "your-password"
}</code></pre>
                </div>
                <p class="mt-4">Başarılı yanıt:</p>
                <div class="bg-base-200 p-4 rounded-lg">
                    <pre><code>{
    "token": "your-api-token"
}</code></pre>
                </div>
                <p class="mt-4">Bu token'ı tüm API isteklerinde <code>Authorization: Bearer your-api-token</code> header'ı ile göndermelisiniz.</p>
            </div>
        </div>

        <!-- Backlink Kontrolü -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title">Backlink Kontrolü</h2>
                <p class="mb-4">Bir backlink'in durumunu kontrol etmek için:</p>
                <div class="bg-base-200 p-4 rounded-lg">
                    <pre><code>POST /api/check-backlink
{
    "url": "https://example.com/backlink"
}</code></pre>
                </div>
                <p class="mt-4">Başarılı yanıt:</p>
                <div class="bg-base-200 p-4 rounded-lg">
                    <pre><code>{
    "status": "active",
    "status_code": 200,
    "headers": {
        "content-type": "text/html",
        "server": "nginx"
    }
}</code></pre>
                </div>
            </div>
        </div>

        <!-- Metrik Alma -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title">Metrik Alma</h2>
                <p class="mb-4">Bir URL'nin metriklerini almak için:</p>
                <div class="bg-base-200 p-4 rounded-lg">
                    <pre><code>POST /api/get-metrics
{
    "url": "https://example.com"
}</code></pre>
                </div>
                <p class="mt-4">Başarılı yanıt:</p>
                <div class="bg-base-200 p-4 rounded-lg">
                    <pre><code>{
    "domain_authority": 45,
    "page_authority": 38,
    "spam_score": 2,
    "moz_rank": 4
}</code></pre>
                </div>
            </div>
        </div>

        <!-- Proje Senkronizasyonu -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title">Proje Senkronizasyonu</h2>
                <p class="mb-4">Bir projenin tüm backlinklerini ve metriklerini senkronize etmek için:</p>
                <div class="bg-base-200 p-4 rounded-lg">
                    <pre><code>POST /api/projects/{project}/sync</code></pre>
                </div>
                <p class="mt-4">Başarılı yanıt:</p>
                <div class="bg-base-200 p-4 rounded-lg">
                    <pre><code>{
    "message": "Proje başarıyla senkronize edildi",
    "backlinks_updated": 10,
    "metrics": [
        {
            "domain_authority": 45,
            "page_authority": 38,
            "spam_score": 2,
            "moz_rank": 4
        }
    ]
}</code></pre>
                </div>
            </div>
        </div>

        <!-- Hata Kodları -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title">Hata Kodları</h2>
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Kod</th>
                                <th>Açıklama</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>400</td>
                                <td>Geçersiz istek</td>
                            </tr>
                            <tr>
                                <td>401</td>
                                <td>Kimlik doğrulama gerekli</td>
                            </tr>
                            <tr>
                                <td>403</td>
                                <td>Erişim reddedildi</td>
                            </tr>
                            <tr>
                                <td>404</td>
                                <td>Kaynak bulunamadı</td>
                            </tr>
                            <tr>
                                <td>429</td>
                                <td>Çok fazla istek</td>
                            </tr>
                            <tr>
                                <td>500</td>
                                <td>Sunucu hatası</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 