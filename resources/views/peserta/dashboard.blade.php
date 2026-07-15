<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peserta - Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        @keyframes wave {
            0%, 100% { transform: rotate(0deg); }
            25% { transform: rotate(-15deg); }
            75% { transform: rotate(15deg); }
        }
        .animate-wave {
            display: inline-block;
            transform-origin: bottom center;
            animation: wave 1.5s ease-in-out infinite;
        }
    </style>
</head>
<body class="bg-gray-100 pb-16">

    @include('layouts.navbar-peserta')

    <!-- Banner Session Alert Flashing -->
    <div class="max-w-6xl mx-auto mt-6 px-4">
        @if(session('success'))
            <div class="bg-green-100 text-green-700 p-4 rounded-xl text-sm font-bold shadow-xs">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="bg-amber-100 text-amber-700 p-4 rounded-xl text-sm font-bold shadow-xs">
                {{ session('error') }}
            </div>
        @endif
    </div>

    <!-- CONTAINER UTAMA -->
    <div class="max-w-6xl mx-auto mt-6 px-4 space-y-10">

        <!-- ===================================================
             SECTION 0: BANNER WELCOME
             =================================================== -->
        <div class="relative bg-gradient-to-r from-indigo-600 to-blue-500 rounded-2xl p-6 md:p-8 shadow-lg text-white overflow-hidden flex flex-col justify-center min-h-[140px]">
            <!-- Ornamen Dekorasi Background -->
            <div class="absolute top-0 right-0 -mt-10 -mr-10 w-40 h-40 bg-white opacity-10 rounded-full blur-2xl pointer-events-none"></div>
            <div class="absolute bottom-0 right-1/4 w-24 h-24 bg-white opacity-10 rounded-full blur-xl pointer-events-none"></div>
            
            <div class="relative z-10 w-full">
                <!-- Sapaan diubah menggunakan Nama Lengkap agar lebih personal -->
                <h1 class="text-2xl md:text-3xl font-black mb-2 flex items-center gap-2 tracking-tight">
                    Hai, {{ Auth::user()->name }}! <span class="animate-wave text-3xl">👋</span>
                </h1>
                
                <p class="text-indigo-100 text-xs md:text-sm leading-relaxed max-w-2xl font-medium">
                    Selamat datang di Dashboard Ujian. Sudah siap untuk menaklukkan tantangan hari ini? Pilih kuis yang tersedia di bawah dan buktikan kemampuan terbaikmu. Tetap fokus, teliti, dan semangat belajar! 🚀
                </p>
            </div>
        </div>

        <div class="block md:hidden bg-white p-4 rounded-2xl shadow-sm border border-gray-100 space-y-3">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Menu Navigasi Utama</h3>
            
            <div class="grid grid-cols-3 gap-3">
                <!-- Tombol ke Daftar Kuis -->
                <a href="{{ route('peserta.quiz.index') }}" class="flex flex-col items-center justify-center p-3 bg-indigo-50/50 hover:bg-indigo-50 border border-indigo-100/50 rounded-xl text-center transition">
                    <div class="w-10 h-10 bg-indigo-600 text-white rounded-lg flex items-center justify-center mb-2 shadow-xs">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.174c-.053-.462.312-.85.778-.85h13.924c.466 0 .83.388.778.85a49.52 49.52 0 0 1-15.48 0ZM19.5 10.174v6.72a2.25 2.25 0 0 1-2.25 2.25H6.75A2.25 2.25 0 0 1 4.5 16.894v-6.72M12 2.25l8.625 4.5L12 11.25l-8.625-4.5L12 2.25Z" />
                        </svg>
                    </div>
                    <span class="text-[10px] font-bold text-gray-700 leading-tight">Daftar Kuis</span>
                </a>

                <!-- Tombol ke Riwayat Ujian -->
                <a href="{{ route('peserta.riwayat.index') }}" class="flex flex-col items-center justify-center p-3 bg-emerald-50/50 hover:bg-emerald-50 border border-emerald-100/50 rounded-xl text-center transition">
                    <div class="w-10 h-10 bg-emerald-600 text-white rounded-lg flex items-center justify-center mb-2 shadow-xs">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.03 0 1.9.793 1.993 1.817A48.236 48.236 0 0 1 18 4.17M2.25 5.742c0-1.135.845-2.098 1.976-2.192a48.424 48.424 0 0 1 1.123-.08M2.25 5.742c.083.003.167.006.25.008m0 0a2.25 2.25 0 0 1 2.25 2.25v10.5A2.25 2.25 0 0 0 7.5 20.75h9m-11.25-15v15" />
                        </svg>
                    </div>
                    <span class="text-[10px] font-bold text-gray-700 leading-tight">Riwayat</span>
                </a>

                <!-- Tombol Bantuan / Petunjuk Terbuka -->
                <a href="#daftar-kuis-section" class="flex flex-col items-center justify-center p-3 bg-amber-50/50 hover:bg-amber-50 border border-amber-100/50 rounded-xl text-center transition">
                    <div class="w-10 h-10 bg-amber-500 text-white rounded-lg flex items-center justify-center mb-2 shadow-xs">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" />
                        </svg>
                    </div>
                    <span class="text-[10px] font-bold text-gray-700 leading-tight">Panduan</span>
                </a>
            </div>
        </div>
        <!-- ===================================================
             SECTION 1: DAFTAR KUIS TERSEDIA
             =================================================== -->
        <div id="daftar-kuis-section" class="space-y-4">
            <div class="flex justify-between items-center">
                <h2 class="text-lg font-bold text-gray-800 tracking-wide">Daftar Kuis Tersedia</h2>
                <a href="{{ route('peserta.quiz.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 bg-indigo-50 px-3 py-1.5 rounded-lg transition shadow-sm">
                    Lihat Selengkapnya &rarr;
                </a>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-5">
                @forelse($quizzes as $quiz)
                    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex flex-col justify-between transition duration-300 hover:shadow-md hover:-translate-y-1">
                        <div>
                            <!-- DYNAMIC TIER ACCESS BADGE DI SISI PESERTA -->
                            @if(auth()->user()->instansi_id !== null)
                                <span class="bg-purple-50 text-purple-700 border border-purple-100 text-[10px] font-bold px-2.5 py-1 rounded-md uppercase tracking-wider">
                                    🏫 Tugas Instansi
                                </span>
                            @else
