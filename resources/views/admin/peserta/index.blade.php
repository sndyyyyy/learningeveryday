<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Kelola Peserta</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style>
        @keyframes fadeIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
        .animate-fade-in { animation: fadeIn 0.3s ease-out forwards; }
    </style>
</head>
<body class="bg-gray-100 font-sans">

    @include('layouts.navbar')

    <div class="max-w-6xl mx-auto mt-8 px-4 grid grid-cols-1 md:grid-cols-3 gap-8">
        
        <div class="md:col-span-1 flex flex-col space-y-6">
            
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
                        <label class="block text-gray-600 text-sm font-semibold mb-1">Email / Username</label>
                        <input type="text" name="email" required class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-gray-600 text-sm font-semibold mb-1">Password</label>
                        <input type="password" name="password" required class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                    </div>
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 rounded-lg transition text-sm cursor-pointer">
                        Simpan Akun
                    </button>
                </form>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 h-fit">
                <h2 class="text-lg font-bold text-gray-800 mb-2">Import via Excel (.CSV)</h2>
                
                <div class="text-[10px] text-gray-500 mb-4 bg-gray-50 p-3 rounded-lg border border-gray-200 leading-relaxed">
                    <p class="font-bold text-gray-700 mb-1">Format kolom (Baris 1 wajib Header):</p>
                    <span class="text-indigo-600 font-mono font-bold bg-indigo-50 px-2 py-0.5 rounded">Nama Lengkap, Email/Username, Password</span>
                </div>
                
                <form action="{{ route('admin.peserta.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div class="border-2 border-dashed border-gray-200 rounded-xl p-3 text-center bg-white hover:bg-gray-50 transition cursor-pointer">
                        <input type="file" name="excel_file" required accept=".csv" class="w-full text-xs text-gray-500 file:text-[11px] file:font-bold file:bg-emerald-50 file:text-emerald-700 file:border-0 file:rounded-md file:px-3 file:py-1.5 cursor-pointer">
                    </div>
                    <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 rounded-lg transition text-sm cursor-pointer shadow-sm flex justify-center items-center gap-2">
                        <span>🚀 Import Data CSV</span>
                    </button>
                </form>
            </div>
            
        </div>

        <div class="md:col-span-2 bg-white p-5 md:p-6 rounded-xl shadow-sm h-fit">
            <h2 class="text-base md:text-lg font-bold text-gray-800 mb-4">Daftar Peserta Terdaftar</h2>
            
            <div class="w-full overflow-x-auto border border-gray-100 rounded-lg">
                <table class="w-full text-left border-collapse min-w-[500px]"> 
                    <thead>
                        <tr class="border-b border-gray-200 text-gray-400 text-xs uppercase bg-gray-50">
                            <th class="py-3 pl-3 w-12">No</th>
                            <th class="py-3 px-3">Nama</th>
                            <th class="py-3 px-3">Username/Email</th>
                            <th class="py-3 px-3 text-center">Tanggal</th>
                            <th class="py-3 px-3 text-center">Aksi Manajemen</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-xs md:text-sm divide-y divide-gray-100">
                        @forelse($peserta as $index => $p)
                            <tr class="hover:bg-gray-50/70 transition {{ $index >= 5 ? 'hidden extra-row-peserta' : '' }}">
                                <td class="py-3.5 pl-3 font-medium">{{ $index + 1 }}</td>
                                <td class="py-3.5 px-3 font-semibold text-gray-800 whitespace-nowrap">{{ $p->name }}</td> 
                                <td class="py-3.5 px-3">{{ Str::before($p->email, '@') }}</td>
                                <td class="py-3.5 px-3 text-center whitespace-nowrap">{{ $p->created_at->format('d M Y') }}</td>
                                
                                <td class="py-3.5 px-3 text-center">
                                    <button onclick="openSecurityGate('{{ $p->id }}', '{{ $p->name }}', '{{ $p->raw_password ?? '***' }}')" 
                                            class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold px-3 py-1.5 rounded-lg transition whitespace-nowrap cursor-pointer shadow-sm">
                                        🔒 Buka Otoritas
                                    </button>
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

            @if(count($peserta) > 5)
                <div class="mt-4 text-center">
