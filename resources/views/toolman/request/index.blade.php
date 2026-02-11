<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Logistik Peminjaman - TekniLog Toolman</title>

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
        // Object Data Lengkap untuk Modal Detail
        selectedReq: { 
            id: '', nama: '', tipe: '', sub_info: '', barang: '', jumlah: 0, 
            sisaStok: 0, catatan: '', deadline: '',
            tgl_pinjam: '', tgl_kembali: '', kondisi: '', 
            denda: 0, status_denda: '', hilang: 0, detail_rusak: '', rating: 0 
        },
        actionRoute: '',
        actionType: '',
        returnCondition: 'aman',
        rating: 5
      }">

    {{-- Sidebar --}}
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'" 
           class="fixed inset-y-0 left-0 z-50 flex flex-col w-72 bg-[#064E3B] text-white border-r border-emerald-900 md:static md:flex-shrink-0 h-full transition-transform duration-300">
        @include('toolman.partials.sidebar')
    </aside>

    <div class="flex flex-1 flex-col h-full min-w-0 overflow-hidden text-left">
        @include('toolman.partials.header')

        <main class="flex-1 overflow-y-auto p-6 lg:p-10 pt-2 custom-scroll text-left">
            <div class="mx-auto w-full max-w-[1550px] space-y-8">
                
                {{-- Notifikasi --}}
                @if(session('success'))
                <div x-data="{show: true}" x-show="show" x-init="setTimeout(() => show = false, 4000)" class="bg-emerald-500 text-white px-6 py-4 rounded-[2rem] shadow-lg flex justify-between items-center mb-6 transition-all relative z-50">
                    <span class="font-bold text-sm uppercase tracking-widest"><i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}</span>
                    <button @click="show = false"><i class="bi bi-x-lg"></i></button>
                </div>
                @endif

                @if(session('error'))
                <div class="bg-red-500 text-white px-6 py-4 rounded-[2rem] shadow-lg flex justify-between items-center mb-6 transition-all relative z-50">
                    <span class="font-bold text-sm uppercase tracking-widest"><i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}</span>
                </div>
                @endif

                {{-- 1. HEADER & SEARCH BAR --}}
                <div class="flex flex-col xl:flex-row xl:items-end justify-between gap-6 leading-none">
                    <div class="text-left leading-none">
                        <h2 class="text-4xl font-black text-slate-900 tracking-tight uppercase leading-none">Logistik & Antrean</h2>
                        <div class="flex flex-wrap gap-2 mt-4">
                            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest self-center mr-2">Wilayah Kelola:</span>
                            @foreach(Auth::user()->assignedDepartments as $dept)
                                <span class="px-3 py-1.5 bg-emerald-50 text-emerald-600 text-[9px] font-black rounded-lg uppercase border border-emerald-100 tracking-wider">{{ $dept->name }}</span>
                            @endforeach
                        </div>
                    </div>

                    <div class="relative w-full md:w-96">
                        <i class="bi bi-search absolute left-5 top-1/2 -translate-y-1/2 text-emerald-500"></i>
                        <input type="text" x-model="searchQuery" placeholder="Cari nama peminjam, kelas, atau alat..." 
                               class="w-full pl-12 pr-6 py-5 bg-white border border-gray-100 rounded-[1.5rem] outline-none font-bold text-xs shadow-sm focus:ring-4 focus:ring-emerald-500/10 transition-all placeholder:text-gray-300">
                    </div>
                </div>

                {{-- 2. KPI STATS --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-white p-7 rounded-[2.5rem] border border-gray-100 shadow-sm flex items-center gap-6">
                        <div class="w-14 h-14 bg-orange-50 text-orange-600 rounded-2xl flex items-center justify-center text-2xl shadow-inner"><i class="bi bi-clock-history"></i></div>
                        <div class="text-left leading-none"><p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Antrean</p><p class="text-3xl font-black text-gray-900">{{ $requests->where('status', 'pending')->count() }}</p></div>
                    </div>
                    <div class="bg-white p-7 rounded-[2.5rem] border border-gray-100 shadow-sm flex items-center gap-6">
                        <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center text-2xl shadow-inner"><i class="bi bi-box-arrow-up"></i></div>
                        <div class="text-left leading-none"><p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Dipinjam</p><p class="text-3xl font-black text-gray-900">{{ $requests->where('status', 'approved')->count() }}</p></div>
                    </div>
                    <div class="bg-white p-7 rounded-[2.5rem] border border-gray-100 shadow-sm flex items-center gap-6 border-b-4 border-b-emerald-500">
                        <div class="w-14 h-14 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center text-2xl shadow-inner"><i class="bi bi-star-fill"></i></div>
                        <div class="text-left leading-none"><p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Selesai</p><p class="text-3xl font-black text-gray-900">{{ $requests->where('status', 'returned')->count() }}</p></div>
                    </div>
                    <div class="bg-slate-900 p-7 rounded-[2.5rem] shadow-xl flex items-center gap-6 text-white border border-slate-800">
                        <div class="w-14 h-14 bg-white/10 text-emerald-400 rounded-2xl flex items-center justify-center text-2xl shadow-inner"><i class="bi bi-shield-lock"></i></div>
                        <div class="text-left leading-none"><p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5">Tagihan Aktif</p><p class="text-xl font-black uppercase tracking-tight">{{ $requests->where('fine_status', 'unpaid')->count() }} KASUS</p></div>
                    </div>
                </div>

                {{-- 3. TAB SWITCHER --}}
                <div class="flex bg-gray-200/50 p-1.5 rounded-2xl gap-1 w-fit border border-gray-100 relative z-10">
                    <button @click="activeTab = 'aktif'; searchQuery = ''" :class="activeTab === 'aktif' ? 'bg-white text-emerald-600 shadow-sm' : 'text-slate-500 hover:text-emerald-500'" class="px-8 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">Sirkulasi Aktif</button>
                    <button @click="activeTab = 'denda'; searchQuery = ''" :class="activeTab === 'denda' ? 'bg-white text-red-600 shadow-sm' : 'text-slate-500 hover:text-red-500'" class="px-8 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">Data Denda</button>
                    <button @click="activeTab = 'riwayat'; searchQuery = ''" :class="activeTab === 'riwayat' ? 'bg-white text-emerald-600 shadow-sm' : 'text-slate-500 hover:text-emerald-500'" class="px-8 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">Arsip & Logistik</button>
                </div>

                {{-- 4. TABLES --}}
                <div class="rounded-[3rem] bg-white shadow-sm border border-gray-100 overflow-hidden text-left relative z-0">
                    
                    {{-- TAB SIRKULASI AKTIF --}}
                    <div x-show="activeTab === 'aktif'" x-transition>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <thead>
                                    <tr class="bg-gray-50/50 text-[10px] uppercase tracking-[0.2em] text-gray-400 font-black bg-gray-50/50 leading-none">
                                        <th class="px-8 py-7">Identitas Peminjam</th>
                                        <th class="px-8 py-7 text-left">Alat & Monitoring Stok</th>
                                        <th class="px-8 py-7">Batas Waktu</th> {{-- ✅ KOLOM BARU --}}
                                        <th class="px-8 py-7">Status</th>
                                        <th class="px-8 py-7 text-center">Konfirmasi Toolman</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50 leading-tight">
                                    @forelse($requests->whereIn('status', ['pending', 'approved']) as $loan)
                                    <tr class="group hover:bg-gray-50/50 transition-all duration-200" 
                                        x-show="searchQuery === '' || '{{ strtolower($loan->user->name) }}'.includes(searchQuery.toLowerCase()) || '{{ strtolower($loan->item->name) }}'.includes(searchQuery.toLowerCase())">
                                        
                                        <td class="px-8 py-6">
                                            <div class="flex flex-col text-left">
                                                <span class="text-gray-900 font-black uppercase text-base tracking-tight">{{ $loan->user->name }}</span>
                                                <div class="flex flex-wrap items-center gap-2 mt-1.5">
                                                    @if($loan->user->role === 'student')
                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[8px] font-black uppercase bg-cyan-50 text-cyan-600 border border-cyan-100"><i class="bi bi-person-fill"></i> Siswa</span>
                                                        <span class="text-[9px] text-gray-400 font-bold uppercase tracking-widest">{{ $loan->user->classRoom->name ?? 'No Class' }}</span>
                                                    @else
                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[8px] font-black uppercase bg-purple-50 text-purple-600 border border-purple-100"><i class="bi bi-people-fill"></i> Kelas</span>
                                                        <span class="text-[9px] text-gray-400 font-bold uppercase tracking-widest">{{ $loan->user->department->name ?? 'Unit' }}</span>
                                                    @endif
                                                    <span class="text-[9px] text-slate-300 mx-1">•</span>
                                                    <span class="text-[9px] text-gray-400 font-bold uppercase tracking-widest">{{ $loan->created_at->format('d M, H:i') }}</span>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="px-8 py-6 text-left">
                                            <div class="flex items-center gap-4">
                                                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-black text-base shadow-inner">{{ $loan->quantity }}</div>
                                                <div class="flex flex-col text-left">
                                                    <span class="text-gray-800 font-black uppercase tracking-tight text-sm">{{ $loan->item->name }}</span>
                                                    <span class="text-[10px] font-black uppercase mt-1 {{ $loan->item->stock < 5 ? 'text-red-500 animate-pulse' : 'text-gray-400' }}">Sisa Stok: {{ $loan->item->stock }} Unit</span>
                                                </div>
                                            </div>
                                        </td>

                                        {{-- ✅ INFO ESTIMASI DURASI --}}
                                        <td class="px-8 py-6">
                                            <div class="flex flex-col text-left leading-tight">
                                                <span class="text-orange-600 font-black text-xs uppercase">{{ $loan->return_date ? \Carbon\Carbon::parse($loan->return_date)->translatedFormat('d M Y') : 'Hari Ini' }}</span>
                                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Pukul {{ $loan->return_date ? \Carbon\Carbon::parse($loan->return_date)->format('H:i') : '-' }} WIB</span>
                                            </div>
                                        </td>

                                        <td class="px-8 py-6">
                                            <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-[0.1em]
                                                {{ $loan->status === 'pending' ? 'bg-orange-50 text-orange-600 border border-orange-100' : 'bg-blue-50 text-blue-600 border border-blue-100' }}">
                                                {{ $loan->status === 'pending' ? 'DALAM ANTREAN' : 'SEDANG DIPINJAM' }}
                                            </span>
                                        </td>

                                        <td class="px-8 py-6 text-center">
                                            <div class="flex items-center justify-center gap-2.5 relative z-10">
                                                <button @click="selectedReq = {
                                                    id: '{{ $loan->id }}', 
                                                    nama: '{{ $loan->user->name }}', 
                                                    tipe: '{{ $loan->user->role === 'student' ? 'Siswa Individu' : 'Perwakilan Kelas' }}',
                                                    sub_info: '{{ $loan->user->role === 'student' ? ($loan->user->classRoom->name ?? '-') : ($loan->user->department->name ?? '-') }}',
                                                    barang: '{{ $loan->item->name }}', 
                                                    jumlah: '{{ $loan->quantity }}', 
                                                    catatan: '{{ $loan->reason }}',
                                                    deadline: '{{ $loan->return_date ? \Carbon\Carbon::parse($loan->return_date)->translatedFormat('d M Y, H:i') : 'Hari Ini' }}',
                                                    tgl_pinjam: '{{ $loan->created_at->format('d M Y, H:i') }}'
                                                }; modalDetail = true" 
                                                class="w-11 h-11 rounded-2xl bg-gray-50 text-gray-400 hover:bg-indigo-50 hover:text-indigo-600 transition-all flex items-center justify-center shadow-sm">
                                                    <i class="bi bi-eye-fill"></i>
                                                </button>
                                                
                                                @if($loan->status === 'pending')
                                                    <button @click="selectedReq = {id: '{{ $loan->id }}', nama: '{{ $loan->user->name }}', barang: '{{ $loan->item->name }}', jumlah: '{{ $loan->quantity }}', sisaStok: '{{ $loan->item->stock }}'}; actionType = 'terima'; actionRoute = '/toolman/request/{{ $loan->id }}/approve'; modalAction = true" 
                                                            class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white transition-all shadow-sm flex items-center justify-center">
                                                        <i class="bi bi-check-lg text-xl"></i>
                                                    </button>
                                                    <button @click="selectedReq = {id: '{{ $loan->id }}', nama: '{{ $loan->user->name }}'}; actionType = 'tolak'; actionRoute = '/toolman/request/{{ $loan->id }}/reject'; modalAction = true" 
                                                            class="w-11 h-11 rounded-xl bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition-all shadow-sm flex items-center justify-center">
                                                        <i class="bi bi-x-lg text-lg"></i>
                                                    </button>
                                                @else
                                                    <button @click="selectedReq = {id: '{{ $loan->id }}', nama: '{{ $loan->user->name }}', barang: '{{ $loan->item->name }}', jumlah: '{{ $loan->quantity }}'}; actionRoute = '/toolman/request/{{ $loan->id }}/return'; modalReturn = true; returnCondition = 'aman'" 
                                                            class="px-6 py-3 bg-emerald-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-xl hover:bg-emerald-700 active:scale-95 transition-all">
                                                        Selesaikan Sesi
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="5" class="py-24 text-center text-gray-300 font-bold uppercase text-[10px] tracking-[0.3em] italic">Gudang sedang tidak memiliki sirkulasi aktif di wilayah Anda.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- TAB DATA DENDA --}}
                    <div x-show="activeTab === 'denda'" x-transition x-cloak>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <thead>
                                    <tr class="bg-gray-50/50 text-[10px] uppercase tracking-[0.2em] text-gray-400 font-black bg-gray-50/50 leading-none">
                                        <th class="px-8 py-7">Peminjam & Barang</th>
                                        <th class="px-8 py-7">Detail Insiden</th>
                                        <th class="px-8 py-7 text-right">Nominal Denda</th>
                                        <th class="px-8 py-7 text-center">Status</th>
                                        <th class="px-8 py-7 text-center">Aksi & Detail</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50 leading-tight">
                                    @forelse($requests->where('fine_amount', '>', 0) as $fine)
                                    <tr class="group hover:bg-red-50/10 transition-all duration-200">
                                        <td class="px-8 py-6">
                                            <div class="flex flex-col text-left">
                                                <span class="text-gray-900 font-black uppercase text-sm tracking-tight">{{ $fine->user->name }}</span>
                                                <div class="flex items-center gap-2 mt-1">
                                                    @if($fine->user->role === 'student')
                                                        <span class="text-[9px] font-black text-cyan-600 uppercase bg-cyan-50 px-1.5 py-0.5 rounded border border-cyan-100">Siswa</span>
                                                    @else
                                                        <span class="text-[9px] font-black text-purple-600 uppercase bg-purple-50 px-1.5 py-0.5 rounded border border-purple-100">Kelas</span>
                                                    @endif
                                                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ $fine->item->name }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-8 py-6">
                                            <span class="text-red-600 font-bold text-xs uppercase">{{ $fine->return_note ?? 'Tidak ada catatan' }}</span>
                                            <div class="mt-1">
                                                <span class="text-[9px] font-black bg-red-100 text-red-600 px-2 py-0.5 rounded uppercase">{{ $fine->return_condition }}</span>
                                                @if($fine->lost_quantity > 0)
                                                    <span class="text-[9px] font-black bg-slate-100 text-slate-600 px-2 py-0.5 rounded uppercase ml-1">{{ $fine->lost_quantity }} Unit Hilang</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-8 py-6 text-right">
                                            <span class="font-black text-red-600 bg-red-50 px-3 py-1.5 rounded-lg border border-red-100 text-sm">Rp {{ number_format($fine->fine_amount, 0, ',', '.') }}</span>
                                        </td>
                                        <td class="px-8 py-6 text-center">
                                            <span class="px-3 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest {{ $fine->fine_status === 'paid' ? 'bg-emerald-100 text-emerald-600' : 'bg-red-100 text-red-600 border border-red-200 animate-pulse' }}">
                                                {{ $fine->fine_status === 'paid' ? 'LUNAS' : 'BELUM LUNAS' }}
                                            </span>
                                        </td>
                                        <td class="px-8 py-6 text-center">
                                            <div class="flex items-center justify-center gap-2 relative z-10">
                                                <button @click="selectedReq = {
                                                    id: '{{ $fine->id }}',
                                                    nama: '{{ $fine->user->name }}',
                                                    tipe: '{{ $fine->user->role === 'student' ? 'Siswa Individu' : 'Perwakilan Kelas' }}',
                                                    sub_info: '{{ $fine->user->role === 'student' ? ($fine->user->classRoom->name ?? '-') : ($fine->user->department->name ?? '-') }}',
                                                    barang: '{{ $fine->item->name }}',
                                                    jumlah: '{{ $fine->quantity }}',
                                                    tgl_pinjam: '{{ $fine->created_at->format('d M Y') }}',
                                                    tgl_kembali: '{{ $fine->return_date ? \Carbon\Carbon::parse($fine->return_date)->format('d M Y') : '-' }}',
                                                    catatan: '{{ $fine->reason }}',
                                                    denda: '{{ number_format($fine->fine_amount, 0, ',', '.') }}',
                                                    status_denda: '{{ $fine->fine_status }}',
                                                    kondisi: '{{ $fine->return_condition }}',
                                                    detail_rusak: '{{ $fine->return_note }}',
                                                    hilang: '{{ $fine->lost_quantity }}'
                                                }; modalDetail = true" 
                                                class="w-9 h-9 rounded-xl bg-gray-50 text-gray-400 hover:bg-indigo-50 hover:text-indigo-600 transition-all flex items-center justify-center shadow-sm">
                                                    <i class="bi bi-eye-fill"></i>
                                                </button>

                                                @if($fine->fine_status === 'unpaid')
                                                    <button @click="selectedReq = {id: '{{ $fine->id }}', nama: '{{ $fine->user->name }}', denda: '{{ number_format($fine->fine_amount, 0, ',', '.') }}'}; actionRoute = '/toolman/request/{{ $fine->id }}/paid'; modalPaid = true" 
                                                            class="px-4 py-2 bg-emerald-600 text-white rounded-xl text-[9px] font-black uppercase shadow-lg hover:bg-emerald-700 transition-all active:scale-95">
                                                        Tandai Lunas
                                                    </button>
                                                @else
                                                    <span class="text-emerald-500 text-xl"><i class="bi bi-check-circle-fill"></i></span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="5" class="py-24 text-center text-gray-300 font-bold uppercase text-[10px] tracking-[0.3em] italic">Tidak ada catatan denda.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- TAB ARSIP & LOGISTIK --}}
                    <div x-show="activeTab === 'riwayat'" x-transition x-cloak>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <thead>
                                    <tr class="text-[10px] uppercase tracking-[0.2em] text-gray-400 font-black bg-gray-50/50 leading-none">
                                        <th class="px-8 py-7">Timestamp Selesai</th>
                                        <th class="px-8 py-7">Identitas Peminjam</th>
                                        <th class="px-8 py-7 text-center">Kondisi Balik</th>
                                        <th class="px-8 py-7 text-center">Trust Rating</th>
                                        <th class="px-8 py-7 text-center">Status Final</th>
                                        <th class="px-8 py-7 text-center">Detail</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50 leading-tight">
                                    @forelse($requests->whereIn('status', ['returned', 'rejected']) as $hist)
                                    <tr class="group hover:bg-gray-50/50 transition-all"
                                        x-show="searchQuery === '' || '{{ strtolower($hist->user->name) }}'.includes(searchQuery.toLowerCase()) || '{{ strtolower($hist->item->name) }}'.includes(searchQuery.toLowerCase())">
                                        <td class="px-8 py-6 font-bold text-gray-400 text-[11px] uppercase tracking-wider">{{ $hist->updated_at->translatedFormat('d M Y, H:i') }} WIB</td>
                                        <td class="px-8 py-6">
                                            <div class="flex flex-col text-left">
                                                <span class="font-black text-gray-900 uppercase text-sm tracking-tight leading-none mb-1">{{ $hist->user->name }}</span>
                                                <div class="flex items-center gap-2 mt-1">
                                                    @if($hist->user->role === 'student')
                                                        <span class="text-[9px] font-black text-cyan-600 uppercase bg-cyan-50 px-1.5 py-0.5 rounded border border-cyan-100">Siswa</span>
                                                    @else
                                                        <span class="text-[9px] font-black text-purple-600 uppercase bg-purple-50 px-1.5 py-0.5 rounded border border-purple-100">Kelas</span>
                                                    @endif
                                                    <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">{{ $hist->item->name }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-8 py-6 text-center">
                                            @if($hist->return_condition)
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[9px] font-black uppercase
                                                    {{ $hist->return_condition === 'aman' ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : ($hist->return_condition === 'rusak' ? 'bg-orange-50 text-orange-600 border border-orange-100' : 'bg-red-50 text-red-600 border border-red-100') }}">
                                                    {{ $hist->return_condition }}
                                                </span>
                                            @else
                                                <span class="text-gray-300 font-black">-</span>
                                            @endif
                                        </td>
                                        <td class="px-8 py-6 text-center text-orange-400">
                                            @if($hist->rating)
                                                <div class="flex justify-center gap-1">
                                                    @for($i=1; $i<=5; $i++)
                                                        <i class="bi bi-star{{ $i <= $hist->rating ? '-fill' : '' }} text-[11px]"></i>
                                                    @endfor
                                                </div>
                                            @else
                                                <span class="text-gray-300 font-black">-</span>
                                            @endif
                                        </td>
                                        <td class="px-8 py-6 text-center">
                                            <span class="px-3 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest {{ $hist->status === 'returned' ? 'bg-emerald-100 text-emerald-600' : 'bg-red-100 text-red-600' }}">
                                                {{ $hist->status === 'returned' ? 'SELESAI' : 'DITOLAK' }}
                                            </span>
                                        </td>
                                        <td class="px-8 py-6 text-center">
                                            <div class="relative z-10">
                                                <button @click="selectedReq = {
                                                    id: '{{ $hist->id }}',
                                                    nama: '{{ $hist->user->name }}',
                                                    tipe: '{{ $hist->user->role === 'student' ? 'Siswa Individu' : 'Perwakilan Kelas' }}',
                                                    sub_info: '{{ $hist->user->role === 'student' ? ($hist->user->classRoom->name ?? '-') : ($hist->user->department->name ?? '-') }}',
                                                    barang: '{{ $hist->item->name }}',
                                                    jumlah: '{{ $hist->quantity }}',
                                                    tgl_pinjam: '{{ $hist->created_at->format('d M Y') }}',
                                                    tgl_kembali: '{{ $hist->return_date ? \Carbon\Carbon::parse($hist->return_date)->format('d M Y') : '-' }}',
                                                    catatan: '{{ $hist->reason }}',
                                                    kondisi: '{{ $hist->return_condition }}',
                                                    rating: '{{ $hist->rating }}'
                                                }; modalDetail = true" 
                                                class="w-9 h-9 rounded-xl bg-gray-50 text-gray-400 hover:bg-emerald-50 hover:text-emerald-600 transition-all flex items-center justify-center shadow-sm mx-auto">
                                                    <i class="bi bi-eye-fill"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="6" class="py-24 text-center text-gray-300 font-bold uppercase text-[10px] tracking-[0.3em] italic">Belum ada data arsip terekam.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    {{-- MODAL SECTION --}}
    
    {{-- 🛠️ MODAL ACTION (TERIMA/TOLAK) --}}
    <div x-show="modalAction" x-cloak class="fixed inset-0 z-[130] flex items-center justify-center p-4 text-center">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="modalAction = false"></div>
        <div x-show="modalAction" x-transition.scale.95 class="relative w-full max-w-md bg-white rounded-[3rem] shadow-2xl p-10 border border-white text-left overflow-hidden leading-none">
            <div :class="actionType === 'terima' ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-red-600'" class="w-20 h-20 rounded-[1.5rem] flex items-center justify-center mx-auto mb-8 shadow-inner"><i :class="actionType === 'terima' ? 'bi-shield-check' : 'bi-shield-exclamation'" class="text-4xl"></i></div>
            <h3 class="text-2xl font-black text-gray-900 mb-2 uppercase tracking-tight text-center leading-none" x-text="actionType === 'terima' ? 'Konfirmasi Peminjaman' : 'Batalkan Request'"></h3>
            <p class="text-sm text-gray-500 mb-8 font-medium text-center">Memproses permintaan dari <span class="font-black text-gray-900 uppercase" x-text="selectedReq.nama"></span></p>
            
            <form :action="actionRoute" method="POST" class="space-y-6">
                @csrf @method('PUT')
                <div x-show="actionType === 'terima'" class="p-6 bg-slate-50 rounded-[2rem] border border-slate-100">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Jumlah Barang Disetujui</label>
                    <div class="flex items-center gap-4">
                        <input type="number" name="approved_quantity" x-model="selectedReq.jumlah" required :max="selectedReq.sisaStok" min="1"
                               class="flex-1 px-5 py-4 bg-white border border-slate-200 rounded-2xl outline-none font-black text-lg text-emerald-600 focus:ring-4 focus:ring-emerald-500/10">
                        <div class="text-right leading-none">
                            <p class="text-[9px] font-black text-slate-400 uppercase mb-1">Stok Rak</p>
                            <p class="text-sm font-black text-slate-900 uppercase" x-text="selectedReq.sisaStok + ' Unit'"></p>
                        </div>
                    </div>
                </div>
                <div class="text-left leading-none">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3" x-text="actionType === 'terima' ? 'Catatan Pengambilan' : 'Alasan Penolakan'"></label>
                    <textarea name="admin_note" :required="actionType === 'tolak'" rows="3" class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl outline-none text-sm font-medium focus:ring-4 focus:ring-emerald-500/10 shadow-inner" placeholder="Tulis catatan di sini..."></textarea>
                </div>
                <div class="flex gap-4 pt-2">
                    <button type="button" @click="modalAction = false" class="flex-1 px-6 py-5 rounded-2xl bg-gray-100 text-slate-500 font-black text-[10px] uppercase tracking-widest">Batal</button>
                    <button type="submit" :class="actionType === 'terima' ? 'bg-emerald-600 shadow-emerald-100' : 'bg-red-500 shadow-red-100'" class="flex-1 px-6 py-5 rounded-2xl text-white font-black text-[10px] shadow-xl uppercase tracking-widest transition-all active:scale-95" x-text="actionType === 'terima' ? 'ACC SEKARANG' : 'KONFIRMASI TOLAK'"></button>
                </div>
            </form>
        </div>
    </div>

    {{-- ✅ MODAL KONFIRMASI KEMBALI --}}
    <div x-show="modalReturn" x-cloak class="fixed inset-0 z-[130] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="modalReturn = false"></div>
        <div x-show="modalReturn" x-transition.scale.95 class="relative w-full max-w-xl bg-white rounded-[3rem] shadow-2xl p-10 border border-white text-left overflow-y-auto max-h-[90vh] custom-scroll">
            <div class="flex items-center gap-6 mb-8 text-left leading-none">
                <div class="w-16 h-16 bg-emerald-50 text-emerald-600 rounded-[1.5rem] flex items-center justify-center text-3xl shadow-inner"><i class="bi bi-box-arrow-in-down"></i></div>
                <div class="text-left">
                    <h3 class="text-2xl font-black text-gray-900 uppercase tracking-tight leading-none mb-2">Proses Pengembalian</h3>
                    <p class="text-xs text-gray-500 font-medium uppercase tracking-widest">Verifikasi akhir: <span class="text-emerald-600 font-black" x-text="selectedReq.nama"></span></p>
                </div>
            </div>
            
            <form :action="actionRoute" method="POST" class="space-y-8">
                @csrf @method('PUT')
                
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Klasifikasi Kondisi Fisik</label>
                    <div class="grid grid-cols-3 gap-3">
                        <label class="cursor-pointer group">
                            <input type="radio" name="return_condition" value="aman" x-model="returnCondition" class="peer hidden" required>
                            <div class="p-5 border-2 border-slate-100 rounded-[1.5rem] text-center peer-checked:border-emerald-500 peer-checked:bg-emerald-50 transition-all">
                                <i class="bi bi-shield-check text-2xl text-gray-300 peer-checked:text-emerald-600 mb-2 block"></i>
                                <span class="text-[9px] font-black uppercase text-gray-500 peer-checked:text-emerald-700">Aman</span>
                            </div>
                        </label>
                        <label class="cursor-pointer group">
                            <input type="radio" name="return_condition" value="rusak" x-model="returnCondition" class="peer hidden">
                            <div class="p-5 border-2 border-slate-100 rounded-[1.5rem] text-center peer-checked:border-orange-500 peer-checked:bg-orange-50 transition-all">
                                <i class="bi bi-exclamation-triangle text-2xl text-gray-300 peer-checked:text-orange-600 mb-2 block"></i>
                                <span class="text-[9px] font-black uppercase text-gray-500 peer-checked:text-orange-700">Rusak</span>
                            </div>
                        </label>
                        <label class="cursor-pointer group">
                            <input type="radio" name="return_condition" value="hilang" x-model="returnCondition" class="peer hidden">
                            <div class="p-5 border-2 border-slate-100 rounded-[1.5rem] text-center peer-checked:border-red-500 peer-checked:bg-red-50 transition-all">
                                <i class="bi bi-question-circle text-2xl text-gray-300 peer-checked:text-red-600 mb-2 block"></i>
                                <span class="text-[9px] font-black uppercase text-gray-500 peer-checked:text-red-700">Hilang</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div x-show="returnCondition !== 'aman'" x-transition class="space-y-6 bg-red-50 p-6 rounded-[2rem] border border-red-100">
                    <div class="flex items-center gap-3 text-red-600 font-bold text-xs uppercase tracking-widest mb-2">
                        <i class="bi bi-exclamation-circle-fill"></i> Data Insiden
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <label class="block text-[10px] font-black text-red-400 uppercase tracking-widest mb-2">Detail Kerusakan/Kehilangan</label>
                            <input type="text" name="return_note" placeholder="Contoh: Layar Pecah" class="w-full px-5 py-4 bg-white border border-red-200 rounded-2xl outline-none text-sm font-bold text-red-700">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-red-400 uppercase tracking-widest mb-2">Unit Rusak/Hilang</label>
                            <input type="number" name="lost_quantity" placeholder="0" :max="selectedReq.jumlah" min="1" class="w-full px-5 py-4 bg-white border border-red-200 rounded-2xl outline-none text-sm font-black text-red-700">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-red-400 uppercase tracking-widest mb-2">Nominal Denda</label>
                            <div class="relative">
                                <span class="absolute left-5 top-1/2 -translate-y-1/2 text-red-400 font-bold text-sm">Rp</span>
                                <input type="number" name="fine_amount" placeholder="0" min="0" class="w-full pl-12 pr-5 py-4 bg-white border border-red-200 rounded-2xl outline-none text-sm font-black text-red-700">
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Tingkat Kepercayaan (Rating)</label>
                    <select name="rating" x-model="rating" required class="w-full px-6 py-5 bg-slate-50 border border-slate-100 rounded-2xl font-black text-sm outline-none">
                        <option value="5">⭐⭐⭐⭐⭐ - Sangat Bertanggung Jawab</option>
                        <option value="4">⭐⭐⭐⭐ - Bagus & Tepat Waktu</option>
                        <option value="3">⭐⭐⭐ - Standar</option>
                        <option value="2">⭐⭐ - Kurang Terawat</option>
                        <option value="1">⭐ - Sangat Bermasalah</option>
                    </select>
                </div>

                <div class="flex gap-4 pt-4 leading-none">
                    <button type="button" @click="modalReturn = false" class="flex-1 px-6 py-5 rounded-2xl bg-gray-100 text-slate-500 font-black text-[10px] uppercase tracking-widest">Batal</button>
                    <button type="submit" class="flex-1 px-6 py-5 rounded-2xl bg-emerald-600 text-white font-black text-[10px] shadow-xl shadow-emerald-100 uppercase tracking-widest active:scale-95 transition-all">Tutup Sesi & Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ✅ MODAL KONFIRMASI LUNAS --}}
    <div x-show="modalPaid" x-cloak class="fixed inset-0 z-[130] flex items-center justify-center p-4 text-center">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="modalPaid = false"></div>
        <div x-show="modalPaid" x-transition.scale.95 class="relative w-full max-w-sm bg-white rounded-[3rem] shadow-2xl p-10 border border-white text-center leading-none">
            <div class="w-20 h-20 bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner"><i class="bi bi-cash-stack text-3xl"></i></div>
            <h3 class="text-2xl font-black text-gray-900 mb-2 uppercase tracking-tight">Konfirmasi Lunas?</h3>
            <p class="text-sm text-gray-500 mb-8 leading-relaxed font-medium">Anda yakin ingin menandai denda <span class="font-black text-gray-900" x-text="selectedReq.nama"></span> sebesar <span class="font-black text-emerald-600" x-text="'Rp ' + selectedReq.denda"></span> sudah lunas?</p>
            <form :action="actionRoute" method="POST" class="flex gap-4">
                @csrf @method('PUT')
                <button type="button" @click="modalPaid = false" class="flex-1 py-4 rounded-2xl bg-gray-100 text-slate-500 font-black text-[10px] uppercase tracking-widest">Batal</button>
                <button type="submit" class="flex-1 py-4 rounded-2xl bg-emerald-600 text-white font-black text-[10px] uppercase shadow-xl tracking-widest">Ya, Sudah Lunas</button>
            </form>
        </div>
    </div>

    {{-- 👁️ ✅ MODAL DETAIL LENGKAP & DINAMIS --}}
    <div x-show="modalDetail" x-cloak class="fixed inset-0 z-[120] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-md" @click="modalDetail = false"></div>
        <div x-show="modalDetail" x-transition.scale.95 class="relative w-full max-w-lg bg-white rounded-[3rem] shadow-2xl p-10 border border-white text-left leading-none overflow-y-auto max-h-[90vh] custom-scroll">
            <div class="mb-8 text-left leading-none">
                <span class="text-[9px] font-black text-emerald-500 uppercase tracking-[0.2em] bg-emerald-50 px-2 py-1 rounded" x-text="selectedReq.tipe"></span>
                <h3 class="text-3xl font-black text-gray-900 uppercase mt-4 tracking-tight leading-none" x-text="selectedReq.nama"></h3>
                <p class="text-[10px] font-black text-indigo-500 uppercase mt-2 tracking-widest" x-text="'Info: ' + selectedReq.sub_info"></p>
            </div>
            
            <div class="space-y-6 text-left leading-none">
                <div class="p-8 bg-gray-50 rounded-[2.5rem] border border-gray-100 space-y-4">
                    <div class="flex justify-between items-center"><span class="text-gray-400 uppercase text-[9px] font-black">Alat Praktikum</span><span class="text-gray-900 font-black uppercase text-sm" x-text="selectedReq.barang"></span></div>
                    <div class="flex justify-between items-center"><span class="text-gray-400 uppercase text-[9px] font-black">Jumlah Request</span><span class="text-emerald-600 font-black text-lg" x-text="selectedReq.jumlah + ' Unit'"></span></div>
                    <div class="flex justify-between items-center pt-2 border-t border-gray-200">
                        <span class="text-gray-400 uppercase text-[9px] font-black">Tanggal Pinjam</span>
                        <span class="text-gray-700 font-black text-xs" x-text="selectedReq.tgl_pinjam || '-'"></span>
                    </div>
                    {{-- ✅ INFO DEADLINE DI MODAL --}}
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400 uppercase text-[9px] font-black">Estimasi Pengembalian</span>
                        <span class="text-orange-600 font-black text-xs" x-text="selectedReq.deadline"></span>
                    </div>
                </div>

                <div x-show="selectedReq.tgl_kembali && selectedReq.tgl_kembali !== '-'" class="p-8 bg-emerald-50/50 rounded-[2.5rem] border border-emerald-100 text-left space-y-3">
                    <div class="flex justify-between items-center"><span class="text-[10px] font-black text-emerald-600 uppercase tracking-widest">Dikembalikan:</span><span class="text-xs font-black text-emerald-900" x-text="selectedReq.tgl_kembali"></span></div>
                    <div class="flex justify-between items-center"><span class="text-[10px] font-black text-emerald-600 uppercase tracking-widest">Kondisi Akhir:</span><span class="text-xs font-black uppercase px-2 py-1 rounded" :class="selectedReq.kondisi === 'aman' ? 'bg-emerald-200 text-emerald-800' : 'bg-red-200 text-red-800'" x-text="selectedReq.kondisi"></span></div>
                </div>

                <div x-show="selectedReq.denda && selectedReq.denda !== '0'" class="p-8 bg-red-50 rounded-[2.5rem] border border-red-100 text-left space-y-4">
                    <div class="flex items-center gap-2 text-red-600 font-black text-xs uppercase tracking-widest mb-2"><i class="bi bi-exclamation-triangle-fill"></i> Laporan Insiden</div>
                    <div class="flex justify-between items-center"><span class="text-red-400 uppercase text-[9px] font-black">Detail Kerusakan</span><span class="text-red-800 font-bold text-xs uppercase" x-text="selectedReq.detail_rusak || '-'"></span></div>
                    <div class="pt-4 border-t border-red-200 flex justify-between items-center"><span class="text-red-500 uppercase text-[9px] font-black">Total Denda</span><span class="text-red-600 font-black text-lg" x-text="'Rp ' + selectedReq.denda"></span></div>
                </div>

                <div class="p-6 bg-slate-50 border border-slate-100 rounded-[2rem]">
                    <p class="text-[9px] font-black text-slate-400 uppercase mb-2 tracking-widest">Catatan Peminjam:</p>
                    <p class="text-xs text-slate-600 font-medium italic leading-relaxed" x-text="selectedReq.catatan || '-'"></p>
                </div>
            </div>
            <button @click="modalDetail = false" class="w-full mt-10 py-5 bg-slate-900 text-white rounded-[1.5rem] font-black text-[10px] uppercase tracking-[0.2em] shadow-xl active:scale-95 transition-all">Tutup Informasi</button>
        </div>
    </div>

</body>
</html>