<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Peminjaman Buku - Librify Akun Kelas</title>

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

{{-- ✅ PANGGIL FUNGSI SCRIPT DI SINI BIAR RAPI --}}
<body class="antialiased flex h-screen w-full overflow-hidden text-left font-jakarta" 
      x-data="studentRequest()">

    {{-- Sidebar Student --}}
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'" 
           class="fixed inset-y-0 left-0 z-50 flex flex-col w-72 bg-slate-950 text-white border-r border-slate-900 md:static md:flex-shrink-0 h-full transition-transform duration-300">
        @include('siswa.partials.sidebar') 
    </aside>

    <div class="flex flex-1 flex-col h-full min-w-0 overflow-hidden">
        {{-- Header Student --}}
        @include('siswa.partials.header')

        <main class="flex-1 overflow-y-auto p-6 lg:p-10 pt-2 custom-scroll">
            <div class="mx-auto w-full max-w-[1550px] space-y-8">
                
                {{-- Alert System --}}
                @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 8000)" class="bg-blue-600 text-white px-6 py-4 rounded-[2rem] shadow-lg flex justify-between items-center transition-all relative z-50">
                    <span class="font-bold text-sm uppercase tracking-widest leading-relaxed"><i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}</span>
                    <button @click="show = false"><i class="bi bi-x-lg"></i></button>
                </div>
                @endif

                @if(session('error') || $errors->any())
                <div x-data="{ show: true }" x-show="show" class="bg-red-500 text-white px-6 py-4 rounded-[2rem] shadow-lg flex justify-between items-center relative z-50">
                    <div class="flex flex-col">
                        <span class="font-bold text-sm uppercase tracking-widest mb-1"><i class="bi bi-exclamation-triangle-fill me-2"></i> Peringatan Sistem</span>
                        <div class="text-xs font-medium leading-relaxed opacity-90">
                            @if(session('error')) <p>{{ session('error') }}</p> @endif
                            @if($errors->any())
                                <ul class="list-disc list-inside">
                                    @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                    <button @click="show = false" class="ml-4 opacity-70 hover:opacity-100"><i class="bi bi-x-lg"></i></button>
                </div>
                @endif

                {{-- 1. HEADER PAGE --}}
                <div class="flex flex-col xl:flex-row xl:items-end justify-between gap-6">
                    <div class="text-left w-full xl:w-auto">
                        <div class="flex items-center gap-3 mb-4">
                            <h2 class="text-3xl font-black text-gray-900 tracking-tight uppercase leading-none">Peminjaman Buku Kelas</h2>
                            <span class="px-3 py-1.5 bg-blue-600 text-white text-[10px] font-black rounded-lg uppercase tracking-widest shadow-lg shadow-blue-100">
                                Akun Perwakilan Kelas
                            </span>
                        </div>
                        <div class="flex bg-gray-200/50 p-1.5 rounded-2xl gap-1 w-fit border border-gray-100">
                            <button @click="activeTab = 'booking'; searchQuery = ''" :class="activeTab === 'booking' ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-500 hover:text-blue-500'" class="px-8 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">Sirkulasi Aktif</button>
                            <button @click="activeTab = 'denda'; searchQuery = ''" :class="activeTab === 'denda' ? 'bg-white text-red-600 shadow-sm' : 'text-slate-500 hover:text-red-500'" class="px-8 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all flex items-center gap-2">
                                Tanggungan Denda
                                @if($myLoans->where('fine_status', 'unpaid')->count() > 0)
                                    <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
                                @endif
                            </button>
                            <button @click="activeTab = 'riwayat'; searchQuery = ''" :class="activeTab === 'riwayat' ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-500 hover:text-blue-500'" class="px-8 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">Riwayat Selesai</button>
                        </div>
                    </div>
                    
                    <div class="flex flex-col md:flex-row items-center gap-4 w-full xl:w-auto">
                        <div class="relative w-full md:w-80 group">
                            <i class="bi bi-search absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-blue-600 transition-colors"></i>
                            <input type="text" x-model="searchQuery" placeholder="Cari judul buku, status..." class="w-full pl-12 pr-6 py-4 bg-white border border-gray-100 rounded-[1.5rem] outline-none font-bold text-xs shadow-sm focus:ring-4 focus:ring-blue-500/10 transition-all placeholder:text-gray-300">
                        </div>

                        <template x-if="activeTab === 'booking'">
                            @if($hasActiveLoan)
                                <button disabled class="w-full md:w-auto inline-flex items-center justify-center gap-3 rounded-[1.5rem] bg-slate-200 px-8 py-4 text-xs font-black text-slate-500 cursor-not-allowed uppercase tracking-widest leading-none flex-shrink-0 border border-slate-300" title="Anda masih memiliki tanggungan peminjaman.">
                                    <i class="bi bi-lock-fill text-lg"></i>
                                    <span>Selesaikan Pinjaman</span>
                                </button>
                            @else
                                <button @click="modalBooking = true; cart = [{ item_id: '', quantity: 1 }]" class="w-full md:w-auto inline-flex items-center justify-center gap-3 rounded-[1.5rem] bg-blue-600 px-8 py-4 text-xs font-black text-white shadow-xl shadow-blue-100 hover:bg-blue-700 transition-all active:scale-95 uppercase tracking-widest leading-none flex-shrink-0">
                                    <i class="bi bi-plus-circle-fill text-lg"></i>
                                    <span>Pinjam Buku</span>
                                </button>
                            @endif
                        </template>
                    </div>
                </div>

                {{-- 2. TAB CONTENT: BOOKING ALAT --}}
                <div x-show="activeTab === 'booking'" x-transition>
                    
                    {{-- STATS SUMMARY --}}
                    @php
                        // Logika Grouping Berdasarkan Waktu dan Status
                        $groupedLoans = $myLoans->groupBy(function($item) {
                            return $item->created_at->format('Y-m-d H:i:s') . '|' . $item->status;
                        });
                        $activeGroups = $groupedLoans->filter(function($group) {
                            return in_array($group->first()->status, ['pending', 'approved', 'borrowed']);
                        });
                    @endphp

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <div class="bg-white p-6 rounded-[2.5rem] border border-gray-100 shadow-sm flex items-center gap-5">
                            <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center text-2xl shadow-inner"><i class="bi bi-collection"></i></div>
                            <div class="text-left leading-none">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Sirkulasi Aktif</p>
                                <p class="text-3xl font-black text-gray-900 leading-none">{{ $activeGroups->count() }} <span class="text-[10px] font-bold text-gray-400 uppercase">Paket</span></p>
                            </div>
                        </div>
                        <div class="bg-white p-6 rounded-[2.5rem] border border-gray-100 shadow-sm flex items-center gap-5 text-left">
                            <div class="w-14 h-14 bg-orange-50 text-orange-600 rounded-2xl flex items-center justify-center text-2xl shadow-inner"><i class="bi bi-hourglass-split"></i></div>
                            <div class="text-left leading-none">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Menunggu Verifikasi</p>
                                <p class="text-3xl font-black text-gray-900 leading-none">{{ $myLoans->where('status', 'pending')->count() }} <span class="text-[10px] font-bold text-gray-400 uppercase">Buku</span></p>
                            </div>
                        </div>
                        <div class="bg-slate-900 p-6 rounded-[2.5rem] shadow-xl flex items-center gap-5 text-white text-left border border-slate-800 relative overflow-hidden">
                            <div class="absolute -right-4 -top-4 w-20 h-20 bg-blue-500/10 rounded-full"></div>
                            <div class="w-14 h-14 bg-white/10 text-blue-400 rounded-2xl flex items-center justify-center text-2xl shadow-inner"><i class="bi bi-person-badge-fill"></i></div>
                            <div class="text-left leading-none relative z-10">
                                <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Akun Kelas</p>
                                <p class="text-lg font-black uppercase leading-none tracking-tight">{{ Auth::user()->name }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-[2.5rem] bg-white shadow-sm border border-gray-100 overflow-hidden text-left">
                        <div class="p-8 border-b border-gray-50 flex items-center justify-between bg-gray-50/30">
                            <h3 class="text-[10px] font-black text-gray-800 uppercase tracking-[0.2em]">Monitoring Peminjaman Kelas</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <thead>
                                    <tr class="text-[10px] uppercase tracking-[0.2em] text-gray-400 font-black bg-white">
                                        <th class="px-8 py-6">Detail Peminjaman</th>
                                        <th class="px-8 py-6">Kuantitas Total</th>
                                        <th class="px-8 py-6">Jadwal & Durasi</th>
                                        <th class="px-8 py-6">Status</th>
                                        <th class="px-8 py-6 text-center">Opsi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @forelse($activeGroups as $groupKey => $group)
                                    @php
                                        $first = $group->first();
                                        $itemCount = $group->count();
                                        $totalQty = $group->sum('quantity');
                                        
                                        $unit = $first->duration_unit == 'hours' ? 'hours' : 'days';
                                        $deadline = $first->created_at->copy()->add($unit, $first->duration_amount);
                                        $isOverdue = now() > $deadline && in_array($first->status, ['approved', 'borrowed']);
                                        
                                        $titleName = $itemCount > 1 ? 'Paket Peminjaman' : $first->item->name;
                                        $subName = $itemCount > 1 ? $itemCount . ' Judul Buku Terlampir' : $first->item->asset_code;
                                        $iconBox = $itemCount > 1 ? 'bi-journals' : 'bi-book';
                                        $loanCode = $first->loan_code ?? '-';
                                        $searchString = strtolower($group->pluck('item.name')->implode(' ') . ' ' . $first->status . ' paket');
                                    @endphp
                                    <tr class="group hover:bg-gray-50/50 transition-all duration-200" 
                                        x-show="searchQuery === '' || '{{ $searchString }}'.includes(searchQuery.toLowerCase())">
                                        <td class="px-8 py-5 text-left">
                                            <div class="flex items-center gap-4">
                                                <div class="w-11 h-11 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg shadow-inner"><i class="{{ $iconBox }}"></i></div>
                                                <div class="text-left leading-tight">
                                                    <p class="text-gray-900 font-black text-base uppercase tracking-tight">{{ $titleName }}</p>
                                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">{{ $subName }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-8 py-5">
                                            <span class="font-black text-gray-600 uppercase text-xs">{{ $totalQty }} Buku</span>
                                        </td>
                                        <td class="px-8 py-5 text-left">
                                            <div class="flex flex-col">
                                                <div class="flex items-center gap-2">
                                                    <i class="bi bi-calendar-event text-gray-300"></i>
                                                    <span class="text-xs font-black text-gray-900">{{ $first->created_at->format('d M Y') }}</span>
                                                    <span class="text-[10px] text-gray-400 font-bold">{{ $first->created_at->format('H:i') }}</span>
                                                </div>
                                                <div class="flex items-center gap-1.5 mt-1.5">
                                                    <span class="px-2 py-0.5 rounded bg-gray-100 text-gray-600 text-[9px] font-black uppercase tracking-wider">
                                                        {{ $first->duration_amount }} {{ $first->duration_unit == 'hours' ? 'Jam' : 'Hari' }}
                                                    </span>
                                                    <i class="bi bi-arrow-right text-[9px] text-gray-300"></i>
                                                    <span class="text-[10px] font-bold {{ $isOverdue ? 'text-red-600 animate-pulse' : 'text-blue-600' }} uppercase">
                                                        Batas: {{ $deadline->format('d M, H:i') }}
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-8 py-5">
                                            @php
                                                $statusClass = match($first->status) {
                                                    'pending'  => 'bg-orange-50 text-orange-600 border border-orange-100',
                                                    'approved' => 'bg-blue-50 text-blue-600 border border-blue-100',
                                                    'borrowed' => 'bg-blue-50 text-blue-600 border border-blue-100',
                                                    default    => 'bg-slate-50 text-slate-500'
                                                };
                                                $statusTextLabel = match($first->status) {
                                                    'pending'  => 'Menunggu',
                                                    'approved' => 'Dipinjam',
                                                    'borrowed' => 'Dipinjam',
                                                    default    => 'Unknown'
                                                };
                                            @endphp
                                            <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest {{ $statusClass }}">
                                                {{ $statusTextLabel }}
                                            </span>
                                            
                                            @if($first->status === 'pending' && !empty($first->loan_code))
                                                <div class="mt-2 text-[9px] font-black text-blue-700 bg-blue-50 border border-blue-200 px-2 py-1 rounded w-fit tracking-widest" title="Berikan kode ini ke Petugas Perpustakaan">
                                                    PIN: {{ $first->loan_code }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-8 py-5 text-center">
                                            <button @click="selectedRequest = {
                                                is_paket: {{ $itemCount > 1 ? 'true' : 'false' }},
                                                items: {{ $group->map(fn($l) => [
                                                    'name' => $l->item->name, 
                                                    'qty' => $l->quantity, 
                                                    'code' => $l->item->asset_code,
                                                    'kondisi' => $l->return_condition,
                                                    'denda' => $l->fine_amount,
                                                    'hilang' => $l->lost_quantity,
                                                    'note_rusak' => $l->return_note
                                                ])->toJson() }},
                                                status: '{{ $first->status }}',
                                                tgl: '{{ $first->created_at->format('d M Y, H:i') }}',
                                                durasi: '{{ $first->duration_amount }} {{ $first->duration_unit == 'hours' ? 'Jam' : 'Hari' }}',
                                                tenggat: '{{ $deadline->format('d M Y, H:i') }}',
                                                note: '{{ $first->reason }}',
                                                admin_note: '{{ $first->admin_note ?? 'Belum ada catatan dari Petugas.' }}',
                                                tgl_kembali: '-',
                                                total_denda: '0',
                                                kode_peminjaman: '{{ $loanCode }}',
                                                is_arsip: false
                                            }; modalStatus = true" class="w-10 h-10 rounded-xl bg-gray-50 text-gray-400 hover:bg-blue-600 hover:text-white transition-all mx-auto flex items-center justify-center shadow-sm border border-gray-100">
                                                <i class="bi bi-info-circle-fill"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="py-20 text-center">
                                            <div class="text-slate-200 text-6xl mb-4"><i class="bi bi-inbox"></i></div>
                                            <p class="text-gray-400 font-bold uppercase text-[10px] tracking-widest">Belum ada peminjaman aktif</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- 3. TAB CONTENT: TABEL DENDA --}}
                <div x-show="activeTab === 'denda'" x-transition x-cloak>
                    <div class="rounded-[2.5rem] bg-white shadow-sm border border-red-100 overflow-hidden text-left">
                        <div class="p-8 border-b border-red-50 flex items-center justify-between bg-red-50/30">
                            <h3 class="text-[10px] font-black text-red-600 uppercase tracking-[0.2em]">Rekapitulasi Tagihan Denda</h3>
                            <span class="text-[10px] font-black text-red-400 uppercase tracking-widest">Total Belum Bayar: Rp {{ number_format($myLoans->where('fine_status', 'unpaid')->sum('fine_amount'), 0, ',', '.') }}</span>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <thead>
                                    <tr class="text-[10px] uppercase tracking-[0.2em] text-gray-400 font-black bg-white">
                                        <th class="px-8 py-6">Judul Buku & Info</th>
                                        <th class="px-8 py-6">Detail Insiden</th>
                                        <th class="px-8 py-6 text-right">Nominal Denda</th>
                                        <th class="px-8 py-6 text-center">Status Pembayaran</th>
                                        <th class="px-8 py-6 text-center">Detail</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-red-50">
                                    @forelse($myLoans->where('fine_amount', '>', 0) as $fine)
                                    @php
                                        $unit = $fine->duration_unit == 'hours' ? 'hours' : 'days';
                                        $deadline = $fine->created_at->copy()->add($unit, $fine->duration_amount);
                                    @endphp
                                    <tr class="group hover:bg-red-50/30 transition-all duration-200" 
                                        x-show="searchQuery === '' || '{{ strtolower($fine->item->name) }}'.includes(searchQuery.toLowerCase()) || '{{ strtolower($fine->return_note ?? '') }}'.includes(searchQuery.toLowerCase())">
                                        <td class="px-8 py-5">
                                            <p class="text-gray-900 font-black uppercase text-sm mb-1">{{ $fine->item->name }}</p>
                                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Kembali: {{ $fine->return_date ? \Carbon\Carbon::parse($fine->return_date)->format('d M Y') : '-' }}</p>
                                        </td>
                                        <td class="px-8 py-5">
                                            <span class="text-red-600 font-bold text-xs uppercase">{{ $fine->return_note ?? 'Tidak ada catatan' }}</span>
                                            <div class="mt-1 flex flex-wrap gap-1.5">
                                                <span class="px-2 py-0.5 rounded bg-red-100 text-red-600 text-[8px] font-black uppercase border border-red-200">{{ $fine->return_condition }}</span>
                                                @if($fine->lost_quantity > 0)
                                                    <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-600 text-[8px] font-black uppercase border border-slate-200">{{ $fine->lost_quantity }} Buku {{ ucfirst($fine->return_condition) }}</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-8 py-5 text-right">
                                            <span class="font-black text-red-600 bg-red-50 px-3 py-1.5 rounded-lg border border-red-100 text-sm whitespace-nowrap">Rp {{ number_format($fine->fine_amount, 0, ',', '.') }}</span>
                                        </td>
                                        <td class="px-8 py-5 text-center">
                                            <span class="px-3 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest 
                                                {{ $fine->fine_status === 'paid' ? 'bg-emerald-100 text-emerald-600 border border-emerald-200' : 'bg-red-100 text-red-600 border border-red-200 animate-pulse' }}">
                                                {{ $fine->fine_status === 'paid' ? 'LUNAS' : 'BELUM LUNAS' }}
                                            </span>
                                        </td>
                                        <td class="px-8 py-5 text-center">
                                            <button @click="selectedRequest = {
                                                is_paket: false,
                                                items: [{
                                                    name: '{{ $fine->item->name }}',
                                                    qty: '{{ $fine->quantity }}',
                                                    code: '{{ $fine->item->asset_code }}',
                                                    kondisi: '{{ $fine->return_condition }}',
                                                    denda: '{{ $fine->fine_amount }}',
                                                    hilang: '{{ $fine->lost_quantity }}',
                                                    note_rusak: '{{ $fine->return_note }}'
                                                }],
                                                status: '{{ $fine->status }}',
                                                tgl: '{{ $fine->created_at->format('d M Y, H:i') }}',
                                                durasi: '{{ $fine->duration_amount }} {{ $fine->duration_unit == 'hours' ? 'Jam' : 'Hari' }}',
                                                tenggat: '{{ $deadline->format('d M Y, H:i') }}',
                                                note: '{{ $fine->reason }}',
                                                admin_note: '{{ $fine->admin_note ?? 'Belum ada catatan.' }}',
                                                tgl_kembali: '{{ $fine->return_date ? \Carbon\Carbon::parse($fine->return_date)->format('d M Y, H:i') : '-' }}',
                                                total_denda: '{{ number_format($fine->fine_amount, 0, ',', '.') }}',
                                                kode_peminjaman: '-',
                                                is_arsip: true
                                            }; modalStatus = true" class="w-10 h-10 rounded-xl bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition-all mx-auto flex items-center justify-center shadow-sm border border-red-100">
                                                <i class="bi bi-eye-fill"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="py-20 text-center">
                                            <div class="text-emerald-200 text-6xl mb-4"><i class="bi bi-check-circle"></i></div>
                                            <p class="text-gray-400 font-bold uppercase text-[10px] tracking-widest">Aman! Tidak ada tagihan denda.</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- ✅ 4. TAB CONTENT: TABEL RIWAYAT SELESAI / ARSIP --}}
                <div x-show="activeTab === 'riwayat'" x-transition x-cloak>
                    @php
                        $groupedArchived = $myLoans->groupBy(function($item) {
                            return $item->created_at->format('Y-m-d H:i:s') . '|' . $item->status;
                        });
                        $arsipGroups = $groupedArchived->filter(function($group) {
                            return in_array($group->first()->status, ['returned', 'rejected']);
                        });
                    @endphp
                    <div class="rounded-[2.5rem] bg-white shadow-sm border border-gray-100 overflow-hidden text-left">
                        <div class="p-8 border-b border-gray-50 flex items-center justify-between bg-gray-50/30">
                            <h3 class="text-[10px] font-black text-gray-800 uppercase tracking-[0.2em]"><i class="bi bi-archive me-2 text-emerald-600"></i> Riwayat Sirkulasi Selesai</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <thead>
                                    <tr class="text-[10px] uppercase tracking-[0.2em] text-gray-400 font-black bg-white">
                                        <th class="px-8 py-6">Detail Peminjaman</th>
                                        <th class="px-8 py-6">Tgl Selesai</th>
                                        <th class="px-8 py-6 text-center">Status</th>
                                        <th class="px-8 py-6 text-center">Detail</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @forelse($arsipGroups as $groupKey => $group)
                                    @php
                                        $first = $group->first();
                                        $itemCount = $group->count();
                                        $titleName = $itemCount > 1 ? 'Paket Peminjaman' : $first->item->name;
                                        $iconBox = $itemCount > 1 ? 'bi-journals' : 'bi-book';
                                        
                                        $unit = $first->duration_unit == 'hours' ? 'hours' : 'days';
                                        $deadline = $first->created_at->copy()->add($unit, $first->duration_amount);
                                        
                                        // Cek apakah ada buku rusak/hilang di dalam grup ini
                                        $hasIncident = $group->contains(function ($val) {
                                            return in_array($val->return_condition, ['rusak', 'hilang']) || $val->lost_quantity > 0 || $val->fine_amount > 0;
                                        });

                                        $searchString = strtolower($group->pluck('item.name')->implode(' ') . ' ' . $first->status);
                                    @endphp
                                    <tr class="group hover:bg-gray-50/50 transition-all duration-200" 
                                        x-show="searchQuery === '' || '{{ $searchString }}'.includes(searchQuery.toLowerCase())">
                                        <td class="px-8 py-5 text-left">
                                            <div class="flex items-center gap-4">
                                                <div class="w-11 h-11 rounded-2xl bg-gray-100 text-gray-500 flex items-center justify-center text-lg shadow-inner uppercase"><i class="{{ $iconBox }}"></i></div>
                                                <div class="text-left leading-tight">
                                                    <p class="text-gray-900 font-black text-base uppercase tracking-tight">{{ $titleName }}</p>
                                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">{{ $itemCount }} Buku</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-8 py-5">
                                            <span class="text-gray-900 font-black text-xs uppercase">{{ $first->updated_at->format('d M Y') }}</span>
                                        </td>
                                        <td class="px-8 py-5 text-center">
                                            <div class="flex flex-col items-center justify-center gap-1.5">
                                                <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest border shadow-sm {{ $first->status === 'returned' ? 'bg-emerald-50 text-emerald-600 border-emerald-200' : 'bg-red-50 text-red-600 border-red-200' }}">
                                                    {{ $first->status === 'returned' ? 'SELESAI' : 'DITOLAK' }}
                                                </span>
                                                
                                                @if($first->status === 'returned' && $hasIncident)
                                                    <span class="text-[8px] font-black text-red-500 bg-red-50 border border-red-100 px-2 py-0.5 rounded uppercase tracking-widest flex items-center gap-1">
                                                        <i class="bi bi-exclamation-triangle-fill"></i> Ada Insiden
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-8 py-5 text-center">
                                            <button @click="selectedRequest = {
                                                is_paket: {{ $itemCount > 1 ? 'true' : 'false' }},
                                                items: {{ $group->map(fn($l) => [
                                                    'name' => $l->item->name, 
                                                    'qty' => $l->quantity, 
                                                    'code' => $l->item->asset_code,
                                                    'kondisi' => $l->return_condition,
                                                    'denda' => $l->fine_amount,
                                                    'hilang' => $l->lost_quantity,
                                                    'note_rusak' => $l->return_note
                                                ])->toJson() }},
                                                status: '{{ $first->status }}',
                                                tgl: '{{ $first->created_at->format('d M Y, H:i') }}',
                                                durasi: '{{ $first->duration_amount }} {{ $first->duration_unit == 'hours' ? 'Jam' : 'Hari' }}',
                                                tenggat: '{{ $deadline->format('d M Y, H:i') }}',
                                                note: '{{ $first->reason }}',
                                                admin_note: '{{ $first->admin_note ?? 'Belum ada catatan dari Petugas.' }}',
                                                tgl_kembali: '{{ $first->return_date ? \Carbon\Carbon::parse($first->return_date)->format('d M Y, H:i') : '-' }}',
                                                total_denda: '{{ number_format($group->sum('fine_amount'), 0, ',', '.') }}',
                                                kode_peminjaman: '-',
                                                is_arsip: true
                                            }; modalStatus = true" class="w-10 h-10 rounded-xl bg-gray-50 text-gray-400 hover:bg-blue-600 hover:text-white transition-all mx-auto flex items-center justify-center shadow-sm border border-gray-100">
                                                <i class="bi bi-eye-fill"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="py-20 text-center">
                                            <div class="text-slate-200 text-6xl mb-4"><i class="bi bi-inbox"></i></div>
                                            <p class="text-gray-400 font-bold uppercase text-[10px] tracking-widest">Belum ada riwayat arsip</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>

    {{-- MODAL BOOKING BUKU (CART SYSTEM + PENCARIAN BUKU) --}}
    <div x-show="modalBooking" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-md" @click="modalBooking = false"></div>
        <div x-show="modalBooking" x-transition.scale.95 class="relative w-full max-w-3xl bg-white rounded-[3rem] shadow-2xl p-10 border border-white text-left overflow-y-auto max-h-[90vh] custom-scroll">
            <div class="mb-8 text-left leading-none flex justify-between items-start">
                <div>
                    <h3 class="text-3xl font-black text-gray-900 font-jakarta uppercase tracking-tight leading-none mb-4">Pinjam Buku</h3>
                    <span class="text-[9px] font-black text-blue-500 uppercase tracking-[0.2em] bg-blue-50 px-2 py-1 rounded">Katalog Perpustakaan</span>
                </div>
                <button @click="modalBooking = false" class="w-10 h-10 rounded-full bg-gray-100 text-gray-500 hover:bg-red-100 hover:text-red-500 transition-colors flex items-center justify-center"><i class="bi bi-x-lg"></i></button>
            </div>
            
            <form action="{{ route('siswa.request.store') }}" method="POST" class="space-y-6 text-left">
                @csrf

                {{-- FILTER & SEARCH PANEL (DI DALAM MODAL) --}}
                <div class="bg-blue-50/50 p-5 rounded-[2rem] border border-blue-100 flex flex-col md:flex-row gap-4 items-center">
                    <div class="relative w-full md:w-1/2">
                        <i class="bi bi-search absolute left-5 top-1/2 -translate-y-1/2 text-blue-500"></i>
                        <input type="text" x-model="searchBook" placeholder="Cari judul / penulis buku..." class="w-full pl-12 pr-5 py-3.5 bg-white border border-blue-100 rounded-xl outline-none font-bold text-xs text-gray-700 focus:ring-2 focus:ring-blue-500/20 shadow-sm">
                    </div>
                    <div class="relative w-full md:w-1/2">
                        <select x-model="filterCategory" class="w-full px-5 py-3.5 bg-white border border-blue-100 rounded-xl outline-none font-bold text-xs text-gray-700 focus:ring-2 focus:ring-blue-500/20 appearance-none cursor-pointer shadow-sm">
                            <option value="">Semua Kategori/Genre</option>
                            <template x-for="cat in availableCategories" :key="cat">
                                <option :value="cat" x-text="cat"></option>
                            </template>
                        </select>
                        <i class="bi bi-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-[10px]"></i>
                    </div>
                </div>

                {{-- LOOPING CART BARANG --}}
                <div class="space-y-4 bg-slate-50/50 p-6 rounded-[2rem] border border-slate-100">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1"><i class="bi bi-cart3 me-1"></i> Keranjang Peminjaman</label>
                    
                    <template x-for="(item, index) in cart" :key="index">
                        <div class="flex gap-3 items-end bg-white p-4 rounded-2xl border border-slate-200 relative group transition-all shadow-sm">
                            
                            <div class="flex-1 text-left leading-none">
                                <label class="block text-[9px] font-bold text-gray-500 uppercase tracking-widest mb-2 ml-1">Pilih Buku</label>
                                <select :name="'items['+index+'][item_id]'" x-model="item.item_id" required class="w-full px-4 py-3.5 bg-gray-50 border border-gray-100 rounded-xl outline-none font-black text-xs text-gray-800 focus:ring-2 focus:ring-blue-500/10 transition-all appearance-none cursor-pointer truncate">
                                    <option value="">-- Pilih Buku Dari Katalog --</option>
                                    <template x-for="buku in filteredBooks" :key="buku.id">
                                        <option :value="buku.id" x-text="buku.name + ' (Sisa: ' + buku.stock + ')'"></option>
                                    </template>
                                </select>
                            </div>

                            {{-- TOMBOL DETAIL BUKU (HANYA AKTIF JIKA ADA BUKU YANG DIPILIH) --}}
                            <button type="button" @click="openBookDetail(item.item_id)" :disabled="!item.item_id" class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 border border-indigo-100 flex items-center justify-center disabled:opacity-40 disabled:cursor-not-allowed hover:bg-indigo-600 hover:text-white transition-colors flex-shrink-0" title="Lihat Detail Buku">
                                <i class="bi bi-info-circle-fill text-lg"></i>
                            </button>
                            
                            <div class="w-20 text-left leading-none">
                                <label class="block text-[9px] font-bold text-gray-500 uppercase tracking-widest mb-2 text-center">Jumlah</label>
                                <input type="number" :name="'items['+index+'][quantity]'" x-model="item.quantity" min="1" required class="w-full px-3 py-3.5 bg-gray-50 border border-gray-100 rounded-xl outline-none font-black text-sm text-gray-800 text-center focus:ring-2 focus:ring-blue-500/10 transition-all">
                            </div>
                            
                            <button type="button" @click="removeItem(index)" x-show="cart.length > 1" class="w-12 h-12 bg-red-50 border border-red-100 text-red-500 rounded-xl flex items-center justify-center hover:bg-red-500 hover:text-white transition-colors flex-shrink-0" title="Hapus Dari Keranjang">
                                <i class="bi bi-trash3-fill text-lg"></i>
                            </button>
                        </div>
                    </template>

                    <button type="button" @click="addItem()" class="w-full py-4 border-2 border-dashed border-blue-200 text-blue-600 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-blue-50 transition-colors flex items-center justify-center gap-2">
                        <i class="bi bi-plus-lg text-sm"></i> Tambah Buku Lain Ke Keranjang
                    </button>
                </div>

                <hr class="border-gray-100 my-2">

                {{-- ESTIMASI DURASI & ALASAN --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 leading-none">
                    <div class="text-left" x-data="{ mode: 'days' }">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3"><i class="bi bi-clock-history me-1"></i> Estimasi Durasi Peminjaman</label>
                        
                        <div class="flex bg-gray-100 p-1 rounded-2xl mb-3">
                            <button type="button" @click="mode = 'hours'" :class="mode === 'hours' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-400 hover:text-gray-600'" class="flex-1 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all"><i class="bi bi-clock mr-1"></i> Per Jam</button>
                            <button type="button" @click="mode = 'days'" :class="mode === 'days' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-400 hover:text-gray-600'" class="flex-1 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all"><i class="bi bi-calendar-day mr-1"></i> Per Hari</button>
                        </div>

                        <input type="hidden" name="duration_unit" :value="mode">

                        <div class="relative">
                            <input type="number" name="duration_amount" min="1" required 
                                   class="w-full px-6 py-5 bg-gray-50 border border-gray-100 rounded-2xl outline-none font-black text-sm text-gray-700 focus:ring-4 focus:ring-blue-500/10 transition-all placeholder:text-gray-300 shadow-inner"
                                   :placeholder="mode === 'hours' ? 'Berapa jam?' : 'Berapa hari?'">
                            <span class="absolute right-6 top-1/2 -translate-y-1/2 text-[10px] font-black text-gray-400 uppercase tracking-widest" 
                                  x-text="mode === 'hours' ? 'JAM' : 'HARI'">
                            </span>
                        </div>
                    </div>

                    <div class="text-left leading-none flex flex-col">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3"><i class="bi bi-card-text me-1"></i> Tujuan Peminjaman</label>
                        <textarea name="reason" required placeholder="Jelaskan untuk keperluan apa kelas meminjam buku ini..." class="w-full flex-1 px-6 py-5 bg-gray-50 border border-gray-100 rounded-2xl outline-none font-medium text-xs focus:ring-4 focus:ring-blue-500/10 transition-all shadow-inner resize-none"></textarea>
                    </div>
                </div>
                
                <div class="flex gap-4 pt-6">
                    <button type="button" @click="modalBooking = false" class="flex-1 px-6 py-5 rounded-[2rem] bg-gray-100 text-slate-500 font-black text-[10px] uppercase tracking-widest transition-all hover:bg-gray-200">Batalkan</button>
                    <button type="submit" class="flex-1 px-6 py-5 rounded-[2rem] bg-blue-600 text-white font-black text-[10px] uppercase tracking-widest shadow-xl shadow-blue-100 hover:bg-blue-700 active:scale-95 transition-all">Ajukan Peminjaman</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ✅ MODAL DETAIL BUKU (MUNCUL SAAT MATA 👁️ DIKLIK DI KERANJANG) --}}
    <div x-show="modalBookDetail" x-cloak class="fixed inset-0 z-[130] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/70 backdrop-blur-md" @click="modalBookDetail = false"></div>
        <div x-show="modalBookDetail" x-transition.scale.95 class="relative w-full max-w-3xl bg-white rounded-[2.5rem] shadow-2xl overflow-hidden flex flex-col md:flex-row h-auto md:h-[500px]">
            
            {{-- Bagian Kiri: Cover Buku --}}
            <div class="w-full md:w-1/2 bg-slate-50 relative flex flex-col items-center justify-center p-8 border-r border-slate-100">
                <button @click="modalBookDetail = false" class="md:hidden absolute top-4 right-4 w-8 h-8 bg-white rounded-full flex items-center justify-center shadow-sm text-gray-500"><i class="bi bi-x-lg"></i></button>
                
                <template x-if="selectedBookToView.image">
                    <img :src="'{{ asset('storage') }}/' + selectedBookToView.image" class="w-full h-auto max-h-[350px] object-cover rounded-xl shadow-md border border-slate-200">
                </template>
                <template x-if="!selectedBookToView.image">
                    <div class="flex flex-col items-center justify-center text-slate-300">
                        <i class="bi bi-book text-6xl mb-2"></i>
                        <span class="text-[10px] font-bold uppercase tracking-widest">Belum Ada Cover</span>
                    </div>
                </template>
            </div>

            {{-- Bagian Kanan: Detail Buku --}}
            <div class="w-full md:w-1/2 p-8 md:p-10 flex flex-col h-full overflow-y-auto custom-scroll text-left leading-none relative">
                <button @click="modalBookDetail = false" class="hidden md:flex absolute top-6 right-6 w-8 h-8 bg-gray-50 rounded-full items-center justify-center text-gray-400 hover:bg-red-50 hover:text-red-500 transition-colors"><i class="bi bi-x-lg"></i></button>

                <div class="mb-6 border-b border-slate-100 pb-5 pr-8">
                    <h3 class="text-2xl font-black text-slate-900 tracking-tight mb-2 uppercase" x-text="selectedBookToView.name"></h3>
                    <p class="text-xs font-bold text-gray-500 mb-4" x-text="'Oleh: ' + (selectedBookToView.author || 'Anonim')"></p>
                    
                    <div class="flex flex-wrap gap-2">
                        <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-50 border border-indigo-100">
                            <span class="text-[9px] font-black text-indigo-400 uppercase tracking-widest">ISBN:</span>
                            <span class="text-[10px] font-black text-indigo-700 uppercase tracking-widest" x-text="selectedBookToView.asset_code"></span>
                        </div>
                        
                        {{-- ✅ UPDATE: Tampilkan Semua Genre --}}
                        <template x-if="selectedBookToView.categories && selectedBookToView.categories.length > 0">
                            <template x-for="cat in selectedBookToView.categories" :key="cat.id">
                                <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-100 border border-slate-200">
                                    <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest" x-text="cat.name"></span>
                                </div>
                            </template>
                        </template>
                        <template x-if="!selectedBookToView.categories || selectedBookToView.categories.length === 0">
                            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-100 border border-slate-200">
                                <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Umum</span>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Status Stok --}}
                <div class="mb-6 text-left leading-none">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3"><i class="bi bi-layers me-1"></i> Ketersediaan Eksemplar</p>
                    <div class="flex items-center justify-between bg-emerald-50 p-4 rounded-xl border border-emerald-100">
                        <div>
                            <p class="text-[9px] font-black text-emerald-500 uppercase tracking-widest mb-1">Stok Siap Pinjam</p>
                            <p class="text-xl font-black text-emerald-700 leading-none" x-text="selectedBookToView.stock + ' Buku'"></p>
                        </div>
                        <i class="bi bi-check-circle-fill text-3xl text-emerald-400"></i>
                    </div>
                </div>

                {{-- Info Tambahan --}}
                <div class="grid grid-cols-2 gap-3 mb-6 text-left">
                    <div class="p-3 bg-slate-50 border border-slate-100 rounded-xl shadow-inner">
                        <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Penerbit</p>
                        <p class="text-[10px] font-bold text-slate-700 uppercase truncate" x-text="selectedBookToView.publisher || '-'"></p>
                    </div>
                    <div class="p-3 bg-slate-50 border border-slate-100 rounded-xl shadow-inner">
                        <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Tahun Terbit</p>
                        <p class="text-[10px] font-bold text-slate-700 uppercase" x-text="selectedBookToView.publish_year || '-'"></p>
                    </div>
                </div>

                <div class="mb-2 text-left leading-none">
                    <p class="text-[10px] font-black text-slate-400 uppercase mb-2 tracking-widest flex items-center gap-2"><i class="bi bi-card-text"></i> Sinopsis / Deskripsi</p>
                    <div class="p-4 bg-gray-50 border border-gray-100 rounded-2xl text-xs font-medium text-slate-600 leading-relaxed shadow-inner">
                        <span x-text="selectedBookToView.description || 'Tidak ada deskripsi atau sinopsis untuk buku ini.'"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 5. MODAL DETAIL STATUS TRANSAKSI (GROUPED ITEMS) --}}
    <div x-show="modalStatus" x-cloak class="fixed inset-0 z-[120] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-md" @click="modalStatus = false"></div>
        <div x-show="modalStatus" x-transition.scale.95 class="relative w-full max-w-xl bg-white rounded-[3rem] shadow-2xl p-10 border border-white flex flex-col max-h-[90vh] text-left leading-none overflow-y-auto custom-scroll">
            
            {{-- HEADER MODAL --}}
            <div class="mb-8 text-left leading-none flex justify-between items-start">
                <div>
                    <span class="text-[9px] font-black text-blue-500 uppercase tracking-[0.2em] bg-blue-50 px-2 py-1 rounded mb-2 inline-block" x-text="selectedRequest.is_arsip ? 'Detail Arsip Sirkulasi' : 'Detail Transaksi'"></span>
                    <h3 class="text-2xl font-black text-gray-900 uppercase mt-2 tracking-tight leading-none" x-text="selectedRequest.is_paket ? 'Paket Peminjaman' : selectedRequest.items[0]?.name"></h3>
                    <p class="text-[10px] font-black text-slate-400 uppercase mt-1.5 tracking-widest" x-text="selectedRequest.items.length + ' Judul Buku Terlampir'"></p>
                </div>
                <button @click="modalStatus = false" class="w-10 h-10 rounded-full bg-gray-100 text-gray-500 hover:bg-red-100 hover:text-red-500 transition-colors flex items-center justify-center flex-shrink-0"><i class="bi bi-x-lg"></i></button>
            </div>

            <div class="space-y-6 text-left leading-none">
                
                {{-- TAMPILAN KODE PIN RAKSASA (Hanya muncul saat status Pending & BUKAN di Arsip) --}}
                <template x-if="!selectedRequest.is_arsip && selectedRequest.status === 'pending' && selectedRequest.kode_peminjaman && selectedRequest.kode_peminjaman !== '-'">
                    <div class="p-6 bg-blue-50 rounded-[2rem] border border-blue-200 text-center shadow-inner relative overflow-hidden">
                        <div class="absolute -right-4 -top-4 w-20 h-20 bg-blue-500/10 rounded-full"></div>
                        <div class="absolute -left-4 -bottom-4 w-16 h-16 bg-blue-500/10 rounded-full"></div>
                        <p class="text-[10px] font-black text-blue-600 uppercase tracking-widest mb-2 relative z-10"><i class="bi bi-key-fill me-1"></i> Tunjukkan Kode Ini ke Petugas</p>
                        <p class="text-3xl lg:text-4xl font-black text-blue-800 tracking-[0.2em] relative z-10" x-text="selectedRequest.kode_peminjaman"></p>
                    </div>
                </template>

                {{-- INFO STATUS & TANGGAL --}}
                <div class="p-6 bg-slate-50 rounded-[2rem] border border-slate-100 flex justify-between items-center shadow-sm">
                    <div class="flex flex-col">
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Status Saat Ini</span>
                        <span class="text-xs font-black uppercase px-2.5 py-1 rounded-md border w-fit" 
                              :class="{
                                  'text-orange-600 bg-orange-50 border-orange-200': selectedRequest.status === 'pending',
                                  'text-blue-600 bg-blue-50 border-blue-200': selectedRequest.status === 'approved' || selectedRequest.status === 'borrowed',
                                  'text-emerald-600 bg-emerald-50 border-emerald-200': selectedRequest.status === 'returned',
                                  'text-red-600 bg-red-50 border-red-200': selectedRequest.status === 'rejected'
                              }"
                              x-text="selectedRequest.status === 'pending' ? 'MENUNGGU VERIFIKASI' : ((selectedRequest.status === 'approved' || selectedRequest.status === 'borrowed') ? 'SEDANG DIPINJAM' : (selectedRequest.status === 'returned' ? 'SUDAH DIKEMBALIKAN' : 'DITOLAK'))"></span>
                    </div>
                    {{-- Durasi Badge --}}
                    <div class="px-4 py-2 bg-white rounded-xl shadow-sm border border-slate-100 text-center">
                         <span class="text-[8px] font-bold text-gray-400 uppercase tracking-widest block mb-0.5">Durasi</span>
                         <span class="text-[11px] font-black text-blue-600 uppercase tracking-widest" x-text="selectedRequest.durasi"></span>
                    </div>
                </div>

                {{-- LIST BARANG DALAM PAKET INI (Dengan Detail Insiden) --}}
                <div class="p-6 bg-white rounded-[2rem] border border-gray-100 shadow-sm space-y-4">
                    <div class="flex items-center gap-2 text-blue-600 font-black text-[10px] uppercase tracking-widest mb-2">
                        <i class="bi bi-book-half"></i> Rincian Buku
                    </div>
                    <div class="space-y-3">
                        <template x-for="item in selectedRequest.items" :key="item.code">
                            <div class="flex flex-col bg-slate-50 border border-slate-100 rounded-xl overflow-hidden">
                                <div class="flex justify-between items-center p-3">
                                    <div class="text-left leading-none overflow-hidden pr-2">
                                        <p class="font-bold text-gray-900 text-xs uppercase truncate" x-text="item.name"></p>
                                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mt-1" x-text="'KODE: ' + item.code"></p>
                                    </div>
                                    <span class="px-2.5 py-1 bg-white border border-gray-200 rounded-md text-[10px] font-black text-blue-600 flex-shrink-0" x-text="item.qty + ' Buku'"></span>
                                </div>
                                
                                {{-- DETAIL INSIDEN --}}
                                <template x-if="item.kondisi && item.kondisi !== 'aman'">
                                    <div class="bg-red-50/50 p-3 border-t border-red-100 flex flex-col gap-1.5">
                                        <div class="flex items-center gap-2">
                                            <span class="px-2 py-0.5 rounded bg-red-100 text-red-600 text-[8px] font-black uppercase border border-red-200" x-text="'KONDISI: ' + item.kondisi"></span>
                                            <template x-if="parseInt(item.hilang) > 0">
                                                <span class="px-2 py-0.5 rounded bg-slate-200 text-slate-600 text-[8px] font-black uppercase border border-slate-300" x-text="item.hilang + ' Buku ' + item.kondisi"></span>
                                            </template>
                                            <template x-if="parseInt(item.denda) > 0">
                                                <span class="px-2 py-0.5 rounded bg-red-600 text-white text-[8px] font-black uppercase border border-red-700" x-text="'Denda: Rp ' + new Intl.NumberFormat('id-ID').format(item.denda)"></span>
                                            </template>
                                        </div>
                                        <p class="text-[9px] text-red-800 font-bold italic" x-text="'Catatan: ' + (item.note_rusak || 'Tidak ada keterangan')"></p>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- TIMELINE SECTION --}}
                <div class="p-6 bg-white rounded-[2rem] border border-gray-100 shadow-sm space-y-4">
                    <div class="flex items-center gap-2 text-blue-600 font-black text-[10px] uppercase tracking-widest mb-1">
                        <i class="bi bi-calendar-week-fill"></i> Timeline Sirkulasi
                    </div>
                    <div class="flex justify-between items-start relative mt-2">
                        <div class="absolute top-2 left-4 right-4 h-0.5 bg-gray-100 -z-10"></div>
                        <div class="flex flex-col items-center bg-white px-2">
                            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Mulai Request</span>
                            <span class="text-[10px] font-bold text-gray-900 bg-gray-50 border border-gray-100 px-2 py-1 rounded" x-text="selectedRequest.tgl"></span>
                        </div>
                        <div class="flex flex-col items-center bg-white px-2">
                            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Batas Waktu (Deadline)</span>
                            <span class="text-[10px] font-bold text-red-500 bg-red-50 border border-red-100 px-2 py-1 rounded" x-text="selectedRequest.tenggat"></span>
                        </div>
                    </div>
                </div>

                {{-- TOTAL DENDA JIKA ADA --}}
                <div x-show="selectedRequest.total_denda && selectedRequest.total_denda !== '0'" class="p-6 bg-red-50 rounded-[2rem] border border-red-100 flex justify-between items-center">
                    <div class="flex flex-col">
                        <span class="text-[9px] font-bold text-red-500 uppercase tracking-widest mb-1">Total Denda Paket Ini</span>
                        <span class="text-xl font-black text-red-600" x-text="'Rp ' + selectedRequest.total_denda"></span>
                    </div>
                    <template x-if="!selectedRequest.is_arsip">
                        <a @click="modalStatus = false; activeTab = 'denda'" class="text-[10px] font-black text-red-500 uppercase bg-white border border-red-200 px-3 py-1.5 rounded-lg cursor-pointer hover:bg-red-500 hover:text-white transition-colors shadow-sm">Cek Tab Denda</a>
                    </template>
                </div>

                {{-- CATATAN & FEEDBACK --}}
                <div class="space-y-4">
                    <div class="p-6 bg-blue-50/50 rounded-[2rem] border border-blue-100 text-left leading-relaxed">
                        <p class="text-[9px] font-black text-blue-500 uppercase tracking-widest mb-2 leading-none">Alasan Peminjaman:</p>
                        <p class="text-xs text-slate-600 font-medium italic" x-text="selectedRequest.note"></p>
                    </div>

                    <div class="p-6 bg-slate-900 rounded-[2rem] border border-slate-800 text-left leading-relaxed shadow-xl">
                        <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest mb-2 leading-none">Feedback Petugas:</p>
                        <p class="text-xs text-slate-300 font-medium" x-text="selectedRequest.admin_note"></p>
                    </div>
                </div>
            </div>

            <button @click="modalStatus = false" class="w-full mt-8 py-5 bg-slate-900 text-white rounded-[2rem] font-black text-[10px] uppercase tracking-[0.2em] shadow-xl hover:bg-blue-600 active:scale-95 transition-all border border-slate-800 hover:border-blue-500">Tutup Panel Informasi</button>
        </div>
    </div>

    {{-- Script Alpine --}}
    <script>
        function studentRequest() {
            return {
                sidebarOpen: false,
                activeTab: 'booking',
                searchQuery: '',
                modalBooking: false,
                modalStatus: false,
                modalBookDetail: false,

                // Data Buku
                itemsList: {!! json_encode($items) !!},
                searchBook: '',
                filterCategory: '',
                selectedBookToView: {},

                // ✅ FIX: Looping untuk multi-kategori
                get availableCategories() {
                    const cats = [];
                    this.itemsList.forEach(i => {
                        if(i.categories && i.categories.length > 0) {
                            i.categories.forEach(c => {
                                if(!cats.includes(c.name)) cats.push(c.name);
                            });
                        }
                    });
                    return cats.sort();
                },

                // ✅ FIX: Pencarian multi-kategori
                get filteredBooks() {
                    return this.itemsList.filter(i => {
                        const matchName = i.name.toLowerCase().includes(this.searchBook.toLowerCase()) || 
                                          (i.author && i.author.toLowerCase().includes(this.searchBook.toLowerCase()));
                        
                        // Cek apakah buku punya kategori yang dipilih user
                        const matchCat = this.filterCategory === '' || 
                                         (i.categories && i.categories.some(c => c.name === this.filterCategory));
                        
                        return matchName && matchCat;
                    });
                },

                openBookDetail(id) {
                    if(!id) return;
                    this.selectedBookToView = this.itemsList.find(i => i.id == id);
                    this.modalBookDetail = true;
                },

                // Cart Logic
                cart: [{ item_id: '', quantity: 1 }],
                addItem() { this.cart.push({ item_id: '', quantity: 1 }); },
                removeItem(index) { if(this.cart.length > 1) { this.cart.splice(index, 1); } },

                // Modal Transaksi Logic
                selectedRequest: {
                    is_paket: false,
                    items: [],
                    status: '', tgl: '', note: '', admin_note: '',
                    tgl_kembali: '', total_denda: 0,
                    durasi: '', tenggat: '',
                    kode_peminjaman: '',
                    is_arsip: false
                }
            }
        }
    </script>
</body>
</html>