@if($quiz->tier_access === 'premium')
    <span class="bg-amber-50 text-amber-700 border border-amber-200 text-[10px] font-bold px-2.5 py-1 rounded-md uppercase tracking-wider">👑 Premium</span>
@elseif($quiz->tier_access === 'khusus')
    <span class="bg-sky-50 text-sky-700 border border-sky-200 text-[10px] font-bold px-2.5 py-1 rounded-md uppercase tracking-wider">⚓ Tes Khusus</span>
@else
    <span class="bg-blue-50 text-blue-700 border border-blue-200 text-[10px] font-bold px-2.5 py-1 rounded-md uppercase tracking-wider">🟢 Basic</span>
@endif
                            @endif

                            <h3 class="font-bold text-gray-800 text-base mt-3 leading-snug">{{ $quiz->title }}</h3>
                            <p class="text-gray-400 text-xs mt-1.5 line-clamp-2 leading-relaxed">{{ $quiz->description ?? 'Tidak ada deskripsi untuk kuis ini.' }}</p>
                        </div>
                        
                        <div class="mt-5">
                            <a href="{{ route('peserta.quiz.show', $quiz->id) }}" class="block text-center bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold py-2.5 rounded-lg transition cursor-pointer shadow-sm">
                                Mulai Kerjakan &rarr;
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-100 text-center col-span-full">
                        <div class="text-4xl mb-3">📭</div>
                        <p class="text-gray-500 text-sm font-medium">Belum ada kuis yang tersedia untuk paket akun Anda saat ini.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- ===================================================
             SECTION 2: RIWAYAT KUIS KAMU
             =================================================== -->
        <div class="space-y-4">
            <div class="flex justify-between items-center">
                <h2 class="text-lg font-bold text-gray-800 tracking-wide">Riwayat Kuis Kamu</h2>
                <a href="{{ route('peserta.riwayat.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 bg-indigo-50 px-3 py-1.5 rounded-lg transition shadow-sm">
                    Lihat Semua &rarr;
                </a>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-5">
                @forelse($history as $h)
                    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex justify-between items-center transition duration-300 hover:shadow-md hover:-translate-y-1">
                        <div>
                            <h4 class="font-bold text-gray-800 text-sm md:text-base line-clamp-1">{{ $h->quiz->title }}</h4>
                            <p class="text-gray-400 text-[10px] mt-1.5 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3 h-3 mr-1 text-gray-400">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                                {{ $h->created_at->format('d M Y, H:i') }} WIB
                            </p>
                        </div>
                        <div class="text-right ml-4 shrink-0 bg-gray-50 p-2 rounded-xl border border-gray-100 min-w-[60px]">
                            <span class="text-xl font-black block leading-none {{ $h->score >= 70 ? 'text-emerald-500' : 'text-red-500' }}">{{ $h->score }}</span>
                            <p class="text-[9px] text-gray-400 font-bold uppercase tracking-wider mt-1">Skor</p>
                        </div>
                    </div>
                @empty
                    <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-100 text-center col-span-full">
                        <div class="text-4xl mb-3">📝</div>
                        <p class="text-gray-500 text-sm font-medium">Kamu belum pernah mengerjakan kuis apapun.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>

</body>
</html>