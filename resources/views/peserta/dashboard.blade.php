<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peserta - Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style>
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
<body class="bg-gray-100 font-sans pb-16">

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
             SECTION 0: BANNER WELCOME (BARU)
             =================================================== -->
        <div class="relative bg-gradient-to-r from-indigo-600 to-blue-500 rounded-2xl p-6 md:p-8 shadow-lg text-white overflow-hidden flex flex-col justify-center min-h-[140px]">
            <!-- Ornamen Dekorasi Background -->
            <div class="absolute top-0 right-0 -mt-10 -mr-10 w-40 h-40 bg-white opacity-10 rounded-full blur-2xl pointer-events-none"></div>
            <div class="absolute bottom-0 right-1/4 w-24 h-24 bg-white opacity-10 rounded-full blur-xl pointer-events-none"></div>
            
            <div class="relative z-10 w-full">
                <!-- Sapaan Dinamis menggunakan Email/Username -->
                <h1 class="text-2xl md:text-3xl font-black mb-2 flex items-center gap-2 tracking-tight">
                    Hai, {{ Auth::user()->email }}! <span class="animate-wave text-3xl">👋</span>
                </h1>
                
                <p class="text-indigo-100 text-xs md:text-sm leading-relaxed max-w-2xl font-medium">
                    Selamat datang di Dashboard Ujian. Sudah siap untuk menaklukkan tantangan hari ini? Pilih kuis yang tersedia di bawah dan buktikan kemampuan terbaikmu. Tetap fokus, teliti, dan semangat belajar! 🚀
                </p>
            </div>
        </div>

        <!-- ===================================================
             SECTION 1: DAFTAR KUIS TERSEDIA
             =================================================== -->
        <div class="space-y-4">
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
                            <span class="bg-blue-50 text-blue-700 text-[10px] font-bold px-2.5 py-1 rounded-md uppercase tracking-wider">Kuis Aktif</span>
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
                        <p class="text-gray-500 text-sm font-medium">Belum ada kuis yang dirilis oleh admin saat ini.</p>
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