<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<!-- Alpine.js Pembantu Kendali Interactive Dropdown Avatar -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<style>
    * {
        font-family: 'Poppins', sans-serif !important;
    }
    @keyframes fadeIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
    .animate-fade-in { animation: fadeIn 0.2s ease-out forwards; }
</style>

<nav class="bg-white shadow-sm px-4 md:px-8 py-3 flex justify-between items-center sticky top-0 z-40 h-16">
    
    <!-- BRAND / LOGO SISI KIRI -->
    <div class="flex items-center">
        <button onclick="toggleSidebar()" class="text-gray-600 hover:text-indigo-600 p-2 focus:outline-none cursor-pointer md:hidden flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
            </svg>
        </button>
        
        <!-- LOGO DYNAMIC DESKTOP (PRIDE SCHOOL / WHITE LABEL) -->
        <div class="hidden md:flex items-center space-x-2.5">
            @if(auth()->user()->school_logo)
                <img src="{{ asset('storage/' . auth()->user()->school_logo) }}" alt="Logo Instansi" 
                     class="w-8 h-8 object-contain rounded-lg shadow-xs bg-white border border-gray-100" />
                <span class="text-base font-bold text-gray-800 tracking-wide">{{ auth()->user()->name }}</span>
            @else
                <img src="{{ asset('images/le.jpg') }}" alt="Logo LE" 
                     onerror="this.onerror=null; this.src='https://placehold.co/40x40/4f46e5/white?text=LE';" 
                     class="w-8 h-8 object-contain rounded-lg shadow-xs" />
                <span class="text-base font-bold text-gray-800 tracking-wide">learning<span class="text-indigo-600">everyday</span></span>
            @endif
        </div>
    </div>

    <!-- SISI TENGAH: LOGO MOBILE & MENU UTAMA DESKTOP -->
    <div class="flex items-center">
        <!-- LOGO DYNAMIC MOBILE -->
        <div class="flex md:hidden items-center space-x-2">
            @if(auth()->user()->school_logo)
                <img src="{{ asset('storage/' . auth()->user()->school_logo) }}" alt="Logo Instansi" 
                     class="w-7 h-7 object-contain rounded-lg shadow-xs bg-white border border-gray-100" />
                <span class="text-sm font-bold text-gray-800 tracking-wide">{{ auth()->user()->name }}</span>
            @else
                <img src="{{ asset('images/le.jpg') }}" alt="Logo LE" 
                     onerror="this.onerror=null; this.src='https://placehold.co/40x40/4f46e5/white?text=LE';" 
                     class="w-7 h-7 object-contain rounded-lg shadow-xs" />
                <span class="text-sm font-bold text-gray-800 tracking-wide">learning<span class="text-indigo-600">everyday</span></span>
            @endif
        </div>

        <!-- MENU TOP NAVIGATION DESKTOP -->
        <div class="hidden md:flex space-x-8 items-center">
            @if(auth()->user()->role === 'super_admin')
                <a href="{{ route('admin.dashboard.utama') }}" 
                   class="{{ Request::routeIs('admin.dashboard.utama') ? 'text-indigo-600 font-bold border-b-2 border-indigo-600 pb-1' : 'text-gray-500 hover:text-indigo-600 font-medium' }} text-sm transition tracking-wide">
                    Dashboard
                </a>
                <a href="{{ route('admin.peserta.index') }}" 
                   class="{{ Request::routeIs('admin.peserta.*') ? 'text-indigo-600 font-bold border-b-2 border-indigo-600 pb-1' : 'text-gray-500 hover:text-indigo-600 font-medium' }} text-sm transition tracking-wide">
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
                <a href="{{ route('admin.approval.index') }}" 
                   class="{{ Request::routeIs('admin.approval.*') ? 'text-indigo-600 font-bold border-b-2 border-indigo-600 pb-1' : 'text-gray-500 hover:text-indigo-600 font-medium' }} text-sm transition tracking-wide">
                    Persetujuan Akun
                </a>
            @endif

            @if(auth()->user()->role === 'admin')
                <a href="{{ route('admin.dashboard.utama') }}" 
                   class="{{ Request::routeIs('admin.dashboard.utama') ? 'text-indigo-600 font-bold border-b-2 border-indigo-600 pb-1' : 'text-gray-500 hover:text-indigo-600 font-medium' }} text-sm transition tracking-wide">
                    Dashboard
                </a>
                <a href="{{ route('admin.bank.index') }}"
                   class="{{ Request::routeIs('admin.bank.*') ? 'text-indigo-600 font-bold border-b-2 border-indigo-600 pb-1' : 'text-gray-500 hover:text-indigo-600 font-medium' }} text-sm transition tracking-wide">
                    Bank Soal
                </a>
                <a href="{{ route('admin.quiz.index') }}" 
                   class="{{ Request::routeIs('admin.quiz.index') || Request::routeIs('admin.quiz.questions') ? 'text-indigo-600 font-bold border-b-2 border-indigo-600 pb-1' : 'text-gray-500 hover:text-indigo-600 font-medium' }} text-sm transition tracking-wide">
                    Kelola Kuis
                </a>
                <a href="{{ route('admin.students.index') }}" 
                   class="{{ Request::routeIs('admin.students.*') ? 'text-indigo-600 font-bold border-b-2 border-indigo-600 pb-1' : 'text-gray-500 hover:text-indigo-600 font-medium' }} text-sm transition tracking-wide">
                    Kelola Siswa
                </a>
            @endif
        </div>
    </div>

    <!-- AREA KANAN NAVBAR: REVISI KLIEN (IKON PROFILE DROPDOWN SIMPEL) -->
    <div class="relative" x-data="{ open: false }">
        <button @click="open = !open" 
                class="w-10 h-10 rounded-full bg-indigo-50 border border-indigo-100 hover:bg-indigo-100 text-indigo-600 flex items-center justify-center transition cursor-pointer focus:outline-none shadow-xs">
            <!-- Icon Profile User Minimalis -->
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
            </svg>
        </button>

        <!-- MENU DROPDOWN POP-OVER -->
        <div x-show="open" 
             @click.away="open = false" 
             x-transition:enter="transition ease-out duration-100"
             x-transition:enter-start="transform opacity-0 scale-95"
             x-transition:enter-end="transform opacity-100 scale-100"
             class="absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-xl border border-gray-100 py-2 z-50">
            
            <div class="px-4 py-2 border-b border-gray-100">
                <p class="text-xs font-bold text-gray-800 truncate">{{ auth()->user()->name }}</p>
                <p class="text-[10px] text-gray-400 capitalize font-medium">{{ str_replace('_', ' ', auth()->user()->role) }} &bull; {{ auth()->user()->email }}</p>
            </div>

            <!-- Khusus Admin Instansi: Opsi Edit Profil & Logo Sekolah -->
            @if(auth()->user()->role === 'admin')
                <button onclick="openSchoolProfileModal()" 
                        class="w-full text-left px-4 py-2.5 text-xs text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 font-semibold transition flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    Profil & Logo Instansi
                </button>
            @endif

            <form action="{{ route('logout') }}" method="POST" onsubmit="return confirm('Apakah kamu yakin ingin keluar dari sistem?')">
                @csrf
                <button type="submit" 
                        class="w-full text-left px-4 py-2.5 text-xs text-red-600 hover:bg-red-50 font-bold transition flex items-center gap-2 cursor-pointer border-t border-gray-50 mt-1">
                    <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    Keluar Sistem (Logout)
                </button>
            </form>
        </div>
    </div>
