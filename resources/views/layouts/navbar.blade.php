<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
    * {
        font-family: 'Poppins', sans-serif !important;
    }
</style>

<nav class="bg-white shadow-sm px-4 md:px-8 py-3 flex justify-between items-center sticky top-0 z-40 h-16">
    
    <div class="flex items-center">
        <button onclick="toggleSidebar()" class="text-gray-600 hover:text-indigo-600 p-2 focus:outline-none cursor-pointer md:hidden flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
            </svg>
        </button>
        
        <div class="hidden md:flex items-center space-x-2">
            <img src="{{ asset('images/le.jpg') }}" alt="Logo" 
                 onerror="this.onerror=null; this.src='https://placehold.co/40x40/4f46e5/white?text=LE';" 
                 class="w-8 h-8 object-contain rounded-lg shadow-sm" />
            <span class="text-base font-bold text-gray-800 tracking-wide">learning<span class="text-indigo-600">everyday</span></span>
        </div>
    </div>

    <div class="flex items-center">
        <div class="flex md:hidden items-center space-x-2">
            <img src="{{ asset('images/le.jpg') }}" alt="Logo" 
                 onerror="this.onerror=null; this.src='https://placehold.co/40x40/4f46e5/white?text=LE';" 
                 class="w-7 h-7 object-contain rounded-lg shadow-sm" />
            <span class="text-sm font-bold text-gray-800 tracking-wide">learning<span class="text-indigo-600">everyday</span></span>
        </div>

        <div class="hidden md:flex space-x-8 items-center">
            <a href="{{ route('admin.dashboard') }}" 
               class="{{ Request::routeIs('admin.dashboard') ? 'text-indigo-600 font-bold border-b-2 border-indigo-600 pb-1' : 'text-gray-500 hover:text-indigo-600 font-medium' }} text-sm transition tracking-wide">
               Kelola Peserta
            </a>
            <a href="{{ route('admin.bank.index') }}"
               class="{{ Request::routeIs('admin.bank.*') ? 'text-indigo-600 font-bold border-b-2 border-indigo-600 pb-1' : 'text-gray-500 hover:text-indigo-600 font-medium' }} text-sm transition tracking-wide">
               Bank Soal
            </a>
            <a href="{{ route('admin.quiz.index') }}" 
               class="{{ Request::routeIs('admin.quiz.index') || Request::routeIs('admin.quiz.questions') ? 'text-indigo-600 font-bold border-b-2 border-indigo-600 pb-1' : 'text-gray-500 hover:text-indigo-600 font-medium' }} text-sm transition tracking-wide">
               Kelola Kuis
            </a>
        </div>
    </div>

    <div class="flex items-center">
        <form action="{{ route('logout') }}" method="POST" onsubmit="return confirm('Apakah kamu yakin ingin keluar dari sistem?')">
            @csrf
            <button type="submit" class="bg-red-50 hover:bg-red-100 text-red-600 p-2.5 md:px-4 md:py-2 rounded-xl transition text-sm font-semibold flex items-center space-x-2 cursor-pointer border border-red-100 shadow-xs">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M19.5 12l-3-3m3 3-3 3m3-3H9" />
                </svg>
                <span class="hidden md:inline text-xs">Logout</span>
            </button>
        </form>
    </div>
</nav>

<div id="sidebar-overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-black/40 z-50 hidden opacity-0 transition-opacity duration-300 backdrop-blur-xs"></div>

