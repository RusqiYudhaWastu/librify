<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
    body { font-family: 'Inter', sans-serif; }
    /* Custom Scrollbar untuk sidebar */
    .sidebar-scroll::-webkit-scrollbar { width: 4px; }
    .sidebar-scroll::-webkit-scrollbar-track { background: transparent; }
    .sidebar-scroll::-webkit-scrollbar-thumb { background: #2563eb; border-radius: 10px; }
</style>

{{-- SIDEBAR DESKTOP --}}
<aside class="hidden md:flex w-72 bg-slate-950 text-blue-100 min-h-screen flex-col fixed inset-y-0 left-0 z-40 border-r border-slate-900 shadow-2xl transition-all duration-300">
    
    {{-- Brand Logo --}}
    <div class="h-20 flex items-center px-6 border-b border-slate-900 bg-slate-950/50 backdrop-blur-sm">
        <a href="{{ route('siswa.dashboard') }}" class="flex items-center gap-3 group text-left">
            <div class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center text-white shadow-lg shadow-blue-500/30 group-hover:scale-110 transition-transform duration-300">
                <i class="bi bi-mortarboard-fill text-xl"></i>
            </div>
            <div>
                <h1 class="text-lg font-black text-white tracking-wide leading-none font-jakarta uppercase">TEKNILOG</h1>
                <p class="text-[9px] text-blue-500 uppercase font-bold tracking-widest mt-1">Siswa Kelas</p>
            </div>
        </a>
    </div>

    <div class="flex-1 overflow-y-auto sidebar-scroll py-6 px-4 space-y-8">
        
        {{-- Section 1: Utama --}}
        <div>
            <h3 class="px-2 mb-3 text-[10px] font-black text-blue-500 uppercase tracking-[0.2em]">Navigasi Kelas</h3>
            <ul class="space-y-1">
                <li>
                    <a href="{{ route('siswa.dashboard') }}" 
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 group {{ request()->routeIs('siswa.dashboard') ? 'bg-gradient-to-r from-blue-600 to-blue-500 text-white shadow-md shadow-blue-900/20' : 'hover:bg-slate-900 hover:text-white' }}">
                        <i class="bi bi-grid-1x2-fill w-5 text-center text-lg {{ request()->routeIs('siswa.dashboard') ? '' : 'text-blue-400 group-hover:text-blue-200' }}"></i>
                        <span class="font-medium text-sm">Dashboard</span>
                    </a>
                </li>
            </ul>
        </div>

        {{-- Section 2: Peminjaman --}}
        <div>
            <h3 class="px-2 mb-3 text-[10px] font-black text-blue-500 uppercase tracking-[0.2em]">Layanan Aset</h3>
            <ul class="space-y-1">
                <li>
                    <a href="{{ route('siswa.request') }}" 
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 group hover:translate-x-1 {{ request()->routeIs('siswa.request') ? 'bg-slate-900 text-white border-l-4 border-blue-500' : 'hover:bg-slate-900 hover:text-white' }}">
                        <i class="bi bi-box-fill w-5 text-center text-lg {{ request()->routeIs('siswa.request') ? 'text-blue-400' : 'text-blue-500 group-hover:text-blue-300' }} transition-colors"></i>
                        <span class="font-medium text-sm">Booking Alat</span>
                    </a>
                </li>
            </ul>
        </div>

        {{-- Section 3: Laporan --}}
        <div>
            <h3 class="px-2 mb-3 text-[10px] font-black text-blue-500 uppercase tracking-[0.2em]">Kendala</h3>
            <ul class="space-y-1">
                <li>
                    <a href="{{ route('siswa.laporan') }}" 
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 group hover:translate-x-1 {{ request()->routeIs('siswa.laporan') ? 'bg-slate-900 text-white border-l-4 border-blue-500' : 'hover:bg-slate-900 hover:text-white' }}">
                        <i class="bi bi-clipboard-x-fill w-5 text-center text-lg {{ request()->routeIs('siswa.laporan') ? 'text-blue-400' : 'text-blue-500 group-hover:text-blue-300' }} transition-colors"></i>
                        <span class="font-medium text-sm">Lapor Masalah</span>
                    </a>
                </li>
            </ul>
        </div>

    </div>

    {{-- User Profile Section --}}
    <div class="p-4 border-t border-slate-900 bg-slate-950/30">
        <a href="#" class="flex items-center gap-3 p-2 rounded-xl hover:bg-slate-900 transition-colors group">
            <div class="relative text-center">
                <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-blue-500 to-sky-500 flex items-center justify-center text-white font-bold text-sm ring-2 ring-slate-950 group-hover:ring-blue-500 transition-all uppercase">
                    CL
                </div>
                <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-slate-950 rounded-full"></div>
            </div>
            <div class="overflow-hidden text-left leading-tight">
                <p class="text-sm font-black text-white truncate group-hover:text-blue-300 transition-colors uppercase">XII PPLG 1</p>
                <p class="text-[9px] text-blue-500 truncate uppercase font-bold tracking-widest mt-0.5">SMKN 1 CIOMAS</p>
            </div>
        </a>
    </div>
</aside>

{{-- FOOTER MOBILE --}}
<footer class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.1)] z-50">
    <nav class="grid grid-cols-3 items-center h-16">
        <a href="{{ route('siswa.dashboard') }}" class="flex flex-col items-center justify-center w-full h-full relative {{ request()->routeIs('siswa.dashboard') ? 'text-blue-600' : 'text-gray-400' }}">
            <i class="bi bi-grid-1x2-fill text-xl"></i>
            <span class="text-[9px] font-bold mt-1 uppercase">Home</span>
        </a>
        <a href="{{ route('siswa.request') }}" class="flex flex-col items-center justify-center w-full h-full relative {{ request()->routeIs('siswa.request') ? 'text-blue-600' : 'text-gray-400' }}">
            <i class="bi bi-box-fill text-xl"></i>
            <span class="text-[9px] font-bold mt-1 uppercase">Booking</span>
        </a>
        <a href="{{ route('siswa.laporan') }}" class="flex flex-col items-center justify-center w-full h-full relative {{ request()->routeIs('siswa.laporan') ? 'text-blue-600' : 'text-gray-400' }}">
            <i class="bi bi-clipboard-x-fill text-xl"></i>
            <span class="text-[9px] font-bold mt-1 uppercase">Laporan</span>
        </a>
    </nav>
</footer>