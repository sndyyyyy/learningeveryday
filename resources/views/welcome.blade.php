<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Learning Everyday - Platform Kuis Interaktif</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .glass-nav { background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(10px); }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 antialiased overflow-x-hidden">

    @include('layouts.navbar-guest')

    <section class="pt-12 pb-20 lg:pt-16 lg:pb-24 px-4 overflow-hidden relative">
        <div class="absolute top-0 right-0 -translate-y-12 translate-x-1/3 w-96 h-96 bg-indigo-300/30 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 translate-y-1/3 -translate-x-1/3 w-72 h-72 bg-blue-300/30 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-12 items-center relative z-10">
            <div class="space-y-6 text-center lg:text-left">
                <div class="inline-flex items-center px-3 py-1 rounded-full bg-indigo-50 border border-indigo-100 text-indigo-600 text-xs font-semibold tracking-wide">
                    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg> Platform Ujian Generasi Baru
                </div>
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-black text-gray-900 leading-tight tracking-tight">
                    Ukur Kemampuanmu dengan <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-blue-500">Lebih Cerdas</span>
                </h1>
                <p class="text-gray-500 text-sm md:text-base leading-relaxed max-w-xl mx-auto lg:mx-0">
                    Sistem ujian interaktif yang dirancang untuk mempermudah evaluasi belajar. Dilengkapi dengan penilaian otomatis, analisis hasil, dan pembahasan materi yang mendalam.
                </p>
                <div class="flex flex-col sm:flex-row gap-3 justify-center lg:justify-start pt-2">
                    <a href="{{ url('/trial-quiz') }}" class="bg-indigo-600 text-white font-semibold px-6 py-3.5 rounded-xl hover:bg-indigo-700 shadow-md hover:shadow-lg transition flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Coba Kuis Gratis
                    </a>
                </div>
            </div>
            
            <div class="relative mx-auto w-full max-w-md lg:max-w-none p-4">
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6 relative z-10 transform lg:rotate-2 hover:rotate-0 transition duration-500">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-4 mb-4">
                        <div class="flex space-x-2">
                            <div class="w-3 h-3 rounded-full bg-red-400"></div>
                            <div class="w-3 h-3 rounded-full bg-amber-400"></div>
                            <div class="w-3 h-3 rounded-full bg-green-400"></div>
                        </div>
                        <div class="text-[10px] font-semibold text-gray-400">Kuis Sedang Berjalan</div>
                    </div>
                    <div class="space-y-3">
                        <div class="h-4 bg-gray-100 rounded w-3/4"></div>
                        <div class="h-4 bg-gray-100 rounded w-1/2"></div>
                        <div class="mt-6 space-y-2">
                            <div class="h-10 bg-indigo-50 border border-indigo-100 rounded-lg w-full flex items-center px-3"><div class="w-4 h-4 rounded-full border-2 border-indigo-300 mr-2"></div><div class="h-2 bg-indigo-200 rounded w-1/3"></div></div>
                            <div class="h-10 bg-gray-50 border border-gray-100 rounded-lg w-full flex items-center px-3"><div class="w-4 h-4 rounded-full border-2 border-gray-300 mr-2"></div><div class="h-2 bg-gray-200 rounded w-1/2"></div></div>
                        </div>
                    </div>
                </div>
                <div class="absolute -bottom-6 -left-6 bg-amber-100 p-4 rounded-xl shadow-lg border border-amber-200 z-20 animate-bounce" style="animation-duration: 3s;">
                    <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
        </div>
    </section>

    <section class="py-20 bg-white">
        <div class="max-w-6xl mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 tracking-tight">Fitur Unggulan Platform</h2>
                <div class="w-16 h-1 bg-indigo-600 mx-auto mt-4 rounded"></div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="p-6 rounded-2xl bg-gray-50 border border-gray-100 hover:shadow-md transition">
                    <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Evaluasi Rumpang & PG</h3>
                    <p class="text-sm text-gray-500 leading-relaxed">Mendukung format ujian cerdas dari pilihan ganda otomatis hingga isian rumpang yang terkalkulasi.</p>
                </div>
                <div class="p-6 rounded-2xl bg-gray-50 border border-gray-100 hover:shadow-md transition">
                    <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Penilaian Instan</h3>
                    <p class="text-sm text-gray-500 leading-relaxed">Hasil kuis langsung dihitung secara otomatis dengan pembobotan skor yang adil dan akurat.</p>
                </div>
                <div class="p-6 rounded-2xl bg-gray-50 border border-gray-100 hover:shadow-md transition">
                    <div class="w-12 h-12 bg-purple-100 text-purple-600 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Pembahasan Multimedia</h3>
                    <p class="text-sm text-gray-500 leading-relaxed">Kuis terintegrasi dengan materi pembahasan teks maupun video untuk memperdalam pemahaman.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="tentang-kami" class="py-20 bg-indigo-50 border-t border-indigo-100">
        <div class="max-w-4xl mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold text-indigo-900 tracking-tight mb-6">Tentang Learning Everyday</h2>
            <p class="text-sm md:text-base text-indigo-700 leading-relaxed max-w-2xl mx-auto">
                Kami berkomitmen untuk menyediakan pengalaman evaluasi pembelajaran yang modern, cepat, dan anti ribet. Dibangun dengan teknologi terkini, kami percaya bahwa ujian tidak harus membosankan, melainkan harus menjadi bagian dari proses belajar yang menyenangkan dan interaktif.
            </p>
        </div>
    </section>

    <footer class="bg-gray-900 text-gray-400 py-10">
        <div class="max-w-6xl mx-auto px-4 grid grid-cols-1 md:grid-cols-2 gap-8 items-center border-b border-gray-800 pb-8 mb-8">
            <div class="flex items-center gap-2">
            <img src="{{ asset('images/le.jpg') }}" alt="Logo" 
                 onerror="this.onerror=null; this.src='https://placehold.co/40x40/4f46e5/white?text=LE';" 
                 class="w-7 h-7 object-contain rounded-lg shadow-sm" />
                <span class="font-bold text-white tracking-tight">learning<span class="text-indigo-400">everyday</span></span>
            </div>
            <div class="flex md:justify-end space-x-6 text-sm">
                <a href="https://instagram.com/learningeverydaywithmisteri/" class="hover:text-white transition">Instagram</a>
                <a href="https://www.tiktok.com/@learning_every.day" class="hover:text-white transition">Tiktok</a>
                <a href="#" class="hover:text-white transition">Bantuan</a>
            </div>
        </div>
        <div class="text-center text-xs">
            &copy; 2026 Learning Everyday. Hak Cipta Dilindungi.
        </div>
    </footer>

</body>
</html>