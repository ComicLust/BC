<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Backlink Takip Sistemi</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%);
        }
        .feature-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .hover-lift {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .hover-lift:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
    </style>
</head>
<body class="antialiased">
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
        <!-- Navigation -->
        <nav class="bg-white/80 backdrop-blur-md shadow-lg fixed w-full z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="flex-shrink-0 flex items-center">
                            <h1 class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">Backlink Takip</h1>
                        </div>
                    </div>
                    <div class="flex items-center">
                        @if (Route::has('login'))
                            <div class="space-x-4">
                                @auth
                                    <a href="{{ url('/dashboard') }}" class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition-all duration-300 transform hover:-translate-y-0.5">
                                        Dashboard
                                    </a>
                                @else
                                    <a href="{{ route('login') }}" class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition-all duration-300 transform hover:-translate-y-0.5">
                                        Giriş Yap
                                    </a>
                                @endauth
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <div class="relative pt-32 pb-20 gradient-bg overflow-hidden">
            <div class="absolute inset-0 bg-grid-white/[0.05] bg-[size:20px_20px]"></div>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
                <div class="text-center">
                    <h2 class="text-5xl font-extrabold text-white sm:text-6xl sm:tracking-tight lg:text-7xl mb-6">
                        Backlink Takip Sistemi
                    </h2>
                    <p class="mt-5 max-w-xl mx-auto text-xl text-white/90">
                        Backlinklerinizi kolayca takip edin, kırık linkleri tespit edin ve SEO performansınızı artırın.
                    </p>
                </div>
            </div>
        </div>

        <!-- Features Section -->
        <div class="py-20 -mt-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                    <!-- Feature 1 -->
                    <div class="feature-card p-8 rounded-2xl shadow-xl hover-lift">
                        <div class="text-center">
                            <div class="w-16 h-16 mx-auto mb-6 rounded-full bg-gradient-to-r from-indigo-500 to-purple-500 flex items-center justify-center text-3xl text-white">🔍</div>
                            <h3 class="text-xl font-bold mb-4 text-gray-900">Otomatik Kontrol</h3>
                            <p class="text-gray-600">Backlinklerinizi otomatik olarak kontrol edin ve kırık linkleri tespit edin.</p>
                        </div>
                    </div>

                    <!-- Feature 2 -->
                    <div class="feature-card p-8 rounded-2xl shadow-xl hover-lift">
                        <div class="text-center">
                            <div class="w-16 h-16 mx-auto mb-6 rounded-full bg-gradient-to-r from-indigo-500 to-purple-500 flex items-center justify-center text-3xl text-white">📊</div>
                            <h3 class="text-xl font-bold mb-4 text-gray-900">Detaylı Raporlar</h3>
                            <p class="text-gray-600">Backlink performansınızı detaylı raporlarla analiz edin.</p>
                        </div>
                    </div>

                    <!-- Feature 3 -->
                    <div class="feature-card p-8 rounded-2xl shadow-xl hover-lift">
                        <div class="text-center">
                            <div class="w-16 h-16 mx-auto mb-6 rounded-full bg-gradient-to-r from-indigo-500 to-purple-500 flex items-center justify-center text-3xl text-white">🔔</div>
                            <h3 class="text-xl font-bold mb-4 text-gray-900">Anlık Bildirimler</h3>
                            <p class="text-gray-600">Kırık linkler için anında bildirim alın ve hızlıca müdahale edin.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="bg-white">
            <div class="max-w-7xl mx-auto py-20 px-4 sm:px-6 lg:px-8">
                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-3xl shadow-xl overflow-hidden">
                    <div class="px-6 py-16 sm:p-16 lg:flex lg:items-center lg:justify-between">
                        <div class="lg:w-0 lg:flex-1">
                            <h2 class="text-3xl font-extrabold tracking-tight text-white sm:text-4xl">
                                <span class="block">Hemen Başlayın</span>
                                <span class="block text-white/90">Backlinklerinizi takip etmeye başlayın.</span>
                            </h2>
                        </div>
                        <div class="mt-8 flex lg:mt-0 lg:flex-shrink-0">
                            <div class="inline-flex rounded-lg shadow">
                                <a href="{{ route('login') }}" class="inline-flex items-center px-8 py-4 border border-transparent text-base font-medium rounded-lg text-indigo-600 bg-white hover:bg-gray-50 transition-colors duration-300">
                                    Giriş Yap
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="bg-gray-900">
            <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
                <div class="text-center text-gray-400">
                    <p>&copy; {{ date('Y') }} Backlink Takip Sistemi. Tüm hakları saklıdır.</p>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>
