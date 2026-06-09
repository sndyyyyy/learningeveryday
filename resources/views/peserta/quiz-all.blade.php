<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peserta - Semua Kuis</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-100 font-sans pb-16">

    @include('layouts.navbar-peserta')

    <div class="max-w-5xl mx-auto mt-8 px-4">
        <div class="mb-6">
            <h2 class="text-xl font-bold text-gray-800">Seluruh Kuis yang Tersedia</h2>
            <p class="text-xs text-gray-500 mt-1">Daftar lengkap kuis interaktif yang bisa kamu kerjakan untuk mengasah kemampuan.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
            @forelse($quizzes as $quiz)
                <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex flex-col justify-between transition hover:shadow-md">
                    <div>
                        <span class="bg-indigo-50 text-indigo-700 text-[10px] font-bold px-2.5 py-1 rounded-md uppercase tracking-wider">Kuis Aktif</span>
                        <h3 class="font-bold text-gray-800 text-base mt-3">{{ $quiz->title }}</h3>
                        <p class="text-gray-400 text-xs mt-1 line-clamp-3">{{ $quiz->description ?? 'Tidak ada deskripsi untuk kuis ini.' }}</p>
                    </div>
                    
                    <div class="mt-5">
                        <a href="{{ route('peserta.quiz.show', $quiz->id) }}" class="block text-center bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold py-2.5 rounded-lg transition cursor-pointer">
                            Mulai Kerjakan &rarr;
                        </a>
                    </div>
                </div>
            @empty
                <div class="bg-white p-8 rounded-xl shadow-sm text-center col-span-full">
                    <p class="text-gray-400 text-sm">Belum ada kuis yang dirilis oleh sistem.</p>
                </div>
            @endforelse
        </div>
    </div>

</body>
</html>