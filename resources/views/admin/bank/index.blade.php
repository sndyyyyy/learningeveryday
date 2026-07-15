<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Bank Soal Utama</title>
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

    <div class="max-w-6xl mx-auto mt-8 px-4 grid grid-cols-1 md:grid-cols-3 gap-8">
        
        <div class="bg-white p-6 rounded-xl shadow-sm h-fit border border-gray-100">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Buat Bank Soal</h2>
            
            @if(session('success'))
                <div class="bg-green-100 text-green-700 p-3 rounded-lg mb-4 text-xs font-semibold">{{ session('success') }}</div>
            @endif

            <form action="{{ route('admin.bank.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-gray-600 text-xs font-semibold mb-1">Nama Kategori Bank Soal</label>
                    <input type="text" name="name" required placeholder="Misal: Reading Test, Structure, dll." 
                           class="w-full px-3 py-2 border border-gray-200 rounded-lg text-xs focus:outline-none focus:border-indigo-500">
                </div>
                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 rounded-lg text-xs transition cursor-pointer">
                    Simpan Bank Soal
                </button>
            </form>
        </div>

        <div class="md:col-span-2 bg-white p-5 md:p-6 rounded-xl shadow-sm border border-gray-100">
            <h2 class="text-base md:text-lg font-bold text-gray-800 mb-4">Daftar Bank Soal Utama</h2>
            
            <div class="w-full overflow-x-auto border border-gray-100 rounded-lg">
                <table class="w-full text-left border-collapse min-w-[450px]">
                    <thead>
                        <tr class="border-b border-gray-200 text-gray-400 text-xs uppercase bg-gray-50">
                            <th class="py-3 pl-3 w-12">No</th>
                            <th class="py-3 px-3">Nama Bank Soal</th>
                            <th class="py-3 px-3 text-center">Jumlah Part</th>
                            <th class="py-3 px-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-xs md:text-sm divide-y divide-gray-100">
                        @forelse($banks as $index => $bank)
                            <tr class="hover:bg-gray-50/70 transition">
                                <td class="py-3.5 pl-3 font-medium">{{ $index + 1 }}</td>
                                <td class="py-3.5 px-3 font-semibold text-gray-800 whitespace-nowrap">{{ $bank->name }}</td>
                                <td class="py-3.5 px-3 text-center">
                                    <span class="bg-indigo-50 text-indigo-700 px-2.5 py-0.5 rounded-full font-bold text-xs">
                                        {{ $bank->parts_count }} Part
                                    </span>
                                </td>
                                <td class="py-3.5 px-3">
                                    <div class="flex flex-wrap items-center justify-center gap-2">
                                        <!-- 1. Kelola Part -->
                                        <a href="{{ route('admin.bank.parts', $bank->id) }}" class="text-indigo-600 hover:text-indigo-800 bg-indigo-50 px-2.5 py-1.5 rounded-md text-xs font-bold transition cursor-pointer whitespace-nowrap inline-block">
                                            Kelola Part &rarr;
                                        </a>

                                        <!-- 2. Edit Nama (Memanggil JavaScript Pop-Up) -->
                                        <button onclick="openEditBankModal('{{ $bank->id }}', '{{ $bank->name }}')" class="text-amber-600 hover:text-amber-800 bg-amber-50 px-2.5 py-1.5 rounded-md text-xs font-bold transition cursor-pointer whitespace-nowrap inline-block">
                                            Edit
                                        </button>

                                        <!-- 3. Hapus -->
<form id="form-delete-bank-{{ $bank->id }}" action="{{ route('admin.bank.destroy', $bank->id) }}" method="POST" class="inline-block">
    @csrf 
    @method('DELETE')
    <!-- Kita buang onsubmit bawaan, ganti dengan onclick pemicu modal custom -->
    <button type="button" 
            onclick="triggerCustomDeleteModal('form-delete-bank-{{ $bank->id }}', '{{ $bank->name }}')" 
            class="text-red-500 hover:text-red-700 font-bold text-xs bg-red-50 px-2.5 py-1.5 rounded-md transition cursor-pointer whitespace-nowrap">
        Hapus
    </button>
