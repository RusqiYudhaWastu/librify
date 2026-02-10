<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Audit Sirkulasi - TekniLog Admin</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
        .font-jakarta { font-family: 'Plus Jakarta Sans', sans-serif; }
        .custom-scroll::-webkit-scrollbar { width: 5px; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #6366f1; border-radius: 20px; }
    </style>
</head>

<body class="antialiased flex h-screen w-full overflow-hidden text-left font-jakarta" 
      x-data="{ 
        sidebarOpen: false, 
        modalDetail: false,
        // Data Objek Lengkap untuk Modal
        selectedAudit: { 
            user: '', dept: '', role: '',
            item: '', code: '', qty: 0,
            borrow_date: '', return_date: '',
            status: '', condition: '', rating: 0,
            admin_note: '', 
            // Data Insiden & Denda
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

        <main class="flex-1 overflow-y-auto p-6 lg:p-10 pt-2 custom-scroll">
            <div class="mx-auto w-full max-w-[1550px] space-y-8">
                
                {{-- 1. HEADER & SEARCH SECTION --}}
                <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-6 leading-none">
                    <div class="text-left leading-none">
                        <h2 class="text-4xl font-black text-slate-900 tracking-tight uppercase leading-none">Audit Sirkulasi Global</h2>
                        <p class="text-sm font-bold text-slate-400 mt-3 uppercase tracking-[0.2em] leading-none border-l-4 border-indigo-600 pl-4">Log Pergerakan Aset Lintas Jurusan</p>
                    </div>
                    
                    <form action="{{ route('admin.audit') }}" method="GET" class="flex flex-col md:flex-row items-center gap-4 w-full xl:w-auto">
                        <div class="relative w-full md:w-96">
                            <i class="bi bi-search absolute left-5 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, barang, atau kode aset..." 
                                   class="w-full pl-12 pr-6 py-4 bg-white border border-gray-100 rounded-2xl outline-none font-bold text-xs shadow-sm focus:ring-4 focus:ring-indigo-500/10 transition-all">
                        </div>
                        <button type="submit" class="w-full md:w-auto px-8 py-4 bg-indigo-600 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-xl shadow-indigo-100 active:scale-95 transition-all">Filter Log</button>
                    </form>
                </div>

                {{-- 2. STATS ANALYTICS --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-white p-7 rounded-[2.5rem] border border-gray-100 shadow-sm flex items-center gap-5">
                        <div class="w-14 h-14 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center text-2xl shadow-inner"><i class="bi bi-journal-text"></i></div>
                        <div class="text-left leading-none"><p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 leading-none">Total Log</p><p class="text-3xl font-black text-gray-900 leading-none">{{ $summary['total_logs'] }}</p></div>
                    </div>
                    <div class="bg-white p-7 rounded-[2.5rem] border border-gray-100 shadow-sm flex items-center gap-5 border-l-4 border-l-red-500">
                        <div class="w-14 h-14 bg-red-50 text-red-600 rounded-2xl flex items-center justify-center text-2xl shadow-inner"><i class="bi bi-exclamation-triangle"></i></div>
                        <div class="text-left leading-none"><p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 leading-none">Barang Rusak</p><p class="text-3xl font-black text-red-600 leading-none">{{ $summary['broken_items'] }}</p></div>
                    </div>
                    <div class="bg-white p-7 rounded-[2.5rem] border border-gray-100 shadow-sm flex items-center gap-5 border-l-4 border-l-orange-500">
                        <div class="w-14 h-14 bg-orange-50 text-orange-600 rounded-2xl flex items-center justify-center text-2xl shadow-inner"><i class="bi bi-search"></i></div>
                        <div class="text-left leading-none"><p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 leading-none">Barang Hilang</p><p class="text-3xl font-black text-orange-600 leading-none">{{ $summary['lost_items'] }}</p></div>
                    </div>
                    <div class="bg-slate-900 p-7 rounded-[2.5rem] shadow-xl flex items-center gap-5 text-white">
                        <div class="w-14 h-14 bg-white/10 text-emerald-400 rounded-2xl flex items-center justify-center text-2xl shadow-inner"><i class="bi bi-box-seam"></i></div>
                        <div class="text-left leading-none"><p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5 leading-none">Unit Beredar</p><p class="text-3xl font-black text-white leading-none">{{ $summary['total_units'] }}</p></div>
                    </div>
                </div>

                {{-- 3. AUDIT TABLE --}}
                <div class="rounded-[3rem] bg-white shadow-sm border border-gray-100 overflow-hidden text-left">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead>
                                <tr class="bg-gray-50/50 text-[10px] uppercase tracking-[0.2em] text-gray-400 font-black leading-none">
                                    <th class="px-8 py-7">Timestamp Audit</th>
                                    <th class="px-8 py-7">Pelaku & Otoritas</th>
                                    <th class="px-8 py-7">Aksi Inventaris</th>
                                    <th class="px-8 py-7">Security Status</th>
                                    <th class="px-8 py-7 text-center">Detail</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 leading-tight">
                                @forelse($auditLogs as $log)
                                <tr class="group hover:bg-gray-50/50 transition-all duration-200">
                                    <td class="px-8 py-6">
                                        <div class="flex flex-col text-left leading-none">
                                            <span class="text-gray-900 font-black uppercase text-[11px] mb-1.5">{{ $log->updated_at->translatedFormat('d M Y') }}</span>
                                            <span class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">{{ $log->updated_at->format('H:i') }} WIB</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="flex flex-col text-left leading-none">
                                            <span class="text-gray-900 font-black uppercase text-sm tracking-tight mb-1.5">{{ $log->user->name }}</span>
                                            <span class="text-[9px] font-black text-indigo-500 bg-indigo-50 px-2 py-0.5 rounded uppercase w-fit border border-indigo-100">
                                                {{ $log->user->department->name ?? 'ADMIN' }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="flex flex-col text-left leading-none">
                                            @php
                                                $actionText = match($log->status) {
                                                    'pending'  => 'Request Baru',
                                                    'approved' => 'Barang Keluar',
                                                    'returned' => 'Barang Masuk',
                                                    'rejected' => 'Request Ditolak',
                                                    default    => 'Update Sistem'
                                                };
                                            @endphp
                                            <span class="text-gray-800 font-black text-[12px] uppercase mb-1.5">{{ $actionText }}</span>
                                            <span class="text-[10px] text-gray-400 font-medium italic truncate max-w-xs">{{ $log->quantity }}x {{ $log->item->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        @php
                                            $isIncident = in_array($log->return_condition, ['rusak', 'hilang']);
                                            $isUnpaid = $log->fine_amount > 0 && $log->fine_status === 'unpaid';
                                            
                                            $badgeClass = match(true) {
                                                $isUnpaid => 'bg-red-50 text-red-600 border-red-100 animate-pulse',
                                                $isIncident => 'bg-orange-50 text-orange-600 border-orange-100',
                                                $log->status === 'returned' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                                default => 'bg-blue-50 text-blue-600 border-blue-100'
                                            };

                                            $statusText = match(true) {
                                                $isUnpaid => 'DENDA BELUM LUNAS',
                                                $isIncident => 'INSIDEN: '.strtoupper($log->return_condition),
                                                $log->return_condition => 'KONDISI: '.strtoupper($log->return_condition),
                                                default => 'STATUS: '.strtoupper($log->status)
                                            };
                                        @endphp
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest border {{ $badgeClass }}">
                                            {{ $statusText }}
                                        </span>
                                    </td>
                                    <td class="px-8 py-6 text-center">
                                        {{-- ✅ Button View Detail dengan Data Lengkap --}}
                                        <button @click="selectedAudit = {
                                            user: '{{ $log->user->name }}',
                                            role: '{{ $log->user->role }}',
                                            dept: '{{ $log->user->department->name ?? 'UMUM' }}',
                                            item: '{{ $log->item->name }}',
                                            code: '{{ $log->item->asset_code }}',
                                            qty: '{{ $log->quantity }}',
                                            borrow_date: '{{ $log->created_at->format('d M Y, H:i') }}',
                                            return_date: '{{ $log->return_date ? \Carbon\Carbon::parse($log->return_date)->format('d M Y, H:i') : '-' }}',
                                            status: '{{ $log->status }}',
                                            condition: '{{ $log->return_condition ?? '-' }}',
                                            rating: '{{ $log->rating ?? 0 }}',
                                            admin_note: '{{ $log->admin_note }}',
                                            // Data Insiden
                                            is_incident: {{ $log->fine_amount > 0 || in_array($log->return_condition, ['rusak', 'hilang']) ? 'true' : 'false' }},
                                            return_note: '{{ $log->return_note ?? '-' }}',
                                            lost_qty: '{{ $log->lost_quantity }}',
                                            fine_amount: '{{ number_format($log->fine_amount, 0, ',', '.') }}',
                                            fine_status: '{{ $log->fine_status }}'
                                        }; modalDetail = true" 
                                        class="w-10 h-10 rounded-2xl bg-white border border-gray-200 text-gray-400 hover:text-indigo-600 hover:border-indigo-200 flex items-center justify-center shadow-sm transition-all mx-auto">
                                            <i class="bi bi-eye-fill"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="py-24 text-center text-gray-300 font-bold uppercase text-[10px] tracking-[0.3em] italic">Belum ada aktivitas terekam.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    {{-- 👁️ MODAL DETAIL AUDIT (SUPER LENGKAP) --}}
    <div x-show="modalDetail" x-cloak class="fixed inset-0 z-[120] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-md" @click="modalDetail = false"></div>
        <div x-show="modalDetail" x-transition.scale.95 class="relative w-full max-w-lg bg-white rounded-[3rem] shadow-2xl p-10 border border-white text-left leading-none overflow-y-auto max-h-[90vh] custom-scroll">
            
            {{-- Header Modal --}}
            <div class="mb-8 text-left leading-none flex justify-between items-start">
                <div class="text-left">
                    <span class="text-[9px] font-black text-indigo-500 uppercase tracking-[0.2em] bg-indigo-50 px-2 py-1 rounded border border-indigo-100">Audit Trail</span>
                    <h3 class="text-3xl font-black text-gray-900 uppercase mt-3 tracking-tight leading-none" x-text="selectedAudit.user"></h3>
                    <p class="text-[10px] font-black text-slate-400 uppercase mt-2 tracking-widest" x-text="'Role: ' + selectedAudit.role + ' | Unit: ' + selectedAudit.dept"></p>
                </div>
                <button @click="modalDetail = false" class="text-gray-400 hover:text-gray-600"><i class="bi bi-x-lg"></i></button>
            </div>

            <div class="space-y-6 text-left leading-none">
                
                {{-- 1. Informasi Aset --}}
                <div class="p-6 bg-slate-50 rounded-[2rem] border border-slate-100 space-y-4">
                    <div class="flex justify-between items-center"><span class="text-gray-400 uppercase text-[9px] font-black tracking-widest">Nama Barang</span><span class="text-gray-900 font-black uppercase text-sm" x-text="selectedAudit.item"></span></div>
                    <div class="flex justify-between items-center"><span class="text-gray-400 uppercase text-[9px] font-black tracking-widest">Kode Aset</span><span class="text-indigo-600 font-black text-xs" x-text="selectedAudit.code"></span></div>
                    <div class="flex justify-between items-center"><span class="text-gray-400 uppercase text-[9px] font-black tracking-widest">Jumlah Transaksi</span><span class="text-gray-900 font-black text-sm" x-text="selectedAudit.qty + ' UNIT'"></span></div>
                </div>

                {{-- 2. Timeline & Status --}}
                <div class="p-8 bg-indigo-50/50 rounded-[2.5rem] border border-indigo-100 text-left space-y-3">
                    <div class="flex justify-between items-center border-b border-indigo-100 pb-2">
                        <span class="text-[10px] font-bold text-gray-500 uppercase">Waktu Pinjam</span>
                        <span class="text-xs font-black text-gray-800" x-text="selectedAudit.borrow_date"></span>
                    </div>
                    <div class="flex justify-between items-center border-b border-indigo-100 pb-2">
                        <span class="text-[10px] font-bold text-gray-500 uppercase">Waktu Kembali</span>
                        <span class="text-xs font-black text-gray-800" x-text="selectedAudit.return_date"></span>
                    </div>
                    <div class="flex justify-between items-center pt-1">
                        <span class="text-[10px] font-bold text-gray-500 uppercase">Kondisi Fisik</span>
                        <span class="text-xs font-black uppercase px-2 py-1 rounded"
                              :class="selectedAudit.condition === 'aman' ? 'bg-emerald-200 text-emerald-800' : (selectedAudit.condition === '-' ? 'bg-slate-200 text-slate-600' : 'bg-red-200 text-red-800')"
                              x-text="selectedAudit.condition"></span>
                    </div>
                    <div x-show="selectedAudit.rating > 0" class="flex justify-between items-center pt-1">
                        <span class="text-[10px] font-bold text-gray-500 uppercase">Rating User</span>
                        <div class="flex text-orange-400 text-xs">
                            <template x-for="i in parseInt(selectedAudit.rating)"><i class="bi bi-star-fill"></i></template>
                        </div>
                    </div>
                </div>

                {{-- 3. BOX MERAH: INSIDEN & DENDA (Hanya Muncul Jika Ada Masalah) --}}
                <div x-show="selectedAudit.is_incident" class="p-8 bg-red-50 rounded-[2.5rem] border border-red-100 text-left space-y-4">
                    <div class="flex items-center gap-2 text-red-600 font-black text-xs uppercase tracking-widest mb-1">
                        <i class="bi bi-exclamation-triangle-fill"></i> Laporan Insiden
                    </div>
                    
                    <div class="flex justify-between items-center border-b border-red-200 pb-2">
                        <span class="text-[10px] font-bold text-red-400 uppercase">Kronologi / Kerusakan</span>
                        <span class="text-xs font-black text-red-800 uppercase text-right" x-text="selectedAudit.return_note"></span>
                    </div>

                    <div x-show="selectedAudit.lost_qty > 0" class="flex justify-between items-center border-b border-red-200 pb-2">
                        <span class="text-[10px] font-bold text-red-400 uppercase">Unit Hilang</span>
                        <span class="text-xs font-black text-red-800 uppercase" x-text="selectedAudit.lost_qty + ' Unit'"></span>
                    </div>

                    <div x-show="selectedAudit.fine_amount !== '0'" class="flex justify-between items-center pt-2">
                        <div class="flex flex-col">
                            <span class="text-[10px] font-bold text-red-400 uppercase">Total Denda</span>
                            <span class="text-lg font-black text-red-600" x-text="'Rp ' + selectedAudit.fine_amount"></span>
                        </div>
                        <span class="px-3 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest"
                              :class="selectedAudit.fine_status === 'paid' ? 'bg-emerald-100 text-emerald-600' : 'bg-white text-red-600 border border-red-200 animate-pulse'"
                              x-text="selectedAudit.fine_status === 'paid' ? 'LUNAS' : 'BELUM BAYAR'"></span>
                    </div>
                </div>

                {{-- 4. Catatan Admin --}}
                <div class="p-6 bg-white border border-gray-100 rounded-[2rem]">
                    <p class="text-[9px] font-black text-gray-400 uppercase mb-2 tracking-widest">Catatan Log Sistem:</p>
                    <p class="text-xs text-gray-600 font-medium italic leading-relaxed" x-text="selectedAudit.admin_note || 'System generated log.'"></p>
                </div>

            </div>

            <button @click="modalDetail = false" class="w-full mt-8 py-5 bg-slate-900 text-white rounded-[1.5rem] font-black text-[10px] uppercase tracking-[0.2em] shadow-xl hover:bg-indigo-600 active:scale-95 transition-all">Tutup Audit Trail</button>
        </div>
    </div>

</body>
</html>