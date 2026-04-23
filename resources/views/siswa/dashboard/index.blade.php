<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Siswa Dashboard - TekniLog</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
        .font-jakarta { font-family: 'Plus Jakarta Sans', sans-serif; }
        .custom-scroll::-webkit-scrollbar { width: 5px; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #2563eb; border-radius: 20px; }
    </style>
</head>

<body class="antialiased flex h-screen w-full overflow-hidden text-left" 
      x-data="{ 
        sidebarOpen: false,
        modalDetail: false,
        
        // ✅ Object dinamis untuk Modal Detail
        selectedReq: {
            is_paket: false,
            items: [],
            title: '', sub_info: '', total_qty: 0,
            date: '', status: '', durasi: '', tenggat: '',
            admin_note: '', total_denda: 0
        }
      }">

    {{-- Sidebar --}}
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'" 
           class="fixed inset-y-0 left-0 z-50 flex flex-col w-72 bg-slate-950 text-white border-r border-slate-900 md:static md:flex-shrink-0 h-full transition-transform duration-300">
        @include('siswa.partials.sidebar') 
    </aside>

    <div class="flex flex-1 flex-col h-full min-w-0 overflow-hidden">
        {{-- Header --}}
        @include('siswa.partials.header')

        <main class="flex-1 overflow-y-auto p-6 lg:p-8 pt-2 custom-scroll text-left">
            <div class="mx-auto w-full max-w-[1600px] space-y-6 text-left">
                
                {{-- 1. HERO SECTION & TRUST SCORE (SEJAJAR & COMPACT) --}}
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-stretch">
                    
                    {{-- Welcome Banner (Left - 2/3) --}}
                    <div class="lg:col-span-2 h-full relative overflow-hidden rounded-[2rem] bg-gradient-to-br from-blue-600 to-indigo-700 p-8 shadow-xl shadow-blue-200/50 flex flex-col justify-center border border-white/20">
                        <div class="relative z-10 flex flex-col h-full justify-between">
                            <div>
                                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/20 border border-white/10 backdrop-blur-md mb-3">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                    <span class="text-white text-[10px] font-black uppercase tracking-widest">Otoritas Kelas Aktif</span>
                                </div>
                                <h1 class="text-2xl lg:text-3xl font-black text-white font-jakarta leading-tight uppercase">
                                    {{ $user->name }} 🎓
                                </h1>
                                <p class="text-blue-50/90 font-medium text-xs mt-2 max-w-lg leading-relaxed">
                                    Selamat datang di Pusat Kendali Aset. Pastikan barang kembali tepat waktu untuk menjaga skor kepercayaan kelas.
                                </p>
                            </div>
                            
                            <div class="mt-6 flex flex-wrap gap-3">
                                <a href="{{ route('siswa.request') }}" class="px-5 py-3 bg-white text-blue-700 rounded-xl font-black text-[10px] uppercase tracking-widest shadow-lg hover:scale-105 transition-all flex items-center gap-2 group">
                                    <i class="bi bi-plus-lg text-sm group-hover:rotate-90 transition-transform"></i> Booking Alat
                                </a>
                                <a href="{{ route('siswa.laporan') }}" class="px-5 py-3 bg-blue-800/40 text-white border border-blue-400/30 rounded-xl font-black text-[10px] uppercase tracking-widest backdrop-blur-sm hover:bg-blue-800/60 transition-all">
                                    Lapor Masalah
                                </a>
                            </div>
                        </div>
                        {{-- Background Decoration --}}
                        <div class="absolute -right-10 -bottom-10 w-56 h-56 bg-gradient-to-t from-indigo-500 to-transparent rounded-full opacity-50 blur-3xl"></div>
                        <i class="bi bi-mortarboard absolute -right-6 top-1/2 -translate-y-1/2 text-[160px] text-white/10 rotate-12"></i>
                    </div>

                    {{-- Trust Score Card (Right - 1/3) --}}
                    @php
                        $starScore = number_format(($trustScore / 100) * 5, 1);
                    @endphp
                    <div class="lg:col-span-1 h-full bg-slate-900 rounded-[2rem] p-8 text-white shadow-xl relative overflow-hidden border border-slate-800 flex flex-col justify-between hover:shadow-2xl transition-all">
                        <div class="relative z-10">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">Skor Kepercayaan</p>
                                    <div class="flex items-center gap-2 mt-1">
                                        <i class="bi bi-star-fill text-yellow-400 text-2xl"></i>
                                        <h3 class="text-4xl font-black font-jakarta {{ $trustColor }}">
                                            {{ $starScore }}<span class="text-sm text-slate-600 font-bold ml-1">/ 5.0</span>
                                        </h3>
                                    </div>
                                </div>
                                <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center text-lg {{ $trustColor }}">
                                    <i class="bi bi-shield-check"></i>
                                </div>
                            </div>
                            
                            <div class="space-y-3">
                                {{-- Progress Bar --}}
                                <div>
                                    <div class="flex justify-between text-[9px] font-bold text-slate-400 uppercase mb-1">
                                        <span>Reputasi Kelas</span>
                                        <span class="{{ $trustColor }}">{{ $trustStatus }}</span>
                                    </div>
                                    <div class="h-2 w-full bg-slate-800 rounded-full overflow-hidden border border-slate-700/50">
                                        <div class="h-full bg-yellow-400 transition-all duration-1000 ease-out shadow-[0_0_8px_rgba(250,204,21,0.5)]" style="width: {{ $trustScore }}%"></div>
                                    </div>
                                </div>

                                {{-- Mini Stats Compact --}}
                                <div class="flex items-center gap-2 mt-2">
                                    <div class="flex-1 p-2.5 bg-white/5 rounded-xl border border-white/5 backdrop-blur-sm">
                                        <p class="text-[8px] font-bold text-slate-500 uppercase">Aset Kelas</p>
                                        <p class="text-xs font-black text-white">
                                            {{ $stats['total_items'] ?? 0 }} Unit
                                        </p>
                                    </div>
                                    <div class="flex-1 p-2.5 bg-white/5 rounded-xl border border-white/5 backdrop-blur-sm">
                                        <p class="text-[8px] font-bold text-slate-500 uppercase">Insiden</p>
                                        <p class="text-xs font-black {{ ($stats['broken_count'] ?? 0) > 0 ? 'text-red-400' : 'text-white' }}">
                                            {{ $stats['broken_count'] ?? 0 }} Kasus
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <i class="bi bi-shield-shaded absolute -right-6 -bottom-6 text-[100px] text-white/5"></i>
                    </div>
                </div>

                {{-- 2. KPI STATS GRID --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 text-left">
                    <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-md transition-all group flex flex-col justify-between h-full">
                        <div class="flex justify-between items-start mb-2">
                            <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-lg group-hover:scale-110 transition-transform shadow-inner"><i class="bi bi-box-seam-fill"></i></div>
                            <span class="text-[9px] font-black text-blue-600 bg-blue-50 px-2 py-1 rounded-lg uppercase tracking-widest border border-blue-100">Pemakaian</span>
                        </div>
                        <div>
                            <p class="text-2xl font-black text-slate-900">{{ $stats['total_items'] }}</p>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Item Aktif</p>
                        </div>
                    </div>
                    
                    <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-md transition-all group flex flex-col justify-between h-full">
                        <div class="flex justify-between items-start mb-2">
                            <div class="w-10 h-10 bg-orange-50 text-orange-600 rounded-xl flex items-center justify-center text-lg group-hover:scale-110 transition-transform shadow-inner"><i class="bi bi-hourglass-split"></i></div>
                            <span class="text-[9px] font-black text-orange-600 bg-orange-50 px-2 py-1 rounded-lg uppercase tracking-widest border border-orange-100">Antrian</span>
                        </div>
                        <div>
                            <p class="text-2xl font-black text-slate-900">{{ $stats['pending_count'] }}</p>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Request Menunggu</p>
                        </div>
                    </div>
                    
                    <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-md transition-all group flex flex-col justify-between h-full">
                        <div class="flex justify-between items-start mb-2">
                            <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center text-lg group-hover:scale-110 transition-transform shadow-inner"><i class="bi bi-check-circle-fill"></i></div>
                            <span class="text-[9px] font-black text-emerald-600 bg-emerald-50 px-2 py-1 rounded-lg uppercase tracking-widest border border-emerald-100">Aman</span>
                        </div>
                        <div>
                            <p class="text-2xl font-black text-slate-900">{{ $stats['finished_count'] }}</p>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Selesai Pinjam</p>
                        </div>
                    </div>
                    
                    <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-md transition-all group flex flex-col justify-between h-full">
                        <div class="flex justify-between items-start mb-2">
                            <div class="w-10 h-10 bg-red-50 text-red-600 rounded-xl flex items-center justify-center text-lg group-hover:scale-110 transition-transform shadow-inner"><i class="bi bi-shield-exclamation"></i></div>
                            <span class="text-[9px] font-black text-red-600 bg-red-50 px-2 py-1 rounded-lg uppercase tracking-widest border border-red-100">Issue</span>
                        </div>
                        <div>
                            <p class="text-xl font-black text-red-600">{{ $stats['broken_count'] }} Kasus</p>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Laporan Kendala</p>
                        </div>
                    </div>
                </div>

                {{-- 3. MAIN CONTENT GRID (8:4 Layout) --}}
                <div class="grid lg:grid-cols-12 gap-6 text-left items-stretch">
                    
                    {{-- LEFT COLUMN: ACTIVE & RECENT --}}
                    <div class="lg:col-span-8 space-y-6 flex flex-col">
                        
                        {{-- ✅ ASET KELAS SAAT INI --}}
                        <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden flex flex-col min-h-[300px]">
                            <div class="p-6 border-b border-slate-50 flex items-center justify-between bg-slate-50/50">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100 shadow-sm"><i class="bi bi-laptop"></i></div>
                                    <div>
                                        <h3 class="text-xs font-black text-slate-800 uppercase tracking-widest leading-none">Aset Kelas Saat Ini</h3>
                                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1">Status: Pemakaian Aktif</p>
                                    </div>
                                </div>
                                <span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-lg text-[9px] font-black uppercase tracking-widest border border-blue-100 animate-pulse">Sesi Aktif</span>
                            </div>
                            
                            <div class="overflow-x-auto flex-1 custom-scroll">
                                <table class="w-full text-left text-sm">
                                    <thead class="sticky top-0 bg-white z-10">
                                        <tr class="bg-slate-50/50 text-[9px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100">
                                            <th class="px-6 py-4">Item / Paket</th>
                                            <th class="px-6 py-4">Total Unit</th>
                                            <th class="px-6 py-4">Deadline</th>
                                            <th class="px-6 py-4 text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-50">
                                        @php
                                            $groupedActive = $activeLoans->groupBy(function($item) {
                                                return $item->created_at->format('Y-m-d H:i') . '|' . $item->status;
                                            });
                                        @endphp

                                        @forelse($groupedActive as $group)
                                        @php
                                            $first = $group->first();
                                            $itemCount = $group->count();
                                            $totalQty = $group->sum('quantity');
                                            
                                            $unit = $first->duration_unit == 'hours' ? 'hours' : 'days';
                                            $deadline = $first->created_at->copy()->add($unit, $first->duration_amount);
                                            
                                            $titleName = $itemCount > 1 ? 'Paket Peminjaman' : $first->item->name;
                                            $iconBox = $itemCount > 1 ? 'bi-boxes' : 'bi-laptop';
                                        @endphp
                                        <tr class="hover:bg-blue-50/30 transition-colors group">
                                            <td class="px-6 py-4">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-9 h-9 rounded-xl bg-slate-50 border border-slate-100 text-slate-500 flex items-center justify-center text-sm shadow-sm group-hover:bg-blue-50 group-hover:text-blue-600 group-hover:border-blue-100 transition-all">
                                                        <i class="bi {{ $iconBox }}"></i>
                                                    </div>
                                                    <div class="text-left leading-tight overflow-hidden">
                                                        <span class="font-black text-slate-800 text-xs uppercase leading-tight truncate max-w-[150px] block">{{ $titleName }}</span>
                                                        <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest mt-1 block">{{ $itemCount > 1 ? $itemCount . ' Jenis Barang' : 'KODE: '.$first->item->asset_code }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <span class="font-black text-blue-600 text-base leading-none">{{ $totalQty }} <span class="text-[9px] uppercase text-slate-400 font-bold tracking-widest">Unit</span></span>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="flex flex-col leading-none">
                                                    <span class="font-bold text-slate-700 text-[10px] mb-1.5">{{ $deadline->format('H:i') }} WIB</span>
                                                    <span class="text-[9px] text-red-500 font-black uppercase tracking-widest">{{ $deadline->format('d M Y') }}</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <span class="px-2.5 py-1 rounded-md text-[8px] font-black uppercase border bg-blue-50 text-blue-600 border-blue-100">DIPAKAI</span>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="py-16 text-center">
                                                <div class="text-slate-200 text-5xl mb-3"><i class="bi bi-inbox"></i></div>
                                                <p class="text-slate-400 font-bold text-xs uppercase tracking-widest italic">Tidak ada aset kelas yang sedang dipinjam.</p>
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- ✅ RIWAYAT AKTIVITAS --}}
                        <div class="bg-white rounded-[2rem] p-6 lg:p-8 border border-slate-100 shadow-sm text-left flex-1">
                            <h3 class="text-[10px] font-black font-jakarta text-slate-800 mb-6 uppercase tracking-[0.2em] leading-none border-b border-slate-50 pb-4">Riwayat Aktivitas Terakhir</h3>
                            <div class="space-y-6">
                                @php
                                    $groupedRecent = $recentActivities->groupBy(function($item) {
                                        return $item->created_at->format('Y-m-d H:i') . '|' . $item->status;
                                    })->take(4);
                                @endphp

                                @foreach($groupedRecent as $group)
                                @php
                                    $activity = $group->first();
                                    $itemCount = $group->count();
                                    $totalQty = $group->sum('quantity');

                                    $statusConfig = match($activity->status) {
                                        'pending'  => ['color' => 'bg-orange-50 text-orange-600 border-orange-100', 'icon' => 'bi-hourglass-split'],
                                        'approved' => ['color' => 'bg-blue-50 text-blue-600 border-blue-100', 'icon' => 'bi-box-arrow-up'],
                                        'returned' => ['color' => 'bg-emerald-50 text-emerald-600 border-emerald-100', 'icon' => 'bi-arrow-return-left'],
                                        'rejected' => ['color' => 'bg-red-50 text-red-600 border-red-100', 'icon' => 'bi-x-circle'],
                                        default    => ['color' => 'bg-slate-50 text-slate-600 border-slate-100', 'icon' => 'bi-info-circle']
                                    };

                                    $title = $itemCount > 1 ? 'Paket: ' . $itemCount . ' Barang' : $activity->item->name;
                                @endphp
                                <div class="flex gap-4 relative group transition-all">
                                    @if(!$loop->last) <div class="absolute left-[1.15rem] top-9 bottom-[-1.5rem] w-0.5 bg-slate-100 group-hover:bg-blue-100 transition-colors"></div> @endif
                                    
                                    <div class="w-10 h-10 rounded-xl {{ $statusConfig['color'] }} border flex items-center justify-center flex-shrink-0 z-10 shadow-sm text-lg transition-transform group-hover:scale-110">
                                        <i class="bi {{ $statusConfig['icon'] }}"></i>
                                    </div>
                                    <div class="text-left leading-none pt-1 flex-1">
                                        <div class="flex items-center gap-2 mb-1.5">
                                            <p class="text-[11px] font-black text-slate-900 uppercase tracking-tight leading-tight">
                                                {{ ucfirst($activity->status) }}: <span class="text-blue-600">{{ $title }}</span>
                                            </p>
                                            <span class="text-[9px] font-black text-slate-400 uppercase bg-slate-50 border border-slate-100 px-2 py-0.5 rounded">{{ $totalQty }} UNIT</span>
                                        </div>
                                        <p class="text-[9px] text-slate-400 font-bold mt-1.5 uppercase tracking-widest">{{ $activity->created_at->diffForHumans() }} • SMKN 1 Ciomas</p>
                                    </div>
                                    
                                    {{-- Tombol Detail --}}
                                    <button @click='selectedReq = {
                                        is_paket: {{ $itemCount > 1 ? "true" : "false" }},
                                        items: {{ $group->map(fn($l) => ["barang" => $l->item->name, "jumlah" => $l->quantity, "kode" => $l->item->asset_code])->toJson() }},
                                        title: @json($title),
                                        date: "{{ $activity->created_at->format("d M Y, H:i") }}",
                                        status: "{{ ucfirst($activity->status) }}",
                                        durasi: "{{ $activity->duration_amount }} {{ $activity->duration_unit }}",
                                        admin_note: @json($activity->admin_note ?? "Tidak ada catatan.")
                                    }; modalDetail = true' class="w-9 h-9 rounded-xl bg-slate-50 text-slate-400 hover:bg-blue-600 hover:text-white transition-all flex items-center justify-center shadow-sm border border-slate-100"><i class="bi bi-eye-fill"></i></button>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- RIGHT COLUMN --}}
                    <div class="lg:col-span-4 flex flex-col gap-6 h-full">
                        
                        {{-- Otoritas Card (✅ SUDAH DIPERBAIKI) --}}
                        <div class="bg-slate-900 rounded-[2rem] p-8 text-white shadow-xl relative overflow-hidden text-left border border-slate-800 flex-1 group/card">
                            
                            {{-- Decorative Ambient Lights --}}
                            <div class="absolute top-0 right-0 w-32 h-32 bg-blue-500/10 rounded-full blur-2xl group-hover/card:bg-blue-500/20 transition-all duration-700 pointer-events-none"></div>
                            <div class="absolute bottom-0 left-0 w-24 h-24 bg-indigo-500/10 rounded-full blur-2xl group-hover/card:bg-indigo-500/20 transition-all duration-700 pointer-events-none"></div>
                            <i class="bi bi-diagram-3-fill absolute -right-6 -bottom-6 text-[120px] text-white/5 rotate-12 pointer-events-none"></i>
                    
                            <div class="relative z-10 flex flex-col h-full">
                                <div class="mb-6">
                                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] flex items-center gap-2 mb-3">
                                        <span class="w-6 h-6 rounded-lg bg-white/10 flex items-center justify-center text-blue-400 shadow-inner">
                                            <i class="bi bi-shield-check"></i>
                                        </span>
                                        Struktur Otoritas
                                    </h3>
                                    <p class="text-[9px] text-slate-500 font-medium tracking-wide">Penanggung jawab utama peminjaman dan kepatuhan aset kelas.</p>
                                </div>
                        
                                <div class="space-y-3 mt-2">
                                    {{-- Ketua Kelas --}}
                                    <div class="flex items-center justify-between p-3.5 bg-white/5 rounded-[1.25rem] border border-white/10 hover:bg-white/10 hover:border-white/20 transition-all cursor-default group">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-blue-700 p-0.5 shadow-lg shadow-blue-500/30 group-hover:scale-105 transition-transform">
                                                <div class="w-full h-full bg-slate-900 rounded-[10px] flex items-center justify-center">
                                                    <i class="bi bi-star-fill text-blue-400 text-sm"></i>
                                                </div>
                                            </div>
                                            <div class="text-left leading-none">
                                                <p class="text-[8px] font-black text-blue-400 uppercase tracking-widest mb-1.5">Ketua Kelas</p>
                                                <p class="text-xs font-black text-slate-100 uppercase truncate max-w-[120px]">{{ $user->chairman_name ?? '-' }}</p>
                                            </div>
                                        </div>
                                        <div class="w-7 h-7 rounded-lg bg-white/5 flex items-center justify-center text-slate-500 group-hover:text-blue-400 transition-colors shadow-inner">
                                            <i class="bi bi-person-badge"></i>
                                        </div>
                                    </div>
                        
                                    {{-- Wakil Ketua --}}
                                    <div class="flex items-center justify-between p-3.5 bg-white/5 rounded-[1.25rem] border border-white/10 hover:bg-white/10 hover:border-white/20 transition-all cursor-default group">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-700 p-0.5 shadow-lg shadow-indigo-500/30 group-hover:scale-105 transition-transform">
                                                <div class="w-full h-full bg-slate-900 rounded-[10px] flex items-center justify-center">
                                                    <i class="bi bi-star-half text-indigo-400 text-sm"></i>
                                                </div>
                                            </div>
                                            <div class="text-left leading-none">
                                                <p class="text-[8px] font-black text-indigo-400 uppercase tracking-widest mb-1.5">Wakil Ketua</p>
                                                <p class="text-xs font-black text-slate-100 uppercase truncate max-w-[120px]">{{ $user->vice_chairman_name ?? '-' }}</p>
                                            </div>
                                        </div>
                                        <div class="w-7 h-7 rounded-lg bg-white/5 flex items-center justify-center text-slate-500 group-hover:text-indigo-400 transition-colors shadow-inner">
                                            <i class="bi bi-person-badge"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Tips Card --}}
                        <div class="bg-white rounded-[2rem] p-6 border border-slate-100 shadow-sm text-left leading-none flex-none">
                            <div class="flex items-center gap-2 mb-4 leading-none">
                                <div class="w-8 h-8 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center shadow-inner text-sm"><i class="bi bi-lightbulb-fill"></i></div>
                                <h3 class="text-[9px] font-black text-slate-800 uppercase tracking-[0.2em] leading-none">Tips Toolman</h3>
                            </div>
                            <p class="text-[10px] text-slate-500 font-medium leading-relaxed italic border-l-2 border-blue-100 pl-3">
                                "Selalu foto kondisi barang pas awal minjem, bro. Biar kalau ada lecet yang bukan salah kelas kita, ada buktinya pas balikin ke gudang."
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    {{-- ✅ MODAL DETAIL PINTAR (DASHBOARD) --}}
    <div x-show="modalDetail" x-cloak class="fixed inset-0 z-[120] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-md" @click="modalDetail = false"></div>
        <div x-show="modalDetail" x-transition.scale.95 class="relative w-full max-w-lg bg-white rounded-[2rem] shadow-2xl p-8 lg:p-10 border border-white flex flex-col max-h-[90vh] text-left leading-none overflow-y-auto custom-scroll">
            
            <div class="flex justify-between items-start mb-6 text-left border-b border-slate-100 pb-5">
                <div>
                    <span class="text-[8px] font-black text-blue-600 uppercase tracking-[0.2em] bg-blue-50 border border-blue-100 px-2.5 py-1 rounded-md mb-2 inline-block">Detail Aktivitas</span>
                    <h3 class="text-xl lg:text-2xl font-black text-slate-900 uppercase mt-2 tracking-tight leading-tight truncate max-w-[250px]" x-text="selectedReq.title"></h3>
                </div>
                <button @click="modalDetail = false" class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 hover:bg-red-100 hover:text-red-500 transition-colors flex items-center justify-center flex-shrink-0"><i class="bi bi-x-lg"></i></button>
            </div>

            <div class="space-y-6 text-left leading-none">
                <div class="grid grid-cols-2 gap-4">
                    <div class="p-5 bg-slate-50 rounded-2xl border border-slate-100">
                        <p class="text-[8px] font-bold text-slate-400 uppercase tracking-widest mb-2">Waktu Update</p>
                        <p class="text-[10px] font-black text-slate-800" x-text="selectedReq.date"></p>
                    </div>
                    <div class="p-5 bg-slate-50 rounded-2xl border border-slate-100">
                        <p class="text-[8px] font-bold text-slate-400 uppercase tracking-widest mb-2">Status</p>
                        <p class="text-[10px] font-black text-blue-600 uppercase" x-text="selectedReq.status"></p>
                    </div>
                </div>

                {{-- List Barang Paket (Jika ada) --}}
                <template x-if="selectedReq.is_paket">
                    <div class="p-6 bg-white rounded-[1.5rem] border border-slate-100 shadow-sm space-y-4">
                        <div class="flex items-center gap-2 text-blue-600 font-black text-[10px] uppercase tracking-widest mb-2 border-b border-slate-100 pb-3">
                            <i class="bi bi-boxes text-sm"></i> Rincian Paket Barang
                        </div>
                        <div class="space-y-3 max-h-[150px] overflow-y-auto custom-scroll pr-1">
                            <template x-for="item in selectedReq.items" :key="item.kode">
                                <div class="flex justify-between items-center p-3 bg-slate-50 border border-slate-100 rounded-xl">
                                    <div class="text-left leading-none overflow-hidden pr-2">
                                        <p class="font-bold text-slate-900 text-[10px] lg:text-xs uppercase truncate max-w-[150px]" x-text="item.barang"></p>
                                        <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mt-1" x-text="'KODE: ' + item.kode"></p>
                                    </div>
                                    <span class="px-2.5 py-1 bg-white border border-slate-200 rounded-md text-[9px] font-black text-blue-600 flex-shrink-0 shadow-sm" x-text="item.jumlah + ' Unit'"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>

                <div class="p-6 bg-slate-900 rounded-[1.5rem] border border-slate-800 text-left shadow-xl relative overflow-hidden mt-2">
                    <div class="relative z-10">
                        <p class="text-[8px] font-black text-emerald-400 uppercase tracking-widest mb-2">Feedback / Pesan Toolman:</p>
                        <p class="text-[10px] text-slate-300 font-medium leading-relaxed italic" x-text="selectedReq.admin_note"></p>
                    </div>
                    <i class="bi bi-chat-right-quote absolute -right-4 -bottom-4 text-[60px] text-white/5"></i>
                </div>
            </div>

            <button @click="modalDetail = false" class="w-full mt-8 py-4 bg-slate-900 text-white rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] shadow-xl hover:bg-blue-600 active:scale-95 transition-all border border-slate-800 hover:border-blue-500">Tutup Informasi</button>
        </div>
    </div>

</body>
</html>