<button id="btn-more-peserta" onclick="toggleRows('extra-row-peserta', 'btn-more-peserta')" class="bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs font-bold py-2 px-4 rounded-lg transition cursor-pointer border border-gray-200">
    Lihat Selengkapnya ↓
</button>
                </div>
            @endif
        </div>

        <div class="md:col-span-3 bg-white p-5 md:p-6 rounded-xl shadow-sm mt-2 mb-8">
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
                            <th class="py-3 px-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-xs md:text-sm divide-y divide-gray-100">
                        @forelse($allResults as $index => $res)
                            <tr class="hover:bg-gray-50/70 transition {{ $index >= 5 ? 'hidden extra-row-rekap' : '' }}">
                                <td class="py-3.5 pl-3 font-medium">{{ $index + 1 }}</td>
                                <td class="py-3.5 px-3 font-semibold text-gray-800 whitespace-nowrap">{{ $res->user->name }}</td>
                                <td class="py-3.5 px-3 text-indigo-600 font-medium whitespace-nowrap">{{ $res->quiz->title }}</td>
                                <td class="py-3.5 px-3 text-center">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $res->score >= 70 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $res->score }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-3 text-center text-gray-500 whitespace-nowrap">{{ $res->created_at->format('d M Y, H:i') }} WIB</td>
                                <td class="py-3.5 px-3 text-center">
                                    <a href="{{ route('admin.rekap.detail', $res->id) }}" class="text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-md text-xs font-bold transition cursor-pointer">
                                        Detail
                                    </a>
                                </td>                        
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-4 text-center text-gray-400">Belum ada hasil kuis.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(count($allResults) > 5)
                <div class="mt-4 text-center">
<button id="btn-more-rekap" onclick="toggleRows('extra-row-rekap', 'btn-more-rekap')" class="bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs font-bold py-2 px-4 rounded-lg transition cursor-pointer border border-gray-200">
    Lihat Selengkapnya ↓
</button>
                </div>
            @endif
        </div>

    </div>

    <div id="security-gate-modal" class="fixed top-0 left-0 w-full h-full bg-black/60 hidden justify-center items-center z-50 backdrop-blur-sm transition-all duration-300">
        <div class="bg-white p-6 rounded-2xl max-w-sm w-[90%] text-center shadow-2xl animate-fade-in flex flex-col">
            
            <div id="gate-lock-section">
                <div class="text-2xl mb-2">🔐</div>
                <h3 class="text-base font-bold text-gray-800 mb-1">Otorisasi Diperlukan</h3>
                <p class="text-xs text-gray-400 mb-4">Masukkan sandi khusus master kuis untuk mengelola akun <span id="target-user-name" class="text-indigo-600 font-bold"></span>.</p>
                
                <div class="mb-4 text-left">
                    <input type="password" id="gate-auth-input" placeholder="Masukkan Sandi Master..."
                           class="w-full px-3 py-2 border border-gray-200 rounded-lg text-xs focus:outline-none focus:border-indigo-500">
                    <p id="gate-error-msg" class="hidden text-[11px] text-red-500 font-semibold mt-1">⚠️ Sandi master salah, akses ditolak!</p>
                </div>
                
                <div class="flex flex-row gap-2 w-full">
                    <button class="w-1/2 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold py-2 rounded-xl text-xs cursor-pointer transition" onclick="closeSecurityGateModal()">
                        Batal
                    </button>
                    <button class="w-1/2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 rounded-xl text-xs cursor-pointer transition" onclick="verifyMasterPassword()">
                        Konfirmasi
                    </button>
                </div>
            </div>

            <div id="gate-unlocked-section" class="hidden">
                <div class="text-2xl mb-2">🔓</div>
                <h3 class="text-base font-bold text-gray-800 mb-1">Otoritas Terbuka</h3>
                <p class="text-xs text-gray-400 mb-4">Berikut adalah data kredensial dan opsi kontrol manajemen:</p>
                
                <div class="bg-indigo-50/70 border border-indigo-100 rounded-xl p-3 mb-4 text-center">
                    <span class="text-[10px] text-indigo-500 font-bold block uppercase tracking-wide">Password Akun Peserta</span>
                    <span id="unlocked-password-text" class="text-base font-mono font-black text-indigo-700 tracking-wider"></span>
                </div>

                <div class="grid grid-cols-2 gap-2 mb-4">
                    <form id="form-action-reset" method="POST">
                        @csrf
                        <button type="submit" class="w-full bg-amber-500 hover:bg-amber-600 text-white font-bold py-2.5 rounded-xl text-xs transition cursor-pointer">
                            🔄 Reset Sandi
                        </button>
                    </form>
                    <form id="form-action-delete" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit" class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-2.5 rounded-xl text-xs transition cursor-pointer" onclick="return confirm('Yakin ingin menghapus permanen peserta ini?')">
                            🗑️ Hapus Akun
                        </button>
                    </form>
                </div>

                <button class="w-full bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold py-2 rounded-xl text-xs cursor-pointer transition" onclick="closeSecurityGateModal()">
                    Tutup Panel
                </button>
            </div>

        </div>
    </div>

    <script>
        // FUNGSI JAVASCRIPT BARU UNTUK LOAD MORE DATA
