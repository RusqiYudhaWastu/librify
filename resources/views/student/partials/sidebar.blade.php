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
</style>

{{-- SIDEBAR DESKTOP (STUDENT) --}}
<aside class="hidden md:flex w-72 bg-slate-900 text-slate-300 min-h-screen flex-col fixed inset-y-0 left-0 z-40 border-r border-slate-800 shadow-2xl transition-all duration-300">
    
    {{-- Logo Brand --}}
    <div class="h-24 flex items-center px-6 border-b border-slate-800 bg-slate-950/50 backdrop-blur-sm">
        <a href="{{ route('student.dashboard') }}" class="flex items-center gap-3 group text-left">
            <div class="w-10 h-10 rounded-xl bg-cyan-600 flex items-center justify-center text-white shadow-lg shadow-cyan-500/30 group-hover:scale-110 transition-transform duration-300">
                <i class="bi bi-mortarboard-fill text-xl"></i>
            </div>
            <div>
                <h1 class="text-lg font-black text-white tracking-wide leading-none font-jakarta">TEKNILOG</h1>
                <p class="text-[9px] text-slate-500 uppercase font-bold tracking-widest mt-1">Student Area</p>
            </div>
        </a>
    </div>

    <div class="flex-1 overflow-y-auto sidebar-scroll py-8 px-4 space-y-8 text-left">
        
        {{-- Section 1: Menu Utama --}}
        <div>
            <h3 class="px-3 mb-4 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Menu Utama</h3>
            <ul class="space-y-2 text-left">
                {{-- Dashboard --}}
                <li>
                    <a href="{{ route('student.dashboard') }}" 
                       class="flex items-center gap-3 px-3 py-3 rounded-xl transition-all duration-200 group {{ request()->routeIs('student.dashboard') ? 'bg-gradient-to-r from-cyan-600 to-cyan-500 text-white shadow-md shadow-cyan-900/20' : 'hover:bg-slate-800 hover:text-white' }}">
                        <i class="bi bi-grid-fill w-5 text-center text-lg {{ request()->routeIs('student.dashboard') ? '' : 'text-slate-400 group-hover:text-cyan-400' }}"></i>
                        <span class="font-medium text-sm">Dashboard Siswa</span>
                    </a>
                </li>

                {{-- Peminjaman --}}
                <li>
                    <a href="{{ route('student.request') }}" 
                       class="flex items-center gap-3 px-3 py-3 rounded-xl transition-all duration-200 group hover:translate-x-1 {{ request()->routeIs('student.request') ? 'bg-slate-800 text-white border-l-4 border-cyan-500' : 'hover:bg-slate-800 hover:text-white' }}">
                        <i class="bi bi-box-seam-fill w-5 text-center text-lg {{ request()->routeIs('student.request') ? 'text-cyan-400' : 'text-slate-400 group-hover:text-cyan-400' }} transition-colors"></i>
                        <span class="font-medium text-sm">Pinjam Alat</span>
                        <span class="ml-auto bg-cyan-500/20 text-[9px] font-black px-2 py-0.5 rounded-md text-cyan-400 uppercase">Baru</span>
                    </a>
                </li>

                {{-- Laporan --}}
                <li>
                    <a href="{{ route('student.laporan') }}" 
                       class="flex items-center gap-3 px-3 py-3 rounded-xl transition-all duration-200 group hover:translate-x-1 {{ request()->routeIs('student.laporan') ? 'bg-slate-800 text-white border-l-4 border-pink-500' : 'hover:bg-slate-800 hover:text-white' }}">
                        <i class="bi bi-flag-fill w-5 text-center text-lg {{ request()->routeIs('student.laporan') ? 'text-pink-400' : 'text-slate-400 group-hover:text-pink-400' }} transition-colors"></i>
                        <span class="font-medium text-sm">Lapor Kendala</span>
                    </a>
                </li>
            </ul>
        </div>

    </div>

    {{-- User Profile Section --}}
    <div class="p-6 border-t border-slate-800 bg-slate-950/30 text-left">
        <div class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-800 transition-colors group cursor-pointer relative overflow-hidden">
            {{-- Foto Profil --}}
            <div class="relative z-10">
                <div class="w-10 h-10 rounded-full bg-slate-700 flex items-center justify-center text-white font-bold text-sm ring-2 ring-slate-900 group-hover:ring-cyan-500 transition-all overflow-hidden">
                    @if(Auth::user()->profile_photo_url)
                        <img src="{{ Auth::user()->profile_photo_url }}" class="w-full h-full object-cover">
                    @else
                        {{ substr(Auth::user()->name, 0, 2) }}
                    @endif
                </div>
                <div class="absolute bottom-0 right-0 w-3 h-3 bg-emerald-500 border-2 border-slate-900 rounded-full"></div>
            </div>
            
            {{-- Info User --}}
            <div class="overflow-hidden leading-tight text-left z-10 flex-1">
                <p class="text-sm font-black text-white truncate group-hover:text-cyan-300 transition-colors uppercase font-jakarta">{{ Str::limit(Auth::user()->name, 12) }}</p>
                <p class="text-[10px] text-slate-500 truncate uppercase font-bold tracking-widest mt-1">Siswa Aktif</p>
            </div>
        </div>
    </div>
</aside>

{{-- FOOTER MOBILE (STUDENT) --}}
<footer class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.1)] z-50">
    <nav class="grid grid-cols-3 items-center h-20 px-6">
        
        {{-- Menu Dashboard --}}
        <a href="{{ route('student.dashboard') }}" class="flex flex-col items-center justify-center w-full h-full relative group {{ request()->routeIs('student.dashboard') ? 'text-cyan-600' : 'text-gray-400' }}">
            <div class="mb-1 transition-transform group-active:scale-90">
                <i class="bi bi-grid-fill text-xl"></i>
            </div>
            <span class="text-[9px] font-black uppercase tracking-widest">Home</span>
            @if(request()->routeIs('student.dashboard'))
                <span class="absolute top-0 w-12 h-1 bg-cyan-600 rounded-b-lg"></span>
            @endif
        </a>

        {{-- Menu Pinjam --}}
        <a href="{{ route('student.request') }}" class="flex flex-col items-center justify-center w-full h-full relative group {{ request()->routeIs('student.request') ? 'text-cyan-600' : 'text-gray-400' }}">
            <div class="mb-1 transition-transform group-active:scale-90">
                <i class="bi bi-box-seam-fill text-xl"></i>
            </div>
            <span class="text-[9px] font-black uppercase tracking-widest">Pinjam</span>
            @if(request()->routeIs('student.request'))
                <span class="absolute top-0 w-12 h-1 bg-cyan-600 rounded-b-lg"></span>
            @endif
        </a>

        {{-- Menu Laporan --}}
        <a href="{{ route('student.laporan') }}" class="flex flex-col items-center justify-center w-full h-full relative group {{ request()->routeIs('student.laporan') ? 'text-pink-600' : 'text-gray-400' }}">
            <div class="mb-1 transition-transform group-active:scale-90">
                <i class="bi bi-flag-fill text-xl"></i>
            </div>
            <span class="text-[9px] font-black uppercase tracking-widest">Lapor</span>
            @if(request()->routeIs('student.laporan'))
                <span class="absolute top-0 w-12 h-1 bg-pink-600 rounded-b-lg"></span>
            @endif
        </a>

    </nav>
</footer>