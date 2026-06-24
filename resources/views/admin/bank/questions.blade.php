<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bank Soal - {{ $part->part_name }}</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-100 font-sans">

    @include('layouts.navbar')

    <div class="bg-white border-t border-gray-100 py-3 px-4 md:px-8 flex items-center shadow-xs text-xs md:text-sm text-gray-500 font-medium">
        <a href="{{ route('admin.bank.index') }}" class="hover:text-indigo-600 transition">Bank Soal Utama</a>
        <span class="mx-2">&rarr;</span>
        <a href="{{ route('admin.bank.parts', $bank->id) }}" class="hover:text-indigo-600 transition">{{ $bank->name }}</a>
        <span class="mx-2">&rarr;</span>
        <span class="text-gray-800 font-bold">Kelola Soal: {{ $part->part_name }}</span>
    </div>

    <div class="max-w-6xl mx-auto mt-6 px-4 pb-16 grid grid-cols-1 md:grid-cols-3 gap-6 md:gap-8">
        
        <div class="space-y-6">
            
            @if(session('success'))
                <div class="bg-green-100 text-green-700 p-3 rounded-xl text-xs font-bold shadow-xs">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="bg-amber-100 text-amber-700 p-3 rounded-xl text-xs font-bold shadow-xs">{{ session('error') }}</div>
            @endif

            <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                <h3 class="text-sm font-bold text-gray-800 mb-3 flex items-center space-x-1">
                    <span>✏️</span> <span>Metode 1: Input Manual Soal</span>
                </h3>
                
                <form action="{{ route('admin.bank.questions.store', $part->id) }}" method="POST" enctype="multipart/form-data" class="space-y-3.5">
                    @csrf
                    <div>
                        <label class="block text-gray-600 text-[11px] font-semibold mb-1">Pertanyaan / Teks Soal</label>
                        <textarea name="question_text" rows="2" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-xs focus:outline-none focus:border-indigo-500"></textarea>
                    </div>
                    <div>
                        <label class="block text-gray-600 text-[11px] font-semibold mb-1">Upload Gambar (Optional)</label>
                        <input type="file" name="image" accept="image/*" class="w-full text-xs text-gray-500 file:mr-4 file:py-1 file:px-2.5 file:rounded file:border-0 file:text-[11px] file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    </div>
                    <div>
                        <label class="block text-gray-600 text-[11px] font-semibold mb-1">Upload Audio Listening (Optional)</label>
                        <input type="file" name="audio" accept="audio/*" class="w-full text-xs text-gray-500 file:mr-4 file:py-1 file:px-2.5 file:rounded file:border-0 file:text-[11px] file:font-bold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100">
                    </div>
                    
                    <div class="grid grid-cols-2 gap-2.5">
                        <div>
                            <label class="block text-gray-600 text-[10px] font-semibold mb-0.5">Pilihan A</label>
                            <input type="text" name="option_a" required class="w-full px-2.5 py-1.5 border border-gray-200 rounded-lg text-xs focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-gray-600 text-[10px] font-semibold mb-0.5">Pilihan B</label>
                            <input type="text" name="option_b" required class="w-full px-2.5 py-1.5 border border-gray-200 rounded-lg text-xs focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-gray-600 text-[10px] font-semibold mb-0.5">Pilihan C</label>
                            <input type="text" name="option_c" required class="w-full px-2.5 py-1.5 border border-gray-200 rounded-lg text-xs focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-gray-600 text-[10px] font-semibold mb-0.5">Pilihan D</label>
                            <input type="text" name="option_d" required class="w-full px-2.5 py-1.5 border border-gray-200 rounded-lg text-xs focus:outline-none">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-gray-600 text-[11px] font-semibold mb-1">Jawaban Benar</label>
                        <select name="correct_answer" required class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-xs focus:outline-none">
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D">D</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-600 text-[11px] font-semibold mb-1">Pembahasan (Optional)</label>
                        <textarea name="explanation" rows="2" placeholder="Tuliskan alasan/materi pembahasan..." class="w-full px-3 py-2 border border-gray-200 rounded-lg text-xs focus:outline-none"></textarea>
                    </div>
                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 rounded-lg text-xs transition cursor-pointer">
                        Simpan ke Bank Soal
                    </button>
                </form>
            </div>

            <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                <h3 class="text-sm font-bold text-gray-800 mb-1 flex items-center space-x-1">
                    <span>📊</span> <span>Metode 2: Import dari Excel</span>
                </h3>
                <p class="text-[10px] text-gray-400 mb-3 leading-relaxed">Format kolom wajib berurutan: soal, opsi_a, opsi_b, opsi_c, opsi_d, jawaban_benar, pembahasan.</p>
                
                <form action="{{ route('admin.bank.questions.import', $part->id) }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                    @csrf
                    <div class="border-2 border-dashed border-gray-200 rounded-xl p-3 text-center bg-gray-50/50">
                        <input type="file" name="excel_file" required accept=".xlsx,.xls,.csv" class="text-xs text-gray-500 file:text-[11px] file:font-bold file:bg-white file:border file:border-gray-200 file:rounded-md file:px-2 file:py-1 file:cursor-pointer">
                    </div>
                    <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 rounded-lg text-xs transition cursor-pointer shadow-xs">
                        🚀 Jalankan Import File
                    </button>
                </form>
            </div>
        </div>

        <div class="md:col-span-2 bg-white p-5 md:p-6 rounded-xl shadow-sm border border-gray-100 h-fit">
            <h2 class="text-base font-bold text-gray-800 mb-1">Koleksi Konten Soal</h2>
            <p class="text-xs text-gray-400 mb-5">Daftar item soal aktif di kategori <span class="text-indigo-600 font-bold">{{ $part->part_name }}</span>.</p>
            
            <div class="space-y-6">
                @forelse($questions as $index => $q)
                    <div class="border-b border-gray-100 pb-5 last:border-b-0">
                        <p class="font-semibold text-gray-800 text-sm leading-relaxed">
                            <span class="text-indigo-600 font-bold">{{ $index + 1 }}.</span> {{ $q->question_text }}
                        </p>

                        @if($q->image)
                            <div class="my-3 pl-4">
                                <img src="/storage/{{ $q->image }}" alt="Media Soal" class="max-h-32 object-contain rounded-lg border border-gray-100 shadow-2xs">
                            </div>
                        @endif

                        @if($q->audio)
                            <div class="my-3 pl-4 max-w-sm">
                                <audio src="{{ route('audio.stream', ['path' => $q->audio]) }}" controls class="w-full scale-90 origin-left outline-none"></audio>
                            </div>
                        @endif

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-2.5 pl-4 text-xs text-gray-600">
                            <p class="p-2 rounded-md {{ $q->correct_answer == 'A' ? 'bg-emerald-50 text-emerald-700 font-bold border border-emerald-100' : 'bg-gray-50' }}">A. {{ $q->options['A'] }}</p>
                            <p class="p-2 rounded-md {{ $q->correct_answer == 'B' ? 'bg-emerald-50 text-emerald-700 font-bold border border-emerald-100' : 'bg-gray-50' }}">B. {{ $q->options['B'] }}</p>
                            <p class="p-2 rounded-md {{ $q->correct_answer == 'C' ? 'bg-emerald-50 text-emerald-700 font-bold border border-emerald-100' : 'bg-gray-50' }}">C. {{ $q->options['C'] }}</p>
                            <p class="p-2 rounded-md {{ $q->correct_answer == 'D' ? 'bg-emerald-50 text-emerald-700 font-bold border border-emerald-100' : 'bg-gray-50' }}">D. {{ $q->options['D'] }}</p>
                        </div>
                        
                        @if($q->explanation)
                            <p class="text-xs text-gray-500 mt-2.5 bg-indigo-50/50 p-2 rounded-lg border-l-2 border-indigo-500 pl-3">
                                <span class="font-bold text-indigo-700">Materi Pembahasan:</span> {{ $q->explanation }}
                            </p>
                        @endif
                    </div>
                @empty
                    <p class="text-gray-400 text-center py-10 text-xs">Kategori part ini belum memiliki koleksi soal.</p>
                @endforelse
            </div>
        </div>

    </div>
</body>
</html>