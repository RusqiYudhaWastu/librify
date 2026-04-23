<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Katalog Buku - Librify Admin</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
        .font-jakarta { font-family: 'Plus Jakarta Sans', sans-serif; }
        .custom-scroll::-webkit-scrollbar { width: 5px; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #4f46e5; border-radius: 20px; }
    </style>
</head>

<body class="antialiased flex h-screen w-full overflow-hidden text-left font-jakarta" 
      x-data="inventoryAdmin()">

    {{-- Sidebar Admin --}}
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'" 
           class="fixed inset-y-0 left-0 z-50 flex flex-col w-72 bg-[#0F172A] text-white border-r border-slate-800 md:static md:flex-shrink-0 h-full transition-transform duration-300">
        @include('admin.partials.sidebar')
    </aside>

    <div class="flex flex-1 flex-col h-full min-w-0 overflow-hidden text-left">
        @include('admin.partials.header')

        <main class="flex-1 overflow-y-auto p-6 lg:p-10 pt-2 custom-scroll">
            <div class="mx-auto w-full max-w-[1550px] space-y-8">
                
                {{-- Notifikasi --}}
                @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" class="bg-emerald-500 text-white px-6 py-4 rounded-2xl shadow-lg flex justify-between items-center mb-6">
                    <span class="font-bold"><i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}</span>
                    <button @click="show = false"><i class="bi bi-x-lg"></i></button>
                </div>
                @endif
                
                @if(session('error') || $errors->any())
                <div x-data="{ show: true }" x-show="show" class="bg-red-500 text-white px-6 py-4 rounded-2xl shadow-lg flex justify-between items-center mb-6">
                    <span class="font-bold text-sm">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> 
                        {{ session('error') ?? 'Terdapat kesalahan pada input form.' }}
                    </span>
                    <button @click="show = false"><i class="bi bi-x-lg"></i></button>
                </div>
                @endif

                {{-- 1. HEADER & SEARCH --}}
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="text-left">
                        <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight leading-none uppercase">Katalog Buku</h2>
                        <p class="text-sm font-medium text-gray-500 mt-2 uppercase tracking-widest leading-none">Manajemen Koleksi Perpustakaan Librify</p>
                    </div>

                    <div class="flex flex-1 max-w-2xl gap-4">
                        <div class="relative flex-1 group">
                            <i class="bi bi-search absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-indigo-600 transition-colors"></i>
                            <input type="text" x-model="search" placeholder="Cari judul buku, penulis, ISBN..." class="w-full pl-12 pr-5 py-4 bg-white border border-gray-100 rounded-2xl outline-none focus:ring-4 focus:ring-indigo-500/10 shadow-sm font-medium text-sm transition-all">
                        </div>
                        
                        <button @click="openAddModal()" class="inline-flex items-center gap-2.5 rounded-2xl bg-indigo-600 px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition-all active:scale-95 flex-shrink-0 uppercase">
                            <i class="bi bi-plus-lg"></i>
                            <span>Tambah Buku</span>
                        </button>
                    </div>
                </div>

                {{-- 2. ANALYTICS CARDS --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 leading-none">
                    <div class="bg-white p-6 rounded-[2.2rem] border border-gray-100 shadow-sm flex items-center gap-5 border-l-4 border-l-indigo-500">
                        <div class="w-14 h-14 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center text-2xl shadow-inner"><i class="bi bi-book-half"></i></div>
                        <div><p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 text-left">Total Eksemplar</p><p class="text-2xl font-black text-gray-900 leading-none">{{ $items->sum('stock') + $items->sum('maintenance_stock') + $items->sum('broken_stock') }}</p></div>
                    </div>
                    <div class="bg-white p-6 rounded-[2.2rem] border border-gray-100 shadow-sm flex items-center gap-5 border-l-4 border-l-emerald-500">
                        <div class="w-14 h-14 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center text-2xl shadow-inner"><i class="bi bi-check-circle"></i></div>
                        <div><p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 text-left">Tersedia</p><p class="text-2xl font-black text-emerald-600 leading-none">{{ $items->sum('stock') }}</p></div>
                    </div>
                    <div class="bg-white p-6 rounded-[2.2rem] border border-gray-100 shadow-sm flex items-center gap-5 border-l-4 border-l-orange-500">
                        <div class="w-14 h-14 bg-orange-50 text-orange-600 rounded-2xl flex items-center justify-center text-2xl shadow-inner"><i class="bi bi-tools"></i></div>
                        <div><p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 text-left">Perawatan</p><p class="text-2xl font-black text-orange-600 leading-none">{{ $items->sum('maintenance_stock') }}</p></div>
                    </div>
                    <div class="bg-slate-900 p-6 rounded-[2.2rem] shadow-xl flex items-center gap-5 text-white border border-slate-800 border-l-4 border-l-red-500">
                        <div class="w-14 h-14 bg-white/10 text-red-400 rounded-2xl flex items-center justify-center text-2xl shadow-inner"><i class="bi bi-exclamation-octagon"></i></div>
                        <div><p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1 text-left">Rusak/Hilang</p><p class="text-2xl font-black text-red-400 text-left leading-none">{{ $items->sum('broken_stock') }}</p></div>
                    </div>
                </div>

                {{-- 3. DATA TABLE --}}
                <div class="rounded-[2.5rem] bg-white shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 overflow-hidden">
                    <div class="overflow-x-auto text-left">
                        <table class="w-full text-left text-sm">
                            <thead>
                                <tr class="text-[10px] uppercase tracking-[0.2em] text-gray-400 font-black bg-gray-50/50">
                                    <th class="px-8 py-6 text-left">Detail Buku</th>
                                    <th class="px-8 py-6 text-left">Kategori / Genre</th>
                                    <th class="px-8 py-6 text-center">Stok Sisa</th>
                                    <th class="px-8 py-6 text-left">Status Kondisi</th>
                                    <th class="px-8 py-6 text-center">Opsi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 font-medium text-gray-600">
                                <template x-for="item in filteredItems" :key="item.id">
                                    <tr class="group hover:bg-gray-50/50 transition-all duration-200">
                                        <td class="px-8 py-5">
                                            <div class="flex items-center gap-4 text-left">
                                                <div class="w-12 h-16 rounded-lg overflow-hidden bg-gray-100 border border-gray-100 flex-shrink-0 relative shadow-sm">
                                                    <template x-if="item.image">
                                                        <img :src="'{{ asset('storage') }}/' + item.image" class="w-full h-full object-cover">
                                                    </template>
                                                    <template x-if="!item.image">
                                                        <div class="w-full h-full flex items-center justify-center text-gray-300">
                                                            <i class="bi bi-image text-lg"></i>
                                                        </div>
                                                    </template>
                                                </div>
                                                <div class="flex flex-col">
                                                    <span class="text-gray-900 font-black text-sm uppercase max-w-[250px] truncate" x-text="item.name"></span>
                                                    <span class="text-[9px] text-gray-500 font-bold uppercase mt-1" x-text="item.author || 'Penulis Tidak Diketahui'"></span>
                                                    <span class="text-[9px] text-indigo-500 font-bold uppercase tracking-widest mt-1" x-text="'ISBN: ' + item.asset_code"></span>
                                                </div>
                                            </div>
                                        </td>
                                        
                                        <td class="px-8 py-5 text-left">
                                            <div class="flex flex-wrap gap-1.5 max-w-[220px]">
                                                <template x-if="item.categories && item.categories.length > 0">
                                                    <template x-for="cat in item.categories" :key="cat.id">
                                                        <span class="inline-flex items-center px-2.5 py-1 bg-indigo-50 border border-indigo-100 rounded-lg text-[9px] font-black text-indigo-600 uppercase tracking-widest shadow-sm" x-text="cat.name"></span>
                                                    </template>
                                                </template>
                                                <template x-if="!item.categories || item.categories.length === 0">
                                                    <span class="inline-flex items-center px-2.5 py-1 bg-slate-100 border border-slate-200 rounded-lg text-[9px] font-black text-slate-500 uppercase tracking-widest shadow-sm">Umum</span>
                                                </template>
                                            </div>
                                        </td>
                                        
                                        <td class="px-8 py-5 text-center">
                                            <div class="w-12 h-10 mx-auto rounded-xl bg-emerald-50 flex items-center justify-center font-black text-emerald-700 border border-emerald-100 shadow-sm" x-text="item.stock"></div>
                                        </td>
                                        <td class="px-8 py-5 text-left">
                                            <template x-if="(!item.maintenance_stock || item.maintenance_stock == 0) && (!item.broken_stock || item.broken_stock == 0)">
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-wider bg-slate-100 text-slate-500 border border-slate-200 shadow-sm">
                                                    <i class="bi bi-shield-check"></i> AMAN
                                                </span>
                                            </template>
                                            <template x-if="(item.maintenance_stock && item.maintenance_stock > 0) || (item.broken_stock && item.broken_stock > 0)">
                                                <div class="flex flex-wrap gap-2">
                                                    <template x-if="item.maintenance_stock && item.maintenance_stock > 0">
                                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[8px] font-black uppercase bg-orange-100 text-orange-600 border border-orange-200 shadow-sm">
                                                            <i class="bi bi-tools"></i> <span x-text="item.maintenance_stock + ' Maint'"></span>
                                                        </span>
                                                    </template>
                                                    <template x-if="item.broken_stock && item.broken_stock > 0">
                                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[8px] font-black uppercase bg-red-100 text-red-600 border border-red-200 shadow-sm">
                                                            <i class="bi bi-exclamation-triangle"></i> <span x-text="item.broken_stock + ' Rusak'"></span>
                                                        </span>
                                                    </template>
                                                </div>
                                            </template>
                                        </td>
                                        <td class="px-8 py-5 text-center">
                                            <div class="flex items-center justify-center gap-2">
                                                <button @click="openDetailModal(item)" class="w-9 h-9 rounded-xl bg-gray-50 text-gray-400 hover:bg-indigo-50 hover:text-indigo-600 transition-all shadow-sm flex items-center justify-center"><i class="bi bi-eye-fill"></i></button>
                                                <button @click="openEditModal(item)" class="w-9 h-9 rounded-xl bg-gray-50 text-gray-400 hover:bg-blue-50 hover:text-blue-600 transition-all shadow-sm flex items-center justify-center"><i class="bi bi-pencil-square"></i></button>
                                                <button @click="openStatusModal(item)" class="w-9 h-9 rounded-xl bg-gray-50 text-gray-400 hover:bg-purple-50 hover:text-purple-600 transition-all shadow-sm flex items-center justify-center" title="Manajemen Kondisi"><i class="bi bi-activity"></i></button>
                                                <button @click="selectedItem = item; maintRoute = `/admin/barang/${item.id}/maintenance`; modalMaint = true" class="w-9 h-9 rounded-xl bg-gray-50 text-gray-400 hover:bg-orange-50 hover:text-orange-600 transition-all shadow-sm flex items-center justify-center" title="Jadwal Perawatan"><i class="bi bi-calendar-check"></i></button>
                                                <button @click="selectedItem = item; deleteRoute = `/admin/barang/${item.id}`; modalDelete = true" class="w-9 h-9 rounded-xl bg-gray-50 text-gray-400 hover:bg-red-50 hover:text-red-600 transition-all shadow-sm flex items-center justify-center"><i class="bi bi-trash3-fill"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    {{-- ✅ MODAL DETAIL --}}
    <div x-show="modalDetail" x-cloak class="fixed inset-0 z-[120] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="modalDetail = false"></div>
        <div x-show="modalDetail" x-transition.scale.95 class="relative w-full max-w-3xl bg-white rounded-[2.5rem] shadow-2xl overflow-hidden border border-white flex flex-col md:flex-row h-auto md:h-[550px]">
            <div class="w-full md:w-1/2 bg-slate-50 relative flex flex-col items-center justify-center p-8 border-r border-slate-100">
                <template x-if="selectedItem.image">
                    <img :src="'{{ asset('storage') }}/' + selectedItem.image" class="w-full h-auto max-h-[350px] object-cover rounded-xl shadow-md border border-slate-200">
                </template>
                <template x-if="!selectedItem.image">
                    <div class="flex flex-col items-center justify-center text-slate-300">
                        <i class="bi bi-book text-6xl mb-2"></i>
                        <span class="text-[10px] font-bold uppercase tracking-widest">Belum Ada Cover</span>
                    </div>
                </template>
            </div>

            <div class="w-full md:w-1/2 p-8 md:p-10 flex flex-col h-full overflow-y-auto custom-scroll text-left leading-none">
                <div>
                    <div class="mb-6 border-b border-slate-100 pb-5 text-left">
                        <h3 class="text-2xl font-black text-slate-900 tracking-tight mb-2 uppercase" x-text="selectedItem.name"></h3>
                        <p class="text-xs font-bold text-gray-500 mb-4" x-text="'Oleh: ' + (selectedItem.author || 'Anonim')"></p>
                        
                        <div class="flex flex-wrap gap-2">
                            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-50 border border-indigo-100">
                                <span class="text-[9px] font-black text-indigo-400 uppercase tracking-widest">ISBN:</span>
                                <span class="text-[10px] font-black text-indigo-700 uppercase tracking-widest" x-text="selectedItem.asset_code"></span>
                            </div>
                            
                            {{-- Tampilkan Semua Genre --}}
                            <template x-if="selectedItem.categories && selectedItem.categories.length > 0">
                                <template x-for="cat in selectedItem.categories" :key="cat.id">
                                    <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-100 border border-slate-200">
                                        <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest" x-text="cat.name"></span>
                                    </div>
                                </template>
                            </template>
                        </div>
                    </div>
                    
                    <div class="space-y-3 mb-6 text-left leading-none">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2 mb-2"><i class="bi bi-layers"></i> Distribusi Eksemplar</p>
                        <div class="flex items-center justify-between bg-emerald-50 p-4 rounded-xl border border-emerald-100">
                            <div><p class="text-[9px] font-black text-emerald-500 uppercase tracking-widest mb-1">Tersedia</p><p class="text-lg font-black text-emerald-700 leading-none" x-text="selectedItem.stock + ' Buku'"></p></div>
                            <i class="bi bi-check-circle-fill text-2xl text-emerald-400"></i>
                        </div>
                        <div class="flex items-center justify-between bg-orange-50 p-4 rounded-xl border border-orange-100">
                            <div><p class="text-[9px] font-black text-orange-500 uppercase tracking-widest mb-1 text-left">Perawatan (Maint)</p><p class="text-lg font-black text-orange-700 leading-none" x-text="(selectedItem.maintenance_stock || 0) + ' Buku'"></p></div>
                            <i class="bi bi-tools text-2xl text-orange-400"></i>
                        </div>
                        <div class="flex items-center justify-between bg-red-50 p-4 rounded-xl border border-red-100">
                            <div><p class="text-[9px] font-black text-red-500 uppercase tracking-widest mb-1 text-left">Rusak / Hilang</p><p class="text-lg font-black text-red-700 leading-none" x-text="(selectedItem.broken_stock || 0) + ' Buku'"></p></div>
                            <i class="bi bi-exclamation-triangle-fill text-2xl text-red-400"></i>
                        </div>
                    </div>

                    {{-- Informasi Penerbit --}}
                    <div class="grid grid-cols-2 gap-3 mb-6 text-left">
                        <div class="p-3 bg-white border border-slate-100 rounded-xl shadow-sm">
                            <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Penerbit</p>
                            <p class="text-[10px] font-bold text-slate-700 uppercase" x-text="selectedItem.publisher || '-'"></p>
                        </div>
                        <div class="p-3 bg-white border border-slate-100 rounded-xl shadow-sm">
                            <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Tahun Terbit</p>
                            <p class="text-[10px] font-bold text-slate-700 uppercase" x-text="selectedItem.publish_year || '-'"></p>
                        </div>
                    </div>

                    <div class="mb-6 text-left leading-none">
                        <p class="text-[10px] font-black text-slate-400 uppercase mb-2 tracking-widest flex items-center gap-2"><i class="bi bi-card-text"></i> Sinopsis / Deskripsi</p>
                        <div class="p-4 bg-white border border-slate-100 rounded-2xl text-xs font-medium text-slate-600 leading-relaxed shadow-sm">
                            <span x-text="selectedItem.description || 'Tidak ada deskripsi.'"></span>
                        </div>
                    </div>

                    <template x-if="selectedItem.maintenance_date">
                        <div class="mb-6 text-left leading-none">
                            <p class="text-[10px] font-black text-orange-500 uppercase mb-2 tracking-widest flex items-center gap-2"><i class="bi bi-calendar-check"></i> Jadwal Perawatan Terdekat</p>
                            <div class="p-4 bg-orange-50/50 border border-orange-100 rounded-2xl">
                                <p class="text-xs font-black text-orange-700 mb-1" x-text="selectedItem.maintenance_date"></p>
                                <p class="text-[10px] font-medium text-orange-600 italic" x-text="selectedItem.maintenance_note || 'Tanpa catatan tindakan.'"></p>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="mt-auto pt-4 flex justify-end">
                    <button @click="modalDetail = false" class="px-8 py-3.5 bg-slate-900 text-white rounded-xl font-bold text-[10px] uppercase tracking-widest">Tutup Detail</button>
                </div>
            </div>
        </div>
    </div>

    {{-- ✅ MODAL TAMBAH / EDIT BUKU (DESAIN KOTAK KATEGORI) --}}
    <div x-show="modalForm" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="modalForm = false"></div>
        <div x-show="modalForm" x-transition.scale.95 class="relative w-full max-w-3xl bg-white rounded-[2.5rem] shadow-2xl p-8 lg:p-10 border border-white text-left overflow-y-auto max-h-[90vh] custom-scroll">
            <h3 class="text-2xl font-black text-gray-900 mb-8 uppercase tracking-tight" x-text="isEdit ? 'Update Data Buku' : 'Registrasi Buku Baru'"></h3>
            
            <form :action="isEdit ? editRoute : '{{ route('admin.barang.store') }}'" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                <template x-if="isEdit">@method('PUT')</template>
                
                <div class="flex flex-col md:flex-row items-center gap-6 p-6 bg-slate-50/50 rounded-3xl border border-dashed border-gray-200">
                    <div class="w-24 h-32 rounded-lg overflow-hidden bg-white border flex-shrink-0 relative shadow-sm">
                        <template x-if="!photoPreview && isEdit && selectedItem.image"><img :src="'{{ asset('storage') }}/' + selectedItem.image" class="w-full h-full object-cover"></template>
                        <template x-if="!photoPreview && (!isEdit || !selectedItem.image)"><div class="w-full h-full flex flex-col items-center justify-center text-gray-300"><i class="bi bi-image text-3xl"></i></div></template>
                        <template x-if="photoPreview"><img :src="photoPreview" class="w-full h-full object-cover"></template>
                    </div>
                    <div class="space-y-2 w-full">
                        <label class="text-[10px] font-black text-indigo-500 uppercase tracking-widest block leading-none">Cover Buku</label>
                        <input type="file" name="image" class="text-[10px] w-full text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:bg-indigo-50 file:text-indigo-600 file:font-bold cursor-pointer" accept="image/*" @change="const file = $event.target.files[0]; if(file){ const reader = new FileReader(); reader.onload = (e) => { photoPreview = e.target.result }; reader.readAsDataURL(file); }">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-5">
                    <div class="col-span-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest text-left">Judul Buku (Wajib)</label>
                        <input type="text" name="name" x-model="selectedItem.name" required class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-xl outline-none font-bold text-gray-800 focus:ring-2 focus:ring-indigo-500/20 shadow-inner">
                    </div>
                    
                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest text-left">Penulis</label>
                        <input type="text" name="author" x-model="selectedItem.author" class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-xl outline-none font-bold text-sm text-gray-800 focus:ring-2 focus:ring-indigo-500/20 shadow-inner" placeholder="Nama Penulis">
                    </div>

                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest text-left">Penerbit</label>
                        <input type="text" name="publisher" x-model="selectedItem.publisher" class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-xl outline-none font-bold text-sm text-gray-800 focus:ring-2 focus:ring-indigo-500/20 shadow-inner" placeholder="Nama Penerbit">
                    </div>

                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest text-left">Tahun Terbit</label>
                        <input type="number" name="publish_year" x-model="selectedItem.publish_year" min="1900" max="{{ date('Y') }}" class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-xl outline-none font-bold focus:ring-2 focus:ring-indigo-500/20 shadow-inner" placeholder="YYYY">
                    </div>

                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest text-left">ISBN / Kode Buku (Wajib)</label>
                        <input type="text" name="asset_code" x-model="selectedItem.asset_code" required class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-xl outline-none font-bold focus:ring-2 focus:ring-indigo-500/20 shadow-inner">
                    </div>

                    <div class="col-span-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest text-left" x-text="isEdit ? 'Jumlah Eksemplar Tersedia' : 'Total Eksemplar Awal'"></label>
                        <input type="number" name="stock" x-model="selectedItem.stock" required min="0" class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-xl outline-none font-bold focus:ring-2 focus:ring-indigo-500/20 shadow-inner">
                    </div>

                    {{-- ✅ KATEGORI / GENRE SEBAGAI KOTAK PILLS (CHECKBOX MULTIPLE FIXED) --}}
                    <div class="col-span-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 text-left"><i class="bi bi-tags-fill me-1 text-indigo-500"></i> Kategori / Genre (Pilih Multi)</label>
                        <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-3 max-h-48 overflow-y-auto custom-scroll p-1">
                            @foreach($categories as $cat)
                            <label class="relative cursor-pointer group">
                                {{-- Value pastikan dikirim sebagai String untuk sinkronisasi Alpine multi-checkbox --}}
                                <input type="checkbox" name="category_ids[]" value="{{ $cat->id }}" x-model="selectedItem.category_ids" class="peer sr-only">
                                <div class="px-4 py-3 bg-white border border-gray-200 rounded-xl peer-checked:bg-indigo-50 peer-checked:border-indigo-500 peer-checked:text-indigo-700 hover:border-indigo-300 transition-all flex items-center justify-between shadow-sm">
                                    <span class="text-xs font-bold truncate transition-colors" title="{{ $cat->name }}">{{ $cat->name }}</span>
                                    <i class="bi bi-check-circle-fill opacity-0 peer-checked:opacity-100 text-indigo-500 transition-opacity"></i>
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="col-span-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest text-left">Sinopsis / Deskripsi</label>
                        <textarea name="description" x-model="selectedItem.description" rows="3" class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-xl outline-none font-medium text-sm focus:ring-2 focus:ring-indigo-500/20 shadow-inner"></textarea>
                    </div>
                </div>

                <div class="pt-4 flex gap-4 mt-8 border-t border-gray-100 pt-6">
                    <button type="button" @click="modalForm = false" class="flex-1 px-6 py-4 rounded-xl bg-gray-100 text-gray-600 font-black text-[10px] uppercase tracking-widest transition-all hover:bg-gray-200">Batalkan</button>
                    <button type="submit" class="flex-1 px-6 py-4 rounded-xl bg-indigo-600 text-white font-black text-[10px] uppercase tracking-widest shadow-xl hover:bg-indigo-700 active:scale-95 transition-all" x-text="isEdit ? 'Simpan Perubahan' : 'Tambahkan Buku'"></button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL MANAJEMEN KONDISI (STOK SPLIT) --}}
    <div x-show="modalStatus" x-cloak class="fixed inset-0 z-[110] flex items-center justify-center p-4 leading-none">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="modalStatus = false"></div>
        <div x-show="modalStatus" x-transition.scale.95 class="relative w-full max-w-md bg-white rounded-[2.5rem] shadow-2xl p-10 border border-white text-center leading-none">
            <div class="w-20 h-20 bg-purple-50 text-purple-600 rounded-[1.5rem] flex items-center justify-center mx-auto mb-6 text-4xl shadow-inner leading-none"><i class="bi bi-shuffle"></i></div>
            <h3 class="text-2xl font-black text-gray-900 mb-1 tracking-tight leading-none uppercase">Manajemen Kondisi Buku</h3>
            <p class="text-[10px] text-gray-500 uppercase tracking-widest font-bold mb-6" x-text="selectedItem.name"></p>
            <form :action="statusRoute" method="POST" class="space-y-6 text-left leading-none">
                @csrf @method('PUT')
                <div class="flex items-center justify-center gap-2 mb-2 leading-none">
                    <span class="px-2.5 py-1 bg-emerald-50 text-emerald-600 rounded-lg text-[9px] font-black uppercase tracking-wider">Siap: <span x-text="selectedItem.stock"></span></span>
                    <span class="px-2.5 py-1 bg-orange-50 text-orange-600 rounded-lg text-[9px] font-black uppercase tracking-wider">Perawatan: <span x-text="selectedItem.maintenance_stock || 0"></span></span>
                    <span class="px-2.5 py-1 bg-red-50 text-red-600 rounded-lg text-[9px] font-black uppercase tracking-wider">Rusak: <span x-text="selectedItem.broken_stock || 0"></span></span>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Pilih Alur Pemindahan</label>
                    <select name="action" x-model="statusAction" class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl outline-none font-bold text-xs text-gray-700 cursor-pointer">
                        <option value="to_maintenance">🔧 Tersedia ➔ Ke Perawatan</option>
                        <option value="to_broken">⚠️ Tersedia ➔ Ke Rusak/Hilang</option>
                        <option value="resolve_maintenance">✅ Selesai Dirawat ➔ Ke Tersedia</option>
                        <option value="resolve_broken">✅ Selesai Diperbaiki ➔ Ke Tersedia</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Jumlah Eksemplar Dipindah</label>
                    <input type="number" name="quantity" x-model="statusQty" min="1" :max="statusAction === 'to_maintenance' || statusAction === 'to_broken' ? selectedItem.stock : (statusAction === 'resolve_maintenance' ? (selectedItem.maintenance_stock || 0) : (selectedItem.broken_stock || 0))" required class="w-full px-5 py-4 bg-white border border-purple-200 rounded-2xl outline-none font-black text-lg text-purple-700 text-center shadow-inner">
                </div>
                <div class="pt-2 flex gap-4"><button type="button" @click="modalStatus = false" class="flex-1 px-6 py-4 rounded-2xl bg-gray-100 text-gray-600 font-black text-[10px] uppercase tracking-widest">Batal</button><button type="submit" class="flex-1 px-6 py-4 rounded-2xl bg-purple-600 text-white font-black text-[10px] uppercase shadow-xl hover:bg-purple-700 transition-all">Pindahkan</button></div>
            </form>
        </div>
    </div>

    {{-- MODAL JADWAL MAINTENANCE --}}
    <div x-show="modalMaint" x-cloak class="fixed inset-0 z-[110] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="modalMaint = false"></div>
        <div x-show="modalMaint" x-transition.scale.95 class="relative w-full max-w-md bg-white rounded-[2.5rem] shadow-2xl p-10 border border-white text-left leading-none">
            <div class="w-16 h-16 bg-orange-50 text-orange-600 rounded-2xl flex items-center justify-center text-2xl mb-6 shadow-inner"><i class="bi bi-calendar-event"></i></div>
            <h3 class="text-2xl font-black text-gray-900 mb-2 uppercase tracking-tight">Jadwal Perawatan</h3>
            <form :action="maintRoute" method="POST" class="space-y-5">
                @csrf @method('PUT')
                <div><label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 leading-none">Tanggal Rencana Perawatan</label><input type="date" name="maintenance_date" :value="selectedItem.maintenance_date" required class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl font-bold text-gray-700"></div>
                <div><label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 leading-none">Rencana Tindakan / Catatan</label><textarea name="maintenance_note" x-model="selectedItem.maintenance_note" rows="3" class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl outline-none text-sm font-medium"></textarea></div>
                <div class="pt-4 flex gap-4"><button type="button" @click="modalMaint = false" class="flex-1 px-6 py-4 rounded-2xl bg-gray-100 text-gray-600 font-black text-[10px] uppercase tracking-widest">Batal</button><button type="submit" class="flex-1 px-6 py-4 rounded-2xl bg-orange-500 text-white font-black text-[10px] uppercase tracking-widest shadow-lg shadow-orange-100 active:scale-95 transition-all">Simpan Jadwal</button></div>
            </form>
        </div>
    </div>

    {{-- MODAL HAPUS --}}
    <div x-show="modalDelete" x-cloak class="fixed inset-0 z-[130] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="modalDelete = false"></div>
        <div x-show="modalDelete" x-transition.scale.95 class="relative w-full max-w-sm bg-white rounded-[3rem] shadow-2xl p-10 text-center border border-white">
            <div class="w-20 h-20 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto mb-6"><i class="bi bi-trash3 text-3xl"></i></div>
            <h3 class="text-2xl font-bold text-gray-900 mb-2 uppercase">Hapus Buku?</h3>
            <p class="text-sm text-gray-500 mb-10 leading-relaxed font-medium">Buku <span class="font-black text-gray-900" x-text="selectedItem.name"></span> akan dihapus permanen dari katalog.</p>
            <div class="flex gap-4"><button @click="modalDelete = false" class="flex-1 px-6 py-4 rounded-2xl bg-gray-100 text-gray-600 font-black text-[10px] uppercase tracking-widest">Batal</button><form :action="deleteRoute" method="POST" class="flex-1">@csrf @method('DELETE')<button type="submit" class="w-full px-6 py-4 rounded-2xl bg-[#EF4444] text-white font-black text-[10px] shadow-lg hover:bg-red-600 transition-all uppercase tracking-widest">Hapus</button></form></div>
        </div>
    </div>

    {{-- SCRIPT LOGIC --}}
    <script>
        function inventoryAdmin() {
            return {
                sidebarOpen: false,
                modalForm: false,
                modalDetail: false,
                modalMaint: false,
                modalStatus: false,
                modalDelete: false,
                isEdit: false,
                search: '',
                photoPreview: null,
                items: {!! json_encode($items) !!}, 
                selectedItem: { category_ids: [] },
                editRoute: '',
                maintRoute: '',
                statusRoute: '',
                deleteRoute: '',
                statusAction: 'to_maintenance',
                statusQty: 1,

                get filteredItems() {
                    if (this.search === '') return this.items;
                    return this.items.filter(i => {
                        const matchName = i.name.toLowerCase().includes(this.search.toLowerCase());
                        const matchCode = i.asset_code.toLowerCase().includes(this.search.toLowerCase());
                        const matchAuthor = i.author && i.author.toLowerCase().includes(this.search.toLowerCase());
                        // Cek apakah ada di salah satu kategori
                        const matchCat = i.categories && i.categories.some(cat => cat.name.toLowerCase().includes(this.search.toLowerCase()));
                        
                        return matchName || matchCode || matchAuthor || matchCat;
                    });
                },

                openAddModal() {
                    this.isEdit = false;
                    this.photoPreview = null;
                    this.selectedItem = { 
                        name: '', asset_code: '', stock: '', description: '', 
                        status: 'ready', category_ids: [], image: '', 
                        maintenance_stock: 0, broken_stock: 0,
                        author: '', publisher: '', publish_year: '' 
                    };
                    this.modalForm = true;
                },

                openEditModal(item) {
                    this.isEdit = true;
                    this.photoPreview = null; 
                    // Gunakan JSON parse untuk deep copy agar tidak bentrok reaktivitas
                    this.selectedItem = JSON.parse(JSON.stringify(item));
                    
                    // ✅ FIX BUG MULTIPLE CHECKBOX ALPINE.JS 
                    // Alpine x-model array membutuhkan value dengan tipe data string agar sinkron dengan input value="" HTML.
                    this.selectedItem.category_ids = item.categories ? item.categories.map(c => c.id.toString()) : [];
                    
                    this.editRoute = `/admin/barang/${item.id}`;
                    this.modalForm = true;
                },

                openDetailModal(item) {
                    this.selectedItem = {...item};
                    this.modalDetail = true;
                },

                openStatusModal(item) {
                    this.selectedItem = {...item};
                    this.statusRoute = `/admin/barang/${item.id}/status`;
                    this.statusAction = 'to_maintenance';
                    this.statusQty = 1;
                    this.modalStatus = true;
                }
            }
        }
    </script>
</body>
</html>