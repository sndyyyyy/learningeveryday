<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Dashboard Utama</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        @keyframes wave {
            0%, 100% { transform: rotate(0deg); }
            25% { transform: rotate(-15deg); }
            75% { transform: rotate(15deg); }
        }
        .animate-wave {
            display: inline-block;
            transform-origin: bottom center;
            animation: wave 1.5s ease-in-out infinite;
        }
    </style>
</head>
<body class="bg-gray-100 font-sans pb-16">

@include('layouts.navbar')

    <!-- CONTAINER UTAMA -->
    <div class="max-w-6xl mx-auto mt-6 px-4 space-y-6">
        
        <!-- ===================================================
             SECTION 0: BANNER WELCOME (ADAPTASI DASHBOARD SISWA)
             =================================================== -->
        <div class="relative bg-gradient-to-r from-indigo-600 to-blue-500 rounded-2xl p-6 md:p-8 shadow-lg text-white overflow-hidden flex flex-col justify-center min-h-[140px]">
            <!-- Ornamen Dekorasi Background -->
            <div class="absolute top-0 right-0 -mt-10 -mr-10 w-40 h-40 bg-white opacity-10 rounded-full blur-2xl pointer-events-none"></div>
            <div class="absolute bottom-0 right-1/4 w-24 h-24 bg-white opacity-10 rounded-full blur-xl pointer-events-none"></div>
            
            <div class="relative z-10 w-full">
                <h1 class="text-2xl md:text-3xl font-black mb-2 flex items-center gap-2 tracking-tight">
                    Selamat Datang Kembali, {{ auth()->user()->name }}! <span class="animate-wave text-3xl">👋</span>
                </h1>
                <p class="text-indigo-100 text-xs md:text-sm leading-relaxed max-w-2xl font-medium uppercase tracking-wider">
                    Hak Akses Tingkat: <span class="text-white font-bold">{{ str_replace('_', ' ', auth()->user()->role) }}</span>
                </p>
            </div>
        </div>

        <!-- ===================================================
             SECTION 1: PINTASAN NAVIGASI UTAMA KHUSUS MOBILE
             =================================================== -->
        <div class="block md:hidden bg-white p-4 rounded-2xl shadow-sm border border-gray-100 space-y-3">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Navigasi Manajemen</h3>
            
            <!-- Grid 4 Kolom jika Super Admin, Grid 3 Kolom jika Admin Instansi -->
            <div class="grid {{ auth()->user()->role === 'super_admin' ? 'grid-cols-4' : 'grid-cols-3' }} gap-2">
                
                @if(auth()->user()->role === 'super_admin')
                    <!-- Tautan Super Admin: Kelola Peserta -->
                    <a href="{{ route('admin.peserta.index') }}" class="flex flex-col items-center justify-center p-2.5 bg-indigo-50/50 hover:bg-indigo-50 border border-indigo-100/50 rounded-xl text-center transition">
                        <div class="w-8 h-8 bg-indigo-600 text-white rounded-lg flex items-center justify-center mb-1.5 shadow-xs">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-2.533-4.656 6.953 6.953 0 0 1-2.212-3.153 4.908 4.908 0 0 1-1.653-.739 4.125 4.125 0 0 0-1.828 3.307 10.56 10.56 0 0 1-1.258 5.485ZM3 19.128a9.38 9.38 0 0 1 2.625.372 9.337 9.337 0 0 1 4.121-.952 4.125 4.125 0 0 1-2.533-4.656 6.953 6.953 0 0 0-2.212-3.153 4.908 4.908 0 0 0-1.653-.739 4.125 4.125 0 0 1-1.828 3.307 10.56 10.56 0 0 0-1.258 5.485ZM12 11.25a3.75 3.75 0 1 0 0-7.5 3.75 3.75 0 0 0 0 7.5Z" />
                            </svg>
                        </div>
                        <span class="text-[9px] font-bold text-gray-700 leading-tight">Peserta</span>
                    </a>
                    
                    <!-- Tautan Super Admin: Bank Soal -->
                    <a href="{{ route('admin.bank.index') }}" class="flex flex-col items-center justify-center p-2.5 bg-blue-50/50 hover:bg-blue-50 border border-blue-100/50 rounded-xl text-center transition">
                        <div class="w-8 h-8 bg-blue-600 text-white rounded-lg flex items-center justify-center mb-1.5 shadow-xs">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 13.5h3.86a2.25 2.25 0 0 1 2.008 1.24l.885 1.77a2.25 2.25 0 0 0 2.007 1.24h1.98a2.25 2.25 0 0 0 2.007-1.24l.885-1.77a2.25 2.25 0 0 1 2.007-1.24h3.86m-18 0h18M2.25 13.5a2.25 2.25 0 0 1 2.25-2.25h15a2.25 2.25 0 0 1 2.25 2.25m-19.5 0v5.25a2.25 2.25 0 0 0 2.25 2.25h15a2.25 2.25 0 0 0 2.25-2.25V13.5m-16.5-9h13.5M9 7.5h6" /></svg>
                        </div>
                        <span class="text-[9px] font-bold text-gray-700 leading-tight">Bank Soal</span>
                    </a>
                    
                    <!-- Tautan Super Admin: Kelola Kuis -->
                    <a href="{{ route('admin.quiz.index') }}" class="flex flex-col items-center justify-center p-2.5 bg-purple-50/50 hover:bg-purple-50 border border-purple-100/50 rounded-xl text-center transition">
                        <div class="w-8 h-8 bg-purple-600 text-white rounded-lg flex items-center justify-center mb-1.5 shadow-xs">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.174c-.053-.462.312-.85.778-.85h13.924c.466 0 .83.388.778.85a49.52 49.52 0 0 1-15.48 0ZM19.5 10.174v6.72a2.25 2.25 0 0 1-2.25 2.25H6.75A2.25 2.25 0 0 1 4.5 16.894v-6.72M12 2.25l8.625 4.5L12 11.25l-8.625-4.5L12 2.25Z" />
                            </svg>
                        </div>
                        <span class="text-[9px] font-bold text-gray-700 leading-tight">Kuis</span>
                    </a>
                    
                    <!-- Tautan Super Admin: Persetujuan Akun -->
                    <a href="{{ route('admin.approval.index') }}" class="flex flex-col items-center justify-center p-2.5 bg-amber-50/50 hover:bg-amber-50 border border-amber-100/50 rounded-xl text-center transition">
                        <div class="w-8 h-8 bg-amber-600 text-white rounded-lg flex items-center justify-center mb-1.5 shadow-xs">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
                            </svg>
                        </div>
                        <span class="text-[9px] font-bold text-gray-700 leading-tight">Persetujuan</span>
                    </a>
                @endif

                @if(auth()->user()->role === 'admin')
                    <!-- Tautan Admin Instansi: Bank Soal -->
                    <a href="{{ route('admin.bank.index') }}" class="flex flex-col items-center justify-center p-2.5 bg-blue-50/50 hover:bg-blue-50 border border-blue-100/50 rounded-xl text-center transition">
                        <div class="w-8 h-8 bg-blue-600 text-white rounded-lg flex items-center justify-center mb-1.5 shadow-xs">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 13.5h3.86a2.25 2.25 0 0 1 2.008 1.24l.885 1.77a2.25 2.25 0 0 0 2.007 1.24h1.98a2.25 2.25 0 0 0 2.007-1.24l.885-1.77a2.25 2.25 0 0 1 2.007-1.24h3.86m-18 0h18M2.25 13.5a2.25 2.25 0 0 1 2.25-2.25h15a2.25 2.25 0 0 1 2.25 2.25m-19.5 0v5.25a2.25 2.25 0 0 0 2.25 2.25h15a2.25 2.25 0 0 0 2.25-2.25V13.5m-16.5-9h13.5M9 7.5h6" /></svg>
                        </div>
                        <span class="text-[9px] font-bold text-gray-700 leading-tight">Bank Soal</span>
                    </a>
                    
                    <!-- Tautan Admin Instansi: Kelola Kuis -->
                    <a href="{{ route('admin.quiz.index') }}" class="flex flex-col items-center justify-center p-2.5 bg-purple-50/50 hover:bg-purple-50 border border-purple-100/50 rounded-xl text-center transition">
                        <div class="w-8 h-8 bg-purple-600 text-white rounded-lg flex items-center justify-center mb-1.5 shadow-xs">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.174c-.053-.462.312-.85.778-.85h13.924c.466 0 .83.388.778.85a49.52 49.52 0 0 1-15.48 0ZM19.5 10.174v6.72a2.25 2.25 0 0 1-2.25 2.25H6.75A2.25 2.25 0 0 1 4.5 16.894v-6.72M12 2.25l8.625 4.5L12 11.25l-8.625-4.5L12 2.25Z" />
                            </svg>
                        </div>
                        <span class="text-[9px] font-bold text-gray-700 leading-tight">Kelola Kuis</span>
                    </a>
                    
                    <!-- Tautan Admin Instansi: Kelola Siswa -->
                    <a href="{{ route('admin.students.index') }}" class="flex flex-col items-center justify-center p-2.5 bg-indigo-50/50 hover:bg-indigo-50 border border-indigo-100/50 rounded-xl text-center transition">
                        <div class="w-8 h-8 bg-indigo-600 text-white rounded-lg flex items-center justify-center mb-1.5 shadow-xs">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.174c-.053-.462.312-.85.778-.85h13.924c.466 0 .83.388.778.85a49.52 49.52 0 0 1-15.48 0ZM19.5 10.174v6.72a2.25 2.25 0 0 1-2.25 2.25H6.75A2.25 2.25 0 0 1 4.5 16.894v-6.72M12 2.25l8.625 4.5L12 11.25l-8.625-4.5L12 2.25Z" />
                            </svg>
                        </div>
                        <span class="text-[9px] font-bold text-gray-700 leading-tight">Kelola Siswa</span>
                    </a>
                @endif
            </div>
        </div>

        <!-- ===================================================
             SECTION 2: GRID STATISTIK DATA ANALYTICS
             =================================================== -->
        <div class="space-y-4">
            <h2 class="text-base font-bold text-gray-800 tracking-wide">Ringkasan Statistik Sistem</h2>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-5">
                
                @if(auth()->user()->role === 'super_admin')
                    <!-- KARTU SUPER ADMIN 1 -->
                    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex flex-col justify-between transition duration-300 hover:shadow-md">
                        <span class="text-xs font-bold text-gray-400 uppercase">Total Instansi Terdaftar</span>
                        <span class="text-3xl font-black text-gray-800 mt-2">{{ $stats['total_instansi'] }}</span>
                    </div>
                    <!-- KARTU SUPER ADMIN 2 -->
                    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex flex-col justify-between transition duration-300 hover:shadow-md">
                        <span class="text-xs font-bold text-gray-400 uppercase">Peserta Mandiri (Pusat)</span>
                        <span class="text-3xl font-black text-gray-800 mt-2">{{ $stats['total_peserta_mandiri'] }}</span>
                    </div>
                    <!-- KARTU SUPER ADMIN 3 -->
                    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex flex-col justify-between transition duration-300 hover:shadow-md">
                        <span class="text-xs font-bold text-gray-400 uppercase">Kuis Produksi Pusat</span>
                        <span class="text-3xl font-black text-gray-800 mt-2">{{ $stats['total_kuis_pusats'] }}</span>
                    </div>
                    <!-- KARTU SUPER ADMIN 4 -->
                    <div class="bg-white p-5 rounded-xl shadow-sm border flex flex-col justify-between transition duration-300 hover:shadow-md {{ $stats['pending_approval'] > 0 ? 'bg-amber-50/50 border-amber-200' : 'border-gray-100' }}">
                        <span class="text-xs font-bold text-gray-400 uppercase">Menunggu Persetujuan</span>
                        <span class="text-3xl font-black {{ $stats['pending_approval'] > 0 ? 'text-amber-600' : 'text-gray-800' }} mt-2">
                            {{ $stats['pending_approval'] }} Akun
                        </span>
                    </div>
                @endif

                @if(auth()->user()->role === 'admin')
                    <!-- KARTU ADMIN INSTANSI 1 -->
                    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex flex-col justify-between transition duration-300 hover:shadow-md">
                        <span class="text-xs font-bold text-gray-400 uppercase">Total Siswa Terdaftar</span>
                        <span class="text-3xl font-black text-gray-800 mt-2">{{ $stats['total_siswa'] }}</span>
                    </div>
                    <!-- KARTU ADMIN INSTANSI 2 -->
                    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex flex-col justify-between transition duration-300 hover:shadow-md">
                        <span class="text-xs font-bold text-gray-400 uppercase">Kuis Internal Sekolah</span>
                        <span class="text-3xl font-black text-gray-800 mt-2">{{ $stats['total_kuis_mandiri'] }}</span>
                    </div>
                    <!-- KARTU ADMIN INSTANSI 3 -->
                    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex flex-col justify-between transition duration-300 hover:shadow-md">
                        <span class="text-xs font-bold text-gray-400 uppercase">Total Ujian Dikerjakan</span>
                        <span class="text-3xl font-black text-gray-800 mt-2">{{ $stats['total_ujian_diikuti'] }} Ujian</span>
                    </div>
                    <!-- KARTU ADMIN INSTANSI 4 -->
                    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex flex-col justify-between transition duration-300 hover:shadow-md">
                        <span class="text-xs font-bold text-gray-400 uppercase">Sisa Kuota Pendaftaran</span>
                        <span class="text-3xl font-black text-indigo-600 mt-2">{{ $stats['sisa_kuota'] }}</span>
                    </div>
                @endif

            </div>
        </div>

    </div>

</body>
</html>