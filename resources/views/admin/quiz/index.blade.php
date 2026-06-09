<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Kelola Kuis</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-100 font-sans">

@include('layouts.navbar')

    <div class="max-w-6xl mx-auto mt-8 px-4 grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="bg-white p-6 rounded-xl shadow-sm h-fit">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Buat Kuis Baru</h2>
            @if(session('success'))
                <div class="bg-green-100 text-green-700 p-3 rounded mb-4 text-sm font-medium">{{ session('success') }}</div>
            @endif
            <form action="{{ route('admin.quiz.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-gray-600 text-sm font-semibold mb-1">Judul Kuis</label>
                    <input type="text" name="title" required class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                </div>
                <div>
                    <label class="block text-gray-600 text-sm font-semibold mb-1">Deskripsi</label>
                    <textarea name="description" rows="3" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"></textarea>
                </div>
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 rounded-lg transition text-sm">Simpan Kuis</button>
            </form>
        </div>

        <div class="md:col-span-2 bg-white p-6 rounded-xl shadow-sm">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Daftar Kuis</h2>
            <div class="grid grid-cols-1 gap-4">
                @forelse($quizzes as $quiz)
<div class="border border-gray-200 p-4 rounded-lg flex justify-between items-center bg-gray-50">
    <div>
        <h3 class="font-bold text-gray-800 text-lg">{{ $quiz->title }}</h3>
        <p class="text-gray-500 text-sm mt-1">{{ $quiz->description ?? 'Tidak ada deskripsi.' }}</p>
    </div>
    <div class="flex space-x-2"> <a href="{{ route('admin.quiz.questions', $quiz->id) }}" class="bg-green-600 hover:bg-green-700 text-white text-xs font-bold px-4 py-2 rounded-lg transition">
            Kelola Soal &rarr;
        </a>
        
        <form action="{{ route('admin.quiz.destroy', $quiz->id) }}" method="POST" onsubmit="return confirm('Hapus kuis ini?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white text-xs font-bold px-3 py-2 rounded-lg transition">
                Hapus
            </button>
        </form>
    </div>
</div>                @empty
                    <p class="text-gray-400 text-center py-4">Belum ada kuis yang dibuat.</p>
                @endforelse
            </div>
        </div>
    </div>
</body>
</html>