</form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-6 text-center text-gray-400">Belum ada bank soal dibuat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- ===================================================
         MODAL POP-UP STYLISH: EDIT NAMA BANK SOAL
         =================================================== -->
    <div id="edit-bank-modal" class="fixed top-0 left-0 w-full h-full bg-black/60 hidden justify-center items-center z-50 backdrop-blur-xs transition-all duration-300">
        <div class="bg-white p-6 rounded-2xl max-w-sm w-[90%] shadow-2xl animate-fade-in flex flex-col">
            <div class="flex justify-between items-center border-b border-gray-100 pb-3 mb-4">
                <h3 class="text-base font-bold text-gray-800 flex items-center gap-1">
                    <span>✏️</span> Ubah Kategori Bank Soal
                </h3>
                <button onclick="closeEditBankModal()" class="text-gray-400 hover:text-gray-600 font-bold cursor-pointer text-lg">&times;</button>
            </div>
            
            <form id="edit-bank-form" method="POST" class="space-y-4 text-left">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-gray-600 text-xs font-semibold mb-1">Nama Kategori Baru</label>
                    <input type="text" id="edit-bank-input" name="name" required
                           class="w-full px-3 py-2 border border-gray-200 rounded-lg text-xs focus:outline-none focus:border-indigo-500 font-medium text-gray-800">
                </div>
                
                <div class="flex flex-row gap-2 w-full pt-1">
                    <button type="button" class="w-1/2 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold py-2 rounded-xl text-xs cursor-pointer transition" onclick="closeEditBankModal()">
                        Batal
                    </button>
                    <button type="submit" class="w-1/2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 rounded-xl text-xs cursor-pointer transition">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

<div id="custom-delete-modal" class="fixed top-0 left-0 w-full h-full bg-black/60 hidden justify-center items-center z-[60] backdrop-blur-xs transition-all duration-300">
        <div class="bg-white p-6 rounded-2xl max-w-sm w-[90%] text-center shadow-2xl animate-fade-in flex flex-col">
            
            <!-- Icon Peringatan -->
            <div class="w-14 h-14 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-7 h-7">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            
            <h3 class="text-base font-bold text-gray-800 mb-1">Hapus Bank Soal?</h3>
            <p class="text-xs text-gray-400 mb-5 leading-relaxed">
                Apakah Anda yakin ingin menghapus kategori <span id="delete-target-name" class="text-red-600 font-bold"></span> beserta seluruh sub-part dan soal di dalamnya? Tindakan ini tidak dapat dibatalkan.
            </p>
            
            <div class="flex flex-row gap-2 w-full">
                <!-- Tombol Batal -->
                <button type="button" 
                        class="w-1/2 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold py-2.5 rounded-xl text-xs cursor-pointer transition" 
                        onclick="closeCustomDeleteModal()">
                    Batal
                </button>
                <!-- Tombol Konfirmasi Eksekusi -->
                <button type="button" 
                        class="w-1/2 bg-red-600 hover:bg-red-700 text-white font-bold py-2.5 rounded-xl text-xs cursor-pointer transition shadow-xs" 
                        onclick="executeFormDelete()">
                    Ya, Hapus Permanen
                </button>
            </div>
        </div>
    </div>

    <script>
        let activeDeleteFormId = null;

function triggerCustomDeleteModal(formId, targetName) {
            activeDeleteFormId = formId;
            
            // Suntik teks nama target bank soal ke dalam deskripsi modal
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
                // Submit form asli Laravel secara otomatis via JS ID yang terlacak
                document.getElementById(activeDeleteFormId).submit();
            }
        }

        function openEditBankModal(id, currentName) {
            const modal = document.getElementById('edit-bank-modal');
            const form = document.getElementById('edit-form-action');
            
            // Suntik rute action form secara dinamis berdasarkan ID target
            document.getElementById('edit-bank-form').action = "/admin/bank/" + id + "/update";
            // Isi form dengan nama kategori lama
            document.getElementById('edit-bank-input').value = currentName;

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => { document.getElementById('edit-bank-input').focus(); }, 50);
        }

        function closeEditBankModal() {
            const modal = document.getElementById('edit-bank-modal');
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }
    </script>
</body>
</html>