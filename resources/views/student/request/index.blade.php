<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Booking Alat - TekniLog Siswa</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
        .font-jakarta { font-family: 'Plus Jakarta Sans', sans-serif; }
        .custom-scroll::-webkit-scrollbar { width: 5px; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #06b6d4; border-radius: 20px; } /* Cyan Theme */
    </style>
</head>

<body class="antialiased flex h-screen w-full overflow-hidden text-left font-jakarta" 
      x-data="{ 
        sidebarOpen: false, 
        activeTab: 'booking', 
        searchQuery: '', 
        modalBooking: false,
        modalStatus: false,
        selectedRequest: {
            barang: '', qty: '', status: '', tgl: '', note: '', admin_note: '',
            tgl_kembali: '', kondisi: '', rating: 0,
            denda: 0, status_denda: '', hilang: 0, note_rusak: ''
        },
      }">

    {{-- Sidebar Student --}}
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'" 
           class="fixed inset-y-0 left-0 z-50 flex flex-col w-72 bg-slate-950 text-white border-r border-slate-900 md:static md:flex-shrink-0 h-full transition-transform duration-300">
        @include('student.partials.sidebar') 
    </aside>

    <div class="flex flex-1 flex-col h-full min-w-0 overflow-hidden">
        {{-- Header Student --}}
        @include('student.partials.header')

        <main class="flex-1 overflow-y-auto p-6 lg:p-10 pt-2 custom-scroll">
            <div class="mx-auto w-full max-w-[1550px] space-y-8">
                
                {{-- Alert System --}}
                @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" class="bg-emerald-500 text-white px-6 py-4 rounded-[2rem] shadow-lg flex justify-between items-center transition-all relative z-50">
                    <span class="font-bold text-sm uppercase tracking-widest"><i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}</span>
                    <button @click="show = false"><i class="bi bi-x-lg"></i></button>
                </div>
                @endif

                @if(session('error'))
                <div class="bg-red-500 text-white px-6 py-4 rounded-[2rem] shadow-lg flex justify-between items-center relative z-50">
                    <span class="font-bold text-sm uppercase tracking-widest"><i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}</span>
                </div>
                @endif

                {{-- 1. HEADER PAGE --}}
                <div class="flex flex-col xl:flex-row xl:items-end justify-between gap-6">
                    
                    {{-- KIRI: Judul & Tab Switcher --}}
                    <div class="text-left w-full xl:w-auto">
                        <div class="flex items-center gap-3 mb-4">
                            <h2 class="text-3xl font-black text-gray-900 tracking-tight uppercase leading-none">Booking & Tagihan</h2>
                            <span class="px-3 py-1.5 bg-cyan-600 text-white text-[10px] font-black rounded-lg uppercase tracking-widest shadow-lg shadow-cyan-100">
                                {{ Auth::user()->classRoom ? Auth::user()->classRoom->name : 'Siswa' }}
                            </span>
                        </div>
                        
                        {{-- Tab Menu --}}
                        <div class="flex bg-gray-200/50 p-1.5 rounded-2xl gap-1 w-fit border border-gray-100">
                            <button @click="activeTab = 'booking'; searchQuery = ''" :class="activeTab === 'booking' ? 'bg-white text-cyan-600 shadow-sm' : 'text-slate-500 hover:text-cyan-500'" class="px-8 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">Peminjaman Alat</button>
                            <button @click="activeTab = 'denda'; searchQuery = ''" :class="activeTab === 'denda' ? 'bg-white text-red-600 shadow-sm' : 'text-slate-500 hover:text-red-500'" class="px-8 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all flex items-center gap-2">
                                Tagihan Denda
                                @if($myLoans->where('fine_status', 'unpaid')->count() > 0)
                                    <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
                                @endif
                            </button>
                        </div>
                    </div>
                    
                    {{-- KANAN: Search & Action Button --}}
                    <div class="flex flex-col md:flex-row items-center gap-4 w-full xl:w-auto">
                        {{-- Search Bar --}}
                        <div class="relative w-full md:w-80 group">
                            <i class="bi bi-search absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-cyan-600 transition-colors"></i>
                            <input type="text" x-model="searchQuery" placeholder="Cari nama alat, status, dll..." 
                                   class="w-full pl-12 pr-6 py-4 bg-white border border-gray-100 rounded-[1.5rem] outline-none font-bold text-xs shadow-sm focus:ring-4 focus:ring-cyan-500/10 transition-all placeholder:text-gray-300">
                        </div>

                        {{-- Tombol Booking --}}
                        <template x-if="activeTab === 'booking'">
                            <button @click="modalBooking = true" class="w-full md:w-auto inline-flex items-center justify-center gap-3 rounded-[1.5rem] bg-cyan-600 px-8 py-4 text-xs font-black text-white shadow-xl shadow-cyan-100 hover:bg-cyan-700 transition-all active:scale-95 uppercase tracking-widest leading-none flex-shrink-0">
                                <i class="bi bi-plus-circle-fill text-lg"></i>
                                <span>Request Baru</span>
                            </button>
                        </template>
                    </div>
                </div>

                {{-- 2. TAB CONTENT: BOOKING ALAT --}}
                <div x-show="activeTab === 'booking'" x-transition>
                    {{-- STATS SUMMARY --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <div class="bg-white p-6 rounded-[2.5rem] border border-gray-100 shadow-sm flex items-center gap-5">
                            <div class="w-14 h-14 bg-cyan-50 text-cyan-600 rounded-2xl flex items-center justify-center text-2xl shadow-inner"><i class="bi bi-cart-check"></i></div>
                            <div class="text-left leading-none">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Riwayat Pesanan</p>
                                <p class="text-3xl font-black text-gray-900 leading-none">{{ $myLoans->count() }}</p>
                            </div>
                        </div>
                        <div class="bg-white p-6 rounded-[2.5rem] border border-gray-100 shadow-sm flex items-center gap-5 text-left">
                            <div class="w-14 h-14 bg-orange-50 text-orange-600 rounded-2xl flex items-center justify-center text-2xl shadow-inner"><i class="bi bi-hourglass-split"></i></div>
                            <div class="text-left leading-none">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Menunggu Verifikasi</p>
                                <p class="text-3xl font-black text-gray-900 leading-none">{{ $myLoans->where('status', 'pending')->count() }}</p>
                            </div>
                        </div>
                        <div class="bg-slate-900 p-6 rounded-[2.5rem] shadow-xl flex items-center gap-5 text-white text-left border border-slate-800 relative overflow-hidden">
                            <div class="absolute -right-4 -top-4 w-20 h-20 bg-cyan-500/10 rounded-full"></div>
                            <div class="w-14 h-14 bg-white/10 text-cyan-400 rounded-2xl flex items-center justify-center text-2xl shadow-inner"><i class="bi bi-person-badge-fill"></i></div>
                            <div class="text-left leading-none relative z-10">
                                <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Akun Siswa</p>
                                <p class="text-lg font-black uppercase leading-none tracking-tight">{{ Auth::user()->name }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- REQUEST HISTORY TABLE --}}
                    <div class="rounded-[2.5rem] bg-white shadow-sm border border-gray-100 overflow-hidden text-left">
                        <div class="p-8 border-b border-gray-50 flex items-center justify-between bg-gray-50/30">
                            <h3 class="text-[10px] font-black text-gray-800 uppercase tracking-[0.2em]">Monitoring Status Peminjaman</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <thead>
                                    <tr class="text-[10px] uppercase tracking-[0.2em] text-gray-400 font-black bg-white">
                                        <th class="px-8 py-6">Item Inventaris</th>
                                        <th class="px-8 py-6">Kuantitas</th>
                                        <th class="px-8 py-6">Tgl Request</th>
                                        <th class="px-8 py-6">Status</th>
                                        <th class="px-8 py-6 text-center">Opsi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @forelse($myLoans as $loan)
                                    <tr class="group hover:bg-gray-50/50 transition-all duration-200" 
                                        x-show="searchQuery === '' || '{{ strtolower($loan->item->name) }}'.includes(searchQuery.toLowerCase()) || '{{ strtolower($loan->status) }}'.includes(searchQuery.toLowerCase())">
                                        <td class="px-8 py-5 text-left">
                                            <div class="flex items-center gap-4">
                                                <div class="w-11 h-11 rounded-2xl bg-cyan-50 text-cyan-600 flex items-center justify-center font-black text-xs shadow-inner uppercase">{{ substr($loan->item->name, 0, 2) }}</div>
                                                <div class="text-left leading-tight">
                                                    <p class="text-gray-900 font-black text-base uppercase tracking-tight">{{ $loan->item->name }}</p>
                                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">{{ $loan->item->asset_code }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-8 py-5">
                                            <span class="font-black text-gray-600 uppercase text-xs">{{ $loan->quantity }} Unit</span>
                                        </td>
                                        <td class="px-8 py-5 text-left">
                                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none">{{ $loan->created_at->translatedFormat('d M Y') }}</span>
                                        </td>
                                        <td class="px-8 py-5">
                                            <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest
                                                {{ $loan->status === 'pending' ? 'bg-orange-50 text-orange-600 border border-orange-100' : '' }}
                                                {{ $loan->status === 'approved' ? 'bg-cyan-50 text-cyan-600 border border-cyan-100' : '' }}
                                                {{ $loan->status === 'returned' ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : '' }}
                                                {{ $loan->status === 'rejected' ? 'bg-red-50 text-red-600 border border-red-100' : '' }}">
                                                {{ $loan->status === 'pending' ? 'Menunggu' : ($loan->status === 'approved' ? 'Dipinjam' : ($loan->status === 'returned' ? 'Selesai' : 'Ditolak')) }}
                                            </span>
                                        </td>
                                        <td class="px-8 py-5 text-center">
                                            <button @click="selectedRequest = {
                                                barang: '{{ $loan->item->name }}',
                                                qty: '{{ $loan->quantity }}',
                                                status: '{{ $loan->status }}',
                                                tgl: '{{ $loan->created_at->format('d M Y, H:i') }}',
                                                note: '{{ $loan->reason }}',
                                                admin_note: '{{ $loan->admin_note ?? 'Belum ada catatan dari Toolman.' }}',
                                                tgl_kembali: '{{ $loan->return_date ? \Carbon\Carbon::parse($loan->return_date)->format('d M Y') : '-' }}',
                                                kondisi: '{{ $loan->return_condition }}',
                                                rating: '{{ $loan->rating }}',
                                                denda: '{{ number_format($loan->fine_amount, 0, ',', '.') }}',
                                                status_denda: '{{ $loan->fine_status }}',
                                                hilang: '{{ $loan->lost_quantity }}',
                                                note_rusak: '{{ $loan->return_note }}'
                                            }; modalStatus = true" class="w-10 h-10 rounded-xl bg-gray-50 text-gray-400 hover:bg-cyan-600 hover:text-white transition-all mx-auto flex items-center justify-center shadow-sm">
                                                <i class="bi bi-info-circle-fill"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="py-20 text-center">
                                            <div class="text-slate-200 text-6xl mb-4"><i class="bi bi-inbox"></i></div>
                                            <p class="text-gray-400 font-bold uppercase text-[10px] tracking-widest">Belum ada riwayat permintaan</p>
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
                            <h3 class="text-[10px] font-black text-red-600 uppercase tracking-[0.2em]">Rekapitulasi Tagihan & Kerusakan</h3>
                            <span class="text-[10px] font-black text-red-400 uppercase tracking-widest">Total Belum Bayar: Rp {{ number_format($myLoans->where('fine_status', 'unpaid')->sum('fine_amount'), 0, ',', '.') }}</span>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <thead>
                                    <tr class="text-[10px] uppercase tracking-[0.2em] text-gray-400 font-black bg-white">
                                        <th class="px-8 py-6">Barang</th>
                                        <th class="px-8 py-6">Tanggal Kembali</th>
                                        <th class="px-8 py-6">Detail Insiden</th>
                                        <th class="px-8 py-6 text-right">Nominal Denda</th>
                                        <th class="px-8 py-6 text-center">Status Pembayaran</th>
                                        <th class="px-8 py-6 text-center">Detail</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-red-50">
                                    @forelse($myLoans->where('fine_amount', '>', 0) as $fine)
                                    <tr class="group hover:bg-red-50/30 transition-all duration-200" 
                                        x-show="searchQuery === '' || '{{ strtolower($fine->item->name) }}'.includes(searchQuery.toLowerCase()) || '{{ strtolower($fine->return_note ?? '') }}'.includes(searchQuery.toLowerCase())">
                                        <td class="px-8 py-5">
                                            <span class="text-gray-900 font-black uppercase text-sm">{{ $fine->item->name }}</span>
                                        </td>
                                        <td class="px-8 py-5">
                                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ $fine->return_date ? \Carbon\Carbon::parse($fine->return_date)->format('d M Y') : '-' }}</span>
                                        </td>
                                        <td class="px-8 py-5">
                                            <span class="text-red-600 font-bold text-xs uppercase">{{ $fine->return_note ?? 'Tidak ada catatan' }}</span>
                                            <div class="mt-1">
                                                <span class="px-2 py-0.5 rounded bg-red-100 text-red-600 text-[9px] font-black uppercase">{{ $fine->return_condition }}</span>
                                                @if($fine->lost_quantity > 0)
                                                    <span class="ml-1 px-2 py-0.5 rounded bg-slate-100 text-slate-600 text-[9px] font-black uppercase">{{ $fine->lost_quantity }} Unit Hilang</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-8 py-5 text-right">
                                            <span class="font-black text-red-600 bg-red-50 px-3 py-1.5 rounded-lg border border-red-100 text-sm">Rp {{ number_format($fine->fine_amount, 0, ',', '.') }}</span>
                                        </td>
                                        <td class="px-8 py-5 text-center">
                                            <span class="px-3 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest 
                                                {{ $fine->fine_status === 'paid' ? 'bg-emerald-100 text-emerald-600 border border-emerald-200' : 'bg-red-100 text-red-600 border border-red-200 animate-pulse' }}">
                                                {{ $fine->fine_status === 'paid' ? 'LUNAS' : 'BELUM LUNAS' }}
                                            </span>
                                        </td>
                                        <td class="px-8 py-5 text-center">
                                            <button @click="selectedRequest = {
                                                barang: '{{ $fine->item->name }}',
                                                qty: '{{ $fine->quantity }}',
                                                status: '{{ $fine->status }}',
                                                tgl: '{{ $fine->created_at->format('d M Y, H:i') }}',
                                                note: '{{ $fine->reason }}',
                                                admin_note: '{{ $fine->admin_note ?? 'Belum ada catatan.' }}',
                                                tgl_kembali: '{{ $fine->return_date ? \Carbon\Carbon::parse($fine->return_date)->format('d M Y') : '-' }}',
                                                kondisi: '{{ $fine->return_condition }}',
                                                rating: '{{ $fine->rating }}',
                                                denda: '{{ number_format($fine->fine_amount, 0, ',', '.') }}',
                                                status_denda: '{{ $fine->fine_status }}',
                                                hilang: '{{ $fine->lost_quantity }}',
                                                note_rusak: '{{ $fine->return_note }}'
                                            }; modalStatus = true" class="w-10 h-10 rounded-xl bg-red-100 text-red-600 hover:bg-red-600 hover:text-white transition-all mx-auto flex items-center justify-center shadow-sm">
                                                <i class="bi bi-eye-fill"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="py-20 text-center">
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

            </div>
        </main>
    </div>

    {{-- 4. MODAL BOOKING ALAT (DENGAN ESTIMASI DURASI) --}}
    <div x-show="modalBooking" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-md" @click="modalBooking = false"></div>
        <div x-show="modalBooking" x-transition.scale.95 class="relative w-full max-w-xl bg-white rounded-[3rem] shadow-2xl p-10 border border-white text-left overflow-y-auto max-h-[90vh] custom-scroll">
            <div class="mb-10 text-left leading-none">
                <h3 class="text-3xl font-black text-gray-900 font-jakarta uppercase tracking-tight leading-none mb-4">Request Alat</h3>
                <div class="flex items-center gap-2">
                    <span class="text-[9px] font-black text-cyan-500 uppercase tracking-[0.2em] bg-cyan-50 px-2 py-1 rounded">Inventaris {{ Auth::user()->department ? Auth::user()->department->name : 'Umum' }}</span>
                </div>
            </div>
            
            <form action="{{ route('student.request.store') }}" method="POST" class="space-y-6">
                @csrf
                <div class="space-y-6">
                    <div class="text-left leading-none">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Pilih Barang Ready</label>
                        <select name="item_id" required class="w-full px-6 py-5 bg-gray-50 border border-gray-100 rounded-2xl outline-none font-bold text-sm text-gray-700 focus:ring-4 focus:ring-cyan-500/10 transition-all appearance-none shadow-inner">
                            <option value="">-- Cari Alat Praktikum --</option>
                            @foreach($items as $item)
                                <option value="{{ $item->id }}">{{ $item->name }} (Tersedia: {{ $item->stock }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-5 leading-none">
                        <div class="text-left">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Jumlah Pinjam</label>
                            <input type="number" name="quantity" required min="1" placeholder="0" class="w-full px-6 py-5 bg-gray-50 border border-gray-100 rounded-2xl outline-none font-bold text-sm focus:ring-4 focus:ring-cyan-500/10 transition-all shadow-inner">
                        </div>
                        
                        {{-- ✅ FITUR BARU: ESTIMASI DURASI --}}
                        <div class="text-left" x-data="{ mode: 'days' }">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Estimasi Durasi</label>
                            
                            {{-- Switcher --}}
                            <div class="flex bg-gray-100 p-1 rounded-2xl mb-3">
                                <button type="button" 
                                        @click="mode = 'hours'"
                                        :class="mode === 'hours' ? 'bg-white text-cyan-600 shadow-sm' : 'text-gray-400 hover:text-gray-600'"
                                        class="flex-1 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">
                                    <i class="bi bi-clock-history mr-1"></i> Per Jam
                                </button>
                                <button type="button" 
                                        @click="mode = 'days'"
                                        :class="mode === 'days' ? 'bg-white text-cyan-600 shadow-sm' : 'text-gray-400 hover:text-gray-600'"
                                        class="flex-1 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">
                                    <i class="bi bi-calendar-day mr-1"></i> Per Hari
                                </button>
                            </div>

                            {{-- Hidden Input Unit --}}
                            <input type="hidden" name="duration_unit" :value="mode">

                            {{-- Input Amount --}}
                            <div class="relative">
                                <input type="number" name="duration_amount" min="1" required 
                                       class="w-full px-6 py-5 bg-gray-50 border border-gray-100 rounded-2xl outline-none font-bold text-sm text-gray-700 focus:ring-4 focus:ring-cyan-500/10 transition-all placeholder:text-gray-300"
                                       :placeholder="mode === 'hours' ? 'Berapa jam?' : 'Berapa hari?'">
                                <span class="absolute right-6 top-1/2 -translate-y-1/2 text-[10px] font-black text-gray-400 uppercase tracking-widest" 
                                      x-text="mode === 'hours' ? 'JAM' : 'HARI'">
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="text-left leading-none">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Tujuan Penggunaan</label>
                        <textarea name="reason" required rows="4" placeholder="Jelaskan untuk praktek apa dan di ruangan mana..." class="w-full px-6 py-5 bg-gray-50 border border-gray-100 rounded-2xl outline-none font-medium text-sm focus:ring-4 focus:ring-cyan-500/10 transition-all shadow-inner"></textarea>
                    </div>
                </div>
                
                <div class="flex gap-4 pt-6">
                    <button type="button" @click="modalBooking = false" class="flex-1 px-6 py-5 rounded-[2rem] bg-gray-100 text-slate-500 font-black text-xs uppercase tracking-widest transition-all">Batalkan</button>
                    <button type="submit" class="flex-1 px-6 py-5 rounded-[2rem] bg-cyan-600 text-white font-black text-xs uppercase tracking-widest shadow-xl shadow-cyan-100 hover:bg-cyan-700 active:scale-95 transition-all">Kirim Pesanan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- 5. MODAL DETAIL STATUS & DENDA --}}
    <div x-show="modalStatus" x-cloak class="fixed inset-0 z-[120] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-md" @click="modalStatus = false"></div>
        <div x-show="modalStatus" x-transition.scale.95 class="relative w-full max-w-lg bg-white rounded-[3rem] shadow-2xl p-10 border border-white flex flex-col max-h-[90vh] text-left leading-none overflow-y-auto custom-scroll">
            
            {{-- HEADER MODAL --}}
            <div class="mb-8 text-left leading-none">
                <span class="text-[9px] font-black text-cyan-500 uppercase tracking-[0.2em] bg-cyan-50 px-2 py-1 rounded mb-2 inline-block">Detail Peminjaman</span>
                <h3 class="text-3xl font-black text-gray-900 uppercase mt-2 tracking-tight leading-none" x-text="selectedRequest.barang"></h3>
                <p class="text-[10px] font-black text-slate-400 uppercase mt-1 tracking-widest" x-text="'Jumlah: ' + selectedRequest.qty + ' Unit'"></p>
            </div>

            <div class="space-y-6 text-left leading-none">
                
                {{-- INFO STATUS & TANGGAL --}}
                <div class="p-6 bg-slate-50 rounded-[2rem] border border-slate-100 flex justify-between items-center">
                    <div class="flex flex-col">
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Status Saat Ini</span>
                        <span class="text-sm font-black uppercase" 
                              :class="{
                                  'text-orange-500': selectedRequest.status === 'pending',
                                  'text-cyan-600': selectedRequest.status === 'approved',
                                  'text-emerald-600': selectedRequest.status === 'returned',
                                  'text-red-600': selectedRequest.status === 'rejected'
                              }"
                              x-text="selectedRequest.status === 'pending' ? 'MENUNGGU VERIFIKASI' : (selectedRequest.status === 'approved' ? 'SEDANG DIPINJAM' : (selectedRequest.status === 'returned' ? 'SUDAH DIKEMBALIKAN' : 'DITOLAK'))"></span>
                    </div>
                    <div class="flex flex-col text-right">
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Tanggal Pinjam</span>
                        <span class="text-xs font-bold text-slate-700" x-text="selectedRequest.tgl"></span>
                    </div>
                </div>

                {{-- DETAIL PENGEMBALIAN --}}
                <div x-show="selectedRequest.status === 'returned'" class="p-8 bg-emerald-50/50 rounded-[2.5rem] border border-emerald-100 text-left space-y-3">
                    <div class="flex justify-between items-center border-b border-emerald-100 pb-2">
                        <span class="text-[10px] font-bold text-emerald-600 uppercase">Tanggal Kembali</span>
                        <span class="text-xs font-black text-emerald-900" x-text="selectedRequest.tgl_kembali"></span>
                    </div>
                    <div class="flex justify-between items-center border-b border-emerald-100 pb-2">
                        <span class="text-[10px] font-bold text-emerald-600 uppercase">Kondisi Fisik</span>
                        <span class="text-xs font-black uppercase px-2 py-1 rounded"
                              :class="selectedRequest.kondisi === 'aman' ? 'bg-emerald-200 text-emerald-800' : 'bg-red-200 text-red-800'"
                              x-text="selectedRequest.kondisi"></span>
                    </div>
                    <div x-show="selectedRequest.rating > 0" class="flex justify-between items-center">
                        <span class="text-[10px] font-bold text-emerald-600 uppercase">Rating Toolman</span>
                        <div class="flex text-orange-400 text-xs">
                            <template x-for="i in parseInt(selectedRequest.rating)">
                                <i class="bi bi-star-fill"></i>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- INFO DENDA --}}
                <div x-show="selectedRequest.denda && selectedRequest.denda !== '0'" class="p-8 bg-red-50 rounded-[2.5rem] border border-red-100 text-left space-y-4">
                    <div class="flex items-center gap-2 text-red-600 font-black text-xs uppercase tracking-widest mb-1">
                        <i class="bi bi-exclamation-triangle-fill"></i> Laporan Insiden
                    </div>
                    
                    <div class="flex justify-between items-center border-b border-red-200 pb-2">
                        <span class="text-[10px] font-bold text-red-400 uppercase">Kronologi / Kerusakan</span>
                        <span class="text-xs font-black text-red-800 uppercase text-right" x-text="selectedRequest.note_rusak || '-'"></span>
                    </div>

                    <div x-show="selectedRequest.hilang > 0" class="flex justify-between items-center border-b border-red-200 pb-2">
                        <span class="text-[10px] font-bold text-red-400 uppercase">Unit Hilang</span>
                        <span class="text-xs font-black text-red-800 uppercase" x-text="selectedRequest.hilang + ' Unit'"></span>
                    </div>

                    <div class="flex justify-between items-center pt-2">
                        <div class="flex flex-col">
                            <span class="text-[10px] font-bold text-red-400 uppercase">Total Denda</span>
                            <span class="text-lg font-black text-red-600" x-text="'Rp ' + selectedRequest.denda"></span>
                        </div>
                        <span class="px-3 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest"
                              :class="selectedRequest.status_denda === 'paid' ? 'bg-emerald-100 text-emerald-600' : 'bg-white text-red-600 border border-red-200 animate-pulse'"
                              x-text="selectedRequest.status_denda === 'paid' ? 'LUNAS' : 'BELUM BAYAR'"></span>
                    </div>
                </div>

                {{-- CATATAN & FEEDBACK --}}
                <div class="space-y-4">
                    <div class="p-6 bg-cyan-50/50 rounded-[2rem] border border-cyan-100 text-left leading-relaxed">
                        <p class="text-[9px] font-black text-cyan-500 uppercase tracking-widest mb-2 leading-none">Alasan Peminjaman:</p>
                        <p class="text-sm text-slate-600 font-medium italic" x-text="selectedRequest.note"></p>
                    </div>

                    <div class="p-6 bg-slate-900 rounded-[2rem] border border-slate-800 text-left leading-relaxed shadow-xl">
                        <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest mb-2 leading-none">Feedback Toolman:</p>
                        <p class="text-sm text-slate-300 font-medium" x-text="selectedRequest.admin_note"></p>
                    </div>
                </div>
            </div>

            <button @click="modalStatus = false" class="w-full mt-8 py-5 bg-slate-900 text-white rounded-[2rem] font-black text-[10px] uppercase tracking-[0.2em] shadow-xl hover:bg-cyan-600 active:scale-95 transition-all">Tutup Informasi</button>
        </div>
    </div>

</body>
</html>