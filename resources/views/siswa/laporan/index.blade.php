<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Riwayat & Laporan - TekniLog Siswa</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
        .font-jakarta { font-family: 'Plus Jakarta Sans', sans-serif; }
        .custom-scroll::-webkit-scrollbar { width: 4px; height: 4px; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #3b82f6; border-radius: 20px; }
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        .glass-card { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.5); }
        
        /* Styling khusus untuk custom Kalender Input */
        input[type="date"]::-webkit-calendar-picker-indicator {
            background-color: #eff6ff;
            color: #2563eb;
            padding: 5px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s;
        }
        input[type="date"]::-webkit-calendar-picker-indicator:hover {
            background-color: #dbeafe;
        }
    </style>
</head>

<body class="antialiased flex h-screen w-full overflow-hidden text-left font-jakarta" 
      x-data="{ 
        sidebarOpen: false, 
        activeTab: 'riwayat', 
        modalTambah: false,
        modalDetail: false,
        photoPreview: null,
        searchQuery: '',
        
        // Object dinamis untuk menampung data detail
        detailData: { 
            type: '', 
            is_paket: false,
            items: [], 
            item: '', date: '', status: '', desc: '', feedback: '', photo: '', 
            qty: 0, return_date: '', condition: '', asset_code: '', total_denda: 0,
            durasi: '', tenggat: '', user_rating: 0,
            severity: '' 
        }
      }">

    {{-- Sidebar Siswa (Class) --}}
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'" 
           class="fixed inset-y-0 left-0 z-50 flex flex-col w-72 bg-slate-950 text-white border-r border-slate-900 md:static md:flex-shrink-0 h-full transition-transform duration-300">
        @include('siswa.partials.sidebar') 
    </aside>

    <div class="flex flex-1 flex-col h-full min-w-0 overflow-hidden">
        {{-- Header Siswa --}}
        @include('siswa.partials.header')

        <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-10 pt-2 custom-scroll">
            <div class="mx-auto w-full max-w-[1550px] space-y-6 lg:space-y-8">
                
                {{-- Alert System --}}
                @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" class="bg-blue-600 text-white px-5 sm:px-6 py-4 rounded-2xl sm:rounded-[1.5rem] shadow-lg flex justify-between items-center transition-all relative z-50">
                    <span class="font-bold text-xs sm:text-sm uppercase tracking-widest"><i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}</span>
                    <button @click="show = false"><i class="bi bi-x-lg"></i></button>
                </div>
                @endif

                @if($errors->any())
                <div class="bg-red-500 text-white px-5 sm:px-6 py-4 rounded-2xl sm:rounded-[1.5rem] shadow-lg relative z-50 mb-4">
                    <ul class="list-disc list-inside text-xs sm:text-sm font-bold">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                {{-- 1. HEADER PAGE & TABS --}}
                <div class="flex flex-col xl:flex-row xl:items-end justify-between gap-5 lg:gap-6">
                    <div class="text-left w-full xl:w-auto">
                        <div class="flex items-center gap-3 mb-4">
                            <h2 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight uppercase leading-none">Riwayat & Laporan Kelas</h2>
                            <span class="hidden sm:inline-block px-3 py-1.5 bg-blue-600 text-white text-[9px] sm:text-[10px] font-black rounded-lg uppercase tracking-widest shadow-md shadow-blue-100">
                                Class Representative
                            </span>
                        </div>
                        
                        <div class="w-full overflow-x-auto hide-scrollbar -mx-4 px-4 sm:mx-0 sm:px-0">
                            <div class="flex items-center bg-gray-200/50 p-1.5 rounded-xl sm:rounded-2xl gap-1 w-max sm:w-fit border border-gray-100">
                                <button @click="activeTab = 'riwayat'; searchQuery = ''" :class="activeTab === 'riwayat' ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-500 hover:text-blue-500'" class="px-5 sm:px-8 py-2.5 sm:py-3 rounded-lg sm:rounded-xl text-[9px] sm:text-[10px] font-black uppercase tracking-widest transition-all flex items-center gap-1 sm:gap-2 whitespace-nowrap">
                                    <i class="bi bi-clock-history text-xs sm:text-sm"></i> Riwayat Kelas
                                </button>
                                <button @click="activeTab = 'laporan'; searchQuery = ''" :class="activeTab === 'laporan' ? 'bg-white text-red-600 shadow-sm' : 'text-slate-500 hover:text-red-500'" class="px-5 sm:px-8 py-2.5 sm:py-3 rounded-lg sm:rounded-xl text-[9px] sm:text-[10px] font-black uppercase tracking-widest transition-all flex items-center gap-1 sm:gap-2 whitespace-nowrap">
                                    <i class="bi bi-exclamation-triangle-fill text-xs sm:text-sm"></i> Lapor Kendala
                                </button>
                                
                                <div class="hidden sm:block h-5 w-px bg-gray-300 mx-1 sm:mx-2"></div>
                                
                                <button @click="activeTab = 'rapot'; searchQuery = ''; setTimeout(initChart, 100)" :class="activeTab === 'rapot' ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-sm shadow-indigo-200 border-indigo-400/50' : 'text-slate-500 hover:text-indigo-600 border-transparent'" class="px-5 sm:px-8 py-2.5 sm:py-3 rounded-lg sm:rounded-xl text-[9px] sm:text-[10px] font-black uppercase tracking-widest transition-all flex items-center gap-1 sm:gap-2 border border-transparent whitespace-nowrap">
                                    <i class="bi bi-bar-chart-line-fill text-xs sm:text-sm"></i> Rapot Grafik
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row items-center gap-3 sm:gap-4 w-full xl:w-auto mt-2 xl:mt-0">
                        <div x-show="activeTab !== 'rapot'" class="relative w-full sm:w-64 md:w-80 group">
                            <i class="bi bi-search absolute left-4 sm:left-5 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-blue-600 transition-colors"></i>
                            <input type="text" x-model="searchQuery" placeholder="Cari data..." 
                                   class="w-full pl-10 sm:pl-12 pr-4 sm:pr-6 py-3 sm:py-3.5 bg-white border border-gray-100 rounded-xl sm:rounded-[1.5rem] outline-none font-bold text-[11px] sm:text-xs shadow-sm focus:ring-2 sm:focus:ring-4 focus:ring-blue-500/10 transition-all placeholder:text-gray-300">
                        </div>
                        
                        <template x-if="activeTab === 'laporan'">
                            <button @click="modalTambah = true" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 sm:gap-3 rounded-xl sm:rounded-[1.5rem] bg-red-600 px-6 sm:px-8 py-3 sm:py-3.5 text-[10px] sm:text-xs font-black text-white shadow-md shadow-red-100 hover:bg-red-700 transition-all active:scale-95 uppercase tracking-widest leading-none flex-shrink-0">
                                <i class="bi bi-plus-lg text-sm sm:text-lg"></i> Buat Laporan
                            </button>
                        </template>
                    </div>
                </div>

                {{-- 2. TAB RIWAYAT (DENGAN FILTER & GROUPING) --}}
                <div x-show="activeTab === 'riwayat'" x-transition>
                    
                    {{-- FILTER BAR DENGAN UI KALENDER YANG DIPERBAGUS --}}
                    <div class="bg-white p-5 sm:p-6 rounded-2xl sm:rounded-[2rem] border border-gray-100 shadow-sm mb-4 sm:mb-6">
                        <form action="{{ route('siswa.laporan.export') }}" method="GET" class="flex flex-col sm:flex-row items-end gap-4 sm:gap-5">
                            <div class="flex-1 w-full text-left">
                                <label class="text-[9px] sm:text-[10px] font-black text-blue-500 uppercase tracking-widest mb-2 block ml-1">Dari Tanggal</label>
                                <div class="relative">
                                    <input type="date" name="start_date" class="w-full px-4 sm:px-5 py-3.5 sm:py-4 bg-gray-50 border border-gray-200 rounded-xl font-bold text-xs sm:text-sm text-gray-700 outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-300 shadow-inner transition-all placeholder:text-gray-300">
                                </div>
                            </div>
                            <div class="flex-1 w-full text-left">
                                <label class="text-[9px] sm:text-[10px] font-black text-blue-500 uppercase tracking-widest mb-2 block ml-1">Sampai Tanggal</label>
                                <div class="relative">
                                    <input type="date" name="end_date" class="w-full px-4 sm:px-5 py-3.5 sm:py-4 bg-gray-50 border border-gray-200 rounded-xl font-bold text-xs sm:text-sm text-gray-700 outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-300 shadow-inner transition-all placeholder:text-gray-300">
                                </div>
                            </div>
                            <button type="submit" class="w-full sm:w-auto px-6 sm:px-8 py-3 sm:py-3.5 bg-slate-900 text-white rounded-xl font-black text-[10px] sm:text-xs uppercase tracking-widest hover:bg-blue-600 transition-all shadow-lg hover:shadow-blue-500/30 flex items-center justify-center gap-2 active:scale-95">
                                <i class="bi bi-printer-fill text-sm"></i> Cetak PDF
                            </button>
                        </form>
                    </div>

                    {{-- TABLE RIWAYAT (GROUPED) --}}
                    @php
                        $groupedLoans = $loans->groupBy(function($item) {
                            return $item->created_at->format('Y-m-d H:i') . '|' . $item->status;
                        });
                    @endphp

                    <div class="rounded-2xl sm:rounded-[2rem] bg-white shadow-sm border border-gray-100 overflow-hidden text-left flex flex-col h-full max-h-[550px]">
                        <div class="p-4 sm:p-6 border-b border-gray-50 flex items-center justify-between bg-gray-50/50">
                            <h3 class="text-[9px] sm:text-[10px] font-black text-gray-800 uppercase tracking-[0.2em]"><i class="bi bi-clock-history me-1.5 sm:me-2 text-blue-600"></i> Log Aktivitas Peminjaman</h3>
                        </div>
                        <div class="overflow-y-auto custom-scroll flex-1">
                            <table class="w-full text-left text-xs lg:text-sm min-w-[650px]">
                                <thead class="sticky top-0 bg-white z-10">
                                    <tr class="text-[7px] sm:text-[8px] lg:text-[9px] uppercase tracking-[0.2em] text-gray-400 font-black border-b border-gray-50">
                                        <th class="px-4 sm:px-6 py-3 sm:py-5">Barang / Alat</th>
                                        <th class="px-4 sm:px-6 py-3 sm:py-5">Jadwal & Durasi</th>
                                        <th class="px-4 sm:px-6 py-3 sm:py-5">Status</th>
                                        <th class="px-4 sm:px-6 py-3 sm:py-5 text-center">Kondisi Akhir</th>
                                        <th class="px-4 sm:px-6 py-3 sm:py-5 text-center">Detail</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @forelse($groupedLoans as $groupKey => $group)
                                    @php
                                        $first = $group->first();
                                        $itemCount = $group->count();
                                        $totalQty = $group->sum('quantity');
                                        
                                        $unit = $first->duration_unit == 'hours' ? 'hours' : 'days';
                                        $deadline = $first->created_at->copy()->add($unit, $first->duration_amount);
                                        $isOverdue = now() > $deadline && $first->status == 'approved';

                                        $titleName = $itemCount > 1 ? 'Paket Peminjaman' : $first->item->name;
                                        $subName = $itemCount > 1 ? $itemCount . ' Jenis Barang' : 'KODE: ' . $first->item->asset_code;
                                        $iconBox = $itemCount > 1 ? 'bi-boxes' : 'bi-box-seam';

                                        $statusClass = match($first->status) {
                                            'borrowed', 'approved' => 'bg-blue-50 text-blue-600 border-blue-100',
                                            'returned' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                            'pending'  => 'bg-orange-50 text-orange-600 border-orange-100',
                                            default    => 'bg-red-50 text-red-600 border-red-100'
                                        };
                                        $statusText = match($first->status) {
                                            'borrowed', 'approved' => 'Dipinjam',
                                            'returned' => 'Selesai',
                                            'pending'  => 'Menunggu',
                                            default    => 'Ditolak'
                                        };
                                        
                                        $searchString = strtolower($group->pluck('item.name')->implode(' '));
                                    @endphp
                                    <tr class="group hover:bg-blue-50/30 transition-all duration-200"
                                        x-show="searchQuery === '' || '{{ $searchString }}'.includes(searchQuery.toLowerCase())">
                                        
                                        <td class="px-4 sm:px-6 py-3 sm:py-4 text-left">
                                            <div class="flex items-center gap-2 sm:gap-3">
                                                <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg sm:rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-black text-xs sm:text-sm shadow-inner uppercase border border-blue-100 flex-shrink-0">
                                                    <i class="{{ $iconBox }}"></i>
                                                </div>
                                                <div class="text-left leading-tight overflow-hidden">
                                                    <p class="font-black text-gray-900 text-[10px] sm:text-xs lg:text-sm uppercase leading-tight truncate max-w-[120px] sm:max-w-[180px]">{{ $titleName }}</p>
                                                    <p class="text-[8px] sm:text-[9px] font-bold text-gray-400 uppercase mt-0.5 sm:mt-1 tracking-widest truncate">{{ $subName }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        
                                        <td class="px-4 sm:px-6 py-3 sm:py-4 text-left">
                                            <div class="flex flex-col">
                                                <div class="flex items-center gap-1 sm:gap-1.5">
                                                    <i class="bi bi-calendar-event text-gray-300 hidden sm:block"></i>
                                                    <span class="text-[9px] sm:text-[10px] lg:text-xs font-black text-gray-900 whitespace-nowrap">{{ $first->created_at->format('d M Y') }}</span>
                                                    <span class="text-[8px] sm:text-[9px] text-gray-400 font-bold whitespace-nowrap">{{ $first->created_at->format('H:i') }}</span>
                                                </div>
                                                <div class="flex items-center gap-1 sm:gap-1.5 mt-1 sm:mt-1.5">
                                                    <span class="px-1.5 sm:px-2 py-0.5 rounded bg-gray-100 text-gray-600 text-[7px] sm:text-[8px] font-black uppercase tracking-wider whitespace-nowrap">
                                                        {{ $first->duration_amount }} {{ $first->duration_unit == 'hours' ? 'Jam' : 'Hari' }}
                                                    </span>
                                                    <i class="bi bi-arrow-right text-[7px] sm:text-[8px] text-gray-300"></i>
                                                    <span class="text-[7px] sm:text-[8px] lg:text-[9px] font-bold {{ $isOverdue ? 'text-red-600 animate-pulse' : 'text-blue-600' }} uppercase whitespace-nowrap">
                                                        Batas: {{ $deadline->format('d M, H:i') }}
                                                    </span>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="px-4 sm:px-6 py-3 sm:py-4">
                                            <span class="inline-flex items-center px-1.5 sm:px-2.5 py-0.5 sm:py-1 rounded-md text-[7px] sm:text-[8px] font-black uppercase tracking-widest border shadow-sm {{ $statusClass }}">
                                                {{ $statusText }}
                                            </span>
                                        </td>

                                        <td class="px-4 sm:px-6 py-3 sm:py-4 text-center">
                                            @if($first->return_condition)
                                                <span class="inline-flex items-center gap-1 sm:gap-1.5 px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-md text-[7px] sm:text-[8px] font-black uppercase shadow-sm border
                                                    {{ $first->return_condition === 'aman' ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : ($first->return_condition === 'rusak' ? 'bg-orange-50 text-orange-600 border-orange-100' : 'bg-red-50 text-red-600 border-red-100') }}">
                                                    {{ $first->return_condition }}
                                                </span>
                                            @else
                                                <span class="text-gray-300 font-black">-</span>
                                            @endif
                                        </td>

                                        <td class="px-4 sm:px-6 py-3 sm:py-4 text-center">
                                            <button type="button" @click='detailData = {
                                                type: "loan",
                                                is_paket: {{ $itemCount > 1 ? 'true' : 'false' }},
                                                items: {{ $group->map(fn($l) => [
                                                    "barang" => $l->item->name, "jumlah" => $l->quantity, "kode" => $l->item->asset_code,
                                                    "kondisi" => $l->return_condition, "denda" => $l->fine_amount, "hilang" => $l->lost_quantity, "note_rusak" => $l->return_note
                                                ])->toJson() }},
                                                item: @json($titleName),
                                                qty: "{{ $totalQty }}",
                                                date: "{{ $first->created_at->format("d M Y, H:i") }}",
                                                status: "{{ $statusText }}",
                                                durasi: "{{ $first->duration_amount }} {{ $first->duration_unit == "hours" ? "Jam" : "Hari" }}",
                                                tenggat: "{{ $deadline->format("d M Y, H:i") }}",
                                                desc: @json($first->reason),
                                                feedback: @json($first->admin_note ?? "Tidak ada catatan."),
                                                return_date: "{{ $first->return_date ? \Carbon\Carbon::parse($first->return_date)->format("d M Y, H:i") : "-" }}",
                                                condition: "{{ $first->return_condition ?? "-" }}",
                                                user_rating: "{{ $first->rating ?? 0 }}",
                                                total_denda: "{{ number_format($group->sum("fine_amount"), 0, ",", ".") }}"
                                            }; modalDetail = true' 
                                            class="w-7 h-7 sm:w-9 sm:h-9 rounded-lg sm:rounded-xl bg-gray-50 text-gray-400 hover:bg-blue-600 hover:text-white transition-all flex items-center justify-center shadow-sm mx-auto border border-gray-100">
                                                <i class="bi bi-eye-fill text-xs sm:text-sm"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="py-16 sm:py-20 text-center">
                                            <div class="text-slate-200 text-4xl sm:text-5xl mb-2 sm:mb-3"><i class="bi bi-inbox"></i></div>
                                            <p class="text-gray-400 font-bold uppercase text-[8px] sm:text-[10px] tracking-widest italic">Belum ada riwayat pemakaian.</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- 3. TAB LAPORAN (Report System) --}}
                <div x-show="activeTab === 'laporan'" x-transition x-cloak class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 text-left">
                    @forelse($reports as $report)
                    <div x-show="searchQuery === '' || '{{ strtolower($report->item->name) }}'.includes(searchQuery.toLowerCase())" 
                         class="p-5 sm:p-6 rounded-2xl sm:rounded-[2rem] border border-gray-100 bg-white hover:border-blue-200 hover:shadow-xl transition-all relative overflow-hidden group flex flex-col justify-between min-h-[220px] sm:min-h-[250px] h-full text-left shadow-sm">
                        
                        <div class="absolute top-0 left-0 w-1 sm:w-1.5 h-full 
                            {{ $report->status === 'pending' ? 'bg-orange-500' : '' }}
                            {{ $report->status === 'process' ? 'bg-blue-500' : '' }}
                            {{ $report->status === 'completed' || $report->status === 'fixed' ? 'bg-emerald-500' : '' }}">
                        </div>

                        <div class="absolute top-4 right-4 sm:top-5 sm:right-5 z-20">
                            <button type="button" @click='detailData = {
                                type: "report",
                                item: @json($report->item->name),
                                asset_code: @json($report->item->asset_code),
                                date: "{{ $report->created_at->format("d M Y, H:i") }}",
                                desc: @json($report->description),
                                status: "{{ $report->status }}",
                                severity: "{{ $report->severity ?? 'Ringan' }}",
                                feedback: @json($report->admin_note ?? "Belum ada tanggapan teknisi."),
                                photo: @json($report->photo_path ? asset("storage/".$report->photo_path) : null)
                            }; modalDetail = true' 
                            class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-white border border-gray-100 text-gray-400 hover:text-blue-600 hover:border-blue-100 flex items-center justify-center shadow-sm transition-all cursor-pointer">
                                <i class="bi bi-eye-fill text-[10px] sm:text-xs"></i>
                            </button>
                        </div>

                        <div class="flex-1 pr-8 sm:pr-10 mb-3 sm:mb-4 relative z-10 pl-2 sm:pl-3">
                            <div class="mb-3">
                                <p class="text-[7px] sm:text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1 sm:mb-1.5 leading-none text-left">{{ $report->created_at->diffForHumans() }}</p>
                                <h4 class="text-xs sm:text-sm font-black text-slate-900 uppercase leading-tight truncate w-full pr-2">{{ $report->item->name }}</h4>
                                <p class="text-[8px] sm:text-[9px] font-black text-blue-500 mt-1 sm:mt-1.5 uppercase text-left">ISBN: {{ $report->item->asset_code }}</p>
                            </div>

                            <div class="mb-3 flex flex-wrap gap-1.5">
                                <span class="px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-md text-[7px] sm:text-[8px] font-black uppercase border shadow-sm leading-none
                                    {{ $report->status === 'pending' ? 'bg-orange-50 text-orange-600 border-orange-100' : '' }}
                                    {{ $report->status === 'process' ? 'bg-blue-50 text-blue-600 border-blue-100' : '' }}
                                    {{ $report->status === 'completed' || $report->status === 'fixed' ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : '' }}">
                                    {{ $report->status === 'pending' ? 'Menunggu' : ($report->status === 'process' ? 'Diproses' : 'Selesai') }}
                                </span>
                                
                                @if(isset($report->severity))
                                    @php
                                        $sevClass = match($report->severity) {
                                            'Ringan' => 'bg-yellow-50 text-yellow-600 border-yellow-100',
                                            'Sedang' => 'bg-orange-50 text-orange-600 border-orange-100',
                                            'Parah'  => 'bg-red-50 text-red-600 border-red-100',
                                            default  => 'bg-gray-50 text-gray-500 border-gray-200'
                                        };
                                    @endphp
                                    <span class="px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-md text-[7px] sm:text-[8px] font-black uppercase border shadow-sm leading-none {{ $sevClass }}">
                                        {{ $report->severity }}
                                    </span>
                                @endif
                            </div>

                            <div class="p-3 sm:p-4 bg-gray-50 rounded-xl sm:rounded-2xl border border-gray-100 shadow-inner text-left leading-none h-16 sm:h-20 overflow-hidden relative">
                                <p class="text-[7px] sm:text-[8px] font-black text-red-500 uppercase mb-1.5 sm:mb-2 tracking-widest leading-none text-left">Keluhan Saya:</p>
                                <p class="text-[9px] sm:text-[10px] text-gray-600 font-medium italic leading-relaxed text-left line-clamp-2 pr-1">"{{ $report->description }}"</p>
                                @if($report->photo_path)
                                    <div class="absolute bottom-1.5 right-1.5 sm:bottom-2 sm:right-2 text-red-400 text-[10px] sm:text-xs flex items-center gap-1 bg-white px-1.5 sm:px-2 py-0.5 sm:py-1 rounded border border-red-50 shadow-sm">
                                        <i class="bi bi-camera-fill text-[7px] sm:text-[8px]"></i> <span class="text-[6px] sm:text-[7px] font-bold uppercase">Bukti</span>
                                    </div>
                                @endif
                                <div class="absolute bottom-0 left-0 w-full h-4 bg-gradient-to-t from-gray-50 to-transparent"></div>
                            </div>
                        </div>

                        <div class="mt-auto relative z-10 pt-2.5 sm:pt-3 border-t border-gray-50 pl-2 sm:pl-3">
                            @if($report->admin_note)
                                <div class="flex items-center gap-2 sm:gap-3">
                                    <div class="w-6 h-6 sm:w-7 sm:h-7 rounded-full bg-slate-900 text-white flex items-center justify-center text-[9px] sm:text-[10px] flex-shrink-0"><i class="bi bi-chat-text-fill"></i></div>
                                    <div class="overflow-hidden">
                                        <p class="text-[7px] sm:text-[8px] font-bold text-slate-400 uppercase leading-none mb-0.5 sm:mb-1 truncate">Balasan Teknisi</p>
                                        <p class="text-[8px] sm:text-[9px] font-black text-slate-800 uppercase leading-none truncate">Ada Tanggapan</p>
                                    </div>
                                </div>
                            @else
                                <div class="flex items-center gap-2 sm:gap-3 opacity-50">
                                    <div class="w-6 h-6 sm:w-7 sm:h-7 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center text-[9px] sm:text-[10px] flex-shrink-0"><i class="bi bi-hourglass"></i></div>
                                    <div class="overflow-hidden">
                                        <p class="text-[7px] sm:text-[8px] font-bold text-gray-400 uppercase leading-none mb-0.5 sm:mb-1 truncate">Status</p>
                                        <p class="text-[8px] sm:text-[9px] font-black text-gray-500 uppercase leading-none truncate">Menunggu...</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="col-span-full py-16 sm:py-20 text-center leading-none text-gray-400 italic">
                        <i class="bi bi-clipboard-check text-3xl sm:text-4xl mb-3 sm:mb-4 block text-slate-200"></i>
                        <span class="text-xs sm:text-sm font-medium">Tidak ada laporan kendala.</span>
                    </div>
                    @endforelse
                </div>

                {{-- 4. TAB RAPOT GRAFIK --}}
                <div x-show="activeTab === 'rapot'" x-transition x-cloak>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 lg:gap-6 mb-6 lg:mb-8">
                        <div class="p-5 lg:p-6 rounded-2xl lg:rounded-[2rem] glass-card border border-blue-100 shadow-xl shadow-blue-50 flex flex-col justify-between bg-white">
                            <div>
                                <span class="px-2.5 sm:px-3 py-1 sm:py-1.5 bg-blue-600 text-white text-[8px] sm:text-[9px] font-black rounded-lg uppercase tracking-widest mb-3 sm:mb-4 inline-block">Personal Rating</span>
                                <p class="text-[9px] sm:text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Rata-rata Individu</p>
                                <h3 class="text-3xl sm:text-4xl font-black text-gray-900">{{ number_format($individualAverage ?? 0, 1) }} <span class="text-xs sm:text-sm text-gray-300">/ 5.0</span></h3>
                            </div>
                            <div class="mt-3 sm:mt-4 flex items-center gap-1 text-yellow-400 text-sm sm:text-base">
                                @for($i=1; $i<=5; $i++)
                                    <i class="bi bi-star{{ $i <= round($individualAverage ?? 0) ? '-fill' : '' }}"></i>
                                @endfor
                            </div>
                        </div>

                        <div class="p-5 lg:p-6 rounded-2xl lg:rounded-[2rem] bg-white border border-gray-100 shadow-sm flex flex-col justify-between">
                            <div>
                                <span class="px-2.5 sm:px-3 py-1 sm:py-1.5 bg-slate-900 text-white text-[8px] sm:text-[9px] font-black rounded-lg uppercase tracking-widest mb-3 sm:mb-4 inline-block">Class Average</span>
                                <p class="text-[9px] sm:text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Rata-rata Kelas</p>
                                <h3 class="text-3xl sm:text-4xl font-black text-gray-900">{{ number_format($classAverage ?? 0, 1) }}</h3>
                            </div>
                            <p class="text-[8px] sm:text-[10px] font-bold text-gray-500 italic mt-3 sm:mt-4 uppercase tracking-tighter">Performa kolektif kelas {{ Auth::user()->classRoom->name ?? '' }}</p>
                        </div>

                        @php
                            $indAvg = $individualAverage ?? 0;
                            $clsAvg = $classAverage ?? 0;
                            $isBetter = $indAvg >= $clsAvg;
                            $diff = abs($indAvg - $clsAvg);
                        @endphp
                        <div class="p-5 lg:p-6 rounded-2xl lg:rounded-[2rem] {{ $isBetter ? 'bg-emerald-50 border-emerald-100' : 'bg-orange-50 border-orange-100' }} border flex flex-col justify-center items-center text-center">
                            <div class="w-12 h-12 sm:w-16 sm:h-16 rounded-full {{ $isBetter ? 'bg-emerald-500' : 'bg-orange-500' }} text-white flex items-center justify-center text-2xl sm:text-3xl mb-3 sm:mb-4 shadow-lg">
                                <i class="bi {{ $isBetter ? 'bi-graph-up-arrow' : 'bi-info-circle' }}"></i>
                            </div>
                            <h4 class="text-[10px] sm:text-xs font-black uppercase tracking-widest {{ $isBetter ? 'text-emerald-700' : 'text-orange-700' }}">
                                {{ $isBetter ? 'Di Atas Rata-rata' : 'Butuh Peningkatan' }}
                            </h4>
                            <p class="text-[8px] sm:text-[10px] font-bold opacity-70 mt-1 uppercase">Selisih {{ number_format($diff, 1) }} dari rata-rata kelas</p>
                        </div>
                    </div>

                    <div class="bg-white p-5 sm:p-6 lg:p-8 rounded-2xl lg:rounded-[2.5rem] border border-gray-100 shadow-sm">
                        <div class="mb-4 sm:mb-6 text-left">
                            <h3 class="text-base sm:text-lg font-black text-gray-900 uppercase tracking-tight leading-none">Tren Kedisiplinan</h3>
                            <p class="text-[8px] sm:text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1.5 sm:mt-2">Statistik Rating Peminjaman 6 Bulan Terakhir</p>
                        </div>
                        <div id="chart-timeline" class="min-h-[250px] sm:min-h-[350px] w-full -ml-2 sm:ml-0"></div>
                    </div>
                </div>

            </div>
        </main>
    </div>

    {{-- MODAL BUAT LAPORAN KENDALA --}}
    <div x-show="modalTambah" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-md" @click="modalTambah = false"></div>
        <div x-show="modalTambah" x-transition.scale.95 class="relative w-full max-w-lg bg-white rounded-3xl sm:rounded-[2rem] shadow-2xl p-6 sm:p-8 lg:p-10 border border-white text-left overflow-y-auto max-h-[90vh] custom-scroll mt-auto sm:mt-0">
            <div class="flex justify-between items-start mb-5 sm:mb-6">
                <h3 class="text-xl sm:text-2xl font-black text-gray-900 font-jakarta uppercase tracking-tight leading-none">Ajukan Laporan</h3>
                <button @click="modalTambah = false" class="w-8 h-8 rounded-full bg-gray-100 text-gray-500 hover:bg-red-100 hover:text-red-500 transition-colors flex items-center justify-center flex-shrink-0"><i class="bi bi-x-lg text-sm"></i></button>
            </div>
            
            <form action="{{ route('siswa.laporan.problem') }}" method="POST" enctype="multipart/form-data" class="space-y-4 sm:space-y-6">
                @csrf
                
                <div class="text-left leading-none">
                    <label class="block text-[9px] sm:text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 sm:mb-3">Pilih Alat Bermasalah</label>
                    <select name="loan_id" required class="w-full px-4 sm:px-5 py-3 sm:py-4 bg-gray-50 border border-gray-100 rounded-xl outline-none font-bold text-[11px] sm:text-xs text-gray-700 focus:ring-2 focus:ring-red-500/20 transition-all appearance-none shadow-sm cursor-pointer truncate">
                        <option value="">-- Pilih dari Peminjaman Kelas --</option>
                        @foreach($loans as $loan)
                            <option value="{{ $loan->id }}">{{ $loan->item->name }} (Dipinjam: {{ $loan->created_at->format('d M') }})</option>
                        @endforeach
                    </select>
                    <p class="text-[8px] sm:text-[9px] text-gray-400 mt-1.5 sm:mt-2 ml-1 italic">*Hanya barang yang pernah/sedang dipinjam yang bisa dilaporkan.</p>
                </div>

                {{-- TINGKAT KEPARAHAN --}}
                <div class="text-left leading-none">
                    <label class="block text-[9px] sm:text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 sm:mb-3">Tingkat Keparahan</label>
                    <select name="severity" required class="w-full px-4 sm:px-5 py-3 sm:py-4 bg-gray-50 border border-gray-100 rounded-xl outline-none font-bold text-[11px] sm:text-xs text-gray-700 focus:ring-2 focus:ring-red-500/20 transition-all appearance-none shadow-sm cursor-pointer">
                        <option value="" disabled selected>-- Pilih Tingkat Keparahan --</option>
                        <option value="Ringan">Ringan (Masih bisa digunakan dengan sedikit kendala)</option>
                        <option value="Sedang">Sedang (Perlu perbaikan, fungsi utama terganggu)</option>
                        <option value="Parah">Rusak Parah / Hilang (Tidak bisa digunakan sama sekali)</option>
                    </select>
                </div>

                <div class="text-left leading-none">
                    <label class="block text-[9px] sm:text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 sm:mb-3">Detail Kejadian / Kerusakan</label>
                    <textarea name="description" required rows="4" placeholder="Jelaskan detail barang apa yang rusak, di bagian mana, dan bagaimana kejadiannya..." class="w-full px-4 sm:px-5 py-3 sm:py-4 bg-gray-50 border border-gray-100 rounded-xl outline-none font-medium text-[11px] sm:text-xs focus:ring-2 focus:ring-red-500/20 transition-all shadow-sm resize-none"></textarea>
                </div>

                <div class="text-left leading-none">
                    <label class="block text-[9px] sm:text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 sm:mb-3">Bukti Foto (Sangat Disarankan)</label>
                    <div class="flex items-center gap-3 sm:gap-4">
                        <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-xl bg-gray-50 border border-dashed border-gray-200 flex items-center justify-center overflow-hidden relative shadow-inner flex-shrink-0">
                            <template x-if="!photoPreview"><i class="bi bi-camera text-lg sm:text-xl text-gray-400"></i></template>
                            <template x-if="photoPreview"><img :src="photoPreview" class="w-full h-full object-cover"></template>
                        </div>
                        <input type="file" name="photo" accept=".jpg,.jpeg,.png" class="text-[9px] sm:text-[10px] text-slate-500 file:mr-2 sm:file:mr-4 file:py-1.5 sm:file:py-2 file:px-3 sm:file:px-4 file:rounded-md file:border-0 file:text-[9px] sm:file:text-[10px] file:font-bold file:bg-red-50 file:text-red-600 hover:file:bg-red-100 cursor-pointer w-full"
                               @change="const file = $event.target.files[0]; if(file){ const reader = new FileReader(); reader.onload = (e) => { photoPreview = e.target.result }; reader.readAsDataURL(file); }">
                    </div>
                </div>

                <div class="flex flex-col-reverse sm:flex-row gap-3 sm:gap-4 pt-2 sm:pt-4">
                    <button type="button" @click="modalTambah = false" class="w-full sm:flex-1 px-6 py-3.5 sm:py-4 rounded-xl bg-gray-100 text-slate-500 font-black text-[9px] sm:text-[10px] uppercase tracking-widest transition-all hover:bg-gray-200">Batal</button>
                    <button type="submit" class="w-full sm:flex-1 px-6 py-3.5 sm:py-4 rounded-xl bg-red-600 text-white font-black text-[9px] sm:text-[10px] uppercase tracking-widest shadow-md shadow-red-100 hover:bg-red-700 active:scale-95 transition-all">Kirim Laporan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ✅ MODAL DETAIL PINTAR (Loan Grouped / Report) DENGAN HEADER STATIS --}}
    <div x-show="modalDetail" x-cloak class="fixed inset-0 z-[120] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-md" @click="modalDetail = false"></div>
        <div x-show="modalDetail" x-transition.scale.95 class="relative w-full max-w-lg bg-white rounded-3xl sm:rounded-[2rem] shadow-2xl p-6 sm:p-8 lg:p-10 border border-white flex flex-col max-h-[90vh] text-left leading-none overflow-y-auto custom-scroll mt-auto sm:mt-0">
            
            <div class="flex justify-between items-start mb-5 sm:mb-6 text-left gap-3 border-b border-gray-100 pb-4">
                <div class="text-left overflow-hidden">
                    {{-- HEADER BESAR STATIS --}}
                    <h3 class="text-xl lg:text-2xl font-black text-gray-900 font-jakarta uppercase tracking-tight leading-tight truncate"
                        x-text="detailData.type === 'loan' ? 'DETAIL PEMINJAMAN' : 'DETAIL LAPORAN KENDALA'"></h3>
                </div>
                <button @click="modalDetail = false" class="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-50 transition-colors flex-shrink-0"><i class="bi bi-x-lg text-sm"></i></button>
            </div>

            <div class="space-y-4 sm:space-y-6 text-left">
                
                {{-- A. TAMPILAN KHUSUS RIWAYAT (LOAN GROUPED) --}}
                <template x-if="detailData.type === 'loan'">
                    <div class="space-y-4">
                        
                        <div class="grid grid-cols-2 gap-3 sm:gap-4">
                            <div class="p-4 sm:p-5 bg-slate-50 rounded-xl sm:rounded-2xl border border-slate-100">
                                <p class="text-[8px] sm:text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1 sm:mb-1.5">Jumlah Total</p>
                                <p class="text-xs sm:text-sm font-black text-gray-800 truncate" x-text="detailData.qty + ' Unit'"></p>
                            </div>
                            <div class="p-4 sm:p-5 bg-slate-50 rounded-xl sm:rounded-2xl border border-slate-100">
                                <p class="text-[8px] sm:text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1 sm:mb-1.5">Status</p>
                                <p class="text-[10px] sm:text-xs font-black text-blue-600 uppercase truncate" x-text="detailData.status"></p>
                            </div>
                        </div>

                        {{-- Rincian Barang Paket PERSIS GAMBAR (Desain Card Stacked) --}}
                        <template x-if="detailData.is_paket || !detailData.is_paket"> 
                            <div class="p-4 sm:p-5 bg-white rounded-xl sm:rounded-2xl border border-slate-200 shadow-sm">
                                <p class="text-[8px] sm:text-[9px] font-black text-blue-600 uppercase tracking-widest mb-2 sm:mb-3 border-b border-slate-100 pb-1.5 sm:pb-2"><i class="bi bi-boxes me-1"></i> Rincian Barang</p>
                                <div class="space-y-2.5 max-h-[200px] sm:max-h-[250px] overflow-y-auto custom-scroll pr-1">
                                    <template x-for="item in detailData.items" :key="item.barang">
                                        <div class="flex flex-col bg-slate-50 border border-slate-100 rounded-lg sm:rounded-xl overflow-hidden">
                                            
                                            <div class="flex justify-between items-start p-3 sm:p-4 bg-white">
                                                <div class="text-left leading-tight overflow-hidden pr-2">
                                                    <h4 class="font-black text-slate-900 text-[10px] sm:text-xs uppercase truncate w-full mb-1" x-text="item.barang"></h4>
                                                    <p class="text-[8px] sm:text-[9px] font-bold text-gray-400 uppercase tracking-widest truncate" x-text="'KODE: ' + item.kode"></p>
                                                </div>
                                                <span class="px-2 sm:px-2.5 py-0.5 sm:py-1 bg-white border border-blue-200 rounded-md text-[8px] sm:text-[10px] font-black text-blue-600 flex-shrink-0 shadow-sm whitespace-nowrap" x-text="item.jumlah + ' Unit'"></span>
                                            </div>
                                            
                                            <template x-if="item.kondisi && item.kondisi !== 'aman' && item.kondisi !== '' && item.kondisi !== null && item.kondisi !== '-'">
                                                <div class="bg-red-50/50 p-3 sm:p-4 border-t border-red-100 flex flex-col gap-1.5 sm:gap-2">
                                                    <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
                                                        <span class="px-1.5 sm:px-2.5 py-0.5 sm:py-1 rounded bg-red-100 text-red-600 text-[7px] sm:text-[8px] font-black uppercase border border-red-200" x-text="'KONDISI: ' + item.kondisi"></span>
                                                        
                                                        <template x-if="parseInt(item.hilang) > 0">
                                                            <span class="px-1.5 sm:px-2.5 py-0.5 sm:py-1 rounded bg-slate-200 text-slate-600 text-[7px] sm:text-[8px] font-black uppercase border border-slate-300" x-text="item.hilang + ' Unit ' + item.kondisi"></span>
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
                        </template>

                        {{-- TIMELINE PEMINJAMAN --}}
                        <div class="p-4 sm:p-5 bg-white rounded-xl sm:rounded-2xl border border-gray-100 shadow-sm space-y-3 sm:space-y-4">
                            <div class="flex items-center gap-1.5 sm:gap-2 text-blue-600 font-black text-[9px] sm:text-[10px] uppercase tracking-widest mb-1">
                                <i class="bi bi-calendar-week-fill"></i> Timeline
                            </div>
                            <div class="flex justify-between items-start relative">
                                <div class="absolute top-1.5 sm:top-2 left-4 right-4 h-0.5 bg-gray-100 -z-10"></div>
                                <div class="flex flex-col items-center bg-white px-1 sm:px-2">
                                    <span class="text-[7px] sm:text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1 sm:mb-1.5">Mulai Request</span>
                                    <span class="text-[8px] sm:text-[10px] font-bold text-gray-900 bg-gray-50 border border-gray-100 px-1.5 sm:px-2 py-0.5 sm:py-1 rounded whitespace-nowrap" x-text="detailData.date"></span>
                                </div>
                                <div class="flex flex-col items-center bg-white px-1 sm:px-2">
                                    <span class="text-[7px] sm:text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1 sm:mb-1.5">Batas Waktu</span>
                                    <span class="text-[8px] sm:text-[10px] font-bold text-red-500 bg-red-50 border border-red-100 px-1.5 sm:px-2 py-0.5 sm:py-1 rounded whitespace-nowrap" x-text="detailData.tenggat"></span>
                                </div>
                            </div>
                            <div class="text-center pt-1 sm:pt-2">
                                <span class="px-2 sm:px-3 py-1 bg-blue-50 text-blue-600 rounded-md text-[7px] sm:text-[8px] font-black uppercase tracking-widest border border-blue-100" x-text="'Durasi: ' + detailData.durasi"></span>
                            </div>
                        </div>

                        {{-- Info Pengembalian & Denda Total --}}
                        <div x-show="detailData.return_date && detailData.return_date !== '-'" class="p-4 sm:p-5 bg-emerald-50/50 rounded-xl sm:rounded-[1.5rem] border border-emerald-100 text-left space-y-2 sm:space-y-3">
                            <div class="flex justify-between items-center pb-1.5 sm:pb-2" :class="{'border-b border-emerald-100/50' : detailData.total_denda !== '0'}">
                                <span class="text-[8px] sm:text-[9px] font-black text-emerald-600 uppercase tracking-widest">Dikembalikan Pada:</span>
                                <span class="text-[9px] sm:text-[10px] font-black text-emerald-900 bg-white border border-emerald-100 px-1.5 sm:px-2 py-0.5 sm:py-1 rounded shadow-sm whitespace-nowrap" x-text="detailData.return_date"></span>
                            </div>
                            <div x-show="detailData.total_denda && detailData.total_denda !== '0'" class="flex justify-between items-center pt-1">
                                <span class="text-[8px] sm:text-[9px] font-black text-red-500 uppercase tracking-widest">Total Denda Transaksi</span>
                                <span class="text-xs sm:text-sm font-black text-red-600 bg-white border border-red-100 px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-lg shadow-sm whitespace-nowrap" x-text="'Rp ' + detailData.total_denda"></span>
                            </div>
                            <div x-show="detailData.user_rating > 0" class="flex justify-between items-center pt-1.5 sm:pt-2 border-t border-emerald-100/50 mt-1">
                                <span class="text-[8px] sm:text-[9px] font-bold text-emerald-600 uppercase tracking-widest">Rating Teknisi</span>
                                <div class="flex text-yellow-400 text-[10px] sm:text-xs gap-0.5 bg-white px-1.5 sm:px-2 py-0.5 rounded shadow-sm border border-emerald-100">
                                    <template x-for="i in parseInt(detailData.user_rating)"><i class="bi bi-star-fill"></i></template>
                                </div>
                            </div>
                        </div>

                        <div class="p-4 sm:p-5 bg-slate-900 rounded-xl sm:rounded-2xl border border-slate-800 text-left shadow-xl">
                            <p class="text-[7px] sm:text-[8px] font-black text-blue-400 uppercase tracking-widest mb-1 sm:mb-1.5">Feedback / Catatan Toolman:</p>
                            <p class="text-[9px] sm:text-[10px] text-slate-300 font-medium leading-relaxed" x-text="detailData.feedback"></p>
                        </div>
                    </div>
                </template>

                {{-- B. TAMPILAN KHUSUS LAPORAN (REPORT) --}}
                <template x-if="detailData.type === 'report'">
                    <div class="space-y-4 sm:space-y-5">
                        
                        <div class="p-4 sm:p-5 bg-white rounded-xl sm:rounded-2xl border border-gray-100 shadow-sm flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-red-50 text-red-600 flex items-center justify-center text-base sm:text-lg flex-shrink-0"><i class="bi bi-box-seam"></i></div>
                            <div class="overflow-hidden text-left leading-tight">
                                <h4 class="font-black text-slate-900 text-[11px] sm:text-xs uppercase truncate w-full mb-1" x-text="detailData.item"></h4>
                                <p class="text-[8px] sm:text-[9px] font-bold text-gray-400 uppercase tracking-widest truncate" x-text="'Kode: ' + detailData.asset_code"></p>
                            </div>
                        </div>

                        <template x-if="detailData.photo">
                            <div class="w-full h-40 sm:h-48 bg-gray-100 rounded-xl sm:rounded-[1.5rem] overflow-hidden border border-gray-200 relative group shadow-inner">
                                <img :src="detailData.photo" class="w-full h-full object-cover transition-transform group-hover:scale-105">
                                <div class="absolute bottom-2 sm:bottom-3 left-2 sm:left-3 bg-black/60 text-white px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-md backdrop-blur-md">
                                    <p class="text-[7px] sm:text-[8px] font-bold uppercase tracking-widest"><i class="bi bi-image-fill mr-1"></i> Bukti Kerusakan</p>
                                </div>
                            </div>
                        </template>
                        
                        <template x-if="!detailData.photo">
                            <div class="w-full h-16 sm:h-20 bg-slate-50 rounded-xl sm:rounded-2xl flex items-center justify-center border border-dashed border-slate-200 text-slate-400">
                                <p class="text-[8px] sm:text-[9px] font-bold uppercase tracking-widest"><i class="bi bi-image-alt mr-1.5 sm:mr-2"></i> Tidak ada foto bukti</p>
                            </div>
                        </template>

                        <div class="grid grid-cols-2 gap-3 sm:gap-4">
                            <div class="p-3 sm:p-4 bg-slate-50 rounded-xl sm:rounded-2xl border border-slate-100">
                                <p class="text-[7px] sm:text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1 sm:mb-1.5">Tanggal Lapor</p>
                                <p class="text-[9px] sm:text-[10px] font-black text-slate-800 uppercase truncate" x-text="detailData.date"></p>
                            </div>
                            <div class="p-3 sm:p-4 bg-slate-50 rounded-xl sm:rounded-2xl border border-slate-100">
                                <p class="text-[7px] sm:text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1 sm:mb-1.5">Status Penanganan</p>
                                <p class="text-[9px] sm:text-[10px] font-black uppercase truncate" 
                                   :class="detailData.status === 'pending' ? 'text-orange-500' : (detailData.status === 'process' ? 'text-blue-600' : 'text-emerald-600')"
                                   x-text="detailData.status === 'pending' ? 'Menunggu' : (detailData.status === 'process' ? 'Diproses' : 'Selesai')"></p>
                            </div>
                        </div>

                        {{-- TINGKAT KEPARAHAN (SEVERITY) --}}
                        <div class="flex items-center gap-3 p-3 sm:p-4 bg-white rounded-xl sm:rounded-2xl border border-gray-100 shadow-sm">
                             <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 shadow-inner border border-yellow-100"
                                  :class="detailData.severity === 'Ringan' ? 'bg-yellow-50 text-yellow-500' : (detailData.severity === 'Sedang' ? 'bg-orange-50 text-orange-500' : 'bg-red-50 text-red-500')">
                                 <i class="bi bi-exclamation-triangle-fill text-xs sm:text-sm"></i>
                             </div>
                             <div class="text-left overflow-hidden">
                                 <p class="text-[7px] sm:text-[8px] font-black text-gray-400 uppercase tracking-widest mb-0.5">Tingkat Keparahan</p>
                                 <p class="text-[10px] sm:text-xs font-black uppercase truncate"
                                    :class="detailData.severity === 'Ringan' ? 'text-yellow-600' : (detailData.severity === 'Sedang' ? 'text-orange-600' : 'text-red-600')" 
                                    x-text="detailData.severity || 'Tidak Diketahui'"></p>
                             </div>
                        </div>

                        <div class="p-4 sm:p-5 bg-white border border-red-100 rounded-xl sm:rounded-2xl shadow-sm text-left">
                            <p class="text-[7px] sm:text-[8px] font-black text-red-500 uppercase tracking-widest mb-1.5 sm:mb-2 border-b border-red-50 pb-1.5 sm:pb-2">Detail Keluhan Siswa:</p>
                            <p class="text-[10px] sm:text-xs text-gray-700 italic font-medium leading-relaxed" x-text="detailData.desc"></p>
                        </div>

                        <div class="p-4 sm:p-5 bg-slate-900 rounded-xl sm:rounded-2xl border border-slate-800 shadow-xl relative overflow-hidden text-left">
                            <div class="relative z-10">
                                <p class="text-[7px] sm:text-[8px] font-black text-blue-400 uppercase tracking-widest mb-1.5 sm:mb-2">Tanggapan Teknisi:</p>
                                <p class="text-[10px] sm:text-xs text-white font-medium leading-relaxed" x-text="detailData.feedback"></p>
                            </div>
                        </div>
                    </div>
                </template>

            </div>

            <button @click="modalDetail = false" class="w-full mt-6 py-3.5 sm:py-4 bg-slate-900 text-white rounded-xl sm:rounded-2xl font-black text-[9px] sm:text-[10px] uppercase tracking-[0.2em] shadow-lg hover:bg-blue-600 active:scale-95 transition-all">Tutup Panel</button>
        </div>
    </div>

    {{-- SCRIPT APEXCHARTS --}}
    <script>
        let isChartRendered = false;

        function initChart() {
            var chartElement = document.querySelector("#chart-timeline");
            if (!chartElement || isChartRendered) return; 

            var chartData = @json($chartData ?? []);
            var chartLabels = @json($chartLabels ?? []);

            if(chartData.length === 0) {
                chartElement.innerHTML = '<div class="flex items-center justify-center h-full min-h-[250px]"><p class="text-xs font-bold text-gray-400 italic">Belum ada data untuk ditampilkan</p></div>';
                isChartRendered = true;
                return;
            }

            var options = {
                series: [{
                    name: 'Rating Saya',
                    data: chartData 
                }],
                chart: {
                    type: 'area',
                    height: window.innerWidth < 640 ? 250 : 350, 
                    toolbar: { show: false },
                    zoom: { enabled: false },
                    fontFamily: 'Plus Jakarta Sans, sans-serif'
                },
                dataLabels: { enabled: false },
                stroke: {
                    curve: 'smooth',
                    width: window.innerWidth < 640 ? 3 : 4,
                    colors: ['#2563eb']
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.45,
                        opacityTo: 0.05,
                        stops: [20, 100, 100, 100]
                    }
                },
                xaxis: {
                    categories: chartLabels,
                    labels: {
                        style: { colors: '#94a3b8', fontSize: window.innerWidth < 640 ? '8px' : '10px', fontWeight: 800, textTransform: 'uppercase' },
                        rotate: -45, 
                        rotateAlways: window.innerWidth < 640
                    },
                    axisBorder: { show: false },
                    axisTicks: { show: false }
                },
                yaxis: {
                    max: 5,
                    min: 0,
                    labels: { style: { colors: '#94a3b8', fontSize: window.innerWidth < 640 ? '8px' : '10px', fontWeight: 800 } }
                },
                grid: {
                    borderColor: '#f1f5f9',
                    strokeDashArray: 4,
                },
                markers: {
                    size: window.innerWidth < 640 ? 4 : 6, 
                    colors: ['#2563eb'], 
                    strokeColors: '#fff', 
                    strokeWidth: 3, 
                    hover: { size: 8 }
                },
                tooltip: {
                    theme: 'dark',
                    y: { formatter: function(val) { return val.toFixed(1) + " Bintang"; } }
                }
            };

            var chart = new ApexCharts(chartElement, options);
            chart.render();
            isChartRendered = true;
        }
    </script>

</body>
</html>