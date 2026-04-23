<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laporan Sirkulasi - Librify</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
        .font-jakarta { font-family: 'Plus Jakarta Sans', sans-serif; }
        .custom-scroll::-webkit-scrollbar { width: 4px; height: 4px; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #10b981; border-radius: 20px; }
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        
        /* Custom UI Calendar Petugas (Emerald Theme) */
        input[type="date"]::-webkit-calendar-picker-indicator {
            background-color: #ecfdf5;
            color: #059669;
            padding: 5px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s;
        }
        input[type="date"]::-webkit-calendar-picker-indicator:hover {
            background-color: #d1fae5;
        }
    </style>
</head>

<body class="antialiased flex h-screen w-full overflow-hidden text-left font-jakarta" 
      x-data="{ 
        sidebarOpen: false, 
        activeTab: 'sirkulasi', 
        searchQuery: '', 
        modalDetail: false, 
        modalFinish: false, 
        viewType: '', 
        
        // Object Data Lengkap untuk Modal Detail
        selectedData: { 
            is_paket: false,
            items: [], 
            id: '', item: '', user: '', dept: '', kelas: '', date: '', status: '', asset_code: '',
            return_date: '', condition: '', rating: 0, admin_note: '', total_denda: 0, lost_qty: 0,
            desc: '', feedback: '', status_raw: '', photo: '', user_rating: '',
            durasi: '', tenggat: '', severity: '', has_incident: false
        }
      }">

    {{-- Sidebar Petugas --}}
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'" 
           class="fixed inset-y-0 left-0 z-50 flex flex-col w-72 bg-[#064E3B] text-white border-r border-emerald-900 md:static md:flex-shrink-0 h-full transition-transform duration-300">
        @include('toolman.partials.sidebar')
    </aside>

    <div class="flex flex-1 flex-col h-full min-w-0 overflow-hidden text-left">
        @include('toolman.partials.header')

        <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 pt-2 custom-scroll text-left">
            <div class="mx-auto w-full max-w-[1550px] space-y-6 lg:space-y-8">
                
                {{-- Alert System --}}
                @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" class="bg-emerald-500 text-white px-5 sm:px-6 py-4 rounded-2xl sm:rounded-[1.5rem] shadow-lg flex justify-between items-center transition-all relative z-50">
                    <span class="font-bold text-xs sm:text-sm uppercase tracking-widest"><i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}</span>
                    <button @click="show = false"><i class="bi bi-x-lg"></i></button>
                </div>
                @endif

                @if(session('error'))
                <div class="bg-red-500 text-white px-6 py-4 rounded-[1.5rem] shadow-lg flex justify-between items-center mb-6 transition-all relative z-50">
                    <span class="font-bold text-sm uppercase tracking-widest"><i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}</span>
                </div>
                @endif

                {{-- 1. HEADER & FILTER FORM --}}
                <div class="space-y-4 lg:space-y-6">
                    <div class="text-left leading-none flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
                        <div>
                            <h2 class="text-2xl sm:text-3xl lg:text-4xl font-black text-slate-900 tracking-tight uppercase leading-none">Sirkulasi & Laporan</h2>
                            <p class="text-[10px] sm:text-xs lg:text-sm font-bold text-emerald-600 mt-2 lg:mt-3 uppercase tracking-widest leading-none border-l-4 border-emerald-600 pl-2 sm:pl-3">Periode Data: {{ $summary['period'] }}</p>
                        </div>
                    </div>

                    <div class="bg-white p-5 sm:p-6 lg:p-8 rounded-2xl sm:rounded-[2rem] border border-gray-100 shadow-sm leading-none">
                        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-4 lg:gap-6 items-end">
                            
                            {{-- Filter Date --}}
                            <form action="{{ route('staff.laporan') }}" method="GET" class="contents">
                                <div class="flex flex-col gap-2 sm:gap-2.5">
                                    <label class="text-[8px] sm:text-[9px] font-black text-emerald-600 uppercase tracking-widest ml-1">Mulai Tanggal</label>
                                    <input type="date" name="start_date" value="{{ request('start_date', now()->startOfMonth()->format('Y-m-d')) }}" 
                                           class="w-full px-4 sm:px-5 py-3 sm:py-3.5 bg-gray-50 border border-gray-200 rounded-xl font-bold text-[10px] sm:text-xs outline-none focus:ring-2 focus:ring-emerald-500/20 text-gray-700 transition-all">
                                </div>

                                <div class="flex flex-col gap-2 sm:gap-2.5">
                                    <label class="text-[8px] sm:text-[9px] font-black text-emerald-600 uppercase tracking-widest ml-1">Sampai Tanggal</label>
                                    <input type="date" name="end_date" value="{{ request('end_date', now()->endOfMonth()->format('Y-m-d')) }}" 
                                           class="w-full px-4 sm:px-5 py-3 sm:py-3.5 bg-gray-50 border border-gray-200 rounded-xl font-bold text-[10px] sm:text-xs outline-none focus:ring-2 focus:ring-emerald-500/20 text-gray-700 transition-all">
                                </div>
                                
                                <button type="submit" class="bg-slate-900 text-white rounded-xl py-3 sm:py-3.5 px-4 font-black text-[9px] sm:text-[10px] uppercase tracking-widest hover:bg-emerald-600 transition-all shadow-md active:scale-95 whitespace-nowrap h-fit flex items-center justify-center gap-2">
                                    <i class="bi bi-filter"></i> Filter Data
                                </button>
                            </form>

                            {{-- Live Search --}}
                            <div class="flex flex-col gap-2 sm:gap-2.5 xl:col-span-2">
                                <label class="text-[8px] sm:text-[9px] font-black text-emerald-600 uppercase tracking-widest ml-1">Pencarian Cepat</label>
                                <div class="relative">
                                    <i class="bi bi-search absolute left-4 sm:left-5 top-1/2 -translate-y-1/2 text-emerald-500"></i>
                                    <input type="text" x-model="searchQuery" placeholder="Cari nama peminjam, buku..." 
                                           class="w-full pl-10 sm:pl-12 pr-4 py-3 sm:py-3.5 bg-emerald-50/30 border border-emerald-100 rounded-xl font-bold text-[10px] sm:text-xs outline-none focus:ring-2 focus:ring-emerald-500/20 transition-all placeholder:text-emerald-700/50">
                                </div>
                            </div>
                        </div>
                        
                        {{-- Hak Akses Badge --}}
                        <div class="mt-5 pt-5 border-t border-gray-100 flex items-center gap-2">
                            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Hak Akses Petugas:</span>
                            <span class="px-2.5 py-1 bg-emerald-50 text-emerald-600 text-[9px] font-black rounded-md uppercase border border-emerald-100 tracking-widest">Global / Semua Koleksi</span>
                        </div>
                    </div>
                </div>

                {{-- 2. KPI STATS --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 lg:gap-6 leading-none text-left">
                    <div class="bg-white p-5 lg:p-6 rounded-2xl sm:rounded-[2rem] border border-gray-100 shadow-sm flex items-center gap-4">
                        <div class="w-12 h-12 lg:w-14 lg:h-14 bg-emerald-50 text-emerald-600 rounded-xl sm:rounded-2xl flex items-center justify-center text-xl lg:text-2xl shadow-inner flex-shrink-0"><i class="bi bi-journal-text"></i></div>
                        <div class="text-left overflow-hidden">
                            <p class="text-[8px] sm:text-[9px] lg:text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 sm:mb-1.5 leading-none truncate">Total Sirkulasi</p>
                            <p class="text-2xl lg:text-3xl font-black text-gray-900 leading-none truncate">{{ $summary['total_logs'] }}</p>
                        </div>
                    </div>
                    <div class="bg-white p-5 lg:p-6 rounded-2xl sm:rounded-[2rem] border border-gray-100 shadow-sm flex items-center gap-4">
                        <div class="w-12 h-12 lg:w-14 lg:h-14 bg-orange-50 text-orange-600 rounded-xl sm:rounded-2xl flex items-center justify-center text-xl lg:text-2xl shadow-inner flex-shrink-0"><i class="bi bi-chat-dots-fill"></i></div>
                        <div class="text-left overflow-hidden">
                            <p class="text-[8px] sm:text-[9px] lg:text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 sm:mb-1.5 leading-none truncate">Kendala Baru</p>
                            <p class="text-2xl lg:text-3xl font-black text-orange-600 leading-none truncate">{{ $summary['pending_problems'] }}</p>
                        </div>
                    </div>
                    <div class="bg-white p-5 lg:p-6 rounded-2xl sm:rounded-[2rem] border border-gray-100 shadow-sm flex items-center gap-4">
                        <div class="w-12 h-12 lg:w-14 lg:h-14 bg-red-50 text-red-600 rounded-xl sm:rounded-2xl flex items-center justify-center text-xl lg:text-2xl shadow-inner flex-shrink-0"><i class="bi bi-cash-stack"></i></div>
                        <div class="text-left overflow-hidden">
                            <p class="text-[8px] sm:text-[9px] lg:text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 sm:mb-1.5 leading-none truncate">Total Denda (Masuk)</p>
                            <p class="text-base sm:text-lg lg:text-xl font-black text-red-600 leading-none truncate max-w-[120px] lg:max-w-full">Rp {{ number_format($summary['total_fines'], 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>

                {{-- 3. TAB SWITCHER --}}
                <div class="w-full overflow-x-auto hide-scrollbar -mx-4 px-4 sm:mx-0 sm:px-0">
                    <div class="flex bg-gray-200/50 p-1 sm:p-1.5 rounded-xl sm:rounded-2xl gap-1 w-max sm:w-fit border border-gray-100 relative z-10 flex-nowrap">
                        <button @click="activeTab = 'sirkulasi'; searchQuery = ''" :class="activeTab === 'sirkulasi' ? 'bg-white text-emerald-600 shadow-sm' : 'text-slate-500 hover:text-emerald-500'" class="px-5 sm:px-6 py-2 sm:py-2.5 rounded-lg sm:rounded-xl text-[8px] sm:text-[9px] font-black uppercase tracking-widest transition-all whitespace-nowrap">Riwayat Sirkulasi</button>
                        <button @click="activeTab = 'kendala'; searchQuery = ''" :class="activeTab === 'kendala' ? 'bg-white text-emerald-600 shadow-sm' : 'text-slate-500 hover:text-emerald-500'" class="px-5 sm:px-6 py-2 sm:py-2.5 rounded-lg sm:rounded-xl text-[8px] sm:text-[9px] font-black uppercase tracking-widest transition-all flex items-center gap-1.5 whitespace-nowrap">
                            Laporan Member
                            @if($summary['pending_problems'] > 0)
                                <span class="bg-red-500 text-white px-1.5 py-0.5 rounded-md text-[7px] sm:text-[8px] animate-pulse">{{ $summary['pending_problems'] }}</span>
                            @endif
                        </button>
                    </div>
                </div>

                {{-- 4. CONTENT AREA --}}
                <div class="rounded-2xl sm:rounded-[2rem] bg-white shadow-sm border border-gray-100 overflow-hidden text-left relative z-0 min-h-[500px] flex flex-col">
                    <div class="p-4 sm:p-6 border-b border-gray-50 flex flex-col sm:flex-row items-start sm:items-center justify-between bg-gray-50/50 gap-3">
                        <h3 class="text-[9px] sm:text-[10px] font-black text-gray-800 uppercase tracking-[0.2em]"><i class="bi bi-list-check me-1.5 sm:me-2 text-emerald-600"></i> <span x-text="activeTab === 'sirkulasi' ? 'Arsip Sirkulasi Perpustakaan' : 'Daftar Laporan Kendala'"></span></h3>
                        <a href="{{ route('staff.laporan.export', request()->all()) }}" class="w-full sm:w-auto px-4 py-2 sm:py-1.5 bg-slate-900 text-white rounded-lg text-[8px] sm:text-[9px] font-black uppercase tracking-widest shadow-sm hover:bg-emerald-600 transition-all flex items-center justify-center sm:justify-start gap-1.5">
                            <i class="bi bi-file-earmark-pdf-fill"></i> Cetak Laporan PDF
                        </a>
                    </div>
                    
                    {{-- ✅ SLIDE 1: TABEL SIRKULASI --}}
                    @php
                        // Logika Grouping Berdasarkan Waktu dan User
                        $groupedLogs = $logs->groupBy(function($item) {
                            return $item->user_id . '|' . $item->created_at->format('Y-m-d H:i') . '|' . $item->status;
                        });
                    @endphp

                    <div x-show="activeTab === 'sirkulasi'" x-transition class="flex-1 flex flex-col overflow-hidden">
                        <div class="overflow-auto custom-scroll flex-1">
                            <table class="w-full text-left text-xs lg:text-sm min-w-[750px]">
                                <thead class="sticky top-0 bg-white z-10">
                                    <tr class="bg-gray-50/80 text-[7px] sm:text-[8px] lg:text-[9px] uppercase tracking-[0.2em] text-gray-400 font-black border-b border-gray-100 leading-none">
                                        <th class="px-4 sm:px-6 py-3 sm:py-5">Waktu</th>
                                        <th class="px-4 sm:px-6 py-3 sm:py-5">Identitas Peminjam</th>
                                        <th class="px-4 sm:px-6 py-3 sm:py-5 text-left">Detail Buku</th>
                                        <th class="px-4 sm:px-6 py-3 sm:py-5">Status Final</th>
                                        <th class="px-4 sm:px-6 py-3 sm:py-5 text-center">Detail</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50 leading-tight">
                                    @forelse($groupedLogs as $groupKey => $group)
                                    @php
                                        $first = $group->first();
                                        $itemCount = $group->count();
                                        
                                        $unit = $first->duration_unit == 'hours' ? 'hours' : 'days';
                                        $deadline = $first->created_at->copy()->add($unit, $first->duration_amount);
                                        
                                        $titleName = $itemCount > 1 ? 'Paket Peminjaman' : $first->item->name;
                                        $subName = $itemCount > 1 ? $itemCount . ' Judul Buku' : 'KODE: ' . $first->item->asset_code;
                                        $iconBox = $itemCount > 1 ? 'bi-journals' : 'bi-book';
                                        
                                        $hasIncident = $group->contains(function ($val) {
                                            return in_array($val->return_condition, ['rusak', 'hilang']) || $val->lost_quantity > 0 || $val->fine_amount > 0;
                                        });

                                        $searchString = strtolower($first->user->name . ' ' . $group->pluck('item.name')->implode(' '));
                                    @endphp
                                    <tr x-show="searchQuery === '' || '{{ $searchString }}'.includes(searchQuery.toLowerCase())"
                                        class="group hover:bg-emerald-50/30 transition-all duration-200">
                                        <td class="px-4 sm:px-6 py-3 sm:py-5 font-bold text-gray-400 text-[9px] sm:text-[10px] uppercase tracking-wider whitespace-nowrap">
                                            {{ $first->created_at->format('d M Y, H:i') }} WIB
                                        </td>
                                        <td class="px-4 sm:px-6 py-3 sm:py-5">
                                            <div class="flex flex-col text-left leading-none">
                                                <span class="text-gray-900 font-black uppercase text-[10px] sm:text-xs tracking-tight mb-1 sm:mb-1.5 truncate max-w-[120px] sm:max-w-[150px] lg:max-w-xs">{{ $first->user->name }}</span>
                                                <div class="flex gap-1 sm:gap-1.5 items-center flex-wrap">
                                                    @if($first->user->role === 'student')
                                                        <span class="text-[7px] sm:text-[8px] font-black text-cyan-600 bg-cyan-50 px-1 sm:px-1.5 py-0.5 rounded uppercase border border-cyan-100 w-fit text-left">Siswa</span>
                                                        <span class="text-[7px] sm:text-[8px] font-black text-slate-500 bg-slate-100 px-1 sm:px-1.5 py-0.5 rounded uppercase w-fit border border-slate-200 truncate max-w-[60px] sm:max-w-[80px]">{{ $first->user->classRoom->name ?? '-' }}</span>
                                                    @else
                                                        <span class="text-[7px] sm:text-[8px] font-black text-purple-600 bg-purple-50 px-1 sm:px-1.5 py-0.5 rounded uppercase border border-purple-100 w-fit text-left">Kelas</span>
                                                        <span class="text-[7px] sm:text-[8px] font-black text-slate-500 bg-slate-100 px-1 sm:px-1.5 py-0.5 rounded uppercase w-fit border border-slate-200 truncate max-w-[60px] sm:max-w-[80px]">{{ $first->user->department->name ?? 'UMUM' }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 sm:px-6 py-3 sm:py-5 text-left">
                                            <div class="flex items-center gap-2 sm:gap-3">
                                                <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg sm:rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-black text-xs sm:text-sm shadow-inner border border-emerald-100 flex-shrink-0"><i class="{{ $iconBox }}"></i></div>
                                                <div class="flex flex-col text-left overflow-hidden">
                                                    <span class="text-gray-800 font-black uppercase tracking-tight text-[10px] sm:text-xs truncate max-w-[120px] sm:max-w-[150px]">{{ $titleName }}</span>
                                                    <span class="text-[8px] sm:text-[9px] text-gray-400 font-bold mt-0.5 sm:mt-1 uppercase tracking-widest">{{ $subName }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        
                                        <td class="px-4 sm:px-6 py-3 sm:py-5">
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
                                            <div class="flex flex-col items-start gap-1.5">
                                                <span class="inline-flex items-center px-1.5 sm:px-2.5 py-0.5 sm:py-1 rounded-md text-[7px] sm:text-[8px] font-black uppercase tracking-widest border shadow-sm {{ $badgeClass }}">
                                                    {{ $statusTextLabel }}
                                                </span>
                                                @if($first->status === 'returned' && $hasIncident)
                                                    <span class="text-[7px] font-black text-red-500 bg-red-50 border border-red-100 px-1.5 py-0.5 rounded uppercase tracking-widest flex items-center gap-1 whitespace-nowrap">
                                                        <i class="bi bi-exclamation-triangle-fill"></i> Ada Insiden
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-4 sm:px-6 py-3 sm:py-5 text-center">
                                            <button type="button" @click='viewType = "log"; selectedData = {
                                                is_paket: {{ $itemCount > 1 ? 'true' : 'false' }},
                                                items: {{ $group->map(fn($l) => [
                                                    "barang" => $l->item->name, 
                                                    "jumlah" => $l->quantity, 
                                                    "kondisi" => $l->return_condition,
                                                    "denda" => $l->fine_amount,
                                                    "hilang" => $l->lost_quantity,
                                                    "note_rusak" => $l->return_note,
                                                    "kode" => $l->item->asset_code
                                                ])->toJson() }},
                                                user: @json($first->user->name),
                                                tipe: "{{ $first->user->role === 'student' ? 'Siswa Individu' : 'Akun Kelas' }}",
                                                dept: @json($first->user->department->name ?? "N/A"),
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
                                                total_denda: "{{ number_format($group->sum("fine_amount"), 0, ",", ".") }}"
                                            }; modalDetail = true' 
                                            class="w-7 h-7 sm:w-9 sm:h-9 rounded-lg sm:rounded-xl bg-gray-50 text-gray-400 hover:bg-emerald-50 hover:text-emerald-600 transition-all mx-auto flex items-center justify-center shadow-sm border border-gray-100">
                                                <i class="bi bi-eye-fill text-xs sm:text-sm"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="5" class="py-16 sm:py-20 text-center text-gray-300 font-bold uppercase text-[8px] sm:text-[9px] tracking-[0.3em] italic">Belum ada riwayat terekam.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- SLIDE 2: DAFTAR KENDALA --}}
                    <div x-show="activeTab === 'kendala'" x-transition x-cloak class="flex-1 p-4 sm:p-6 lg:p-8 text-left bg-slate-50/30 overflow-y-auto custom-scroll min-h-[400px]">
                        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-6">
                            @forelse($incomingProblems as $problem)
                            <div x-show="searchQuery === '' || '{{ strtolower($problem->user->name) }}'.includes(searchQuery.toLowerCase()) || '{{ strtolower($problem->item->name) }}'.includes(searchQuery.toLowerCase())"
                                 class="p-5 sm:p-6 rounded-2xl sm:rounded-[2rem] border border-gray-100 bg-white hover:border-emerald-200 hover:shadow-lg transition-all relative overflow-hidden group text-left h-full flex flex-col justify-between min-h-[280px] sm:min-h-[300px]">
                                
                                {{-- Status Color --}}
                                <div class="absolute top-0 left-0 w-1.5 sm:w-2 h-full {{ $problem->status === 'fixed' ? 'bg-emerald-500' : ($problem->status === 'checked' ? 'bg-blue-500' : 'bg-orange-500') }}"></div>
                                
                                {{-- Tombol Detail --}}
                                <div class="absolute top-4 right-4 sm:top-5 sm:right-5 z-20">
                                    <button type="button" @click='viewType = "report"; selectedData = {
                                        id: "{{ $problem->id }}",
                                        item: @json($problem->item->name),
                                        user: @json($problem->user->name),
                                        dept: @json($problem->user->department->name ?? "N/A"),
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
                                    class="w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-white border border-gray-100 text-gray-400 hover:text-emerald-600 hover:border-emerald-100 flex items-center justify-center shadow-sm transition-all cursor-pointer">
                                        <i class="bi bi-eye-fill text-[10px] sm:text-xs"></i>
                                    </button>
                                </div>

                                {{-- Top Content --}}
                                <div class="flex-1 pr-10 sm:pr-12 mb-4 relative z-10 pl-2">
                                    <div class="mb-4">
                                        <p class="text-[7px] sm:text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1 sm:mb-1.5 leading-none text-left">{{ $problem->created_at->diffForHumans() }}</p>
                                        <h4 class="text-xs sm:text-sm font-black text-slate-900 uppercase leading-tight text-left truncate w-full" title="{{ $problem->item->name }}">{{ $problem->item->name }}</h4>
                                        <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-2 mt-1 sm:mt-1.5">
                                            <p class="text-[8px] sm:text-[9px] font-black text-emerald-500 uppercase text-left truncate max-w-[120px]">{{ $problem->user->name }}</p>
                                            
                                            @if(isset($problem->severity))
                                                @php
                                                    $sevClass = match($problem->severity) {
                                                        'Ringan' => 'bg-yellow-50 text-yellow-600 border-yellow-100',
                                                        'Sedang' => 'bg-orange-50 text-orange-600 border-orange-100',
                                                        'Parah'  => 'bg-red-50 text-red-600 border-red-100',
                                                        default  => 'bg-gray-50 text-gray-500 border-gray-200'
                                                    };
                                                @endphp
                                                <span class="px-1.5 sm:px-2 py-0.5 rounded text-[6px] sm:text-[7px] font-black uppercase border shadow-sm leading-none w-fit {{ $sevClass }}">
                                                    {{ $problem->severity }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="p-3 sm:p-4 bg-gray-50 rounded-xl sm:rounded-[1.5rem] border border-gray-100 mb-4 shadow-inner text-left leading-none h-20 sm:h-24 overflow-hidden relative">
                                        <p class="text-[7px] sm:text-[8px] font-black text-red-500 uppercase mb-1.5 sm:mb-2 tracking-widest leading-none text-left"><i class="bi bi-exclamation-triangle-fill me-1"></i> Keluhan:</p>
                                        <p class="text-[9px] sm:text-[11px] text-gray-600 font-medium italic leading-relaxed text-left line-clamp-3">"{{ $problem->description }}"</p>
                                        
                                        @if($problem->photo_path)
                                            <div class="absolute bottom-1.5 sm:bottom-2 right-1.5 sm:right-2 text-red-400 text-[10px] sm:text-xs flex items-center gap-1 bg-red-50 px-1.5 sm:px-2 py-0.5 rounded border border-red-100 shadow-sm">
                                                <i class="bi bi-camera-fill text-[8px] sm:text-[10px]"></i> <span class="text-[6px] sm:text-[7px] font-black uppercase">Ada Bukti</span>
                                            </div>
                                        @endif
                                        <div class="absolute bottom-0 left-0 w-full h-4 bg-gradient-to-t from-gray-50 to-transparent"></div>
                                    </div>
                                </div>
                                
                                {{-- Bottom Action --}}
                                <div class="mt-auto relative z-10 border-t border-slate-100 pt-3 sm:pt-4">
                                    <div class="flex items-center gap-2 sm:gap-3 text-left leading-none">
                                        <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shadow-sm border border-emerald-100"><i class="bi bi-journal-medical text-[10px] sm:text-xs"></i></div>
                                        <div class="text-left leading-none">
                                            <p class="text-[7px] sm:text-[8px] font-black text-gray-400 uppercase leading-none mb-0.5 sm:mb-1 text-left tracking-widest">Status Laporan</p>
                                            <p class="text-[9px] sm:text-[10px] font-black text-slate-800 uppercase tracking-widest text-left truncate max-w-[150px]" 
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
                            <div class="col-span-full py-16 sm:py-20 text-center leading-none text-gray-400 italic">
                                Belum ada laporan kendala yang masuk.
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    {{-- MODAL SECTION --}}

    {{-- ✅ MODAL KONFIRMASI SELESAI --}}
    <div x-show="modalFinish" x-cloak class="fixed inset-0 z-[120] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="modalFinish = false"></div>
        <div x-show="modalFinish" x-transition.scale.95 class="relative w-full max-w-sm bg-white rounded-[2rem] shadow-2xl p-6 sm:p-8 border border-white text-left overflow-hidden">
            <div class="text-center mb-5 sm:mb-6">
                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-emerald-50 text-emerald-600 rounded-[1rem] sm:rounded-[1.2rem] flex items-center justify-center mx-auto mb-3 sm:mb-4 text-xl sm:text-2xl shadow-inner"><i class="bi bi-journal-check"></i></div>
                <h3 class="text-base sm:text-lg font-black text-gray-900 uppercase tracking-tight">Konfirmasi Perbaikan</h3>
                <p class="text-[9px] sm:text-[10px] text-gray-500 mt-1 uppercase tracking-widest">Buku: <span class="font-bold text-gray-800 truncate block max-w-[200px] mx-auto mt-0.5" x-text="selectedData.item"></span></p>
            </div>

            <form :action="'/staff/laporan/masalah/' + selectedData.id" method="POST" class="space-y-4">
                @csrf @method('PUT')
                <input type="hidden" name="status" value="fixed">
                
                <div>
                    <label class="block text-[8px] sm:text-[9px] font-black text-emerald-600 uppercase tracking-widest mb-1.5 sm:mb-2 ml-1">Catatan Solusi</label>
                    <textarea name="admin_note" required rows="3" class="w-full px-3 sm:px-4 py-2.5 sm:py-3 bg-slate-50 border border-slate-200 rounded-xl outline-none text-[10px] sm:text-xs font-medium text-slate-700 focus:ring-2 focus:ring-emerald-500/20 placeholder:text-gray-300 resize-none shadow-inner" placeholder="Tuliskan perbaikan yang dilakukan pada buku..."></textarea>
                </div>

                <div class="flex gap-2 sm:gap-3 pt-2">
                    <button type="button" @click="modalFinish = false" class="flex-1 py-2.5 sm:py-3 bg-gray-100 text-gray-500 rounded-lg sm:rounded-xl font-black text-[8px] sm:text-[9px] uppercase tracking-widest hover:bg-gray-200 transition-all">Batal</button>
                    <button type="submit" class="flex-1 py-2.5 sm:py-3 bg-emerald-600 text-white rounded-lg sm:rounded-xl font-black text-[8px] sm:text-[9px] uppercase tracking-widest shadow-lg hover:bg-emerald-700 transition-all active:scale-95">Selesai</button>
                </div>
            </form>
        </div>
    </div>

    {{-- 👁️ MODAL DETAIL LENGKAP --}}
    <div x-show="modalDetail" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4 text-left">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-md" @click="modalDetail = false"></div>
        <div x-show="modalDetail" x-transition.scale.95 class="relative w-full max-w-lg bg-white rounded-[2rem] shadow-2xl p-8 lg:p-10 border border-white text-left leading-none overflow-y-auto max-h-[90vh] custom-scroll">
            
            <div class="flex justify-between items-start mb-6 text-left leading-none border-b border-slate-50 pb-6">
                <div class="text-left leading-none">
                    <h3 class="text-xl lg:text-2xl font-black text-gray-900 uppercase font-jakarta leading-tight text-left" 
                        x-text="viewType === 'log' ? 'DETAIL TRANSAKSI' : 'DETAIL LAPORAN KENDALA'"></h3>
                </div>
                <button @click="modalDetail = false" class="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-50 transition-colors flex-shrink-0"><i class="bi bi-x-lg"></i></button>
            </div>

            <div class="space-y-6 text-left leading-none">
                
                {{-- Info Umum User --}}
                <div class="flex items-center gap-3 sm:gap-4 p-3 sm:p-4 bg-slate-50/80 rounded-xl sm:rounded-2xl border border-slate-100 shadow-sm hover:border-emerald-100 transition-colors mb-4">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg sm:rounded-xl bg-white flex items-center justify-center shadow-sm text-slate-500 border border-slate-100 flex-shrink-0"><i class="bi bi-person-badge-fill text-sm sm:text-base"></i></div>
                    <div class="flex-1 overflow-hidden">
                        <p class="text-[7px] sm:text-[8px] font-black text-slate-400 uppercase tracking-widest mb-0.5 sm:mb-1">Identitas Member</p>
                        <p class="text-[10px] sm:text-xs lg:text-sm font-black text-slate-800 uppercase truncate max-w-[200px]" x-text="selectedData.user"></p>
                        <p class="text-[7px] sm:text-[8px] font-bold text-slate-500 mt-1 uppercase tracking-widest" x-text="selectedData.tipe + ' (' + selectedData.kelas + ')'"></p>
                    </div>
                </div>

                {{-- A. TAMPILAN KHUSUS: LOG SIRKULASI --}}
                <template x-if="viewType === 'log'">
                    <div class="space-y-4">
                        
                        <div class="p-4 sm:p-5 bg-slate-50 rounded-xl sm:rounded-2xl border border-slate-100 flex flex-col justify-center">
                            <p class="text-[8px] sm:text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1 sm:mb-1.5">Status Peminjaman</p>
                            <div class="flex items-center gap-2">
                                <p class="text-[10px] sm:text-xs font-black uppercase" 
                                   :class="{
                                       'text-orange-600': selectedData.status_raw === 'pending',
                                       'text-blue-600': selectedData.status_raw === 'approved' || selectedData.status_raw === 'borrowed',
                                       'text-emerald-600': selectedData.status_raw === 'returned',
                                       'text-red-600': selectedData.status_raw === 'rejected'
                                   }"
                                   x-text="selectedData.status"></p>
                                
                                <template x-if="selectedData.status_raw === 'returned' && selectedData.has_incident">
                                    <span class="px-1.5 sm:px-2 py-0.5 rounded bg-red-100 text-red-600 text-[7px] sm:text-[8px] font-black uppercase border border-red-200 tracking-widest flex items-center gap-1">
                                        <i class="bi bi-exclamation-triangle-fill"></i> Ada Insiden
                                    </span>
                                </template>
                            </div>
                        </div>

                        <div class="p-4 sm:p-5 bg-white rounded-xl sm:rounded-2xl border border-slate-200 shadow-sm">
                            <p class="text-[8px] sm:text-[9px] font-black text-emerald-600 uppercase tracking-widest mb-2 sm:mb-3 border-b border-slate-100 pb-1.5 sm:pb-2">
                                <i class="bi bi-journals me-1"></i> Rincian Buku <span x-show="selectedData.is_paket" x-text="'('+selectedData.items.length+')'"></span>
                            </p>
                            <div class="space-y-2.5 max-h-[150px] sm:max-h-[200px] overflow-y-auto custom-scroll pr-1">
                                <template x-for="item in selectedData.items" :key="item.barang">
                                    <div class="flex flex-col bg-slate-50 border border-slate-100 rounded-lg sm:rounded-xl overflow-hidden shadow-sm">
                                        
                                        <div class="flex justify-between items-start p-3 sm:p-4 bg-white">
                                            <div class="text-left leading-tight overflow-hidden pr-2">
                                                <h4 class="font-black text-slate-900 text-[10px] sm:text-xs uppercase truncate w-full mb-1" x-text="item.barang"></h4>
                                                <p class="text-[8px] sm:text-[9px] font-bold text-gray-400 uppercase tracking-widest truncate" x-text="'KODE: ' + item.kode"></p>
                                            </div>
                                            <span class="px-2 sm:px-2.5 py-0.5 sm:py-1 bg-white border border-emerald-200 rounded-md text-[8px] sm:text-[10px] font-black text-emerald-600 flex-shrink-0 shadow-sm whitespace-nowrap" x-text="item.jumlah + ' Buku'"></span>
                                        </div>
                                        
                                        <template x-if="item.kondisi && item.kondisi !== 'aman' && item.kondisi !== '' && item.kondisi !== null && item.kondisi !== '-'">
                                            <div class="bg-red-50/50 p-3 sm:p-4 border-t border-red-100 flex flex-col gap-1.5 sm:gap-2">
                                                <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
                                                    <span class="px-1.5 sm:px-2.5 py-0.5 sm:py-1 rounded bg-red-100 text-red-600 text-[7px] sm:text-[8px] font-black uppercase border border-red-200" x-text="'KONDISI: ' + item.kondisi"></span>
                                                    
                                                    <template x-if="parseInt(item.hilang) > 0">
                                                        <span class="px-1.5 sm:px-2.5 py-0.5 sm:py-1 rounded bg-slate-200 text-slate-600 text-[7px] sm:text-[8px] font-black uppercase border border-slate-300" x-text="item.hilang + ' Buku ' + item.kondisi"></span>
                                                    </template>
                                                    
                                                    <template x-if="parseInt(item.denda) > 0">
                                                        <span class="px-1.5 sm:px-2.5 py-0.5 sm:py-1 rounded bg-red-600 text-white text-[7px] sm:text-[8px] font-black uppercase border border-red-700 shadow-sm" x-text="'DENDA: RP ' + new Intl.NumberFormat('id-ID').format(item.denda)"></span>
                                                    </template>
                                                </div>
                                                <p class="text-[8px] sm:text-[9px] text-red-800 font-bold italic mt-0.5" x-text="'Catatan: ' + (item.note_rusak || 'Tidak ada keterangan')"></p>
                                            </div>
                                        </template>
                                        
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div class="p-4 sm:p-5 bg-white rounded-xl sm:rounded-2xl border border-slate-200 shadow-sm space-y-3 sm:space-y-4">
                            <div class="flex items-center gap-1.5 sm:gap-2 text-emerald-600 font-black text-[9px] sm:text-[10px] uppercase tracking-widest mb-1">
                                <i class="bi bi-calendar-week-fill"></i> Timeline Sirkulasi
                            </div>
                            <div class="flex justify-between items-start relative mt-1 sm:mt-2">
                                <div class="absolute top-1.5 sm:top-2 left-0 right-0 h-0.5 bg-gray-100 -z-10"></div>
                                <div class="flex flex-col items-center bg-white px-1 sm:px-2">
                                    <span class="text-[7px] sm:text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1">Pinjam</span>
                                    <span class="text-[8px] sm:text-[10px] font-bold text-gray-900 bg-gray-50 px-1.5 sm:px-2 py-0.5 sm:py-1 rounded border border-gray-100 whitespace-nowrap" x-text="selectedData.date"></span>
                                </div>
                                <div class="flex flex-col items-center bg-white px-1 sm:px-2">
                                    <span class="text-[7px] sm:text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1">Deadline</span>
                                    <span class="text-[8px] sm:text-[10px] font-bold text-red-500 bg-red-50 px-1.5 sm:px-2 py-0.5 sm:py-1 rounded border border-red-100 whitespace-nowrap" x-text="selectedData.tenggat"></span>
                                </div>
                            </div>
                            <div class="text-center pt-1 border-t border-slate-50 mt-1.5 sm:mt-2">
                                <span class="px-2 sm:px-2.5 py-0.5 sm:py-1 bg-emerald-50 text-emerald-600 rounded-md border border-emerald-100 text-[7px] sm:text-[8px] font-black uppercase tracking-widest" x-text="'Durasi: ' + selectedData.durasi"></span>
                            </div>
                        </div>

                        <div x-show="selectedData.return_date && selectedData.return_date !== '-'" class="p-4 sm:p-5 rounded-xl sm:rounded-2xl border border-emerald-100 bg-emerald-50/50 space-y-2 sm:space-y-3">
                            <div class="flex justify-between items-center mb-1.5 sm:pb-2 border-b border-emerald-100/50">
                                <span class="text-[8px] sm:text-[9px] font-bold text-slate-500 uppercase tracking-widest">Dikembalikan Pada</span>
                                <span class="text-[9px] sm:text-[10px] font-black text-slate-800 bg-white px-1.5 sm:px-2 py-0.5 sm:py-1 rounded shadow-sm border border-emerald-100" x-text="selectedData.return_date"></span>
                            </div>
                            <div x-show="selectedData.total_denda && selectedData.total_denda !== '0'" class="flex justify-between items-center pt-1">
                                <span class="text-[8px] sm:text-[9px] font-bold text-red-500 uppercase tracking-widest">Total Denda Paket</span>
                                <span class="text-sm font-black text-red-600 bg-white px-2 py-1 rounded shadow-sm border border-red-100" x-text="'Rp ' + selectedData.total_denda"></span>
                            </div>
                            <div x-show="selectedData.user_rating > 0" class="flex justify-between items-center pt-1.5 sm:pt-2 border-t border-emerald-100/50 mt-1">
                                <span class="text-[8px] sm:text-[9px] font-bold text-emerald-600 uppercase tracking-widest">Rating Transaksi</span>
                                <div class="flex text-yellow-400 text-[10px] sm:text-xs gap-0.5 bg-white px-1.5 sm:px-2 py-0.5 rounded shadow-sm border border-emerald-100">
                                    <template x-for="i in parseInt(selectedData.user_rating)"><i class="bi bi-star-fill"></i></template>
                                </div>
                            </div>
                        </div>

                        <div x-show="selectedData.admin_note" class="p-4 sm:p-5 bg-slate-900 rounded-xl sm:rounded-2xl border border-slate-800 shadow-xl text-left">
                            <p class="text-[7px] sm:text-[8px] font-black text-emerald-400 uppercase tracking-widest mb-1 sm:mb-1.5">Feedback / Catatan Petugas:</p>
                            <p class="text-[9px] sm:text-[10px] text-slate-300 font-medium leading-relaxed" x-text="selectedData.admin_note"></p>
                        </div>
                    </div>
                </template>

                {{-- B. TAMPILAN KHUSUS LAPORAN (REPORT) --}}
                <template x-if="viewType === 'report'">
                    <div class="space-y-4 sm:space-y-5">
                        
                        <div class="p-4 sm:p-5 bg-white rounded-xl sm:rounded-2xl border border-gray-100 shadow-sm flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-red-50 text-red-600 flex items-center justify-center text-base sm:text-lg flex-shrink-0"><i class="bi bi-journal-x"></i></div>
                            <div class="overflow-hidden text-left leading-tight">
                                <h4 class="font-black text-slate-900 text-[11px] sm:text-xs uppercase truncate w-full mb-1" x-text="selectedData.item"></h4>
                                <p class="text-[8px] sm:text-[9px] font-bold text-gray-400 uppercase tracking-widest truncate" x-text="'Kode Buku: ' + selectedData.asset_code"></p>
                            </div>
                        </div>

                        <template x-if="selectedData.photo">
                            <div class="w-full h-40 sm:h-48 bg-gray-100 rounded-xl sm:rounded-[1.5rem] overflow-hidden border border-gray-200 relative shadow-inner group">
                                <img :src="selectedData.photo" class="w-full h-full object-contain bg-black transition-transform group-hover:scale-105">
                                <div class="absolute bottom-2 sm:bottom-3 left-2 sm:left-3 bg-black/70 text-white px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-md backdrop-blur-md border border-white/20">
                                    <p class="text-[7px] sm:text-[8px] font-bold uppercase tracking-widest"><i class="bi bi-camera-fill mr-1"></i> Bukti Lampiran</p>
                                </div>
                            </div>
                        </template>
                        
                        <template x-if="!selectedData.photo">
                            <div class="w-full h-16 sm:h-20 bg-slate-50 rounded-xl sm:rounded-2xl flex items-center justify-center border border-dashed border-slate-200 text-slate-400">
                                <p class="text-[8px] sm:text-[9px] font-bold uppercase tracking-widest"><i class="bi bi-image-alt mr-1.5 sm:mr-2"></i> Tidak ada foto bukti</p>
                            </div>
                        </template>

                        <div class="grid grid-cols-2 gap-3 sm:gap-4">
                            <div class="p-3 sm:p-4 bg-slate-50 rounded-xl sm:rounded-2xl border border-slate-100">
                                <p class="text-[7px] sm:text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1 sm:mb-1.5">Tanggal Lapor</p>
                                <p class="text-[9px] sm:text-[10px] font-black text-slate-800 uppercase truncate" x-text="selectedData.date"></p>
                            </div>
                            <div class="p-3 sm:p-4 bg-slate-50 rounded-xl sm:rounded-2xl border border-slate-100 overflow-hidden text-left">
                                <p class="text-[7px] sm:text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1 sm:mb-1.5 leading-none">Status Penanganan</p>
                                <p class="text-[9px] sm:text-[10px] font-black uppercase whitespace-nowrap truncate" 
                                   :class="selectedData.status_raw === 'pending' ? 'text-orange-500' : (selectedData.status_raw === 'checked' ? 'text-blue-600' : 'text-emerald-600')"
                                   x-text="selectedData.status_raw === 'pending' ? 'Menunggu' : (selectedData.status_raw === 'checked' ? 'Diproses' : 'Selesai')"></p>
                            </div>
                        </div>

                        {{-- TINGKAT KEPARAHAN (SEVERITY) --}}
                        <div class="flex items-center gap-3 p-3 sm:p-4 bg-white rounded-xl sm:rounded-2xl border border-gray-100 shadow-sm">
                             <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 shadow-inner border border-yellow-100"
                                  :class="selectedData.severity === 'Ringan' ? 'bg-yellow-50 text-yellow-500' : (selectedData.severity === 'Sedang' ? 'bg-orange-50 text-orange-500' : 'bg-red-50 text-red-500')">
                                 <i class="bi bi-exclamation-triangle-fill text-xs sm:text-sm"></i>
                             </div>
                             <div class="text-left overflow-hidden">
                                 <p class="text-[7px] sm:text-[8px] font-black text-gray-400 uppercase tracking-widest mb-0.5 leading-none">Tingkat Keparahan</p>
                                 <p class="text-[10px] sm:text-xs font-black uppercase truncate"
                                    :class="selectedData.severity === 'Ringan' ? 'text-yellow-600' : (selectedData.severity === 'Sedang' ? 'text-orange-600' : 'text-red-600')" 
                                    x-text="selectedData.severity || 'Tidak Diketahui'"></p>
                             </div>
                        </div>

                        <div class="p-4 sm:p-5 bg-white border border-red-100 rounded-xl sm:rounded-2xl shadow-sm text-left">
                            <p class="text-[7px] sm:text-[8px] font-black text-red-500 uppercase tracking-widest mb-1.5 sm:mb-2 border-b border-red-50 pb-1.5 sm:pb-2">Detail Keluhan Member:</p>
                            <p class="text-[10px] sm:text-xs text-gray-700 italic font-medium leading-relaxed" x-text="selectedData.desc"></p>
                        </div>

                        <div class="p-4 sm:p-5 bg-slate-900 rounded-xl sm:rounded-2xl border border-slate-800 shadow-xl relative overflow-hidden text-left">
                            <div class="relative z-10">
                                <p class="text-[7px] sm:text-[8px] font-black text-emerald-400 uppercase tracking-widest mb-1.5 sm:mb-2">Catatan Penanganan (Petugas):</p>
                                <p class="text-[10px] sm:text-xs text-white font-medium leading-relaxed" x-text="selectedData.feedback || 'Belum ada catatan.'"></p>
                            </div>
                        </div>
                        
                        <div class="pt-2 text-center" x-show="selectedData.status_raw !== 'fixed'">
                             <button @click="modalDetail = false; setTimeout(() => { modalFinish = true; }, 300)" class="w-full py-3.5 sm:py-4 bg-emerald-600 text-white rounded-xl sm:rounded-2xl font-black text-[9px] sm:text-[10px] uppercase tracking-[0.2em] shadow-lg hover:bg-emerald-700 transition-all active:scale-95">Tandai Selesai Diperbaiki</button>
                        </div>
                    </div>
                </template>

            </div>

            <button x-show="viewType === 'log' || (viewType === 'report' && selectedData.status_raw === 'fixed')" @click="modalDetail = false" class="w-full mt-6 py-3.5 sm:py-4 bg-slate-900 text-white rounded-xl sm:rounded-2xl font-black text-[9px] sm:text-[10px] uppercase tracking-[0.2em] shadow-lg hover:bg-emerald-600 active:scale-95 transition-all">Tutup Panel</button>
        </div>
    </div>

</body>
</html>