<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sirkulasi Buku - Librify</title>

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
        /* Animasi getar untuk kode salah */
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        .animate-shake { animation: shake 0.3s ease-in-out; }
    </style>
</head>

<body class="antialiased flex h-screen w-full overflow-hidden text-left font-jakarta" 
      x-data="{ 
        sidebarOpen: false, 
        activeTab: 'aktif', 
        searchQuery: '', 
        modalDetail: false,
        modalAction: false,
        modalReturn: false,
        modalPaid: false, 
        viewType: 'log',
        
        masterKode: '', 
        kodeError: false,
        
        // Object Data Lengkap & Universal untuk Semua Modal Detail
        selectedReq: { 
            is_paket: false,
            items: [], 
            id: '', nama: '', tipe: '', sub_info: '', 
            catatan: '', admin_note: '', tenggat: '', durasi: '',
            tgl_pinjam: '', tgl_kembali: '', isLate: false,
            total_denda: 0, status_denda: '', 
            avg_rating: 5, 
            trx_rating: 0, 
            status: '', item: '', asset_code: '',
            kode_peminjaman: ''
        },
        actionRoute: '',
        actionType: '', 
        rating: 5,

        // FUNGSI VALIDASI KODE TANPA RELOAD
        submitActionForm(e) {
            if (this.actionType === 'approve' && this.selectedReq.kode_peminjaman) {
                if (this.masterKode.trim().toUpperCase() !== this.selectedReq.kode_peminjaman.toUpperCase()) {
                    this.kodeError = true;
                    return; 
                }
            }
            e.target.submit();
        }
      }">

    {{-- Sidebar --}}
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'" 
           class="fixed inset-y-0 left-0 z-50 flex flex-col w-72 bg-[#064E3B] text-white border-r border-emerald-900 md:static md:flex-shrink-0 h-full transition-transform duration-300">
        @include('toolman.partials.sidebar')
    </aside>

    <div class="flex flex-1 flex-col h-full min-w-0 overflow-hidden text-left">
        @include('toolman.partials.header')

        <main class="flex-1 overflow-y-auto p-4 lg:p-8 pt-2 custom-scroll text-left">
            <div class="mx-auto w-full max-w-[1550px] space-y-6 lg:space-y-8">
                
                {{-- Notifikasi --}}
                @if(session('success'))
                <div x-data="{show: true}" x-show="show" x-init="setTimeout(() => show = false, 4000)" class="bg-emerald-500 text-white px-6 py-4 rounded-[1.5rem] shadow-lg flex justify-between items-center mb-6 transition-all relative z-50">
                    <span class="font-bold text-sm uppercase tracking-widest"><i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}</span>
                    <button @click="show = false"><i class="bi bi-x-lg"></i></button>
                </div>
                @endif

                @if(session('error'))
                <div class="bg-red-500 text-white px-6 py-4 rounded-[1.5rem] shadow-lg flex justify-between items-center mb-6 transition-all relative z-50">
                    <span class="font-bold text-sm uppercase tracking-widest"><i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}</span>
                </div>
                @endif

                {{-- 1. HEADER & SEARCH BAR --}}
                <div class="flex flex-col xl:flex-row xl:items-end justify-between gap-6 leading-none">
                    <div class="text-left leading-none">
                        <h2 class="text-3xl lg:text-4xl font-black text-slate-900 tracking-tight uppercase leading-none">Sirkulasi Buku & Antrean</h2>
                        <div class="flex flex-wrap gap-2 mt-4 items-center">
                            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest mr-1">Hak Akses:</span>
                            <span class="px-2.5 py-1 bg-emerald-50 text-emerald-600 text-[9px] font-black rounded-md uppercase border border-emerald-100 tracking-widest">Global / Semua Koleksi</span>
                        </div>
                    </div>

                    <div class="relative w-full md:w-80 group">
                        <i class="bi bi-search absolute left-5 top-1/2 -translate-y-1/2 text-emerald-500 transition-colors"></i>
                        <input type="text" x-model="searchQuery" placeholder="Cari peminjam, kelas, atau buku..." 
                               class="w-full pl-12 pr-6 py-4 bg-white border border-gray-100 rounded-[1.5rem] outline-none font-bold text-xs shadow-sm focus:ring-4 focus:ring-emerald-500/10 transition-all placeholder:text-gray-300">
                    </div>
                </div>

                {{-- 2. KPI STATS (COMPACT) --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">
                    <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm flex items-center gap-5">
                        <div class="w-12 h-12 bg-orange-50 text-orange-600 rounded-xl flex items-center justify-center text-xl shadow-inner"><i class="bi bi-clock-history"></i></div>
                        <div class="text-left leading-none"><p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Antrean ACC</p><p class="text-2xl font-black text-gray-900">{{ $requests->where('status', 'pending')->count() }}</p></div>
                    </div>
                    <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm flex items-center gap-5">
                        <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-xl shadow-inner"><i class="bi bi-box-arrow-up"></i></div>
                        <div class="text-left leading-none"><p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Sedang Keluar</p><p class="text-2xl font-black text-gray-900">{{ $requests->where('status', 'approved')->count() }}</p></div>
                    </div>
                    <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm flex items-center gap-5 border-b-4 border-b-emerald-500">
                        <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center text-xl shadow-inner"><i class="bi bi-star-fill"></i></div>
                        <div class="text-left leading-none"><p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Telah Selesai</p><p class="text-2xl font-black text-gray-900">{{ $requests->where('status', 'returned')->count() }}</p></div>
                    </div>
                    <div class="bg-slate-900 p-6 rounded-[2rem] shadow-xl flex items-center gap-5 text-white border border-slate-800">
                        <div class="w-12 h-12 bg-white/10 text-emerald-400 rounded-xl flex items-center justify-center text-xl shadow-inner"><i class="bi bi-shield-lock"></i></div>
                        <div class="text-left leading-none"><p class="text-[9px] font-black text-slate-500 uppercase tracking-widest mb-1.5">Tagihan Aktif</p><p class="text-lg font-black uppercase tracking-tight">{{ $requests->where('fine_status', 'unpaid')->count() }} Kasus</p></div>
                    </div>
                </div>

                {{-- 3. TAB SWITCHER --}}
                <div class="flex bg-gray-200/50 p-1.5 rounded-2xl gap-1 w-fit border border-gray-100 relative z-10">
                    <button @click="activeTab = 'aktif'; searchQuery = ''" :class="activeTab === 'aktif' ? 'bg-white text-emerald-600 shadow-sm' : 'text-slate-500 hover:text-emerald-500'" class="px-6 lg:px-8 py-2.5 lg:py-3 rounded-xl text-[9px] lg:text-[10px] font-black uppercase tracking-widest transition-all">Sirkulasi Aktif</button>
                    <button @click="activeTab = 'denda'; searchQuery = ''" :class="activeTab === 'denda' ? 'bg-white text-red-600 shadow-sm' : 'text-slate-500 hover:text-red-500'" class="px-6 lg:px-8 py-2.5 lg:py-3 rounded-xl text-[9px] lg:text-[10px] font-black uppercase tracking-widest transition-all">Data Denda</button>
                    <button @click="activeTab = 'riwayat'; searchQuery = ''" :class="activeTab === 'riwayat' ? 'bg-white text-emerald-600 shadow-sm' : 'text-slate-500 hover:text-emerald-500'" class="px-6 lg:px-8 py-2.5 lg:py-3 rounded-xl text-[9px] lg:text-[10px] font-black uppercase tracking-widest transition-all">Arsip Sirkulasi</button>
                </div>

                {{-- 4. TABLES --}}
                <div class="rounded-[2rem] bg-white shadow-sm border border-gray-100 overflow-hidden text-left relative z-0 flex flex-col min-h-[500px]">
                    
                    {{-- TAB SIRKULASI AKTIF --}}
                    @php
                        $groupedActive = $requests->whereIn('status', ['pending', 'approved'])->groupBy(function($item) {
                            return $item->user_id . '|' . $item->created_at->format('Y-m-d H:i') . '|' . $item->status;
                        });
                    @endphp
                    
                    <div x-show="activeTab === 'aktif'" x-transition class="flex flex-col h-full">
                        <div class="overflow-x-auto flex-1 custom-scroll">
                            <table class="w-full text-left text-sm">
                                <thead class="sticky top-0 bg-white z-10">
                                    <tr class="bg-gray-50/80 text-[9px] uppercase tracking-[0.2em] text-gray-400 font-black border-b border-gray-100 leading-none">
                                        <th class="px-6 py-5">Identitas Peminjam</th>
                                        <th class="px-6 py-5 text-left">Paket Buku & Total</th>
                                        <th class="px-6 py-5">Batas Waktu</th>
                                        <th class="px-6 py-5">Status</th>
                                        <th class="px-6 py-5 text-center">Aksi Petugas</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50 leading-tight">
                                    @forelse($groupedActive as $groupKey => $group)
                                    @php
                                        $first = $group->first();
                                        $itemCount = $group->count();
                                        $totalQty = $group->sum('quantity');
                                        
                                        $unit = $first->duration_unit == 'hours' ? 'hours' : 'days';
                                        $deadline = $first->created_at->copy()->add($unit, $first->duration_amount);
                                        $isLate = now()->greaterThan($deadline) && $first->status === 'approved' ? 'true' : 'false';
                                        
                                        $titleName = $itemCount > 1 ? 'Paket Peminjaman' : $first->item->name;
                                        $subName = $itemCount > 1 ? $itemCount . ' Judul Buku' : 'KODE: ' . $first->item->asset_code;
                                        $iconBox = $itemCount > 1 ? 'bi-journals' : 'bi-book';
                                        
                                        $avgRating = (float) ($first->user_rating ?: 5.0);
                                        $searchString = strtolower($first->user->name . ' ' . $group->pluck('item.name')->implode(' '));
                                        
                                        $detailData = [
                                            'is_paket' => $itemCount > 1,
                                            'items' => $group->map(function($l) {
                                                return [
                                                    'id' => $l->id,
                                                    'barang' => $l->item->name,
                                                    'jumlah' => $l->quantity,
                                                    'kode' => $l->item->asset_code,
                                                    'sisaStok' => $l->item->stock,
                                                    'kondisi' => 'aman',
                                                    'note_rusak' => '',
                                                    'denda' => 0,
                                                    'hilang' => 0
                                                ];
                                            })->toArray(),
                                            'nama' => $first->user->name,
                                            'tipe' => $first->user->role === 'student' ? 'Siswa Individu' : 'Perwakilan Kelas',
                                            'sub_info' => $first->user->role === 'student' ? ($first->user->classRoom->name ?? '-') : ($first->user->department->name ?? '-'),
                                            'catatan' => $first->reason,
                                            'admin_note' => $first->admin_note ?? '',
                                            'tenggat' => $deadline->translatedFormat('d M Y, H:i'),
                                            'durasi' => $first->duration_amount . ' ' . ($first->duration_unit == 'hours' ? 'Jam' : 'Hari'),
                                            'tgl_pinjam' => $first->created_at->format('d M Y, H:i'),
                                            'tgl_kembali' => '-',
                                            'total_denda' => '0',
                                            'avg_rating' => $avgRating,
                                            'trx_rating' => 0,
                                            'status' => $first->status,
                                            'item' => $titleName,
                                            'asset_code' => $itemCount > 1 ? $itemCount.' Buku' : $first->item->asset_code,
                                            'isLate' => $isLate === 'true',
                                            'kode_peminjaman' => $first->loan_code ?? ''
                                        ];
                                    @endphp
                                    <tr class="group hover:bg-emerald-50/30 transition-all duration-200" 
                                        x-show="searchQuery === '' || '{{ $searchString }}'.includes(searchQuery.toLowerCase())">
                                        
                                        <td class="px-6 py-5">
                                            <div class="flex flex-col text-left">
                                                <span class="text-gray-900 font-black uppercase text-sm tracking-tight truncate max-w-[180px]">{{ $first->user->name }}</span>
                                                <div class="flex flex-wrap items-center gap-2 mt-1.5">
                                                    @if($first->user->role === 'student')
                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[8px] font-black uppercase bg-cyan-50 text-cyan-600 border border-cyan-100"><i class="bi bi-person-fill"></i> Siswa</span>
                                                        <span class="text-[8px] text-gray-400 font-bold uppercase tracking-widest">{{ $first->user->classRoom->name ?? 'No Class' }}</span>
                                                    @else
                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[8px] font-black uppercase bg-purple-50 text-purple-600 border border-purple-100"><i class="bi bi-people-fill"></i> Kelas</span>
                                                        <span class="text-[8px] text-gray-400 font-bold uppercase tracking-widest">{{ $first->user->department->name ?? 'Umum' }}</span>
                                                    @endif
                                                    
                                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[8px] font-black uppercase {{ $avgRating >= 4 ? 'bg-yellow-50 text-yellow-600 border border-yellow-100' : 'bg-red-50 text-red-600 border border-red-100' }}">
                                                        <i class="bi bi-star-fill"></i> {{ number_format($avgRating, 1) }}
                                                    </span>

                                                    @if($avgRating < 4)
                                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[8px] font-black uppercase bg-red-50 text-red-600 border border-red-100 animate-pulse">
                                                            <i class="bi bi-exclamation-triangle-fill"></i> Rawan
                                                        </span>
                                                    @endif

                                                    <span class="text-[9px] text-slate-300 mx-1">•</span>
                                                    <span class="text-[8px] text-gray-400 font-bold uppercase tracking-widest">{{ $first->created_at->format('d M, H:i') }}</span>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="px-6 py-5 text-left">
                                            <div class="flex items-center gap-4">
                                                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-black text-sm shadow-inner border border-emerald-100 flex-shrink-0">
                                                    <i class="bi {{ $iconBox }}"></i>
                                                </div>
                                                <div class="flex flex-col text-left overflow-hidden">
                                                    <span class="text-gray-800 font-black uppercase tracking-tight text-xs lg:text-sm truncate max-w-[150px] lg:max-w-xs">{{ $titleName }}</span>
                                                    <span class="text-[9px] font-bold text-gray-400 uppercase mt-1 tracking-widest">{{ $subName }} ({{ $totalQty }} Eksemplar)</span>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="px-6 py-5">
                                            <div class="flex flex-col text-left leading-tight">
                                                <span class="text-gray-500 font-bold text-[10px] uppercase mb-1">{{ $first->duration_amount }} {{ $first->duration_unit == 'hours' ? 'Jam' : 'Hari' }}</span>
                                                <span class="text-orange-600 font-black text-xs uppercase">{{ $deadline->translatedFormat('d M Y') }}</span>
                                                <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-1">Pukul {{ $deadline->format('H:i') }} WIB</span>
                                            </div>
                                        </td>

                                        <td class="px-6 py-5">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[8px] font-black uppercase tracking-widest border shadow-sm
                                                {{ $first->status === 'pending' ? 'bg-orange-50 text-orange-600 border-orange-100' : 'bg-blue-50 text-blue-600 border-blue-100' }}">
                                                {{ $first->status === 'pending' ? 'ANTREAN' : 'DIPINJAM' }}
                                            </span>
                                        </td>

                                        <td class="px-6 py-5 text-center">
                                            <div class="flex items-center justify-center gap-2 relative z-10">
                                                <button type="button" 
                                                    data-req="{{ json_encode($detailData) }}"
                                                    @click="selectedReq = JSON.parse($el.dataset.req); modalDetail = true" 
                                                    class="w-9 h-9 rounded-xl bg-gray-50 text-gray-400 hover:bg-emerald-50 hover:text-emerald-600 transition-all flex items-center justify-center shadow-sm border border-gray-100">
                                                    <i class="bi bi-eye-fill"></i>
                                                </button>
                                                
                                                @if($first->status === 'pending')
                                                    {{-- TOMBOL TERIMA (APPROVE) --}}
                                                    <button type="button" 
                                                        data-req="{{ json_encode($detailData) }}"
                                                        @click="masterKode = ''; kodeError = false; selectedReq = JSON.parse($el.dataset.req); actionType = 'approve'; actionRoute = '{{ route('staff.request.batch_action') }}'; modalAction = true" 
                                                        class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white transition-all shadow-sm flex items-center justify-center border border-emerald-100">
                                                        <i class="bi bi-check-lg text-lg"></i>
                                                    </button>
                                                    {{-- TOMBOL TOLAK (REJECT) --}}
                                                    <button type="button" 
                                                        data-req="{{ json_encode($detailData) }}"
                                                        @click="masterKode = ''; kodeError = false; selectedReq = JSON.parse($el.dataset.req); actionType = 'reject'; actionRoute = '{{ route('staff.request.batch_action') }}'; modalAction = true" 
                                                        class="w-9 h-9 rounded-xl bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition-all shadow-sm flex items-center justify-center border border-red-100">
                                                        <i class="bi bi-x-lg text-base"></i>
                                                    </button>
                                                @else
                                                    {{-- TOMBOL KEMBALIKAN BUKU --}}
                                                    <button type="button" 
                                                        data-req="{{ json_encode($detailData) }}"
                                                        @click="selectedReq = JSON.parse($el.dataset.req); actionRoute = '{{ route('staff.request.batch_return') }}'; modalReturn = true;" 
                                                        class="px-4 py-2 bg-emerald-600 text-white rounded-xl text-[9px] font-black uppercase tracking-widest shadow-lg shadow-emerald-100 hover:bg-emerald-700 active:scale-95 transition-all">
                                                        Selesaikan
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="5" class="py-20 text-center text-gray-300 font-bold uppercase text-[9px] tracking-[0.3em] italic">Perpustakaan sedang tidak memiliki sirkulasi aktif.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- TAB DATA DENDA --}}
                    <div x-show="activeTab === 'denda'" x-transition x-cloak class="flex flex-col h-full">
                        <div class="overflow-x-auto flex-1 custom-scroll">
                            <table class="w-full text-left text-sm">
                                <thead class="sticky top-0 bg-white z-10">
                                    <tr class="bg-gray-50/80 text-[9px] uppercase tracking-[0.2em] text-gray-400 font-black border-b border-gray-100 leading-none">
                                        <th class="px-6 py-5">Peminjam & Buku</th>
                                        <th class="px-6 py-5">Detail Insiden</th>
                                        <th class="px-6 py-5 text-right">Nominal Denda</th>
                                        <th class="px-6 py-5 text-center">Status</th>
                                        <th class="px-6 py-5 text-center">Aksi & Detail</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50 leading-tight">
                                    @forelse($requests->where('fine_amount', '>', 0) as $fine)
                                    @php
                                        $unit = $fine->duration_unit == 'hours' ? 'hours' : 'days';
                                        $deadline = $fine->created_at->copy()->add($unit, $fine->duration_amount);
                                        
                                        $avgRating = (float) ($fine->user_rating ?: 5.0);
                                        $trxRating = (float) ($fine->rating ?: 0);
                                        
                                        $detailDataDenda = [
                                            'is_paket' => false, 
                                            'items' => [[
                                                'barang' => $fine->item->name, 
                                                'jumlah' => $fine->quantity, 
                                                'kondisi' => $fine->return_condition, 
                                                'note_rusak' => $fine->return_note,
                                                'kode' => $fine->item->asset_code
                                            ]],
                                            'nama' => $fine->user->name, 
                                            'tipe' => $fine->user->role === 'student' ? 'Siswa Individu' : 'Perwakilan Kelas', 
                                            'sub_info' => $fine->user->role === 'student' ? ($fine->user->classRoom->name ?? '-') : ($fine->user->department->name ?? '-'),
                                            'tgl_pinjam' => $fine->created_at->format('d M Y, H:i'), 
                                            'tgl_kembali' => $fine->return_date ? \Carbon\Carbon::parse($fine->return_date)->format('d M Y, H:i') : '-',
                                            'catatan' => $fine->reason, 
                                            'total_denda' => number_format($fine->fine_amount, 0, ',', '.'), 
                                            'admin_note' => $fine->admin_note ?? 'Tidak ada catatan feedback.',
                                            'durasi' => $fine->duration_amount . ' ' . ($fine->duration_unit == 'hours' ? 'Jam' : 'Hari'),
                                            'tenggat' => $deadline->format('d M Y, H:i'),
                                            'avg_rating' => $avgRating,
                                            'trx_rating' => $trxRating,
                                            'status' => $fine->status,
                                            'item' => $fine->item->name,
                                            'asset_code' => $fine->item->asset_code,
                                            'isLate' => false,
                                            'id' => $fine->id,
                                            'denda_raw' => number_format($fine->fine_amount, 0, ',', '.')
                                        ];
                                    @endphp
                                    <tr class="group hover:bg-red-50/10 transition-all duration-200" 
                                        x-show="searchQuery === '' || '{{ strtolower($fine->user->name) }}'.includes(searchQuery.toLowerCase()) || '{{ strtolower($fine->item->name) }}'.includes(searchQuery.toLowerCase())">
                                        <td class="px-6 py-5">
                                            <div class="flex flex-col text-left">
                                                <span class="text-gray-900 font-black uppercase text-xs lg:text-sm tracking-tight truncate max-w-[150px]">{{ $fine->user->name }}</span>
                                                <div class="flex items-center gap-2 mt-1.5">
                                                    <span class="text-[8px] lg:text-[9px] font-black text-gray-500 uppercase tracking-widest bg-gray-50 px-2 py-0.5 rounded border border-gray-100">{{ $fine->item->name }}</span>
                                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[8px] font-black uppercase {{ $avgRating >= 4 ? 'bg-yellow-50 text-yellow-600 border border-yellow-100' : 'bg-red-50 text-red-600 border border-red-100' }}">
                                                        <i class="bi bi-star-fill"></i> {{ number_format($avgRating, 1) }}
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-5">
                                            <span class="text-red-600 font-bold text-[9px] lg:text-[10px] uppercase truncate max-w-[150px] block">{{ $fine->return_note ?? 'Tidak ada catatan' }}</span>
                                            <div class="mt-1 flex flex-wrap gap-1.5">
                                                <span class="px-2 py-0.5 rounded bg-red-100 text-red-600 text-[8px] font-black uppercase border border-red-200">{{ $fine->return_condition }}</span>
                                                @if($fine->lost_quantity > 0)
                                                    <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-600 text-[8px] font-black uppercase border border-slate-200">{{ $fine->lost_quantity }} Hilang</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-5 text-right">
                                            <span class="font-black text-red-600 bg-red-50 px-2.5 py-1.5 rounded-lg border border-red-100 text-xs lg:text-sm whitespace-nowrap">Rp {{ number_format($fine->fine_amount, 0, ',', '.') }}</span>
                                        </td>
                                        <td class="px-6 py-5 text-center">
                                            <span class="px-2.5 py-1 rounded-md text-[8px] font-black uppercase tracking-widest border shadow-sm
                                                {{ $fine->fine_status === 'paid' ? 'bg-emerald-50 text-emerald-600 border-emerald-200' : 'bg-red-50 text-red-600 border-red-200 animate-pulse' }}">
                                                {{ $fine->fine_status === 'paid' ? 'LUNAS' : 'BELUM LUNAS' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-5 text-center">
                                            <div class="flex items-center justify-center gap-2 relative z-10">
                                                <button type="button" 
                                                    data-req="{{ json_encode($detailDataDenda) }}"
                                                    @click="viewType = 'log'; selectedReq = JSON.parse($el.dataset.req); modalDetail = true" 
                                                    class="w-9 h-9 rounded-xl bg-gray-50 text-gray-400 hover:bg-emerald-50 hover:text-emerald-600 transition-all flex items-center justify-center shadow-sm border border-gray-100">
                                                    <i class="bi bi-eye-fill"></i>
                                                </button>

                                                @if($fine->fine_status === 'unpaid')
                                                    <button type="button" 
                                                        data-req="{{ json_encode($detailDataDenda) }}"
                                                        @click="selectedReq = JSON.parse($el.dataset.req); actionRoute = '/staff/request/' + selectedReq.id + '/paid'; modalPaid = true" 
                                                        class="px-3 py-2 bg-emerald-600 text-white rounded-xl text-[9px] font-black uppercase shadow-md hover:bg-emerald-700 transition-all active:scale-95 whitespace-nowrap">
                                                        Tandai Lunas
                                                    </button>
                                                @else
                                                    <span class="text-emerald-500 text-lg w-9 h-9 flex items-center justify-center"><i class="bi bi-check-circle-fill"></i></span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="5" class="py-20 text-center text-gray-300 font-bold uppercase text-[9px] tracking-[0.3em] italic">Tidak ada catatan denda.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- TAB ARSIP SIRKULASI --}}
                    @php
                        $groupedArchived = $requests->whereIn('status', ['returned', 'rejected'])->groupBy(function($item) {
                            return $item->user_id . '|' . $item->created_at->format('Y-m-d H:i') . '|' . $item->status;
                        });
                    @endphp
                    <div x-show="activeTab === 'riwayat'" x-transition x-cloak class="flex flex-col h-full">
                        <div class="overflow-x-auto flex-1 custom-scroll">
                            <table class="w-full text-left text-sm">
                                <thead class="sticky top-0 bg-white z-10">
                                    <tr class="bg-gray-50/80 text-[9px] uppercase tracking-[0.2em] text-gray-400 font-black border-b border-gray-100 leading-none">
                                        <th class="px-6 py-5">Timestamp Selesai</th>
                                        <th class="px-6 py-5">Identitas Peminjam</th>
                                        <th class="px-6 py-5 text-center">Status Final</th>
                                        <th class="px-6 py-5 text-center">Detail Paket</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50 leading-tight">
                                    @forelse($groupedArchived as $groupKey => $group)
                                    @php
                                        $first = $group->first();
                                        $itemCount = $group->count();
                                        $unit = $first->duration_unit == 'hours' ? 'hours' : 'days';
                                        $deadline = $first->created_at->copy()->add($unit, $first->duration_amount);
                                        
                                        $avgRating = (float) ($first->user_rating ?: 5.0);
                                        $trxRating = (float) ($first->rating ?: 0);
                                        
                                        $detailDataArsip = [
                                            'is_paket' => $itemCount > 1,
                                            'items' => $group->map(function($l) {
                                                return [
                                                    'barang' => $l->item->name,
                                                    'jumlah' => $l->quantity,
                                                    'kode' => $l->item->asset_code,
                                                    'kondisi' => $l->return_condition,
                                                    'denda' => $l->fine_amount,
                                                    'hilang' => $l->lost_quantity,
                                                    'note_rusak' => $l->return_note
                                                ];
                                            })->toArray(),
                                            'nama' => $first->user->name,
                                            'tipe' => $first->user->role === 'student' ? 'Siswa Individu' : 'Perwakilan Kelas',
                                            'sub_info' => $first->user->role === 'student' ? ($first->user->classRoom->name ?? '-') : ($first->user->department->name ?? '-'),
                                            'tgl_pinjam' => $first->created_at->format('d M Y, H:i'),
                                            'tgl_kembali' => $first->return_date ? \Carbon\Carbon::parse($first->return_date)->format('d M Y, H:i') : '-',
                                            'catatan' => $first->reason,
                                            'admin_note' => $first->admin_note ?? 'Tidak ada catatan feedback.',
                                            'durasi' => $first->duration_amount . ' ' . ($first->duration_unit == 'hours' ? 'Jam' : 'Hari'),
                                            'tenggat' => $deadline->format('d M Y, H:i'),
                                            'total_denda' => number_format($group->sum('fine_amount'), 0, ',', '.'),
                                            'avg_rating' => $avgRating,
                                            'trx_rating' => $trxRating,
                                            'status' => $first->status,
                                            'item' => $itemCount > 1 ? 'Paket Peminjaman' : $first->item->name,
                                            'asset_code' => $itemCount > 1 ? $itemCount.' Buku' : $first->item->asset_code,
                                            'isLate' => false
                                        ];
                                    @endphp
                                    <tr class="group hover:bg-gray-50/50 transition-all"
                                        x-show="searchQuery === '' || '{{ strtolower($first->user->name) }}'.includes(searchQuery.toLowerCase())">
                                        <td class="px-6 py-5 font-bold text-gray-400 text-[10px] lg:text-[11px] uppercase tracking-wider">{{ $first->updated_at->translatedFormat('d M Y, H:i') }} WIB</td>
                                        <td class="px-6 py-5">
                                            <div class="flex flex-col text-left">
                                                <span class="font-black text-gray-900 uppercase text-xs lg:text-sm tracking-tight leading-none mb-1.5">{{ $first->user->name }}</span>
                                                <div class="flex items-center gap-2">
                                                    @if($first->user->role === 'student')
                                                        <span class="text-[8px] font-black text-cyan-600 uppercase bg-cyan-50 px-1.5 py-0.5 rounded border border-cyan-100">Siswa</span>
                                                    @else
                                                        <span class="text-[8px] font-black text-purple-600 uppercase bg-purple-50 px-1.5 py-0.5 rounded border border-purple-100">Kelas</span>
                                                    @endif
                                                    <span class="text-[8px] font-black text-gray-500 uppercase tracking-widest border border-gray-200 px-1.5 py-0.5 rounded">{{ $itemCount > 1 ? 'Paket ' . $itemCount . ' Buku' : $first->item->name }}</span>
                                                    
                                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[8px] font-black uppercase {{ $avgRating >= 4 ? 'bg-yellow-50 text-yellow-600 border border-yellow-100' : 'bg-red-50 text-red-600 border border-red-100' }}">
                                                        <i class="bi bi-star-fill"></i> {{ number_format($avgRating, 1) }}
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-5 text-center">
                                            <span class="px-2.5 py-1 rounded-md text-[8px] font-black uppercase tracking-widest border shadow-sm {{ $first->status === 'returned' ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-red-50 text-red-600 border-red-100' }}">
                                                {{ $first->status === 'returned' ? 'SELESAI / KEMBALI' : 'DITOLAK' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-5 text-center">
                                            <button type="button" 
                                                data-req="{{ json_encode($detailDataArsip) }}"
                                                @click="viewType = 'log'; selectedReq = JSON.parse($el.dataset.req); modalDetail = true" 
                                                class="w-9 h-9 rounded-xl bg-gray-50 text-gray-400 hover:bg-emerald-50 hover:text-emerald-600 transition-all flex items-center justify-center shadow-sm border border-gray-100 mx-auto">
                                                <i class="bi bi-eye-fill"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="4" class="py-20 text-center text-gray-300 font-bold uppercase text-[9px] tracking-[0.3em] italic">Belum ada data arsip terekam.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    {{-- ====================== MODAL SECTION ====================== --}}
    
    {{-- 🛠️ 1. MODAL ACTION (ACC/REJECT BATCH) --}}
    <div x-show="modalAction" x-cloak class="fixed inset-0 z-[130] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="modalAction = false"></div>
        <div x-show="modalAction" x-transition.scale.95 class="relative w-full max-w-lg bg-white rounded-[2rem] shadow-2xl p-8 lg:p-10 border border-white text-left overflow-hidden leading-none">
            <div :class="actionType === 'approve' ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-red-600'" class="w-16 h-16 rounded-[1.2rem] flex items-center justify-center mx-auto mb-6 shadow-inner"><i :class="actionType === 'approve' ? 'bi-shield-check' : 'bi-shield-exclamation'" class="text-3xl"></i></div>
            <h3 class="text-2xl font-black text-gray-900 mb-2 uppercase tracking-tight text-center leading-none" x-text="actionType === 'approve' ? 'Konfirmasi Peminjaman' : 'Tolak Peminjaman'"></h3>
            <p class="text-xs text-gray-500 mb-4 font-medium text-center uppercase tracking-widest">Peminjam: <span class="font-black text-gray-900" x-text="selectedReq.nama"></span></p>
            
            <template x-if="selectedReq.avg_rating < 4">
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl flex items-center justify-center gap-3">
                    <i class="bi bi-exclamation-triangle-fill text-red-500 text-xl animate-pulse"></i>
                    <div class="text-left">
                        <p class="text-[9px] font-black text-red-600 uppercase tracking-widest">Peringatan Petugas!</p>
                        <p class="text-[10px] font-bold text-red-800">Rating peminjam ini sangat rendah (<span x-text="parseFloat(selectedReq.avg_rating).toFixed(1)"></span>/5.0). Harap periksa detail.</p>
                    </div>
                </div>
            </template>

            <form :action="actionRoute" method="POST" class="space-y-6" @submit.prevent="submitActionForm($event)">
                @csrf @method('PUT')
                
                <input type="hidden" name="action" :value="actionType">

                <div x-show="actionType === 'approve'" class="max-h-[30vh] overflow-y-auto custom-scroll pr-2 space-y-3">
                    
                    <div class="mb-6 p-4 rounded-2xl border transition-all duration-300" :class="kodeError ? 'bg-red-50/50 border-red-300 animate-shake' : 'bg-emerald-50/50 border-emerald-100'">
                        <label class="block text-[10px] font-black uppercase tracking-widest mb-2" :class="kodeError ? 'text-red-600' : 'text-emerald-600'"><i class="bi bi-key-fill me-1"></i> Kode Peminjaman (Wajib)</label>
                        
                        <input type="text" x-model="masterKode" @input="kodeError = false" placeholder="Contoh: X7B9Q2" :required="actionType === 'approve'" class="w-full px-4 py-3 bg-white border rounded-xl outline-none font-black text-sm text-center shadow-sm uppercase tracking-widest placeholder:text-gray-300 placeholder:font-normal transition-all" :class="kodeError ? 'border-red-400 text-red-600 focus:ring-red-500/20' : 'border-emerald-200 text-emerald-600 focus:ring-emerald-500/20'">
                        
                        <p x-show="!kodeError" class="text-[9px] text-emerald-500 mt-2 font-medium leading-tight">Minta siswa menyebutkan kode unik dari aplikasi mereka untuk memverifikasi pengambilan buku.</p>
                        
                        <div x-show="kodeError" x-transition class="mt-3 flex items-center gap-2 bg-red-100/70 p-2.5 rounded-lg border border-red-200">
                            <i class="bi bi-x-octagon-fill text-red-500 text-base"></i>
                            <span class="text-[10px] font-black text-red-600 uppercase tracking-widest">KODE SALAH! Silakan periksa kembali.</span>
                        </div>
                    </div>

                    <label class="block text-[9px] font-black text-emerald-500 uppercase tracking-widest mb-1 ml-1"><i class="bi bi-box-seam-fill me-1"></i> Verifikasi Jumlah (Sesuai Stok)</label>
                    <template x-for="(item, index) in selectedReq.items" :key="item.id">
                        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 flex items-center justify-between gap-4 group hover:border-emerald-200 transition-colors">
                            <div class="flex-1 text-left overflow-hidden">
                                <p class="text-xs font-black text-slate-800 uppercase truncate" x-text="item.barang"></p>
                                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1" x-text="'Stok Perpustakaan: ' + item.sisaStok + ' Buku'"></p>
                            </div>
                            <div class="w-24">
                                <input type="hidden" name="ids[]" :value="item.id">
                                <input type="hidden" :name="'loan_codes['+item.id+']'" :value="masterKode">
                                
                                <input type="number" :name="'approved_quantities['+item.id+']'" x-model="item.jumlah" :max="item.sisaStok" min="1" required class="w-full px-3 py-2.5 bg-white border border-emerald-200 rounded-xl outline-none font-black text-sm text-emerald-600 text-center focus:ring-2 focus:ring-emerald-500/20 shadow-sm transition-all">
                            </div>
                        </div>
                    </template>
                </div>

                <div x-show="actionType === 'reject'">
                    <template x-for="item in selectedReq.items" :key="item.id">
                        <input type="hidden" name="ids[]" :value="item.id">
                    </template>
                </div>

                <div class="text-left leading-none">
                    <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1" x-text="actionType === 'approve' ? 'Catatan Pengambilan (Opsional)' : 'Alasan Penolakan (Wajib)'"></label>
                    <textarea name="admin_note" :required="actionType === 'reject'" rows="3" class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl outline-none text-xs font-medium focus:ring-2 focus:ring-emerald-500/20 shadow-inner resize-none" placeholder="Tulis catatan di sini..."></textarea>
                </div>
                <div class="flex gap-4 pt-2">
                    <button type="button" @click="modalAction = false" class="flex-1 px-6 py-4 rounded-2xl bg-gray-100 text-slate-500 font-black text-[10px] uppercase tracking-widest hover:bg-gray-200 transition-colors">Batal</button>
                    <button type="submit" :class="actionType === 'approve' ? 'bg-emerald-600 shadow-emerald-100 hover:bg-emerald-700' : 'bg-red-500 shadow-red-100 hover:bg-red-600'" class="flex-1 px-6 py-4 rounded-2xl text-white font-black text-[10px] shadow-xl uppercase tracking-widest transition-all active:scale-95" x-text="actionType === 'approve' ? 'ACC SEKARANG' : 'KONFIRMASI TOLAK'"></button>
                </div>
            </form>
        </div>
    </div>

    {{-- 📦 2. MODAL KONFIRMASI KEMBALI (BATCH RETURN) --}}
    <div x-show="modalReturn" x-cloak class="fixed inset-0 z-[130] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="modalReturn = false"></div>
        <div x-show="modalReturn" x-transition.scale.95 class="relative w-full max-w-2xl bg-white rounded-[2rem] shadow-2xl p-8 lg:p-10 border border-white text-left overflow-y-auto max-h-[90vh] custom-scroll">
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-emerald-50 text-emerald-600 rounded-[1.2rem] flex items-center justify-center text-2xl shadow-inner border border-emerald-100"><i class="bi bi-box-arrow-in-down"></i></div>
                    <div class="text-left">
                        <h3 class="text-xl font-black text-gray-900 uppercase tracking-tight leading-none mb-1.5">Pengembalian Buku</h3>
                        <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest">Peminjam: <span class="text-emerald-600 font-black" x-text="selectedReq.nama"></span></p>
                    </div>
                </div>
                <button @click="modalReturn = false" class="w-8 h-8 rounded-full bg-gray-100 text-gray-500 hover:bg-red-100 hover:text-red-500 transition-colors flex items-center justify-center"><i class="bi bi-x-lg"></i></button>
            </div>
            
            <form :action="actionRoute" method="POST" class="space-y-6">
                @csrf @method('PUT')
                
                <div x-show="selectedReq.isLate" x-transition x-cloak class="mb-4 p-4 bg-orange-50 rounded-2xl border border-orange-200 flex items-start gap-3">
                    <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center text-orange-500 shadow-sm flex-shrink-0"><i class="bi bi-alarm-fill text-sm"></i></div>
                    <div class="text-left leading-none mt-1">
                        <h4 class="text-[9px] font-black text-orange-600 uppercase tracking-widest mb-1">Peringatan Terlambat!</h4>
                        <p class="text-[9px] text-orange-800 font-medium leading-relaxed">Pengembalian buku ini melewati batas waktu. Disarankan menurunkan Skor Rating sebagai sanksi keterlambatan.</p>
                    </div>
                </div>

                <div class="max-h-[45vh] overflow-y-auto custom-scroll pr-2 space-y-4">
                    <template x-for="(item, index) in selectedReq.items" :key="item.id">
                        <div class="bg-slate-50 p-5 rounded-2xl border border-slate-200 transition-all hover:border-emerald-200">
                            <div class="flex justify-between items-center mb-4">
                                <h4 class="font-black text-gray-900 text-xs uppercase" x-text="item.barang"></h4>
                                <span class="text-[9px] font-black bg-white px-2 py-1 border border-slate-200 rounded text-emerald-600 uppercase tracking-widest" x-text="item.jumlah + ' Buku'"></span>
                            </div>
                            
                            <input type="hidden" name="ids[]" :value="item.id">
                            
                            <div class="grid grid-cols-3 gap-2 mb-4">
                                <label class="cursor-pointer group">
                                    <input type="radio" :name="'returns['+item.id+'][condition]'" value="aman" class="peer hidden" @change="item.kondisi = 'aman'" checked>
                                    <div class="py-2.5 border border-slate-200 bg-white rounded-xl text-center peer-checked:border-emerald-500 peer-checked:bg-emerald-50 transition-all text-gray-400 peer-checked:text-emerald-600 shadow-sm">
                                        <i class="bi bi-shield-check block mb-1 text-sm"></i><span class="text-[8px] font-black uppercase tracking-widest">Aman</span>
                                    </div>
                                </label>
                                <label class="cursor-pointer group">
                                    <input type="radio" :name="'returns['+item.id+'][condition]'" value="rusak" class="peer hidden" @change="item.kondisi = 'rusak'">
                                    <div class="py-2.5 border border-slate-200 bg-white rounded-xl text-center peer-checked:border-orange-500 peer-checked:bg-orange-50 transition-all text-gray-400 peer-checked:text-orange-600 shadow-sm">
                                        <i class="bi bi-exclamation-triangle block mb-1 text-sm"></i><span class="text-[8px] font-black uppercase tracking-widest">Rusak</span>
                                    </div>
                                </label>
                                <label class="cursor-pointer group">
                                    <input type="radio" :name="'returns['+item.id+'][condition]'" value="hilang" class="peer hidden" @change="item.kondisi = 'hilang'">
                                    <div class="py-2.5 border border-slate-200 bg-white rounded-xl text-center peer-checked:border-red-500 peer-checked:bg-red-50 transition-all text-gray-400 peer-checked:text-red-600 shadow-sm">
                                        <i class="bi bi-question-circle block mb-1 text-sm"></i><span class="text-[8px] font-black uppercase tracking-widest">Hilang</span>
                                    </div>
                                </label>
                            </div>

                            <div x-show="item.kondisi !== 'aman'" x-transition class="space-y-3 bg-white p-4 rounded-xl border border-red-100 shadow-sm">
                                <input type="text" :name="'returns['+item.id+'][note]'" placeholder="Detail kerusakan/kehilangan buku..." class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg text-[10px] font-bold outline-none focus:border-red-300 transition-colors">
                                <div class="flex gap-3">
                                    <div class="w-1/3">
                                        <input type="number" :name="'returns['+item.id+'][lost_quantity]'" placeholder="Buku Error" :max="item.jumlah" min="1" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg text-[10px] font-bold outline-none focus:border-red-300 transition-colors text-center">
                                    </div>
                                    <div class="w-2/3 relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[10px] font-black text-gray-400">Rp</span>
                                        <input type="number" :name="'returns['+item.id+'][fine]'" placeholder="Nominal Denda" min="0" class="w-full pl-8 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-lg text-[10px] font-bold outline-none focus:border-red-300 transition-colors">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="bg-gray-50 p-5 rounded-2xl border border-gray-200">
                    <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1"><i class="bi bi-star-fill text-yellow-400 me-1"></i> Beri Rating Perilaku Peminjam</label>
                    <select name="rating" required class="w-full px-5 py-4 bg-white border border-gray-200 rounded-xl font-bold text-xs outline-none shadow-sm focus:ring-2 focus:ring-emerald-500/20 cursor-pointer appearance-none">
                        <option value="5">⭐⭐⭐⭐⭐ - Sangat Bertanggung Jawab</option>
                        <option value="4">⭐⭐⭐⭐ - Bagus & Tepat Waktu</option>
                        <option value="3">⭐⭐⭐ - Standar</option>
                        <option value="2">⭐⭐ - Buku Kurang Terawat / Agak Telat</option>
                        <option value="1">⭐ - Sangat Bermasalah</option>
                    </select>
                </div>

                <div class="flex gap-4 pt-2 leading-none">
                    <button type="submit" class="w-full py-4 rounded-2xl bg-emerald-600 text-white font-black text-[10px] shadow-xl shadow-emerald-100 uppercase tracking-[0.2em] active:scale-95 hover:bg-emerald-700 transition-all">Simpan & Tutup Sesi</button>
                </div>
            </form>
        </div>
    </div>

    {{-- 💸 3. MODAL KONFIRMASI LUNAS --}}
    <div x-show="modalPaid" x-cloak class="fixed inset-0 z-[130] flex items-center justify-center p-4 text-center">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="modalPaid = false"></div>
        <div x-show="modalPaid" x-transition.scale.95 class="relative w-full max-w-sm bg-white rounded-[2rem] shadow-2xl p-8 lg:p-10 border border-white text-center leading-none">
            <div class="w-16 h-16 bg-emerald-50 text-emerald-600 rounded-[1.2rem] flex items-center justify-center mx-auto mb-6 shadow-inner"><i class="bi bi-cash-stack text-2xl"></i></div>
            <h3 class="text-xl font-black text-gray-900 mb-2 uppercase tracking-tight">Konfirmasi Lunas?</h3>
            <p class="text-[10px] text-gray-500 mb-8 leading-relaxed font-bold uppercase tracking-widest">Denda <span class="font-black text-gray-900" x-text="selectedReq.nama"></span><br><span class="font-black text-emerald-600 text-sm mt-1 block" x-text="'Rp ' + selectedReq.denda_raw"></span></p>
            <form :action="actionRoute" method="POST" class="flex gap-3">
                @csrf @method('PUT')
                <button type="button" @click="modalPaid = false" class="flex-1 py-3 rounded-xl bg-gray-100 text-slate-500 font-black text-[9px] uppercase tracking-widest">Batal</button>
                <button type="submit" class="flex-1 py-3 rounded-xl bg-emerald-600 text-white font-black text-[9px] uppercase shadow-lg tracking-widest hover:bg-emerald-700">Ya, Lunas</button>
            </form>
        </div>
    </div>

    {{-- 👁️ 4. MODAL DETAIL LENGKAP --}}
    <div x-show="modalDetail" x-cloak class="fixed inset-0 z-[120] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-md" @click="modalDetail = false"></div>
        <div x-show="modalDetail" x-transition.scale.95 class="relative w-full max-w-xl bg-white rounded-[2rem] shadow-2xl p-8 lg:p-10 border border-white text-left leading-none overflow-y-auto max-h-[90vh] custom-scroll">
            <div class="mb-8 text-left leading-none flex justify-between items-start">
                <div>
                    <span class="text-[8px] font-black text-emerald-500 uppercase tracking-[0.2em] bg-emerald-50 px-2.5 py-1 rounded border border-emerald-100" x-text="selectedReq.tipe"></span>
                    <h3 class="text-2xl font-black text-gray-900 uppercase mt-3 tracking-tight leading-none" x-text="selectedReq.item"></h3>
                    <div class="flex items-center gap-2 mt-2">
                        <p class="text-[9px] font-black text-indigo-500 uppercase tracking-widest bg-indigo-50 px-2 py-0.5 rounded border border-indigo-100" x-text="'Afiliasi: ' + selectedReq.sub_info"></p>
                        <span :class="selectedReq.avg_rating >= 4 ? 'text-yellow-600 bg-yellow-50 border-yellow-100' : 'text-red-600 bg-red-50 border-red-100 animate-pulse'" class="text-[9px] font-bold px-2 py-0.5 rounded flex items-center gap-1 border shadow-sm">
                            <i class="bi bi-star-fill text-[8px]"></i> <span x-text="'RATA-RATA: ' + parseFloat(selectedReq.avg_rating).toFixed(1)"></span>
                        </span>
                    </div>
                </div>
                <button @click="modalDetail = false" class="w-8 h-8 rounded-full bg-gray-100 text-gray-500 hover:bg-red-100 hover:text-red-500 transition-colors flex items-center justify-center flex-shrink-0"><i class="bi bi-x-lg"></i></button>
            </div>
            
            <div class="space-y-6 text-left leading-none">
                
                <template x-if="selectedReq.avg_rating < 4">
                    <div class="p-4 bg-red-50 rounded-2xl border border-red-200 flex items-start gap-3 shadow-inner">
                        <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center text-red-500 shadow-sm flex-shrink-0"><i class="bi bi-exclamation-triangle-fill text-sm animate-pulse"></i></div>
                        <div class="text-left leading-none mt-1">
                            <h4 class="text-[9px] font-black text-red-600 uppercase tracking-widest mb-1">Peringatan: Rating Rendah!</h4>
                            <p class="text-[9px] text-red-800 font-bold leading-relaxed">Peminjam ini memiliki reputasi pengembalian yang kurang baik. Selalu periksa kelengkapan dan kondisi buku secara mendetail.</p>
                        </div>
                    </div>
                </template>

                <div class="grid grid-cols-2 gap-4">
                    <div class="p-5 bg-emerald-50 rounded-[1.5rem] border border-emerald-100">
                        <p class="text-[9px] font-black text-emerald-600 uppercase tracking-widest mb-1.5">Status Final</p>
                        <p class="text-sm font-black text-emerald-800 uppercase" x-text="selectedReq.status === 'returned' ? 'SELESAI' : (selectedReq.status === 'rejected' ? 'DITOLAK' : (selectedReq.status === 'pending' ? 'MENUNGGU' : 'DIPINJAM'))"></p>
                    </div>
                    <div class="p-5 bg-slate-50 rounded-[1.5rem] border border-slate-100">
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2">Rating Sesi Ini</p>
                        <div class="flex text-orange-400 text-xs">
                            <template x-if="parseInt(selectedReq.trx_rating) > 0">
                                <template x-for="i in parseInt(selectedReq.trx_rating)"><i class="bi bi-star-fill"></i></template>
                            </template>
                            <template x-if="parseInt(selectedReq.trx_rating) === 0">
                                <span class="text-gray-400 font-bold italic">Belum dinilai</span>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="p-6 bg-slate-50 rounded-[1.5rem] border border-slate-100 space-y-4">
                    <div class="flex items-center justify-between text-slate-800 font-black text-[10px] uppercase tracking-widest mb-2 border-b border-slate-200 pb-3">
                        <span><i class="bi bi-journals text-emerald-500 text-sm me-1"></i> Daftar Buku Dipinjam</span>
                        <span class="text-emerald-600 font-bold" x-text="selectedReq.is_paket ? selectedReq.items.length + ' Judul' : '1 Judul'"></span>
                    </div>
                    <div class="space-y-2 max-h-[150px] overflow-y-auto custom-scroll pr-1">
                        <template x-for="item in selectedReq.items" :key="item.id">
                            <div class="flex justify-between items-center bg-white p-3 rounded-xl shadow-sm border border-slate-100 hover:border-emerald-100 transition-colors">
                                <div class="text-left leading-none overflow-hidden pr-2">
                                    <p class="font-bold text-gray-900 text-xs uppercase truncate max-w-[200px] mb-1" x-text="item.barang"></p>
                                    <template x-if="item.kondisi && item.kondisi !== 'aman' && item.kondisi !== ''">
                                        <p class="text-[8px] font-bold text-red-500 mt-1 uppercase truncate max-w-[200px]" x-text="item.note_rusak || 'Rusak/Hilang'"></p>
                                    </template>
                                </div>
                                <div class="flex items-center gap-2 flex-shrink-0">
                                    <span class="text-[9px] font-black text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-100" x-text="item.jumlah + ' Buku'"></span>
                                    <template x-if="item.kondisi && item.kondisi !== 'aman' && item.kondisi !== ''">
                                        <span class="text-[8px] font-black uppercase bg-red-50 text-red-500 border border-red-100 px-2 py-0.5 rounded" x-text="item.kondisi"></span>
                                    </template>
                                    <template x-if="item.kondisi === 'aman'">
                                        <span class="text-[8px] font-black uppercase bg-emerald-50 text-emerald-600 border border-emerald-100 px-2 py-0.5 rounded">Aman</span>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="p-6 bg-white rounded-[1.5rem] border border-gray-100 shadow-sm space-y-4">
                    <div class="flex items-center gap-2 text-emerald-600 font-black text-[10px] uppercase tracking-widest mb-1">
                        <i class="bi bi-calendar-week-fill"></i> Timeline Sirkulasi
                    </div>
                    <div class="flex justify-between items-start relative mt-2">
                        <div class="absolute top-2 left-4 right-4 h-0.5 bg-gray-100 -z-10"></div>
                        <div class="flex flex-col items-center bg-white px-2">
                            <span class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Pinjam</span>
                            <span class="text-[10px] font-bold text-gray-900 bg-gray-50 border border-gray-100 px-2 py-1 rounded" x-text="selectedReq.tgl_pinjam"></span>
                        </div>
                        <div class="flex flex-col items-center bg-white px-2">
                            <span class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Deadline</span>
                            <span class="text-[10px] font-bold text-red-500 bg-red-50 border border-red-100 px-2 py-1 rounded" x-text="selectedReq.tenggat"></span>
                        </div>
                    </div>
                    <div class="text-center pt-2 mt-1 border-t border-slate-50">
                        <span class="px-3 py-1 bg-emerald-50 text-emerald-600 rounded-lg text-[9px] font-black uppercase tracking-widest border border-emerald-100" x-text="'Durasi: ' + selectedReq.durasi"></span>
                    </div>
                </div>

                <div x-show="selectedReq.tgl_kembali && selectedReq.tgl_kembali !== '-'" class="p-6 bg-emerald-50/50 rounded-[1.5rem] border border-emerald-100 text-left space-y-3">
                    <div class="flex justify-between items-center" :class="{'border-b border-emerald-100/50 pb-3' : selectedReq.total_denda && selectedReq.total_denda !== '0'}">
                        <span class="text-[9px] font-black text-emerald-600 uppercase tracking-widest">Dikembalikan:</span>
                        <span class="text-[10px] font-black text-emerald-900 bg-white px-2 py-1 rounded shadow-sm border border-emerald-100" x-text="selectedReq.tgl_kembali"></span>
                    </div>
                    <div x-show="selectedReq.total_denda && selectedReq.total_denda !== '0'" class="flex justify-between items-center pt-1">
                        <span class="text-[9px] font-black text-red-500 uppercase tracking-widest">Total Denda Paket</span>
                        <span class="text-sm font-black text-red-600 bg-white px-3 py-1 rounded-lg border border-red-100 shadow-sm" x-text="'Rp ' + selectedReq.total_denda"></span>
                    </div>
                </div>

                <div class="space-y-3">
                    <div class="p-5 bg-white border border-slate-100 rounded-2xl shadow-sm">
                        <p class="text-[8px] font-black text-slate-400 uppercase mb-1.5 tracking-widest">Alasan / Catatan Peminjam:</p>
                        <p class="text-[10px] text-slate-600 font-bold italic leading-relaxed" x-text="selectedReq.catatan || '-'"></p>
                    </div>
                    <div x-show="selectedReq.admin_note" class="p-5 bg-slate-900 border border-slate-800 rounded-2xl shadow-lg">
                        <p class="text-[8px] font-black text-emerald-400 uppercase mb-1.5 tracking-widest">Feedback Petugas:</p>
                        <p class="text-[10px] text-slate-300 font-bold leading-relaxed" x-text="selectedReq.admin_note"></p>
                    </div>
                </div>
            </div>

            <button @click="modalDetail = false" class="w-full mt-8 py-4 bg-slate-900 text-white rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] shadow-xl hover:bg-emerald-600 active:scale-95 transition-all border border-slate-800 hover:border-emerald-500">Tutup Panel Informasi</button>
        </div>
    </div>

</body>
</html>