<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Klasifikasi Aset - TekniLog Admin</title>
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
    </style>
</head>

<body class="antialiased flex h-screen w-full overflow-hidden text-left font-jakarta" 
      x-data="{ 
        tab: 'barang', 
        searchQuery: '', 
        sidebarOpen: false, 
        
        // State Modal
        modalBarang: false, 
        modalJurusan: false,
        modalDelete: false,
        modalDetailBarang: false,   
        modalDetailJurusan: false,  
        modalKelasJurusan: false,   

        isEdit: false,
        selectedItem: { id: '', name: '', depts: [] },
        
        // Data Dinamis
        selectedCat: { name: '', items: [], depts: [], total_stock: 0, item_count: 0 },
        selectedDept: { id: '', name: '', categories: [], class_rooms: [] },
        
        // ✅ NEW: Khusus Form Kelas (Biar bisa Edit)
        classForm: { id: '', name: '', isEdit: false },

        actionRoute: ''
      }">

    {{-- Sidebar --}}
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'" class="fixed inset-y-0 left-0 z-50 flex flex-col w-72 bg-[#0F172A] text-white border-r border-slate-800 md:static md:flex-shrink-0 h-full transition-transform duration-300">
        @include('admin.partials.sidebar')
    </aside>

    <div class="flex flex-1 flex-col h-full min-w-0 overflow-hidden text-left">
        @include('admin.partials.header')

        <main class="flex-1 overflow-y-auto p-6 lg:p-10 pt-2 custom-scroll text-left">
            <div class="mx-auto w-full max-w-[1400px] space-y-8">
                
                {{-- Notifikasi --}}
                @if(session('success'))
                <div x-data="{show: true}" x-show="show" x-init="setTimeout(() => show = false, 3000)" class="bg-emerald-500 text-white px-6 py-4 rounded-[2rem] shadow-lg flex justify-between items-center mb-6">
                    <span class="font-bold text-sm uppercase tracking-widest"><i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}</span>
                    <button @click="show = false"><i class="bi bi-x-lg"></i></button>
                </div>
                @endif
                
                @if(session('error'))
                <div x-data="{show: true}" x-show="show" x-init="setTimeout(() => show = false, 3000)" class="bg-red-500 text-white px-6 py-4 rounded-[2rem] shadow-lg flex justify-between items-center mb-6">
                    <span class="font-bold text-sm uppercase tracking-widest"><i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}</span>
                    <button @click="show = false"><i class="bi bi-x-lg"></i></button>
                </div>
                @endif

                {{-- 1. HEADER & TAB SWITCHER --}}
                <div class="flex flex-col xl:flex-row xl:items-end justify-between gap-6 leading-none">
                    <div class="text-left leading-none">
                        <h2 class="text-4xl font-black text-slate-900 tracking-tight uppercase leading-none">Otoritas Klasifikasi</h2>
                        <div class="flex bg-slate-200/50 p-1.5 rounded-2xl gap-1 mt-6 w-fit border border-slate-100 shadow-inner">
                            <button @click="tab = 'barang'; searchQuery = ''" :class="tab === 'barang' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500'" class="px-6 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">Kategori Alat</button>
                            <button @click="tab = 'jurusan'; searchQuery = ''" :class="tab === 'jurusan' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500'" class="px-6 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">Kategori Jurusan</button>
                        </div>
                    </div>
                    
                    <div class="flex flex-col md:flex-row items-center gap-4 w-full xl:w-auto leading-none">
                        <div class="relative w-full md:w-80 leading-none">
                            <i class="bi bi-search absolute left-5 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="text" x-model="searchQuery" placeholder="Cari nama klasifikasi..." 
                                   class="w-full pl-12 pr-6 py-4 bg-white border border-gray-100 rounded-2xl outline-none font-bold text-xs shadow-sm focus:ring-4 focus:ring-indigo-500/10 transition-all">
                        </div>

                        <div class="flex gap-3 w-full md:w-auto leading-none">
                            <button x-show="tab === 'barang'" @click="isEdit = false; selectedItem = {id: '', name: '', depts: []}; actionRoute = '{{ route('admin.kategori.store') }}'; modalBarang = true" class="flex-1 md:flex-none bg-indigo-600 px-8 py-4 rounded-[2rem] text-white font-bold text-[10px] uppercase shadow-xl shadow-indigo-100 hover:bg-indigo-700 transition-all active:scale-95 flex items-center justify-center gap-2 tracking-widest">
                                <i class="bi bi-plus-lg text-lg"></i> Kategori
                            </button>
                            <button x-show="tab === 'jurusan'" @click="isEdit = false; selectedItem = {id: '', name: ''}; actionRoute = '{{ route('admin.jurusan.store') }}'; modalJurusan = true" class="flex-1 md:flex-none bg-emerald-600 px-8 py-4 rounded-[2rem] text-white font-bold text-[10px] uppercase shadow-xl shadow-emerald-100 hover:bg-emerald-700 transition-all active:scale-95 flex items-center justify-center gap-2 tracking-widest">
                                <i class="bi bi-plus-lg text-lg"></i> Jurusan
                            </button>
                        </div>
                    </div>
                </div>

                {{-- 2. SLIDE: KATEGORI BARANG --}}
                <div x-show="tab === 'barang'" x-transition class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($categories as $cat)
                    <div class="bg-white p-8 rounded-[3rem] border border-slate-100 shadow-sm group relative overflow-hidden text-left hover:shadow-xl transition-all"
                         x-show="searchQuery === '' || '{{ strtolower($cat->name) }}'.includes(searchQuery.toLowerCase())">
                        <div class="absolute -right-4 -top-4 w-24 h-24 bg-indigo-50/50 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
                        <div class="relative z-10">
                            <div class="flex items-start justify-between mb-6">
                                <div class="w-14 h-14 bg-indigo-600 text-white rounded-2xl flex items-center justify-center text-2xl font-black shadow-lg shadow-indigo-100">{{ substr($cat->name, 0, 1) }}</div>
                                <div class="flex gap-1.5">
                                    <button @click="selectedCat = {
                                        name: '{{ $cat->name }}', 
                                        items: {{ $cat->items->map(fn($i) => ['name' => $i->name, 'code' => $i->asset_code, 'stock' => $i->stock]) }}, 
                                        depts: {{ $cat->departments->pluck('name') }},
                                        total_stock: {{ $cat->items->sum('stock') }},
                                        item_count: {{ $cat->items->count() }}
                                    }; modalDetailBarang = true" class="w-9 h-9 rounded-xl bg-slate-50 text-slate-400 hover:bg-indigo-600 hover:text-white flex items-center justify-center transition-all shadow-sm"><i class="bi bi-info-circle-fill"></i></button>
                                    
                                    <button @click="isEdit = true; selectedItem = {id: '{{ $cat->id }}', name: '{{ $cat->name }}', depts: {{ $cat->departments->pluck('id') }} }; actionRoute = '/admin/kategori/{{ $cat->id }}'; modalBarang = true" class="w-9 h-9 rounded-xl bg-slate-50 text-slate-400 hover:bg-blue-600 hover:text-white flex items-center justify-center transition-all shadow-sm"><i class="bi bi-pencil-square"></i></button>
                                    <button @click="selectedItem = {id: '{{ $cat->id }}', name: '{{ $cat->name }}'}; actionRoute = '/admin/kategori/{{ $cat->id }}'; modalDelete = true" class="w-9 h-9 rounded-xl bg-slate-50 text-slate-400 hover:bg-red-600 hover:text-white flex items-center justify-center transition-all shadow-sm"><i class="bi bi-trash3"></i></button>
                                </div>
                            </div>
                            <h4 class="font-black text-slate-900 text-xl uppercase mb-3 leading-tight tracking-tight">{{ $cat->name }}</h4>
                            <div class="flex flex-wrap gap-1.5 mb-6">
                                @if($cat->departments->count() > 0)
                                    @foreach($cat->departments as $d)
                                    <span class="px-2 py-1 bg-indigo-50 text-indigo-600 text-[8px] font-black rounded uppercase border border-indigo-100">{{ $d->name }}</span>
                                    @endforeach
                                @else
                                    <span class="px-2 py-1 bg-gray-100 text-gray-500 text-[8px] font-black rounded uppercase border border-gray-200">GLOBAL / UMUM</span>
                                @endif
                            </div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none"><i class="bi bi-box-seam me-1"></i> {{ $cat->items_count }} Item Tersedia</p>
                        </div>
                    </div>
                    @empty
                    <div class="col-span-full py-20 text-center opacity-50 italic uppercase text-[10px] font-black tracking-widest leading-none">Belum ada kategori terdaftar.</div>
                    @endforelse
                </div>

                {{-- 3. SLIDE: KATEGORI JURUSAN --}}
                <div x-show="tab === 'jurusan'" x-transition x-cloak class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 text-left">
                    @forelse($departments as $dept)
                    <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm relative group hover:border-emerald-200 transition-all text-left"
                         x-show="searchQuery === '' || '{{ strtolower($dept->name) }}'.includes(searchQuery.toLowerCase())">
                        <div class="flex justify-between items-start mb-6">
                            <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center text-xl shadow-inner"><i class="bi bi-mortarboard-fill"></i></div>
                            <div class="flex gap-1.5">
                                {{-- Tombol DETAIL OTORITAS (MATA) --}}
                                <button @click="selectedDept = {
                                    id: '{{ $dept->id }}',
                                    name: '{{ $dept->name }}', 
                                    categories: {{ $dept->categories->pluck('name') }},
                                    class_rooms: [] 
                                }; modalDetailJurusan = true" 
                                class="w-8 h-8 rounded-lg bg-slate-50 text-slate-400 hover:bg-slate-800 hover:text-white flex items-center justify-center transition-all"
                                title="Lihat Otoritas">
                                    <i class="bi bi-eye-fill text-sm"></i>
                                </button>

                                {{-- ✅ Tombol KELOLA KELAS (GEDUNG) --}}
                                <button @click="selectedDept = {
                                    id: '{{ $dept->id }}',
                                    name: '{{ $dept->name }}', 
                                    categories: [],
                                    class_rooms: {{ $dept->classRooms->toJson() }} 
                                }; 
                                classForm = { id: '', name: '', isEdit: false };
                                modalKelasJurusan = true" 
                                class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white flex items-center justify-center transition-all"
                                title="Kelola Kelas">
                                    <i class="bi bi-building-gear text-sm"></i>
                                </button>
                                
                                {{-- Edit & Delete --}}
                                <button @click="isEdit = true; selectedItem = {id: '{{ $dept->id }}', name: '{{ $dept->name }}'}; actionRoute = '/admin/jurusan/{{ $dept->id }}'; modalJurusan = true" class="w-8 h-8 rounded-lg bg-slate-50 text-slate-400 hover:bg-blue-600 hover:text-white flex items-center justify-center transition-all"><i class="bi bi-pencil-square text-sm"></i></button>
                                <button @click="selectedItem = {id: '{{ $dept->id }}', name: '{{ $dept->name }}'}; actionRoute = '/admin/jurusan/{{ $dept->id }}'; modalDelete = true" class="w-8 h-8 rounded-lg bg-slate-50 text-slate-400 hover:bg-red-600 hover:text-white flex items-center justify-center transition-all"><i class="bi bi-trash3 text-sm"></i></button>
                            </div>
                        </div>
                        <h4 class="font-black text-slate-900 text-lg uppercase leading-none tracking-tight">{{ $dept->name }}</h4>
                        <div class="mt-3 flex gap-2">
                             <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">{{ $dept->categories_count }} Otoritas</p>
                             <span class="text-[9px] text-slate-300">•</span>
                             <p class="text-[9px] font-black text-emerald-500 uppercase tracking-widest">{{ $dept->class_rooms_count ?? 0 }} Kelas</p>
                        </div>
                    </div>
                    @empty
                    <div class="col-span-full py-20 text-center opacity-50 italic uppercase text-[10px] font-black tracking-widest">Database jurusan masih kosong.</div>
                    @endforelse
                </div>
            </div>
        </main>
    </div>

    {{-- MODAL SECTION --}}
    
    {{-- MODAL FORM KATEGORI --}}
    <div x-show="modalBarang" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-md" @click="modalBarang = false"></div>
        <div x-show="modalBarang" x-transition.scale.95 class="relative w-full max-w-lg bg-white rounded-[3rem] p-10 shadow-2xl text-left overflow-y-auto max-h-[90vh] custom-scroll border border-white">
            <h3 class="text-2xl font-black text-slate-900 uppercase mb-8 leading-none" x-text="isEdit ? 'Ubah Kategori' : 'Kategori Baru'"></h3>
            <form :action="isEdit ? '/admin/kategori/' + selectedItem.id : '{{ route('admin.kategori.store') }}'" method="POST" class="space-y-8">
                @csrf <template x-if="isEdit"><input type="hidden" name="_method" value="PUT"></template>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Nama Klasifikasi Alat</label>
                    <input type="text" name="name" x-model="selectedItem.name" required class="w-full px-6 py-5 bg-slate-50 border border-slate-100 rounded-2xl outline-none font-bold text-sm focus:ring-4 focus:ring-indigo-500/10 shadow-inner">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Otoritas Akses Jurusan</label>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="col-span-2 p-4 bg-yellow-50 rounded-2xl border border-yellow-100 mb-2">
                            <p class="text-[9px] font-bold text-yellow-600 uppercase mb-1">Catatan:</p>
                            <p class="text-[10px] text-yellow-700">Jika tidak ada jurusan yang dipilih, kategori ini akan menjadi <strong>UMUM (GLOBAL)</strong>.</p>
                        </div>
                        @foreach($departments as $d)
                        <label class="flex items-center gap-3 p-4 bg-slate-50 rounded-2xl cursor-pointer hover:bg-indigo-50 transition-all group">
                            <input type="checkbox" name="department_ids[]" value="{{ $d->id }}" 
                                   :checked="isEdit && selectedItem.depts.includes({{ $d->id }})"
                                   class="w-5 h-5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500/20">
                            <span class="text-[10px] font-black text-slate-600 uppercase group-hover:text-indigo-600">{{ $d->name }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
                <div class="flex gap-4 pt-4 leading-none">
                    <button type="button" @click="modalBarang = false" class="flex-1 py-5 rounded-2xl bg-slate-100 text-slate-500 font-bold text-[10px] uppercase tracking-widest">Batal</button>
                    <button type="submit" class="flex-1 py-5 rounded-2xl bg-indigo-600 text-white font-bold text-[10px] uppercase shadow-xl tracking-widest active:scale-95 transition-all">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL FORM JURUSAN --}}
    <div x-show="modalJurusan" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-md" @click="modalJurusan = false"></div>
        <div x-show="modalJurusan" x-transition.scale.95 class="relative w-full max-w-md bg-white rounded-[3rem] p-10 shadow-2xl text-left border border-white leading-none">
            <h3 class="text-2xl font-black text-slate-900 uppercase mb-8 leading-none" x-text="isEdit ? 'Update Jurusan' : 'Jurusan Baru'"></h3>
            <form :action="isEdit ? '/admin/jurusan/' + selectedItem.id : '{{ route('admin.jurusan.store') }}'" method="POST" class="space-y-6">
                @csrf <template x-if="isEdit"><input type="hidden" name="_method" value="PUT"></template>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Nama Unit/Jurusan</label>
                    <input type="text" name="name" x-model="selectedItem.name" required class="w-full px-6 py-5 bg-slate-50 border border-slate-100 rounded-2xl outline-none font-bold text-sm shadow-inner">
                </div>
                <div class="flex gap-4 pt-4 leading-none">
                    <button type="button" @click="modalJurusan = false" class="flex-1 py-5 rounded-2xl bg-slate-100 text-slate-500 font-bold text-[10px] uppercase tracking-widest">Batal</button>
                    <button type="submit" class="flex-1 py-5 rounded-2xl bg-emerald-600 text-white font-bold text-[10px] uppercase shadow-xl tracking-widest active:scale-95 transition-all">Konfirmasi</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL DETAIL JURUSAN (HANYA LIHAT OTORITAS KATEGORI) --}}
    <div x-show="modalDetailJurusan" x-cloak class="fixed inset-0 z-[120] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-md" @click="modalDetailJurusan = false"></div>
        <div x-show="modalDetailJurusan" x-transition.scale.95 class="relative w-full max-w-md bg-white rounded-[3rem] p-10 shadow-2xl text-left border border-white leading-none">
            <div class="flex items-center gap-5 mb-10 leading-none">
                <div class="w-16 h-16 bg-slate-800 text-white rounded-[1.5rem] flex items-center justify-center text-3xl shadow-lg"><i class="bi bi-shield-lock-fill"></i></div>
                <div class="text-left leading-none">
                    <h3 class="text-2xl font-black text-slate-900 uppercase tracking-tight mb-2" x-text="selectedDept.name"></h3>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none">Otoritas Kategori Alat</p>
                </div>
            </div>
            
            <div class="space-y-3 max-h-[40vh] overflow-y-auto custom-scroll pr-2 leading-none">
                <template x-for="cat in selectedDept.categories">
                    <div class="flex items-center gap-4 p-5 bg-slate-50 border border-slate-100 rounded-[1.5rem] leading-none">
                        <i class="bi bi-check-circle-fill text-indigo-500 text-xl"></i>
                        <span class="text-xs font-black text-slate-700 uppercase leading-none" x-text="cat"></span>
                    </div>
                </template>
                <template x-if="selectedDept.categories.length === 0">
                    <div class="text-center py-6 text-gray-400 italic text-xs font-bold uppercase">Belum ada kategori khusus.</div>
                </template>
            </div>
            <button @click="modalDetailJurusan = false" class="w-full mt-10 py-5 bg-slate-900 text-white rounded-[1.5rem] font-black text-[10px] uppercase active:scale-95 transition-all">Tutup</button>
        </div>
    </div>

    {{-- ✅ MODAL KELOLA KELAS (DENGAN FITUR EDIT & DELETE SAMA) --}}
    <div x-show="modalKelasJurusan" x-cloak class="fixed inset-0 z-[120] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-md" @click="modalKelasJurusan = false"></div>
        <div x-show="modalKelasJurusan" x-transition.scale.95 class="relative w-full max-w-2xl bg-white rounded-[2.5rem] shadow-2xl overflow-hidden border border-white">
            
            {{-- Header Modal Hijau --}}
            <div class="bg-emerald-600 p-8 text-white relative overflow-hidden">
                <div class="absolute -right-10 -top-10 text-white/10"><i class="bi bi-building-gear text-9xl"></i></div>
                <h3 class="text-2xl font-black uppercase tracking-tight relative z-10" x-text="selectedDept.name"></h3>
                <p class="text-emerald-100 text-xs font-bold uppercase tracking-widest relative z-10 mt-1">Manajemen Kelas & Rombel</p>
                <button @click="modalKelasJurusan = false" class="absolute top-6 right-6 text-white/70 hover:text-white transition-colors"><i class="bi bi-x-lg text-xl"></i></button>
            </div>

            <div class="p-8 max-h-[70vh] overflow-y-auto custom-scroll">
                {{-- Form Tambah / Edit Kelas --}}
                <div class="bg-gray-50 p-6 rounded-3xl border border-gray-100 mb-8 transition-all">
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest" x-text="classForm.isEdit ? 'Edit Nama Kelas' : 'Tambah Kelas Baru'"></h4>
                        {{-- Tombol Batal Edit (Hanya muncul pas edit) --}}
                        <button x-show="classForm.isEdit" @click="classForm = { id: '', name: '', isEdit: false }" class="text-[10px] font-bold text-red-500 uppercase hover:underline">Batal Edit</button>
                    </div>

                    <form :action="classForm.isEdit ? '/admin/kelas/' + classForm.id : '{{ route('admin.kelas.store') }}'" method="POST" class="flex gap-4">
                        @csrf 
                        {{-- Handle Method PUT kalau lagi Edit --}}
                        <template x-if="classForm.isEdit"><input type="hidden" name="_method" value="PUT"></template>
                        
                        <input type="hidden" name="department_id" :value="selectedDept.id">
                        
                        <div class="flex-1">
                            <input type="text" name="name" x-model="classForm.name" placeholder="Nama Kelas (Cth: 10 PPLG 1)" required 
                                   class="w-full px-5 py-3 bg-white border border-gray-200 rounded-xl font-bold text-xs outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                        </div>
                        
                        <button type="submit" 
                                class="px-6 py-3 text-white rounded-xl font-black text-xs uppercase shadow-md transition-all active:scale-95 flex items-center gap-2"
                                :class="classForm.isEdit ? 'bg-blue-600 hover:bg-blue-700' : 'bg-emerald-600 hover:bg-emerald-700'">
                            <i class="bi" :class="classForm.isEdit ? 'bi-save' : 'bi-plus-lg'"></i>
                            <span x-text="classForm.isEdit ? 'Simpan' : 'Tambah'"></span>
                        </button>
                    </form>
                </div>

                {{-- List Kelas --}}
                <div>
                    <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">Daftar Kelas Terdaftar</h4>
                    <div class="space-y-3">
                        <template x-for="kelas in selectedDept.class_rooms" :key="kelas.id">
                            <div class="flex items-center justify-between p-4 bg-white border border-gray-100 rounded-2xl shadow-sm hover:border-emerald-200 transition-all">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center font-black text-xs"><span x-text="kelas.name.substring(0, 3)"></span></div>
                                    <p class="font-black text-sm text-gray-800 uppercase" x-text="kelas.name"></p>
                                </div>
                                <div class="flex gap-2">
                                    {{-- Tombol Edit (Pensil) --}}
                                    <button @click="classForm = { id: kelas.id, name: kelas.name, isEdit: true }" class="w-8 h-8 rounded-lg bg-blue-50 text-blue-500 hover:bg-blue-600 hover:text-white transition-all flex items-center justify-center">
                                        <i class="bi bi-pencil-square text-xs"></i>
                                    </button>
                                    
                                    {{-- Tombol Hapus (Sampah) - Memicu Modal Delete Global --}}
                                    <button @click="selectedItem = {id: kelas.id, name: kelas.name}; actionRoute = '/admin/kelas/' + kelas.id; modalDelete = true" 
                                            class="w-8 h-8 rounded-lg bg-red-50 text-red-500 hover:bg-red-600 hover:text-white transition-all flex items-center justify-center">
                                        <i class="bi bi-trash3-fill text-xs"></i>
                                    </button>
                                </div>
                            </div>
                        </template>
                        
                        <div x-show="selectedDept.class_rooms.length === 0" class="text-center py-8 border-2 border-dashed border-gray-100 rounded-3xl">
                            <i class="bi bi-inbox text-4xl text-gray-200"></i>
                            <p class="text-xs font-bold text-gray-400 uppercase mt-2">Belum ada kelas</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL DETAIL KATEGORI (BARANG) --}}
    <div x-show="modalDetailBarang" x-cloak class="fixed inset-0 z-[120] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-md" @click="modalDetailBarang = false"></div>
        <div x-show="modalDetailBarang" x-transition.scale.95 class="relative w-full max-w-2xl bg-white rounded-[3rem] p-10 shadow-2xl flex flex-col border border-white leading-none overflow-y-auto max-h-[90vh] custom-scroll">
            <div class="flex justify-between items-start mb-8 text-left leading-none">
                <div class="text-left">
                    <span class="text-[9px] font-black text-indigo-500 uppercase tracking-[0.2em] bg-indigo-50 px-2 py-1 rounded border border-indigo-100">Detail Klasifikasi</span>
                    <h3 class="text-3xl font-black text-slate-900 uppercase tracking-tight leading-none mt-3" x-text="selectedCat.name"></h3>
                </div>
                <div class="text-right">
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1">Total Aset</span>
                    <span class="text-2xl font-black text-indigo-600" x-text="selectedCat.total_stock + ' Unit'"></span>
                </div>
            </div>
            <div class="flex flex-wrap gap-2 mb-8 leading-none">
                <template x-if="selectedCat.depts.length > 0">
                    <template x-for="d in selectedCat.depts"><span class="text-[9px] font-black text-indigo-600 bg-indigo-50 border border-indigo-100 px-3 py-1.5 rounded-lg uppercase" x-text="d"></span></template>
                </template>
                <template x-if="selectedCat.depts.length === 0"><span class="text-[9px] font-black text-gray-500 bg-gray-100 border border-gray-200 px-3 py-1.5 rounded-lg uppercase">GLOBAL / UMUM</span></template>
            </div>
            <div class="flex-1 overflow-y-auto custom-scroll pr-2 space-y-3 max-h-[40vh] text-left leading-none">
                <template x-for="item in selectedCat.items" :key="item.code">
                    <div class="flex items-center justify-between p-5 rounded-[2rem] bg-slate-50 border border-slate-100 leading-none hover:bg-white hover:shadow-md transition-all">
                        <div class="text-left leading-none">
                            <p class="font-black text-slate-800 text-sm uppercase mb-1.5" x-text="item.name"></p>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest leading-none" x-text="'Kode: ' + item.code"></p>
                        </div>
                        <div class="text-right leading-none">
                            <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1">Stok</span>
                            <span class="text-lg font-black text-indigo-600" x-text="item.stock"></span>
                        </div>
                    </div>
                </template>
                <template x-if="selectedCat.items.length === 0"><div class="text-center py-10 text-gray-400 italic text-xs font-bold uppercase">Belum ada barang.</div></template>
            </div>
            <button @click="modalDetailBarang = false" class="w-full mt-8 py-5 bg-slate-900 text-white rounded-[2rem] font-black text-[10px] uppercase tracking-widest active:scale-95 transition-all">Tutup Detail</button>
        </div>
    </div>

    {{-- MODAL DELETE --}}
    <div x-show="modalDelete" x-cloak class="fixed inset-0 z-[150] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="modalDelete = false"></div>
        <div x-show="modalDelete" x-transition.scale.95 class="relative w-full max-w-sm bg-white rounded-[3rem] p-10 text-center border border-white leading-none">
            <div class="w-20 h-20 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner"><i class="bi bi-trash3-fill text-3xl"></i></div>
            <h3 class="text-2xl font-black text-gray-900 mb-2 uppercase tracking-tight">Hapus Data?</h3>
            <p class="text-sm text-gray-500 mb-10 leading-relaxed font-medium">Data <span class="font-black text-gray-900 uppercase" x-text="selectedItem.name"></span> akan dihapus.</p>
            <form :action="actionRoute" method="POST" class="flex gap-4 leading-none">
                @csrf @method('DELETE')
                <button type="button" @click="modalDelete = false" class="flex-1 py-4 rounded-2xl bg-gray-100 text-slate-600 font-bold text-[10px] uppercase tracking-widest">Batal</button>
                <button type="submit" class="flex-1 py-4 rounded-2xl bg-red-500 text-white font-bold text-[10px] uppercase shadow-xl active:scale-95 transition-all">Ya, Hapus</button>
            </form>
        </div>
    </div>

</body>
</html>