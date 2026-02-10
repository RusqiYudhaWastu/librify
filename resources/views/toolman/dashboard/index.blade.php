<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Toolman Dashboard - TekniLog</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
        .font-jakarta { font-family: 'Plus Jakarta Sans', sans-serif; }
        .custom-scroll::-webkit-scrollbar { width: 5px; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #10b981; border-radius: 20px; }
    </style>
</head>

<body class="antialiased flex h-screen w-full overflow-hidden text-left font-jakarta" x-data="{ sidebarOpen: false }">

    {{-- Sidebar --}}
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'" 
           class="fixed inset-y-0 left-0 z-50 flex flex-col w-72 bg-[#064E3B] text-white border-r border-emerald-900 md:static md:flex-shrink-0 h-full transition-transform duration-300">
        @include('toolman.partials.sidebar')
    </aside>

    <div class="flex flex-1 flex-col h-full min-w-0 overflow-hidden">
        {{-- Header --}}
        @include('toolman.partials.header')

        <main class="flex-1 overflow-y-auto p-6 lg:p-10 pt-2 custom-scroll text-left">
            <div class="mx-auto w-full max-w-[1550px] space-y-8 text-left leading-none">
                
                {{-- 1. HERO SECTION (DENGAN LIST JURUSAN) --}}
                <div class="relative overflow-hidden rounded-[3rem] bg-gradient-to-br from-emerald-600 to-teal-800 p-10 lg:p-14 shadow-2xl shadow-emerald-100 text-left">
                    <div class="relative z-10 text-left leading-none">
                        <div class="flex flex-wrap gap-2 mb-6">
                            <span class="px-4 py-1.5 rounded-full bg-white/20 text-white text-[9px] font-black uppercase tracking-widest backdrop-blur-md border border-white/10">Wilayah Otoritas:</span>
                            @foreach(Auth::user()->assignedDepartments as $dept)
                                <span class="px-3 py-1.5 rounded-lg bg-emerald-400/30 text-white text-[9px] font-black uppercase tracking-widest border border-white/20">{{ $dept->name }}</span>
                            @endforeach
                        </div>

                        <h1 class="text-3xl lg:text-5xl font-black text-white font-jakarta uppercase leading-tight">
                            Halo, {{ Auth::user()->name }} 👋
                        </h1>
                        <p class="text-emerald-50/80 font-medium text-sm mt-4 max-w-lg leading-relaxed">
                            Pantau pergerakan aset di wilayah Anda. Saat ini sistem mencatat <b>{{ $stats['active'] }} peminjaman aktif</b> yang perlu diawasi.
                        </p>
                        
                        <div class="mt-10 flex gap-4">
                            <a href="{{ route('toolman.request') }}" class="px-8 py-4 bg-white text-emerald-700 rounded-[1.5rem] font-black text-[10px] uppercase tracking-[0.2em] shadow-xl hover:scale-105 transition-all flex items-center gap-2">
                                <i class="bi bi-box-seam"></i> Kelola Logistik
                            </a>
                            <a href="{{ route('toolman.laporan') }}" class="px-8 py-4 bg-emerald-700/50 text-white rounded-[1.5rem] font-black text-[10px] uppercase tracking-[0.2em] hover:bg-emerald-700 transition-all border border-emerald-500/30 flex items-center gap-2">
                                <i class="bi bi-file-earmark-text"></i> Laporan
                            </a>
                        </div>
                    </div>
                    <div class="absolute -right-16 -bottom-16 h-96 w-96 rounded-full bg-white/10 blur-3xl text-left"></div>
                    <i class="bi bi-shield-lock-fill absolute top-10 right-10 text-[180px] text-white opacity-5"></i>
                </div>

                {{-- 2. KPI STATS (FILTERED) --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 text-left">
                    <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm flex items-center gap-6 group hover:border-orange-200 transition-all">
                        <div class="w-16 h-16 bg-orange-50 text-orange-600 rounded-2xl flex items-center justify-center text-3xl shadow-inner group-hover:scale-110 transition-transform"><i class="bi bi-clock-history"></i></div>
                        <div class="text-left">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 leading-none">Menunggu ACC</p>
                            <p class="text-4xl font-black text-gray-900 leading-none">{{ $stats['pending'] }}</p>
                            <a href="{{ route('toolman.request') }}" class="text-[9px] font-bold text-orange-500 mt-2 block hover:underline">Lihat Antrean &rarr;</a>
                        </div>
                    </div>
                    <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm flex items-center gap-6 group hover:border-blue-200 transition-all">
                        <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center text-3xl shadow-inner group-hover:scale-110 transition-transform"><i class="bi bi-box-arrow-up"></i></div>
                        <div class="text-left">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 leading-none">Sedang Dipinjam</p>
                            <p class="text-4xl font-black text-gray-900 leading-none">{{ $stats['active'] }}</p>
                            <a href="{{ route('toolman.request') }}" class="text-[9px] font-bold text-blue-500 mt-2 block hover:underline">Cek Status &rarr;</a>
                        </div>
                    </div>
                    <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm flex items-center gap-6 group hover:border-emerald-200 transition-all">
                        <div class="w-16 h-16 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center text-3xl shadow-inner group-hover:scale-110 transition-transform"><i class="bi bi-check2-circle"></i></div>
                        <div class="text-left">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 leading-none">Kembali Hari Ini</p>
                            <p class="text-4xl font-black text-gray-900 leading-none">{{ $stats['returned_today'] }}</p>
                            <span class="text-[9px] font-bold text-emerald-500 mt-2 block">Data Harian</span>
                        </div>
                    </div>
                    <div class="bg-slate-900 p-8 rounded-[2.5rem] shadow-xl flex items-center gap-6 text-white border border-slate-800">
                        <div class="w-16 h-16 bg-white/10 text-emerald-400 rounded-2xl flex items-center justify-center text-3xl shadow-inner"><i class="bi bi-cart-dash"></i></div>
                        <div class="text-left">
                            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2 leading-none">Total Sirkulasi</p>
                            <p class="text-4xl font-black text-white leading-none">{{ $stats['total_items_out'] }}</p>
                            <span class="text-[9px] font-bold text-slate-400 mt-2 block">All Time</span>
                        </div>
                    </div>
                </div>

                {{-- 3. CONTENT GRID --}}
                <div class="grid lg:grid-cols-12 gap-8 text-left">
                    
                    {{-- TIMELINE AKTIVITAS (SCOPE JURUSAN) --}}
                    <div class="lg:col-span-8 bg-white rounded-[3rem] border border-gray-100 shadow-sm p-10 text-left leading-none h-fit">
                        <div class="flex items-center justify-between mb-10">
                            <div class="text-left">
                                <h3 class="text-lg font-black text-gray-800 uppercase tracking-tight leading-none mb-2">Aktivitas Terkini</h3>
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest leading-none">Log Peminjaman Realtime</p>
                            </div>
                            <div class="flex gap-2">
                                <span class="w-2 h-2 rounded-full bg-orange-500 animate-pulse"></span>
                                <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Live Update</span>
                            </div>
                        </div>

                        <div class="space-y-0">
                            @forelse($recentLoans as $loan)
                            <div class="flex gap-6 relative text-left group">
                                {{-- Garis Connector --}}
                                @if(!$loop->last) 
                                    <div class="absolute left-[1.65rem] top-12 bottom-0 w-0.5 bg-gray-100 group-hover:bg-emerald-100 transition-colors"></div> 
                                @endif
                                
                                {{-- Icon Status --}}
                                @php
                                    $statusConfig = match($loan->status) {
                                        'pending'  => ['icon' => 'bi-hourglass-split', 'color' => 'text-orange-600', 'bg' => 'bg-orange-50', 'border' => 'border-orange-100'],
                                        'approved' => ['icon' => 'bi-box-arrow-up', 'color' => 'text-blue-600', 'bg' => 'bg-blue-50', 'border' => 'border-blue-100'],
                                        'returned' => ['icon' => 'bi-check-lg', 'color' => 'text-emerald-600', 'bg' => 'bg-emerald-50', 'border' => 'border-emerald-100'],
                                        'rejected' => ['icon' => 'bi-x-lg', 'color' => 'text-red-600', 'bg' => 'bg-red-50', 'border' => 'border-red-100'],
                                        default    => ['icon' => 'bi-question', 'color' => 'text-gray-600', 'bg' => 'bg-gray-50', 'border' => 'border-gray-100']
                                    };
                                @endphp

                                <div class="w-14 h-14 rounded-2xl {{ $statusConfig['bg'] }} {{ $statusConfig['color'] }} border {{ $statusConfig['border'] }} flex items-center justify-center flex-shrink-0 z-10 shadow-sm text-xl transition-transform group-hover:scale-110">
                                    <i class="{{ $statusConfig['icon'] }}"></i>
                                </div>

                                <div class="text-left flex-1 pb-10">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <p class="text-sm font-black text-gray-900 uppercase tracking-tight leading-none mb-1">
                                                {{ $loan->user->name }}
                                            </p>
                                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                                                {{ $loan->user->department->name ?? 'UMUM' }} &bull; {{ $loan->quantity }} Unit
                                            </p>
                                        </div>
                                        <span class="text-[9px] font-black text-gray-400 uppercase bg-gray-50 px-2 py-1 rounded">{{ $loan->updated_at->diffForHumans() }}</span>
                                    </div>
                                    
                                    <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100 mt-2 flex justify-between items-center group-hover:bg-white group-hover:shadow-md transition-all">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center text-gray-400 shadow-sm border border-gray-100">
                                                <i class="bi bi-box-seam"></i>
                                            </div>
                                            <span class="text-xs font-bold text-gray-700 uppercase">{{ $loan->item->name }}</span>
                                        </div>
                                        <span class="text-[9px] font-black px-2 py-1 rounded uppercase tracking-wider {{ $statusConfig['bg'] }} {{ $statusConfig['color'] }}">
                                            {{ $loan->status }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="py-20 text-center leading-none">
                                <i class="bi bi-clipboard-x text-5xl text-gray-200 mb-4 block"></i>
                                <p class="text-gray-400 font-bold uppercase text-[10px] tracking-[0.3em]">Belum ada aktivitas di wilayah jurusan lu bro.</p>
                            </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- ALERTS & INFO (RIGHT COLUMN) --}}
                    <div class="lg:col-span-4 space-y-8 text-left leading-none">
                        
                        {{-- CARD STOK KRITIS --}}
                        <div class="bg-red-50 rounded-[3rem] p-10 border border-red-100 text-left shadow-inner relative overflow-hidden">
                            <div class="absolute top-0 right-0 w-32 h-32 bg-red-100/50 rounded-full blur-2xl -mr-10 -mt-10"></div>
                            
                            <div class="flex items-center gap-4 mb-8 text-red-600 leading-none relative z-10">
                                <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-xl shadow-sm border border-red-100"><i class="bi bi-exclamation-octagon-fill"></i></div>
                                <div class="text-left">
                                    <h3 class="text-[10px] font-black uppercase tracking-[0.2em] leading-none mb-1">Stok Menipis</h3>
                                    <p class="text-[9px] font-bold text-red-400 uppercase leading-none">Restock Required</p>
                                </div>
                            </div>

                            <div class="space-y-3 relative z-10">
                                @forelse($lowStockItems as $low)
                                <div class="flex justify-between items-center p-4 bg-white rounded-[1.5rem] shadow-sm border border-red-100 transition-transform hover:scale-[1.02]">
                                    <div class="text-left leading-none">
                                        <p class="text-[11px] font-black text-gray-800 uppercase tracking-tight mb-1">{{ $low->name }}</p>
                                        <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest">Kat: {{ $low->category->name }}</p>
                                    </div>
                                    <span class="text-[10px] font-black px-2.5 py-1 bg-red-50 text-red-600 rounded-lg border border-red-200">SISA {{ $low->stock }}</span>
                                </div>
                                @empty
                                <div class="p-6 text-center bg-white/60 rounded-3xl border border-dashed border-red-200 leading-none">
                                    <p class="text-[9px] text-emerald-600 font-black uppercase tracking-[0.2em]"><i class="bi bi-check-circle-fill me-1"></i> Stok Aman</p>
                                </div>
                                @endforelse
                            </div>
                        </div>

                        {{-- CARD JADWAL / INFO --}}
                        <div class="bg-slate-900 rounded-[3rem] p-10 text-white shadow-2xl relative overflow-hidden text-left border border-slate-800">
                            <div class="relative z-10 leading-none">
                                <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 mb-8 leading-none">Informasi Jadwal</h3>
                                <div class="space-y-4">
                                    <div class="flex items-center gap-4 p-5 bg-white/5 rounded-[1.5rem] border border-white/5 hover:bg-white/10 transition-colors">
                                        <div class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center"><i class="bi bi-calendar-event"></i></div>
                                        <div>
                                            <p class="text-[9px] font-bold text-slate-400 uppercase mb-1 tracking-widest leading-none">Hari Ini</p>
                                            <p class="text-sm font-black uppercase tracking-tight">{{ now()->translatedFormat('l, d F Y') }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-4 p-5 bg-white/5 rounded-[1.5rem] border border-white/5 hover:bg-white/10 transition-colors">
                                        <div class="w-10 h-10 rounded-xl bg-blue-500/20 text-blue-400 flex items-center justify-center"><i class="bi bi-person-badge"></i></div>
                                        <div>
                                            <p class="text-[9px] font-bold text-slate-400 uppercase mb-1 tracking-widest leading-none">Petugas Piket</p>
                                            <p class="text-sm font-black uppercase tracking-tight">{{ Auth::user()->name }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="absolute -left-10 -bottom-10 h-40 w-40 rounded-full bg-emerald-500/20 blur-3xl"></div>
                        </div>

                    </div>

                </div>
            </div>
        </main>
    </div>

</body>
</html>