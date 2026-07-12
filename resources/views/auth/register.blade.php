<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Akun - learningeveryday</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen p-4 py-12 relative">

    <a href="{{ url('/') }}" class="absolute top-6 left-6 text-gray-500 hover:text-indigo-600 font-semibold text-xs flex items-center gap-1 transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Kembali ke Beranda
    </a>

    <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-lg border border-gray-100 mt-6 animate-fade-in">
        <div class="flex flex-col items-center mb-6">
            <img src="{{ asset('images/le.jpg') }}" alt="Logo" 
                 onerror="this.onerror=null; this.src='https://placehold.co/50x50/4f46e5/white?text=LE';" 
                 class="w-12 h-12 object-contain rounded-xl shadow-sm mb-2" />
            <h2 class="text-xl font-bold text-gray-800 tracking-wide">
                learning<span class="text-indigo-600">everyday</span>
            </h2>
            <p class="text-xs text-gray-400 mt-1">Formulir Pengajuan Akun & Berlangganan</p>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-600 p-4 rounded-xl mb-4 text-xs font-semibold leading-relaxed">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-600 p-3 rounded-xl mb-4 text-xs font-semibold">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ url('/register') }}" method="POST" class="space-y-4">
            @csrf
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-600 font-semibold text-xs mb-1">Nama Lengkap / Instansi</label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="Nama Anda atau Sekolah"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-gray-600 font-semibold text-xs mb-1">Email / Username</label>
                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="nama@email.com"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500">
                </div>
            </div>

            <div>
                <label class="block text-gray-600 font-semibold text-xs mb-2">Pilih Model Berlangganan</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                    <!-- Siswa Basic -->
                    <label class="border-2 border-gray-200 rounded-xl p-3 flex flex-col justify-between cursor-pointer transition select-none hover:border-indigo-500 has-[:checked]:border-indigo-600 has-[:checked]:bg-indigo-50/30">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-bold text-gray-800">Siswa Basic</span>
                            <input type="radio" name="subscription" value="siswa_basic" required class="w-3.5 h-3.5 text-indigo-600">
                        </div>
                        <p class="text-[10px] text-gray-400 leading-normal">Akses terbatas pada koleksi soal standar dari tim pusat.</p>
                    </label>

                    <!-- Siswa Premium -->
                    <label class="border-2 border-gray-200 rounded-xl p-3 flex flex-col justify-between cursor-pointer transition select-none hover:border-indigo-500 has-[:checked]:border-indigo-600 has-[:checked]:bg-indigo-50/30">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-bold text-gray-800">Siswa Premium</span>
                            <input type="radio" name="subscription" value="siswa_premium" class="w-3.5 h-3.5 text-indigo-600">
                        </div>
                        <p class="text-[10px] text-gray-400 leading-normal">Buka seluruh bank soal premium tanpa batasan fitur.</p>
                    </label>

                    <!-- Instansi Basic -->
                    <label class="border-2 border-gray-200 rounded-xl p-3 flex flex-col justify-between cursor-pointer transition select-none hover:border-indigo-500 has-[:checked]:border-indigo-600 has-[:checked]:bg-indigo-50/30">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-bold text-gray-800">Instansi Basic</span>
                            <input type="radio" name="subscription" value="instansi_basic" class="w-3.5 h-3.5 text-indigo-600">
                        </div>
                        <p class="text-[10px] text-gray-400 leading-normal">Mendapat hak kelola admin kuis. Maksimal kuota 50 siswa.</p>
                    </label>

                    <!-- Instansi Premium -->
                    <label class="border-2 border-gray-200 rounded-xl p-3 flex flex-col justify-between cursor-pointer transition select-none hover:border-indigo-500 has-[:checked]:border-indigo-600 has-[:checked]:bg-indigo-50/30">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-bold text-gray-800">Instansi Premium</span>
                            <input type="radio" name="subscription" value="instansi_premium" class="w-3.5 h-3.5 text-indigo-600">
                        </div>
                        <p class="text-[10px] text-gray-400 leading-normal">Kelola admin mandiri tanpa batasan kuis maupun siswa.</p>
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-gray-600 font-semibold text-xs mb-1">Password</label>
                <input type="password" name="password" required placeholder="••••••••"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500">
            </div>

            <button type="submit" 
                class="w-full bg-indigo-600 text-white font-bold py-2.5 rounded-xl hover:bg-indigo-700 transition duration-200 cursor-pointer shadow-sm text-sm">
                Ajukan Registrasi Akun
            </button>

            <div class="text-center pt-2">
                <p class="text-xs text-gray-500">Sudah memesan atau punya akun? <a href="{{ url('/login') }}" class="text-indigo-600 font-bold hover:underline">Masuk disini</a></p>
            </div>
        </form>
    </div>

</body>
</html>