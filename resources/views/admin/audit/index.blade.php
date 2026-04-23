<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Audit Sirkulasi - Librify Admin</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
        .font-jakarta { font-family: 'Plus Jakarta Sans', sans-serif; }
        .custom-scroll::-webkit-scrollbar { width: 4px; height: 4px; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #4f46e5; border-radius: 20px; }
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        
        /* Custom UI Calendar Admin (Indigo Theme) */
        input[type="date"]::-webkit-calendar-picker-indicator {
            background-color: #eef2ff;
            color: #4f46e5;
            padding: 5px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s;
        }
        input[type="date"]::-webkit-calendar-picker-indicator:hover {
            background-color: #e0e7ff;
        }
    </style>
</head>

<body class="antialiased flex h-screen w-full overflow-hidden text-left font-jakarta" 
      x-data="{ 
        sidebarOpen: false, 
        modalDetail: false,
        searchQuery: '',
        
        // Data Objek Lengkap untuk Modal (Support Multiple Items)
        selectedAudit: { 
            is_paket: false,
            items: [], // Array untuk menampung rincian buku
            user: '', role: '',
            item: '', code: '', qty: 0,
            borrow_date: '', return_date: '',
            status: '', condition: '', rating: 0,
            admin_note: '', user_rating: '', status_raw: '',
            durasi: '', tenggat: '',
            is_incident: false,
            return_note: '', lost_qty: 0, 
            fine_amount: 0, fine_status: ''
        }
      }">

    {{-- Sidebar Admin --}}
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'" 
           class="fixed inset-y-0 left-0 z-50 flex flex-col w-72 bg-[#0F172A] text-white border-r border-slate-800 md:static md:flex-shrink-0 h-full transition-transform duration-300">
        @include('admin.partials.sidebar')
    </aside>

    <div class="flex flex-1 flex-col h-full min-w-0 overflow-hidden text-left">
        @include('admin.partials.header')

        <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 pt-2 custom-scroll">
            <div class="mx-auto w-full max-w-[1550px] space-y-6 lg:space-y-8">
                
                {{-- 1. HEADER & SEARCH SECTION --}}
                <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-5 lg:gap-6 leading-none">
                    <div class="text-left leading-none">
                        <h2 class="text-2xl sm:text-3xl lg:text-4xl font-black text-slate-900 tracking-tight uppercase leading-none">Audit Sirkulasi Global</h2>
                        <p class="text-[10px] sm:text-xs lg:text-sm font-bold text-slate-400 mt-2 lg:mt-3 uppercase tracking-[0.2em] leading-none border-l-4 border-indigo-600 pl-2 sm:pl-3">Periode: {{ $summary['period'] }}</p>
                    </div>
                    
                    {{-- Form Pencarian Otomatis --}}
                    <form action="{{ route('admin.audit') }}" method="GET" class="flex flex-col md:flex-row items-center gap-3 w-full xl:w-auto">
                        
                        {{-- Dropdown Filter Status --}}
                        <div class="relative w-full md:w-48 group">
                            <i class="bi bi-funnel-fill absolute left-4 sm:left-5 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-indigo-600 transition-colors"></i>
                            <select name="status" 
                                    @change="$el.closest('form').submit()" 
                                    class="w-full pl-10 sm:pl-12 pr-8 py-3 sm:py-3.5 lg:py-4 bg-white border border-gray-100 rounded-xl sm:rounded-[1.5rem] outline-none font-bold text-[11px] sm:text-xs shadow-sm focus:ring-2 sm:focus:ring-4 focus:ring-indigo-500/10 transition-all appearance-none cursor-pointer text-slate-600 uppercase tracking-wide">
                                <option value="" {{ request('status') == '' ? 'selected' : '' }}>Semua Status</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Dipinjam</option>
                                <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Selesai</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                            </select>
                            <i class="bi bi-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-[10px]"></i>
                        </div>

                        {{-- Search Input (Auto Submit) --}}
                        <div class="relative w-full md:w-72 lg:w-80 group">
                            <i class="bi bi-search absolute left-4 sm:left-5 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-indigo-600 transition-colors"></i>
                            <input type="text" name="search" 
                                   value="{{ request('search') }}" 
                                   @input.debounce.500ms="$el.closest('form').submit()"
                                   placeholder="Cari nama, buku, atau ISBN..." 
                                   class="w-full pl-10 sm:pl-12 pr-4 sm:pr-6 py-3 sm:py-3.5 lg:py-4 bg-white border border-gray-100 rounded-xl sm:rounded-[1.5rem] outline-none font-bold text-[11px] sm:text-xs shadow-sm focus:ring-2 sm:focus:ring-4 focus:ring-indigo-500/10 transition-all placeholder:text-gray-300">
                        </div>
                    </form>
                </div>

                {{-- 2. STATS ANALYTICS --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 lg:gap-6 text-left">
                    <div class="bg-white p-5 lg:p-6 rounded-2xl sm:rounded-[2rem] border border-gray-100 shadow-sm flex items-center gap-4">
                        <div class="w-12 h-12 lg:w-14 lg:h-14 bg-indigo-50 text-indigo-600 rounded-xl sm:rounded-2xl flex items-center justify-center text-xl shadow-inner flex-shrink-0"><i class="bi bi-journal-text"></i></div>
                        <div class="leading-none overflow-hidden">
                            <p class="text-[8px] sm:text-[9px] lg:text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 sm:mb-1.5 leading-none truncate">Total Log</p>
                            <p class="text-2xl lg:text-3xl font-black text-gray-900 leading-none truncate">{{ $summary['total_logs'] }}</p>
                        </div>
                    </div>
                    <div class="bg-white p-5 lg:p-6 rounded-2xl sm:rounded-[2rem] border border-gray-100 shadow-sm flex items-center gap-4">
                        <div class="w-12 h-12 lg:w-14 lg:h-14 bg-emerald-50 text-emerald-600 rounded-xl sm:rounded-2xl flex items-center justify-center text-xl shadow-inner flex-shrink-0"><i class="bi bi-arrow-repeat"></i></div>
                        <div class="leading-none overflow-hidden">
                            <p class="text-[8px] sm:text-[9px] lg:text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 sm:mb-1.5 leading-none truncate">Sedang Aktif</p>
                            <p class="text-2xl lg:text-3xl font-black text-emerald-600 leading-none truncate">{{ $summary['active_loans'] }}</p>
                        </div>
                    </div>
                    <div class="bg-white p-5 lg:p-6 rounded-2xl sm:rounded-[2rem] border border-gray-100 shadow-sm flex items-center gap-4 border-l-4 border-l-red-500">
                        <div class="w-12 h-12 lg:w-14 lg:h-14 bg-red-50 text-red-600 rounded-xl sm:rounded-2xl flex items-center justify-center text-xl shadow-inner flex-shrink-0"><i class="bi bi-exclamation-triangle"></i></div>
                        <div class="leading-none overflow-hidden">
                            <p class="text-[8px] sm:text-[9px] lg:text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 sm:mb-1.5 leading-none truncate">Buku Rusak</p>
                            <p class="text-2xl lg:text-3xl font-black text-red-600 leading-none truncate">{{ $summary['broken_items'] }}</p>
                        </div>
                    </div>
                    <div class="bg-white p-5 lg:p-6 rounded-2xl sm:rounded-[2rem] border border-gray-100 shadow-sm flex items-center gap-4 border-l-4 border-l-orange-500">
                        <div class="w-12 h-12 lg:w-14 lg:h-14 bg-orange-50 text-orange-600 rounded-xl sm:rounded-2xl flex items-center justify-center text-xl shadow-inner flex-shrink-0"><i class="bi bi-search"></i></div>
                        <div class="leading-none overflow-hidden">
                            <p class="text-[8px] sm:text-[9px] lg:text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 sm:mb-1.5 leading-none truncate">Buku Hilang</p>
                            <p class="text-2xl lg:text-3xl font-black text-orange-600 leading-none truncate">{{ $summary['lost_items'] }}</p>
                        </div>
                    </div>
                    <div class="bg-slate-900 p-5 lg:p-6 rounded-2xl sm:rounded-[2rem] shadow-xl flex items-center gap-4 text-white border border-slate-800">
                        <div class="w-12 h-12 lg:w-14 lg:h-14 bg-white/10 text-indigo-400 rounded-xl sm:rounded-2xl flex items-center justify-center text-xl shadow-inner flex-shrink-0"><i class="bi bi-cash-stack"></i></div>
                        <div class="leading-none overflow-hidden">
                            <p class="text-[8px] sm:text-[9px] lg:text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1 sm:mb-1.5 leading-none truncate">Total Denda</p>
                            <p class="text-base sm:text-lg lg:text-xl font-black text-white leading-none truncate w-full">Rp {{ number_format($summary['total_fines'], 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>

                {{-- 3. AUDIT TABLE (GROUPED) --}}
                <div class="rounded-3xl lg:rounded-[2.5rem] bg-white shadow-sm border border-gray-100 overflow-hidden text-left flex flex-col min-h-[500px]">
                    <div class="p-5 lg:p-6 border-b border-gray-50 flex items-center justify-between bg-gray-50/50">
                        <h3 class="text-[9px] lg:text-[10px] font-black text-gray-800 uppercase tracking-[0.2em]"><i class="bi bi-list-check me-1.5 sm:me-2 text-indigo-600"></i> Data Log Audit & Aktivitas Terpusat</h3>
                    </div>
                    <div class="overflow-x-auto custom-scroll flex-1">
                        <table class="w-full text-left text-sm min-w-[800px]">
                            <thead class="sticky top-0 bg-white z-10">
                                <tr class="bg-gray-50/80 text-[7px] sm:text-[8px] lg:text-[9px] uppercase tracking-[0.2em] text-gray-400 font-black border-b border-gray-100 leading-none">
                                    <th class="px-4 sm:px-6 lg:px-8 py-4 lg:py-6">Timestamp</th>
                                    <th class="px-4 sm:px-6 lg:px-8 py-4 lg:py-6">Pelaku & Otoritas</th>
                                    <th class="px-4 sm:px-6 lg:px-8 py-4 lg:py-6">Aksi Sirkulasi</th>
                                    <th class="px-4 sm:px-6 lg:px-8 py-4 lg:py-6">Jadwal & Durasi</th>
                                    <th class="px-4 sm:px-6 lg:px-8 py-4 lg:py-6">Security Status</th>
                                    <th class="px-4 sm:px-6 lg:px-8 py-4 lg:py-6 text-center">Detail</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 leading-tight">
                                @php
                                    // LOGIC GROUPING BATCHING
                                    $groupedAuditLogs = $auditLogs->groupBy(function($item) {
                                        return $item->user_id . '|' . $item->created_at->format('Y-m-d H:i') . '|' . $item->status;
                                    });
                                @endphp

                                @forelse($groupedAuditLogs as $groupKey => $group)
                                @php
                                    $first = $group->first();
                                    $itemCount = $group->count();
                                    $totalQty = $group->sum('quantity');

                                    // Logic Timeline & Overdue
                                    $unit = $first->duration_unit == 'hours' ? 'hours' : 'days';
                                    $deadline = $first->created_at->copy()->add($unit, $first->duration_amount);
                                    $isOverdue = now() > $deadline && $first->status == 'approved';

                                    // Pengecekan insiden dan denda
                                    $paketHasIncident = $group->whereIn('return_condition', ['rusak', 'hilang'])->count() > 0 || $group->where('fine_amount', '>', 0)->count() > 0;
                                    $isUnpaid = $group->where('fine_amount', '>', 0)->where('fine_status', 'unpaid')->count() > 0;
                                    
                                    $searchString = strtolower($first->user->name . ' ' . $group->pluck('item.name')->implode(' '));
                                @endphp
                                <tr class="group hover:bg-gray-50/50 transition-all duration-200"
                                    x-show="searchQuery === '' || '{{ $searchString }}'.includes(searchQuery.toLowerCase())">
                                    
                                    {{-- TIMESTAMP --}}
                                    <td class="px-4 sm:px-6 py-4 lg:px-8 lg:py-6">
                                        <div class="flex flex-col text-left leading-none">
                                            <span class="text-gray-900 font-black uppercase text-[9px] sm:text-[10px] lg:text-[11px] mb-1 sm:mb-1.5 whitespace-nowrap">{{ $first->updated_at->translatedFormat('d M Y') }}</span>
                                            <span class="text-[8px] sm:text-[9px] lg:text-[10px] text-gray-400 font-bold uppercase tracking-widest whitespace-nowrap">{{ $first->updated_at->format('H:i') }} WIB</span>
                                        </div>
                                    </td>
                                    
                                    {{-- PELAKU & OTORITAS --}}
                                    <td class="px-4 sm:px-6 py-4 lg:px-8 lg:py-6">
                                        <div class="flex flex-col text-left leading-none">
                                            <span class="text-gray-900 font-black uppercase text-[11px] sm:text-xs lg:text-sm tracking-tight mb-1.5 truncate max-w-[120px] sm:max-w-[150px] lg:max-w-full" title="{{ $first->user->name }}">{{ $first->user->name }}</span>
                                            
                                            <div class="flex flex-wrap gap-1 sm:gap-1.5 lg:gap-2 items-center">
                                                @if($first->user->role === 'student' || $first->user->role === 'siswa')
                                                    <span class="text-[7px] sm:text-[8px] lg:text-[9px] font-black text-cyan-600 bg-cyan-50 px-1.5 py-0.5 rounded uppercase border border-cyan-100 whitespace-nowrap">Member</span>
                                                @elseif($first->user->role === 'class' || $first->user->role === 'kelas')
                                                    <span class="text-[7px] sm:text-[8px] lg:text-[9px] font-black text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded uppercase border border-blue-100 whitespace-nowrap">Akun Kelas</span>
                                                @else
                                                    <span class="text-[7px] sm:text-[8px] lg:text-[9px] font-black text-indigo-600 bg-indigo-50 px-1.5 py-0.5 rounded uppercase border border-indigo-100 whitespace-nowrap">Petugas</span>
                                                @endif

                                                @if($first->user_rating > 0)
                                                    @php
                                                        $rateColor = $first->user_rating >= 4.5 ? 'text-emerald-600 bg-emerald-50 border-emerald-100' : 
                                                                    ($first->user_rating >= 3.0 ? 'text-orange-600 bg-orange-50 border-orange-100' : 'text-red-600 bg-red-50 border-red-100');
                                                    @endphp
                                                    <span class="text-[7px] sm:text-[8px] lg:text-[9px] font-black {{ $rateColor }} px-1.5 py-0.5 rounded uppercase border flex items-center gap-1 shadow-sm whitespace-nowrap">
                                                        <i class="bi bi-star-fill text-[6px] sm:text-[7px] lg:text-[8px]"></i> {{ number_format($first->user_rating, 1) }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    {{-- AKSI SIRKULASI --}}
                                    <td class="px-4 sm:px-6 py-4 lg:px-8 lg:py-6">
                                        <div class="flex flex-col text-left leading-none">
                                            @php
                                                $actionText = match($first->status) {
                                                    'pending'  => 'Request Baru',
                                                    'approved' => 'Buku Keluar',
                                                    'returned' => 'Buku Masuk',
                                                    'rejected' => 'Request Ditolak',
                                                    default    => 'Update Sistem'
                                                };
                                            @endphp
                                            <span class="text-gray-800 font-black text-[10px] sm:text-[11px] lg:text-[12px] uppercase mb-1 sm:mb-1.5 whitespace-nowrap">{{ $actionText }}</span>
                                            @if($itemCount > 1)
                                                <span class="text-[8px] sm:text-[9px] lg:text-[10px] text-indigo-500 font-bold uppercase tracking-widest border border-indigo-100 bg-indigo-50 px-1.5 py-0.5 rounded w-fit whitespace-nowrap">Paket: {{ $itemCount }} Jdl ({{ $totalQty }} Buku)</span>
                                            @else
                                                <span class="text-[8px] sm:text-[9px] lg:text-[10px] text-gray-400 font-medium italic truncate max-w-[120px] sm:max-w-[150px] lg:max-w-xs">{{ $totalQty }}x {{ $first->item->name }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    
                                    {{-- KOLOM JADWAL --}}
                                    <td class="px-4 sm:px-6 py-4 lg:px-8 lg:py-6">
                                        <div class="flex flex-col leading-tight">
                                            <span class="text-[8px] sm:text-[9px] lg:text-[10px] text-slate-500 font-bold mb-1 uppercase whitespace-nowrap">{{ $first->duration_amount }} {{ $first->duration_unit == 'hours' ? 'Jam' : 'Hari' }}</span>
                                            <span class="text-[8px] sm:text-[9px] lg:text-[10px] font-black {{ $isOverdue ? 'text-red-500 animate-pulse' : 'text-indigo-600' }} uppercase tracking-widest whitespace-nowrap">
                                                Batas: {{ $deadline->format('d M, H:i') }}
                                            </span>
                                        </div>
                                    </td>

                                    {{-- SECURITY STATUS (Berlaku untuk paket) --}}
                                    <td class="px-4 sm:px-6 py-4 lg:px-8 lg:py-6">
                                        @php
                                            $badgeClass = match(true) {
                                                $isUnpaid => 'bg-red-50 text-red-600 border-red-100 animate-pulse',
                                                $paketHasIncident => 'bg-orange-50 text-orange-600 border-orange-100',
                                                $first->status === 'returned' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                                $first->status === 'rejected' => 'bg-red-50 text-red-600 border-red-100',
                                                $first->status === 'pending' => 'bg-orange-50 text-orange-600 border-orange-100',
                                                default => 'bg-blue-50 text-blue-600 border-blue-100'
                                            };

                                            $statusText = match(true) {
                                                $isUnpaid => 'DENDA BELUM LUNAS',
                                                $paketHasIncident => 'ADA INSIDEN',
                                                $first->status === 'returned' => 'SELESAI AMAN',
                                                $first->status === 'rejected' => 'DITOLAK',
                                                $first->status === 'pending' => 'MENUNGGU',
                                                default => 'STATUS: DIPINJAM'
                                            };
                                        @endphp
                                        <span class="inline-flex items-center px-2 sm:px-2.5 lg:px-3 py-1 sm:py-1.5 rounded-lg sm:rounded-xl text-[7px] sm:text-[8px] lg:text-[9px] font-black uppercase tracking-widest border {{ $badgeClass }} shadow-sm whitespace-nowrap">
                                            {{ $statusText }}
                                        </span>
                                    </td>
                                    <td class="px-4 sm:px-6 py-4 lg:px-8 lg:py-6 text-center">
                                        <button @click="selectedAudit = {
                                            is_paket: {{ $itemCount > 1 ? 'true' : 'false' }},
                                            items: {{ $group->map(fn($l) => ['buku' => $l->item->name, 'kode' => $l->item->asset_code, 'jumlah' => $l->quantity, 'kondisi' => $l->return_condition, 'denda' => $l->fine_amount, 'hilang' => $l->lost_quantity, 'note_rusak' => $l->return_note])->toJson() }},
                                            user: '{{ $first->user->name }}',
                                            role: '{{ $first->user->role }}',
                                            user_rating: '{{ $first->user_rating ?? 0 }}',
                                            
                                            item: '{{ $itemCount > 1 ? 'Paket Peminjaman' : $first->item->name }}',
                                            code: '{{ $first->item->asset_code }}',
                                            qty: '{{ $totalQty }}',
                                            
                                            borrow_date: '{{ $first->created_at->format('d M Y, H:i') }}',
                                            durasi: '{{ $first->duration_amount }} {{ $first->duration_unit == 'hours' ? 'Jam' : 'Hari' }}',
                                            tenggat: '{{ $deadline->format('d M Y, H:i') }}',
                                            return_date: '{{ $first->return_date ? \Carbon\Carbon::parse($first->return_date)->format('d M Y, H:i') : '-' }}',
                                            status: '{{ $first->status }}',
                                            status_raw: '{{ $first->status }}',
                                            
                                            admin_note: '{{ $first->admin_note ?? 'Tidak ada feedback dari Petugas.' }}',
                                            is_incident: {{ $paketHasIncident ? 'true' : 'false' }},
                                            fine_amount: '{{ number_format($group->sum('fine_amount'), 0, ',', '.') }}'
                                        }; modalDetail = true" 
                                        class="w-8 h-8 sm:w-9 sm:h-9 lg:w-10 lg:h-10 rounded-lg sm:rounded-xl lg:rounded-2xl bg-white border border-gray-200 text-gray-400 hover:text-indigo-600 hover:border-indigo-200 flex items-center justify-center shadow-sm transition-all mx-auto">
                                            <i class="bi bi-eye-fill text-xs sm:text-sm"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="6" class="py-16 sm:py-20 text-center text-gray-300 font-bold uppercase text-[8px] sm:text-[9px] lg:text-[10px] tracking-[0.3em] italic">Belum ada aktivitas terekam.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    {{-- 👁️ MODAL DETAIL AUDIT (GROUPED / MULTIPLE ITEMS) --}}
    <div x-show="modalDetail" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-md" @click="modalDetail = false"></div>
        <div x-show="modalDetail" x-transition.scale.95 class="relative w-full max-w-lg bg-white rounded-3xl sm:rounded-[2rem] lg:rounded-[3rem] shadow-2xl p-6 sm:p-8 lg:p-10 border border-white text-left leading-none overflow-y-auto max-h-[90vh] custom-scroll mt-auto sm:mt-0">
            
            {{-- Header Modal Statis --}}
            <div class="mb-5 sm:mb-6 lg:mb-8 text-left leading-none flex justify-between items-start border-b border-slate-100 pb-4 sm:pb-5">
                <div class="text-left overflow-hidden">
                    <span class="text-[7px] sm:text-[8px] lg:text-[9px] font-black text-indigo-500 uppercase tracking-[0.2em] bg-indigo-50 border border-indigo-100 px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-md mb-2 inline-block whitespace-nowrap">Audit Trail Pusat</span>
                    <h3 class="text-xl sm:text-2xl lg:text-3xl font-black text-gray-900 uppercase font-jakarta leading-tight truncate">DETAIL TRANSAKSI</h3>
                </div>
                <button @click="modalDetail = false" class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-gray-50 text-gray-400 hover:text-red-500 hover:bg-red-50 flex items-center justify-center transition-colors flex-shrink-0"><i class="bi bi-x-lg text-sm sm:text-base"></i></button>
            </div>

            <div class="space-y-4 sm:space-y-6 text-left leading-none">
                
                {{-- Identitas Peminjam --}}
                <div class="flex items-center gap-3 sm:gap-4 p-3 sm:p-4 bg-slate-50/80 rounded-xl sm:rounded-2xl border border-slate-100 shadow-sm hover:border-indigo-100 transition-colors">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg sm:rounded-xl bg-white flex items-center justify-center shadow-sm text-slate-500 border border-slate-100 flex-shrink-0"><i class="bi bi-person-badge-fill text-lg sm:text-xl"></i></div>
                    <div class="flex-1 overflow-hidden leading-tight">
                        <p class="text-xs sm:text-sm lg:text-base font-black text-slate-800 uppercase truncate w-full" x-text="selectedAudit.user"></p>
                        <div class="flex flex-wrap items-center gap-1.5 sm:gap-2 mt-1 sm:mt-1.5">
                            <span class="text-[7px] sm:text-[8px] font-black text-indigo-500 uppercase tracking-widest border border-indigo-100 bg-indigo-50 px-1.5 sm:px-2 py-0.5 rounded truncate max-w-[150px]" x-text="selectedAudit.role === 'student' || selectedAudit.role === 'siswa' ? 'MEMBER' : (selectedAudit.role === 'class' || selectedAudit.role === 'kelas' ? 'AKUN KELAS' : 'PETUGAS')"></span>
                            
                            <template x-if="selectedAudit.user_rating > 0">
                                <span class="text-[7px] sm:text-[8px] font-black text-white bg-indigo-600 px-1.5 sm:px-2 py-0.5 rounded flex items-center gap-1 shadow-sm whitespace-nowrap">
                                    <i class="bi bi-star-fill text-[6px] sm:text-[7px] text-yellow-300"></i> <span x-text="parseFloat(selectedAudit.user_rating).toFixed(1)"></span>
                                </span>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- Status Peminjaman Box (Full Width) --}}
                <div class="p-4 sm:p-5 bg-slate-50 rounded-xl sm:rounded-2xl border border-slate-100 flex flex-col justify-center shadow-sm">
                    <p class="text-[8px] sm:text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1 sm:mb-1.5">Status Peminjaman</p>
                    <div class="flex items-center gap-2">
                        <p class="text-[10px] sm:text-xs font-black uppercase" 
                           :class="{
                               'text-orange-600': selectedAudit.status_raw === 'pending',
                               'text-indigo-600': selectedAudit.status_raw === 'approved' || selectedAudit.status_raw === 'borrowed',
                               'text-emerald-600': selectedAudit.status_raw === 'returned',
                               'text-red-600': selectedAudit.status_raw === 'rejected'
                           }"
                           x-text="selectedAudit.status === 'returned' ? 'SELESAI' : (selectedAudit.status === 'rejected' ? 'DITOLAK' : (selectedAudit.status === 'pending' ? 'MENUNGGU' : 'DIPINJAM'))"></p>
                        
                        {{-- BADGE INSIDEN --}}
                        <template x-if="selectedAudit.status_raw === 'returned' && selectedAudit.is_incident">
                            <span class="px-1.5 sm:px-2 py-0.5 rounded bg-red-100 text-red-600 text-[7px] sm:text-[8px] font-black uppercase border border-red-200 tracking-widest flex items-center gap-1">
                                <i class="bi bi-exclamation-triangle-fill"></i> Ada Insiden
                            </span>
                        </template>
                    </div>
                </div>

                {{-- Rincian Buku Paket (Desain Card Stacked) --}}
                <div class="p-4 sm:p-5 bg-white rounded-xl sm:rounded-2xl border border-slate-200 shadow-sm">
                    <div class="flex justify-between items-center border-b border-slate-100 pb-1.5 sm:pb-2 mb-2 sm:mb-3">
                        <span class="text-[8px] sm:text-[9px] font-black text-indigo-600 uppercase tracking-widest"><i class="bi bi-book-half me-1"></i> Rincian Buku <span x-show="selectedAudit.is_paket" x-text="'('+selectedAudit.items.length+')'"></span></span>
                        <span class="text-indigo-900 font-black text-[9px] sm:text-[10px]" x-text="selectedAudit.qty + ' BUKU TOTAL'"></span>
                    </div>

                    <div class="space-y-2.5 max-h-[150px] sm:max-h-[200px] overflow-y-auto custom-scroll pr-1">
                        <template x-for="item in selectedAudit.items" :key="item.buku">
                            <div class="flex flex-col bg-slate-50 border border-slate-100 rounded-lg sm:rounded-xl overflow-hidden shadow-sm">
                                
                                <div class="flex justify-between items-start p-3 sm:p-4 bg-white">
                                    <div class="text-left leading-tight overflow-hidden pr-2">
                                        <h4 class="font-black text-slate-900 text-[10px] sm:text-xs uppercase truncate w-full mb-1" x-text="item.buku"></h4>
                                        <p class="text-[8px] sm:text-[9px] font-bold text-gray-400 uppercase tracking-widest truncate" x-text="'KODE: ' + item.kode"></p>
                                    </div>
                                    <span class="px-2 sm:px-2.5 py-0.5 sm:py-1 bg-white border border-indigo-200 rounded-md text-[8px] sm:text-[10px] font-black text-indigo-600 flex-shrink-0 shadow-sm whitespace-nowrap" x-text="item.jumlah + ' Buku'"></span>
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

                {{-- Visualisasi Timeline --}}
                <div class="p-4 sm:p-5 bg-white rounded-xl sm:rounded-2xl border border-slate-200 shadow-sm space-y-3 sm:space-y-4">
                    <div class="flex items-center gap-1.5 sm:gap-2 text-indigo-600 font-black text-[9px] sm:text-[10px] uppercase tracking-widest mb-1">
                        <i class="bi bi-calendar-week-fill"></i> Timeline Sirkulasi
                    </div>
                    <div class="flex justify-between items-start relative mt-1 sm:mt-2">
                        <div class="absolute top-1.5 sm:top-2 left-0 right-0 h-0.5 bg-gray-100 -z-10"></div>
                        <div class="flex flex-col items-center bg-white px-1 sm:px-2">
                            <span class="text-[7px] sm:text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1">Pinjam</span>
                            <span class="text-[8px] sm:text-[10px] font-bold text-gray-900 bg-gray-50 px-1.5 sm:px-2 py-0.5 sm:py-1 rounded border border-gray-100 whitespace-nowrap" x-text="selectedAudit.borrow_date"></span>
                        </div>
                        <div class="flex flex-col items-center bg-white px-1 sm:px-2">
                            <span class="text-[7px] sm:text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1">Deadline</span>
                            <span class="text-[8px] sm:text-[10px] font-bold text-red-500 bg-red-50 px-1.5 sm:px-2 py-0.5 sm:py-1 rounded border border-red-100 whitespace-nowrap" x-text="selectedAudit.tenggat"></span>
                        </div>
                    </div>
                    <div class="text-center pt-1 border-t border-slate-50 mt-1.5 sm:mt-2">
                        <span class="px-2 sm:px-2.5 py-0.5 sm:py-1 bg-indigo-50 text-indigo-600 rounded-md border border-indigo-100 text-[7px] sm:text-[8px] font-black uppercase tracking-widest" x-text="'Durasi: ' + selectedAudit.durasi"></span>
                    </div>
                </div>

                {{-- Tanggal Kembali & Total Denda Paket --}}
                <div x-show="selectedAudit.return_date && selectedAudit.return_date !== '-'" class="p-4 sm:p-5 rounded-xl sm:rounded-[1.5rem] border border-emerald-100 bg-emerald-50/50 space-y-2 sm:space-y-3">
                    <div class="flex justify-between items-center mb-1.5 sm:mb-2 pb-1.5 sm:pb-2" :class="{'border-b border-emerald-100/50' : selectedAudit.fine_amount !== '0'}">
                        <span class="text-[8px] sm:text-[9px] font-bold text-slate-500 uppercase tracking-widest">Dikembalikan Pada</span>
                        <span class="text-[9px] sm:text-[10px] font-black text-slate-800 bg-white px-1.5 sm:px-2 py-0.5 sm:py-1 rounded shadow-sm border border-emerald-100 whitespace-nowrap" x-text="selectedAudit.return_date"></span>
                    </div>
                    <div x-show="selectedAudit.fine_amount && selectedAudit.fine_amount !== '0'" class="flex justify-between items-center pt-1">
                        <span class="text-[8px] sm:text-[9px] font-bold text-red-500 uppercase tracking-widest">Total Denda Paket</span>
                        <span class="text-xs sm:text-sm font-black text-red-600 bg-white px-2 py-1 rounded shadow-sm border border-red-100 whitespace-nowrap" x-text="'Rp ' + selectedAudit.fine_amount"></span>
                    </div>
                </div>

                {{-- Feedback --}}
                <div x-show="selectedAudit.admin_note" class="p-4 sm:p-5 bg-slate-900 rounded-xl sm:rounded-2xl border border-slate-800 shadow-xl relative overflow-hidden">
                    <p class="text-[7px] sm:text-[8px] font-black text-indigo-400 uppercase tracking-widest mb-1 sm:mb-1.5 relative z-10">Catatan Log Sistem (Petugas):</p>
                    <p class="text-[9px] sm:text-[10px] text-slate-300 font-medium leading-relaxed relative z-10" x-text="selectedAudit.admin_note"></p>
                </div>
            </div>

            <button @click="modalDetail = false" class="w-full mt-6 sm:mt-8 py-4 sm:py-5 bg-slate-900 text-white rounded-xl sm:rounded-2xl font-black text-[9px] sm:text-[10px] uppercase tracking-[0.2em] shadow-lg hover:bg-indigo-600 active:scale-95 transition-all border border-slate-800 hover:border-indigo-500">Tutup Panel Audit</button>
        </div>
    </div>

</body>
</html>