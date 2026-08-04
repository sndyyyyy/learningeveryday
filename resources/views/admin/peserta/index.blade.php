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

    <div class="bg-white border-t border-gray-100 py-3 px-4 md:px-8 flex items-center shadow-xs">
        <a href="{{ route('admin.dashboard.utama') }}" class="text-xs md:text-sm text-gray-500 hover:text-indigo-600 font-semibold transition">
            &larr; Kembali ke Beranda
        </a>
    </div>

    <div class="max-w-6xl mx-auto mt-8 px-4 grid grid-cols-1 md:grid-cols-3 gap-8">
        
        <!-- SISI KIRI: TAMBAH PESERTA PUSAT & IMPORT CSV -->
        <div class="md:col-span-1 flex flex-col space-y-6">
            
            <div class="bg-white p-6 rounded-xl shadow-sm h-fit">
                <h2 class="text-lg font-bold text-gray-800 mb-4">Tambah Peserta Mandiri</h2>
                
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
                        <input type="text" name="email" required placeholder="Bisa tanpa @" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm font-mono">
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
                
                <div class="text-[10px] text-gray-500 mb-4 bg-gray-50 p-3 rounded-lg border border-gray-200 leading-relaxed space-y-2">
                    <div>
                        <p class="font-bold text-gray-700 mb-1">Format kolom (Baris 1 wajib Header):</p>
                        <span class="text-indigo-600 font-mono font-bold bg-indigo-50 px-2 py-0.5 rounded">Nama Lengkap, Email/Username, Password</span>
                    </div>

                    <!-- 📥 TOMBOL DOWNLOAD FORMAT CSV -->
                    <div class="pt-1">
                        <a href="{{ asset('templates/template.csv') }}" download 
                        class="inline-flex items-center gap-1.5 text-indigo-600 hover:text-indigo-800 font-bold hover:underline transition">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                            </svg>
                            <span>Download Format CSV (.csv)</span>
                        </a>
                    </div>
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

        <!-- SISI KANAN: TERBAGI MENJADI 2 TABEL (MANDIRI & INSTANSI) -->
        <div class="md:col-span-2 space-y-8">
            
            <!-- TABEL 1: PESERTA MANDIRI / PUSAT -->
            <div class="bg-white p-5 md:p-6 rounded-xl shadow-sm h-fit">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-base md:text-lg font-bold text-gray-800">Daftar Peserta Mandiri (Pusat)</h2>
                    <span class="text-xs bg-blue-50 text-blue-700 font-bold px-2.5 py-1 rounded-md">{{ count($pesertaMandiri) }} Akun</span>
                </div>
                
                <div class="w-full overflow-x-auto border border-gray-100 rounded-lg">
                    <table class="w-full text-left border-collapse min-w-[500px]"> 
                        <thead>
                            <tr class="border-b border-gray-200 text-gray-400 text-xs uppercase bg-gray-50">
                                <th class="py-3 pl-3 w-12">No</th>
                                <th class="py-3 px-3">Nama</th>
                                <th class="py-3 px-3">Username/Email</th>
                                <th class="py-3 px-3 text-center">Tanggal</th>
                                <th class="py-3 px-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 text-xs md:text-sm divide-y divide-gray-100">
                            @forelse($pesertaMandiri as $index => $p)
                                <tr class="hover:bg-gray-50/70 transition {{ $index >= 5 ? 'hidden extra-row-peserta' : '' }}">
                                    <td class="py-3.5 pl-3 font-medium">{{ $index + 1 }}</td>
                                    <td class="py-3.5 px-3 font-semibold text-gray-800 whitespace-nowrap">{{ $p->name }}</td> 
                                    <!-- FIX: MENAMPILKAN USERNAME / EMAIL UTUH TANPA POTONGAN STR::BEFORE -->
                                    <td class="py-3.5 px-3 font-mono text-indigo-600">{{ $p->email }}</td>
                                    <td class="py-3.5 px-3 text-center whitespace-nowrap">{{ $p->created_at->format('d M Y') }}</td>
                                    
                                    <td class="py-3.5 px-3 text-center">
                                        <button onclick="openSecurityGate('{{ $p->id }}', '{{ addslashes($p->name) }}', '{{ $p->raw_password ?? '***' }}')" 
                                                class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold px-3 py-1.5 rounded-lg transition whitespace-nowrap cursor-pointer shadow-sm">
                                            Buka Otoritas
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-6 text-center text-gray-400">Belum ada peserta mandiri terdaftar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(count($pesertaMandiri) > 5)
                    <div class="mt-4 text-center">
                        <button id="btn-more-peserta" onclick="toggleRows('extra-row-peserta', 'btn-more-peserta')" class="bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs font-bold py-2 px-4 rounded-lg transition cursor-pointer border border-gray-200">
                            Lihat Selengkapnya ↓
                        </button>
                    </div>
                @endif
            </div>

            <!-- TABEL 2: DAFTAR INSTANSI & EKSPANSI DETAIL SISWA -->
            <div class="bg-white p-5 md:p-6 rounded-xl shadow-sm h-fit border border-gray-100">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-base md:text-lg font-bold text-gray-800">Daftar Instansi Terdaftar</h2>
                    <span class="text-xs bg-blue-50 text-blue-700 font-bold px-2.5 py-1 rounded-md">{{ count($instansiList) }} Instansi</span>
                </div>
                
                <div class="w-full overflow-x-auto border border-gray-100 rounded-lg">
                    <table class="w-full text-left border-collapse min-w-[500px]"> 
                        <thead>
                            <tr class="border-b border-gray-200 text-gray-400 text-xs uppercase bg-gray-50">
                                <th class="py-3 pl-3 w-12">No</th>
                                <th class="py-3 px-3">Nama Instansi</th>
                                <th class="py-3 px-3">Username / Email</th>
                                <th class="py-3 px-3 text-center">Jumlah Siswa</th>
                                <th class="py-3 px-3 text-center">Aksi Detail</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 text-xs md:text-sm divide-y divide-gray-100">
                            @forelse($instansiList as $index => $ins)
                                <tr class="hover:bg-gray-50/70 transition">
                                    <td class="py-3.5 pl-3 font-medium">{{ $index + 1 }}</td>
                                    <td class="py-3.5 px-3 font-bold text-gray-800 whitespace-nowrap">{{ $ins->name }}</td> 
                                    <td class="py-3.5 px-3 font-mono text-gray-500">{{ $ins->email }}</td>
                                    <td class="py-3.5 px-3 text-center">
                                        <span class="bg-blue-50 text-blue-700 font-bold px-2.5 py-1 rounded-md text-xs">
                                            {{ $ins->students->count() }} Siswa
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-3 text-center">
                                        <button onclick="openInstansiStudentsModal('{{ addslashes($ins->name) }}', '{{ json_encode($ins->students) }}')" 
                                                class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-3 py-1.5 rounded-lg transition whitespace-nowrap cursor-pointer shadow-sm">
                                            Detail Siswa
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-6 text-center text-gray-400">Belum ada instansi terdaftar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- REKAP HASIL KUIS PESERTA PUSAT -->
        <div class="md:col-span-3 bg-white p-5 md:p-6 rounded-xl shadow-sm mt-2 mb-8 border border-gray-100">
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
                                <td class="py-3.5 px-3 font-semibold text-gray-800 whitespace-nowrap">{{ $res->user?->name }}</td>
                                <td class="py-3.5 px-3 text-indigo-600 font-medium whitespace-nowrap">{{ $res->quiz?->title }}</td>
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

    <!-- MODAL POP-UP OTORITAS PESERTA -->
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
                            Reset Sandi
                        </button>
                    </form>
                    <form id="form-action-delete" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit" class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-2.5 rounded-xl text-xs transition cursor-pointer" onclick="return confirm('Yakin ingin menghapus permanen peserta ini?')">
                            Hapus Akun
                        </button>
                    </form>
                </div>

                <button class="w-full bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold py-2 rounded-xl text-xs cursor-pointer transition" onclick="closeSecurityGateModal()">
                    Tutup Panel
                </button>
            </div>

        </div>
    </div>

    <!-- MODAL POP-UP BARU: DETAIL SISWA INSTANSI -->
    <div id="instansi-students-modal" class="fixed top-0 left-0 w-full h-full bg-black/60 hidden justify-center items-center z-50 backdrop-blur-sm transition-all duration-300">
        <div class="bg-white p-6 rounded-2xl max-w-2xl w-[90%] shadow-2xl animate-fade-in flex flex-col max-h-[85vh]">
            <div class="flex justify-between items-center border-b border-gray-100 pb-3 mb-4">
                <h3 class="text-base font-bold text-gray-800 flex items-center gap-2">
                    Daftar Siswa: <span id="instansi-target-title" class="text-blue-600 font-extrabold"></span>
                </h3>
                <button onclick="closeInstansiStudentsModal()" class="text-gray-400 hover:text-gray-600 font-bold cursor-pointer text-lg">&times;</button>
            </div>

            <div class="overflow-y-auto flex-1 pr-1 border border-gray-100 rounded-lg">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-200 text-gray-400 text-xs uppercase bg-gray-50 sticky top-0">
                            <th class="py-2.5 pl-3 w-10">No</th>
                            <th class="py-2.5 px-3">Nama Siswa</th>
                            <th class="py-2.5 px-3">Kelas</th>
                            <th class="py-2.5 px-3">Username / Email</th>
                            <th class="py-2.5 px-3">Password</th>
                        </tr>
                    </thead>
                    <tbody id="instansi-students-tbody" class="text-gray-600 text-xs divide-y divide-gray-100">
                        <!-- Disuntikkan via JS -->
                    </tbody>
                </table>
            </div>

            <div class="pt-4 text-right">
                <button onclick="closeInstansiStudentsModal()" class="bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold px-4 py-2 rounded-xl text-xs transition cursor-pointer">
                    Tutup Modal
                </button>
            </div>
        </div>
    </div>

    <script>
        function toggleRows(rowClass, btnId) {
            const rows = document.querySelectorAll('.' + rowClass);
            const btn = document.getElementById(btnId);
            const isHidden = rows[0].classList.contains('hidden');

            if (isHidden) {
                rows.forEach(row => {
                    row.classList.remove('hidden');
                    row.classList.add('animate-fade-in');
                });
                btn.innerHTML = 'Lihat Lebih Sedikit ↑';
            } else {
                rows.forEach(row => {
                    row.classList.add('hidden');
                    row.classList.remove('animate-fade-in');
                });
                btn.innerHTML = 'Lihat Selengkapnya ↓';
            }
        }

        // JS MASTER GATE
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

        // JS UNTUK MODAL DETAIL SISWA INSTANSI
        function openInstansiStudentsModal(instansiName, studentsJson) {
            const students = JSON.parse(studentsJson);
            document.getElementById('instansi-target-title').innerText = instansiName;
            
            const tbody = document.getElementById('instansi-students-tbody');
            tbody.innerHTML = '';

            if(students.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="py-4 text-center text-gray-400">Belum ada akun siswa yang didaftarkan oleh instansi ini.</td></tr>';
            } else {
                students.forEach((st, idx) => {
                    const tr = document.createElement('tr');
                    tr.className = 'hover:bg-gray-50/70 transition';
                    tr.innerHTML = `
                        <td class="py-2.5 pl-3 font-medium">${idx + 1}</td>
                        <td class="py-2.5 px-3 font-semibold text-gray-800">${st.name}</td>
                        <td class="py-2.5 px-3"><span class="bg-indigo-50 text-indigo-700 font-bold px-2 py-0.5 rounded text-[10px]">${st.class_group ?? 'Umum'}</span></td>
                        <td class="py-2.5 px-3 font-mono text-indigo-600">${st.email}</td>
                        <td class="py-2.5 px-3 font-mono text-gray-400">${st.raw_password ?? '***'}</td>
                    `;
                    tbody.appendChild(tr);
                });
            }

            const modal = document.getElementById('instansi-students-modal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeInstansiStudentsModal() {
            const modal = document.getElementById('instansi-students-modal');
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }
    </script>

</body>
</html>