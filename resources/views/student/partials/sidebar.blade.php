<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
    body { font-family: 'Inter', sans-serif; }
    /* Custom Scrollbar Cyan */
    .sidebar-scroll::-webkit-scrollbar { width: 4px; }
    .sidebar-scroll::-webkit-scrollbar-track { background: transparent; }
    .sidebar-scroll::-webkit-scrollbar-thumb { background: #06b6d4; border-radius: 10px; }
    .font-jakarta { font-family: 'Plus Jakarta Sans', sans-serif; }

    /* Efek Glow untuk FAB Cyan */
    .fab-glow-cyan {
        box-shadow: 0 10px 25px -5px rgba(6, 182, 212, 0.4), 0 8px 10px -6px rgba(6, 182, 212, 0.4);
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
<aside id="sidebar-student" 
       :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
       class="fixed inset-y-0 left-0 z-50 w-72 bg-slate-950 text-slate-300 min-h-screen flex flex-col border-r border-slate-900 shadow-2xl transition-transform duration-300 ease-in-out md:static md:flex">
    
    {{-- Logo Brand & Close Button --}}
    <div class="h-24 flex items-center justify-between px-6 border-b border-slate-800 bg-slate-950/50 backdrop-blur-sm">
        <a href="{{ route('student.dashboard') }}" class="flex items-center gap-3 group text-left">
            <div class="w-10 h-10 rounded-xl bg-cyan-600 flex items-center justify-center text-white shadow-lg shadow-cyan-500/30 group-hover:scale-110 transition-transform duration-300">
                <i class="bi bi-mortarboard-fill text-xl"></i>
            </div>
            <div>
                <h1 class="text-lg font-black text-white tracking-wide leading-none font-jakarta uppercase">TEKNILOG</h1>
                <p class="text-[9px] text-slate-500 uppercase font-bold tracking-widest mt-1">Student Portal</p>
            </div>
        </a>

        {{-- TOMBOL CLOSE (Hanya muncul di Mobile saat Sidebar Terbuka) --}}
        <button @click="sidebarOpen = false" class="md:hidden text-cyan-400 hover:text-white transition-colors p-2">
            <i class="bi bi-x-lg text-2xl"></i>
        </button>
    </div>

    <div class="flex-1 overflow-y-auto sidebar-scroll py-8 px-4 space-y-10 text-left">
        
        {{-- Section 1: Menu Utama --}}
        <div>
            <h3 class="px-3 mb-4 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Menu Utama</h3>
            <ul class="space-y-2 text-left">
                <li>
                    <a href="{{ route('student.dashboard') }}" 
                       class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group {{ request()->routeIs('student.dashboard') ? 'bg-gradient-to-r from-cyan-600 to-cyan-500 text-white shadow-lg shadow-cyan-900/40' : 'hover:bg-slate-900 hover:text-white' }}">
                        <i class="bi bi-grid-fill w-5 text-center text-lg {{ request()->routeIs('student.dashboard') ? '' : 'text-slate-400 group-hover:text-cyan-400' }}"></i>
                        <span class="font-bold text-sm">Dashboard Siswa</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('student.request') }}" 
                       class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group hover:translate-x-1 {{ request()->routeIs('student.request') ? 'bg-slate-900 text-white border-l-4 border-cyan-500' : 'hover:bg-slate-900 hover:text-white' }}">
                        <i class="bi bi-box-seam-fill w-5 text-center text-lg {{ request()->routeIs('student.request') ? 'text-cyan-400' : 'text-slate-400 group-hover:text-cyan-400' }} transition-colors"></i>
                        <span class="font-bold text-sm">Pinjam Alat</span>
                        <span class="ml-auto bg-cyan-500/20 text-[9px] font-black px-2 py-0.5 rounded-md text-cyan-400">BARU</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('student.laporan') }}" 
                       class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group hover:translate-x-1 {{ request()->routeIs('student.laporan') ? 'bg-slate-900 text-white border-l-4 border-pink-500' : 'hover:bg-slate-900 hover:text-white' }}">
                        <i class="bi bi-flag-fill w-5 text-center text-lg {{ request()->routeIs('student.laporan') ? 'text-pink-400' : 'text-slate-400 group-hover:text-pink-400' }} transition-colors"></i>
                        <span class="font-bold text-sm">Lapor Kendala</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    {{-- User Profile Section --}}
    <div class="p-6 border-t border-slate-800 bg-slate-950/30 text-left">
        <div class="flex items-center gap-3 p-3 rounded-2xl hover:bg-slate-900 transition-colors group cursor-pointer border border-transparent hover:border-slate-800">
            <div class="relative">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-cyan-500 to-blue-500 flex items-center justify-center text-white font-black text-sm ring-2 ring-slate-950 group-hover:ring-cyan-500 transition-all text-center uppercase shadow-inner">
                    @if(Auth::user()->profile_photo_url)
                        <img src="{{ Auth::user()->profile_photo_url }}" class="w-full h-full object-cover rounded-xl">
                    @else
                        {{ substr(Auth::user()->name, 0, 2) }}
                    @endif
                </div>
                <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-emerald-500 border-2 border-slate-950 rounded-full flex items-center justify-center">
                    <div class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></div>
                </div>
            </div>
            <div class="overflow-hidden leading-tight text-left">
                <p class="text-sm font-black text-white truncate group-hover:text-cyan-300 transition-colors uppercase font-jakarta tracking-tight">{{ Str::limit(Auth::user()->name, 12) }}</p>
                <p class="text-[9px] text-slate-500 truncate uppercase font-bold tracking-[0.1em] mt-0.5">SMKN 1 CIOMAS</p>
            </div>
        </div>
    </div>
</aside>

{{-- 3. FLOATING ACTION BUTTON (Hanya muncul di Mobile) --}}
<div class="md:hidden fixed bottom-6 right-6 z-40">
    <button @click="sidebarOpen = true" 
            class="w-14 h-14 bg-cyan-600 text-white rounded-2xl fab-glow-cyan flex items-center justify-center active:scale-95 transition-all duration-200 border border-cyan-400/30">
        <i class="bi bi-grid-fill text-2xl"></i>
    </button>
</div>