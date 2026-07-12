<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Part - {{ $bank->name }}</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style>
        /* FIX: Menambahkan deklarasi keyframes agar animasi pop-up modal bekerja mulus */
        @keyframes fadeIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
        .animate-fade-in { animation: fadeIn 0.2s ease-out forwards; }
    </style>
</head>
<body class="bg-gray-100 font-sans">

    @include('layouts.navbar')

    <div class="bg-white border-t border-gray-100 py-3 px-4 md:px-8 flex items-center shadow-xs">
        <a href="{{ route('admin.bank.index') }}" class="text-xs md:text-sm text-gray-500 hover:text-indigo-600 font-semibold transition">
            &larr; Kembali ke Bank Soal Utama
        </a>
    </div>

    <div class="max-w-6xl mx-auto mt-6 md:mt-8 px-4 grid grid-cols-1 md:grid-cols-3 gap-8">
        
        <div class="bg-white p-6 rounded-xl shadow-sm h-fit border border-gray-100">
            <span class="text-[10px] uppercase font-black text-indigo-500 tracking-wider">Bank Soal: {{ $bank->name }}</span>
            <h2 class="text-lg font-bold text-gray-800 mb-4 mt-1">Tambah Part Baru</h2>
            
            @if(session('success'))
                <div class="bg-green-100 text-green-700 p-3 rounded-lg mb-4 text-xs font-semibold">{{ session('success') }}</div>
            @endif

            <form action="{{ route('admin.bank.parts.store', $bank->id) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-gray-600 text-xs font-semibold mb-1">Nama Part / Bagian</label>
                    <input type="text" name="part_name" required placeholder="Misal: Part 1: Short Conversation" 
                           class="w-full px-3 py-2 border border-gray-200 rounded-lg text-xs focus:outline-none focus:border-indigo-500">
                </div>
                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 rounded-lg text-xs transition cursor-pointer">
                    Simpan Part
                </button>
            </form>
        </div>

        <div class="md:col-span-2 bg-white p-5 md:p-6 rounded-xl shadow-sm border border-gray-100">
            <h2 class="text-base md:text-lg font-bold text-gray-800 mb-4">Daftar Part Struktur di <span class="text-indigo-600">{{ $bank->name }}</span></h2>
            
            <div class="w-full overflow-x-auto border border-gray-100 rounded-lg">
                <table class="w-full text-left border-collapse min-w-[450px]">
                    <thead>
                        <tr class="border-b border-gray-200 text-gray-400 text-xs uppercase bg-gray-50">
                            <th class="py-3 pl-3 w-12">No</th>
                            <th class="py-3 px-3">Nama Part</th>
                            <th class="py-3 px-3 text-center">Total Soal Terisi</th>
                            <th class="py-3 px-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-xs md:text-sm divide-y divide-gray-100">
                        @forelse($parts as $index => $part)
                            <tr class="hover:bg-gray-50/70 transition">
                                <td class="py-3.5 pl-3 font-medium">{{ $index + 1 }}</td>
                                <td class="py-3.5 px-3 font-semibold text-gray-800">{{ $part->part_name }}</td>
                                <td class="py-3.5 px-3 text-center">
                                    <span class="bg-amber-50 text-amber-800 border border-amber-200 px-2.5 py-0.5 rounded-full font-bold text-xs">
                                        {{ $part->questions_count }} Soal
                                    </span>
                                </td>
                                <td class="py-3.5 px-3">
                                    <div class="flex flex-wrap items-center justify-center gap-2">
                                        <a href="{{ route('admin.bank.questions', $part->id) }}" class="text-indigo-600 hover:text-indigo-800 bg-indigo-50 px-2.5 py-1.5 rounded-md text-xs font-bold transition cursor-pointer whitespace-nowrap inline-block">
                                            Isi Soal &rarr;
                                        </a>
                                        
                                        <button onclick="openEditPartModal('{{ $part->id }}', '{{ $part->part_name }}')" class="text-amber-600 hover:text-amber-800 bg-amber-50 px-2.5 py-1.5 rounded-md text-xs font-bold transition cursor-pointer whitespace-nowrap inline-block">
                                            Edit
                                        </button>

                                        <form id="form-delete-part-{{ $part->id }}" action="{{ route('admin.bank.parts.destroy', $part->id) }}" method="POST" class="inline-block">
                                            @csrf 
                                            @method('DELETE')
                                            <button type="button" 
                                                    onclick="triggerCustomDeleteModal('form-delete-part-{{ $part->id }}', '{{ $part->part_name }}')" 
                                                    class="text-red-500 hover:text-red-700 font-bold text-xs bg-red-50 px-2.5 py-1.5 rounded-md transition cursor-pointer whitespace-nowrap">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-6 text-center text-gray-400">Wadah Bank ini belum memiliki susunan Part.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- MODAL POP UP: EDIT SUB-PART -->
    <div id="edit-part-modal" class="fixed top-0 left-0 w-full h-full bg-black/60 hidden justify-center items-center z-50 backdrop-blur-xs transition-all duration-300">
        <div class="bg-white p-6 rounded-2xl max-w-sm w-[90%] shadow-2xl flex flex-col animate-fade-in">
            <div class="flex justify-between items-center border-b border-gray-100 pb-3 mb-4">
                <h3 class="text-base font-bold text-gray-800 flex items-center gap-1">
                    <span>✏️</span> Ubah Sub-Part Soal
                </h3>
                <button onclick="closeEditPartModal()" class="text-gray-400 hover:text-gray-600 font-bold cursor-pointer text-lg">&times;</button>
            </div>
            
            <form id="edit-part-form" method="POST" class="space-y-4 text-left">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-gray-600 text-xs font-semibold mb-1">Nama Part Baru</label>
                    <input type="text" id="edit-part-input" name="part_name" required
                           class="w-full px-3 py-2 border border-gray-200 rounded-lg text-xs focus:outline-none focus:border-indigo-500 font-medium text-gray-800">
                </div>
                
                <div class="flex flex-row gap-2 w-full pt-1">
                    <button type="button" class="w-1/2 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold py-2 rounded-xl text-xs cursor-pointer transition" onclick="closeEditPartModal()">
                        Batal
                    </button>
                    <button type="submit" class="w-1/2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 rounded-xl text-xs cursor-pointer transition">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL POP UP: GLOBAL KONFIRMASI HAPUS -->
    <div id="custom-delete-modal" class="fixed top-0 left-0 w-full h-full bg-black/60 hidden justify-center items-center z-[60] backdrop-blur-xs transition-all duration-300">
        <div class="bg-white p-6 rounded-2xl max-w-sm w-[90%] text-center shadow-2xl animate-fade-in flex flex-col">
            
            <div class="w-14 h-14 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-7 h-7">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            
            <h3 class="text-base font-bold text-gray-800 mb-1">Hapus Part Soal?</h3>
            <!-- FIX: Mengubah teks deskripsi dari kata 'kategori' menjadi 'part' agar kontekstual -->
            <p class="text-xs text-gray-400 mb-5 leading-relaxed">
                Apakah Anda yakin ingin menghapus part <span id="delete-target-name" class="text-red-600 font-bold"></span> beserta seluruh susunan soal di dalamnya? Tindakan ini tidak dapat dibatalkan.
            </p>
            
            <div class="flex flex-row gap-2 w-full">
                <button type="button" 
                        class="w-1/2 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold py-2.5 rounded-xl text-xs cursor-pointer transition" 
                        onclick="closeCustomDeleteModal()">
                    Batal
                </button>
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

        function openEditPartModal(id, currentPartName) {
            const modal = document.getElementById('edit-part-modal');
            document.getElementById('edit-part-form').action = "/admin/bank/parts/" + id + "/update";
            document.getElementById('edit-part-input').value = currentPartName;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => { document.getElementById('edit-part-input').focus(); }, 50);
        }

        function closeEditPartModal() {
            const modal = document.getElementById('edit-part-modal');
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }
    </script>
</body>
</html>