<div id="sidebar-drawer" class="fixed top-0 left-0 bottom-0 w-64 bg-white z-50 -translate-x-full transition-transform duration-300 ease-in-out shadow-2xl flex flex-col">
    <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
        <span class="font-bold text-gray-700 text-sm">Menu Navigasi</span>
        <button onclick="toggleSidebar()" class="text-gray-400 hover:text-gray-600 p-1 cursor-pointer flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
    
    <div class="flex-1 p-4 space-y-2">
        <a href="{{ route('admin.dashboard') }}" 
           class="flex items-center space-x-3 p-3 rounded-xl transition text-sm {{ Request::routeIs('admin.dashboard') ? 'bg-indigo-50 text-indigo-600 font-bold' : 'text-gray-600 hover:bg-gray-50 font-medium' }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-2.533-4.656 6.953 6.953 0 0 1-2.212-3.153 4.908 4.908 0 0 1-1.653-.739 4.125 4.125 0 0 0-1.828 3.307 10.56 10.56 0 0 1-1.258 5.485ZM3 19.128a9.38 9.38 0 0 1 2.625.372 9.337 9.337 0 0 1 4.121-.952 4.125 4.125 0 0 1-2.533-4.656 6.953 6.953 0 0 0-2.212-3.153 4.908 4.908 0 0 0-1.653-.739 4.125 4.125 0 0 1-1.828 3.307 10.56 10.56 0 0 0-1.258 5.485ZM12 11.25a3.75 3.75 0 1 0 0-7.5 3.75 3.75 0 0 0 0 7.5Z" />
            </svg>
            <span>Kelola Peserta</span>
        </a>

        <a href="{{ route('admin.bank.index') }}" 
            class="flex items-center space-x-3 p-3 rounded-xl transition text-sm {{ Request::routeIs('admin.bank.*') ? 'bg-indigo-50 text-indigo-600 font-bold' : 'text-gray-600 hover:bg-gray-50 font-medium' }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 13.5h3.86a2.25 2.25 0 0 1 2.008 1.24l.885 1.77a2.25 2.25 0 0 0 2.007 1.24h1.98a2.25 2.25 0 0 0 2.007-1.24l.885-1.77a2.25 2.25 0 0 1 2.007-1.24h3.86m-18 0h18M2.25 13.5a2.25 2.25 0 0 1 2.25-2.25h15a2.25 2.25 0 0 1 2.25 2.25m-19.5 0v5.25a2.25 2.25 0 0 0 2.25 2.25h15a2.25 2.25 0 0 0 2.25-2.25V13.5m-16.5-9h13.5M9 7.5h6" /></svg>
            <span>Bank Soal</span>
        </a>
        
        <a href="{{ route('admin.quiz.index') }}" 
           class="flex items-center space-x-3 p-3 rounded-xl transition text-sm {{ Request::routeIs('admin.quiz.index') || Request::routeIs('admin.quiz.questions') ? 'bg-indigo-50 text-indigo-600 font-bold' : 'text-gray-600 hover:bg-gray-50 font-medium' }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.174c-.053-.462.312-.85.778-.85h13.924c.466 0 .83.388.778.85a49.52 49.52 0 0 1-15.48 0ZM19.5 10.174v6.72a2.25 2.25 0 0 1-2.25 2.25H6.75A2.25 2.25 0 0 1 4.5 16.894v-6.72M12 2.25l8.625 4.5L12 11.25l-8.625-4.5L12 2.25Z" />
            </svg>
            <span>Kelola Kuis</span>
        </a>
    </div>
    
    <div class="p-4 border-t border-gray-100 text-center">
        <p class="text-[10px] text-gray-400 font-medium">v1.0 &bull; Admin Mode</p>
    </div>
</div>

<script>
    function toggleSidebar() {
        const overlay = document.getElementById('sidebar-overlay');
        const drawer = document.getElementById('sidebar-drawer');
        
        if (drawer.classList.contains('-translate-x-full')) {
            overlay.classList.remove('hidden');
            setTimeout(() => {
                overlay.classList.remove('opacity-0');
                drawer.classList.remove('-translate-x-full');
            }, 10);
        } else {
            drawer.classList.add('-translate-x-full');
            overlay.classList.add('opacity-0');
            setTimeout(() => {
                overlay.classList.add('hidden');
            }, 300);
        }
    }
</script>   