<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
    body { font-family: 'Inter', sans-serif; }
    /* Custom Scrollbar Emerald */
    .sidebar-scroll::-webkit-scrollbar { width: 4px; }
    .sidebar-scroll::-webkit-scrollbar-track { background: transparent; }
    .sidebar-scroll::-webkit-scrollbar-thumb { background: #059669; border-radius: 10px; }
    .font-jakarta { font-family: 'Plus Jakarta Sans', sans-serif; }
    
    /* Efek Glow untuk FAB */
    .fab-glow {
        box-shadow: 0 10px 25px -5px rgba(16, 185, 129, 0.4), 0 8px 10px -6px rgba(16, 185, 129, 0.4);
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
     class="fixed inset-0 bg-black/60 backdrop-blur-sm z-40 md:hidden" x-cloak>
</div>

{{-- 2. SIDEBAR CONTAINER --}}
<aside id="sidebar" 
       :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
       class="fixed inset-y-0 left-0 z-50 w-72 bg-emerald-950 text-emerald-100 min-h-screen flex flex-col border-r border-emerald-900 shadow-2xl transition-transform duration-300 ease-in-out md:static md:flex">
    
    {{-- Brand Logo & Close Button --}}
    <div class="h-20 flex items-center justify-between px-6 border-b border-emerald-900 bg-emerald-950/50 backdrop-blur-sm">
        <a href="{{ route('staff.dashboard') }}" class="flex items-center gap-3 group text-left">
            <div class="w-10 h-10 rounded-xl bg-emerald-600 flex items-center justify-center text-white shadow-lg shadow-emerald-500/30 group-hover:scale-110 transition-transform duration-300">
                <i class="bi bi-book-half text-xl"></i>
            </div>
            <div>
                <h1 class="text-lg font-black text-white tracking-wide leading-none font-jakarta uppercase">LIBRIFY</h1>
                <p class="text-[9px] text-emerald-500 uppercase font-bold tracking-widest mt-1">Librarian Portal</p>
            </div>
        </a>

        {{-- TOMBOL CLOSE (Muncul di Mobile saat Sidebar Terbuka) --}}
        <button @click="sidebarOpen = false" class="md:hidden text-emerald-400 hover:text-white transition-colors p-2">
            <i class="bi bi-x-lg text-2xl"></i>
        </button>
    </div>

    <div class="flex-1 overflow-y-auto sidebar-scroll py-8 px-4 space-y-10 text-left">
        
        {{-- Section 1: Utama --}}
        <div>
            <h3 class="px-2 mb-4 text-[10px] font-black text-emerald-500/60 uppercase tracking-[0.2em]">Pusat Operasional</h3>
            <ul class="space-y-1.5 text-left">
                <li>
                    <a href="{{ route('staff.dashboard') }}" 
                       class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group {{ request()->routeIs('staff.dashboard') ? 'bg-gradient-to-r from-emerald-600 to-emerald-500 text-white shadow-lg shadow-emerald-900/40' : 'hover:bg-emerald-900/50 hover:text-white' }}">
                        <i class="bi bi-grid-1x2-fill w-5 text-center text-lg {{ request()->routeIs('staff.dashboard') ? '' : 'text-emerald-500 group-hover:text-emerald-300' }}"></i>
                        <span class="font-bold text-sm">Dashboard Petugas</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('staff.request') }}" 
                       class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group hover:translate-x-1 {{ request()->routeIs('staff.request') ? 'bg-emerald-900 text-white border-l-4 border-emerald-500' : 'hover:bg-emerald-900/50 hover:text-white' }}">
                        <i class="bi bi-journal-check w-5 text-center text-lg {{ request()->routeIs('staff.request') ? 'text-emerald-400' : 'text-emerald-500 group-hover:text-emerald-300' }} transition-colors"></i>
                        <span class="font-bold text-sm">Sirkulasi Buku</span>
                        <span class="ml-auto bg-emerald-500 text-[9px] font-black px-1.5 py-0.5 rounded text-white tracking-tighter">REQ</span>
                    </a>
                </li>
            </ul>
        </div>

        {{-- Section 2: Pelaporan --}}
        <div>
            <h3 class="px-2 mb-4 text-[10px] font-black text-emerald-500/60 uppercase tracking-[0.2em]">Monitoring</h3>
            <ul class="space-y-1.5 text-left">
                <li>
                    <a href="{{ route('staff.laporan') }}" 
                       class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group hover:translate-x-1 {{ request()->routeIs('staff.laporan') ? 'bg-emerald-900 text-white border-l-4 border-emerald-500' : 'hover:bg-emerald-900/50 hover:text-white' }}">
                        <i class="bi bi-file-earmark-text-fill w-5 text-center text-lg {{ request()->routeIs('staff.laporan') ? 'text-emerald-400' : 'text-emerald-500 group-hover:text-emerald-300' }} transition-colors"></i>
                        <span class="font-bold text-sm">Laporan Koleksi</span>
                    </a>
                </li>
            </ul>
        </div>

    </div>

    {{-- User Profile Section --}}
    <div class="p-4 border-t border-emerald-900 bg-emerald-950/30 text-left">
        <div class="flex items-center gap-3 p-2 rounded-2xl hover:bg-emerald-900 transition-colors group cursor-pointer border border-transparent hover:border-emerald-800">
            <div class="relative">
                <img src="{{ Auth::user()->profile_photo_url }}" class="w-10 h-10 rounded-xl object-cover ring-2 ring-emerald-950 group-hover:ring-emerald-500 transition-all shadow-inner">
                <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-emerald-500 border-2 border-emerald-950 rounded-full flex items-center justify-center">
                    <div class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></div>
                </div>
            </div>
            <div class="overflow-hidden leading-tight text-left">
                <p class="text-sm font-black text-white truncate group-hover:text-emerald-300 transition-colors uppercase font-jakarta tracking-tight">{{ Str::limit(Auth::user()->name, 12) }}</p>
                <p class="text-[9px] text-emerald-500/80 truncate uppercase font-bold tracking-[0.1em] mt-0.5">Staf Perpustakaan</p>
            </div>
        </div>
    </div>
</aside>

{{-- 3. FLOATING ACTION BUTTON (Pengganti Navigasi Bawah) --}}
{{-- Muncul hanya di mobile (md:hidden) --}}
<div class="md:hidden fixed bottom-6 right-6 z-40">
    <button @click="sidebarOpen = true" 
            class="w-14 h-14 bg-emerald-600 text-white rounded-2xl fab-glow flex items-center justify-center active:scale-90 transition-all duration-200 border border-emerald-400/30">
        <i class="bi bi-grid-fill text-2xl"></i>
    </button>
</div>