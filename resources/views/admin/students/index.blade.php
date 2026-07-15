<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Kelola Akun Siswa</title>
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
        
        <!-- SISI KIRI: FORM PENDAFTARAN SISWA & INFO KUOTA -->
        <div class="space-y-4">
            <!-- Card Info Kuota -->
            <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                <span class="text-[10px] uppercase font-black text-indigo-500 tracking-wider">Status Paket Langganan</span>
                <h3 class="font-bold text-gray-800 text-base mt-0.5 capitalize">{{ str_replace('_', ' ', $instansi->subscription) }}</h3>
                
                <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between">
                    <span class="text-xs text-gray-500 font-medium">Kuota Terpakai:</span>
                    <span class="text-xs font-black text-gray-800">
                        {{ $currentStudentCount }} / {{ $instansi->subscription === 'instansi_basic' ? '50' : '∞' }} Siswa
                    </span>
                </div>
                <!-- Progress Bar Kuota -->
                @if($instansi->subscription === 'instansi_basic')
                <div class="w-full h-1.5 bg-gray-100 rounded-full mt-2 overflow-hidden">
                    <div class="h-full bg-indigo-600 transition-all" style="width: {{ ($currentStudentCount / 50) * 100 }}%"></div>
                </div>
                @endif
            </div>

            <!-- Form Daftarkan Siswa -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <h2 class="text-base font-bold text-gray-800 mb-4">Daftarkan Siswa Baru</h2>
                
                @if(session('success'))
                    <div class="bg-green-100 text-green-700 p-3 rounded mb-4 text-xs font-semibold">{{ session('success') }}</div>
                @endif

                @if($errors->has('limit_reached'))
                    <div class="bg-red-50 border border-red-200 text-red-600 p-3 rounded-xl mb-4 text-xs font-semibold leading-relaxed">
                        {{ $errors->first('limit_reached') }}
                    </div>
                @endif

                <form action="{{ route('admin.students.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-gray-600 text-xs font-semibold mb-1">Nama Lengkap Siswa</label>
                        <input type="text" name="name" required placeholder="Nama Siswa" 
                               class="w-full px-3 py-2 border border-gray-200 rounded-lg text-xs focus:outline-none focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-gray-600 text-xs font-semibold mb-1">Email / Username Login</label>
                        <input type="email" name="email" required placeholder="siswa@sekolah.com" 
                               class="w-full px-3 py-2 border border-gray-200 rounded-lg text-xs focus:outline-none focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-gray-600 text-xs font-semibold mb-1">Password Awal</label>
                        <input type="text" name="password" required placeholder="Minimal 6 Karakter" 
                               class="w-full px-3 py-2 border border-gray-200 rounded-lg text-xs focus:outline-none focus:border-indigo-500">
                    </div>
                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 rounded-lg text-xs transition cursor-pointer">
                        Daftarkan Akun Siswa
                    </button>
                </form>
            </div>
        </div>

        <!-- SISI KANAN: TABEL DAFTAR SISWA TERDAFTAR -->
        <div class="md:col-span-2 bg-white p-5 md:p-6 rounded-xl shadow-sm border border-gray-100">
            <h2 class="text-base md:text-lg font-bold text-gray-800 mb-4">Daftar Akun Siswa Terdaftar</h2>
            
            <div class="w-full overflow-x-auto border border-gray-100 rounded-lg">
                <table class="w-full text-left border-collapse min-w-[500px]">
                    <thead>
                        <tr class="border-b border-gray-200 text-gray-400 text-xs uppercase bg-gray-50">
                            <th class="py-3 pl-3 w-12">No</th>
                            <th class="py-3 px-3">Nama Siswa</th>
                            <th class="py-3 px-3">Email / Username</th>
                            <th class="py-3 px-3">Password Mentah</th>
                            <th class="py-3 px-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-xs md:text-sm divide-y divide-gray-100">
                        @forelse($students as $index => $student)
                            <tr class="hover:bg-gray-50/70 transition">
                                <td class="py-3.5 pl-3 font-medium">{{ $index + 1 }}</td>
                                <td class="py-3.5 px-3 font-semibold text-gray-800">{{ $student->name }}</td>
                                <td class="py-3.5 px-3 text-gray-500">{{ $student->email }}</td>
                                <td class="py-3.5 px-3 font-mono text-gray-400">{{ $student->raw_password }}</td>
                                <td class="py-3.5 px-3">
                                    <div class="flex items-center justify-center">
                                        <form id="form-delete-student-{{ $student->id }}" action="{{ route('admin.students.destroy', $student->id) }}" method="POST" class="inline-block">
                                            @csrf @method('DELETE')
                                            <button type="button" 
                                                    onclick="triggerCustomDeleteModal('form-delete-student-{{ $student->id }}', '{{ $student->name }}')" 
                                                    class="text-red-500 hover:text-red-700 font-bold text-xs bg-red-50 px-2.5 py-1.5 rounded-md transition cursor-pointer whitespace-nowrap">
                                                Hapus Akses
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-gray-400">Belum ada akun siswa yang didaftarkan oleh instansi Anda.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-span-1 md:col-span-3 bg-white p-5 md:p-6 rounded-xl shadow-sm border border-gray-100 mt-6">
            <h2 class="text-base md:text-lg font-bold text-gray-800 mb-1">Histori Ujian Siswa</h2>
            <p class="text-xs text-gray-400 mb-4">Rekam jejak skor dan nilai kuis yang dikerjakan oleh murid instansi Anda.</p>
            
            <div class="w-full overflow-x-auto border border-gray-100 rounded-lg">
                <table class="w-full text-left border-collapse min-w-[600px]">
                    <thead>
                        <tr class="border-b border-gray-200 text-gray-400 text-xs uppercase bg-gray-50">
                            <th class="py-3 pl-3 w-12">No</th>
                            <th class="py-3 px-3">Nama Siswa</th>
                            <th class="py-3 px-3">Kuis Yang Diikuti</th>
                            <th class="py-3 px-3">Tanggal Pengerjaan</th>
                            <th class="py-3 px-3 text-center">Skor Akhir</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-xs md:text-sm divide-y divide-gray-100">
                        @forelse($histories as $index => $history)
                            <tr class="hover:bg-gray-50/70 transition">
                                <td class="py-3.5 pl-3 font-medium">{{ $index + 1 }}</td>
                                <td class="py-3.5 px-3 font-semibold text-gray-800">{{ $history->user?->name }}</td>
                                <td class="py-3.5 px-3 font-medium text-indigo-600">{{ $history->quiz?->title }}</td>
                                <td class="py-3.5 px-3 text-gray-400">{{ $history->created_at->format('d M Y, H:i') }} WIB</td>
                                <td class="py-3.5 px-3 text-center">
                                    <span class="px-2.5 py-1 rounded-md font-bold text-xs {{ $history->score >= 70 ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-red-50 text-red-700 border border-red-100' }}">
                                        {{ $history->score }} / 100
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-gray-400">Belum ada rekaman ujian dari siswa instansi Anda saat ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MODAL POP-UP GLOBAL KONFIRMASI HAPUS SISWA -->
    <div id="custom-delete-modal" class="fixed top-0 left-0 w-full h-full bg-black/60 hidden justify-center items-center z-[60] backdrop-blur-xs transition-all duration-300">
        <div class="bg-white p-6 rounded-2xl max-w-sm w-[90%] text-center shadow-2xl animate-fade-in flex flex-col">
            <div class="w-14 h-14 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-7 h-7">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <h3 class="text-base font-bold text-gray-800 mb-1">Cabut Akses Siswa?</h3>
            <p class="text-xs text-gray-400 mb-5 leading-relaxed">
                Apakah Anda yakin ingin menghapus akun siswa <span id="delete-target-name" class="text-red-600 font-bold"></span>? Siswa ini tidak akan bisa login atau mengerjakan kuis lagi dari instansi Anda.
            </p>
            <div class="flex flex-row gap-2 w-full">
                <button type="button" class="w-1/2 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold py-2.5 rounded-xl text-xs cursor-pointer transition" onclick="closeCustomDeleteModal()">Batal</button>
                <button type="button" class="w-1/2 bg-red-600 hover:bg-red-700 text-white font-bold py-2.5 rounded-xl text-xs cursor-pointer transition shadow-xs" onclick="executeFormDelete()">Ya, Hapus Akun</button>
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
    </script>
</body>
</html>