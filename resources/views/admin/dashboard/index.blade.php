<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard - TekniLog</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
        .font-jakarta { font-family: 'Plus Jakarta Sans', sans-serif; }
        .custom-scroll::-webkit-scrollbar { width: 5px; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #6366f1; border-radius: 20px; }
        
        .glass-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }
    </style>
</head>

<body class="antialiased flex h-screen w-full overflow-hidden text-left font-jakarta" 
      x-data="{ 
        sidebarOpen: false, 
        currentTime: '',
        init() {
            setInterval(() => {
                const now = new Date();
                this.currentTime = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            }, 1000);
        }
      }">

    {{-- Sidebar Indigo Theme --}}
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'" 
           class="fixed inset-y-0 left-0 z-50 flex flex-col w-72 bg-[#0F172A] text-white transition-transform duration-300 ease-in-out border-r border-slate-800 md:static md:flex-shrink-0 h-full">
        @include('admin.partials.sidebar')
    </aside>

    <div class="flex flex-1 flex-col h-full min-w-0 overflow-hidden">
        {{-- Header --}}
        @include('admin.partials.header')

        <main class="flex-1 overflow-y-auto p-6 lg:p-10 pt-2 custom-scroll text-left">
            <div class="mx-auto w-full max-w-[1550px] space-y-8 text-left">
                
                {{-- 1. HERO SECTION: DINAMIS --}}
                <div class="relative overflow-hidden rounded-[2.5rem] bg-indigo-600 p-10 lg:p-12 shadow-2xl shadow-indigo-200">
                    <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-8 text-left leading-none">
                        <div class="space-y-4 text-left">
                            <div class="flex items-center gap-3">
                                <span class="px-3 py-1 bg-white/20 rounded-lg text-[10px] font-black text-white uppercase tracking-widest backdrop-blur-sm">Oversight Mode</span>
                                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                <span class="text-[10px] font-bold text-indigo-100 uppercase">Sistem Online</span>
                            </div>
                            <h1 class="text-3xl lg:text-5xl font-black text-white tracking-tight">
                                Halo, {{ Auth::user()->name }}! 👋
                            </h1>
                            <p class="text-indigo-100 font-medium text-sm lg:text-base max-w-lg leading-relaxed">
                                Pantau sirkulasi alat dari seluruh jurusan di **SMKN 1 CIOMAS** melalui panel kendali utama lu hari ini.
                            </p>
                        </div>
                        <div class="glass-card px-10 py-7 rounded-[2.5rem] text-center shadow-2xl min-w-[220px]">
                            <p class="text-[10px] font-black text-indigo-600 uppercase tracking-[0.2em] mb-2">Waktu Operasional</p>
                            <p class="text-4xl font-black text-slate-900 tabular-nums leading-none" x-text="currentTime"></p>
                            <p class="text-[10px] font-bold text-slate-400 mt-3 uppercase tracking-widest">{{ now()->translatedFormat('d M Y') }}</p>
                        </div>
                    </div>
                    <div class="absolute -right-20 -top-20 h-80 w-80 rounded-full bg-indigo-500/30 blur-3xl"></div>
                </div>

                {{-- 2. KPI STATS GRID (DINAMIS DARI $stats) --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 text-left">
                    <div class="bg-white p-7 rounded-[2.5rem] border border-gray-100 shadow-sm group hover:shadow-md transition-all">
                        <div class="flex justify-between items-start mb-4">
                            <div class="w-14 h-14 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform shadow-inner"><i class="bi bi-people-fill"></i></div>
                            <span class="text-[9px] font-black text-emerald-500 bg-emerald-50 px-2 py-1 rounded-lg uppercase">Total</span>
                        </div>
                        <p class="text-3xl font-black text-gray-900 leading-none">{{ $stats['total_users'] }}</p>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-2">Akun Terdaftar</p>
                    </div>

                    <div class="bg-white p-7 rounded-[2.5rem] border border-gray-100 shadow-sm group hover:shadow-md transition-all">
                        <div class="flex justify-between items-start mb-4">
                            <div class="w-14 h-14 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform shadow-inner"><i class="bi bi-arrow-left-right"></i></div>
                            <span class="text-[9px] font-black text-blue-500 bg-blue-50 px-2 py-1 rounded-lg uppercase">Live</span>
                        </div>
                        <p class="text-3xl font-black text-gray-900 leading-none">{{ $stats['active_loans'] }}</p>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-2">Sedang Dipinjam</p>
                    </div>

                    <div class="bg-white p-7 rounded-[2.5rem] border border-gray-100 shadow-sm group hover:shadow-md transition-all">
                        <div class="flex justify-between items-start mb-4">
                            <div class="w-14 h-14 bg-pink-50 text-pink-600 rounded-2xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform shadow-inner"><i class="bi bi-tools"></i></div>
                            <span class="text-[9px] font-black text-pink-500 bg-pink-50 px-2 py-1 rounded-lg uppercase tracking-tighter">Alert</span>
                        </div>
                        <p class="text-3xl font-black text-pink-600 leading-none">{{ $stats['maintenance'] }}</p>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-2">Laporan Masalah</p>
                    </div>

                    <div class="bg-slate-900 p-7 rounded-[2.5rem] shadow-xl flex flex-col justify-between overflow-hidden relative border border-slate-800">
                        <div class="relative z-10 text-left leading-none">
                            <p class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-2">Integrity Score</p>
                            <p class="text-3xl font-black text-white">98%</p>
                        </div>
                        <div class="relative z-10 flex items-center gap-2 text-emerald-400 mt-4">
                            <i class="bi bi-shield-lock-fill text-xl"></i>
                            <span class="text-[10px] font-black uppercase tracking-widest">Sistem Aman</span>
                        </div>
                        <i class="bi bi-fingerprint absolute -right-4 top-1/2 -translate-y-1/2 text-[100px] text-white/5"></i>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 text-left">
                    
                    {{-- 3. AUDIT SIRKULASI GLOBAL (DINAMIS DARI $recentActivities) --}}
                    <div class="lg:col-span-8 space-y-8 text-left">
                        <div class="bg-white rounded-[3rem] border border-gray-100 shadow-sm overflow-hidden text-left">
                            <div class="p-8 border-b border-gray-50 flex items-center justify-between bg-gray-50/30 text-left leading-none">
                                <div class="text-left leading-none">
                                    <h3 class="text-xl font-black text-gray-900 uppercase tracking-tight">Log Sirkulasi Terkini</h3>
                                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-2">Audit Lintas Jurusan</p>
                                </div>
                                <a href="{{ route('admin.audit') }}" class="px-5 py-3 bg-indigo-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition-all">Buka Audit</a>
                            </div>
                            <div class="overflow-x-auto text-left">
                                <table class="w-full text-left text-sm">
                                    <thead>
                                        <tr class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] border-b border-gray-50 bg-gray-50/20">
                                            <th class="px-8 py-5">Waktu</th>
                                            <th class="px-8 py-5">PIC / Kelas</th>
                                            <th class="px-8 py-5">Aktivitas Item</th>
                                            <th class="px-8 py-5 text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-50">
                                        @forelse($recentActivities as $activity)
                                        <tr class="group hover:bg-gray-50/50 transition-all">
                                            <td class="px-8 py-5 font-bold text-gray-400 text-[11px]">{{ $activity->created_at->format('H:i') }} WIB</td>
                                            <td class="px-8 py-5">
                                                <div class="flex flex-col text-left leading-tight">
                                                    <span class="font-black text-gray-900 uppercase">{{ $activity->user->name }}</span>
                                                    <span class="text-[9px] text-indigo-500 font-bold uppercase">{{ $activity->user->role === 'class' ? 'Siswa' : 'Unit Toolman' }}</span>
                                                </div>
                                            </td>
                                            <td class="px-8 py-5 text-left leading-tight">
                                                <p class="font-bold text-gray-700">{{ $activity->quantity }}x {{ $activity->item->name }}</p>
                                            </td>
                                            <td class="px-8 py-5 text-center">
                                                @php
                                                    $badgeStyle = match($activity->status) {
                                                        'pending'  => 'bg-orange-50 text-orange-600',
                                                        'approved' => 'bg-blue-50 text-blue-600',
                                                        'returned' => 'bg-emerald-50 text-emerald-600',
                                                        default    => 'bg-slate-50 text-slate-400'
                                                    };
                                                @endphp
                                                <span class="px-3 py-1.5 {{ $badgeStyle }} rounded-lg text-[9px] font-black uppercase tracking-widest border border-current/10">{{ $activity->status }}</span>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="4" class="py-10 text-center text-gray-400 font-bold uppercase text-[10px] tracking-widest italic">Belum ada aktivitas sirkulasi terdeteksi.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- UTILISASI PER JURUSAN (DINAMIS DARI $categories) --}}
                        <div class="bg-white rounded-[3rem] p-10 border border-gray-100 shadow-sm text-left">
                            <h3 class="text-sm font-black text-gray-800 mb-10 uppercase tracking-widest text-left leading-none">Distribusi Aset Per Kategori</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10">
                                @foreach($categories as $cat)
                                <div class="space-y-3 text-left">
                                    <div class="flex justify-between text-[10px] font-black uppercase leading-none">
                                        <span class="text-gray-400 tracking-tighter">{{ $cat->name }}</span>
                                        <span class="text-indigo-600">{{ $cat->items_count }}</span>
                                    </div>
                                    <div class="h-2 w-full bg-gray-50 rounded-full overflow-hidden shadow-inner">
                                        <div class="h-full bg-indigo-500 rounded-full transition-all duration-1000" 
                                             style="width: {{ ($cat->items_count / max($stats['total_items'], 1)) * 100 }}%"></div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- 4. SIDEBAR ACTIONS (DINAMIS ROLE DISTRIBUTION) --}}
                    <div class="lg:col-span-4 space-y-8 text-left">
                        {{-- QUICK LINKS --}}
                        <div class="bg-slate-900 rounded-[3rem] p-10 text-white shadow-2xl relative overflow-hidden text-left border border-slate-800">
                            <h3 class="text-xl font-bold font-jakarta mb-8 relative z-10 leading-none">Aksi Cepat</h3>
                            <div class="space-y-4 relative z-10">
                                <a href="{{ route('admin.pengguna.index') }}" class="flex items-center gap-4 p-5 bg-white/5 rounded-2xl border border-white/5 hover:bg-indigo-600 transition-all group">
                                    <div class="w-11 h-11 rounded-xl bg-white/10 flex items-center justify-center text-slate-400 group-hover:text-white transition-colors shadow-inner"><i class="bi bi-person-plus text-xl"></i></div>
                                    <span class="text-xs font-black uppercase tracking-widest leading-none">Kelola Pengguna</span>
                                </a>
                                <a href="{{ route('admin.barang.index') }}" class="flex items-center gap-4 p-5 bg-white/5 rounded-2xl border border-white/5 hover:bg-indigo-600 transition-all group">
                                    <div class="w-11 h-11 rounded-xl bg-white/10 flex items-center justify-center text-slate-400 group-hover:text-white transition-colors shadow-inner"><i class="bi bi-box-seam text-xl"></i></div>
                                    <span class="text-xs font-black uppercase tracking-widest leading-none">Input Inventaris</span>
                                </a>
                            </div>
                            <i class="bi bi-lightning-fill absolute -right-8 -bottom-8 text-[150px] text-white/5"></i>
                        </div>

                        {{-- SESSION TRACKER (DINAMIS ROLE COUNT) --}}
                        <div class="bg-white rounded-[3rem] p-10 border border-gray-100 shadow-sm text-left">
                            <h3 class="text-sm font-black text-gray-800 mb-8 uppercase tracking-widest leading-none text-left">Otoritas Akun</h3>
                            <div class="space-y-6 text-left">
                                <div class="flex items-center justify-between p-4 bg-indigo-50/50 rounded-2xl border border-indigo-100/50">
                                    <div class="flex items-center gap-4">
                                        <div class="w-9 h-9 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-bold text-[10px] shadow-lg">AD</div>
                                        <span class="text-xs font-bold text-gray-700">Administrator</span>
                                    </div>
                                    <span class="text-xs font-black text-indigo-600">{{ sprintf('%02d', $stats['role_admin'] ?? 0) }} Akun</span>
                                </div>
                                <div class="flex items-center justify-between p-4 bg-emerald-50/50 rounded-2xl border border-emerald-100/50">
                                    <div class="flex items-center gap-4">
                                        <div class="w-9 h-9 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-bold text-[10px] shadow-lg">TL</div>
                                        <span class="text-xs font-bold text-gray-700">Toolman Unit</span>
                                    </div>
                                    <span class="text-xs font-black text-emerald-600">{{ sprintf('%02d', $stats['role_toolman'] ?? 0) }} Akun</span>
                                </div>
                                <div class="flex items-center justify-between p-4 bg-blue-50/50 rounded-2xl border border-blue-100/50">
                                    <div class="flex items-center gap-4 text-left">
                                        <div class="w-9 h-9 rounded-xl bg-blue-600 text-white flex items-center justify-center font-bold text-[10px] shadow-lg">CL</div>
                                        <span class="text-xs font-bold text-gray-700">Perwakilan Kelas</span>
                                    </div>
                                    <span class="text-xs font-black text-blue-600">{{ sprintf('%02d', $stats['role_class'] ?? 0) }} Akun</span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </main>
    </div>

</body>
</html>