<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard - Librify</title>

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
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.2);
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

        <main class="flex-1 overflow-y-auto p-4 lg:p-8 pt-2 custom-scroll text-left">
            <div class="mx-auto w-full max-w-[1550px] space-y-6 text-left">
                
                {{-- 1. HERO SECTION: PUSAT KENDALI (SKALA DIPERKECIL) --}}
                <div class="relative overflow-hidden rounded-[2rem] bg-gradient-to-br from-indigo-700 to-blue-900 p-8 lg:p-10 shadow-2xl shadow-indigo-200/50">
                    <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6 text-left leading-none">
                        <div class="space-y-4 text-left">
                            <div class="flex items-center gap-3">
                                <span class="px-3 py-1.5 bg-white/20 rounded-lg text-[9px] font-black text-white uppercase tracking-widest backdrop-blur-md border border-white/10">Super Admin Oversight</span>
                                <div class="flex items-center gap-2 bg-emerald-500/20 px-2.5 py-1.5 rounded-lg border border-emerald-500/30">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                    <span class="text-[9px] font-bold text-emerald-100 uppercase tracking-widest">Sistem Online</span>
                                </div>
                            </div>
                            <h1 class="text-2xl lg:text-4xl font-black text-white tracking-tight leading-tight">
                                Halo, {{ Auth::user()->name }}! 👋
                            </h1>
                            <p class="text-indigo-100 font-medium text-xs lg:text-sm max-w-xl leading-relaxed">
                                Anda memiliki akses penuh. Pantau sirkulasi buku, aktivitas pengguna, dan integritas sistem <b class="text-white">Perpustakaan</b> hari ini.
                            </p>
                        </div>
                        
                        {{-- Jam & Tanggal Digital --}}
                        <div class="glass-card px-8 py-6 rounded-[1.5rem] text-center shadow-2xl min-w-[220px] transform hover:scale-105 transition-transform duration-300 text-white">
                            <p class="text-[9px] font-black text-indigo-200 uppercase tracking-[0.3em] mb-2">Waktu Server Utama</p>
                            <p class="text-4xl font-black tabular-nums leading-none tracking-tight mb-2" x-text="currentTime"></p>
                            <p class="text-[10px] font-bold text-indigo-100 uppercase tracking-widest">{{ now()->translatedFormat('l, d F Y') }}</p>
                        </div>
                    </div>
                    
                    {{-- Ornamen Dekorasi --}}
                    <div class="absolute -right-20 -bottom-20 h-80 w-80 rounded-full bg-blue-500/30 blur-3xl"></div>
                    <div class="absolute right-1/3 top-10 h-32 w-32 rounded-full bg-indigo-400/20 blur-2xl"></div>
                    <i class="bi bi-shield-check absolute -left-10 -bottom-10 text-[180px] text-white/5 -rotate-12"></i>
                </div>

                {{-- 2. KPI STATS GRID (DETAIL METRIK - SKALA DIPERKECIL) --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 text-left">
                    <div class="bg-white p-6 rounded-[1.5rem] border border-gray-100 shadow-sm group hover:shadow-md hover:border-indigo-200 transition-all flex flex-col justify-between">
                        <div class="flex justify-between items-start mb-4">
                            <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center text-xl group-hover:scale-110 transition-transform shadow-inner"><i class="bi bi-people-fill"></i></div>
                            <span class="text-[8px] font-black text-indigo-500 bg-indigo-50 border border-indigo-100 px-2 py-1 rounded md:rounded-lg uppercase tracking-widest">Total</span>
                        </div>
                        <div>
                            <p class="text-3xl font-black text-gray-900 leading-none mb-1.5">{{ $stats['total_users'] ?? 0 }}</p>
                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Populasi Akun Aktif</p>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-[1.5rem] border border-gray-100 shadow-sm group hover:shadow-md hover:border-blue-200 transition-all flex flex-col justify-between">
                        <div class="flex justify-between items-start mb-4">
                            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-xl group-hover:scale-110 transition-transform shadow-inner"><i class="bi bi-arrow-left-right"></i></div>
                            <span class="text-[8px] font-black text-blue-500 bg-blue-50 border border-blue-100 px-2 py-1 rounded md:rounded-lg uppercase tracking-widest">Live</span>
                        </div>
                        <div>
                            <p class="text-3xl font-black text-gray-900 leading-none mb-1.5">{{ $stats['active_loans'] ?? 0 }}</p>
                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Peminjaman Berjalan</p>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-[1.5rem] border border-gray-100 shadow-sm group hover:shadow-md hover:border-orange-200 transition-all flex flex-col justify-between">
                        <div class="flex justify-between items-start mb-4">
                            <div class="w-12 h-12 bg-orange-50 text-orange-600 rounded-xl flex items-center justify-center text-xl group-hover:scale-110 transition-transform shadow-inner"><i class="bi bi-exclamation-triangle"></i></div>
                            <span class="text-[8px] font-black text-orange-500 bg-orange-50 border border-orange-100 px-2 py-1 rounded md:rounded-lg uppercase tracking-widest">Warning</span>
                        </div>
                        <div>
                            <p class="text-3xl font-black text-orange-600 leading-none mb-1.5">{{ $stats['maintenance'] ?? 0 }}</p>
                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Laporan Keterlambatan</p>
                        </div>
                    </div>

                    <div class="bg-slate-900 p-6 rounded-[1.5rem] shadow-lg flex flex-col justify-between overflow-hidden relative border border-slate-800 hover:shadow-xl transition-all">
                        <div class="relative z-10 text-left leading-none flex justify-between items-start">
                            <div>
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2">Integrity Score</p>
                                <p class="text-4xl font-black text-white tracking-tight">98<span class="text-xl text-slate-500">%</span></p>
                            </div>
                            <div class="w-8 h-8 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center border border-emerald-500/30">
                                <i class="bi bi-shield-check text-sm"></i>
                            </div>
                        </div>
                        <div class="relative z-10 mt-4 pt-3 border-t border-slate-700">
                            <p class="text-[8px] font-bold text-slate-400 uppercase tracking-widest leading-relaxed">Sistem beroperasi normal tanpa anomali.</p>
                        </div>
                        <i class="bi bi-fingerprint absolute -right-6 -bottom-6 text-[120px] text-white/5"></i>
                    </div>
                </div>

                {{-- 3. MAIN GRID (KIRI: AUDIT & GRAFIK | KANAN: AKSIC CEPAT & ROLE) --}}
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 md:gap-8 text-left">
                    
                    {{-- KOLOM KIRI (LEBAR) --}}
                    <div class="lg:col-span-8 space-y-6 md:space-y-8 text-left flex flex-col">
                        
                        {{-- Log Sirkulasi Terkini (GROUPED) --}}
                        <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden text-left flex flex-col h-full max-h-[450px]">
                            <div class="p-6 border-b border-gray-50 flex items-center justify-between bg-gray-50/50 text-left leading-none">
                                <div class="text-left leading-none flex items-center gap-3">
                                    <div class="w-10 h-10 bg-white rounded-xl shadow-sm border border-gray-100 flex items-center justify-center text-indigo-600 text-lg"><i class="bi bi-activity"></i></div>
                                    <div>
                                        <h3 class="text-base font-black text-gray-900 uppercase tracking-tight">Log Sirkulasi Terkini</h3>
                                        <p class="text-[9px] text-gray-400 font-bold uppercase tracking-widest mt-1">Audit Sirkulasi Perpustakaan Realtime</p>
                                    </div>
                                </div>
                                <a href="{{ route('admin.audit') }}" class="px-4 py-2.5 bg-indigo-600 text-white rounded-lg text-[9px] font-black uppercase tracking-widest shadow-md shadow-indigo-100 hover:bg-indigo-700 transition-all flex items-center gap-1.5">
                                    Lihat <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                            <div class="overflow-y-auto custom-scroll flex-1">
                                <table class="w-full text-left text-xs">
                                    <thead class="sticky top-0 bg-white shadow-sm z-10">
                                        <tr class="text-[8px] font-black text-gray-400 uppercase tracking-[0.2em] border-b border-gray-100">
                                            <th class="px-6 py-4">Waktu</th>
                                            <th class="px-6 py-4">Peminjam</th>
                                            <th class="px-6 py-4">Detail Buku</th>
                                            <th class="px-6 py-4 text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-50 text-sm">
                                        @php
                                            // Grouping Logic for Dashboard Recent Activities
                                            $groupedActivities = collect($recentActivities ?? [])->groupBy(function($item) {
                                                return $item->user_id . '|' . $item->created_at->format('Y-m-d H:i') . '|' . $item->status;
                                            })->take(5);
                                        @endphp

                                        @forelse($groupedActivities as $group)
                                        @php
                                            $first = $group->first();
                                            $itemCount = $group->count();
                                            $totalQty = $group->sum('quantity');
                                            $iconBox = $itemCount > 1 ? 'bi-journals' : 'bi-book';
                                        @endphp
                                        <tr class="group hover:bg-indigo-50/30 transition-colors">
                                            <td class="px-6 py-4 font-black text-gray-400 text-[9px] uppercase tracking-wider whitespace-nowrap">{{ $first->created_at->format('H:i') }} WIB</td>
                                            <td class="px-6 py-4">
                                                <div class="flex flex-col text-left leading-none">
                                                    <span class="font-black text-gray-900 uppercase text-xs mb-1 truncate max-w-[150px]">{{ $first->user->name }}</span>
                                                    <span class="text-[8px] font-black text-indigo-500 bg-indigo-50 px-2 py-0.5 rounded w-fit uppercase tracking-widest border border-indigo-100">
                                                        {{ $first->user->role === 'class' ? 'Kolektif Kelas' : ($first->user->role === 'student' ? 'Siswa Individu' : 'Staf / Guru') }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-left leading-tight">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center text-sm group-hover:bg-white group-hover:shadow-sm transition-all flex-shrink-0 border border-transparent group-hover:border-slate-200">
                                                        <i class="bi {{ $iconBox }}"></i>
                                                    </div>
                                                    <div>
                                                        @if($itemCount > 1)
                                                            <p class="font-black text-gray-800 text-xs uppercase leading-tight truncate max-w-[150px]">Peminjaman Beragam</p>
                                                            <p class="text-[8px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">{{ $itemCount }} Judul <span class="text-indigo-600 font-black">({{ $totalQty }} Eks)</span></p>
                                                        @else
                                                            <p class="font-black text-gray-800 text-xs uppercase leading-tight truncate max-w-[150px]">{{ $first->item->name }}</p>
                                                            <p class="text-[8px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">Total: <span class="text-indigo-600 font-black">{{ $totalQty }} Buku</span></p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                @php
                                                    $badgeStyle = match($first->status) {
                                                        'pending'  => 'bg-orange-50 text-orange-600 border-orange-200',
                                                        'approved' => 'bg-blue-50 text-blue-600 border-blue-200',
                                                        'returned' => 'bg-emerald-50 text-emerald-600 border-emerald-200',
                                                        default    => 'bg-slate-50 text-slate-500 border-slate-200'
                                                    };
                                                    $statusLabel = match($first->status) {
                                                        'pending'  => 'Menunggu',
                                                        'approved' => 'Dipinjam',
                                                        'returned' => 'Selesai',
                                                        default    => 'Ditolak'
                                                    };
                                                @endphp
                                                <span class="px-2.5 py-1 {{ $badgeStyle }} rounded-md text-[8px] font-black uppercase tracking-widest border shadow-sm">{{ $statusLabel }}</span>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="4" class="py-12 text-center text-gray-400 font-bold uppercase text-[9px] tracking-[0.2em] italic">Belum ada aktivitas sirkulasi.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Distribusi Asset Per Kategori --}}
                        <div class="bg-white rounded-[2rem] p-6 lg:p-8 border border-gray-100 shadow-sm text-left">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-500 flex items-center justify-center text-lg"><i class="bi bi-pie-chart-fill"></i></div>
                                <div>
                                    <h3 class="text-sm font-black text-gray-900 uppercase tracking-tight leading-none">Distribusi Koleksi Buku</h3>
                                    <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-1">Pemetaan ketersediaan buku per genre</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-6">
                                @foreach($categories ?? [] as $cat)
                                <div class="space-y-2.5 text-left group">
                                    <div class="flex justify-between items-end text-[9px] font-black uppercase leading-none">
                                        <span class="text-gray-600 tracking-wider group-hover:text-indigo-600 transition-colors truncate max-w-[150px]">{{ $cat->name }}</span>
                                        <div class="text-right flex-shrink-0">
                                            <span class="text-indigo-600 text-xs">{{ $cat->items_count }}</span>
                                            <span class="text-gray-400 ml-1">Buku</span>
                                        </div>
                                    </div>
                                    <div class="h-2 w-full bg-gray-100 rounded-full overflow-hidden shadow-inner">
                                        <div class="h-full bg-gradient-to-r from-indigo-500 to-blue-500 rounded-full transition-all duration-1000 group-hover:brightness-110" 
                                             style="width: {{ ($cat->items_count / max($stats['total_items'] ?? 1, 1)) * 100 }}%"></div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- KOLOM KANAN (SEMPIT) --}}
                    <div class="lg:col-span-4 space-y-6 md:space-y-8 text-left">
                        
                        {{-- QUICK LINKS --}}
                        <div class="bg-slate-900 rounded-[2rem] p-6 lg:p-8 text-white shadow-xl relative overflow-hidden text-left border border-slate-800">
                            <h3 class="text-[9px] font-black text-indigo-400 uppercase tracking-[0.2em] mb-5 relative z-10 leading-none flex items-center gap-1.5"><i class="bi bi-lightning-charge-fill text-xs"></i> Panel Aksi Cepat</h3>
                            <div class="space-y-3 relative z-10">
                                <a href="{{ route('admin.pengguna.index') }}" class="flex items-center gap-3 p-3 bg-white/5 rounded-xl border border-white/5 hover:bg-indigo-600 transition-all hover:scale-[1.02] active:scale-95 group">
                                    <div class="w-9 h-9 rounded-lg bg-white/10 flex items-center justify-center text-indigo-300 group-hover:text-white transition-colors shadow-inner"><i class="bi bi-person-gear"></i></div>
                                    <div class="leading-none text-left">
                                        <span class="text-[11px] font-black uppercase tracking-widest block mb-1">Manajemen Akun</span>
                                        <span class="text-[8px] font-bold text-slate-400 uppercase tracking-widest group-hover:text-indigo-200">Staf, Kelas & Siswa</span>
                                    </div>
                                </a>
                                <a href="{{ route('admin.barang.index') }}" class="flex items-center gap-3 p-3 bg-white/5 rounded-xl border border-white/5 hover:bg-indigo-600 transition-all hover:scale-[1.02] active:scale-95 group">
                                    <div class="w-9 h-9 rounded-lg bg-white/10 flex items-center justify-center text-emerald-300 group-hover:text-white transition-colors shadow-inner"><i class="bi bi-book"></i></div>
                                    <div class="leading-none text-left">
                                        <span class="text-[11px] font-black uppercase tracking-widest block mb-1">Katalog Buku</span>
                                        <span class="text-[8px] font-bold text-slate-400 uppercase tracking-widest group-hover:text-indigo-200">Registrasi Buku Baru</span>
                                    </div>
                                </a>
                                <a href="{{ route('admin.kategori.index') }}" class="flex items-center gap-3 p-3 bg-white/5 rounded-xl border border-white/5 hover:bg-indigo-600 transition-all hover:scale-[1.02] active:scale-95 group">
                                    <div class="w-9 h-9 rounded-lg bg-white/10 flex items-center justify-center text-blue-300 group-hover:text-white transition-colors shadow-inner"><i class="bi bi-tags"></i></div>
                                    <div class="leading-none text-left">
                                        <span class="text-[11px] font-black uppercase tracking-widest block mb-1">Kategori Koleksi</span>
                                        <span class="text-[8px] font-bold text-slate-400 uppercase tracking-widest group-hover:text-indigo-200">Genre & Klasifikasi</span>
                                    </div>
                                </a>
                            </div>
                            <div class="absolute -right-10 -bottom-10 h-32 w-32 rounded-full bg-indigo-500/20 blur-2xl"></div>
                        </div>

                        {{-- SESSION TRACKER (DINAMIS ROLE COUNT) --}}
                        <div class="bg-white rounded-[2rem] p-6 lg:p-8 border border-gray-100 shadow-sm text-left flex-1">
                            <div class="flex items-center gap-2.5 mb-6">
                                <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-sm"><i class="bi bi-pie-chart"></i></div>
                                <h3 class="text-[11px] font-black text-gray-900 uppercase tracking-widest leading-none">Distribusi Peran</h3>
                            </div>
                            
                            <div class="space-y-3 text-left">
                                <div class="flex items-center justify-between p-3 bg-indigo-50/50 rounded-xl border border-indigo-100/50 hover:bg-indigo-50 transition-colors">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-bold text-[9px] shadow-sm"><i class="bi bi-person-fill-lock"></i></div>
                                        <span class="text-[10px] font-black uppercase tracking-wider text-gray-800">Administrator</span>
                                    </div>
                                    <span class="text-xs font-black text-indigo-600">{{ sprintf('%02d', $stats['role_admin'] ?? 0) }}</span>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-emerald-50/50 rounded-xl border border-emerald-100/50 hover:bg-emerald-50 transition-colors">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-emerald-600 text-white flex items-center justify-center font-bold text-[9px] shadow-sm"><i class="bi bi-person-workspace"></i></div>
                                        <span class="text-[10px] font-black uppercase tracking-wider text-gray-800">Petugas Perpus</span>
                                    </div>
                                    <span class="text-xs font-black text-emerald-600">{{ sprintf('%02d', $stats['role_staff'] ?? ($stats['role_toolman'] ?? 0)) }}</span>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-blue-50/50 rounded-xl border border-blue-100/50 hover:bg-blue-50 transition-colors">
                                    <div class="flex items-center gap-3 text-left">
                                        <div class="w-8 h-8 rounded-lg bg-blue-600 text-white flex items-center justify-center font-bold text-[9px] shadow-sm"><i class="bi bi-building"></i></div>
                                        <span class="text-[10px] font-black uppercase tracking-wider text-gray-800">Akun Kelas</span>
                                    </div>
                                    <span class="text-xs font-black text-blue-600">{{ sprintf('%02d', $stats['role_class'] ?? 0) }}</span>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-cyan-50/50 rounded-xl border border-cyan-100/50 hover:bg-cyan-50 transition-colors">
                                    <div class="flex items-center gap-3 text-left">
                                        <div class="w-8 h-8 rounded-lg bg-cyan-600 text-white flex items-center justify-center font-bold text-[9px] shadow-sm"><i class="bi bi-person-badge"></i></div>
                                        <span class="text-[10px] font-black uppercase tracking-wider text-gray-800">Siswa Individu</span>
                                    </div>
                                    <span class="text-xs font-black text-cyan-600">{{ sprintf('%02d', $stats['role_student'] ?? 0) }}</span>
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