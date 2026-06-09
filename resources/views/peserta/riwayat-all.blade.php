<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peserta - Semua Riwayat</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-100 font-sans pb-16">

    @include('layouts.navbar-peserta')

    <div class="max-w-4xl mx-auto mt-8 px-4">
        <div class="mb-6">
            <h2 class="text-xl font-bold text-gray-800">Semua Riwayat Pengerjaan</h2>
            <p class="text-xs text-gray-500 mt-1">Berikut adalah daftar seluruh kuis yang telah kamu selesaikan.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @forelse($history as $h)
                <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex justify-between items-center transition hover:shadow-md">
                    <div>
                        <h4 class="font-bold text-gray-800 text-sm md:text-base">{{ $h->quiz->title }}</h4>
                        <p class="text-gray-400 text-[11px] mt-1 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5 mr-1 text-gray-400">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            {{ $h->created_at->format('d M Y, H:i') }} WIB
                        </p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="text-right">
                            <span class="text-2xl font-extrabold {{ $h->score >= 70 ? 'text-emerald-500' : 'text-red-500' }}">{{ $h->score }}</span>
                            <p class="text-[9px] text-gray-400 uppercase font-bold tracking-wider">Skor</p>
                        </div>
                        <a href="{{ route('peserta.riwayat.detail', $h->id) }}" class="bg-indigo-50 hover:bg-indigo-100 text-indigo-600 text-xs font-bold px-3 py-2 rounded-lg transition cursor-pointer">
                            Detail &rarr;
                        </a>
                    </div>
                </div>
            @empty
                <div class="bg-white p-8 rounded-xl shadow-sm text-center col-span-full">
                    <p class="text-gray-400 text-sm">Kamu belum pernah menyelesaikan kuis apapun.</p>
                </div>
            @endforelse
        </div>
    </div>

</body>
</html>