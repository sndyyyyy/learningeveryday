<nav class="bg-white/80 backdrop-blur-md shadow-sm px-4 md:px-8 py-3 flex justify-between items-center sticky top-0 z-40 h-16 border-b border-gray-100">
    
    <div class="flex items-center space-x-2">
        <img src="{{ asset('images/le.jpg') }}" alt="Logo" 
             onerror="this.onerror=null; this.src='https://placehold.co/40x40/4f46e5/white?text=LE';" 
             class="w-8 h-8 object-contain rounded-lg shadow-sm" />
        <span class="text-base font-bold text-gray-800 tracking-wide">learning<span class="text-indigo-600">everyday</span></span>
    </div>

    <div class="flex items-center space-x-4 md:space-x-8">
        
        <a href="#tentang-kami" class="text-gray-600 hover:text-indigo-600 font-semibold text-xs md:text-sm transition flex items-center gap-1.5 cursor-pointer">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 md:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <span class="hidden md:inline">Tentang Kami</span>
        </a>

        <a href="{{ url('/login') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl text-xs md:text-sm font-bold transition shadow-sm flex items-center gap-1.5">
            {{-- <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 md:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" /></svg> --}}
            <span class="md:inline">Login</span>
        </a>
    </div>
</nav>