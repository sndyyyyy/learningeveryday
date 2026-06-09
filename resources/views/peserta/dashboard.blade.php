<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peserta - Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-100 font-sans pb-16">

    @include('layouts.navbar-peserta')

    <!-- Banner Session Alert Flashing (Ditempatkan di atas secara penuh) -->
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

    <!-- CONTAINER UTAMA (Menggunakan susunan vertikal fleksibel, bukan grid 3 kolom lagi) -->
    <div class="max-w-6xl mx-auto mt-6 px-4 space-y-10">

        <!-- ===================================================
             SECTION 1: DAFTAR KUIS TERSEDIA (MELEBAR PENUH)
             =================================================== -->
        <div class="space-y-4">
            <div class="flex justify-between items-center">
                <h2 class="text-lg font-bold text-gray-800 tracking-wide">Daftar Kuis Tersedia</h2>
                <a href="{{ route('peserta.quiz.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 bg-indigo-50 px-3 py-1.5 rounded-lg transition">
                    Lihat Selengkapnya &rarr;
                </a>
            </div>
            
            <!-- Grid Responsif Kuis: 1 Kolom di HP, 2 di Tablet, 3 di Desktop Desktop -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-5">
                @forelse($quizzes as $quiz)
                    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex flex-col justify-between transition hover:shadow-md">
                        <div>
                            <span class="bg-blue-50 text-blue-700 text-[10px] font-bold px-2.5 py-1 rounded-md uppercase tracking-wider">Kuis Aktif</span>
                            <h3 class="font-bold text-gray-800 text-base mt-3 leading-snug">{{ $quiz->title }}</h3>
                            <p class="text-gray-400 text-xs mt-1.5 line-clamp-2 leading-relaxed">{{ $quiz->description ?? 'Tidak ada deskripsi untuk kuis ini.' }}</p>
                        </div>
                        
                        <div class="mt-5">
                            <a href="{{ route('peserta.quiz.show', $quiz->id) }}" class="block text-center bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold py-2.5 rounded-lg transition cursor-pointer">
                                Mulai Kerjakan &rarr;
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="bg-white p-8 rounded-xl shadow-sm text-center col-span-full">
                        <p class="text-gray-400 text-sm">Belum ada kuis yang dirilis oleh admin.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- ===================================================
             SECTION 2: RIWAYAT KUIS KAMU (PINDAH KE BAWAH)
             =================================================== -->
        <div class="space-y-4">
            <div class="flex justify-between items-center">
                <h2 class="text-lg font-bold text-gray-800 tracking-wide">Riwayat Kuis Kamu</h2>
                <a href="{{ route('peserta.riwayat.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 bg-indigo-50 px-3 py-1.5 rounded-lg transition">
                    Lihat Semua &rarr;
                </a>
            </div>
            
            <!-- Grid Responsif Kotak Ujian: Sejajar rapi 3 kotak menyamping di desktop -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-5">
                @forelse($history as $h)
                    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex justify-between items-center transition hover:shadow-md">
                        <div>
                            <h4 class="font-bold text-gray-800 text-sm md:text-base line-clamp-1">{{ $h->quiz->title }}</h4>
                            <p class="text-gray-400 text-[10px] mt-1 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3 h-3 mr-1">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                                {{ $h->created_at->format('d M Y, H:i') }} WIB
                            </p>
                        </div>
                        <div class="text-right ml-4 shrink-0">
                            <!-- Skor hijau jika lulus (>=70) dan merah jika di bawahnya -->
                            <span class="text-xl font-black {{ $h->score >= 70 ? 'text-emerald-500' : 'text-red-500' }}">{{ $h->score }}</span>
                            <p class="text-[9px] text-gray-400 font-bold uppercase tracking-wider">Skor</p>
                        </div>
                    </div>
                @empty
                    <div class="bg-white p-8 rounded-xl shadow-sm text-center col-span-full">
                        <p class="text-gray-400 text-sm py-2">Kamu belum pernah mengerjakan kuis apapun.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>

</body>
</html>