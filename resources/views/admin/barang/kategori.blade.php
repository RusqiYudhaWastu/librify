<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kategori Buku - Librify Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
        .font-jakarta { font-family: 'Plus Jakarta Sans', sans-serif; }
        .custom-scroll::-webkit-scrollbar { width: 5px; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #6366f1; border-radius: 20px; }
        
        @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        .toast-animate { animation: slideInRight 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    </style>
</head>

<body class="antialiased flex h-screen w-full overflow-hidden text-left font-jakarta" 
      x-data="{ 
        searchQuery: '', 
        sidebarOpen: false, 
        
        // State Modal
        modalKategori: false, 
        modalDelete: false,
        modalDetail: false,   

        isEdit: false,
        selectedItem: { id: '', name: '' },
        
        // Data Dinamis
        selectedCat: { name: '', items: [], total_stock: 0, ready_stock: 0, maint_stock: 0, broken_stock: 0, item_count: 0 },
        
        actionRoute: ''
      }">

    {{-- ✅ NOTIFICATIONS --}}
    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" 
         class="fixed top-10 right-10 z-[200] bg-emerald-500 text-white px-8 py-5 rounded-[1.5rem] shadow-2xl flex justify-between items-center transition-all toast-animate border-2 border-emerald-400">
        <span class="font-black text-sm uppercase tracking-widest"><i class="bi bi-check-circle-fill me-3 text-lg"></i> {{ session('success') }}</span>
        <button @click="show = false" class="ml-6 opacity-70 hover:opacity-100 transition-opacity"><i class="bi bi-x-lg"></i></button>
    </div>
    @endif

    @if(session('error') || $errors->any())
    <div x-data="{ show: true }" x-show="show" 
         class="fixed top-10 right-10 z-[200] bg-red-600 text-white px-8 py-6 rounded-[1.5rem] shadow-2xl transition-all toast-animate border-2 border-red-400 max-w-md">
        <div class="flex justify-between items-start mb-3 border-b border-red-500 pb-3">
            <span class="font-black text-sm uppercase tracking-widest"><i class="bi bi-exclamation-triangle-fill me-2"></i> Peringatan Sistem</span>
            <button @click="show = false" class="ml-6 opacity-70 hover:opacity-100 transition-opacity"><i class="bi bi-x-lg"></i></button>
        </div>
        <div class="text-xs font-bold leading-relaxed opacity-90">
            @if(session('error')) <p class="mb-2">{{ session('error') }}</p> @endif
            @if($errors->any())
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                </ul>
            @endif
        </div>
    </div>
    @endif

    {{-- Sidebar --}}
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'" class="fixed inset-y-0 left-0 z-50 flex flex-col w-72 bg-[#0F172A] text-white border-r border-slate-800 md:static md:flex-shrink-0 h-full transition-transform duration-300">
        @include('admin.partials.sidebar')
    </aside>

    <div class="flex flex-1 flex-col h-full min-w-0 overflow-hidden text-left">
        @include('admin.partials.header')

        <main class="flex-1 overflow-y-auto p-6 lg:p-10 pt-2 custom-scroll text-left">
            <div class="mx-auto w-full max-w-[1550px] space-y-8">
                
                {{-- 1. HEADER --}}
                <div class="flex flex-col xl:flex-row xl:items-end justify-between gap-6 leading-none">
                    <div class="text-left leading-none">
                        <h2 class="text-4xl font-black text-slate-900 tracking-tight uppercase leading-none">Kategori Koleksi</h2>
                        <p class="text-xs font-bold text-indigo-600 mt-3 uppercase tracking-widest leading-none">Manajemen Genre & Klasifikasi Buku</p>
                    </div>
                    
                    <div class="flex flex-col md:flex-row items-center gap-4 w-full xl:w-auto leading-none">
                        <div class="relative w-full md:w-80 leading-none">
                            <i class="bi bi-search absolute left-5 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="text" x-model="searchQuery" placeholder="Cari nama kategori..." 
                                   class="w-full pl-12 pr-6 py-4 bg-white border border-gray-100 rounded-2xl outline-none font-bold text-xs shadow-sm focus:ring-4 focus:ring-indigo-500/10 transition-all">
                        </div>

                        <div class="flex gap-3 w-full md:w-auto leading-none">
                            <button @click="isEdit = false; selectedItem = {id: '', name: ''}; actionRoute = '{{ route('admin.kategori.store') }}'; modalKategori = true" class="flex-1 md:flex-none bg-indigo-600 px-8 py-4 rounded-[2rem] text-white font-bold text-[10px] uppercase shadow-xl shadow-indigo-100 hover:bg-indigo-700 transition-all active:scale-95 flex items-center justify-center gap-2 tracking-widest">
                                <i class="bi bi-plus-lg text-lg"></i> Kategori Baru
                            </button>
                        </div>
                    </div>
                </div>

                {{-- 2. GRID KATEGORI BUKU --}}
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    @forelse($categories as $cat)
                    <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm group hover:shadow-xl transition-all relative overflow-hidden text-left"
                         x-show="searchQuery === '' || '{{ strtolower($cat->name) }}'.includes(searchQuery.toLowerCase())">
                        
                        {{-- Ornamen Latar --}}
                        <div class="absolute -right-8 -top-8 w-32 h-32 bg-indigo-50/50 rounded-full group-hover:scale-[2] transition-transform duration-700"></div>
                        
                        <div class="relative z-10">
                            <div class="flex justify-between items-start mb-6">
                                <div class="w-16 h-16 bg-indigo-600 text-white rounded-2xl flex items-center justify-center text-3xl font-black shadow-lg shadow-indigo-200">
                                    {{ substr($cat->name, 0, 1) }}
                                </div>
                                <div class="flex gap-2">
                                    {{-- Inject JSON Data --}}
                                    <button data-cat="{{ json_encode([
                                                'name' => $cat->name,
                                                'items' => $cat->items->map(fn($i) => ['name' => $i->name, 'code' => $i->asset_code, 'stock' => $i->stock, 'maint' => $i->maintenance_stock, 'broken' => $i->broken_stock]),
                                                'total_stock' => $cat->items->sum('stock') + $cat->items->sum('maintenance_stock') + $cat->items->sum('broken_stock'),
                                                'ready_stock' => $cat->items->sum('stock'),
                                                'maint_stock' => $cat->items->sum('maintenance_stock'),
                                                'broken_stock' => $cat->items->sum('broken_stock'),
                                                'item_count' => $cat->items_count ?? $cat->items->count()
                                            ]) }}" 
                                            @click="selectedCat = JSON.parse($el.dataset.cat); modalDetail = true" 
                                            class="w-10 h-10 rounded-xl bg-slate-50 text-slate-400 hover:bg-indigo-600 hover:text-white flex items-center justify-center transition-all shadow-sm" title="Lihat Detail Kategori">
                                        <i class="bi bi-info-circle-fill"></i>
                                    </button>
                                    
                                    <button @click="isEdit = true; selectedItem = {id: '{{ $cat->id }}', name: '{{ $cat->name }}'}; actionRoute = '/admin/kategori/{{ $cat->id }}'; modalKategori = true" class="w-10 h-10 rounded-xl bg-slate-50 text-slate-400 hover:bg-blue-600 hover:text-white flex items-center justify-center transition-all shadow-sm" title="Edit Kategori"><i class="bi bi-pencil-square"></i></button>
                                    <button @click="selectedItem = {id: '{{ $cat->id }}', name: '{{ $cat->name }}'}; actionRoute = '/admin/kategori/{{ $cat->id }}'; modalDelete = true" class="w-10 h-10 rounded-xl bg-slate-50 text-slate-400 hover:bg-red-600 hover:text-white flex items-center justify-center transition-all shadow-sm" title="Hapus Kategori"><i class="bi bi-trash3"></i></button>
                                </div>
                            </div>
                            
                            <h4 class="font-black text-slate-900 text-xl uppercase mb-6 leading-tight tracking-tight">{{ $cat->name }}</h4>

                            {{-- Grid Info Stok --}}
                            <div class="grid grid-cols-3 gap-2 border-t border-slate-100 pt-5">
                                <div>
                                    <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Fisik</p>
                                    <p class="text-sm font-black text-slate-800">{{ $cat->items->sum('stock') + $cat->items->sum('maintenance_stock') + $cat->items->sum('broken_stock') }}</p>
                                </div>
                                <div>
                                    <p class="text-[8px] font-black text-emerald-500 uppercase tracking-widest mb-1">Siap Pinjam</p>
                                    <p class="text-sm font-black text-emerald-600">{{ $cat->items->sum('stock') }}</p>
                                </div>
                                <div>
                                    <p class="text-[8px] font-black text-indigo-400 uppercase tracking-widest mb-1">Total Judul</p>
                                    <p class="text-sm font-black text-indigo-600">{{ $cat->items_count ?? $cat->items->count() }} Buku</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-span-full py-20 text-center opacity-50 italic uppercase text-[10px] font-black tracking-widest leading-none">Belum ada kategori buku terdaftar.</div>
                    @endforelse
                </div>

            </div>
        </main>
    </div>

    {{-- MODAL SECTION --}}
    
    {{-- MODAL FORM KATEGORI --}}
    <div x-show="modalKategori" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-md" @click="modalKategori = false"></div>
        <div x-show="modalKategori" x-transition.scale.95 class="relative w-full max-w-md bg-white rounded-[3rem] p-10 shadow-2xl text-left border border-white">
            <h3 class="text-2xl font-black text-slate-900 uppercase mb-2 leading-none" x-text="isEdit ? 'Ubah Kategori' : 'Kategori Baru'"></h3>
            <p class="text-xs text-gray-500 mb-8 font-medium">Tambahkan klasifikasi genre atau jenis buku baru.</p>
            
            <form :action="isEdit ? '/admin/kategori/' + selectedItem.id : '{{ route('admin.kategori.store') }}'" method="POST" class="space-y-6">
                @csrf <template x-if="isEdit"><input type="hidden" name="_method" value="PUT"></template>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Nama Kategori Buku</label>
                    <input type="text" name="name" x-model="selectedItem.name" required placeholder="Contoh: Fiksi, Sains, Sejarah" class="w-full px-6 py-5 bg-slate-50 border border-slate-100 rounded-2xl outline-none font-bold text-sm focus:ring-4 focus:ring-indigo-500/10 shadow-inner placeholder:text-gray-300">
                </div>
                
                <div class="flex gap-4 pt-2 leading-none">
                    <button type="button" @click="modalKategori = false" class="flex-1 py-5 rounded-2xl bg-slate-100 text-slate-500 font-bold text-[10px] uppercase tracking-widest hover:bg-slate-200 transition-all">Batal</button>
                    <button type="submit" class="flex-1 py-5 rounded-2xl bg-indigo-600 text-white font-bold text-[10px] uppercase shadow-xl tracking-widest active:scale-95 transition-all hover:bg-indigo-700" x-text="isEdit ? 'Simpan' : 'Tambahkan'"></button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL DETAIL KATEGORI --}}
    <div x-show="modalDetail" x-cloak class="fixed inset-0 z-[120] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-md" @click="modalDetail = false"></div>
        <div x-show="modalDetail" x-transition.scale.95 class="relative w-full max-w-2xl bg-white rounded-[3rem] p-10 shadow-2xl flex flex-col border border-white leading-none overflow-y-auto max-h-[90vh] custom-scroll">
            
            {{-- Header Detail --}}
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center bg-indigo-50 p-6 rounded-3xl border border-indigo-100 mb-8 gap-4">
                <div class="text-left">
                    <p class="text-[9px] font-black text-indigo-400 uppercase tracking-widest mb-1.5">Informasi Kategori</p>
                    <h3 class="text-3xl font-black text-indigo-900 uppercase tracking-tight mb-3" x-text="selectedCat.name"></h3>
                    <div class="flex flex-wrap gap-2">
                        <span class="text-[10px] font-bold text-emerald-600 bg-emerald-100/70 border border-emerald-200 px-2.5 py-1 rounded-lg uppercase tracking-widest">Siap: <span x-text="selectedCat.ready_stock"></span></span>
                        <span class="text-[10px] font-bold text-orange-600 bg-orange-100/70 border border-orange-200 px-2.5 py-1 rounded-lg uppercase tracking-widest">Maint: <span x-text="selectedCat.maint_stock"></span></span>
                        <span class="text-[10px] font-bold text-red-600 bg-red-100/70 border border-red-200 px-2.5 py-1 rounded-lg uppercase tracking-widest">Rusak: <span x-text="selectedCat.broken_stock"></span></span>
                    </div>
                </div>
                <div class="text-left sm:text-right w-full sm:w-auto border-t sm:border-t-0 sm:border-l border-indigo-200 pt-4 sm:pt-0 sm:pl-6">
                    <span class="text-[9px] font-black text-indigo-400 uppercase tracking-widest block mb-1.5">Total Eksemplar Fisik</span>
                    <span class="text-4xl font-black text-indigo-600" x-text="selectedCat.total_stock"></span>
                </div>
            </div>

            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 text-left"><i class="bi bi-journal-bookmark-fill me-1"></i> Daftar Buku di Kategori Ini</p>
            
            <div class="flex-1 overflow-y-auto custom-scroll pr-2 space-y-3 max-h-[40vh] text-left leading-none">
                <template x-for="item in selectedCat.items" :key="item.code">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between p-5 rounded-[1.5rem] bg-white border border-slate-200 leading-none hover:border-indigo-300 hover:shadow-lg transition-all shadow-sm group gap-4">
                        <div class="flex items-center gap-4 text-left leading-none">
                            <div class="w-12 h-12 bg-slate-50 border border-slate-100 rounded-xl flex items-center justify-center text-slate-400 group-hover:bg-indigo-50 group-hover:text-indigo-500 transition-colors">
                                <i class="bi bi-book-fill text-xl"></i>
                            </div>
                            <div>
                                <p class="font-black text-slate-800 text-sm uppercase mb-1.5 truncate max-w-[200px] lg:max-w-xs" x-text="item.name"></p>
                                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest leading-none" x-text="'Kode: ' + item.code"></p>
                            </div>
                        </div>
                        <div class="flex gap-2 w-full sm:w-auto justify-end mt-2 sm:mt-0">
                            <div class="text-center px-3 py-1.5 bg-emerald-50 rounded-lg border border-emerald-100 flex-1 sm:flex-none">
                                <p class="text-[8px] font-black text-emerald-500 uppercase tracking-widest mb-1">Siap</p>
                                <p class="text-sm font-black text-emerald-700" x-text="item.stock"></p>
                            </div>
                            <template x-if="item.maint > 0">
                                <div class="text-center px-3 py-1.5 bg-orange-50 rounded-lg border border-orange-100 flex-1 sm:flex-none">
                                    <p class="text-[8px] font-black text-orange-500 uppercase tracking-widest mb-1">Maint</p>
                                    <p class="text-sm font-black text-orange-700" x-text="item.maint"></p>
                                </div>
                            </template>
                            <template x-if="item.broken > 0">
                                <div class="text-center px-3 py-1.5 bg-red-50 rounded-lg border border-red-100 flex-1 sm:flex-none">
                                    <p class="text-[8px] font-black text-red-500 uppercase tracking-widest mb-1">Rusak</p>
                                    <p class="text-sm font-black text-red-700" x-text="item.broken"></p>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
                <template x-if="selectedCat.items.length === 0">
                    <div class="text-center py-10 bg-slate-50 rounded-3xl border border-slate-100">
                        <p class="text-slate-400 italic text-xs font-bold uppercase mb-1">Kategori Kosong</p>
                        <p class="text-[9px] text-slate-400 font-medium">Belum ada buku yang didaftarkan ke kategori ini.</p>
                    </div>
                </template>
            </div>
            <button @click="modalDetail = false" class="w-full mt-8 py-5 bg-slate-900 text-white rounded-[1.5rem] font-black text-[10px] uppercase tracking-widest active:scale-95 transition-all hover:bg-slate-800 shadow-xl">Tutup Panel</button>
        </div>
    </div>

    {{-- MODAL DELETE --}}
    <div x-show="modalDelete" x-cloak class="fixed inset-0 z-[150] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="modalDelete = false"></div>
        <div x-show="modalDelete" x-transition.scale.95 class="relative w-full max-w-sm bg-white rounded-[3rem] p-10 text-center border border-white leading-none">
            <div class="w-20 h-20 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner"><i class="bi bi-trash3-fill text-3xl"></i></div>
            <h3 class="text-2xl font-black text-gray-900 mb-2 uppercase tracking-tight">Hapus Kategori?</h3>
            <p class="text-sm text-gray-500 mb-10 leading-relaxed font-medium">Data <span class="font-black text-gray-900 uppercase" x-text="selectedItem.name"></span> akan dihapus secara permanen.</p>
            <form :action="actionRoute" method="POST" class="flex gap-4 leading-none">
                @csrf @method('DELETE')
                <button type="button" @click="modalDelete = false" class="flex-1 py-4 rounded-2xl bg-gray-100 text-slate-600 font-bold text-[10px] uppercase tracking-widest hover:bg-gray-200 transition-all">Batal</button>
                <button type="submit" class="flex-1 py-4 rounded-2xl bg-red-500 text-white font-bold text-[10px] uppercase shadow-xl active:scale-95 transition-all hover:bg-red-600">Ya, Hapus</button>
            </form>
        </div>
    </div>

</body>
</html>