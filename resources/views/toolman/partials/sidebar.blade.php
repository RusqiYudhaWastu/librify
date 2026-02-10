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
</style>

{{-- SIDEBAR DESKTOP --}}
<aside class="hidden md:flex w-72 bg-emerald-950 text-emerald-100 min-h-screen flex-col fixed inset-y-0 left-0 z-40 border-r border-emerald-900 shadow-2xl transition-all duration-300">
    
    {{-- Brand Logo --}}
    <div class="h-20 flex items-center px-6 border-b border-emerald-900 bg-emerald-950/50 backdrop-blur-sm">
        <a href="{{ route('toolman.dashboard') }}" class="flex items-center gap-3 group text-left">
            <div class="w-10 h-10 rounded-xl bg-emerald-600 flex items-center justify-center text-white shadow-lg shadow-emerald-500/30 group-hover:scale-110 transition-transform duration-300">
                <i class="bi bi-tools text-xl"></i>
            </div>
            <div>
                <h1 class="text-lg font-black text-white tracking-wide leading-none font-jakarta">TEKNILOG</h1>
                <p class="text-[9px] text-emerald-500 uppercase font-bold tracking-widest mt-1">Toolman Panel</p>
            </div>
        </a>
    </div>

    <div class="flex-1 overflow-y-auto sidebar-scroll py-8 px-4 space-y-10 text-left">
        
        {{-- Section 1: Utama --}}
        <div>
            <h3 class="px-2 mb-4 text-[10px] font-black text-emerald-500/60 uppercase tracking-[0.2em]">Pusat Operasional</h3>
            <ul class="space-y-1.5 text-left">
                <li>
                    <a href="{{ route('toolman.dashboard') }}" 
                       class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group {{ request()->routeIs('toolman.dashboard') ? 'bg-gradient-to-r from-emerald-600 to-emerald-500 text-white shadow-lg shadow-emerald-900/40' : 'hover:bg-emerald-900/50 hover:text-white' }}">
                        <i class="bi bi-grid-1x2-fill w-5 text-center text-lg {{ request()->routeIs('toolman.dashboard') ? '' : 'text-emerald-500 group-hover:text-emerald-300' }}"></i>
                        <span class="font-bold text-sm">Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('toolman.request') }}" 
                       class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group hover:translate-x-1 {{ request()->routeIs('toolman.request') ? 'bg-emerald-900 text-white border-l-4 border-emerald-500' : 'hover:bg-emerald-900/50 hover:text-white' }}">
                        <i class="bi bi-clipboard2-check-fill w-5 text-center text-lg {{ request()->routeIs('toolman.request') ? 'text-emerald-400' : 'text-emerald-500 group-hover:text-emerald-300' }} transition-colors"></i>
                        <span class="font-bold text-sm">Permintaan Pinjam</span>
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
                    <a href="{{ route('toolman.laporan') }}" 
                       class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group hover:translate-x-1 {{ request()->routeIs('toolman.laporan') ? 'bg-emerald-900 text-white border-l-4 border-emerald-500' : 'hover:bg-emerald-900/50 hover:text-white' }}">
                        <i class="bi bi-file-earmark-text-fill w-5 text-center text-lg {{ request()->routeIs('toolman.laporan') ? 'text-emerald-400' : 'text-emerald-500 group-hover:text-emerald-300' }} transition-colors"></i>
                        <span class="font-bold text-sm">Laporan Kerusakan</span>
                    </a>
                </li>
            </ul>
        </div>

    </div>

    {{-- User Profile Section --}}
    <div class="p-4 border-t border-emerald-900 bg-emerald-950/30 text-left">
        <div class="flex items-center gap-3 p-2 rounded-2xl hover:bg-emerald-900 transition-colors group cursor-pointer border border-transparent hover:border-emerald-800">
            <div class="relative">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-500 to-teal-500 flex items-center justify-center text-white font-black text-sm ring-2 ring-emerald-950 group-hover:ring-emerald-500 transition-all text-center uppercase shadow-inner">
                    {{ substr(Auth::user()->name, 0, 2) }}
                </div>
                <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-emerald-500 border-2 border-emerald-950 rounded-full flex items-center justify-center">
                    <div class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></div>
                </div>
            </div>
            <div class="overflow-hidden leading-tight text-left">
                <p class="text-sm font-black text-white truncate group-hover:text-emerald-300 transition-colors uppercase font-jakarta tracking-tight">{{ Auth::user()->name }}</p>
                <p class="text-[9px] text-emerald-500/80 truncate uppercase font-bold tracking-[0.1em] mt-0.5">{{ Auth::user()->role }} Dept.</p>
            </div>
        </div>
    </div>
</aside>

{{-- FOOTER MOBILE --}}
<footer class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-100 shadow-[0_-4px_10px_-1px_rgba(0,0,0,0.05)] z-50">
    <nav class="grid grid-cols-3 items-center h-16">
        <a href="{{ route('toolman.dashboard') }}" class="flex flex-col items-center justify-center w-full h-full relative {{ request()->routeIs('toolman.dashboard') ? 'text-emerald-600' : 'text-gray-400' }}">
            <i class="bi bi-grid-1x2-fill text-xl"></i>
            <span class="text-[9px] font-black mt-1 uppercase">Home</span>
            @if(request()->routeIs('toolman.dashboard'))
                <div class="absolute top-0 w-8 h-1 bg-emerald-600 rounded-b-full"></div>
            @endif
        </a>
        <a href="{{ route('toolman.request') }}" class="flex flex-col items-center justify-center w-full h-full relative {{ request()->routeIs('toolman.request') ? 'text-emerald-600' : 'text-gray-400' }}">
            <i class="bi bi-clipboard2-check-fill text-xl"></i>
            <span class="text-[9px] font-black mt-1 uppercase">Request</span>
            @if(request()->routeIs('toolman.request'))
                <div class="absolute top-0 w-8 h-1 bg-emerald-600 rounded-b-full"></div>
            @endif
        </a>
        <a href="{{ route('toolman.laporan') }}" class="flex flex-col items-center justify-center w-full h-full relative {{ request()->routeIs('toolman.laporan') ? 'text-emerald-600' : 'text-gray-400' }}">
            <i class="bi bi-file-earmark-text-fill text-xl"></i>
            <span class="text-[9px] font-black mt-1 uppercase">Laporan</span>
            @if(request()->routeIs('toolman.laporan'))
                <div class="absolute top-0 w-8 h-1 bg-emerald-600 rounded-b-full"></div>
            @endif
        </a>
    </nav>
</footer>