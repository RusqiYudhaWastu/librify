<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Member - Librify</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
        .font-jakarta { font-family: 'Plus Jakarta Sans', sans-serif; }
        .custom-scroll::-webkit-scrollbar { width: 5px; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #06b6d4; border-radius: 20px; }
    </style>
</head>

{{-- ✅ PANGGIL FUNGSI SCRIPT DI SINI BIAR RAPI & GAK BOCOR --}}
<body class="antialiased flex h-screen w-full overflow-hidden text-left font-jakarta" 
      x-data="studentDashboard()">

    {{-- Sidebar Student --}}
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'" 
           class="fixed inset-y-0 left-0 z-50 flex flex-col w-72 bg-slate-950 text-white border-r border-slate-900 md:static md:flex-shrink-0 h-full transition-transform duration-300">
        @include('student.partials.sidebar') 
    </aside>

    <div class="flex flex-1 flex-col h-full min-w-0 overflow-hidden">
        {{-- Header Student --}}
        @include('student.partials.header')

        <main class="flex-1 overflow-y-auto p-6 lg:p-8 pt-2 custom-scroll text-left">
            <div class="mx-auto w-full max-w-[1600px] space-y-6 text-left">

                {{-- 1. HERO SECTION & TRUST SCORE (SEJAJAR & COMPACT) --}}
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-stretch">
                    
                    {{-- Welcome Banner (Left - 2/3) --}}
                    <div class="lg:col-span-2 h-full relative overflow-hidden rounded-[2rem] bg-gradient-to-br from-cyan-600 to-blue-700 p-8 shadow-xl shadow-cyan-200/50 flex flex-col justify-center border border-white/20">
                        <div class="relative z-10 flex flex-col h-full justify-between">
                            <div>
                                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/20 border border-white/10 backdrop-blur-md mb-3">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-400 animate-pulse"></span>
                                    <span class="text-white text-[10px] font-black uppercase tracking-widest">Sistem Online</span>
                                </div>
                                <h1 class="text-2xl lg:text-3xl font-black text-white font-jakarta leading-tight uppercase">
                                    Halo, {{ explode(' ', Auth::user()->name)[0] }}! 👋
                                </h1>
                                <p class="text-cyan-50/90 font-medium text-xs mt-2 max-w-lg leading-relaxed">
                                    Selamat datang di dashboard Librify. Cek status sirkulasimu dan pastikan tidak ada tagihan keterlambatan buku ya!
                                </p>
                            </div>
                            
                            <div class="mt-6 flex flex-wrap gap-3">
                                <a href="{{ route('student.request') }}" class="px-5 py-3 bg-white text-cyan-700 rounded-xl font-black text-[10px] uppercase tracking-widest shadow-lg hover:scale-105 transition-all flex items-center gap-2 group">
                                    <i class="bi bi-plus-lg text-sm group-hover:rotate-90 transition-transform"></i> Pinjam Buku
                                </a>
                                <a href="{{ route('student.laporan') }}" class="px-5 py-3 bg-cyan-800/40 text-white border border-cyan-400/30 rounded-xl font-black text-[10px] uppercase tracking-widest backdrop-blur-sm hover:bg-cyan-800/60 transition-all">
                                    Lapor Masalah
                                </a>
                            </div>
                        </div>
                        {{-- Background Decoration --}}
                        <div class="absolute -right-10 -bottom-10 w-56 h-56 bg-gradient-to-t from-blue-600 to-transparent rounded-full opacity-50 blur-3xl"></div>
                        <i class="bi bi-book absolute -right-6 top-1/2 -translate-y-1/2 text-[160px] text-white/10 rotate-12"></i>
                    </div>

                    {{-- Trust Score Card (Right - 1/3) --}}
                    <div class="lg:col-span-1 h-full bg-slate-900 rounded-[2rem] p-8 text-white shadow-xl relative overflow-hidden border {{ $cardBorder }} flex flex-col justify-between hover:shadow-2xl transition-all">
                        <div class="relative z-10">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">Skor Kepercayaan</p>
                                    <div class="flex items-center gap-2 mt-1">
                                        <i class="bi bi-star-fill text-yellow-400 text-2xl"></i>
                                        <h3 class="text-4xl font-black font-jakarta {{ $scoreColor }}">
                                            {{ $starScore }}<span class="text-sm text-slate-600 font-bold ml-1">/ 5.0</span>
                                        </h3>
                                    </div>
                                </div>
                                <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center text-lg {{ $scoreColor }}">
                                    <i class="bi bi-star"></i>
                                </div>
                            </div>
                            
                            <div class="space-y-3">
                                {{-- Progress Bar --}}
                                <div>
                                    <div class="flex justify-between text-[9px] font-bold text-slate-400 uppercase mb-1">
                                        <span>Reputasi</span>
                                        <span class="{{ $scoreColor }}">{{ $statusText }}</span>
                                    </div>
                                    <div class="h-2 w-full bg-slate-800 rounded-full overflow-hidden border border-slate-700/50">
                                        <div class="h-full {{ $barColor }} transition-all duration-1000 ease-out shadow-[0_0_8px_currentColor]" style="width: {{ $trustScore }}%"></div>
                                    </div>
                                </div>

                                {{-- Mini Stats Compact --}}
                                <div class="flex items-center gap-2 mt-2">
                                    <div class="flex-1 p-2.5 bg-white/5 rounded-xl border border-white/5 backdrop-blur-sm">
                                        <p class="text-[8px] font-bold text-slate-500 uppercase">Denda</p>
                                        <p class="text-xs font-black {{ $totalFines > 0 ? 'text-red-400' : 'text-white' }}">
                                            Rp {{ number_format($totalFines ?? 0, 0, ',', '.') }}
                                        </p>
                                    </div>
                                    <div class="flex-1 p-2.5 bg-white/5 rounded-xl border border-white/5 backdrop-blur-sm">
                                        <p class="text-[8px] font-bold text-slate-500 uppercase">Dipinjam</p>
                                        <p class="text-xs font-black {{ $activeLoans > 0 ? 'text-yellow-400' : 'text-white' }}">
                                            {{ $activeLoans }} Buku
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <i class="bi bi-star absolute -right-6 -bottom-6 text-[100px] text-white/5"></i>
                    </div>
                </div>

                {{-- 2. KPI STATS GRID (COMPACT & SEJAJAR) --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 text-left">
                    <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-md transition-all group flex flex-col justify-between h-full">
                        <div class="flex justify-between items-start mb-2">
                            <div class="w-10 h-10 bg-cyan-50 text-cyan-600 rounded-xl flex items-center justify-center text-lg group-hover:scale-110 transition-transform shadow-inner"><i class="bi bi-book-half"></i></div>
                            <span class="text-[9px] font-black text-cyan-600 bg-cyan-50 px-2 py-1 rounded-lg uppercase tracking-widest border border-cyan-100">Aktif</span>
                        </div>
                        <div>
                            <p class="text-2xl font-black text-slate-900">{{ $activeLoans }}</p>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Sedang Dipinjam</p>
                        </div>
                    </div>
                    
                    @php $pendingCount = $recentActivities->where('status', 'pending')->count(); @endphp
                    <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-md transition-all group flex flex-col justify-between h-full">
                        <div class="flex justify-between items-start mb-2">
                            <div class="w-10 h-10 bg-orange-50 text-orange-600 rounded-xl flex items-center justify-center text-lg group-hover:scale-110 transition-transform shadow-inner"><i class="bi bi-hourglass-split"></i></div>
                            <span class="text-[9px] font-black text-orange-600 bg-orange-50 px-2 py-1 rounded-lg uppercase tracking-widest border border-orange-100">Proses</span>
                        </div>
                        <div>
                            <p class="text-2xl font-black text-slate-900">{{ $pendingCount }}</p>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Menunggu Acc</p>
                        </div>
                    </div>
                    
                    <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-md transition-all group flex flex-col justify-between h-full">
                        <div class="flex justify-between items-start mb-2">
                            <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center text-lg group-hover:scale-110 transition-transform shadow-inner"><i class="bi bi-check-circle-fill"></i></div>
                            <span class="text-[9px] font-black text-emerald-600 bg-emerald-50 px-2 py-1 rounded-lg uppercase tracking-widest border border-emerald-100">Selesai</span>
                        </div>
                        <div>
                            <p class="text-2xl font-black text-slate-900">{{ $returnedLoans }}</p>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Kembali</p>
                        </div>
                    </div>
                    
                    <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-md transition-all group flex flex-col justify-between h-full">
                        <div class="flex justify-between items-start mb-2">
                            <div class="w-10 h-10 bg-red-50 text-red-600 rounded-xl flex items-center justify-center text-lg group-hover:scale-110 transition-transform shadow-inner"><i class="bi bi-cash-coin"></i></div>
                            <span class="text-[9px] font-black text-red-600 bg-red-50 px-2 py-1 rounded-lg uppercase tracking-widest border border-red-100">Tagihan</span>
                        </div>
                        <div>
                            <p class="text-xl font-black text-red-600 truncate">Rp {{ number_format($totalFines ?? 0, 0, ',', '.') }}</p>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Denda</p>
                        </div>
                    </div>
                </div>

                {{-- 3. MAIN CONTENT GRID (8:4 Layout) --}}
                <div class="grid lg:grid-cols-12 gap-6 text-left items-stretch">
                    
                    {{-- LEFT: RECENT ACTIVITY (GROUPED) --}}
                    <div class="lg:col-span-8 bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden flex flex-col h-full max-h-[500px]">
                        <div class="p-6 border-b border-slate-50 flex justify-between items-center bg-slate-50/50">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-cyan-50 text-cyan-600 flex items-center justify-center border border-cyan-100 shadow-sm"><i class="bi bi-clock-history"></i></div>
                                <div>
                                    <h3 class="text-xs font-black text-slate-800 uppercase tracking-widest leading-none">Aktivitas Terakhir</h3>
                                </div>
                            </div>
                            <a href="{{ route('student.request') }}" class="text-[10px] font-bold text-slate-400 hover:text-cyan-600 uppercase tracking-wider transition-colors">Lihat Riwayat Penuh</a>
                        </div>
                        
                        <div class="overflow-y-auto custom-scroll flex-1">
                            <table class="w-full text-left">
                                <thead class="sticky top-0 bg-white z-10">
                                    <tr class="bg-slate-50/50 text-[9px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100">
                                        <th class="px-6 py-4">Buku / Paket</th>
                                        <th class="px-6 py-4">Jadwal & Waktu</th>
                                        <th class="px-6 py-4 text-center">Status</th>
                                        <th class="px-6 py-4 text-center">Detail</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-50 text-sm">
                                    @php
                                        // Logic Grouping untuk Dashboard
                                        $groupedRecent = $recentActivities->groupBy(function($item) {
                                            return $item->created_at->format('Y-m-d H:i') . '|' . $item->status;
                                        })->take(5); // Ambil 5 grup terbaru
                                    @endphp

                                    @forelse($groupedRecent as $group)
                                    @php
                                        $first = $group->first();
                                        $itemCount = $group->count();
                                        $totalQty = $group->sum('quantity');
                                        
                                        $unit = $first->duration_unit == 'hours' ? 'hours' : 'days';
                                        $deadline = $first->created_at->copy()->add($unit, $first->duration_amount);
                                        
                                        $titleName = $itemCount > 1 ? 'Paket Peminjaman' : $first->item->name;
                                        $subName = $itemCount > 1 ? $itemCount . ' Judul Buku' : 'QTY: ' . $first->quantity . ' Buku';
                                        $iconBox = $itemCount > 1 ? 'bi-journals' : 'bi-book';
                                        
                                        $statusClass = match($first->status) {
                                            'borrowed', 'approved' => 'bg-cyan-50 text-cyan-600 border-cyan-100',
                                            'returned' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                            'pending'  => 'bg-orange-50 text-orange-600 border-orange-100',
                                            default    => 'bg-red-50 text-red-600 border-red-100'
                                        };
                                        $statusTextLabel = match($first->status) {
                                            'borrowed', 'approved' => 'Dipinjam',
                                            'returned' => 'Selesai',
                                            'pending'  => 'Menunggu',
                                            default    => 'Ditolak'
                                        };
                                    @endphp
                                    <tr class="hover:bg-cyan-50/30 transition-colors group">
                                        <td class="px-6 py-4 text-left">
                                            <div class="flex items-center gap-3">
                                                <div class="w-9 h-9 rounded-xl bg-slate-50 border border-slate-100 text-slate-500 flex items-center justify-center text-sm shadow-sm group-hover:bg-cyan-50 group-hover:text-cyan-600 group-hover:border-cyan-100 transition-all">
                                                    <i class="bi {{ $iconBox }}"></i>
                                                </div>
                                                <div class="text-left leading-tight overflow-hidden">
                                                    <p class="font-black text-slate-800 text-xs uppercase leading-tight truncate max-w-[150px]">{{ $titleName }}</p>
                                                    <p class="text-[9px] font-bold text-slate-400 uppercase mt-1 tracking-widest">{{ $subName }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex flex-col text-left leading-none">
                                                <p class="text-[10px] font-bold text-slate-500 uppercase mb-1">{{ $first->created_at->format('d M Y') }}</p>
                                                <p class="text-[9px] font-bold text-slate-400">{{ $first->created_at->format('H:i') }} WIB</p>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="px-2.5 py-1 rounded-md text-[8px] font-black uppercase border {{ $statusClass }}">
                                                {{ $statusTextLabel }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <button type="button" @click='selectedReq = {
                                                is_paket: {{ $itemCount > 1 ? 'true' : 'false' }},
                                                items: {{ $group->map(fn($l) => ["buku" => $l->item->name, "jumlah" => $l->quantity, "kode" => $l->item->asset_code])->toJson() }},
                                                title: @json($titleName),
                                                sub_info: @json($subName),
                                                total_qty: "{{ $totalQty }}",
                                                date: "{{ $first->created_at->format("d M Y, H:i") }}",
                                                status: "{{ $statusTextLabel }}",
                                                durasi: "{{ $first->duration_amount }} {{ $first->duration_unit == "hours" ? "Jam" : "Hari" }}",
                                                tenggat: "{{ $deadline->format("d M Y, H:i") }}"
                                            }; modalDetail = true' 
                                            class="w-9 h-9 rounded-xl bg-gray-50 text-gray-400 hover:bg-cyan-600 hover:text-white transition-all mx-auto flex items-center justify-center shadow-sm border border-gray-100">
                                                <i class="bi bi-eye-fill"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="py-16 text-center">
                                            <div class="text-slate-200 text-5xl mb-3"><i class="bi bi-inbox"></i></div>
                                            <p class="text-slate-400 font-bold text-xs uppercase tracking-widest italic">Belum ada aktivitas.</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- RIGHT: PROFILE CARD (COMPACT & ALIGNED) --}}
                    <div class="lg:col-span-4 flex flex-col gap-6 h-full">
                        
                        {{-- Profile Card --}}
                        <div class="bg-white rounded-[2rem] border border-slate-100 shadow-lg relative overflow-hidden group flex-1 flex flex-col">
                            <div class="h-20 bg-gradient-to-r from-cyan-500 to-blue-600 relative">
                                <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 8px 8px;"></div>
                            </div>

                            <div class="px-6 pb-6 text-center relative flex-1 flex flex-col">
                                <div class="w-20 h-20 mx-auto -mt-10 rounded-[1.5rem] bg-white p-1 shadow-md relative z-10">
                                    <div class="w-full h-full rounded-[1.2rem] overflow-hidden bg-slate-100 flex items-center justify-center">
                                        @if(Auth::user()->profile_photo_url)
                                            <img src="{{ Auth::user()->profile_photo_url }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="text-cyan-600 font-black text-2xl uppercase">{{ substr(Auth::user()->name, 0, 2) }}</div>
                                        @endif
                                    </div>
                                    <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-emerald-500 border-4 border-white rounded-full"></div>
                                </div>

                                <h3 class="mt-3 text-base font-black text-slate-900 uppercase tracking-tight leading-tight">{{ Auth::user()->name }}</h3>
                                <p class="text-[9px] font-bold text-cyan-600 uppercase tracking-widest mt-0.5 mb-5">Member Perpustakaan</p>

                                <div class="space-y-2 text-left mt-auto">
                                    <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50 border border-slate-100 group-hover:border-cyan-100 transition-colors">
                                        <div class="flex items-center gap-2">
                                            <div class="w-6 h-6 rounded-lg bg-white text-cyan-600 flex items-center justify-center text-xs shadow-sm"><i class="bi bi-mortarboard-fill"></i></div>
                                            <span class="text-[9px] font-bold text-slate-500 uppercase tracking-widest">Akses</span>
                                        </div>
                                        <span class="text-[10px] font-black text-slate-800">{{ Auth::user()->classRoom->name ?? 'MEMBER UMUM' }}</span>
                                    </div>

                                    <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50 border border-slate-100 group-hover:border-cyan-100 transition-colors">
                                        <div class="flex items-center gap-2">
                                            <div class="w-6 h-6 rounded-lg bg-white text-cyan-600 flex items-center justify-center text-xs shadow-sm"><i class="bi bi-upc-scan"></i></div>
                                            <span class="text-[9px] font-bold text-slate-500 uppercase tracking-widest">NISN</span>
                                        </div>
                                        <span class="text-[10px] font-black text-slate-800">{{ Auth::user()->nisn ?? '-' }}</span>
                                    </div>

                                    <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50 border border-slate-100 group-hover:border-cyan-100 transition-colors">
                                        <div class="flex items-center gap-2">
                                            <div class="w-6 h-6 rounded-lg bg-white text-cyan-600 flex items-center justify-center text-xs shadow-sm"><i class="bi bi-envelope-fill"></i></div>
                                            <span class="text-[9px] font-bold text-slate-500 uppercase tracking-widest">Email</span>
                                        </div>
                                        <span class="text-[9px] font-black text-slate-800 truncate w-24 text-right">{{ Auth::user()->email }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Tips Card --}}
                        <div class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-[2rem] p-6 text-white shadow-lg relative overflow-hidden flex-none">
                            <div class="relative z-10">
                                <div class="flex items-center gap-2 mb-2">
                                    <div class="w-6 h-6 bg-white/10 rounded-lg flex items-center justify-center backdrop-blur-sm text-xs"><i class="bi bi-lightbulb-fill text-yellow-300"></i></div>
                                    <h3 class="text-[10px] font-black uppercase tracking-widest">Tips Petugas</h3>
                                </div>
                                <p class="text-[10px] text-slate-300 font-medium leading-relaxed italic">
                                    "Jaga kondisi buku yang kamu pinjam. Hilang atau rusak akan dikenakan denda sesuai kebijakan perpustakaan."
                                </p>
                            </div>
                            <i class="bi bi-quote absolute -right-2 -bottom-2 text-[80px] text-white/5"></i>
                        </div>

                    </div>
                </div>

            </div>
        </main>
    </div>

    {{-- ✅ MODAL DETAIL PINTAR (GROUPED ITEMS) --}}
    <div x-show="modalDetail" x-cloak class="fixed inset-0 z-[120] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-md" @click="modalDetail = false"></div>
        <div x-show="modalDetail" x-transition.scale.95 class="relative w-full max-w-lg bg-white rounded-[2rem] shadow-2xl p-8 lg:p-10 border border-white flex flex-col max-h-[90vh] text-left leading-none overflow-y-auto custom-scroll">
            
            <div class="flex justify-between items-start mb-6 text-left border-b border-slate-100 pb-5">
                <div>
                    <span class="text-[8px] font-black text-cyan-600 uppercase tracking-[0.2em] bg-cyan-50 border border-cyan-100 px-2.5 py-1 rounded-md mb-2 inline-block">Aktivitas Terakhir</span>
                    <h3 class="text-xl lg:text-2xl font-black text-gray-900 uppercase mt-2 tracking-tight leading-tight truncate max-w-[250px]" x-text="selectedReq.title"></h3>
                    <p class="text-[10px] font-bold text-gray-400 uppercase mt-1.5 tracking-widest" x-text="selectedReq.sub_info"></p>
                </div>
                <button @click="modalDetail = false" class="w-8 h-8 rounded-full bg-gray-100 text-gray-500 hover:bg-red-100 hover:text-red-500 transition-colors flex items-center justify-center flex-shrink-0"><i class="bi bi-x-lg"></i></button>
            </div>

            <div class="space-y-6 text-left leading-none">
                
                {{-- INFO STATUS & TANGGAL --}}
                <div class="p-6 bg-slate-50 rounded-2xl border border-slate-100 flex justify-between items-center shadow-sm">
                    <div class="flex flex-col">
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Status Saat Ini</span>
                        <span class="text-xs font-black uppercase px-2.5 py-1 rounded-md border w-fit" 
                              :class="{
                                  'text-orange-600 bg-orange-50 border-orange-200': selectedReq.status === 'Menunggu',
                                  'text-cyan-600 bg-cyan-50 border-cyan-200': selectedReq.status === 'Dipinjam',
                                  'text-emerald-600 bg-emerald-50 border-emerald-200': selectedReq.status === 'Selesai',
                                  'text-red-600 bg-red-50 border-red-200': selectedReq.status === 'Ditolak'
                              }"
                              x-text="selectedReq.status"></span>
                    </div>
                    <div class="px-3 py-2 bg-white rounded-xl shadow-sm border border-slate-100 text-center">
                         <span class="text-[8px] font-bold text-gray-400 uppercase tracking-widest block mb-0.5">Durasi</span>
                         <span class="text-[10px] font-black text-cyan-600 uppercase tracking-widest" x-text="selectedReq.durasi"></span>
                    </div>
                </div>

                {{-- LIST BUKU DALAM PAKET (Jika Paket) --}}
                <template x-if="selectedReq.is_paket">
                    <div class="p-6 bg-white rounded-[1.5rem] border border-gray-100 shadow-sm space-y-4">
                        <div class="flex items-center gap-2 text-cyan-600 font-black text-[10px] uppercase tracking-widest mb-2 border-b border-slate-100 pb-3">
                            <i class="bi bi-journals text-sm"></i> Rincian Buku
                        </div>
                        <div class="space-y-3 max-h-[150px] overflow-y-auto custom-scroll pr-1">
                            <template x-for="item in selectedReq.items" :key="item.kode">
                                <div class="flex justify-between items-center p-3 bg-slate-50 border border-slate-100 rounded-xl">
                                    <div class="text-left leading-none overflow-hidden pr-2">
                                        <p class="font-bold text-gray-900 text-[10px] lg:text-xs uppercase truncate max-w-[150px]" x-text="item.buku"></p>
                                        <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest mt-1" x-text="'KODE: ' + item.kode"></p>
                                    </div>
                                    <span class="px-2.5 py-1 bg-white border border-gray-200 rounded-md text-[9px] font-black text-cyan-600 flex-shrink-0 shadow-sm" x-text="item.jumlah + ' Buku'"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>

                {{-- TIMELINE SECTION --}}
                <div class="p-6 bg-white rounded-[1.5rem] border border-gray-100 shadow-sm space-y-4">
                    <div class="flex items-center gap-2 text-cyan-600 font-black text-[10px] uppercase tracking-widest mb-1">
                        <i class="bi bi-calendar-week-fill"></i> Timeline Sirkulasi
                    </div>
                    <div class="flex justify-between items-start relative mt-2">
                        <div class="absolute top-2 left-4 right-4 h-0.5 bg-gray-100 -z-10"></div>
                        <div class="flex flex-col items-center bg-white px-2">
                            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Mulai Request</span>
                            <span class="text-[10px] font-bold text-gray-900 bg-gray-50 border border-gray-100 px-2 py-1 rounded" x-text="selectedReq.date"></span>
                        </div>
                        <div class="flex flex-col items-center bg-white px-2">
                            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Batas Waktu (Deadline)</span>
                            <span class="text-[10px] font-bold text-red-500 bg-red-50 border border-red-100 px-2 py-1 rounded" x-text="selectedReq.tenggat"></span>
                        </div>
                    </div>
                </div>

            </div>

            <button @click="modalDetail = false" class="w-full mt-8 py-4 bg-slate-900 text-white rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] shadow-xl hover:bg-cyan-600 active:scale-95 transition-all border border-slate-800 hover:border-cyan-500">Tutup Panel Informasi</button>
        </div>
    </div>

    {{-- Script Alpine --}}
    <script>
        function studentDashboard() {
            return {
                sidebarOpen: false,
                modalDetail: false,
                selectedReq: {
                    is_paket: false,
                    items: [],
                    title: '', sub_info: '', total_qty: 0,
                    date: '', status: '', durasi: '', tenggat: ''
                }
            }
        }
    </script>
</body>
</html>