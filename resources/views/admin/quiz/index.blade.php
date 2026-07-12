<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Kelola Kuis</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style>
        @keyframes fadeIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
        .animate-fade-in { animation: fadeIn 0.2s ease-out forwards; }
    </style>
</head>
<body class="bg-gray-100 font-sans">

@include('layouts.navbar')

    <div class="max-w-6xl mx-auto mt-8 px-4 grid grid-cols-1 md:grid-cols-3 gap-8">
        
        <!-- SISI KIRI: PEMBUATAN KUIS -->
        <div class="bg-white p-6 rounded-xl shadow-sm h-fit border border-gray-100">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Buat Kuis Baru</h2>
            @if(session('success'))
                <div class="bg-green-100 text-green-700 p-3 rounded mb-4 text-sm font-medium">{{ session('success') }}</div>
            @endif
            <form action="{{ route('admin.quiz.store') }}" method="POST" class="space-y-4">
                @csrf
                <!-- Input hidden pembantu untuk validasi required_if di controller -->
                <input type="hidden" name="auth_role" value="{{ auth()->user()->role }}">

                <div>
                    <label class="block text-gray-600 text-xs font-semibold mb-1">Judul Kuis</label>
                    <input type="text" name="title" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-xs focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-gray-600 text-xs font-semibold mb-1">Deskripsi</label>
                    <textarea name="description" rows="2" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-xs focus:outline-none focus:border-indigo-500"></textarea>
                </div>

                <!-- HANYA TAMPIL JIKA SUPER ADMIN: Dropdown Pilihan Paket Akses Kuis -->
                @if(auth()->user()->role === 'super_admin')
                <div>
                    <label class="block text-gray-600 text-xs font-semibold mb-1">Akses Paket Kuis (Tier)</label>
                    <select name="tier_access" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-xs focus:outline-none bg-indigo-50 font-bold text-indigo-700">
                        <option value="all">Bisa Diakses Semua (Basic & Premium)</option>
                        <option value="basic">Khusus Paket Basic</option>
                        <option value="premium">Khusus Paket Premium Only</option>
                    </select>
                </div>
                @endif

                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 rounded-lg text-xs transition cursor-pointer">
                    Simpan Kuis Baru
                </button>
            </form>
        </div>

        <!-- SISI KANAN: DAFTAR KUIS -->
        <div class="md:col-span-2 bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Daftar Kuis</h2>
            <div class="grid grid-cols-1 gap-4">
                @forelse($quizzes as $quiz)
                    <div class="border border-gray-200 p-4 rounded-xl flex flex-col sm:flex-row justify-between sm:items-center bg-gray-50 gap-4 transition hover:bg-gray-100/50">
                        <div class="flex-1 space-y-1.5">
                            <div class="flex items-center flex-wrap gap-2">
                                <h3 class="font-bold text-gray-800 text-base md:text-lg leading-tight">{{ $quiz->title }}</h3>
                                
                                <!-- BADGE INDIKATOR HAK AKSES TIER (MULTI-TENANCY) -->
                                @if($quiz->created_by !== 1 && $quiz->user?->role === 'admin') 
                                    <!-- Angka 1 diasumsikan sebagai ID Super Admin utamamu -->
                                    <span class="bg-purple-50 text-purple-700 border border-purple-100 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide">
                                        🏫 Instansi: {{ $quiz->user?->name ?? 'External' }}
                                    </span>
                                @else
                                    @if($quiz->tier_access === 'basic')
                                        <span class="bg-blue-50 text-blue-700 border border-blue-100 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide">🟢 Pusat: Basic</span>
                                    @elseif($quiz->tier_access === 'premium')
                                        <span class="bg-amber-50 text-amber-700 border border-amber-100 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide">👑 Pusat: Premium</span>
                                    @else
                                        <span class="bg-emerald-50 text-emerald-700 border border-emerald-100 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide">🌍 Pusat: Semua</span>
                                    @endif
                                @endif
                            </div>
                            <p class="text-gray-500 text-xs md:text-sm leading-relaxed">{{ $quiz->description ?? 'Tidak ada deskripsi.' }}</p>
                        </div>
                        
                        <!-- TOMBOL AKSI -->
                        <div class="shrink-0">
                            <div class="flex flex-wrap items-center justify-end sm:justify-start gap-2">
                                <a href="{{ route('admin.quiz.questions', $quiz->id) }}" class="text-indigo-600 hover:text-indigo-800 bg-indigo-50 px-2.5 py-1.5 rounded-md text-xs font-bold transition cursor-pointer whitespace-nowrap inline-block">
                                    Kelola Soal &rarr;
                                </a>
                                
                                <form id="form-delete-quiz-{{ $quiz->id }}" action="{{ route('admin.quiz.destroy', $quiz->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <!-- FIX: Mengganti confirm bawaan ke modal custom global -->
                                    <button type="button" 
                                            onclick="triggerCustomDeleteModal('form-delete-quiz-{{ $quiz->id }}', '{{ $quiz->title }}')"
                                            class="text-red-500 hover:text-red-700 font-bold text-xs bg-red-50 px-2.5 py-1.5 rounded-md transition cursor-pointer whitespace-nowrap">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>      
                @empty
                    <p class="text-gray-400 text-center py-4">Belum ada kuis yang dibuat.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- ===================================================
         MODAL POP-UP STYLISH: GLOBAL KONFIRMASI HAPUS KUIS
         =================================================== -->
    <div id="custom-delete-modal" class="fixed top-0 left-0 w-full h-full bg-black/60 hidden justify-center items-center z-[60] backdrop-blur-xs transition-all duration-300">
        <div class="bg-white p-6 rounded-2xl max-w-sm w-[90%] text-center shadow-2xl animate-fade-in flex flex-col">
            
            <div class="w-14 h-14 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-7 h-7">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            
            <h3 class="text-base font-bold text-gray-800 mb-1">Hapus Paket Kuis?</h3>
            <p class="text-xs text-gray-400 mb-5 leading-relaxed">
                Apakah Anda yakin ingin menghapus kuis <span id="delete-target-name" class="text-red-600 font-bold"></span> beserta seluruh daftar pertanyaan dan rekaman lembar jawab di dalamnya? Tindakan ini permanen.
            </p>
            
            <div class="flex flex-row gap-2 w-full">
                <button type="button" class="w-1/2 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold py-2.5 rounded-xl text-xs cursor-pointer transition" onclick="closeCustomDeleteModal()">
                    Batal
                </button>
                <button type="button" class="w-1/2 bg-red-600 hover:bg-red-700 text-white font-bold py-2.5 rounded-xl text-xs cursor-pointer transition shadow-xs" onclick="executeFormDelete()">
                    Ya, Hapus Kuis
                </button>
            </div>
        </div>
    </div>

    <!-- ENGINE JAVASCRIPT KENDALI MODAL -->
    <script>
        let activeDeleteFormId = null;

        function triggerCustomDeleteModal(formId, targetName) {
            activeDeleteFormId = formId;
            document.getElementById('delete-target-name').innerText = targetName;
            
            const modal = document.getElementById('custom-delete-modal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeCustomDeleteModal() {
            const modal = document.getElementById('custom-delete-modal');
            modal.classList.remove('flex');
            modal.classList.add('hidden');
            activeDeleteFormId = null;
        }

        function executeFormDelete() {
            if (activeDeleteFormId) {
                document.getElementById(activeDeleteFormId).submit();
            }
        }
    </script>
</body>
</html>