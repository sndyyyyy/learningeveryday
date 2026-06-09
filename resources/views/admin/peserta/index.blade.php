<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Kelola Peserta</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-100 font-sans">

@include('layouts.navbar')

    
    <div class="max-w-6xl mx-auto mt-8 px-4 grid grid-cols-1 md:grid-cols-3 gap-8">
        
        <div class="bg-white p-6 rounded-xl shadow-sm h-fit">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Tambah Peserta Baru</h2>
            
            @if(session('success'))
                <div class="bg-green-100 text-green-700 p-3 rounded mb-4 text-sm font-medium">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('admin.peserta.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-gray-600 text-sm font-semibold mb-1">Nama Lengkap</label>
                    <input type="text" name="name" required class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                </div>
                <div>
                    <label class="block text-gray-600 text-sm font-semibold mb-1">Email</label>
                    <input type="email" name="email" required class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                </div>
                <div>
                    <label class="block text-gray-600 text-sm font-semibold mb-1">Password</label>
                    <input type="password" name="password" required class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                </div>
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 rounded-lg transition text-sm">
                    Simpan Akun
                </button>
            </form>
        </div>

 <div class="md:col-span-2 bg-white p-5 md:p-6 rounded-xl shadow-sm">
        <h2 class="text-base md:text-lg font-bold text-gray-800 mb-4">Daftar Peserta Terdaftar</h2>
        
        <div class="w-full overflow-x-auto border border-gray-100 rounded-lg">
            <table class="w-full text-left border-collapse min-w-[500px]"> <thead>
                    <tr class="border-b border-gray-200 text-gray-400 text-xs uppercase bg-gray-50">
                        <th class="py-3 pl-3">No</th>
                        <th class="py-3 px-3">Nama</th>
                        <th class="py-3 px-3">Email</th>
                        <th class="py-3 px-3 text-center">Tanggal</th>
                        <th class="py-3 px-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-xs md:text-sm divide-y divide-gray-100">
                    @forelse($peserta as $index => $p)
                        <tr class="hover:bg-gray-50/70 transition">
                            <td class="py-3.5 pl-3 font-medium">{{ $index + 1 }}</td>
                            <td class="py-3.5 px-3 font-semibold text-gray-800 whitespace-nowrap">{{ $p->name }}</td> <td class="py-3.5 px-3">{{ $p->email }}</td>
                            <td class="py-3.5 px-3 text-center whitespace-nowrap">{{ $p->created_at->format('d M Y') }}</td>
                            <td class="py-3.5 px-3 text-center">
                                <form action="{{ route('admin.peserta.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus?')" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 font-bold text-xs bg-red-50 px-2.5 py-1 rounded-md transition cursor-pointer">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-4 text-center text-gray-400">Belum ada peserta.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="md:col-span-3 bg-white p-5 md:p-6 rounded-xl shadow-sm mt-2">
        <h2 class="text-base md:text-lg font-bold text-gray-800 mb-4">Rekap Hasil Kuis Peserta</h2>
        
        <div class="w-full overflow-x-auto border border-gray-100 rounded-lg">
            <table class="w-full text-left border-collapse min-w-[650px]">
                <thead>
                    <tr class="border-b border-gray-200 text-gray-400 text-xs uppercase bg-gray-50">
                        <th class="py-3 pl-3">No</th>
                        <th class="py-3 px-3">Nama Peserta</th>
                        <th class="py-3 px-3">Nama Kuis</th>
                        <th class="py-3 px-3 text-center">Skor</th>
                        <th class="py-3 px-3 text-center">Tanggal Pengerjaan</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-xs md:text-sm divide-y divide-gray-100">
                    @forelse($allResults as $index => $res)
                        <tr class="hover:bg-gray-50/70 transition">
                            <td class="py-3.5 pl-3 font-medium">{{ $index + 1 }}</td>
                            <td class="py-3.5 px-3 font-semibold text-gray-800 whitespace-nowrap">{{ $res->user->name }}</td>
                            <td class="py-3.5 px-3 text-indigo-600 font-medium whitespace-nowrap">{{ $res->quiz->title }}</td>
                            <td class="py-3.5 px-3 text-center">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $res->score >= 70 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $res->score }}
                                </span>
                            </td>
                            <td class="py-3.5 px-3 text-center text-gray-500 whitespace-nowrap">{{ $res->created_at->format('d M Y, H:i') }} WIB</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-4 text-center text-gray-400">Belum ada hasil kuis.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    </div>

</body>
</html>