</nav>

<div id="sidebar-overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-black/40 z-50 hidden opacity-0 transition-opacity duration-300 backdrop-blur-xs"></div>

<!-- SIDEBAR MOBILE DRAWER -->
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
        @if(auth()->user()->role === 'super_admin')
            <a href="{{ route('admin.dashboard.utama') }}" 
               class="flex items-center space-x-3 p-3 rounded-xl transition text-sm {{ Request::routeIs('admin.dashboard.utama') ? 'bg-indigo-50 text-indigo-600 font-bold' : 'text-gray-600 hover:bg-gray-50 font-medium' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" /></svg>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('admin.peserta.index') }}" 
               class="flex items-center space-x-3 p-3 rounded-xl transition text-sm {{ Request::routeIs('admin.peserta.*') ? 'bg-indigo-50 text-indigo-600 font-bold' : 'text-gray-600 hover:bg-gray-50 font-medium' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-2.533-4.656 6.953 6.953 0 0 1-2.212-3.153 4.908 4.908 0 0 1-1.653-.739 4.125 4.125 0 0 0-1.828 3.307 10.56 10.56 0 0 1-1.258 5.485ZM3 19.128a9.38 9.38 0 0 1 2.625.372 9.337 9.337 0 0 1 4.121-.952 4.125 4.125 0 0 1-2.533-4.656 6.953 6.953 0 0 0-2.212-3.153 4.908 4.908 0 0 0-1.653-.739 4.125 4.125 0 0 1-1.828 3.307 10.56 10.56 0 0 0-1.258 5.485ZM12 11.25a3.75 3.75 0 1 0 0-7.5 3.75 3.75 0 0 0 0 7.5Z" /></svg>
                <span>Kelola Peserta</span>
            </a>
            <a href="{{ route('admin.bank.index') }}" 
               class="flex items-center space-x-3 p-3 rounded-xl transition text-sm {{ Request::routeIs('admin.bank.*') ? 'bg-indigo-50 text-indigo-600 font-bold' : 'text-gray-600 hover:bg-gray-50 font-medium' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 13.5h3.86a2.25 2.25 0 0 1 2.008 1.24l.885 1.77a2.25 2.25 0 0 0 2.007 1.24h1.98a2.25 2.25 0 0 0 2.007-1.24l.885-1.77a2.25 2.25 0 0 1 2.007-1.24h3.86m-18 0h18M2.25 13.5a2.25 2.25 0 0 1 2.25-2.25h15a2.25 2.25 0 0 1 2.25 2.25m-19.5 0v5.25a2.25 2.25 0 0 0 2.25 2.25h15a2.25 2.25 0 0 0 2.25-2.25V13.5m-16.5-9h13.5M9 7.5h6" /></svg>
                <span>Bank Soal</span>
            </a>
            <a href="{{ route('admin.quiz.index') }}" 
               class="flex items-center space-x-3 p-3 rounded-xl transition text-sm {{ Request::routeIs('admin.quiz.index') || Request::routeIs('admin.quiz.questions') ? 'bg-indigo-50 text-indigo-600 font-bold' : 'text-gray-600 hover:bg-gray-50 font-medium' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.174c-.053-.462.312-.85.778-.85h13.924c.466 0 .83.388.778.85a49.52 49.52 0 0 1-15.48 0ZM19.5 10.174v6.72a2.25 2.25 0 0 1-2.25 2.25H6.75A2.25 2.25 0 0 1 4.5 16.894v-6.72M12 2.25l8.625 4.5L12 11.25l-8.625-4.5L12 2.25Z" /></svg>
                <span>Kelola Kuis</span>
            </a>
            <a href="{{ route('admin.approval.index') }}" 
               class="flex items-center space-x-3 p-3 rounded-xl transition text-sm {{ Request::routeIs('admin.approval.*') ? 'bg-indigo-50 text-indigo-600 font-bold' : 'text-gray-600 hover:bg-gray-50 font-medium' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" /></svg>
                <span>Persetujuan Akun</span>
            </a>
        @endif

        @if(auth()->user()->role === 'admin')
            <a href="{{ route('admin.dashboard.utama') }}" 
               class="flex items-center space-x-3 p-3 rounded-xl transition text-sm {{ Request::routeIs('admin.dashboard.utama') ? 'bg-indigo-50 text-indigo-600 font-bold' : 'text-gray-600 hover:bg-gray-50 font-medium' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" /></svg>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('admin.bank.index') }}" 
               class="flex items-center space-x-3 p-3 rounded-xl transition text-sm {{ Request::routeIs('admin.bank.*') ? 'bg-indigo-50 text-indigo-600 font-bold' : 'text-gray-600 hover:bg-gray-50 font-medium' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 13.5h3.86a2.25 2.25 0 0 1 2.008 1.24l.885 1.77a2.25 2.25 0 0 0 2.007 1.24h1.98a2.25 2.25 0 0 0 2.007-1.24l.885-1.77a2.25 2.25 0 0 1 2.007-1.24h3.86m-18 0h18M2.25 13.5a2.25 2.25 0 0 1 2.25-2.25h15a2.25 2.25 0 0 1 2.25 2.25m-19.5 0v5.25a2.25 2.25 0 0 0 2.25 2.25h15a2.25 2.25 0 0 0 2.25-2.25V13.5m-16.5-9h13.5M9 7.5h6" /></svg>
                <span>Bank Soal</span>
            </a>
            <a href="{{ route('admin.quiz.index') }}" 
               class="flex items-center space-x-3 p-3 rounded-xl transition text-sm {{ Request::routeIs('admin.quiz.index') || Request::routeIs('admin.quiz.questions') ? 'bg-indigo-50 text-indigo-600 font-bold' : 'text-gray-600 hover:bg-gray-50 font-medium' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.174c-.053-.462.312-.85.778-.85h13.924c.466 0 .83.388.778.85a49.52 49.52 0 0 1-15.48 0ZM19.5 10.174v6.72a2.25 2.25 0 0 1-2.25 2.25H6.75A2.25 2.25 0 0 1 4.5 16.894v-6.72M12 2.25l8.625 4.5L12 11.25l-8.625-4.5L12 2.25Z" /></svg>
                <span>Kelola Kuis</span>
            </a>
            <a href="{{ route('admin.students.index') }}" 
               class="flex items-center space-x-3 p-3 rounded-xl transition text-sm {{ Request::routeIs('admin.students.*') ? 'bg-indigo-50 text-indigo-600 font-bold' : 'text-gray-600 hover:bg-gray-50 font-medium' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.174c-.053-.462.312-.85.778-.85h13.924c.466 0 .83.388.778.85a49.52 49.52 0 0 1-15.48 0ZM19.5 10.174v6.72a2.25 2.25 0 0 1-2.25 2.25H6.75A2.25 2.25 0 0 1 4.5 16.894v-6.72M12 2.25l8.625 4.5L12 11.25l-8.625-4.5L12 2.25Z" /></svg>
                <span>Kelola Siswa</span>
            </a>
        @endif
    </div>
    
    <div class="p-4 border-t border-gray-100 text-center">
        <p class="text-[10px] text-gray-400 font-medium">v1.0 &bull; Admin Mode</p>
    </div>
