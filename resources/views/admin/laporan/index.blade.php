<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Monitoring Logistik - TekniLog Admin</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
        .font-jakarta { font-family: 'Plus Jakarta Sans', sans-serif; }
        .custom-scroll::-webkit-scrollbar { width: 5px; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #f472b6; border-radius: 20px; }
    </style>
</head>

<body class="antialiased flex h-screen w-full overflow-hidden text-left font-jakarta" 
      x-data="{ 
        sidebarOpen: false, 
        activeTab: 'sirkulasi', 
        searchQuery: '', 
        modalDetail: false,
        viewType: '', 
        
        // Data Dinamis untuk Modal
        selectedData: { 
            id: '', item: '', user: '', dept: '', date: '', status: '', asset_code: '',
            return_date: '', condition: '', rating: 0, admin_note: '', denda: 0, lost_qty: 0,
            desc: '', feedback: '', status_raw: '', photo: '' 
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
                
                {{-- 1. HEADER & FILTER FORM --}}
                <div class="space-y-6">
                    <div class="text-left leading-none">
                        <h2 class="text-4xl font-black text-slate-900 tracking-tight uppercase leading-none">Pusat Oversight Logistik</h2>
                        <p class="text-sm font-bold text-pink-500 mt-3 uppercase tracking-widest leading-none border-l-4 border-pink-500 pl-4">Periode Laporan: {{ $summary['period'] }}</p>
                    </div>

                    <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm leading-none">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-6 items-end">
                            
                            {{-- Filter Date & Dept --}}
                            <form action="{{ route('admin.laporan') }}" method="GET" class="contents">
                                <div class="flex flex-col gap-3">
                                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Mulai</label>
                                    <input type="date" name="start_date" value="{{ request('start_date', now()->startOfMonth()->format('Y-m-d')) }}" 
                                           class="w-full px-5 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl font-bold text-xs outline-none focus:ring-4 focus:ring-pink-500/10 transition-all">
                                </div>

                                <div class="flex flex-col gap-3">
                                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Sampai</label>
                                    <input type="date" name="end_date" value="{{ request('end_date', now()->endOfMonth()->format('Y-m-d')) }}" 
                                           class="w-full px-5 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl font-bold text-xs outline-none focus:ring-4 focus:ring-pink-500/10 transition-all">
                                </div>

                                <div class="flex flex-col gap-3">
                                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Unit Jurusan</label>
                                    <select name="department_id" class="w-full px-5 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl font-bold text-xs outline-none focus:ring-4 focus:ring-pink-500/10 transition-all appearance-none cursor-pointer">
                                        <option value="">Semua Unit</option>
                                        @foreach($departments as $dept)
                                            <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <button type="submit" class="bg-slate-900 text-white rounded-2xl py-3.5 px-4 font-black text-[10px] uppercase tracking-widest hover:bg-pink-600 transition-all shadow-xl active:scale-95">
                                    <i class="bi bi-filter"></i> Terapkan
                                </button>
                            </form>

                            {{-- Live Search & Export --}}
                            <div class="flex flex-col gap-3">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Pencarian Cepat</label>
                                <div class="relative">
                                    <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-pink-500"></i>
                                    <input type="text" x-model="searchQuery" placeholder="Cari data..." 
                                           class="w-full pl-11 pr-4 py-3.5 bg-pink-50/30 border border-pink-100 rounded-2xl font-bold text-xs outline-none focus:ring-4 focus:ring-pink-500/10 transition-all placeholder:text-pink-300">
                                </div>
                            </div>

                            <a href="{{ route('admin.laporan.export', request()->all()) }}" 
                               class="bg-pink-500 text-white rounded-2xl py-3.5 px-4 font-black text-[10px] uppercase tracking-widest hover:bg-pink-600 transition-all shadow-xl shadow-pink-100 active:scale-95 flex items-center justify-center gap-2">
                                <i class="bi bi-file-earmark-pdf-fill"></i> Cetak PDF
                            </a>
                        </div>
                    </div>
                </div>

                {{-- 2. ANALYTICS CARDS --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 leading-none">
                    <div class="bg-white p-7 rounded-[2.5rem] border border-gray-100 shadow-sm flex items-center gap-5">
                        <div class="w-14 h-14 bg-pink-50 text-pink-600 rounded-2xl flex items-center justify-center text-2xl shadow-inner"><i class="bi bi-journal-check"></i></div>
                        <div class="text-left"><p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Total Log</p><p class="text-3xl font-black text-gray-900 leading-none">{{ $summary['total'] }}</p></div>
                    </div>
                    <div class="bg-white p-7 rounded-[2.5rem] border border-gray-100 shadow-sm flex items-center gap-5">
                        <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center text-2xl shadow-inner"><i class="bi bi-arrow-left-right"></i></div>
                        <div class="text-left"><p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Sedang Pinjam</p><p class="text-3xl font-black text-gray-900 leading-none">{{ $summary['active'] }}</p></div>
                    </div>
                    <div class="bg-white p-7 rounded-[2.5rem] border border-gray-100 shadow-sm flex items-center gap-5">
                        <div class="w-14 h-14 bg-red-50 text-red-600 rounded-2xl flex items-center justify-center text-2xl shadow-inner"><i class="bi bi-exclamation-triangle"></i></div>
                        <div class="text-left"><p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Barang Rusak</p><p class="text-3xl font-black text-red-600 leading-none">{{ $summary['broken'] }}</p></div>
                    </div>
                    <div class="bg-slate-900 p-7 rounded-[2.5rem] shadow-xl flex items-center gap-5 text-white border border-slate-800">
                        <div class="w-14 h-14 bg-white/10 text-emerald-400 rounded-2xl flex items-center justify-center text-2xl shadow-inner"><i class="bi bi-patch-check"></i></div>
                        <div class="text-left"><p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5">Data Selesai</p><p class="text-3xl font-black text-white leading-none">{{ $summary['done'] }}</p></div>
                    </div>
                </div>

                {{-- 3. TAB SWITCHER --}}
                <div class="flex bg-gray-200/50 p-1.5 rounded-2xl gap-1 w-fit border border-gray-100">
                    <button @click="activeTab = 'sirkulasi'; searchQuery = ''" :class="activeTab === 'sirkulasi' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500'" class="px-8 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">Sirkulasi Aset</button>
                    <button @click="activeTab = 'masalah'; searchQuery = ''" :class="activeTab === 'masalah' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500'" class="px-8 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">
                        Keluhan Siswa
                        <span x-show="{{ $summary['problem_count'] }} > 0" class="ml-2 bg-red-500 text-white px-2 py-0.5 rounded-full text-[8px] animate-pulse">{{ $summary['problem_count'] }}</span>
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
                                                <span class="text-[9px] font-black text-pink-600 bg-pink-50 px-2 py-0.5 rounded uppercase border border-pink-100 w-fit text-left">{{ $log->user->department->name ?? 'UNIT' }}</span>
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
                                            {{-- ✅ FIXED: Gunakan kutip SATU (') untuk atribut @click agar JSON kutip DUA (") aman --}}
                                            <button type="button" @click='viewType = "log"; selectedData = {
                                                item: @json($log->item->name),
                                                user: @json($log->user->name),
                                                dept: @json(optional($log->user->department)->name ?? "Non-Unit"),
                                                date: "{{ $log->created_at->format("d M Y, H:i") }}",
                                                return_date: "{{ $log->return_date ? \Carbon\Carbon::parse($log->return_date)->format("d M Y, H:i") : "-" }}",
                                                status: "{{ $log->status }}",
                                                condition: "{{ $log->return_condition ?? "-" }}",
                                                rating: "{{ $log->rating ?? 0 }}",
                                                admin_note: @json($log->admin_note),
                                                denda: "{{ number_format($log->fine_amount, 0, ",", ".") }}",
                                                lost_qty: "{{ $log->lost_quantity }}",
                                                asset_code: @json($log->item->asset_code)
                                            }; modalDetail = true'
                                            class="w-11 h-11 rounded-2xl bg-gray-50 text-gray-400 hover:bg-pink-600 hover:text-white transition-all mx-auto flex items-center justify-center shadow-sm cursor-pointer">
                                                <i class="bi bi-eye-fill"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="5" class="py-24 text-center text-gray-300 font-bold uppercase text-[10px] tracking-[0.3em] italic">Tidak ada riwayat terekam.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- SLIDE 2: DAFTAR KENDALA SISWA (EQUAL HEIGHT & CLICKABLE FIX) --}}
                    <div x-show="activeTab === 'masalah'" x-transition class="p-10 text-left">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                            @forelse($incomingProblems as $problem)
                            {{-- CARD UTAMA --}}
                            <div x-show="searchQuery === '' || '{{ strtolower($problem->user->name) }}'.includes(searchQuery.toLowerCase())"
                                 class="p-8 rounded-[2.5rem] border border-gray-100 bg-gray-50/50 hover:border-pink-200 hover:bg-white hover:shadow-xl transition-all relative overflow-hidden group text-left h-full flex flex-col justify-between min-h-[320px]">
                                
                                {{-- 1. Status Color --}}
                                <div class="absolute top-0 left-0 w-2 h-full {{ $problem->status === 'pending' ? 'bg-orange-500' : 'bg-emerald-500' }}"></div>
                                
                                {{-- 2. Tombol Detail (FIX: Z-Index 20 & Single Quote Click Wrapper) --}}
                                <div class="absolute top-6 right-6 z-20">
                                    <button type="button" @click='viewType = "report"; selectedData = {
                                        id: "{{ $problem->id }}",
                                        item: @json($problem->item->name),
                                        user: @json($problem->user->name),
                                        dept: @json(optional($problem->user->department)->name ?? "Non-Unit"),
                                        date: "{{ $problem->created_at->format("d M Y, H:i") }}",
                                        status: "{{ $problem->status }}",
                                        desc: @json($problem->description),
                                        feedback: @json($problem->admin_note ?? "Belum ada catatan."),
                                        asset_code: @json($problem->item->asset_code),
                                        photo: @json($problem->photo_path ? asset("storage/".$problem->photo_path) : null)
                                    }; modalDetail = true' 
                                    class="w-10 h-10 rounded-full bg-white border border-gray-100 text-gray-400 hover:text-pink-600 hover:border-pink-100 flex items-center justify-center shadow-sm transition-all cursor-pointer">
                                        <i class="bi bi-eye-fill"></i>
                                    </button>
                                </div>

                                {{-- 3. Top Content --}}
                                <div class="flex-1 pr-12 mb-4 relative z-10">
                                    <div class="mb-4">
                                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 leading-none text-left">{{ $problem->created_at->diffForHumans() }}</p>
                                        <h4 class="text-lg font-black text-slate-900 uppercase leading-tight text-left truncate w-full">{{ $problem->item->name }}</h4>
                                        <p class="text-[10px] font-black text-pink-500 mt-2 uppercase text-left">{{ $problem->user->name }} ({{ $problem->user->department->name ?? 'UNIT' }})</p>
                                    </div>
                                    
                                    {{-- Status Badge --}}
                                    <div class="mb-4">
                                        <span class="px-2.5 py-1 rounded-lg text-[8px] font-black uppercase bg-white border border-gray-200 shadow-sm leading-none">{{ $problem->status }}</span>
                                    </div>

                                    <div class="p-6 bg-white rounded-3xl border border-gray-100 mb-6 shadow-inner text-left leading-none h-28 overflow-hidden relative">
                                        <p class="text-[9px] font-black text-red-500 uppercase mb-3 tracking-widest leading-none text-left">Keluhan:</p>
                                        <p class="text-xs text-gray-600 font-medium italic leading-relaxed text-left line-clamp-3">"{{ $problem->description }}"</p>
                                        
                                        {{-- ✅ INDIKATOR FOTO --}}
                                        @if($problem->photo_path)
                                            <div class="absolute bottom-3 right-3 text-red-400 text-xs flex items-center gap-1 bg-red-50 px-2 py-1 rounded-lg border border-red-50">
                                                <i class="bi bi-camera-fill"></i> <span class="text-[8px] font-bold uppercase">Ada Bukti</span>
                                            </div>
                                        @endif
                                        <div class="absolute bottom-0 left-0 w-full h-6 bg-gradient-to-t from-white to-transparent"></div>
                                    </div>
                                </div>
                                
                                {{-- 4. Bottom Info --}}
                                <div class="mt-auto relative z-10">
                                    <div class="flex items-center gap-4 text-left leading-none">
                                        <div class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center shadow-md"><i class="bi bi-person-badge-fill"></i></div>
                                        <div class="text-left leading-none">
                                            <p class="text-[9px] font-black text-gray-400 uppercase leading-none mb-1.5 text-left">Monitoring Terpusat</p>
                                            <p class="text-[11px] font-bold text-slate-800 uppercase tracking-tight text-left">Akses Penuh Administrator</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="col-span-full py-20 text-center leading-none text-gray-400 italic">
                                Tidak ada laporan kendala masuk.
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    {{-- 👁️ MODAL DETAIL CERDAS --}}
    <div x-show="modalDetail" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4 text-left">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="modalDetail = false"></div>
        <div x-show="modalDetail" x-transition.scale.95 class="relative w-full max-w-lg bg-white rounded-[3rem] shadow-2xl p-10 border border-white text-left leading-none overflow-y-auto max-h-[90vh] custom-scroll">
            
            <div class="flex justify-between items-start mb-8 text-left leading-none">
                <div class="text-left leading-none">
                    <p class="text-[9px] font-black uppercase tracking-[0.2em] mb-3 leading-none text-left" 
                       :class="viewType === 'log' ? 'text-pink-500' : 'text-orange-500'"
                       x-text="viewType === 'log' ? 'Detail Log Sirkulasi' : 'Detail Laporan Kendala'"></p>
                    <h3 class="text-2xl font-black text-gray-900 uppercase font-jakarta leading-none text-left" x-text="selectedData.item"></h3>
                    <p class="text-[10px] font-bold text-gray-400 mt-2 uppercase" x-text="'Kode: ' + (selectedData.asset_code || '-')"></p>
                </div>
                <button type="button" @click="modalDetail = false" class="text-gray-400 hover:text-gray-600"><i class="bi bi-x-lg"></i></button>
            </div>

            <div class="space-y-6 text-left leading-none">
                {{-- Info Umum (Muncul di kedua tipe) --}}
                <div class="grid grid-cols-2 gap-4">
                    <div class="p-6 bg-slate-50 rounded-[2rem] border border-gray-100 text-left">
                        <span class="text-[9px] font-black text-gray-400 uppercase block mb-2">PIC / Pelapor</span>
                        <span class="text-sm font-black text-gray-800 uppercase" x-text="selectedData.user"></span>
                    </div>
                    <div class="p-6 bg-slate-50 rounded-[2rem] border border-gray-100 text-left">
                        <span class="text-[9px] font-black text-gray-400 uppercase block mb-2">Unit Jurusan</span>
                        <span class="text-sm font-black text-gray-800 uppercase" x-text="selectedData.dept"></span>
                    </div>
                </div>

                {{-- TAMPILAN KHUSUS: LOG SIRKULASI --}}
                <template x-if="viewType === 'log'">
                    <div class="space-y-6">
                        <div class="p-8 bg-emerald-50/50 rounded-[2.5rem] border border-emerald-100 text-left space-y-3">
                            <div class="flex justify-between items-center border-b border-emerald-100 pb-2">
                                <span class="text-[10px] font-bold text-gray-500 uppercase">Waktu Pinjam</span>
                                <span class="text-xs font-black text-gray-800" x-text="selectedData.date"></span>
                            </div>
                            <div class="flex justify-between items-center border-b border-emerald-100 pb-2">
                                <span class="text-[10px] font-bold text-gray-500 uppercase">Waktu Kembali</span>
                                <span class="text-xs font-black text-gray-800" x-text="selectedData.return_date"></span>
                            </div>
                            <div class="flex justify-between items-center pt-1">
                                <span class="text-[10px] font-bold text-gray-500 uppercase">Kondisi Akhir</span>
                                <span class="text-xs font-black uppercase px-2 py-1 rounded bg-white border border-gray-200" x-text="selectedData.condition"></span>
                            </div>
                            <div x-show="selectedData.rating > 0" class="flex justify-between items-center pt-1">
                                <span class="text-[10px] font-bold text-gray-500 uppercase">Rating</span>
                                <div class="flex text-orange-400 text-xs">
                                    <template x-for="i in parseInt(selectedData.rating)"><i class="bi bi-star-fill"></i></template>
                                </div>
                            </div>
                        </div>

                        {{-- Info Denda di Log --}}
                        <div x-show="selectedData.denda !== '0'" class="p-6 bg-red-50 rounded-[2rem] border border-red-100 text-left">
                            <p class="text-[9px] font-black text-red-500 uppercase mb-2">Catatan Insiden / Denda</p>
                            <div class="flex justify-between items-center">
                                <span class="text-xs font-bold text-red-700">Total Denda</span>
                                <span class="text-sm font-black text-red-800" x-text="'Rp ' + selectedData.denda"></span>
                            </div>
                            <div x-show="selectedData.lost_qty > 0" class="mt-2 text-[10px] text-red-600 font-medium">
                                *Unit Hilang/Rusak: <span x-text="selectedData.lost_qty"></span>
                            </div>
                        </div>

                        <div class="p-6 bg-white border border-gray-100 rounded-[2rem] text-left">
                            <p class="text-[9px] font-black text-gray-400 uppercase mb-2">Catatan Admin:</p>
                            <p class="text-xs text-gray-600 italic" x-text="selectedData.admin_note || '-'"></p>
                        </div>
                    </div>
                </template>

                {{-- TAMPILAN KHUSUS: LAPORAN KENDALA --}}
                <template x-if="viewType === 'report'">
                    <div class="space-y-6">
                        
                        {{-- ✅ FOTO BUKTI DI MODAL --}}
                        <template x-if="selectedData.photo">
                            <div class="w-full h-56 bg-gray-100 rounded-[2rem] overflow-hidden border border-gray-200 relative group shadow-sm">
                                <img :src="selectedData.photo" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                                <div class="absolute bottom-4 left-4 bg-black/60 text-white px-3 py-1.5 rounded-full backdrop-blur-md">
                                    <p class="text-[9px] font-bold uppercase tracking-wider"><i class="bi bi-image-fill mr-1"></i> Bukti Kerusakan</p>
                                </div>
                            </div>
                        </template>
                        <template x-if="!selectedData.photo">
                            <div class="w-full h-24 bg-slate-50 rounded-2xl flex items-center justify-center border border-dashed border-slate-200 text-slate-400">
                                <p class="text-[10px] font-bold uppercase tracking-widest"><i class="bi bi-image-alt mr-2"></i> Tidak ada foto bukti</p>
                            </div>
                        </template>

                        <div class="p-8 bg-red-50/50 rounded-[2.5rem] border border-red-100 text-left leading-relaxed">
                            <p class="text-[9px] font-black text-red-500 uppercase mb-3 tracking-widest leading-none">Kronologi / Keluhan Siswa:</p>
                            <p class="text-sm text-slate-700 font-medium italic" x-text="selectedData.desc"></p>
                        </div>

                        <div class="p-8 bg-slate-900 rounded-[2.5rem] border border-slate-800 text-left leading-relaxed shadow-xl">
                            <div class="flex items-center justify-between mb-3">
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none">Status & Feedback:</p>
                                <span class="text-[9px] font-black uppercase text-white bg-slate-700 px-2 py-1 rounded" x-text="selectedData.status"></span>
                            </div>
                            <p class="text-sm text-white font-medium" x-text="selectedData.feedback"></p>
                        </div>
                    </div>
                </template>

            </div>

            <button type="button" @click="modalDetail = false" class="w-full mt-8 py-5 bg-slate-900 text-white rounded-[1.5rem] font-black text-[10px] uppercase tracking-[0.2em] shadow-xl hover:bg-pink-600 active:scale-95 transition-all">Tutup Detail</button>
        </div>
    </div>

</body>
</html>