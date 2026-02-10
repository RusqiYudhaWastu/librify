<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
    body { font-family: 'Inter', sans-serif; }
    /* Custom Scrollbar untuk sidebar agar rapi */
    .sidebar-scroll::-webkit-scrollbar { width: 4px; }
    .sidebar-scroll::-webkit-scrollbar-track { background: transparent; }
    .sidebar-scroll::-webkit-scrollbar-thumb { background: #4b5563; border-radius: 10px; }
    .font-jakarta { font-family: 'Plus Jakarta Sans', sans-serif; }
</style>

{{-- SIDEBAR DESKTOP --}}
<aside class="hidden md:flex w-72 bg-slate-900 text-slate-300 min-h-screen flex-col fixed inset-y-0 left-0 z-40 border-r border-slate-800 shadow-2xl transition-all duration-300">
    
    {{-- Logo Brand --}}
    <div class="h-20 flex items-center px-6 border-b border-slate-800 bg-slate-950/50 backdrop-blur-sm">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 group text-left">
            <div class="w-10 h-10 rounded-xl bg-indigo-600 flex items-center justify-center text-white shadow-lg shadow-indigo-500/30 group-hover:scale-110 transition-transform duration-300">
                <i class="bi bi-cpu-fill text-xl"></i>
            </div>
            <div>
                <h1 class="text-lg font-black text-white tracking-wide leading-none font-jakarta">TEKNILOG</h1>
                <p class="text-[9px] text-slate-500 uppercase font-bold tracking-widest mt-1">Admin Panel</p>
            </div>
        </a>
    </div>

    <div class="flex-1 overflow-y-auto sidebar-scroll py-6 px-4 space-y-8 text-left">
        
        {{-- Section 1: Utama --}}
        <div>
            <h3 class="px-2 mb-3 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Pusat Kendali</h3>
            <ul class="space-y-1 text-left">
                <li>
                    <a href="{{ route('admin.dashboard') }}" 
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 group {{ request()->routeIs('admin.dashboard') ? 'bg-gradient-to-r from-indigo-600 to-indigo-500 text-white shadow-md shadow-indigo-900/20' : 'hover:bg-slate-800 hover:text-white' }}">
                        <i class="bi bi-grid-1x2-fill w-5 text-center text-lg {{ request()->routeIs('admin.dashboard') ? '' : 'text-slate-400 group-hover:text-indigo-400' }}"></i>
                        <span class="font-medium text-sm">Dashboard Admin</span>
                    </a>
                </li>
            </ul>
        </div>

        {{-- Section 2: Administrasi --}}
        <div>
            <h3 class="px-2 mb-3 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Manajemen</h3>
            <ul class="space-y-1 text-left">
                <li>
                    <a href="{{ route('admin.pengguna.index') }}" 
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 group hover:translate-x-1 {{ request()->routeIs('admin.pengguna.*') ? 'bg-slate-800 text-white border-l-4 border-indigo-500' : 'hover:bg-slate-800 hover:text-white' }}">
                        <i class="bi bi-people-fill w-5 text-center text-lg {{ request()->routeIs('admin.pengguna.*') ? 'text-indigo-400' : 'text-slate-400 group-hover:text-indigo-400' }} transition-colors"></i>
                        <span class="font-medium text-sm">Database Pengguna</span>
                        <span class="ml-auto bg-slate-700 text-[9px] font-black px-1.5 py-0.5 rounded-md text-slate-300 uppercase">User</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.barang.index') }}" 
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 group hover:translate-x-1 {{ request()->routeIs('admin.barang.*') ? 'bg-slate-800 text-white border-l-4 border-indigo-500' : 'hover:bg-slate-800 hover:text-white' }}">
                        <i class="bi bi-box-seam-fill w-5 text-center text-lg {{ request()->routeIs('admin.barang.*') ? 'text-indigo-400' : 'text-slate-400 group-hover:text-indigo-400' }} transition-colors"></i>
                        <span class="font-medium text-sm">Manajemen Barang</span>
                        <span class="ml-auto bg-indigo-500/20 text-[9px] font-black px-1.5 py-0.5 rounded-md text-indigo-400 uppercase">Aset</span>
                    </a>
                </li>
                {{-- NEW: Menu Kategori Barang --}}
                <li>
                    <a href="{{ route('admin.kategori.index') }}" 
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 group hover:translate-x-1 {{ request()->routeIs('admin.kategori.*') ? 'bg-slate-800 text-white border-l-4 border-indigo-500' : 'hover:bg-slate-800 hover:text-white' }}">
                        <i class="bi bi-tags-fill w-5 text-center text-lg {{ request()->routeIs('admin.kategori.*') ? 'text-indigo-400' : 'text-slate-400 group-hover:text-indigo-400' }} transition-colors"></i>
                        <span class="font-medium text-sm">Kategori Unit</span>
                        <span class="ml-auto bg-indigo-500/20 text-[9px] font-black px-1.5 py-0.5 rounded-md text-indigo-400 uppercase">Tags</span>
                    </a>
                </li>
            </ul>
        </div>

        {{-- Section 3: Monitoring --}}
        <div>
            <h3 class="px-2 mb-3 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Pengawasan</h3>
            <ul class="space-y-1 text-left">
                <li>
                    <a href="{{ route('admin.audit') }}" 
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 group hover:translate-x-1 {{ request()->routeIs('admin.audit') ? 'bg-slate-800 text-white border-l-4 border-indigo-500' : 'hover:bg-slate-800 hover:text-white' }}">
                        <i class="bi bi-fingerprint w-5 text-center text-lg {{ request()->routeIs('admin.audit') ? 'text-indigo-400' : 'text-slate-400 group-hover:text-indigo-400' }} transition-colors"></i>
                        <span class="font-medium text-sm">Audit Sirkulasi</span>
                    </a>
                </li>
            </ul>
        </div>

        {{-- Section 4: Laporan --}}
        <div>
            <h3 class="px-2 mb-3 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Analitik</h3>
            <ul class="space-y-1 text-left">
                <li>
                    <a href="{{ route('admin.laporan') }}" 
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 group hover:translate-x-1 {{ request()->routeIs('admin.laporan') ? 'bg-slate-800 text-white border-l-4 border-pink-500' : 'hover:bg-slate-800 hover:text-white' }}">
                        <i class="bi bi-file-earmark-bar-graph-fill w-5 text-center text-lg {{ request()->routeIs('admin.laporan') ? 'text-pink-400' : 'text-slate-400 group-hover:text-pink-400' }} transition-colors"></i>
                        <span class="font-medium text-sm">Laporan Inventaris</span>
                    </a>
                </li>
            </ul>
        </div>

    </div>

    {{-- User Profile Section --}}
    <div class="p-4 border-t border-slate-800 bg-slate-950/30 text-left">
        <div class="flex items-center gap-3 p-2 rounded-xl hover:bg-slate-800 transition-colors group cursor-pointer">
            <div class="relative">
                <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-indigo-500 to-purple-500 flex items-center justify-center text-white font-bold text-sm ring-2 ring-slate-900 group-hover:ring-indigo-500 transition-all text-center uppercase">
                    {{ substr(Auth::user()->name, 0, 2) }}
                </div>
                <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-slate-900 rounded-full"></div>
            </div>
            <div class="overflow-hidden leading-tight text-left">
                <p class="text-sm font-black text-white truncate group-hover:text-indigo-300 transition-colors uppercase font-jakarta">{{ Auth::user()->name }}</p>
                <p class="text-[9px] text-slate-500 truncate uppercase font-bold tracking-widest mt-0.5">{{ Auth::user()->role }} Master</p>
            </div>
        </div>
    </div>
</aside>

{{-- FOOTER MOBILE --}}
<footer class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.1)] z-50">
    <nav class="grid grid-cols-6 items-center h-16">
        <a href="{{ route('admin.dashboard') }}" class="flex flex-col items-center justify-center w-full h-full relative {{ request()->routeIs('admin.dashboard') ? 'text-indigo-600' : 'text-gray-400' }}">
            <i class="bi bi-grid-1x2-fill text-lg"></i>
            <span class="text-[8px] font-bold mt-1 uppercase">Home</span>
        </a>
        <a href="{{ route('admin.pengguna.index') }}" class="flex flex-col items-center justify-center w-full h-full relative {{ request()->routeIs('admin.pengguna.*') ? 'text-indigo-600' : 'text-gray-400' }}">
            <i class="bi bi-people-fill text-lg"></i>
            <span class="text-[8px] font-bold mt-1 uppercase">Users</span>
        </a>
        <a href="{{ route('admin.barang.index') }}" class="flex flex-col items-center justify-center w-full h-full relative {{ request()->routeIs('admin.barang.*') ? 'text-indigo-600' : 'text-gray-400' }}">
            <i class="bi bi-box-seam-fill text-lg"></i>
            <span class="text-[8px] font-bold mt-1 uppercase">Items</span>
        </a>
        {{-- NEW: Menu Kategori di Mobile Footer --}}
        <a href="{{ route('admin.kategori.index') }}" class="flex flex-col items-center justify-center w-full h-full relative {{ request()->routeIs('admin.kategori.*') ? 'text-indigo-600' : 'text-gray-400' }}">
            <i class="bi bi-tags-fill text-lg"></i>
            <span class="text-[8px] font-bold mt-1 uppercase">Tags</span>
        </a>
        <a href="{{ route('admin.audit') }}" class="flex flex-col items-center justify-center w-full h-full relative {{ request()->routeIs('admin.audit') ? 'text-indigo-600' : 'text-gray-400' }}">
            <i class="bi bi-fingerprint text-lg"></i>
            <span class="text-[8px] font-bold mt-1 uppercase">Audit</span>
        </a>
        <a href="{{ route('admin.laporan') }}" class="flex flex-col items-center justify-center w-full h-full relative {{ request()->routeIs('admin.laporan') ? 'text-indigo-600' : 'text-gray-400' }}">
            <i class="bi bi-file-earmark-bar-graph-fill text-lg"></i>
            <span class="text-[8px] font-bold mt-1 uppercase">Reports</span>
        </a>
    </nav>
</footer>