function toggleRows(rowClass, btnId) {
    const rows = document.querySelectorAll('.' + rowClass);
    const btn = document.getElementById(btnId);
    
    // Cek apakah data saat ini sedang tersembunyi atau tidak
    // Kita cek baris pertama dari data extra apakah ada class 'hidden'
    const isHidden = rows[0].classList.contains('hidden');

    if (isHidden) {
        // Jika tersembunyi -> Tampilkan semua
        rows.forEach(row => {
            row.classList.remove('hidden');
            row.classList.add('animate-fade-in');
        });
        btn.innerHTML = 'Lihat Lebih Sedikit ↑';
    } else {
        // Jika sedang tampil -> Sembunyikan lagi ke-6 dst
        rows.forEach(row => {
            row.classList.add('hidden');
            row.classList.remove('animate-fade-in');
        });
        btn.innerHTML = 'Lihat Selengkapnya ↓';
    }
}
        // ==============================================
        // SCRIPT SECURITY GATE (TETAP SAMA SEPERTI LAMA)
        // ==============================================
        const MASTER_PASSWORD_KEY = "adminrahasia";
        let currentUserId = null;
        let currentUserRawPassword = "";

        function openSecurityGate(userId, userName, rawPassword) {
            currentUserId = userId;
            currentUserRawPassword = rawPassword;

            document.getElementById("target-user-name").innerText = userName;

            document.getElementById("gate-lock-section").classList.remove("hidden");
            document.getElementById("gate-unlocked-section").classList.add("hidden");
            document.getElementById("gate-auth-input").value = "";
            document.getElementById("gate-error-msg").classList.add("hidden");

            const modal = document.getElementById("security-gate-modal");
            modal.classList.remove("hidden");
            modal.classList.add("flex");

            setTimeout(() => { document.getElementById("gate-auth-input").focus(); }, 50);
        }

        function verifyMasterPassword() {
            const inputVal = document.getElementById("gate-auth-input").value;
            const errorMsg = document.getElementById("gate-error-msg");

            if (inputVal === MASTER_PASSWORD_KEY) {
                document.getElementById("unlocked-password-text").innerText = currentUserRawPassword;

                document.getElementById("form-action-reset").action = "/admin/peserta/" + currentUserId + "/reset-password";
                document.getElementById("form-action-delete").action = "/admin/peserta/" + currentUserId;

                document.getElementById("gate-lock-section").classList.add("hidden");
                document.getElementById("gate-unlocked-section").classList.remove("hidden");
            } else {
                errorMsg.classList.remove("hidden");
            }
        }

        function closeSecurityGateModal() {
            const modal = document.getElementById("security-gate-modal");
            modal.classList.remove("flex");
            modal.classList.add("hidden");

            currentUserId = null;
            currentUserRawPassword = "";
        }

        document.getElementById("gate-auth-input").addEventListener("keyup", function(event) {
            if (event.key === "Enter") {
                verifyMasterPassword();
            }
        });
    </script>

</body>
</html>