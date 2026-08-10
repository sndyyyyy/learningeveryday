<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Kelola Tes Khusus</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style>
        @keyframes fadeIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
        .animate-fade-in { animation: fadeIn 0.2s ease-out forwards; }
    </style>
</head>
<body class="bg-gray-100 font-sans">

@include('layouts.navbar')

    <div class="bg-white border-t border-gray-100 py-3 px-4 md:px-8 flex items-center shadow-xs">
        <a href="{{ route('admin.dashboard.utama') }}" class="text-xs md:text-sm text-gray-500 hover:text-indigo-600 font-semibold transition">
            &larr; Kembali ke Beranda
        </a>
    </div>

    <div class="max-w-6xl mx-auto mt-8 px-4 grid grid-cols-1 md:grid-cols-3 gap-8 pb-16">
        
        <!-- SISI KIRI: FORM BUAT JENIS TES KHUSUS BARU -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 h-fit">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Tambah Jenis Tes Khusus</h2>

            @if(session('success'))
                <div class="bg-green-100 text-green-700 p-3 rounded mb-4 text-xs font-semibold">{{ session('success') }}</div>
            @endif

            <form action="{{ route('admin.special_tests.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-gray-600 text-xs font-semibold mb-1">Nama Jenis Tes</label>
                    <input type="text" name="name" required placeholder="Contoh: Marlins Test, TOEFL ITP" 
                           class="w-full px-3 py-2 border border-gray-200 rounded-lg text-xs focus:outline-none focus:border-indigo-500 font-bold">
                </div>
                <div>
                    <label class="block text-gray-600 text-xs font-semibold mb-1">Deskripsi Singkat</label>
                    <textarea name="description" rows="3" placeholder="Keterangan sertifikasi / tes..."
                              class="w-full px-3 py-2 border border-gray-200 rounded-lg text-xs focus:outline-none focus:border-indigo-500"></textarea>
                </div>
                <button type="submit" class="w-full bg-sky-600 hover:bg-sky-700 text-white font-bold py-2 rounded-lg text-xs transition cursor-pointer shadow-xs">
                    Simpan Jenis Tes
                </button>
            </form>
        </div>

        <!-- SISI KANAN: DAFTAR TES KHUSUS & MAPPING PESERTA -->
        <div class="md:col-span-2 space-y-6">
            <h2 class="text-lg font-bold text-gray-800">Daftar Tes Khusus & Peserta Terdaftar</h2>

            @forelse($specialTests as $st)
                <div class="bg-white p-5 rounded-xl shadow-sm border border-sky-100 space-y-4">
                    <div class="flex justify-between items-start border-b border-gray-100 pb-3">
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="font-extrabold text-gray-800 text-base">{{ $st->name }}</h3>
                                <span class="bg-sky-50 text-sky-700 font-bold text-[10px] px-2.5 py-0.5 rounded-md uppercase border border-sky-100">
                                    {{ $st->users->count() }} Peserta
                                </span>
                            </div>
                            <p class="text-gray-400 text-xs mt-1 leading-relaxed">{{ $st->description ?? 'Tidak ada deskripsi.' }}</p>
                        </div>

                        <form action="{{ route('admin.special_tests.destroy', $st->id) }}" method="POST" onsubmit="return confirm('Hapus Jenis Tes Khusus {{ $st->name }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-bold bg-red-50 px-2.5 py-1.5 rounded-md transition cursor-pointer">
                                Hapus Jenis Tes
                            </button>
                        </form>
                    </div>

                    <!-- TABEL DAFTAR PESERTA KHUSUS DARI TES INI -->
                    <div class="space-y-2">
                        <div class="flex justify-between items-center">
                            <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wide">Daftar Peserta {{ $st->name }}</h4>
                            <button onclick="openAddParticipantModal('{{ $st->id }}', '{{ addslashes($st->name) }}')" 
                                    class="text-sky-600 hover:text-sky-800 bg-sky-50 px-2.5 py-1 rounded-lg text-xs font-bold transition cursor-pointer">
                                + Daftarkan Peserta
                            </button>
                        </div>

                        <div class="w-full overflow-x-auto border border-gray-100 rounded-lg">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="border-b border-gray-200 text-gray-400 text-[10px] uppercase bg-gray-50">
                                        <th class="py-2 pl-3 w-8">No</th>
                                        <th class="py-2 px-3">Nama Peserta</th>
                                        <th class="py-2 px-3">Username / Email</th>
                                        <th class="py-2 px-3">Password Mentah</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-600 text-xs divide-y divide-gray-100">
                                    @forelse($st->users as $idx => $u)
                                        <tr class="hover:bg-gray-50/70">
                                            <td class="py-2 pl-3 font-medium">{{ $idx + 1 }}</td>
                                            <td class="py-2 px-3 font-bold text-gray-800">{{ $u->name }}</td>
                                            <td class="py-2 px-3 font-mono text-sky-600">{{ $u->email }}</td>
                                            <td class="py-2 px-3 font-mono text-gray-400">{{ $u->raw_password ?? '***' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="py-4 text-center text-gray-400 text-xs">Belum ada peserta yang didaftarkan pada jenis tes ini.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white p-8 rounded-xl text-center border border-gray-100 text-gray-400">
                    Belum ada Jenis Tes Khusus yang dibuat. Silakan tambahkan di formulir sebelah kiri.
                </div>
            @endforelse
        </div>
    </div>

    <!-- MODAL PENDAFTARAN PESERTA TES KHUSUS -->
    <div id="add-participant-modal" class="fixed top-0 left-0 w-full h-full bg-black/60 hidden justify-center items-center z-50 backdrop-blur-xs">
        <div class="bg-white p-6 rounded-2xl max-w-sm w-[90%] shadow-2xl animate-fade-in flex flex-col">
            <div class="flex justify-between items-center border-b border-gray-100 pb-3 mb-4">
                <h3 class="text-base font-bold text-gray-800">
                    Daftarkan Peserta <span id="modal-test-name" class="text-sky-600"></span>
                </h3>
                <button onclick="closeAddParticipantModal()" class="text-gray-400 hover:text-gray-600 font-bold text-lg cursor-pointer">&times;</button>
            </div>

            <form id="add-participant-form" method="POST" class="space-y-4 text-left">
                @csrf
                <div>
                    <label class="block text-gray-600 text-xs font-semibold mb-1">Nama Lengkap Peserta</label>
                    <input type="text" name="name" required placeholder="Nama Peserta" 
                           class="w-full px-3 py-2 border border-gray-200 rounded-lg text-xs focus:outline-none focus:border-sky-500 font-bold">
                </div>
                <div>
                    <label class="block text-gray-600 text-xs font-semibold mb-1">Email / Username Login</label>
                    <input type="text" name="email" required placeholder="username_peserta" 
                           class="w-full px-3 py-2 border border-gray-200 rounded-lg text-xs focus:outline-none focus:border-sky-500 font-mono">
                </div>
                <div>
                    <label class="block text-gray-600 text-xs font-semibold mb-1">Password</label>
                    <input type="text" name="password" required placeholder="Minimal 6 Karakter" 
                           class="w-full px-3 py-2 border border-gray-200 rounded-lg text-xs focus:outline-none focus:border-sky-500 font-mono">
                </div>

                <div class="flex gap-2 pt-2">
                    <button type="button" onclick="closeAddParticipantModal()" class="w-1/2 bg-gray-100 text-gray-600 font-bold py-2 rounded-xl text-xs">Batal</button>
                    <button type="submit" class="w-1/2 bg-sky-600 hover:bg-sky-700 text-white font-bold py-2 rounded-xl text-xs">Daftarkan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openAddParticipantModal(testId, testName) {
            document.getElementById('modal-test-name').innerText = testName;
            document.getElementById('add-participant-form').action = "/admin/special-tests/" + testId + "/participants";
            
            const modal = document.getElementById('add-participant-modal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeAddParticipantModal() {
            const modal = document.getElementById('add-participant-modal');
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }
    </script>
</body>
</html>