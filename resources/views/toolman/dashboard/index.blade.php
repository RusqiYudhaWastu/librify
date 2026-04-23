<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Petugas Dashboard - Librify</title>

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

    {{-- Sidebar --}}
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'" 
           class="fixed inset-y-0 left-0 z-50 flex flex-col w-72 bg-[#064E3B] text-white border-r border-emerald-900 md:static md:flex-shrink-0 h-full transition-transform duration-300">
        @include('toolman.partials.sidebar')
    </aside>

    <div class="flex flex-1 flex-col h-full min-w-0 overflow-hidden">
        {{-- Header --}}
        @include('toolman.partials.header')

        <main class="flex-1 overflow-y-auto p-4 lg:p-8 pt-2 custom-scroll text-left">
            <div class="mx-auto w-full max-w-[1550px] space-y-6 text-left leading-none">
                
                {{-- 1. HERO SECTION --}}
                <div class="relative overflow-hidden rounded-[2rem] bg-gradient-to-br from-emerald-600 to-teal-800 p-8 lg:p-10 shadow-2xl shadow-emerald-200/50 text-left border border-emerald-500/50">
                    <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6 text-left leading-none">
                        <div class="space-y-4">
                            <div class="flex flex-wrap items-center gap-2 mb-2">
                                <span class="px-3 py-1.5 rounded-lg bg-white/20 text-white text-[9px] font-black uppercase tracking-widest backdrop-blur-md border border-white/10">Hak Akses:</span>
                                <span class="px-2.5 py-1.5 rounded-lg bg-emerald-400/30 text-white text-[9px] font-black uppercase tracking-widest border border-white/20"><i class="bi bi-globe-americas me-1"></i> Global / Seluruh Koleksi</span>
                            </div>

                            <h1 class="text-2xl lg:text-4xl font-black text-white font-jakarta uppercase leading-tight">
                                Halo, {{ Auth::user()->name }} 👋
                            </h1>
                            <p class="text-emerald-50/80 font-medium text-xs lg:text-sm mt-2 max-w-lg leading-relaxed">
                                Pantau sirkulasi peminjaman, kelola persetujuan, dan pastikan kondisi koleksi buku perpustakaan tetap terjaga dengan baik.
                            </p>
                        </div>

                        {{-- Jam & Tanggal Digital --}}
                        <div class="glass-card px-8 py-6 rounded-[1.5rem] text-center shadow-2xl min-w-[220px] text-white flex-shrink-0 mt-4 lg:mt-0">
                            <p class="text-[9px] font-black text-emerald-200 uppercase tracking-[0.3em] mb-2 flex items-center justify-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-emerald-300 animate-pulse"></span> Waktu Server</p>
                            <p class="text-4xl font-black tabular-nums leading-none tracking-tight mb-2" x-text="currentTime"></p>
                            <p class="text-[10px] font-bold text-emerald-100 uppercase tracking-widest">{{ now()->translatedFormat('l, d F Y') }}</p>
                        </div>
                    </div>
                    
                    {{-- Ornamen --}}
                    <div class="absolute -right-16 -bottom-16 h-64 w-64 lg:h-80 lg:w-80 rounded-full bg-white/10 blur-3xl text-left"></div>
                    <i class="bi bi-book-half absolute -left-10 -bottom-10 text-[180px] text-white opacity-5 -rotate-12"></i>
                </div>

                {{-- 2. METRIK OPERASIONAL --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 text-left">
                    {{-- Menunggu ACC --}}
                    <div class="bg-white p-6 rounded-[1.5rem] border border-gray-100 shadow-sm flex flex-col justify-between group hover:shadow-md hover:border-orange-200 transition-all">
                        <div class="flex justify-between items-start mb-4">
                            <div class="w-12 h-12 bg-orange-50 text-orange-600 rounded-xl flex items-center justify-center text-xl shadow-inner group-hover:scale-110 transition-transform"><i class="bi bi-bell-fill"></i></div>
                            <span class="text-[8px] font-black text-orange-500 bg-orange-50 border border-orange-100 px-2 py-1 rounded-lg uppercase tracking-widest">Urgent</span>
                        </div>
                        <div>
                            <p class="text-3xl font-black text-gray-900 leading-none mb-1.5">{{ $stats['pending'] ?? 0 }}</p>
                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Antrean Peminjaman</p>
                        </div>
                    </div>

                    {{-- Sedang Dipinjam --}}
                    <div class="bg-white p-6 rounded-[1.5rem] border border-gray-100 shadow-sm flex flex-col justify-between group hover:shadow-md hover:border-blue-200 transition-all">
                        <div class="flex justify-between items-start mb-4">
                            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-xl shadow-inner group-hover:scale-110 transition-transform"><i class="bi bi-book"></i></div>
                            <span class="text-[8px] font-black text-blue-500 bg-blue-50 border border-blue-100 px-2 py-1 rounded-lg uppercase tracking-widest">Monitoring</span>
                        </div>
                        <div>
                            <p class="text-3xl font-black text-gray-900 leading-none mb-1.5">{{ $stats['active'] ?? 0 }}</p>
                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Buku Sedang Dipinjam</p>
                        </div>
                    </div>

                    {{-- Kembali Hari Ini --}}
                    <div class="bg-white p-6 rounded-[1.5rem] border border-gray-100 shadow-sm flex flex-col justify-between group hover:shadow-md hover:border-emerald-200 transition-all">
                        <div class="flex justify-between items-start mb-4">
                            <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center text-xl shadow-inner group-hover:scale-110 transition-transform"><i class="bi bi-arrow-down-left-square-fill"></i></div>
                            <span class="text-[8px] font-black text-emerald-500 bg-emerald-50 border border-emerald-100 px-2 py-1 rounded-lg uppercase tracking-widest">Today</span>
                        </div>
                        <div>
                            <p class="text-3xl font-black text-gray-900 leading-none mb-1.5">{{ $stats['returned_today'] ?? 0 }}</p>
                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Dikembalikan Hari Ini</p>
                        </div>
                    </div>

                    {{-- Buku Bermasalah --}}
                    <div class="bg-slate-900 p-6 rounded-[1.5rem] shadow-lg flex flex-col justify-between relative border border-slate-800 hover:shadow-xl transition-all">
                        <div class="flex justify-between items-start mb-4 relative z-10">
                            <div class="w-12 h-12 bg-white/10 text-red-400 rounded-xl flex items-center justify-center text-xl shadow-inner"><i class="bi bi-journal-x"></i></div>
                            <span class="text-[8px] font-black text-red-400 border border-red-500/30 bg-red-500/10 px-2 py-1 rounded-lg uppercase tracking-widest">Action Needed</span>
                        </div>
                        <div class="relative z-10">
                            @php 
                                $totalBermasalah = ($stats['maintenance'] ?? 0) + ($stats['broken'] ?? 0); 
                            @endphp
                            <p class="text-3xl font-black text-white leading-none mb-1.5">{{ $totalBermasalah }}</p>
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Buku Rusak / Hilang</p>
                        </div>
                        <i class="bi bi-exclamation-triangle absolute -right-4 -bottom-4 text-[100px] text-red-500/10"></i>
                    </div>
                </div>

                {{-- 3. MAIN GRID (KIRI: TABEL & RASIO | KANAN: JADWAL & STOK) --}}
                <div class="grid lg:grid-cols-12 gap-6 md:gap-8 text-left">
                    
                    {{-- KOLOM KIRI (LEBAR) --}}
                    <div class="lg:col-span-8 space-y-6 md:space-y-8 text-left">
                        
                        {{-- Tabel Log Permintaan Terkini --}}
                        <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden text-left flex flex-col h-full max-h-[450px]">
                            <div class="p-6 border-b border-gray-50 flex items-center justify-between bg-gray-50/50 text-left leading-none">
                                <div class="text-left leading-none flex items-center gap-3">
                                    <div class="w-10 h-10 bg-white rounded-xl shadow-sm border border-gray-100 flex items-center justify-center text-emerald-600 text-lg"><i class="bi bi-list-check"></i></div>
                                    <div>
                                        <h3 class="text-base font-black text-gray-900 uppercase tracking-tight">Log Sirkulasi Terkini</h3>
                                        <p class="text-[9px] text-gray-400 font-bold uppercase tracking-widest mt-1.5">Tracking Peminjaman & Pengembalian</p>
                                    </div>
                                </div>
                                <a href="{{ route('staff.request') }}" class="px-4 py-2.5 bg-emerald-600 text-white rounded-lg text-[9px] font-black uppercase tracking-widest shadow-md shadow-emerald-100 hover:bg-emerald-700 transition-all flex items-center gap-1.5">
                                    Kelola <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                            
                            <div class="overflow-y-auto custom-scroll flex-1">
                                <table class="w-full text-left text-xs">
                                    <thead class="sticky top-0 bg-white shadow-sm z-10">
                                        <tr class="text-[8px] font-black text-gray-400 uppercase tracking-[0.2em] border-b border-gray-100">
                                            <th class="px-6 py-4">Status & Waktu</th>
                                            <th class="px-6 py-4">Detail Peminjam</th>
                                            <th class="px-6 py-4">Buku Dipinjam</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-50">
                                        @forelse($recentLoans ?? [] as $loan)
                                        <tr class="group hover:bg-emerald-50/30 transition-colors">
                                            <td class="px-6 py-4">
                                                @php
                                                    $statusConfig = match($loan->status) {
                                                        'pending'  => 'bg-orange-50 text-orange-600 border-orange-200',
                                                        'approved' => 'bg-blue-50 text-blue-600 border-blue-200',
                                                        'returned' => 'bg-emerald-50 text-emerald-600 border-emerald-200',
                                                        'rejected' => 'bg-red-50 text-red-600 border-red-200',
                                                        default    => 'bg-slate-50 text-slate-500 border-slate-200'
                                                    };
                                                @endphp
                                                <div class="flex flex-col gap-1.5 items-start">
                                                    <span class="px-2.5 py-1 {{ $statusConfig }} rounded-md text-[8px] font-black uppercase tracking-widest border shadow-sm">{{ $loan->status }}</span>
                                                    <span class="font-black text-gray-400 text-[9px] uppercase tracking-wider whitespace-nowrap">{{ $loan->created_at->format('d M - H:i') }} WIB</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="flex flex-col text-left leading-none">
                                                    <span class="font-black text-gray-900 uppercase text-xs mb-1.5 truncate max-w-[180px]">{{ $loan->user->name }}</span>
                                                    <span class="text-[8px] font-black text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded w-fit uppercase tracking-widest border border-emerald-100 truncate"><i class="bi bi-person-badge"></i> {{ $loan->user->role === 'class' ? 'Akun Kelas' : 'Siswa / Member' }}</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-left leading-tight">
                                                <p class="font-black text-gray-800 text-xs uppercase truncate max-w-[180px]">{{ $loan->item->name }}</p>
                                                <p class="text-[8px] font-bold text-gray-400 uppercase tracking-widest mt-1">Total: <span class="text-emerald-600 font-black">{{ $loan->quantity }} Buku</span></p>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="3" class="py-12 text-center text-gray-400 font-bold uppercase text-[9px] tracking-[0.2em] italic">Belum ada aktivitas sirkulasi terbaru.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Indikator Kesehatan Buku --}}
                        <div class="bg-white rounded-[2rem] p-6 lg:p-8 border border-gray-100 shadow-sm text-left">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center text-lg"><i class="bi bi-heart-pulse-fill"></i></div>
                                <div>
                                    <h3 class="text-sm font-black text-gray-900 uppercase tracking-tight leading-none">Kondisi Fisik Koleksi</h3>
                                    <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-1">Rasio kondisi buku secara keseluruhan</p>
                                </div>
                            </div>

                            @php
                                $totalFisik = max(($stats['total_items'] ?? 1), 1);
                                $siap = $stats['ready'] ?? 0;
                                $maint = $stats['maintenance'] ?? 0;
                                $rusak = $stats['broken'] ?? 0;

                                $pctSiap = ($siap / $totalFisik) * 100;
                                $pctMaint = ($maint / $totalFisik) * 100;
                                $pctRusak = ($rusak / $totalFisik) * 100;
                            @endphp

                            <div class="space-y-6">
                                {{-- Progress Bar Stacked --}}
                                <div class="h-4 w-full bg-gray-100 rounded-full overflow-hidden flex shadow-inner">
                                    <div class="h-full bg-emerald-500 transition-all duration-1000" style="width: {{ $pctSiap }}%" title="Layak Baca"></div>
                                    <div class="h-full bg-orange-500 transition-all duration-1000" style="width: {{ $pctMaint }}%" title="Perbaikan"></div>
                                    <div class="h-full bg-red-500 transition-all duration-1000" style="width: {{ $pctRusak }}%" title="Rusak / Hilang"></div>
                                </div>

                                {{-- Legends --}}
                                <div class="grid grid-cols-3 gap-4">
                                    <div class="bg-emerald-50/50 p-3 rounded-xl border border-emerald-100">
                                        <p class="text-[8px] font-black text-emerald-500 uppercase tracking-widest mb-1 flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> Layak Baca</p>
                                        <p class="text-base font-black text-emerald-700">{{ $siap }} <span class="text-[9px] text-emerald-500 font-bold">Buku</span></p>
                                    </div>
                                    <div class="bg-orange-50/50 p-3 rounded-xl border border-orange-100">
                                        <p class="text-[8px] font-black text-orange-500 uppercase tracking-widest mb-1 flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-orange-500"></span> Perbaikan</p>
                                        <p class="text-base font-black text-orange-700">{{ $maint }} <span class="text-[9px] text-orange-500 font-bold">Buku</span></p>
                                    </div>
                                    <div class="bg-red-50/50 p-3 rounded-xl border border-red-100">
                                        <p class="text-[8px] font-black text-red-500 uppercase tracking-widest mb-1 flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-red-500"></span> Rusak / Hilang</p>
                                        <p class="text-base font-black text-red-700">{{ $rusak }} <span class="text-[9px] text-red-500 font-bold">Buku</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- KOLOM KANAN: ALERTS & ACTIONS --}}
                    <div class="lg:col-span-4 space-y-6 md:space-y-8 text-left leading-none">
                        
                        {{-- QUICK ACTIONS PANEL --}}
                        <div class="bg-slate-900 rounded-[2rem] p-6 lg:p-8 text-white shadow-xl relative overflow-hidden text-left border border-slate-800">
                            <h3 class="text-[9px] font-black text-emerald-400 uppercase tracking-[0.2em] mb-5 relative z-10 leading-none flex items-center gap-1.5"><i class="bi bi-lightning-charge-fill text-xs"></i> Panel Aksi Cepat</h3>
                            <div class="space-y-3 relative z-10">
                                <a href="{{ route('staff.request') }}" class="flex items-center gap-3 p-3 bg-white/5 rounded-xl border border-white/5 hover:bg-emerald-600 transition-all hover:scale-[1.02] active:scale-95 group">
                                    <div class="w-9 h-9 rounded-lg bg-white/10 flex items-center justify-center text-emerald-300 group-hover:text-white transition-colors shadow-inner"><i class="bi bi-ui-checks"></i></div>
                                    <div class="leading-none text-left">
                                        <span class="text-[11px] font-black uppercase tracking-widest block mb-1">Sirkulasi Buku</span>
                                        <span class="text-[8px] font-bold text-slate-400 uppercase tracking-widest group-hover:text-emerald-200">ACC & Pengembalian Buku</span>
                                    </div>
                                </a>
                                <a href="{{ route('staff.laporan') }}" class="flex items-center gap-3 p-3 bg-white/5 rounded-xl border border-white/5 hover:bg-emerald-600 transition-all hover:scale-[1.02] active:scale-95 group">
                                    <div class="w-9 h-9 rounded-lg bg-white/10 flex items-center justify-center text-blue-300 group-hover:text-white transition-colors shadow-inner"><i class="bi bi-file-earmark-text"></i></div>
                                    <div class="leading-none text-left">
                                        <span class="text-[11px] font-black uppercase tracking-widest block mb-1">Cetak Laporan</span>
                                        <span class="text-[8px] font-bold text-slate-400 uppercase tracking-widest group-hover:text-emerald-200">Unduh Rekap Peminjaman</span>
                                    </div>
                                </a>
                            </div>
                            <div class="absolute -right-10 -bottom-10 h-32 w-32 rounded-full bg-emerald-500/20 blur-2xl"></div>
                        </div>

                        {{-- BUKU PERLU PERBAIKAN --}}
                        <div class="bg-orange-50 rounded-[2rem] p-6 lg:p-8 border border-orange-100 text-left shadow-inner relative overflow-hidden">
                            <div class="flex items-center gap-3 mb-5 text-orange-600 leading-none relative z-10">
                                <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-lg shadow-sm border border-orange-100"><i class="bi bi-journal-medical"></i></div>
                                <div class="text-left">
                                    <h3 class="text-[10px] font-black uppercase tracking-[0.2em] leading-none mb-1">Perawatan Koleksi</h3>
                                    <p class="text-[8px] font-bold text-orange-400 uppercase leading-none">Buku Perlu Perbaikan</p>
                                </div>
                            </div>

                            <div class="space-y-2.5 relative z-10 max-h-[200px] overflow-y-auto custom-scroll pr-1">
                                @forelse($maintenanceItems ?? [] as $maint)
                                <div class="p-3 bg-white rounded-xl shadow-sm border border-orange-100 transition-transform hover:scale-[1.02]">
                                    <div class="flex justify-between items-start mb-1.5">
                                        <p class="text-[10px] font-black text-gray-800 uppercase tracking-tight truncate pr-2">{{ $maint->name }}</p>
                                        <span class="text-[8px] font-black text-orange-600 bg-orange-50 px-2 py-0.5 rounded border border-orange-200 whitespace-nowrap">{{ \Carbon\Carbon::parse($maint->updated_at)->format('d M') }}</span>
                                    </div>
                                    <p class="text-[8px] font-medium text-gray-500 italic truncate">{{ $maint->maintenance_note ?? 'Perlu perbaikan sampul/halaman' }}</p>
                                </div>
                                @empty
                                <div class="p-5 text-center bg-white/60 rounded-xl border border-dashed border-orange-200 leading-none">
                                    <p class="text-[9px] text-orange-400 font-black uppercase tracking-[0.2em]"><i class="bi bi-check2-all me-1"></i> Koleksi Aman</p>
                                </div>
                                @endforelse
                            </div>
                        </div>

                        {{-- CARD STOK KRITIS --}}
                        <div class="bg-red-50 rounded-[2rem] p-6 lg:p-8 border border-red-100 text-left shadow-inner relative overflow-hidden">
                            <div class="absolute top-0 right-0 w-24 h-24 bg-red-100/50 rounded-full blur-2xl -mr-6 -mt-6"></div>
                            
                            <div class="flex items-center gap-3 mb-5 text-red-600 leading-none relative z-10">
                                <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-lg shadow-sm border border-red-100"><i class="bi bi-exclamation-octagon-fill"></i></div>
                                <div class="text-left">
                                    <h3 class="text-[10px] font-black uppercase tracking-[0.2em] leading-none mb-1">Stok Menipis</h3>
                                    <p class="text-[8px] font-bold text-red-400 uppercase leading-none">Peringatan Restock Buku</p>
                                </div>
                            </div>

                            <div class="space-y-2.5 relative z-10 max-h-[150px] overflow-y-auto custom-scroll pr-1">
                                @forelse($lowStockItems ?? [] as $low)
                                <div class="flex justify-between items-center p-3 bg-white rounded-xl shadow-sm border border-red-100 transition-transform hover:scale-[1.02]">
                                    <div class="text-left leading-none overflow-hidden pr-2">
                                        <p class="text-[10px] font-black text-gray-800 uppercase tracking-tight mb-1.5 truncate">{{ $low->name }}</p>
                                        <p class="text-[7px] font-black text-gray-400 uppercase tracking-widest truncate">Kode: {{ $low->asset_code ?? $low->id }}</p>
                                    </div>
                                    <span class="text-[8px] font-black px-2 py-1 bg-red-50 text-red-600 rounded-md border border-red-200 flex-shrink-0">SISA {{ $low->stock }}</span>
                                </div>
                                @empty
                                <div class="p-5 text-center bg-white/60 rounded-xl border border-dashed border-red-200 leading-none">
                                    <p class="text-[9px] text-emerald-600 font-black uppercase tracking-[0.2em]"><i class="bi bi-check-circle-fill me-1"></i> Seluruh Stok Aman</p>
                                </div>
                                @endforelse
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </main>
    </div>

</body>
</html>