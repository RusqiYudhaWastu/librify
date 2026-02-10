<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Siswa - TekniLog</title>

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

<body class="antialiased flex h-screen w-full overflow-hidden text-left" x-data="{ sidebarOpen: false }">

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
                
                {{-- LOGIC SCORE SYSTEM --}}
                @php
                    $baseScore = 100;
                    $finePenalty = ($totalFines > 0) ? floor($totalFines / 1000) : 0; 
                    $trustScore = max(0, $baseScore - $finePenalty);

                    if($trustScore >= 90) {
                        $scoreColor = 'text-emerald-400';
                        $barColor = 'bg-emerald-500';
                        $statusText = 'Sangat Baik';
                        $cardBorder = 'border-slate-800';
                    } elseif($trustScore >= 70) {
                        $scoreColor = 'text-yellow-400';
                        $barColor = 'bg-yellow-500';
                        $statusText = 'Perlu Perhatian';
                        $cardBorder = 'border-yellow-500/50';
                    } else {
                        $scoreColor = 'text-red-400';
                        $barColor = 'bg-red-500';
                        $statusText = 'Bermasalah';
                        $cardBorder = 'border-red-500/50';
                    }
                @endphp

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
                                    Selamat datang di dashboard TekniLog. Cek status peminjamanmu dan pastikan tidak ada tanggungan alat ya!
                                </p>
                            </div>
                            
                            <div class="mt-6 flex flex-wrap gap-3">
                                <a href="{{ route('student.request') }}" class="px-5 py-3 bg-white text-cyan-700 rounded-xl font-black text-[10px] uppercase tracking-widest shadow-lg hover:scale-105 transition-all flex items-center gap-2 group">
                                    <i class="bi bi-plus-lg text-sm group-hover:rotate-90 transition-transform"></i> Pinjam Alat
                                </a>
                                <a href="{{ route('student.laporan') }}" class="px-5 py-3 bg-cyan-800/40 text-white border border-cyan-400/30 rounded-xl font-black text-[10px] uppercase tracking-widest backdrop-blur-sm hover:bg-cyan-800/60 transition-all">
                                    Lapor Kendala
                                </a>
                            </div>
                        </div>
                        {{-- Background Decoration --}}
                        <div class="absolute -right-10 -bottom-10 w-56 h-56 bg-gradient-to-t from-blue-600 to-transparent rounded-full opacity-50 blur-3xl"></div>
                        <i class="bi bi-motherboard absolute -right-6 top-1/2 -translate-y-1/2 text-[160px] text-white/10 rotate-12"></i>
                    </div>

                    {{-- Trust Score Card (Right - 1/3) --}}
                    <div class="lg:col-span-1 h-full bg-slate-900 rounded-[2rem] p-8 text-white shadow-xl relative overflow-hidden border {{ $cardBorder }} flex flex-col justify-between">
                        <div class="relative z-10">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">Skor Kepercayaan</p>
                                    <h3 class="text-4xl font-black font-jakarta {{ $scoreColor }} mt-1">
                                        {{ $trustScore }}<span class="text-sm text-slate-600 font-bold ml-1">/100</span>
                                    </h3>
                                </div>
                                <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center text-lg {{ $scoreColor }}">
                                    <i class="bi bi-shield-check"></i>
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
                                        <div class="h-full {{ $barColor }} transition-all duration-1000 ease-out" style="width: {{ $trustScore }}%"></div>
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
                                            {{ $activeLoans }} Unit
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 2. KPI STATS GRID (COMPACT & SEJAJAR) --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 text-left">
                    {{-- Card 1 --}}
                    <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-lg transition-all group flex flex-col justify-between h-full">
                        <div class="flex justify-between items-start mb-2">
                            <div class="w-10 h-10 bg-cyan-50 text-cyan-600 rounded-xl flex items-center justify-center text-lg group-hover:scale-110 transition-transform"><i class="bi bi-box-seam-fill"></i></div>
                            <span class="text-[9px] font-black text-cyan-600 bg-cyan-50 px-2 py-1 rounded-lg uppercase">Aktif</span>
                        </div>
                        <div>
                            <p class="text-2xl font-black text-slate-900">{{ $activeLoans }}</p>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Sedang Dipinjam</p>
                        </div>
                    </div>
                    {{-- Card 2 --}}
                    @php $pendingCount = $recentActivities->where('status', 'pending')->count(); @endphp
                    <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-lg transition-all group flex flex-col justify-between h-full">
                        <div class="flex justify-between items-start mb-2">
                            <div class="w-10 h-10 bg-orange-50 text-orange-600 rounded-xl flex items-center justify-center text-lg group-hover:scale-110 transition-transform"><i class="bi bi-hourglass-split"></i></div>
                            <span class="text-[9px] font-black text-orange-600 bg-orange-50 px-2 py-1 rounded-lg uppercase">Proses</span>
                        </div>
                        <div>
                            <p class="text-2xl font-black text-slate-900">{{ $pendingCount }}</p>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Menunggu Acc</p>
                        </div>
                    </div>
                    {{-- Card 3 --}}
                    <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-lg transition-all group flex flex-col justify-between h-full">
                        <div class="flex justify-between items-start mb-2">
                            <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center text-lg group-hover:scale-110 transition-transform"><i class="bi bi-check-circle-fill"></i></div>
                            <span class="text-[9px] font-black text-emerald-600 bg-emerald-50 px-2 py-1 rounded-lg uppercase">Selesai</span>
                        </div>
                        <div>
                            <p class="text-2xl font-black text-slate-900">{{ $returnedLoans }}</p>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Kembali</p>
                        </div>
                    </div>
                    {{-- Card 4 --}}
                    <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-lg transition-all group flex flex-col justify-between h-full">
                        <div class="flex justify-between items-start mb-2">
                            <div class="w-10 h-10 bg-red-50 text-red-600 rounded-xl flex items-center justify-center text-lg group-hover:scale-110 transition-transform"><i class="bi bi-cash-coin"></i></div>
                            <span class="text-[9px] font-black text-red-600 bg-red-50 px-2 py-1 rounded-lg uppercase">Tagihan</span>
                        </div>
                        <div>
                            <p class="text-xl font-black text-red-600 truncate">Rp {{ number_format($totalFines ?? 0, 0, ',', '.') }}</p>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Denda</p>
                        </div>
                    </div>
                </div>

                {{-- 3. MAIN CONTENT GRID (8:4 Layout) --}}
                <div class="grid lg:grid-cols-12 gap-6 text-left items-stretch">
                    
                    {{-- LEFT: RECENT ACTIVITY --}}
                    <div class="lg:col-span-8 bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden flex flex-col h-full">
                        <div class="p-6 border-b border-slate-50 flex justify-between items-center bg-slate-50/30">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-cyan-50 text-cyan-600 flex items-center justify-center"><i class="bi bi-clock-history"></i></div>
                                <div>
                                    <h3 class="text-xs font-black text-slate-800 uppercase tracking-widest leading-none">Aktivitas Terakhir</h3>
                                </div>
                            </div>
                            <a href="#" class="text-[10px] font-bold text-slate-400 hover:text-cyan-600 uppercase tracking-wider transition-colors">Lihat Semua</a>
                        </div>
                        
                        <div class="overflow-x-auto flex-1">
                            <table class="w-full text-left">
                                <thead class="bg-slate-50/50 text-[9px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-50">
                                    <tr>
                                        <th class="px-6 py-4">Barang</th>
                                        <th class="px-6 py-4">Waktu</th>
                                        <th class="px-6 py-4 text-center">Status</th>
                                        <th class="px-6 py-4 text-right">Kode</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-50 text-sm">
                                    @forelse($recentActivities as $loan)
                                    <tr class="hover:bg-slate-50/50 transition-colors group">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center text-sm group-hover:bg-white group-hover:shadow-sm transition-all">
                                                    <i class="bi bi-box"></i>
                                                </div>
                                                <div>
                                                    <p class="font-bold text-slate-800 text-xs uppercase leading-tight">{{ $loan->item->name }}</p>
                                                    <p class="text-[9px] font-bold text-slate-400 uppercase mt-0.5">{{ $loan->quantity }} Unit</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <p class="text-[10px] font-bold text-slate-500 uppercase">{{ \Carbon\Carbon::parse($loan->created_at)->format('d M') }}</p>
                                            <p class="text-[9px] font-bold text-slate-400">{{ \Carbon\Carbon::parse($loan->created_at)->format('H:i') }} WIB</p>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @php
                                                $statusClass = match($loan->status) {
                                                    'borrowed' => 'bg-yellow-50 text-yellow-600 border-yellow-100',
                                                    'returned' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                                    'pending'  => 'bg-slate-50 text-slate-600 border-slate-200',
                                                    default    => 'bg-red-50 text-red-600 border-red-100'
                                                };
                                                $statusText = match($loan->status) {
                                                    'borrowed' => 'Dipinjam',
                                                    'returned' => 'Selesai',
                                                    'pending'  => 'Menunggu',
                                                    default    => 'Ditolak'
                                                };
                                            @endphp
                                            <span class="px-2.5 py-1 rounded-md text-[9px] font-black uppercase border {{ $statusClass }}">
                                                {{ $statusText }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <span class="font-mono text-[10px] font-bold text-slate-300">#{{ $loan->id }}</span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="py-12 text-center text-slate-400 font-bold text-xs uppercase tracking-widest italic">Belum ada aktivitas.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- RIGHT: PROFILE CARD (COMPACT & ALIGNED) --}}
                    <div class="lg:col-span-4 flex flex-col gap-6 h-full">
                        
                        {{-- Profile Card --}}
                        <div class="bg-white rounded-[2rem] border border-slate-100 shadow-lg relative overflow-hidden group flex-1">
                            {{-- Header --}}
                            <div class="h-20 bg-gradient-to-r from-cyan-500 to-blue-600 relative">
                                <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 8px 8px;"></div>
                            </div>

                            <div class="px-6 pb-6 text-center relative">
                                {{-- Avatar Offset --}}
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

                                {{-- Identity --}}
                                <h3 class="mt-3 text-base font-black text-slate-900 uppercase tracking-tight leading-tight">{{ Auth::user()->name }}</h3>
                                <p class="text-[9px] font-bold text-cyan-600 uppercase tracking-widest mt-0.5 mb-5">Siswa SMKN 1 Ciomas</p>

                                {{-- Details List (Compact) --}}
                                <div class="space-y-2 text-left">
                                    <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50 border border-slate-100 group-hover:border-cyan-100 transition-colors">
                                        <div class="flex items-center gap-2">
                                            <div class="w-6 h-6 rounded-lg bg-white text-cyan-600 flex items-center justify-center text-xs shadow-sm"><i class="bi bi-mortarboard-fill"></i></div>
                                            <span class="text-[9px] font-bold text-slate-500 uppercase">Kelas</span>
                                        </div>
                                        <span class="text-[10px] font-black text-slate-800">{{ Auth::user()->classRoom->name ?? '-' }}</span>
                                    </div>

                                    <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50 border border-slate-100 group-hover:border-cyan-100 transition-colors">
                                        <div class="flex items-center gap-2">
                                            <div class="w-6 h-6 rounded-lg bg-white text-cyan-600 flex items-center justify-center text-xs shadow-sm"><i class="bi bi-upc-scan"></i></div>
                                            <span class="text-[9px] font-bold text-slate-500 uppercase">NISN</span>
                                        </div>
                                        <span class="text-[10px] font-black text-slate-800">{{ Auth::user()->nisn ?? '-' }}</span>
                                    </div>

                                    <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50 border border-slate-100 group-hover:border-cyan-100 transition-colors">
                                        <div class="flex items-center gap-2">
                                            <div class="w-6 h-6 rounded-lg bg-white text-cyan-600 flex items-center justify-center text-xs shadow-sm"><i class="bi bi-envelope-fill"></i></div>
                                            <span class="text-[9px] font-bold text-slate-500 uppercase">Email</span>
                                        </div>
                                        <span class="text-[9px] font-black text-slate-800 truncate w-24 text-right">{{ Auth::user()->email }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Tips Card (Compact) --}}
                        <div class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-[2rem] p-6 text-white shadow-lg relative overflow-hidden flex-none">
                            <div class="relative z-10">
                                <div class="flex items-center gap-2 mb-2">
                                    <div class="w-6 h-6 bg-white/10 rounded-lg flex items-center justify-center backdrop-blur-sm text-xs"><i class="bi bi-lightbulb-fill text-yellow-300"></i></div>
                                    <h3 class="text-[10px] font-black uppercase tracking-widest">Tips Toolman</h3>
                                </div>
                                <p class="text-[10px] text-slate-300 font-medium leading-relaxed italic">
                                    "Cek kondisi barang sebelum meninggalkan ruangan. Kerusakan di luar adalah tanggung jawabmu."
                                </p>
                            </div>
                            <i class="bi bi-quote absolute -right-2 -bottom-2 text-[80px] text-white/5"></i>
                        </div>

                    </div>
                </div>

            </div>
        </main>
    </div>

</body>
</html>