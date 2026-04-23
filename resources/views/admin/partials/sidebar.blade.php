<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
    body { font-family: 'Inter', sans-serif; }
    /* Custom Scrollbar Indigo */
    .sidebar-scroll::-webkit-scrollbar { width: 4px; }
    .sidebar-scroll::-webkit-scrollbar-track { background: transparent; }
    .sidebar-scroll::-webkit-scrollbar-thumb { background: #4f46e5; border-radius: 10px; }
    .font-jakarta { font-family: 'Plus Jakarta Sans', sans-serif; }

    /* Efek Glow untuk FAB Indigo Admin */
    .fab-glow-admin {
        box-shadow: 0 10px 25px -5px rgba(79, 70, 229, 0.4), 0 8px 10px -6px rgba(79, 70, 229, 0.4);
    }
</style>

{{-- 1. OVERLAY GELAP (Muncul saat sidebar aktif di mobile) --}}
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
<aside id="sidebar-admin" 
       :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
       class="fixed inset-y-0 left-0 z-50 w-72 bg-slate-950 text-slate-300 min-h-screen flex flex-col border-r border-slate-800 shadow-2xl transition-transform duration-300 ease-in-out md:static md:flex">
    
    {{-- Brand Logo & Close Button --}}
    <div class="h-20 flex items-center justify-between px-6 border-b border-slate-800 bg-slate-950/50 backdrop-blur-sm">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 group text-left leading-none">
            <div class="w-10 h-10 rounded-xl bg-indigo-600 flex items-center justify-center text-white shadow-lg shadow-indigo-500/30 group-hover:scale-110 transition-transform duration-300">
                <i class="bi bi-book-half text-xl"></i>
            </div>
            <div class="text-left leading-none">
                <h1 class="text-lg font-black text-white tracking-wide leading-none font-jakarta uppercase">LIBRIFY</h1>
                <p class="text-[9px] text-slate-500 uppercase font-bold tracking-widest mt-1.5 leading-none">Library Oversight</p>
            </div>
        </a>

        {{-- TOMBOL CLOSE (Mobile Only) --}}
        <button @click="sidebarOpen = false" class="md:hidden text-indigo-400 hover:text-white transition-colors p-2 leading-none">
            <i class="bi bi-x-lg text-2xl"></i>
        </button>
    </div>

    <div class="flex-1 overflow-y-auto sidebar-scroll py-8 px-4 space-y-10 text-left">
        
        {{-- Section 1: Pusat Kendali --}}
        <div>
            <h3 class="px-3 mb-4 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] leading-none text-left">Pusat Kendali</h3>
            <ul class="space-y-1.5 text-left">
                <li>
                    <a href="{{ route('admin.dashboard') }}" 
                       class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group {{ request()->routeIs('admin.dashboard') ? 'bg-gradient-to-r from-indigo-600 to-indigo-500 text-white shadow-lg shadow-indigo-900/40' : 'hover:bg-slate-900 hover:text-white' }}">
                        <i class="bi bi-grid-1x2-fill w-5 text-center text-lg {{ request()->routeIs('admin.dashboard') ? '' : 'text-slate-400 group-hover:text-indigo-400' }}"></i>
                        <span class="font-bold text-sm">Dashboard Admin</span>
                    </a>
                </li>
            </ul>
        </div>

        {{-- Section 2: Manajemen Koleksi & User --}}
        <div>
            <h3 class="px-3 mb-4 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] leading-none text-left">Manajemen</h3>
            <ul class="space-y-1.5 text-left">
                <li>
                    <a href="{{ route('admin.pengguna.index') }}" 
                       class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group hover:translate-x-1 {{ request()->routeIs('admin.pengguna.*') ? 'bg-slate-900 text-white border-l-4 border-indigo-500' : 'hover:bg-slate-900 hover:text-white' }}">
                        <i class="bi bi-people-fill w-5 text-center text-lg {{ request()->routeIs('admin.pengguna.*') ? 'text-indigo-400' : 'text-slate-400 group-hover:text-indigo-400' }} transition-colors"></i>
                        <span class="font-bold text-sm">Database Pengguna</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.barang.index') }}" 
                       class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group hover:translate-x-1 {{ request()->routeIs('admin.barang.*') ? 'bg-slate-900 text-white border-l-4 border-indigo-500' : 'hover:bg-slate-900 hover:text-white' }}">
                        <i class="bi bi-journal-bookmark-fill w-5 text-center text-lg {{ request()->routeIs('admin.barang.*') ? 'text-indigo-400' : 'text-slate-400 group-hover:text-indigo-400' }} transition-colors"></i>
                        <span class="font-bold text-sm">Katalog Buku</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.kategori.index') }}" 
                       class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group hover:translate-x-1 {{ request()->routeIs('admin.kategori.*') ? 'bg-slate-900 text-white border-l-4 border-indigo-500' : 'hover:bg-slate-900 hover:text-white' }}">
                        <i class="bi bi-tags-fill w-5 text-center text-lg {{ request()->routeIs('admin.kategori.*') ? 'text-indigo-400' : 'text-slate-400 group-hover:text-indigo-400' }} transition-colors"></i>
                        <span class="font-bold text-sm">Kategori Koleksi</span>
                    </a>
                </li>
            </ul>
        </div>

        {{-- Section 3: Monitoring & Analitik --}}
        <div>
            <h3 class="px-3 mb-4 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] leading-none text-left">Monitoring</h3>
            <ul class="space-y-1.5 text-left">
                <li>
                    <a href="{{ route('admin.audit') }}" 
                       class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group hover:translate-x-1 {{ request()->routeIs('admin.audit') ? 'bg-slate-900 text-white border-l-4 border-indigo-500' : 'hover:bg-slate-900 hover:text-white' }}">
                        <i class="bi bi-arrow-left-right w-5 text-center text-lg {{ request()->routeIs('admin.audit') ? 'text-indigo-400' : 'text-slate-400 group-hover:text-indigo-400' }} transition-colors"></i>
                        <span class="font-bold text-sm">Audit Peminjaman</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.laporan') }}" 
                       class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group hover:translate-x-1 {{ request()->routeIs('admin.laporan') ? 'bg-slate-900 text-white border-l-4 border-pink-500' : 'hover:bg-slate-900 hover:text-white' }}">
                        <i class="bi bi-file-earmark-bar-graph-fill w-5 text-center text-lg {{ request()->routeIs('admin.laporan') ? 'text-pink-400' : 'text-slate-400 group-hover:text-pink-400' }} transition-colors"></i>
                        <span class="font-bold text-sm">Laporan Koleksi</span>
                    </a>
                </li>
            </ul>
        </div>

    </div>

    {{-- User Profile Section --}}
    <div class="p-4 border-t border-slate-800 bg-slate-950/30 text-left">
        <div class="flex items-center gap-3 p-2 rounded-2xl hover:bg-slate-900 transition-colors group cursor-pointer border border-transparent hover:border-slate-800">
            <div class="relative">
                <img src="{{ Auth::user()->profile_photo_url }}" class="w-10 h-10 rounded-xl object-cover ring-2 ring-slate-950 group-hover:ring-indigo-500 transition-all shadow-inner">
                <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-emerald-500 border-2 border-slate-900 rounded-full flex items-center justify-center">
                    <div class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></div>
                </div>
            </div>
            <div class="overflow-hidden leading-tight text-left">
                <p class="text-sm font-black text-white truncate group-hover:text-indigo-300 transition-colors uppercase font-jakarta tracking-tight">{{ Str::limit(Auth::user()->name, 12) }}</p>
                <p class="text-[9px] text-slate-500 truncate uppercase font-bold tracking-[0.1em] mt-0.5">{{ Auth::user()->role }} System</p>
            </div>
        </div>
    </div>
</aside>

{{-- 3. FLOATING ACTION BUTTON (Mobile Only) --}}
<div class="md:hidden fixed bottom-6 right-6 z-40">
    <button @click="sidebarOpen = true" 
            class="w-14 h-14 bg-indigo-600 text-white rounded-2xl fab-glow-admin flex items-center justify-center active:scale-95 transition-all duration-200 border border-indigo-400/30">
        <i class="bi bi-command text-2xl"></i>
    </button>
</div>