</div>

<!-- MODAL POP-UP PENGATURAN PROFIL & LOGO SEKOLAH -->
<div id="school-profile-modal" class="fixed top-0 left-0 w-full h-full bg-black/60 hidden justify-center items-center z-[100] backdrop-blur-xs transition-all duration-300">
    <div class="bg-white p-6 rounded-2xl max-w-sm w-[90%] shadow-2xl animate-fade-in flex flex-col">
        
        <div class="flex justify-between items-center border-b border-gray-100 pb-3 mb-4">
            <h3 class="text-base font-bold text-gray-800 flex items-center gap-1.5">
                <span>🏫</span> Profil Instansi Sekolah
            </h3>
            <button onclick="closeSchoolProfileModal()" class="text-gray-400 hover:text-gray-600 font-bold cursor-pointer text-lg">&times;</button>
        </div>

        <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-left">
            @csrf
            
            <div>
                <label class="block text-gray-600 text-xs font-semibold mb-1">Nama Instansi / Sekolah</label>
                <input type="text" name="name" value="{{ auth()->user()->name }}" required
                       class="w-full px-3 py-2 border border-gray-200 rounded-lg text-xs focus:outline-none focus:border-indigo-500 font-bold text-gray-800">
            </div>

            <div>
                <label class="block text-gray-600 text-xs font-semibold mb-1">Unggah Logo Sekolah (PNG/JPG)</label>
                <input type="file" name="school_logo" accept="image/*"
                       class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-xs focus:outline-none focus:border-indigo-500 text-gray-600">
                <p class="text-[10px] text-gray-400 mt-1">Logo ini akan otomatis tampil di header ujian murid Anda.</p>
            </div>

            @if(auth()->user()->school_logo)
                <div class="p-3 bg-gray-50 border border-gray-100 rounded-xl flex items-center gap-3">
                    <img src="{{ asset('storage/' . auth()->user()->school_logo) }}" class="w-10 h-10 object-contain rounded-lg border bg-white" />
                    <div>
                        <p class="text-[10px] font-bold text-gray-700">Logo Sekolah Saat Ini</p>
                        <p class="text-[9px] text-emerald-600 font-semibold">✔ Terpasang di Sistem</p>
                    </div>
                </div>
            @endif

            <div class="flex flex-row gap-2 w-full pt-2">
                <button type="button" class="w-1/2 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold py-2.5 rounded-xl text-xs cursor-pointer transition" onclick="closeSchoolProfileModal()">
                    Batal
                </button>
                <button type="submit" class="w-1/2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 rounded-xl text-xs cursor-pointer transition shadow-xs">
                    Simpan Profil
                </button>
            </div>
        </form>
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

    function openSchoolProfileModal() {
        const modal = document.getElementById('school-profile-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeSchoolProfileModal() {
        const modal = document.getElementById('school-profile-modal');
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }
</script>