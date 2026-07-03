<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - learningeveryday</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            font-family: 'Poppins', sans-serif !important;
        }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen p-4">

    <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md border border-gray-100">
        
        <div class="flex flex-col items-center mb-6">
            <img src="{{ asset('images/le.jpg') }}" alt="Logo" 
                 onerror="this.onerror=null; this.src='https://placehold.co/50x50/4f46e5/white?text=LE';" 
                 class="w-12 h-12 object-contain rounded-xl shadow-sm mb-2" />
            <h2 class="text-xl font-bold text-gray-800 tracking-wide">
                learning<span class="text-indigo-600">everyday</span>
            </h2>
            <p class="text-xs text-gray-400 mt-1">Silakan masuk untuk memulai kuis interaktif</p>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-600 p-3 rounded-xl mb-4 text-xs font-semibold">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ url('/login') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-gray-600 font-semibold text-xs mb-1">Email</label>
                <input type="text" name="email" value="{{ old('email') }}" required placeholder="nama@email.com"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-gray-600 font-semibold text-xs mb-1">Password</label>
                <div class="relative flex items-center">
                    <input type="password" name="password" id="login_password" required placeholder="••••••••"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 pr-10">
                    
                    <button type="button" onclick="togglePasswordVisibility()" class="absolute right-3 text-gray-400 hover:text-indigo-600 focus:outline-none cursor-pointer p-1">
                        <svg id="eye_open" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.645C3.304 7.852 7.14 5 12 5c4.86 0 8.696 2.852 9.964 6.678a1.012 1.012 0 0 1 0 .645C20.696 16.148 16.86 19 12 19c-4.86 0-8.696-2.852-9.964-6.678Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                        <svg id="eye_close" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 hidden">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.822 7.822L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                        </svg>
                    </button>
                </div>
            </div>

            <button type="submit" 
                class="w-full bg-indigo-600 text-white font-bold py-2.5 rounded-xl hover:bg-indigo-700 transition duration-200 cursor-pointer shadow-sm text-sm">
                Masuk
            </button>
        </form>
    </div>

    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('login_password');
            const eyeOpenIcon = document.getElementById('eye_open');
            const eyeCloseIcon = document.getElementById('eye_close');

            if (passwordInput.type === 'password') {
                // Ubah tipe ke teks agar password kelihatan
                passwordInput.type = 'text';
                eyeOpenIcon.classList.add('hidden');
                eyeCloseIcon.classList.remove('hidden');
            } else {
                // Kembalikan ke tipe password agar tersembunyi bulat-bulat
                passwordInput.type = 'password';
                eyeCloseIcon.classList.add('hidden');
                eyeOpenIcon.classList.remove('hidden');
            }
        }
    </script>
</body>
</html>