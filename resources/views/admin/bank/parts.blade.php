<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Part - {{ $bank->name }}</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
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
                                <td class="py-3.5 px-3 text-center space-x-2">
                                    <a href="{{ route('admin.bank.questions', $part->id) }}" class="text-white bg-indigo-600 hover:bg-indigo-700 px-3 py-1 rounded-md text-xs font-bold transition cursor-pointer">
                                        Isi Soal &rarr;
                                    </a>
                                    <form action="{{ route('admin.bank.parts.destroy', $part->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus Part ini beserta seluruh soal di dalamnya?')" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 font-bold text-xs bg-red-50 px-2 py-1 rounded-md transition cursor-pointer">
                                            Hapus
                                        </button>
                                    </form>
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
</body>
</html>