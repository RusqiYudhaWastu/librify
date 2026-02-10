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

<body class="antialiased flex h-screen w-full overflow-hidden text-left" x-data="{ sidebarOpen: false }">

    {{-- Sidebar --}}
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'" 
           class="fixed inset-y-0 left-0 z-50 flex flex-col w-72 bg-slate-950 text-white border-r border-slate-900 md:static md:flex-shrink-0 h-full transition-transform duration-300">
        @include('siswa.partials.sidebar') 
    </aside>

    <div class="flex flex-1 flex-col h-full min-w-0 overflow-hidden">
        {{-- Header --}}
        @include('siswa.partials.header')

        <main class="flex-1 overflow-y-auto p-6 lg:p-10 pt-2 custom-scroll text-left">
            <div class="mx-auto w-full max-w-[1550px] space-y-8 text-left">
                
                {{-- 1. HERO SECTION & TRUST SCORE --}}
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div class="lg:col-span-2 relative overflow-hidden rounded-[3rem] bg-gradient-to-br from-blue-600 to-indigo-700 p-10 shadow-2xl shadow-blue-200">
                        <div class="relative z-10 text-left">
                            <span class="px-4 py-1.5 rounded-full bg-white/20 text-white text-[10px] font-black uppercase tracking-widest backdrop-blur-md">Otoritas Kelas Aktif</span>
                            <h1 class="text-3xl lg:text-4xl font-black text-white font-jakarta mt-4 leading-tight uppercase">
                                {{ $user->name }} 🎓
                            </h1>
                            <p class="text-blue-50/80 font-medium text-sm mt-2 max-w-md">
                                Selamat datang di Pusat Kendali Aset. Pastikan barang kembali tepat waktu untuk menjaga skor kepercayaan kelas.
                            </p>
                            
                            <div class="mt-10 flex flex-wrap gap-4">
                                <a href="{{ route('siswa.request') }}" class="px-8 py-4 bg-white text-blue-700 rounded-2xl font-black text-xs uppercase tracking-widest shadow-xl hover:scale-105 transition-all flex items-center gap-3">
                                    <i class="bi bi-plus-circle-fill text-lg"></i> Booking Alat Baru
                                </a>
                                <a href="{{ route('siswa.laporan') }}" class="px-8 py-4 bg-blue-800/30 text-white border border-blue-400/30 rounded-2xl font-black text-xs uppercase tracking-widest backdrop-blur-sm hover:bg-blue-800/50 transition-all">
                                    Lapor Masalah
                                </a>
                            </div>
                        </div>
                        <div class="absolute -right-16 -bottom-16 h-80 w-80 rounded-full bg-white/10 blur-3xl"></div>
                        <div class="absolute top-10 right-10 opacity-20"><i class="bi bi-mortarboard text-[150px] text-white"></i></div>
                    </div>

                    {{-- Dinamis: Trust Score Card --}}
                    <div class="bg-slate-900 rounded-[3rem] p-10 text-white shadow-2xl flex flex-col justify-between relative overflow-hidden group border border-slate-800">
                        <div class="relative z-10 text-left">
                            <p class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-1">Skor Kepercayaan</p>
                            <h3 class="text-4xl font-black font-jakarta">
                                {{ $trustScore }}<span class="text-lg text-slate-500">/100</span>
                            </h3>
                            <p class="text-xs font-bold mt-2 {{ $trustColor }}">
                                Status: {{ $trustStatus }}
                            </p>
                        </div>
                        <div class="relative z-10 mt-8 space-y-4 text-left">
                            <div class="h-1.5 w-full bg-slate-800 rounded-full overflow-hidden">
                                <div class="h-full bg-blue-500 group-hover:animate-pulse transition-all duration-1000" 
                                     style="width: {{ $trustScore }}%"></div>
                            </div>
                            <p class="text-[10px] text-slate-400 font-medium leading-relaxed">
                                Skor ini dihitung berdasarkan ketepatan waktu pengembalian dan kondisi barang di **SMKN 1 CIOMAS**.
                            </p>
                        </div>
                        <i class="bi bi-shield-check absolute -right-4 top-1/2 -translate-y-1/2 text-[120px] text-white/5"></i>
                    </div>
                </div>

                {{-- 2. KPI STATS GRID (DINAMIS) --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 text-left">
                    <div class="bg-white p-7 rounded-[2.5rem] border border-gray-100 shadow-sm hover:shadow-xl transition-all group">
                        <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center text-2xl mb-5 group-hover:rotate-12 transition-transform"><i class="bi bi-box-fill"></i></div>
                        <p class="text-3xl font-black text-gray-900">{{ $stats['total_items'] }}</p>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-1">Item Aktif</p>
                    </div>
                    <div class="bg-white p-7 rounded-[2.5rem] border border-gray-100 shadow-sm hover:shadow-xl transition-all group">
                        <div class="w-14 h-14 bg-orange-50 text-orange-600 rounded-2xl flex items-center justify-center text-2xl mb-5 group-hover:rotate-12 transition-transform"><i class="bi bi-hourglass-split"></i></div>
                        <p class="text-3xl font-black text-gray-900">{{ $stats['pending_count'] }}</p>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-1">Request Menunggu</p>
                    </div>
                    <div class="bg-white p-7 rounded-[2.5rem] border border-gray-100 shadow-sm hover:shadow-xl transition-all group">
                        <div class="w-14 h-14 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center text-2xl mb-5 group-hover:rotate-12 transition-transform"><i class="bi bi-check-circle"></i></div>
                        <p class="text-3xl font-black text-gray-900">{{ $stats['finished_count'] }}</p>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-1">Selesai Pinjam</p>
                    </div>
                    <div class="bg-white p-7 rounded-[2.5rem] border border-gray-100 shadow-sm hover:shadow-xl transition-all group">
                        <div class="w-14 h-14 bg-red-50 text-red-600 rounded-2xl flex items-center justify-center text-2xl mb-5 group-hover:rotate-12 transition-transform"><i class="bi bi-shield-exclamation"></i></div>
                        <p class="text-3xl font-black text-red-600">{{ $stats['broken_count'] }}</p>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-1">Laporan Kendala</p>
                    </div>
                </div>

                {{-- 3. CONTENT GRID --}}
                <div class="grid lg:grid-cols-12 gap-8 text-left">
                    
                    {{-- ACTIVE INVENTORY (LOOPING DARI CONTROLLER) --}}
                    <div class="lg:col-span-8 space-y-8">
                        <div class="bg-white rounded-[3rem] border border-gray-100 shadow-sm overflow-hidden">
                            <div class="p-8 border-b border-gray-50 flex items-center justify-between bg-gray-50/30">
                                <div class="text-left">
                                    <h3 class="font-black text-gray-800 font-jakarta uppercase text-xs tracking-widest leading-none">Aset Kelas Saat Ini</h3>
                                    <p class="text-[10px] text-gray-400 font-bold mt-2 uppercase">Wajib dikembalikan sebelum Sesi jam praktek berakhir.</p>
                                </div>
                                <span class="px-4 py-1.5 bg-blue-50 text-blue-600 rounded-full text-[9px] font-black uppercase tracking-widest border border-blue-100 animate-pulse">Sesi Aktif</span>
                            </div>
                            <div class="p-4 overflow-x-auto">
                                <table class="w-full text-left text-sm">
                                    <thead class="text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                        <tr>
                                            <th class="px-6 py-4">Item Praktikum</th>
                                            <th class="px-6 py-4">Jumlah</th>
                                            <th class="px-6 py-4">Waktu Pinjam</th>
                                            <th class="px-6 py-4 text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-50">
                                        @forelse($activeLoans as $loan)
                                        <tr class="group hover:bg-gray-50 transition-all">
                                            <td class="px-6 py-5 text-left">
                                                <div class="flex items-center gap-4">
                                                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-lg"><i class="bi bi-laptop"></i></div>
                                                    <div class="flex flex-col">
                                                        <span class="font-black text-gray-900 leading-tight">{{ $loan->item->name }}</span>
                                                        <span class="text-[10px] text-gray-400 font-bold uppercase">{{ $loan->item->asset_code }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-5">
                                                <span class="font-black text-blue-600 text-lg">{{ $loan->quantity }} <span class="text-[10px] uppercase text-gray-400">Unit</span></span>
                                            </td>
                                            <td class="px-6 py-5">
                                                <div class="flex flex-col">
                                                    <span class="font-bold text-gray-700">{{ $loan->updated_at->format('H:i') }} WIB</span>
                                                    <span class="text-[9px] text-emerald-500 font-black uppercase tracking-tighter">{{ $loan->updated_at->format('d M Y') }}</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-5 text-center">
                                                <span class="px-3 py-1 bg-blue-100 text-blue-600 rounded-xl text-[9px] font-black uppercase tracking-widest">DIPAKAI</span>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="py-12 text-center text-gray-400 font-bold uppercase text-[10px] tracking-widest">Tidak ada barang yang sedang dipinjam.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- TIMELINE AKTIVITAS (DINAMIS) --}}
                        <div class="bg-white rounded-[3rem] p-10 border border-gray-100 shadow-sm text-left">
                            <h3 class="text-sm font-black font-jakarta text-gray-800 mb-8 uppercase tracking-widest leading-none">Riwayat Aktivitas Kelas</h3>
                            <div class="space-y-8">
                                @foreach($recentActivities as $activity)
                                <div class="flex gap-6 relative">
                                    @if(!$loop->last) <div class="absolute left-5 top-10 bottom-0 w-0.5 bg-gray-100"></div> @endif
                                    
                                    @php
                                        $bgColor = match($activity->status) {
                                            'pending'  => 'bg-orange-50 text-orange-600',
                                            'approved' => 'bg-blue-50 text-blue-600',
                                            'returned' => 'bg-emerald-50 text-emerald-600',
                                            'rejected' => 'bg-red-50 text-red-600',
                                            default    => 'bg-slate-50 text-slate-600'
                                        };
                                        $icon = match($activity->status) {
                                            'pending'  => 'bi-hourglass-split',
                                            'approved' => 'bi-box-arrow-up',
                                            'returned' => 'bi-arrow-return-left',
                                            'rejected' => 'bi-x-circle',
                                            default    => 'bi-info-circle'
                                        };
                                    @endphp

                                    <div class="w-10 h-10 rounded-2xl {{ $bgColor }} flex items-center justify-center flex-shrink-0 z-10 shadow-sm"><i class="bi {{ $icon }}"></i></div>
                                    <div class="text-left">
                                        <p class="text-xs font-black text-gray-900 leading-tight">
                                            {{ ucfirst($activity->status) }}: {{ $activity->item->name }} ({{ $activity->quantity }} Unit)
                                        </p>
                                        <p class="text-[10px] text-gray-400 font-bold mt-1 uppercase">{{ $activity->created_at->diffForHumans() }} • SMKN 1 Ciomas</p>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- SIDEBAR INFO (RIGHT) --}}
                    <div class="lg:col-span-4 space-y-8">
                        {{-- Otoritas Card --}}
                        <div class="bg-slate-900 rounded-[3rem] p-10 text-white shadow-2xl relative overflow-hidden text-left border border-slate-800">
                            <h3 class="text-xl font-bold font-jakarta mb-8 relative z-10 leading-none">Otoritas Kelas</h3>
                            <div class="space-y-4 relative z-10">
                                <div class="flex items-center gap-4 p-5 bg-white/5 rounded-2xl border border-white/5 group hover:bg-blue-600 transition-all">
                                    <div class="w-12 h-12 rounded-xl bg-blue-500 flex items-center justify-center font-black text-lg shadow-lg">K</div>
                                    <div class="text-left leading-none">
                                        <p class="text-[10px] font-black text-slate-500 group-hover:text-blue-200 uppercase tracking-widest mb-1">Ketua Kelas</p>
                                        <p class="text-sm font-bold">{{ $user->chairman_name ?? '-' }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-4 p-5 bg-white/5 rounded-2xl border border-white/5 group hover:bg-indigo-600 transition-all">
                                    <div class="w-12 h-12 rounded-xl bg-indigo-500 flex items-center justify-center font-black text-lg shadow-lg">W</div>
                                    <div class="text-left leading-none">
                                        <p class="text-[10px] font-black text-slate-500 group-hover:text-indigo-200 uppercase tracking-widest mb-1">Wakil Ketua</p>
                                        <p class="text-sm font-bold">{{ $user->vice_chairman_name ?? '-' }}</p>
                                    </div>
                                </div>
                            </div>
                            <i class="bi bi-shield-shaded absolute -right-8 -bottom-8 text-[150px] text-white/5"></i>
                        </div>

                        {{-- Tips Card --}}
                        <div class="bg-white rounded-[3rem] p-8 border border-gray-100 shadow-sm text-left">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-8 h-8 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center shadow-inner"><i class="bi bi-lightbulb-fill"></i></div>
                                <h3 class="text-xs font-black text-gray-800 uppercase tracking-widest leading-none">Tips Toolman</h3>
                            </div>
                            <p class="text-[11px] text-gray-500 font-medium leading-relaxed italic">
                                "Selalu foto kondisi barang pas awal minjem, bro. Biar kalau ada lecet yang bukan salah kelas kita, ada buktinya pas balikin ke gudang."
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>

</body>
</html>