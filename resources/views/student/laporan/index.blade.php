<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Riwayat & Laporan - TekniLog Siswa</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
        .font-jakarta { font-family: 'Plus Jakarta Sans', sans-serif; }
        .custom-scroll::-webkit-scrollbar { width: 5px; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #06b6d4; border-radius: 20px; } /* Cyan Scrollbar */
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
            type: '', // 'loan' atau 'report'
            item: '', date: '', status: '', desc: '', feedback: '', photo: '', 
            qty: 0, return_date: '', condition: '', asset_code: ''
        }
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

                @if($errors->any())
                <div class="bg-red-500 text-white px-6 py-4 rounded-[2rem] shadow-lg relative z-50 mb-4">
                    <ul class="list-disc list-inside text-sm font-bold">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                {{-- 1. HEADER PAGE & TABS --}}
                <div class="flex flex-col xl:flex-row xl:items-end justify-between gap-6">
                    <div class="text-left w-full xl:w-auto">
                        <div class="flex items-center gap-3 mb-4">
                            <h2 class="text-3xl font-black text-gray-900 tracking-tight uppercase leading-none">Riwayat & Laporan</h2>
                            <span class="px-3 py-1.5 bg-cyan-600 text-white text-[10px] font-black rounded-lg uppercase tracking-widest shadow-lg shadow-cyan-100">Student Area</span>
                        </div>
                        
                        <div class="flex bg-gray-200/50 p-1.5 rounded-2xl gap-1 w-fit border border-gray-100">
                            <button @click="activeTab = 'riwayat'; searchQuery = ''" :class="activeTab === 'riwayat' ? 'bg-white text-cyan-600 shadow-sm' : 'text-slate-500 hover:text-cyan-500'" class="px-8 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all flex items-center gap-2">
                                <i class="bi bi-clock-history text-sm"></i> Riwayat Pemakaian
                            </button>
                            <button @click="activeTab = 'laporan'; searchQuery = ''" :class="activeTab === 'laporan' ? 'bg-white text-red-600 shadow-sm' : 'text-slate-500 hover:text-red-500'" class="px-8 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all flex items-center gap-2">
                                <i class="bi bi-exclamation-triangle-fill text-sm"></i> Lapor Kendala
                            </button>
                        </div>
                    </div>
                    
                    <div class="flex flex-col md:flex-row items-center gap-4 w-full xl:w-auto">
                        <div class="relative w-full md:w-80 group">
                            <i class="bi bi-search absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-cyan-600 transition-colors"></i>
                            <input type="text" x-model="searchQuery" placeholder="Cari data..." 
                                   class="w-full pl-12 pr-6 py-4 bg-white border border-gray-100 rounded-[1.5rem] outline-none font-bold text-xs shadow-sm focus:ring-4 focus:ring-cyan-500/10 transition-all placeholder:text-gray-300">
                        </div>
                        
                        <template x-if="activeTab === 'laporan'">
                            <button @click="modalTambah = true" class="w-full md:w-auto inline-flex items-center justify-center gap-3 rounded-[1.5rem] bg-red-600 px-8 py-4 text-xs font-black text-white shadow-xl shadow-red-100 hover:bg-red-700 transition-all active:scale-95 uppercase tracking-widest leading-none flex-shrink-0">
                                <i class="bi bi-plus-lg text-lg"></i> Buat Laporan
                            </button>
                        </template>
                    </div>
                </div>

                {{-- 2. TAB RIWAYAT (DENGAN FILTER & CETAK PDF) --}}
                <div x-show="activeTab === 'riwayat'" x-transition>
                    
                    {{-- FILTER BAR --}}
                    <div class="bg-white p-6 rounded-[2.5rem] border border-gray-100 shadow-sm mb-6">
                        <form action="{{ route('student.laporan.export') }}" method="GET" class="flex flex-col md:flex-row items-end gap-4">
                            <div class="flex-1 w-full text-left">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 block ml-2">Dari Tanggal</label>
                                <input type="date" name="start_date" class="w-full px-5 py-3 bg-gray-50 border border-gray-100 rounded-2xl font-bold text-xs outline-none focus:ring-4 focus:ring-cyan-500/10">
                            </div>
                            <div class="flex-1 w-full text-left">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 block ml-2">Sampai Tanggal</label>
                                <input type="date" name="end_date" class="w-full px-5 py-3 bg-gray-50 border border-gray-100 rounded-2xl font-bold text-xs outline-none focus:ring-4 focus:ring-cyan-500/10">
                            </div>
                            <button type="submit" class="w-full md:w-auto px-8 py-3.5 bg-slate-900 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-cyan-600 transition-all shadow-lg flex items-center justify-center gap-2">
                                <i class="bi bi-printer-fill text-sm"></i> Cetak PDF
                            </button>
                        </form>
                    </div>

                    {{-- TABLE RIWAYAT --}}
                    <div class="rounded-[2.5rem] bg-white shadow-sm border border-gray-100 overflow-hidden text-left">
                        <div class="p-8 border-b border-gray-50 flex items-center justify-between bg-gray-50/30">
                            <h3 class="text-[10px] font-black text-gray-800 uppercase tracking-[0.2em]">Log Aktivitas Peminjaman</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <thead>
                                    <tr class="text-[10px] uppercase tracking-[0.2em] text-gray-400 font-black bg-white">
                                        <th class="px-8 py-6">Barang / Alat</th>
                                        <th class="px-8 py-6">Tanggal Pinjam</th>
                                        <th class="px-8 py-6">Status</th>
                                        <th class="px-8 py-6 text-center">Kondisi Akhir</th>
                                        <th class="px-8 py-6 text-center">Detail</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @forelse($loans as $loan)
                                    <tr class="group hover:bg-gray-50/50 transition-all duration-200"
                                        x-show="searchQuery === '' || '{{ strtolower($loan->item->name) }}'.includes(searchQuery.toLowerCase())">
                                        <td class="px-8 py-5">
                                            <div class="flex items-center gap-4">
                                                <div class="w-11 h-11 rounded-2xl bg-cyan-50 text-cyan-600 flex items-center justify-center font-black text-xs shadow-inner uppercase">
                                                    {{ substr($loan->item->name, 0, 2) }}
                                                </div>
                                                <div class="text-left leading-tight">
                                                    <p class="text-gray-900 font-black text-sm uppercase tracking-tight">{{ $loan->item->name }}</p>
                                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">Kode: {{ $loan->item->asset_code }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-8 py-5">
                                            <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest">{{ $loan->created_at->format('d M Y, H:i') }}</span>
                                        </td>
                                        <td class="px-8 py-5">
                                            @php
                                                $statusClass = match($loan->status) {
                                                    'borrowed' => 'bg-yellow-50 text-yellow-600 border-yellow-100',
                                                    'returned' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                                    'pending'  => 'bg-gray-50 text-gray-500 border-gray-200',
                                                    default    => 'bg-red-50 text-red-600 border-red-100'
                                                };
                                                $statusText = match($loan->status) {
                                                    'borrowed' => 'Dipinjam',
                                                    'returned' => 'Selesai',
                                                    'pending'  => 'Menunggu',
                                                    default    => 'Ditolak'
                                                };
                                            @endphp
                                            <span class="px-3 py-1.5 rounded-lg text-[9px] font-black uppercase border {{ $statusClass }}">
                                                {{ $statusText }}
                                            </span>
                                        </td>
                                        <td class="px-8 py-5 text-center">
                                            @if($loan->return_condition)
                                                <span class="px-3 py-1.5 rounded-lg text-[9px] font-black uppercase border
                                                    {{ $loan->return_condition === 'aman' ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-red-50 text-red-600 border-red-100' }}">
                                                    {{ $loan->return_condition }}
                                                </span>
                                            @else
                                                <span class="text-gray-300 font-black">-</span>
                                            @endif
                                        </td>
                                        <td class="px-8 py-5 text-center">
                                            <button type="button" @click='detailData = {
                                                type: "loan",
                                                item: @json($loan->item->name),
                                                asset_code: @json($loan->item->asset_code),
                                                qty: "{{ $loan->quantity }}",
                                                date: "{{ $loan->created_at->format("d M Y, H:i") }}",
                                                status: "{{ $statusText }}",
                                                desc: @json($loan->reason),
                                                feedback: @json($loan->admin_note ?? "Tidak ada catatan."),
                                                return_date: "{{ $loan->return_date ? \Carbon\Carbon::parse($loan->return_date)->format("d M Y, H:i") : "Belum dikembalikan" }}",
                                                condition: "{{ $loan->return_condition ?? "-" }}"
                                            }; modalDetail = true' 
                                            class="w-9 h-9 rounded-xl bg-slate-100 text-slate-500 hover:bg-cyan-600 hover:text-white transition-all flex items-center justify-center shadow-sm mx-auto">
                                                <i class="bi bi-eye-fill"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="py-20 text-center">
                                            <p class="text-gray-400 font-bold uppercase text-[10px] tracking-widest">Belum ada riwayat peminjaman.</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- 3. TAB CONTENT: DAFTAR LAPORAN (CARD MODEL BARU) --}}
                <div x-show="activeTab === 'laporan'" x-transition x-cloak>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        @forelse($reports as $report)
                        {{-- CARD SEJAJAR & RAPI --}}
                        <div x-show="searchQuery === '' || '{{ strtolower($report->item->name) }}'.includes(searchQuery.toLowerCase())" 
                             class="p-8 rounded-[2.5rem] border border-gray-100 bg-white hover:border-cyan-200 hover:shadow-xl transition-all relative overflow-hidden group flex flex-col justify-between h-full min-h-[300px] text-left">
                            
                            {{-- 1. Status Color Bar (Indikator Warna Kiri) --}}
                            <div class="absolute top-0 left-0 w-2 h-full 
                                {{ $report->status === 'pending' ? 'bg-orange-500' : '' }}
                                {{ $report->status === 'process' ? 'bg-blue-500' : '' }}
                                {{ $report->status === 'completed' || $report->status === 'fixed' ? 'bg-emerald-500' : '' }}">
                            </div>

                            {{-- 2. Tombol Detail (Fixed Top Right) --}}
                            <div class="absolute top-6 right-6 z-20">
                                <button type="button" @click='detailData = {
                                    type: "report",
                                    item: @json($report->item->name),
                                    asset_code: @json($report->item->asset_code),
                                    date: "{{ $report->created_at->format("d M Y, H:i") }}",
                                    desc: @json($report->description),
                                    status: "{{ $report->status }}",
                                    feedback: @json($report->admin_note ?? "Belum ada tanggapan teknisi."),
                                    photo: @json($report->photo_path ? asset("storage/".$report->photo_path) : null)
                                }; modalDetail = true' 
                                class="w-10 h-10 rounded-full bg-white border border-gray-100 text-gray-400 hover:text-cyan-600 hover:border-cyan-100 flex items-center justify-center shadow-sm transition-all cursor-pointer">
                                    <i class="bi bi-eye-fill"></i>
                                </button>
                            </div>

                            {{-- 3. Top Content --}}
                            <div class="flex-1 pr-12 mb-4 relative z-10">
                                <div class="mb-4">
                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 leading-none text-left">{{ $report->created_at->diffForHumans() }}</p>
                                    <h4 class="text-lg font-black text-slate-900 uppercase leading-tight truncate w-full">{{ $report->item->name }}</h4>
                                    <p class="text-[10px] font-black text-cyan-500 mt-1 uppercase text-left">Kode: {{ $report->item->asset_code }}</p>
                                </div>

                                {{-- Status Badge Text --}}
                                <div class="mb-4">
                                    <span class="px-2.5 py-1 rounded-lg text-[8px] font-black uppercase border shadow-sm leading-none
                                        {{ $report->status === 'pending' ? 'bg-orange-50 text-orange-600 border-orange-100' : '' }}
                                        {{ $report->status === 'process' ? 'bg-blue-50 text-blue-600 border-blue-100' : '' }}
                                        {{ $report->status === 'completed' || $report->status === 'fixed' ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : '' }}">
                                        {{ $report->status === 'pending' ? 'Menunggu' : ($report->status === 'process' ? 'Diproses' : 'Selesai') }}
                                    </span>
                                </div>

                                <div class="p-6 bg-gray-50 rounded-3xl border border-gray-100 mb-2 shadow-inner text-left leading-none h-28 overflow-hidden relative">
                                    <p class="text-[9px] font-black text-red-500 uppercase mb-3 tracking-widest leading-none">Keluhan Saya:</p>
                                    <p class="text-xs text-gray-600 font-medium italic leading-relaxed text-left line-clamp-3">"{{ $report->description }}"</p>
                                    
                                    {{-- ✅ Indikator Ada Foto --}}
                                    @if($report->photo_path)
                                        <div class="absolute bottom-3 right-3 text-red-400 text-xs flex items-center gap-1 bg-white px-2 py-1 rounded-lg border border-red-50 shadow-sm">
                                            <i class="bi bi-camera-fill"></i> <span class="text-[8px] font-bold uppercase">Bukti</span>
                                        </div>
                                    @endif
                                    <div class="absolute bottom-0 left-0 w-full h-6 bg-gradient-to-t from-gray-50 to-transparent"></div>
                                </div>
                            </div>

                            {{-- 4. Bottom Info (Feedback Indicator) --}}
                            <div class="mt-auto relative z-10 pt-4 border-t border-gray-50">
                                @if($report->admin_note)
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-slate-900 text-white flex items-center justify-center text-xs"><i class="bi bi-chat-text-fill"></i></div>
                                        <div>
                                            <p class="text-[9px] font-bold text-slate-400 uppercase leading-none mb-1">Balasan Teknisi</p>
                                            <p class="text-[10px] font-black text-slate-800 uppercase leading-none">Ada Tanggapan</p>
                                        </div>
                                    </div>
                                @else
                                    <div class="flex items-center gap-3 opacity-50">
                                        <div class="w-8 h-8 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center text-xs"><i class="bi bi-hourglass"></i></div>
                                        <div>
                                            <p class="text-[9px] font-bold text-gray-400 uppercase leading-none mb-1">Status</p>
                                            <p class="text-[10px] font-black text-gray-500 uppercase leading-none">Menunggu...</p>
                                        </div>
                                    </div>
                                @endif
                            </div>

                        </div>
                        @empty
                        <div class="col-span-full py-20 text-center text-gray-400 italic">
                            <i class="bi bi-clipboard-check text-4xl mb-4 block text-slate-200"></i>
                            Tidak ada laporan kendala yang diajukan.
                        </div>
                        @endforelse
                    </div>
                </div>

            </div>
        </main>
    </div>

    {{-- 3. MODAL BUAT LAPORAN --}}
    <div x-show="modalTambah" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-md" @click="modalTambah = false"></div>
        <div x-show="modalTambah" x-transition.scale.95 class="relative w-full max-w-lg bg-white rounded-[3rem] shadow-2xl p-10 border border-white text-left overflow-y-auto max-h-[90vh] custom-scroll">
            <h3 class="text-2xl font-black text-gray-900 font-jakarta uppercase tracking-tight leading-none mb-8">Ajukan Laporan</h3>
            
            <form action="{{ route('student.laporan.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                <div class="text-left leading-none">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Pilih Alat Bermasalah</label>
                    <select name="loan_id" required class="w-full px-6 py-5 bg-gray-50 border border-gray-100 rounded-2xl outline-none font-bold text-sm text-gray-700 focus:ring-4 focus:ring-red-500/10 transition-all appearance-none shadow-inner">
                        <option value="">-- Pilih dari Peminjaman --</option>
                        @foreach($loans as $loan)
                            <option value="{{ $loan->id }}">{{ $loan->item->name }} (Dipinjam: {{ $loan->created_at->format('d M') }})</option>
                        @endforeach
                    </select>
                    <p class="text-[9px] text-gray-400 mt-2 ml-2 italic">*Hanya barang yang sedang/pernah dipinjam yang bisa dilaporkan.</p>
                </div>

                <div class="text-left leading-none">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Kronologi / Kerusakan</label>
                    <textarea name="description" required rows="4" placeholder="Jelaskan detail kerusakan atau kendala yang dialami..." class="w-full px-6 py-5 bg-gray-50 border border-gray-100 rounded-2xl outline-none font-medium text-sm focus:ring-4 focus:ring-red-500/10 transition-all shadow-inner"></textarea>
                </div>

                <div class="text-left leading-none">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Bukti Foto (Opsional)</label>
                    <div class="flex items-center gap-4">
                        <div class="w-20 h-20 rounded-2xl bg-gray-100 border border-dashed border-gray-300 flex items-center justify-center overflow-hidden relative">
                            <template x-if="!photoPreview"><i class="bi bi-camera text-2xl text-gray-400"></i></template>
                            <template x-if="photoPreview"><img :src="photoPreview" class="w-full h-full object-cover"></template>
                        </div>
                        <input type="file" name="photo" accept=".jpg,.jpeg,.png" class="text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100"
                               @change="const file = $event.target.files[0]; if(file){ const reader = new FileReader(); reader.onload = (e) => { photoPreview = e.target.result }; reader.readAsDataURL(file); }">
                    </div>
                    <p class="text-[9px] text-gray-400 mt-2 ml-1">*Hanya format JPG/PNG</p>
                </div>

                <div class="flex gap-4 pt-6">
                    <button type="button" @click="modalTambah = false" class="flex-1 px-6 py-5 rounded-[2rem] bg-gray-100 text-slate-500 font-black text-xs uppercase tracking-widest transition-all">Batal</button>
                    <button type="submit" class="flex-1 px-6 py-5 rounded-[2rem] bg-red-600 text-white font-black text-xs uppercase tracking-widest shadow-xl shadow-red-100 hover:bg-red-700 active:scale-95 transition-all">Kirim Laporan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- 4. MODAL DETAIL PINTAR (Loan / Report) --}}
    <div x-show="modalDetail" x-cloak class="fixed inset-0 z-[120] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-md" @click="modalDetail = false"></div>
        <div x-show="modalDetail" x-transition.scale.95 class="relative w-full max-w-lg bg-white rounded-[3rem] shadow-2xl p-10 border border-white flex flex-col max-h-[90vh] text-left leading-none overflow-y-auto custom-scroll">
            
            <div class="flex justify-between items-start mb-8 text-left">
                <div class="text-left">
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] mb-3" 
                       :class="detailData.type === 'loan' ? 'text-cyan-500' : 'text-red-500'" 
                       x-text="detailData.type === 'loan' ? 'Detail Peminjaman' : 'Detail Laporan'"></p>
                    <h3 class="text-2xl font-black text-gray-900 font-jakarta uppercase tracking-tight leading-none" x-text="detailData.item"></h3>
                    <p class="text-xs font-bold text-gray-400 mt-2" x-text="'Kode: ' + detailData.asset_code"></p>
                </div>
                <button @click="modalDetail = false" class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 hover:text-gray-600 transition-colors flex-shrink-0"><i class="bi bi-x-lg"></i></button>
            </div>

            <div class="space-y-6 text-left">
                
                {{-- A. TAMPILAN KHUSUS RIWAYAT (LOAN) --}}
                <template x-if="detailData.type === 'loan'">
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-6 bg-slate-50 rounded-[2rem] border border-slate-100">
                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2">Jumlah</p>
                                <p class="text-base font-black text-gray-800" x-text="detailData.qty + ' Unit'"></p>
                            </div>
                            <div class="p-6 bg-slate-50 rounded-[2rem] border border-slate-100">
                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2">Status</p>
                                <p class="text-xs font-black text-cyan-600 uppercase" x-text="detailData.status"></p>
                            </div>
                        </div>
                        <div class="p-6 bg-cyan-50/50 rounded-[2rem] border border-cyan-100 space-y-3">
                            <div class="flex justify-between border-b border-cyan-100 pb-2">
                                <span class="text-[10px] font-bold text-cyan-600 uppercase">Dipinjam</span>
                                <span class="text-xs font-black text-cyan-900" x-text="detailData.date"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-[10px] font-bold text-cyan-600 uppercase">Dikembalikan</span>
                                <span class="text-xs font-black text-cyan-900" x-text="detailData.return_date"></span>
                            </div>
                        </div>
                        <div class="p-6 rounded-[2rem] border border-slate-100 bg-white">
                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2">Kondisi Akhir</p>
                            <span class="px-3 py-1.5 rounded-lg text-[10px] font-black uppercase"
                                  :class="detailData.condition === 'aman' ? 'bg-emerald-100 text-emerald-600' : 'bg-red-100 text-red-600'"
                                  x-text="detailData.condition || 'Belum Ada'"></span>
                        </div>
                        <div class="p-6 bg-slate-900 rounded-[2rem] border border-slate-800 text-left shadow-xl">
                            <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest mb-2">Alasan / Catatan:</p>
                            <p class="text-sm text-slate-300 font-medium leading-relaxed" x-text="detailData.desc"></p>
                        </div>
                    </div>
                </template>

                {{-- B. TAMPILAN KHUSUS LAPORAN (REPORT) --}}
                <template x-if="detailData.type === 'report'">
                    <div class="space-y-6">
                        
                        {{-- ✅ MENAMPILKAN BUKTI FOTO (JIKA ADA) --}}
                        <template x-if="detailData.photo">
                            <div class="w-full h-56 bg-gray-100 rounded-[2rem] overflow-hidden border border-gray-200 relative group shadow-sm">
                                <img :src="detailData.photo" class="w-full h-full object-cover transition-transform group-hover:scale-105">
                                <div class="absolute bottom-4 left-4 bg-black/60 text-white px-3 py-1.5 rounded-full backdrop-blur-md">
                                    <p class="text-[9px] font-bold uppercase tracking-wider"><i class="bi bi-image-fill mr-1"></i> Bukti Kerusakan</p>
                                </div>
                            </div>
                        </template>
                        {{-- Placeholder Jika Tidak Ada Foto --}}
                        <template x-if="!detailData.photo">
                            <div class="w-full h-24 bg-slate-50 rounded-[2rem] flex items-center justify-center border border-dashed border-slate-200 text-slate-400">
                                <p class="text-[10px] font-bold uppercase tracking-widest"><i class="bi bi-image-alt mr-2"></i> Tidak ada foto bukti</p>
                            </div>
                        </template>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-5 bg-red-50 rounded-[2rem] border border-red-100">
                                <p class="text-[9px] font-black text-red-400 uppercase tracking-widest mb-1">Tanggal Lapor</p>
                                <p class="text-xs font-black text-red-800" x-text="detailData.date"></p>
                            </div>
                            <div class="p-5 bg-slate-50 rounded-[2rem] border border-slate-100">
                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Status</p>
                                <p class="text-xs font-black uppercase" 
                                   :class="detailData.status === 'pending' ? 'text-orange-500' : (detailData.status === 'process' ? 'text-blue-600' : 'text-emerald-600')"
                                   x-text="detailData.status"></p>
                            </div>
                        </div>

                        <div class="p-6 bg-white border border-red-100 rounded-[2rem] shadow-sm">
                            <p class="text-[9px] font-black text-red-500 uppercase tracking-widest mb-2 border-b border-red-50 pb-2">Detail Keluhan:</p>
                            <p class="text-sm text-gray-700 italic font-medium leading-relaxed" x-text="detailData.desc"></p>
                        </div>

                        <div class="p-6 bg-slate-900 rounded-[2rem] border border-slate-800 text-left shadow-xl relative overflow-hidden">
                            <div class="relative z-10">
                                <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest mb-3">Tanggapan Teknisi:</p>
                                <div class="flex gap-3">
                                    <div class="w-1 bg-cyan-500 rounded-full h-auto"></div>
                                    <p class="text-sm text-slate-300 font-medium leading-relaxed" x-text="detailData.feedback"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

            </div>

            <button @click="modalDetail = false" class="w-full mt-8 py-5 bg-slate-900 text-white rounded-[2rem] font-black text-[10px] uppercase tracking-[0.2em] shadow-xl hover:bg-cyan-600 active:scale-95 transition-all">Tutup Informasi</button>
        </div>
    </div>

</body>
</html>