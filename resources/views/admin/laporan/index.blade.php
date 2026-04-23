<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laporan Sirkulasi - Librify Admin</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
        .font-jakarta { font-family: 'Plus Jakarta Sans', sans-serif; }
        .custom-scroll::-webkit-scrollbar { width: 5px; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #4f46e5; border-radius: 20px; }
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        .glass-card { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.5); }
        
        /* Custom UI Calendar Admin (Indigo Theme) */
        input[type="date"]::-webkit-calendar-picker-indicator {
            background-color: #e0e7ff;
            color: #4f46e5;
            padding: 5px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s;
        }
        input[type="date"]::-webkit-calendar-picker-indicator:hover {
            background-color: #c7d2fe;
        }
    </style>
</head>

<body class="antialiased flex h-screen w-full overflow-hidden text-left font-jakarta" 
      x-data="{ 
        sidebarOpen: false, 
        activeTab: 'sirkulasi', 
        searchQuery: '', 
        modalDetail: false,
        viewType: 'log', 
        
        // Data Dinamis untuk Modal
        selectedData: { 
            id: '', item: '', user: '', kelas: '', date: '', status: '', asset_code: '',
            return_date: '', condition: '', rating: 0, admin_note: '', total_denda: 0, lost_qty: 0,
            desc: '', feedback: '', status_raw: '', photo: '', user_rating: '',
            is_paket: false, items: [],
            durasi: '', tenggat: '', severity: '', has_incident: false
        }
      }">

    {{-- Sidebar Admin --}}
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'" 
           class="fixed inset-y-0 left-0 z-50 flex flex-col w-72 bg-[#0F172A] text-white border-r border-slate-800 md:static md:flex-shrink-0 h-full transition-transform duration-300">
        @include('admin.partials.sidebar')
    </aside>

    <div class="flex flex-1 flex-col h-full min-w-0 overflow-hidden text-left">
        @include('admin.partials.header')

        <main class="flex-1 overflow-y-auto p-6 lg:p-10 pt-2 custom-scroll">
            <div class="mx-auto w-full max-w-[1550px] space-y-8">
                
                {{-- Alert System --}}
                @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" class="bg-emerald-500 text-white px-6 py-4 rounded-[2rem] shadow-lg flex justify-between items-center transition-all relative z-50">
                    <span class="font-bold text-sm uppercase tracking-widest"><i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}</span>
                    <button @click="show = false"><i class="bi bi-x-lg"></i></button>
                </div>
                @endif

                @if($errors->any())
                <div class="bg-red-500 text-white px-6 py-4 rounded-[2rem] shadow-lg relative z-50 mb-4">
                    <ul class="list-disc list-inside text-sm font-bold">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                {{-- 1. HEADER & FILTER FORM --}}
                <div class="space-y-6">
                    <div class="text-left leading-none">
                        <h2 class="text-4xl font-black text-slate-900 tracking-tight uppercase leading-none">Pusat Laporan Sirkulasi</h2>
                        <p class="text-sm font-bold text-indigo-500 mt-3 uppercase tracking-widest leading-none border-l-4 border-indigo-500 pl-4">Periode Laporan: {{ $summary['period'] }}</p>
                    </div>

                    <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm leading-none">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 items-end">
                            
                            {{-- Filter Date --}}
                            <form action="{{ route('admin.laporan') }}" method="GET" class="contents">
                                <div class="flex flex-col gap-3 lg:col-span-1">
                                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Mulai Tanggal</label>
                                    <input type="date" name="start_date" value="{{ request('start_date', now()->startOfMonth()->format('Y-m-d')) }}" 
                                           class="w-full px-5 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl font-bold text-xs outline-none focus:ring-4 focus:ring-indigo-500/10 transition-all text-gray-700">
                                </div>

                                <div class="flex flex-col gap-3 lg:col-span-1">
                                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Sampai Tanggal</label>
                                    <input type="date" name="end_date" value="{{ request('end_date', now()->endOfMonth()->format('Y-m-d')) }}" 
                                           class="w-full px-5 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl font-bold text-xs outline-none focus:ring-4 focus:ring-indigo-500/10 transition-all text-gray-700">
                                </div>
                                
                                <div class="lg:col-span-1">
                                    <button type="submit" class="w-full bg-slate-900 text-white rounded-2xl py-3.5 px-4 font-black text-[10px] uppercase tracking-widest hover:bg-indigo-600 transition-all shadow-xl active:scale-95 flex items-center justify-center gap-1.5">
                                        <i class="bi bi-filter text-xs"></i> Terapkan
                                    </button>
                                </div>
                            </form>

                            {{-- Live Search --}}
                            <div class="flex flex-col gap-3 lg:col-span-2">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Pencarian Cepat</label>
                                <div class="relative">
                                    <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-indigo-500"></i>
                                    <input type="text" x-model="searchQuery" placeholder="Cari nama member, buku..." 
                                           class="w-full pl-11 pr-4 py-3.5 bg-indigo-50/30 border border-indigo-100 rounded-2xl font-bold text-xs outline-none focus:ring-4 focus:ring-indigo-500/10 transition-all placeholder:text-indigo-700/50 text-gray-700">
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- 2. ANALYTICS CARDS --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 leading-none text-left">
                    <div class="bg-white p-7 rounded-[2.5rem] border border-gray-100 shadow-sm flex items-center gap-5">
                        <div class="w-14 h-14 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center text-2xl shadow-inner flex-shrink-0"><i class="bi bi-journal-text"></i></div>
                        <div class="text-left overflow-hidden">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 leading-none truncate">Total Sirkulasi</p>
                            <p class="text-3xl font-black text-gray-900 leading-none truncate">{{ $summary['total'] }}</p>
                        </div>
                    </div>
                    <div class="bg-white p-7 rounded-[2.5rem] border border-gray-100 shadow-sm flex items-center gap-5">
                        <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center text-2xl shadow-inner flex-shrink-0"><i class="bi bi-arrow-repeat"></i></div>
                        <div class="text-left overflow-hidden">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 leading-none truncate">Sedang Dipinjam</p>
                            <p class="text-3xl font-black text-blue-600 leading-none truncate">{{ $summary['active'] }}</p>
                        </div>
                    </div>
                    <div class="bg-white p-7 rounded-[2.5rem] border border-gray-100 shadow-sm flex items-center gap-5">
                        <div class="w-14 h-14 bg-red-50 text-red-600 rounded-2xl flex items-center justify-center text-2xl shadow-inner flex-shrink-0"><i class="bi bi-exclamation-triangle"></i></div>
                        <div class="text-left overflow-hidden">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 leading-none truncate">Buku Rusak</p>
                            <p class="text-3xl font-black text-red-600 leading-none truncate">{{ $summary['broken'] }}</p>
                        </div>
                    </div>
                    <div class="bg-white p-7 rounded-[2.5rem] border border-gray-100 shadow-sm flex items-center gap-5">
                        <div class="w-14 h-14 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center text-2xl shadow-inner flex-shrink-0"><i class="bi bi-patch-check"></i></div>
                        <div class="text-left overflow-hidden">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 leading-none truncate">Telah Selesai</p>
                            <p class="text-3xl font-black text-emerald-600 leading-none truncate">{{ $summary['done'] }}</p>
                        </div>
                    </div>
                    <div class="bg-slate-900 p-7 rounded-[2.5rem] shadow-xl flex items-center gap-5 text-white">
                        <div class="w-14 h-14 bg-white/10 text-emerald-400 rounded-2xl flex items-center justify-center text-2xl shadow-inner flex-shrink-0"><i class="bi bi-cash-stack"></i></div>
                        <div class="text-left overflow-hidden">
                            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5 leading-none truncate">Total Denda</p>
                            <p class="text-xl font-black text-white leading-none truncate w-full">Rp {{ number_format($summary['total_fines'], 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>

                {{-- 3. TAB SWITCHER --}}
                <div class="w-full overflow-x-auto hide-scrollbar -mx-4 px-4 sm:mx-0 sm:px-0">
                    <div class="flex bg-gray-200/50 p-1.5 rounded-2xl gap-1 w-max sm:w-fit border border-gray-100 relative z-10 flex-nowrap">
                        <button @click="activeTab = 'sirkulasi'; searchQuery = ''" :class="activeTab === 'sirkulasi' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-indigo-500'" class="px-6 py-2.5 sm:py-3 rounded-xl text-[9px] sm:text-[10px] font-black uppercase tracking-widest transition-all whitespace-nowrap">Riwayat Sirkulasi</button>
                        <button @click="activeTab = 'kendala'; searchQuery = ''" :class="activeTab === 'kendala' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-indigo-500'" class="px-6 py-2.5 sm:py-3 rounded-xl text-[9px] sm:text-[10px] font-black uppercase tracking-widest transition-all flex items-center gap-1.5 whitespace-nowrap">
                            Laporan Member
                            @if(isset($summary['pending_problems']) && $summary['pending_problems'] > 0)
                                <span class="bg-red-500 text-white px-1.5 py-0.5 rounded-md text-[7px] sm:text-[8px] animate-pulse">{{ $summary['pending_problems'] }}</span>
                            @endif
                        </button>
                    </div>
                </div>

                {{-- 4. CONTENT AREA --}}
                <div class="rounded-[3rem] bg-white shadow-sm border border-gray-100 overflow-hidden text-left flex flex-col min-h-[500px]">
                    
                    {{-- HEADER TABEL & CETAK PDF --}}
                    <div class="p-6 lg:p-8 border-b border-gray-50 flex flex-col sm:flex-row items-start sm:items-center justify-between bg-gray-50/50 gap-3">
                        <h3 class="text-[9px] sm:text-[10px] font-black text-gray-800 uppercase tracking-[0.2em]"><i class="bi bi-list-check me-1.5 sm:me-2 text-indigo-600"></i> <span x-text="activeTab === 'sirkulasi' ? 'Arsip Sirkulasi Perpustakaan' : 'Daftar Laporan Kendala Buku'"></span></h3>
                        <a href="{{ route('admin.laporan.export', request()->all()) }}" class="w-full sm:w-auto px-5 py-2 sm:py-2.5 bg-indigo-600 text-white rounded-xl text-[9px] lg:text-[10px] font-black uppercase tracking-widest shadow-md hover:bg-indigo-700 transition-all flex items-center justify-center gap-1.5 sm:gap-2 active:scale-95">
                            <i class="bi bi-printer-fill text-xs sm:text-sm"></i> Cetak Laporan PDF
                        </a>
                    </div>
                    
                    {{-- ✅ SLIDE 1: TABEL SIRKULASI (GROUPED MULTIPLE ITEMS) --}}
                    <div x-show="activeTab === 'sirkulasi'" x-transition class="flex-1 flex flex-col overflow-hidden">
                        <div class="overflow-x-auto flex-1 custom-scroll">
                            <table class="w-full text-left text-xs lg:text-sm min-w-[750px]">
                                <thead class="sticky top-0 bg-white z-10">
                                    <tr class="bg-gray-50/80 text-[8px] lg:text-[9px] uppercase tracking-[0.2em] text-gray-400 font-black border-b border-gray-100 leading-none">
                                        <th class="px-6 lg:px-8 py-4 lg:py-7">Waktu</th>
                                        <th class="px-6 lg:px-8 py-4 lg:py-7">Identitas Peminjam</th>
                                        <th class="px-6 lg:px-8 py-4 lg:py-7 text-left">Paket Buku</th>
                                        <th class="px-6 lg:px-8 py-4 lg:py-7">Status Final</th>
                                        <th class="px-6 lg:px-8 py-4 lg:py-7 text-center">Detail</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50 leading-tight">
                                    @php
                                        $groupedLogs = $logs->groupBy(function($item) {
                                            return $item->user_id . '|' . $item->created_at->format('Y-m-d H:i') . '|' . $item->status;
                                        });
                                    @endphp

                                    @forelse($groupedLogs as $groupKey => $group)
                                    @php
                                        $first = $group->first();
                                        $itemCount = $group->count();
                                        $totalQty = $group->sum('quantity');
                                        
                                        $unit = $first->duration_unit == 'hours' ? 'hours' : 'days';
                                        $deadline = $first->created_at->copy()->add($unit, $first->duration_amount);
                                        $isOverdue = now() > $deadline && in_array($first->status, ['approved', 'borrowed']);
                                        
                                        $titleName = $itemCount > 1 ? 'Paket Peminjaman' : $first->item->name;
                                        $subName = $itemCount > 1 ? $itemCount . ' Judul Buku' : 'QTY: ' . $first->quantity . ' Buku';
                                        $iconBox = $itemCount > 1 ? 'bi-journals' : 'bi-book';
                                        $loanCode = $first->loan_code ?? '-';
                                        
                                        $hasIncident = $group->contains(function ($val) {
                                            return in_array($val->return_condition, ['rusak', 'hilang']) || $val->lost_quantity > 0 || $val->fine_amount > 0;
                                        });

                                        $searchString = strtolower($first->user->name . ' ' . $group->pluck('item.name')->implode(' '));
                                    @endphp
                                    <tr x-show="searchQuery === '' || '{{ $searchString }}'.includes(searchQuery.toLowerCase())"
                                        class="group hover:bg-indigo-50/30 transition-all duration-200">
                                        <td class="px-6 lg:px-8 py-4 lg:py-6 text-left">
                                            <div class="flex flex-col text-left">
                                                <span class="text-gray-900 font-black uppercase text-[10px] lg:text-[11px] mb-1 sm:mb-1.5 whitespace-nowrap">{{ $first->created_at->format('d M Y') }}</span>
                                                <span class="text-[9px] lg:text-[10px] text-gray-400 font-bold uppercase tracking-widest whitespace-nowrap">{{ $first->created_at->format('H:i') }} WIB</span>
                                            </div>
                                        </td>
                                        <td class="px-6 lg:px-8 py-4 lg:py-6">
                                            <div class="flex flex-col text-left leading-none">
                                                <span class="text-gray-900 font-black uppercase text-[11px] lg:text-sm tracking-tight mb-1.5 truncate max-w-[150px] lg:max-w-xs" title="{{ $first->user->name }}">{{ $first->user->name }}</span>
                                                <div class="flex flex-wrap gap-1.5 lg:gap-2 items-center">
                                                    @if($first->user->role === 'student')
                                                        <span class="text-[8px] lg:text-[9px] font-black text-cyan-600 bg-cyan-50 px-1.5 lg:px-2 py-0.5 rounded uppercase border border-cyan-100 w-fit text-left">Siswa</span>
                                                        <span class="text-[8px] lg:text-[9px] font-black text-slate-500 bg-slate-100 px-1.5 lg:px-2 py-0.5 rounded uppercase w-fit border border-slate-200 truncate max-w-[80px] lg:max-w-[100px]">{{ $first->user->classRoom->name ?? 'Member' }}</span>
                                                    @else
                                                        <span class="text-[8px] lg:text-[9px] font-black text-purple-600 bg-purple-50 px-1.5 lg:px-2 py-0.5 rounded uppercase border border-purple-100 w-fit text-left">Akun Kelas</span>
                                                    @endif
                                                    
                                                    @if($first->user_rating > 0)
                                                        @php
                                                            $rateColor = $first->user_rating >= 4.5 ? 'text-emerald-600 bg-emerald-50 border-emerald-100' : 
                                                                        ($first->user_rating >= 3.0 ? 'text-orange-600 bg-orange-50 border-orange-100' : 'text-red-600 bg-red-50 border-red-100');
                                                        @endphp
                                                        <span class="text-[8px] lg:text-[9px] font-black {{ $rateColor }} px-1.5 lg:px-2 py-0.5 rounded uppercase border flex items-center gap-1 shadow-sm whitespace-nowrap">
                                                            <i class="bi bi-star-fill text-[7px] lg:text-[8px]"></i> {{ number_format($first->user_rating, 1) }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 lg:px-8 py-4 lg:py-6 text-left">
                                            <div class="flex items-center gap-3 lg:gap-4">
                                                <div class="w-10 h-10 lg:w-11 lg:h-11 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-black text-sm shadow-inner border border-indigo-100 flex-shrink-0"><i class="{{ $iconBox }}"></i></div>
                                                <div class="flex flex-col text-left overflow-hidden">
                                                    <span class="text-gray-800 font-black uppercase tracking-tight text-[11px] lg:text-sm truncate max-w-[150px] lg:max-w-[180px]">{{ $titleName }}</span>
                                                    <span class="text-[9px] lg:text-[10px] text-gray-400 font-bold mt-1 uppercase tracking-widest">{{ $subName }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        
                                        <td class="px-6 lg:px-8 py-4 lg:py-6">
                                            @php
                                                $badgeClass = match(true) {
                                                    $first->status === 'returned' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                                    $first->status === 'rejected' => 'bg-red-50 text-red-600 border-red-100',
                                                    $first->status === 'pending' => 'bg-orange-50 text-orange-600 border-orange-100',
                                                    default => 'bg-blue-50 text-blue-600 border-blue-100'
                                                };
                                                $statusTextLabel = match($first->status) {
                                                    'returned' => 'SELESAI',
                                                    'rejected' => 'DITOLAK',
                                                    'pending' => 'MENUNGGU',
                                                    default => 'DIPINJAM'
                                                };
                                            @endphp
                                            <div class="flex flex-col items-start gap-1.5 sm:gap-2">
                                                <span class="inline-flex items-center px-2.5 sm:px-3 py-1 sm:py-1.5 rounded-md lg:rounded-lg text-[8px] lg:text-[9px] font-black uppercase tracking-widest border shadow-sm whitespace-nowrap {{ $badgeClass }}">
                                                    {{ $statusTextLabel }}
                                                </span>
                                                @if($first->status === 'returned' && $hasIncident)
                                                    <span class="text-[7px] sm:text-[8px] font-black text-red-500 bg-red-50 border border-red-100 px-1.5 sm:px-2 py-0.5 rounded uppercase tracking-widest flex items-center gap-1 whitespace-nowrap">
                                                        <i class="bi bi-exclamation-triangle-fill"></i> Ada Insiden
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 lg:px-8 py-4 lg:py-6 text-center">
                                            <button type="button" @click='viewType = "log"; selectedData = {
                                                is_paket: {{ $itemCount > 1 ? 'true' : 'false' }},
                                                items: {{ $group->map(fn($l) => [
                                                    "barang" => $l->item->name, 
                                                    "jumlah" => $l->quantity, 
                                                    "kode" => $l->item->asset_code,
                                                    "kondisi" => $l->return_condition,
                                                    "denda" => $l->fine_amount,
                                                    "hilang" => $l->lost_quantity,
                                                    "note_rusak" => $l->return_note
                                                ])->toJson() }},
                                                user: @json($first->user->name),
                                                tipe: "{{ $first->user->role === 'student' ? 'Siswa Individu' : 'Akun Kelas' }}",
                                                kelas: @json($first->user->classRoom->name ?? "-"),
                                                user_rating: @json($first->user_rating ?? 0),
                                                date: "{{ $first->created_at->format("d M Y, H:i") }}",
                                                durasi: "{{ $first->duration_amount }} {{ $first->duration_unit == "hours" ? "Jam" : "Hari" }}",
                                                tenggat: "{{ $deadline->format("d M Y, H:i") }}",
                                                return_date: "{{ $first->return_date ? \Carbon\Carbon::parse($first->return_date)->format("d M Y, H:i") : "-" }}",
                                                status: "{{ $statusTextLabel }}",
                                                status_raw: "{{ $first->status }}",
                                                has_incident: {{ $hasIncident ? 'true' : 'false' }},
                                                admin_note: @json($first->admin_note ?? "Tidak ada catatan feedback."),
                                                total_denda: "{{ number_format($group->sum("fine_amount"), 0, ",", ".") }}",
                                                item: @json($titleName),
                                                asset_code: @json($itemCount > 1 ? $itemCount." Buku" : $first->item->asset_code)
                                            }; modalDetail = true' 
                                            class="w-9 h-9 lg:w-10 lg:h-10 rounded-xl bg-gray-50 text-gray-400 hover:bg-indigo-50 hover:text-indigo-600 transition-all mx-auto flex items-center justify-center shadow-sm border border-gray-100">
                                                <i class="bi bi-eye-fill text-sm"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="5" class="py-24 text-center text-gray-300 font-bold uppercase text-[9px] lg:text-[10px] tracking-[0.3em] italic">Belum ada riwayat terekam di sistem.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- SLIDE 2: DAFTAR KENDALA MEMBER --}}
                    <div x-show="activeTab === 'kendala'" x-transition x-cloak class="flex-1 p-6 lg:p-8 text-left bg-slate-50/30 overflow-y-auto custom-scroll min-h-[400px]">
                        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5 lg:gap-6">
                            @forelse($incomingProblems as $problem)
                            <div x-show="searchQuery === '' || '{{ strtolower($problem->user->name) }}'.includes(searchQuery.toLowerCase()) || '{{ strtolower($problem->item->name) }}'.includes(searchQuery.toLowerCase())"
                                 class="p-6 rounded-[2rem] border border-gray-100 bg-white hover:border-indigo-200 hover:shadow-lg transition-all relative overflow-hidden group text-left h-full flex flex-col justify-between min-h-[280px] sm:min-h-[300px]">
                                
                                {{-- Status Color --}}
                                <div class="absolute top-0 left-0 w-1.5 sm:w-2 h-full {{ $problem->status === 'fixed' ? 'bg-emerald-500' : ($problem->status === 'checked' ? 'bg-blue-500' : 'bg-orange-500') }}"></div>
                                
                                {{-- Tombol Detail --}}
                                <div class="absolute top-5 right-5 z-20">
                                    <button type="button" @click='viewType = "report"; selectedData = {
                                        id: "{{ $problem->id }}",
                                        item: @json($problem->item->name),
                                        user: @json($problem->user->name),
                                        kelas: @json($problem->user->classRoom->name ?? "-"),
                                        user_rating: @json($problem->user_rating ?? 0),
                                        date: "{{ $problem->created_at->format("d M Y, H:i") }}",
                                        status: "{{ $problem->status === 'pending' ? 'MENUNGGU' : ($problem->status === 'checked' ? 'DIPROSES' : 'SELESAI') }}",
                                        severity: "{{ $problem->severity ?? 'Ringan' }}", 
                                        desc: @json($problem->description),
                                        feedback: @json($problem->admin_note ?? "Belum ada catatan."),
                                        asset_code: @json($problem->item->asset_code),
                                        status_raw: "{{ $problem->status }}",
                                        photo: @json($problem->photo_path ? asset("storage/".$problem->photo_path) : null)
                                    }; modalDetail = true'
                                    class="w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-white border border-gray-100 text-gray-400 hover:text-indigo-600 hover:border-indigo-100 flex items-center justify-center shadow-sm transition-all cursor-pointer">
                                        <i class="bi bi-eye-fill text-[10px] sm:text-xs"></i>
                                    </button>
                                </div>

                                {{-- Top Content --}}
                                <div class="flex-1 pr-10 sm:pr-12 mb-4 relative z-10 pl-2">
                                    <div class="mb-4">
                                        <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1.5 leading-none text-left">{{ $problem->created_at->diffForHumans() }}</p>
                                        <h4 class="text-xs sm:text-sm font-black text-slate-900 uppercase leading-tight text-left truncate w-full" title="{{ $problem->item->name }}">{{ $problem->item->name }}</h4>
                                        <div class="flex flex-col sm:flex-row sm:items-center gap-1.5 sm:gap-2 mt-1.5">
                                            <p class="text-[9px] font-black text-indigo-500 uppercase text-left truncate max-w-[120px]">{{ $problem->user->name }}</p>
                                            
                                            {{-- Label Tingkat Keparahan --}}
                                            @if(isset($problem->severity))
                                                @php
                                                    $sevClass = match($problem->severity) {
                                                        'Ringan' => 'bg-yellow-50 text-yellow-600 border-yellow-100',
                                                        'Sedang' => 'bg-orange-50 text-orange-600 border-orange-100',
                                                        'Parah'  => 'bg-red-50 text-red-600 border-red-100',
                                                        default  => 'bg-gray-50 text-gray-500 border-gray-200'
                                                    };
                                                @endphp
                                                <span class="px-1.5 sm:px-2 py-0.5 rounded text-[7px] font-black uppercase border shadow-sm leading-none w-fit {{ $sevClass }}">
                                                    {{ $problem->severity }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="p-4 bg-gray-50 rounded-[1.5rem] border border-gray-100 mb-4 shadow-inner text-left leading-none h-24 overflow-hidden relative">
                                        <p class="text-[8px] font-black text-red-500 uppercase mb-2 tracking-widest leading-none text-left"><i class="bi bi-exclamation-triangle-fill me-1"></i> Keluhan Member:</p>
                                        <p class="text-[10px] sm:text-[11px] text-gray-600 font-medium italic leading-relaxed text-left line-clamp-3">"{{ $problem->description }}"</p>
                                        
                                        @if($problem->photo_path)
                                            <div class="absolute bottom-2 right-2 text-red-400 text-[10px] sm:text-xs flex items-center gap-1 bg-red-50 px-2 py-0.5 rounded border border-red-100 shadow-sm">
                                                <i class="bi bi-camera-fill text-[8px] sm:text-[10px]"></i> <span class="text-[7px] font-black uppercase">Ada Bukti</span>
                                            </div>
                                        @endif
                                        <div class="absolute bottom-0 left-0 w-full h-4 bg-gradient-to-t from-gray-50 to-transparent"></div>
                                    </div>
                                </div>
                                
                                {{-- Bottom Action --}}
                                <div class="mt-auto relative z-10 border-t border-slate-100 pt-4">
                                    <div class="flex items-center gap-3 text-left leading-none">
                                        <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shadow-sm border border-indigo-100"><i class="bi bi-person-badge-fill text-xs"></i></div>
                                        <div class="text-left leading-none">
                                            <p class="text-[8px] font-black text-gray-400 uppercase leading-none mb-1 text-left tracking-widest">Status Laporan</p>
                                            <p class="text-[10px] font-black text-slate-800 uppercase tracking-widest text-left truncate max-w-[150px]" 
                                               :class="{
                                                   'text-orange-500': '{{ $problem->status }}' === 'pending',
                                                   'text-blue-500': '{{ $problem->status }}' === 'checked',
                                                   'text-emerald-500': '{{ $problem->status }}' === 'fixed'
                                               }">{{ strtoupper($problem->status) }}</p>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            @empty
                            <div class="col-span-full py-20 text-center leading-none text-gray-400 italic">
                                Belum ada laporan kendala yang masuk.
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    {{-- 👁️ MODAL DETAIL LENGKAP PINTAR (LOG SIRKULASI & REPORT KENDALA) --}}
    <div x-show="modalDetail" x-cloak class="fixed inset-0 z-[120] flex items-center justify-center p-4 text-left">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-md" @click="modalDetail = false"></div>
        <div x-show="modalDetail" x-transition.scale.95 class="relative w-full max-w-lg bg-white rounded-3xl sm:rounded-[3rem] shadow-2xl p-8 lg:p-10 border border-white flex flex-col max-h-[90vh] text-left leading-none overflow-y-auto custom-scroll mt-auto sm:mt-0">
            
            {{-- HEADER BESAR STATIS --}}
            <div class="flex justify-between items-start mb-6 text-left gap-3 border-b border-gray-100 pb-5 sm:pb-6">
                <div class="text-left overflow-hidden">
                    <h3 class="text-2xl lg:text-3xl font-black text-gray-900 font-jakarta uppercase tracking-tight leading-tight truncate" 
                        x-text="viewType === 'log' ? 'DETAIL TRANSAKSI' : 'DETAIL LAPORAN'"></h3>
                </div>
                <button @click="modalDetail = false" class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-50 transition-colors flex-shrink-0"><i class="bi bi-x-lg text-sm"></i></button>
            </div>

            <div class="space-y-6 text-left leading-none">
                
                {{-- Info Umum User --}}
                <div class="flex items-center gap-4 p-4 sm:p-5 bg-slate-50/80 rounded-2xl border border-slate-100 shadow-sm hover:border-indigo-100 transition-colors mb-4">
                    <div class="w-10 h-10 lg:w-12 lg:h-12 rounded-xl bg-white flex items-center justify-center shadow-sm text-slate-500 border border-slate-100 flex-shrink-0"><i class="bi bi-person-badge-fill text-base lg:text-lg"></i></div>
                    <div class="flex-1 overflow-hidden leading-tight">
                        <p class="text-[8px] lg:text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1 lg:mb-1.5">Peminjam / Pelapor</p>
                        <p class="text-sm lg:text-base font-black text-slate-800 uppercase truncate max-w-full" x-text="selectedData.user"></p>
                        
                        <div class="flex flex-wrap items-center gap-2 mt-1.5">
                            <span class="text-[8px] lg:text-[9px] font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded border border-indigo-100 uppercase tracking-widest truncate max-w-[150px]" x-text="selectedData.tipe + ' (' + selectedData.kelas + ')'"></span>
                            
                            <template x-if="selectedData.user_rating > 0">
                                <span class="text-[8px] lg:text-[9px] font-black text-white bg-indigo-600 px-2 py-0.5 rounded flex items-center gap-1 shadow-sm whitespace-nowrap">
                                    <i class="bi bi-star-fill text-[7px] text-yellow-300"></i> <span x-text="parseFloat(selectedData.user_rating).toFixed(1)"></span>
                                </span>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- A. TAMPILAN KHUSUS: LOG SIRKULASI --}}
                <template x-if="viewType === 'log'">
                    <div class="space-y-4">
                        
                        {{-- Status Peminjaman Box (Full Width) --}}
                        <div class="p-5 bg-slate-50 rounded-2xl border border-slate-100 flex flex-col justify-center shadow-sm">
                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Status Peminjaman</p>
                            <div class="flex items-center gap-2">
                                <p class="text-xs font-black uppercase" 
                                   :class="{
                                       'text-orange-600': selectedData.status_raw === 'pending',
                                       'text-indigo-600': selectedData.status_raw === 'approved' || selectedData.status_raw === 'borrowed',
                                       'text-emerald-600': selectedData.status_raw === 'returned',
                                       'text-red-600': selectedData.status_raw === 'rejected'
                                   }"
                                   x-text="selectedData.status"></p>
                                
                                {{-- BADGE INSIDEN --}}
                                <template x-if="selectedData.status_raw === 'returned' && selectedData.has_incident">
                                    <span class="px-2 py-0.5 rounded bg-red-100 text-red-600 text-[8px] font-black uppercase border border-red-200 tracking-widest flex items-center gap-1">
                                        <i class="bi bi-exclamation-triangle-fill"></i> Ada Insiden
                                    </span>
                                </template>
                            </div>
                        </div>

                        {{-- Rincian Barang Paket --}}
                        <div class="p-5 bg-white rounded-2xl border border-slate-200 shadow-sm">
                            <p class="text-[9px] font-black text-indigo-600 uppercase tracking-widest mb-3 border-b border-slate-100 pb-2">
                                <i class="bi bi-journals me-1"></i> Rincian Buku <span x-show="selectedData.is_paket" x-text="'('+selectedData.items.length+')'"></span>
                            </p>
                            <div class="space-y-2.5 max-h-[200px] overflow-y-auto custom-scroll pr-1">
                                <template x-for="item in selectedData.items" :key="item.barang">
                                    <div class="flex flex-col bg-slate-50 border border-slate-100 rounded-xl overflow-hidden shadow-sm">
                                        
                                        <div class="flex justify-between items-start p-4 bg-white">
                                            <div class="text-left leading-tight overflow-hidden pr-2">
                                                <h4 class="font-black text-slate-900 text-xs uppercase truncate w-full mb-1" x-text="item.barang"></h4>
                                                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest truncate" x-text="'KODE: ' + item.kode"></p>
                                            </div>
                                            <span class="px-2.5 py-1 bg-white border border-indigo-200 rounded-md text-[10px] font-black text-indigo-600 flex-shrink-0 shadow-sm whitespace-nowrap" x-text="item.jumlah + ' Buku'"></span>
                                        </div>
                                        
                                        <template x-if="item.kondisi && item.kondisi !== 'aman' && item.kondisi !== '' && item.kondisi !== null && item.kondisi !== '-'">
                                            <div class="bg-red-50/50 p-4 border-t border-red-100 flex flex-col gap-2">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <span class="px-2.5 py-1 rounded bg-red-100 text-red-600 text-[8px] font-black uppercase border border-red-200" x-text="'KONDISI: ' + item.kondisi"></span>
                                                    
                                                    <template x-if="parseInt(item.hilang) > 0">
                                                        <span class="px-2.5 py-1 rounded bg-slate-200 text-slate-600 text-[8px] font-black uppercase border border-slate-300" x-text="item.hilang + ' Buku ' + item.kondisi"></span>
                                                    </template>
                                                    
                                                    <template x-if="parseInt(item.denda) > 0">
                                                        <span class="px-2.5 py-1 rounded bg-red-600 text-white text-[8px] font-black uppercase border border-red-700 shadow-sm" x-text="'DENDA: RP ' + new Intl.NumberFormat('id-ID').format(item.denda)"></span>
                                                    </template>
                                                </div>
                                                <p class="text-[9px] text-red-800 font-bold italic mt-0.5" x-text="'Catatan: ' + (item.note_rusak || 'Tidak ada keterangan')"></p>
                                            </div>
                                        </template>
                                        
                                    </div>
                                </template>
                            </div>
                        </div>

                        {{-- Visualisasi Timeline --}}
                        <div class="p-5 bg-white rounded-2xl border border-slate-200 shadow-sm space-y-4">
                            <div class="flex items-center gap-2 text-indigo-600 font-black text-[10px] uppercase tracking-widest mb-1">
                                <i class="bi bi-calendar-week-fill"></i> Timeline Sirkulasi
                            </div>
                            <div class="flex justify-between items-start relative mt-2">
                                <div class="absolute top-2 left-0 right-0 h-0.5 bg-gray-100 -z-10"></div>
                                <div class="flex flex-col items-center bg-white px-2">
                                    <span class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Pinjam</span>
                                    <span class="text-[10px] font-bold text-gray-900 bg-gray-50 px-2 py-1 rounded border border-gray-100 whitespace-nowrap" x-text="selectedData.date"></span>
                                </div>
                                <div class="flex flex-col items-center bg-white px-2">
                                    <span class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Deadline</span>
                                    <span class="text-[10px] font-bold text-red-500 bg-red-50 px-2 py-1 rounded border border-red-100 whitespace-nowrap" x-text="selectedData.tenggat"></span>
                                </div>
                            </div>
                            <div class="text-center pt-2 border-t border-slate-50 mt-2">
                                <span class="px-2.5 py-1 bg-indigo-50 text-indigo-600 rounded-md border border-indigo-100 text-[8px] font-black uppercase tracking-widest" x-text="'Durasi: ' + selectedData.durasi"></span>
                            </div>
                        </div>

                        {{-- Info Pengembalian & Denda Total --}}
                        <div x-show="selectedData.return_date && selectedData.return_date !== '-'" class="p-5 rounded-[1.5rem] border border-emerald-100 bg-emerald-50/50 space-y-3">
                            <div class="flex justify-between items-center pb-2" :class="{'border-b border-emerald-100/50' : selectedData.total_denda !== '0'}">
                                <span class="text-[9px] font-bold text-slate-500 uppercase tracking-widest">Dikembalikan Pada</span>
                                <span class="text-[10px] font-black text-slate-800 bg-white px-2 py-1 rounded shadow-sm border border-emerald-100 whitespace-nowrap" x-text="selectedData.return_date"></span>
                            </div>
                            <div x-show="selectedData.total_denda && selectedData.total_denda !== '0'" class="flex justify-between items-center pt-1">
                                <span class="text-[9px] font-bold text-red-500 uppercase tracking-widest">Total Denda Transaksi</span>
                                <span class="text-sm font-black text-red-600 bg-white px-2.5 py-1 rounded-lg shadow-sm border border-red-100 whitespace-nowrap" x-text="'Rp ' + selectedData.total_denda"></span>
                            </div>
                        </div>

                        <div x-show="selectedData.admin_note" class="p-5 bg-slate-900 rounded-2xl border border-slate-800 shadow-xl relative overflow-hidden">
                            <p class="text-[8px] font-black text-indigo-400 uppercase tracking-widest mb-1.5 relative z-10">Catatan Log Sistem (Petugas):</p>
                            <p class="text-[10px] text-slate-300 font-medium leading-relaxed relative z-10" x-text="selectedData.admin_note"></p>
                        </div>
                    </div>
                </template>

                {{-- B. TAMPILAN KHUSUS LAPORAN (REPORT) --}}
                <template x-if="viewType === 'report'">
                    <div class="space-y-5">
                        
                        <div class="p-5 bg-white rounded-2xl border border-gray-100 shadow-sm flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-red-50 text-red-600 flex items-center justify-center text-lg flex-shrink-0"><i class="bi bi-exclamation-octagon"></i></div>
                            <div class="overflow-hidden text-left leading-tight">
                                <h4 class="font-black text-slate-900 text-xs uppercase truncate w-full mb-1" x-text="selectedData.item"></h4>
                                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest truncate" x-text="'Kode: ' + selectedData.asset_code"></p>
                            </div>
                        </div>

                        <template x-if="selectedData.photo">
                            <div class="w-full h-48 bg-gray-100 rounded-[1.5rem] overflow-hidden border border-gray-200 relative group shadow-inner">
                                <img :src="selectedData.photo" class="w-full h-full object-contain bg-black transition-transform group-hover:scale-105">
                                <div class="absolute bottom-3 left-3 bg-black/70 text-white px-2.5 py-1 rounded-md backdrop-blur-md border border-white/20">
                                    <p class="text-[8px] font-bold uppercase tracking-widest"><i class="bi bi-image-fill mr-1"></i> Bukti Kerusakan</p>
                                </div>
                            </div>
                        </template>
                        
                        <template x-if="!selectedData.photo">
                            <div class="w-full h-20 bg-slate-50 rounded-2xl flex items-center justify-center border border-dashed border-slate-200 text-slate-400">
                                <p class="text-[9px] font-bold uppercase tracking-widest"><i class="bi bi-image-alt mr-2"></i> Tidak ada foto bukti</p>
                            </div>
                        </template>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                                <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Tanggal Lapor</p>
                                <p class="text-[10px] font-black text-slate-800 uppercase truncate" x-text="selectedData.date"></p>
                            </div>
                            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 overflow-hidden text-left">
                                <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1.5 leading-none">Status Penanganan</p>
                                <p class="text-[10px] font-black uppercase whitespace-nowrap truncate" 
                                   :class="selectedData.status_raw === 'pending' ? 'text-orange-500' : (selectedData.status_raw === 'checked' ? 'text-indigo-600' : 'text-emerald-600')"
                                   x-text="selectedData.status_raw === 'pending' ? 'Menunggu' : (selectedData.status_raw === 'checked' ? 'Diproses' : 'Selesai')"></p>
                            </div>
                        </div>

                        {{-- TINGKAT KEPARAHAN (SEVERITY) --}}
                        <div class="flex items-center gap-3 p-4 bg-white rounded-2xl border border-gray-100 shadow-sm">
                             <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 shadow-inner border border-yellow-100"
                                  :class="selectedData.severity === 'Ringan' ? 'bg-yellow-50 text-yellow-500 border-yellow-100' : (selectedData.severity === 'Sedang' ? 'bg-orange-50 text-orange-500 border-orange-100' : 'bg-red-50 text-red-500 border-red-100')">
                                 <i class="bi bi-exclamation-triangle-fill text-sm"></i>
                             </div>
                             <div class="text-left overflow-hidden">
                                 <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-0.5 leading-none">Tingkat Keparahan</p>
                                 <p class="text-xs font-black uppercase truncate"
                                    :class="selectedData.severity === 'Ringan' ? 'text-yellow-600' : (selectedData.severity === 'Sedang' ? 'text-orange-600' : 'text-red-600')" 
                                    x-text="selectedData.severity || 'Tidak Diketahui'"></p>
                             </div>
                        </div>

                        <div class="p-5 bg-white border border-red-100 rounded-2xl shadow-sm text-left">
                            <p class="text-[8px] font-black text-red-500 uppercase tracking-widest mb-2 border-b border-red-50 pb-2">Detail Keluhan Member:</p>
                            <p class="text-xs text-gray-700 italic font-medium leading-relaxed" x-text="selectedData.desc"></p>
                        </div>

                        <div class="p-5 bg-slate-900 rounded-2xl border border-slate-800 shadow-xl relative overflow-hidden text-left">
                            <div class="relative z-10">
                                <p class="text-[8px] font-black text-indigo-400 uppercase tracking-widest mb-2">Tanggapan Petugas:</p>
                                <p class="text-xs text-white font-medium leading-relaxed" x-text="selectedData.feedback"></p>
                            </div>
                        </div>
                    </div>
                </template>

            </div>

            <button @click="modalDetail = false" class="w-full mt-8 py-5 bg-slate-900 text-white rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] shadow-xl hover:bg-indigo-600 active:scale-95 transition-all">Tutup Panel Audit</button>
        </div>
    </div>

</body>
</html>