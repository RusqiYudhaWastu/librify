<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
    body { font-family: 'Inter', sans-serif; }
    /* Custom Scrollbar Blue */
    .sidebar-scroll::-webkit-scrollbar { width: 4px; }
    .sidebar-scroll::-webkit-scrollbar-track { background: transparent; }
    .sidebar-scroll::-webkit-scrollbar-thumb { background: #2563eb; border-radius: 10px; }
    .font-jakarta { font-family: 'Plus Jakarta Sans', sans-serif; }
    
    /* Efek Glow untuk FAB Biru */
    .fab-glow-blue {
        box-shadow: 0 10px 25px -5px rgba(37, 99, 235, 0.4), 0 8px 10px -6px rgba(37, 99, 235, 0.4);
    }
</style>

{{-- 1. OVERLAY GELAP --}}
<div x-show="sidebarOpen" 
     x-transition:enter="transition opacity-linear duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition opacity-linear duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @click="sidebarOpen = false" 
     class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-40 md:hidden" x-cloak>
</div>

{{-- 2. SIDEBAR CONTAINER --}}
<aside id="sidebar-siswa" 
       :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
       class="fixed inset-y-0 left-0 z-50 w-72 bg-slate-950 text-blue-100 min-h-screen flex flex-col border-r border-slate-900 shadow-2xl transition-transform duration-300 ease-in-out md:static md:flex">
    
    {{-- Brand Logo & Close Button --}}
    <div class="h-20 flex items-center justify-between px-6 border-b border-slate-900 bg-slate-950/50 backdrop-blur-sm">
        <a href="{{ route('siswa.dashboard') }}" class="flex items-center gap-3 group text-left">
            <div class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center text-white shadow-lg shadow-blue-500/30 group-hover:scale-110 transition-transform duration-300">
                <i class="bi bi-mortarboard-fill text-xl"></i>
            </div>
            <div>
                <h1 class="text-lg font-black text-white tracking-wide leading-none font-jakarta uppercase">TEKNILOG</h1>
                <p class="text-[9px] text-blue-500 uppercase font-bold tracking-widest mt-1">Siswa Kelas</p>
            </div>
        </a>

        {{-- TOMBOL CLOSE (Muncul di Mobile saat Sidebar Terbuka) --}}
        <button @click="sidebarOpen = false" class="md:hidden text-blue-400 hover:text-white transition-colors p-2">
            <i class="bi bi-x-lg text-2xl"></i>
        </button>
    </div>

    <div class="flex-1 overflow-y-auto sidebar-scroll py-8 px-4 space-y-10 text-left">
        
        {{-- Section 1: Utama --}}
        <div>
            <h3 class="px-2 mb-4 text-[10px] font-black text-blue-500 uppercase tracking-[0.2em]">Navigasi Kelas</h3>
            <ul class="space-y-1.5 text-left">
                <li>
                    <a href="{{ route('siswa.dashboard') }}" 
                       class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group {{ request()->routeIs('siswa.dashboard') ? 'bg-gradient-to-r from-blue-600 to-blue-500 text-white shadow-lg shadow-blue-900/40' : 'hover:bg-slate-900 hover:text-white' }}">
                        <i class="bi bi-grid-1x2-fill w-5 text-center text-lg {{ request()->routeIs('siswa.dashboard') ? '' : 'text-blue-400 group-hover:text-blue-200' }}"></i>
                        <span class="font-bold text-sm">Dashboard</span>
                    </a>
                </li>
            </ul>
        </div>

        {{-- Section 2: Layanan Aset --}}
        <div>
            <h3 class="px-2 mb-4 text-[10px] font-black text-blue-500 uppercase tracking-[0.2em]">Layanan Aset</h3>
            <ul class="space-y-1.5 text-left">
                <li>
                    <a href="{{ route('siswa.request') }}" 
                       class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group hover:translate-x-1 {{ request()->routeIs('siswa.request') ? 'bg-slate-900 text-white border-l-4 border-blue-500' : 'hover:bg-slate-900 hover:text-white' }}">
                        <i class="bi bi-box-fill w-5 text-center text-lg {{ request()->routeIs('siswa.request') ? 'text-blue-400' : 'text-blue-500 group-hover:text-blue-300' }} transition-colors"></i>
                        <span class="font-bold text-sm">Booking Alat</span>
                    </a>
                </li>
            </ul>
        </div>

        {{-- Section 3: Kendala --}}
        <div>
            <h3 class="px-2 mb-4 text-[10px] font-black text-blue-500 uppercase tracking-[0.2em]">Kendala</h3>
            <ul class="space-y-1.5 text-left">
                <li>
                    <a href="{{ route('siswa.laporan') }}" 
                       class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group hover:translate-x-1 {{ request()->routeIs('siswa.laporan') ? 'bg-slate-900 text-white border-l-4 border-blue-500' : 'hover:bg-slate-900 hover:text-white' }}">
                        <i class="bi bi-clipboard-x-fill w-5 text-center text-lg {{ request()->routeIs('siswa.laporan') ? 'text-blue-400' : 'text-blue-500 group-hover:text-blue-300' }} transition-colors"></i>
                        <span class="font-bold text-sm">Lapor Masalah</span>
                    </a>
                </li>
            </ul>
        </div>

    </div>

    {{-- User Profile Section --}}
    <div class="p-4 border-t border-slate-900 bg-slate-950/30 text-left">
        <div class="flex items-center gap-3 p-2 rounded-2xl hover:bg-slate-900 transition-colors group cursor-pointer border border-transparent hover:border-slate-800">
            <div class="relative">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-500 to-sky-500 flex items-center justify-center text-white font-black text-sm ring-2 ring-slate-950 group-hover:ring-blue-500 transition-all text-center uppercase shadow-inner">
                    {{ substr(Auth::user()->name, 0, 2) }}
                </div>
                <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-blue-500 border-2 border-slate-950 rounded-full flex items-center justify-center">
                    <div class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></div>
                </div>
            </div>
            <div class="overflow-hidden leading-tight text-left">
                <p class="text-sm font-black text-white truncate group-hover:text-blue-300 transition-colors uppercase font-jakarta tracking-tight">{{ Auth::user()->name }}</p>
                <p class="text-[9px] text-blue-500/80 truncate uppercase font-bold tracking-[0.1em] mt-0.5">SMKN 1 CIOMAS</p>
            </div>
        </div>
    </div>
</aside>

{{-- 3. FLOATING ACTION BUTTON (Hanya Mobile) --}}
<div class="md:hidden fixed bottom-6 right-6 z-40">
    <button @click="sidebarOpen = true" 
            class="w-14 h-14 bg-blue-600 text-white rounded-2xl fab-glow-blue flex items-center justify-center active:scale-90 transition-all duration-200 border border-blue-400/30">
        <i class="bi bi-grid-fill text-2xl"></i>
    </button>
</div>