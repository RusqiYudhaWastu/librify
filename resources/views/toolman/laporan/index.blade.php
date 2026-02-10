<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pusat Laporan Logistik - TekniLog Toolman</title>

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
        activeTab: 'sirkulasi',
        searchQuery: '', 
        modalDetail: false,
        modalFinish: false, 
        viewType: '', // 'log' atau 'report'
        
        // Data Dinamis untuk Modal
        selectedData: { 
            id: '', item: '', user: '', dept: '', date: '', status: '', asset_code: '',
            return_date: '', condition: '', rating: 0, admin_note: '', denda: 0, lost_qty: 0,
            desc: '', feedback: '', status_raw: '', photo: '' 
        }
      }">

    {{-- Sidebar Toolman --}}
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'" 
           class="fixed inset-y-0 left-0 z-50 flex flex-col w-72 bg-[#064E3B] text-white border-r border-emerald-900 md:static md:flex-shrink-0 h-full transition-transform duration-300">
        @include('toolman.partials.sidebar')
    </aside>

    <div class="flex flex-1 flex-col h-full min-w-0 overflow-hidden text-left">
        @include('toolman.partials.header')

        <main class="flex-1 overflow-y-auto p-6 lg:p-10 pt-2 custom-scroll">
            <div class="mx-auto w-full max-w-[1550px] space-y-8">
                
                {{-- Alert System --}}
                @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" class="bg-emerald-500 text-white px-6 py-4 rounded-[2rem] shadow-lg flex justify-between items-center transition-all relative z-50">
                    <span class="font-bold text-sm uppercase tracking-widest"><i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}</span>
                    <button @click="show = false"><i class="bi bi-x-lg"></i></button>
                </div>
                @endif

                {{-- 1. HEADER & FILTER FORM --}}
                <div class="space-y-6">
                    <div class="text-left leading-none">
                        <h2 class="text-4xl font-black text-slate-900 tracking-tight uppercase leading-none">Logistik & Pelaporan</h2>
                        <p class="text-sm font-bold text-emerald-600 mt-3 uppercase tracking-widest leading-none border-l-4 border-emerald-600 pl-4">Periode Data: {{ $summary['period'] }}</p>
                    </div>

                    {{-- Form Filter & Search --}}
                    <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm leading-none">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-6 items-end">
                            
                            {{-- Filter Date --}}
                            <form action="{{ route('toolman.laporan') }}" method="GET" class="contents">
                                <div class="flex flex-col gap-3">
                                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Mulai</label>
                                    <input type="date" name="start_date" value="{{ request('start_date', now()->startOfMonth()->format('Y-m-d')) }}" 
                                           class="w-full px-5 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl font-bold text-xs outline-none focus:ring-4 focus:ring-emerald-500/10">
                                </div>

                                <div class="flex flex-col gap-3">
                                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Sampai</label>
                                    <input type="date" name="end_date" value="{{ request('end_date', now()->endOfMonth()->format('Y-m-d')) }}" 
                                           class="w-full px-5 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl font-bold text-xs outline-none focus:ring-4 focus:ring-emerald-500/10">
                                </div>
                                
                                <button type="submit" class="bg-slate-900 text-white rounded-2xl py-3.5 px-4 font-black text-[10px] uppercase tracking-widest hover:bg-emerald-600 transition-all shadow-xl active:scale-95">
                                    <i class="bi bi-filter"></i> Filter
                                </button>
                            </form>

                            {{-- Live Search --}}
                            <div class="flex flex-col gap-3">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Pencarian Cepat</label>
                                <div class="relative">
                                    <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-emerald-500"></i>
                                    <input type="text" x-model="searchQuery" placeholder="Cari nama, alat, dll..." 
                                           class="w-full pl-11 pr-4 py-3.5 bg-emerald-50/30 border border-emerald-100 rounded-2xl font-bold text-xs outline-none focus:ring-4 focus:ring-emerald-500/10 transition-all placeholder:text-emerald-700/50">
                                </div>
                            </div>

                            {{-- Export Button --}}
                            <a href="{{ route('toolman.laporan.export', request()->all()) }}" 
                               class="bg-emerald-600 text-white rounded-2xl py-3.5 px-4 font-black text-[10px] uppercase tracking-widest hover:bg-emerald-700 transition-all shadow-xl shadow-emerald-100 active:scale-95 flex items-center justify-center gap-2">
                                <i class="bi bi-file-earmark-pdf-fill"></i> Cetak PDF
                            </a>
                        </div>
                    </div>
                </div>

                {{-- 2. ANALYTICS CARDS --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 leading-none text-left">
                    <div class="bg-white p-7 rounded-[2.5rem] border border-gray-100 shadow-sm flex items-center gap-5">
                        <div class="w-14 h-14 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center text-2xl shadow-inner"><i class="bi bi-journal-text"></i></div>
                        <div class="text-left">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 leading-none">Total Log</p>
                            <p class="text-3xl font-black text-gray-900 leading-none">{{ $summary['total_logs'] }}</p>
                        </div>
                    </div>
                    <div class="bg-white p-7 rounded-[2.5rem] border border-gray-100 shadow-sm flex items-center gap-5">
                        <div class="w-14 h-14 bg-orange-50 text-orange-600 rounded-2xl flex items-center justify-center text-2xl shadow-inner"><i class="bi bi-chat-dots-fill"></i></div>
                        <div class="text-left">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 leading-none">Kendala Baru</p>
                            <p class="text-3xl font-black text-orange-600 leading-none">{{ $summary['pending_problems'] }}</p>
                        </div>
                    </div>
                </div>

                {{-- 3. TAB SWITCHER --}}
                <div class="flex bg-gray-200/50 p-1.5 rounded-2xl gap-1 w-fit border border-gray-100">
                    <button @click="activeTab = 'sirkulasi'; searchQuery = ''" :class="activeTab === 'sirkulasi' ? 'bg-white text-emerald-600 shadow-sm' : 'text-slate-500'" class="px-8 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">Sirkulasi Aset</button>
                    <button @click="activeTab = 'kendala'; searchQuery = ''" :class="activeTab === 'kendala' ? 'bg-white text-emerald-600 shadow-sm' : 'text-slate-500'" class="px-8 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">
                        Laporan Siswa
                        <span x-show="{{ $summary['pending_problems'] }} > 0" class="ml-2 bg-red-500 text-white px-2 py-0.5 rounded-full text-[8px] animate-pulse">{{ $summary['pending_problems'] }}</span>
                    </button>
                </div>

                {{-- 4. CONTENT AREA --}}
                <div class="rounded-[3rem] bg-white shadow-sm border border-gray-100 overflow-hidden text-left">
                    
                    {{-- SLIDE 1: TABEL SIRKULASI --}}
                    <div x-show="activeTab === 'sirkulasi'" x-transition>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <thead>
                                    <tr class="text-[10px] uppercase tracking-[0.2em] text-gray-400 font-black bg-gray-50/50 leading-none">
                                        <th class="px-8 py-7">Waktu</th>
                                        <th class="px-8 py-7">Identitas Peminjam</th>
                                        <th class="px-8 py-7">Barang & Asset</th>
                                        <th class="px-8 py-7">Status</th>
                                        <th class="px-8 py-7 text-center">Detail</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50 leading-tight">
                                    @forelse($logs as $log)
                                    <tr x-show="searchQuery === '' || '{{ strtolower($log->user->name) }}'.includes(searchQuery.toLowerCase()) || '{{ strtolower($log->item->name) }}'.includes(searchQuery.toLowerCase())"
                                        class="group hover:bg-gray-50/50 transition-all duration-200">
                                        <td class="px-8 py-6">
                                            <div class="flex flex-col text-left leading-none">
                                                <span class="text-gray-900 font-black uppercase text-[11px] mb-1.5">{{ $log->created_at->format('d M Y') }}</span>
                                                <span class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">{{ $log->created_at->format('H:i') }} WIB</span>
                                            </div>
                                        </td>
                                        <td class="px-8 py-6">
                                            <div class="flex flex-col text-left leading-none">
                                                <span class="text-gray-900 font-black uppercase text-sm tracking-tight mb-1.5">{{ $log->user->name }}</span>
                                                <span class="text-[9px] font-black text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded uppercase border border-emerald-100 w-fit">{{ $log->user->department->name ?? 'UNIT' }}</span>
                                            </div>
                                        </td>
                                        <td class="px-8 py-6">
                                            <div class="flex flex-col text-left leading-none">
                                                <span class="text-slate-800 font-black uppercase text-xs">{{ $log->item->name }}</span>
                                                <span class="text-[9px] text-slate-400 font-bold mt-1">QTY: {{ $log->quantity }} Unit</span>
                                            </div>
                                        </td>
                                        <td class="px-8 py-6">
                                            @php
                                                $badgeClass = match(true) {
                                                    in_array($log->return_condition, ['rusak', 'hilang']) => 'bg-red-50 text-red-600 border-red-100',
                                                    $log->status === 'returned' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                                    default => 'bg-blue-50 text-blue-600 border-blue-100'
                                                };
                                                $statusText = $log->return_condition ? 'KONDISI: '.$log->return_condition : 'STATUS: '.$log->status;
                                            @endphp
                                            <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest border {{ $badgeClass }}">
                                                {{ $statusText }}
                                            </span>
                                        </td>
                                        <td class="px-8 py-6 text-center">
                                            <button @click="viewType = 'log'; selectedData = {
                                                item: '{{ $log->item->name }}',
                                                user: '{{ $log->user->name }}',
                                                dept: '{{ $log->user->department->name ?? 'N/A' }}',
                                                date: '{{ $log->created_at->format('d M Y, H:i') }}',
                                                return_date: '{{ $log->return_date ? \Carbon\Carbon::parse($log->return_date)->format('d M Y, H:i') : '-' }}',
                                                status: '{{ $log->status }}',
                                                condition: '{{ $log->return_condition ?? '-' }}',
                                                rating: '{{ $log->rating ?? 0 }}',
                                                admin_note: '{{ $log->admin_note }}',
                                                denda: '{{ number_format($log->fine_amount, 0, ',', '.') }}',
                                                lost_qty: '{{ $log->lost_quantity }}',
                                                asset_code: '{{ $log->item->asset_code }}'
                                            }; modalDetail = true" 
                                            class="w-10 h-10 rounded-xl bg-gray-50 text-gray-400 hover:bg-emerald-600 hover:text-white transition-all mx-auto flex items-center justify-center shadow-sm">
                                                <i class="bi bi-eye-fill"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="5" class="py-24 text-center text-gray-300 font-bold uppercase text-[10px] tracking-[0.3em] italic">Belum ada riwayat terekam.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- SLIDE 2: DAFTAR KENDALA SISWA (UPDATED ONE CLICK ACTION) --}}
                    <div x-show="activeTab === 'kendala'" x-transition class="p-10 text-left">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                            @forelse($incomingProblems as $problem)
                            <div x-show="searchQuery === '' || '{{ strtolower($problem->user->name) }}'.includes(searchQuery.toLowerCase()) || '{{ strtolower($problem->item->name) }}'.includes(searchQuery.toLowerCase())"
                                 class="p-8 rounded-[2.5rem] border border-gray-100 bg-gray-50/50 hover:border-emerald-200 hover:bg-white hover:shadow-xl transition-all relative overflow-hidden group text-left flex flex-col justify-between h-full">
                                
                                <div class="absolute top-0 left-0 w-2 h-full {{ $problem->status === 'fixed' ? 'bg-emerald-500' : ($problem->status === 'checked' ? 'bg-blue-500' : 'bg-orange-500') }}"></div>
                                
                                {{-- Tombol Detail (View Foto & Info) --}}
                                <button @click="viewType = 'report'; selectedData = {
                                    id: '{{ $problem->id }}',
                                    item: '{{ $problem->item->name }}',
                                    user: '{{ $problem->user->name }}',
                                    dept: '{{ $problem->user->department->name ?? 'N/A' }}',
                                    date: '{{ $problem->created_at->format('d M Y, H:i') }}',
                                    status: '{{ $problem->status }}',
                                    desc: '{{ $problem->description }}',
                                    feedback: '{{ $problem->admin_note ?? 'Belum ada catatan.' }}',
                                    asset_code: '{{ $problem->item->asset_code }}',
                                    photo: '{{ $problem->photo_path ? asset('storage/'.$problem->photo_path) : null }}' {{-- ✅ FOTO DIKIRIM --}}
                                }; modalDetail = true"
                                class="absolute top-6 right-6 w-10 h-10 rounded-full bg-white border border-gray-100 text-gray-400 hover:text-emerald-600 hover:border-emerald-100 flex items-center justify-center shadow-sm transition-all z-10">
                                    <i class="bi bi-eye-fill"></i>
                                </button>

                                <div>
                                    <div class="mb-4 pr-12">
                                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2">{{ $problem->created_at->diffForHumans() }}</p>
                                        <h4 class="text-lg font-black text-slate-900 uppercase leading-tight truncate w-40">{{ $problem->item->name }}</h4>
                                        <p class="text-[10px] font-black text-emerald-500 mt-1 uppercase">{{ $problem->user->name }}</p>
                                    </div>

                                    <div class="p-6 bg-white rounded-3xl border border-gray-100 mb-6 shadow-inner text-left leading-none h-24 overflow-hidden relative">
                                        <p class="text-[9px] font-black text-red-500 uppercase mb-3 tracking-widest">Keluhan:</p>
                                        <p class="text-xs text-gray-600 font-medium italic leading-relaxed line-clamp-2">"{{ $problem->description }}"</p>
                                        
                                        {{-- ✅ INDIKATOR FOTO --}}
                                        @if($problem->photo_path)
                                            <div class="absolute bottom-3 right-3 text-red-400 text-xs flex items-center gap-1 bg-red-50 px-2 py-1 rounded-lg">
                                                <i class="bi bi-camera-fill"></i> <span class="text-[8px] font-bold uppercase">Ada Bukti</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                
                                {{-- ✅ ONE CLICK ACTION -> OPEN MODAL FORM --}}
                                @if($problem->status !== 'fixed')
                                    <button @click="selectedData = {
                                        id: '{{ $problem->id }}',
                                        item: '{{ $problem->item->name }}',
                                        user: '{{ $problem->user->name }}'
                                    }; modalFinish = true"
                                    class="w-full py-4 bg-slate-900 hover:bg-emerald-600 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-lg transition-all active:scale-95 flex items-center justify-center gap-2">
                                        <i class="bi bi-check-circle-fill"></i> Selesai Perbaikan
                                    </button>
                                @else
                                    <div class="w-full py-4 bg-emerald-100 text-emerald-700 rounded-2xl font-black text-[10px] uppercase tracking-widest flex items-center justify-center gap-2 border border-emerald-200">
                                        <i class="bi bi-check-all text-lg"></i> Kasus Selesai
                                    </div>
                                @endif
                            </div>
                            @empty
                            <div class="col-span-full py-20 text-center leading-none">
                                <i class="bi bi-shield-check text-5xl text-emerald-200 mb-4 block"></i>
                                <p class="text-gray-400 font-bold uppercase text-[10px] tracking-[0.3em]">Aman! Tidak ada laporan kendala.</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    {{-- ✅ MODAL KONFIRMASI SELESAI (DENGAN CATATAN) --}}
    <div x-show="modalFinish" x-cloak class="fixed inset-0 z-[120] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="modalFinish = false"></div>
        <div x-show="modalFinish" x-transition.scale.95 class="relative w-full max-w-md bg-white rounded-[3rem] shadow-2xl p-8 border border-white text-left overflow-hidden">
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-emerald-50 text-emerald-600 rounded-[1.5rem] flex items-center justify-center mx-auto mb-4 text-3xl shadow-inner"><i class="bi bi-clipboard-check-fill"></i></div>
                <h3 class="text-xl font-black text-gray-900 uppercase tracking-tight">Konfirmasi Perbaikan</h3>
                <p class="text-xs text-gray-500 mt-1">Barang: <span class="font-bold text-gray-800" x-text="selectedData.item"></span></p>
            </div>

            <form :action="'/toolman/laporan/masalah/' + selectedData.id" method="POST" class="space-y-5">
                @csrf @method('PUT')
                <input type="hidden" name="status" value="fixed">
                
                <div>
                    <label class="block text-[10px] font-black text-emerald-600 uppercase tracking-widest mb-2 ml-1">Catatan Perbaikan / Solusi</label>
                    <textarea name="admin_note" required rows="3" class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl outline-none text-sm font-bold text-slate-700 focus:ring-4 focus:ring-emerald-500/10 placeholder:text-gray-300" placeholder="Contoh: Kabel diganti baru, dibersihkan, dll..."></textarea>
                </div>

                <div class="flex gap-3">
                    <button type="button" @click="modalFinish = false" class="flex-1 py-4 bg-gray-100 text-gray-500 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-gray-200 transition-all">Batal</button>
                    <button type="submit" class="flex-1 py-4 bg-emerald-600 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-xl hover:bg-emerald-700 transition-all active:scale-95">Simpan & Selesai</button>
                </div>
            </form>
        </div>
    </div>

    {{-- 👁️ MODAL DETAIL (LOG & REPORT WITH PHOTO) --}}
    <div x-show="modalDetail" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4 text-left">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="modalDetail = false"></div>
        <div x-show="modalDetail" x-transition.scale.95 class="relative w-full max-w-lg bg-white rounded-[3rem] shadow-2xl p-10 border border-white text-left leading-none overflow-y-auto max-h-[90vh] custom-scroll">
            
            <div class="flex justify-between items-start mb-8 text-left leading-none">
                <div class="text-left leading-none">
                    <p class="text-[9px] font-black uppercase tracking-[0.2em] mb-3 leading-none text-left" 
                       :class="viewType === 'log' ? 'text-emerald-500' : 'text-orange-500'"
                       x-text="viewType === 'log' ? 'Detail Log Sirkulasi' : 'Detail Laporan Kendala'"></p>
                    <h3 class="text-2xl font-black text-gray-900 uppercase font-jakarta leading-none text-left" x-text="selectedData.item"></h3>
                    <p class="text-[10px] font-bold text-gray-400 mt-2 uppercase" x-text="'Kode: ' + (selectedData.asset_code || '-')"></p>
                </div>
                <button @click="modalDetail = false" class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 hover:text-gray-600 transition-colors"><i class="bi bi-x-lg"></i></button>
            </div>

            <div class="space-y-6 text-left leading-none">
                
                {{-- INFO UMUM USER (MUNCUL DI KEDUA TIPE) --}}
                <div class="flex items-center gap-4 p-4 bg-slate-50 rounded-2xl border border-slate-100">
                    <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center shadow-sm text-slate-600"><i class="bi bi-person-badge"></i></div>
                    <div>
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1">Pelapor / Peminjam</p>
                        <p class="text-sm font-black text-slate-800 uppercase" x-text="selectedData.user"></p>
                    </div>
                </div>

                {{-- TAMPILAN KHUSUS: LAPORAN KENDALA --}}
                <template x-if="viewType === 'report'">
                    <div class="space-y-6">
                        
                        {{-- ✅ MENAMPILKAN BUKTI FOTO DI MODAL --}}
                        <template x-if="selectedData.photo">
                            <div class="w-full h-56 bg-gray-100 rounded-[2rem] overflow-hidden border border-gray-200 relative group shadow-sm">
                                <img :src="selectedData.photo" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                                <div class="absolute bottom-4 left-4 bg-black/60 text-white px-3 py-1.5 rounded-full backdrop-blur-md">
                                    <p class="text-[9px] font-bold uppercase tracking-wider"><i class="bi bi-image-fill mr-1"></i> Bukti Kerusakan</p>
                                </div>
                            </div>
                        </template>
                        {{-- Placeholder Jika Tidak Ada Foto --}}
                        <template x-if="!selectedData.photo">
                            <div class="w-full h-24 bg-slate-50 rounded-2xl flex items-center justify-center border border-dashed border-slate-200 text-slate-400">
                                <p class="text-[10px] font-bold uppercase tracking-widest"><i class="bi bi-image-alt mr-2"></i> Tidak ada foto bukti</p>
                            </div>
                        </template>

                        <div class="p-6 bg-red-50 rounded-[2rem] border border-red-100 text-left">
                            <p class="text-[9px] font-black text-red-500 uppercase mb-2 tracking-widest">Detail Keluhan:</p>
                            <p class="text-sm text-gray-700 font-medium italic leading-relaxed" x-text="selectedData.desc"></p>
                        </div>

                        <div class="p-6 bg-slate-900 rounded-[2rem] border border-slate-800 shadow-xl">
                            <p class="text-[9px] font-black text-slate-500 uppercase mb-2 tracking-widest">Catatan Penanganan (Anda):</p>
                            <p class="text-sm text-white font-medium" x-text="selectedData.feedback || 'Belum ada catatan.'"></p>
                        </div>
                    </div>
                </template>

                {{-- TAMPILAN KHUSUS: LOG SIRKULASI --}}
                <template x-if="viewType === 'log'">
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-5 bg-emerald-50 rounded-2xl border border-emerald-100">
                                <p class="text-[9px] font-bold text-emerald-600 uppercase mb-1">Status</p>
                                <p class="text-sm font-black text-emerald-800 uppercase" x-text="selectedData.status"></p>
                            </div>
                            <div class="p-5 bg-slate-50 rounded-2xl border border-slate-100">
                                <p class="text-[9px] font-bold text-slate-500 uppercase mb-1">Rating</p>
                                <div class="flex text-orange-400 text-xs">
                                    <template x-for="i in parseInt(selectedData.rating)"><i class="bi bi-star-fill"></i></template>
                                </div>
                            </div>
                        </div>
                        <div class="p-6 rounded-2xl border border-slate-100 bg-white">
                            <div class="flex justify-between border-b border-slate-50 pb-2 mb-2">
                                <span class="text-[10px] font-bold text-slate-400 uppercase">Pinjam</span>
                                <span class="text-xs font-black text-slate-700" x-text="selectedData.date"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-[10px] font-bold text-slate-400 uppercase">Kembali</span>
                                <span class="text-xs font-black text-slate-700" x-text="selectedData.return_date"></span>
                            </div>
                        </div>
                        <div x-show="selectedData.denda !== '0'" class="p-6 bg-red-50 rounded-2xl border border-red-100">
                            <p class="text-[9px] font-black text-red-500 uppercase mb-2">Catatan Insiden</p>
                            <p class="text-lg font-black text-red-600" x-text="'Denda: Rp ' + selectedData.denda"></p>
                            <p class="text-xs text-red-400 mt-1 font-bold italic" x-text="selectedData.admin_note"></p>
                        </div>
                    </div>
                </template>
            </div>

            <button @click="modalDetail = false" class="w-full mt-8 py-5 bg-slate-900 text-white rounded-[2rem] font-black text-[10px] uppercase tracking-[0.2em] shadow-xl hover:bg-emerald-600 active:scale-95 transition-all">Tutup Detail</button>
        </div>
    </div>

</body>
</html>