<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Bank Soal Utama</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-100 font-sans">

    @include('layouts.navbar')

    <div class="max-w-6xl mx-auto mt-8 px-4 grid grid-cols-1 md:grid-cols-3 gap-8">
        
        <div class="bg-white p-6 rounded-xl shadow-sm h-fit border border-gray-100">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Buat Wadah Bank Soal</h2>
            
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
                    Simpan Wadah Bank
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
                                        <a href="{{ route('admin.bank.parts', $bank->id) }}" class="text-indigo-600 hover:text-indigo-800 bg-indigo-50 px-2.5 py-1.5 rounded-md text-xs font-bold transition cursor-pointer whitespace-nowrap inline-block">
                                            Kelola Part &rarr;
                                        </a>
                                        <form action="{{ route('admin.bank.destroy', $bank->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus wadah ini beserta seluruh part di dalamnya?')" class="inline-block">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700 font-bold text-xs bg-red-50 px-2.5 py-1.5 rounded-md transition cursor-pointer whitespace-nowrap">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-6 text-center text-gray-400">Belum ada wadah bank soal dibuat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</body>
</html>