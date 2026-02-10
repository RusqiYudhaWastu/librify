<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manajemen Inventaris - TekniLog Admin</title>

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
      x-data="inventoryAdmin()">

    {{-- Sidebar Admin --}}
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'" 
           class="fixed inset-y-0 left-0 z-50 flex flex-col w-72 bg-[#0F172A] text-white border-r border-slate-800 md:static md:flex-shrink-0 h-full transition-transform duration-300">
        @include('admin.partials.sidebar')
    </aside>

    <div class="flex flex-1 flex-col h-full min-w-0 overflow-hidden text-left">
        {{-- Header Admin --}}
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

                {{-- 1. HEADER & SEARCH --}}
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="text-left">
                        <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight leading-none uppercase">Gudang Inventaris</h2>
                        <p class="text-sm font-medium text-gray-500 mt-2 uppercase tracking-widest leading-none">Manajemen Asset & Kategori SMKN 1 Ciomas</p>
                    </div>

                    <div class="flex flex-1 max-w-2xl gap-4">
                        <div class="relative flex-1 group">
                            <i class="bi bi-search absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-indigo-600 transition-colors"></i>
                            <input type="text" x-model="search" placeholder="Cari nama barang, kode, atau kategori..." class="w-full pl-12 pr-5 py-4 bg-white border border-gray-100 rounded-2xl outline-none focus:ring-4 focus:ring-indigo-500/10 shadow-sm font-medium text-sm transition-all">
                        </div>
                        
                        <button @click="openAddModal()" class="inline-flex items-center gap-2.5 rounded-2xl bg-indigo-600 px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition-all active:scale-95 flex-shrink-0 uppercase">
                            <i class="bi bi-plus-lg"></i>
                            <span>Tambah Asset</span>
                        </button>
                    </div>
                </div>

                {{-- 2. ANALYTICS CARDS --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-white p-6 rounded-[2.2rem] border border-gray-100 shadow-sm flex items-center gap-5">
                        <div class="w-14 h-14 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center text-2xl shadow-inner"><i class="bi bi-box-seam"></i></div>
                        <div><p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 text-left">Total Unit</p><p class="text-2xl font-black text-gray-900 leading-none">{{ $items->sum('stock') }}</p></div>
                    </div>
                    <div class="bg-white p-6 rounded-[2.2rem] border border-gray-100 shadow-sm flex items-center gap-5">
                        <div class="w-14 h-14 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center text-2xl shadow-inner"><i class="bi bi-check-circle"></i></div>
                        <div><p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 text-left">Siap Pakai</p><p class="text-2xl font-black text-gray-900 leading-none">{{ $items->where('status', 'ready')->count() }}</p></div>
                    </div>
                    <div class="bg-white p-6 rounded-[2.2rem] border border-gray-100 shadow-sm flex items-center gap-5">
                        <div class="w-14 h-14 bg-orange-50 text-orange-600 rounded-2xl flex items-center justify-center text-2xl shadow-inner"><i class="bi bi-tools"></i></div>
                        <div><p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 text-left">Maintenance</p><p class="text-2xl font-black text-gray-900 leading-none">{{ $items->where('status', 'maintenance')->count() }}</p></div>
                    </div>
                    <div class="bg-slate-900 p-6 rounded-[2.2rem] shadow-xl flex items-center gap-5 text-white border border-slate-800">
                        <div class="w-14 h-14 bg-white/10 text-red-400 rounded-2xl flex items-center justify-center text-2xl shadow-inner"><i class="bi bi-exclamation-octagon"></i></div>
                        <div><p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1 text-left">Rusak</p><p class="text-2xl font-black text-left leading-none">{{ $items->where('status', 'broken')->count() }}</p></div>
                    </div>
                </div>

                {{-- 3. DATA TABLE --}}
                <div class="rounded-[2.5rem] bg-white shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 overflow-hidden">
                    <div class="overflow-x-auto text-left">
                        <table class="w-full text-left text-sm">
                            <thead>
                                <tr class="text-[10px] uppercase tracking-[0.2em] text-gray-400 font-black bg-gray-50/50">
                                    <th class="px-8 py-6 text-left">Informasi Asset</th>
                                    <th class="px-8 py-6 text-left">Kategori</th>
                                    <th class="px-8 py-6 text-center">Stok</th>
                                    <th class="px-8 py-6 text-left">Status</th>
                                    <th class="px-8 py-6 text-center">Opsi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 font-medium text-gray-600">
                                <template x-for="item in filteredItems" :key="item.id">
                                    <tr class="group hover:bg-gray-50/50 transition-all duration-200">
                                        <td class="px-8 py-5">
                                            <div class="flex items-center gap-4 text-left">
                                                {{-- Foto Thumbnail di Tabel --}}
                                                <div class="w-12 h-12 rounded-xl overflow-hidden bg-gray-100 border border-gray-100 flex-shrink-0 relative">
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
                                                    <span class="text-gray-900 font-black text-base" x-text="item.name"></span>
                                                    <span class="text-[10px] text-indigo-500 font-bold uppercase tracking-widest mt-1" x-text="item.asset_code"></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-8 py-5 text-left">
                                            <span class="px-3 py-1.5 bg-slate-100 rounded-lg text-[10px] font-black text-slate-600 uppercase tracking-wider" 
                                                  x-text="item.category ? item.category.name : 'Umum'"></span>
                                        </td>
                                        <td class="px-8 py-5 text-center">
                                            <div class="w-12 h-10 mx-auto rounded-xl bg-slate-50 flex items-center justify-center font-black text-gray-700 border border-slate-100" x-text="item.stock"></div>
                                        </td>
                                        <td class="px-8 py-5 text-left">
                                            <span :class="{
                                                'bg-indigo-100 text-indigo-600': item.status === 'ready',
                                                'bg-orange-100 text-orange-600': item.status === 'maintenance',
                                                'bg-red-100 text-red-600': item.status === 'broken'
                                            }" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-wider" 
                                            x-text="item.status === 'ready' ? 'Siap Pakai' : (item.status === 'maintenance' ? 'Maintenance' : 'Rusak')"></span>
                                        </td>
                                        <td class="px-8 py-5 text-center">
                                            <div class="flex items-center justify-center gap-2">
                                                <button @click="openDetailModal(item)" class="w-9 h-9 rounded-xl bg-gray-50 text-gray-400 hover:bg-indigo-50 hover:text-indigo-600 transition-all shadow-sm flex items-center justify-center"><i class="bi bi-eye-fill"></i></button>
                                                <button @click="openEditModal(item)" class="w-9 h-9 rounded-xl bg-gray-50 text-gray-400 hover:bg-blue-50 hover:text-blue-600 transition-all shadow-sm flex items-center justify-center"><i class="bi bi-pencil-square"></i></button>
                                                <button @click="selectedItem = item; statusRoute = `/admin/barang/${item.id}/status`; modalStatus = true" class="w-9 h-9 rounded-xl bg-gray-50 text-gray-400 hover:bg-purple-50 hover:text-purple-600 transition-all shadow-sm flex items-center justify-center"><i class="bi bi-activity"></i></button>
                                                <button @click="selectedItem = item; maintRoute = `/admin/barang/${item.id}/maintenance`; modalMaint = true" class="w-9 h-9 rounded-xl bg-gray-50 text-gray-400 hover:bg-orange-50 hover:text-orange-600 transition-all shadow-sm flex items-center justify-center"><i class="bi bi-calendar-check"></i></button>
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

    {{-- ✅ 4. MODAL DETAIL YANG DIPERBARUI (NO ZOOM, CLEAR LAYOUT) --}}
    <div x-show="modalDetail" x-cloak class="fixed inset-0 z-[120] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-md" @click="modalDetail = false"></div>
        
        {{-- Container Modal lebih lebar --}}
        <div x-show="modalDetail" x-transition.scale.95 
             class="relative w-full max-w-3xl bg-white rounded-[2.5rem] shadow-2xl overflow-hidden border border-white flex flex-col md:flex-row h-auto md:h-[520px]">
            
            {{-- KOLOM KIRI: FOTO (Object Contain + Padding) --}}
            <div class="w-full md:w-1/2 bg-slate-50 relative flex items-center justify-center p-8 border-r border-slate-100">
                <template x-if="selectedItem.image">
                    {{-- ✅ LOGIC UTAMA: object-contain biar gak ke-crop, max-h biar gak kegedean --}}
                    <img :src="'{{ asset('storage') }}/' + selectedItem.image" class="w-full h-auto max-h-[350px] object-contain rounded-xl shadow-sm bg-white border border-slate-100">
                </template>
                <template x-if="!selectedItem.image">
                    <div class="flex flex-col items-center justify-center text-slate-300">
                        <i class="bi bi-image text-6xl mb-2"></i>
                        <span class="text-[10px] font-bold uppercase tracking-widest">No Image Available</span>
                    </div>
                </template>
                
                {{-- Status Badge di Pojok --}}
                <div class="absolute top-6 left-6">
                    <span class="px-3 py-1.5 rounded-lg text-[10px] font-black uppercase text-white shadow-lg backdrop-blur-sm tracking-widest"
                          :class="{
                              'bg-emerald-500/90': selectedItem.status === 'ready',
                              'bg-amber-500/90': selectedItem.status === 'maintenance',
                              'bg-red-500/90': selectedItem.status === 'broken'
                          }" x-text="selectedItem.status">
                    </span>
                </div>
            </div>

            {{-- KOLOM KANAN: INFORMASI DETAIL --}}
            <div class="w-full md:w-1/2 p-8 md:p-10 flex flex-col justify-between h-full overflow-y-auto custom-scroll">
                <div>
                    {{-- Judul & Badge Kode --}}
                    <div class="mb-8 border-b border-slate-100 pb-6">
                        <h3 class="text-3xl font-black text-slate-900 leading-none tracking-tight mb-3" x-text="selectedItem.name"></h3>
                        <div class="flex flex-wrap gap-2">
                            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-50 border border-indigo-100">
                                <span class="text-[9px] font-black text-indigo-400 uppercase tracking-widest">CODE:</span>
                                <span class="text-[10px] font-black text-indigo-700 uppercase tracking-widest" x-text="selectedItem.asset_code"></span>
                            </div>
                            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-100 border border-slate-200">
                                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">KAT:</span>
                                <span class="text-[10px] font-black text-slate-600 uppercase tracking-widest" x-text="selectedItem.category ? selectedItem.category.name : 'UMUM'"></span>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Grid Informasi --}}
                    <div class="space-y-6">
                        {{-- Stok --}}
                        <div class="flex items-center justify-between bg-slate-50 p-4 rounded-2xl border border-slate-100">
                            <div>
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Stok Gudang</p>
                                <p class="text-xl font-black text-slate-800 leading-none" x-text="selectedItem.stock + ' Unit'"></p>
                            </div>
                            <i class="bi bi-box-seam text-2xl text-slate-300"></i>
                        </div>

                        {{-- Deskripsi --}}
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase mb-2 tracking-widest flex items-center gap-2">
                                <i class="bi bi-card-text"></i> Spesifikasi / Deskripsi
                            </p>
                            <div class="p-4 bg-white border border-slate-100 rounded-2xl text-xs font-medium text-slate-600 leading-relaxed shadow-sm">
                                <span x-text="selectedItem.description || 'Tidak ada deskripsi tambahan untuk alat ini.'"></span>
                            </div>
                        </div>

                        {{-- Info Maintenance --}}
                        <div x-show="selectedItem.maintenance_date" class="pt-2">
                            <p class="text-[10px] font-black text-orange-400 uppercase mb-2 tracking-widest flex items-center gap-2">
                                <i class="bi bi-wrench-adjustable"></i> Info Maintenance
                            </p>
                            <div class="flex items-start gap-4 p-4 bg-orange-50/50 border border-orange-100 rounded-2xl">
                                <div class="flex-1">
                                    <p class="text-[9px] font-bold text-orange-400 uppercase mb-1">Jadwal Servis</p>
                                    <p class="text-xs font-bold text-slate-800" x-text="selectedItem.maintenance_date"></p>
                                </div>
                                <div class="flex-1 border-l border-orange-100 pl-4">
                                    <p class="text-[9px] font-bold text-orange-400 uppercase mb-1">Catatan</p>
                                    <p class="text-xs font-medium text-slate-600" x-text="selectedItem.maintenance_note || '-'"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 pt-4 flex justify-end">
                    <button @click="modalDetail = false" class="px-8 py-3.5 bg-slate-900 text-white rounded-xl font-bold text-[10px] uppercase tracking-widest hover:bg-black hover:shadow-lg transition-all active:scale-95">
                        Tutup Panel
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- 5. MODAL TAMBAH / EDIT (SUPPORT GAMBAR) --}}
    <div x-show="modalForm" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="modalForm = false"></div>
        <div x-show="modalForm" x-transition.scale.95 class="relative w-full max-w-xl bg-white rounded-[2.5rem] shadow-2xl p-10 border border-white text-left overflow-y-auto max-h-[90vh] custom-scroll">
            <h3 class="text-2xl font-black text-gray-900 mb-8" x-text="isEdit ? 'Update Data Asset' : 'Registrasi Asset Baru'"></h3>
            
            <form :action="isEdit ? editRoute : '{{ route('admin.barang.store') }}'" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                <template x-if="isEdit">@method('PUT')</template>
                
                {{-- Input Foto dengan Preview --}}
                <div class="flex items-center gap-6 p-6 bg-slate-50/50 rounded-3xl border border-dashed border-gray-200">
                    <div class="w-24 h-24 rounded-2xl overflow-hidden bg-white border flex-shrink-0 relative">
                        {{-- Preview Logic --}}
                        <template x-if="!photoPreview && isEdit && selectedItem.image">
                            <img :src="'{{ asset('storage') }}/' + selectedItem.image" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!photoPreview && (!isEdit || !selectedItem.image)">
                            <div class="w-full h-full flex flex-col items-center justify-center text-gray-300">
                                <i class="bi bi-image text-3xl"></i>
                            </div>
                        </template>
                        <template x-if="photoPreview">
                            <img :src="photoPreview" class="w-full h-full object-cover">
                        </template>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-indigo-500 uppercase tracking-widest block leading-none">Foto Barang</label>
                        <input type="file" name="image" class="text-[10px] text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-indigo-50 file:text-indigo-600 cursor-pointer" accept="image/*" 
                               @change="const file = $event.target.files[0]; if(file){ const reader = new FileReader(); reader.onload = (e) => { photoPreview = e.target.result }; reader.readAsDataURL(file); }">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-5">
                    <div class="col-span-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Pilih Kategori Barang</label>
                        <select name="category_id" x-model="selectedItem.category_id" required class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl outline-none font-bold text-sm text-gray-700 focus:ring-4 focus:ring-indigo-500/10">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-span-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">Nama Lengkap Barang</label>
                        <input type="text" name="name" x-model="selectedItem.name" required class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl outline-none font-bold text-gray-700">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">Kode Asset</label>
                        <input type="text" name="asset_code" x-model="selectedItem.asset_code" required class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl outline-none font-bold">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">Stok Awal</label>
                        <input type="number" name="stock" x-model="selectedItem.stock" required class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl outline-none font-bold">
                    </div>

                    <div class="col-span-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">Deskripsi Singkat</label>
                        <textarea name="description" x-model="selectedItem.description" rows="3" class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl outline-none font-medium text-sm"></textarea>
                    </div>
                </div>
                <div class="pt-4 flex gap-4">
                    <button type="button" @click="modalForm = false" class="flex-1 px-6 py-4 rounded-2xl bg-gray-100 text-gray-600 font-black text-xs uppercase tracking-widest">Batal</button>
                    <button type="submit" class="flex-1 px-6 py-4 rounded-2xl bg-indigo-600 text-white font-black text-xs uppercase tracking-widest shadow-xl active:scale-95 transition-all">Simpan Asset</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL UPDATE STATUS & MAINTENANCE (Sama seperti sebelumnya) --}}
    <div x-show="modalStatus" x-cloak class="fixed inset-0 z-[110] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="modalStatus = false"></div>
        <div x-show="modalStatus" x-transition.scale.95 class="relative w-full max-w-md bg-white rounded-[2.5rem] shadow-2xl p-10 border border-white text-center">
            <div class="w-20 h-20 bg-purple-50 text-purple-600 rounded-full flex items-center justify-center mx-auto mb-6 text-3xl shadow-inner"><i class="bi bi-activity"></i></div>
            <h3 class="text-2xl font-black text-gray-900 mb-2">Ubah Kondisi</h3>
            <form :action="statusRoute" method="POST" class="space-y-3">
                @csrf @method('PUT')
                <input type="hidden" name="status" :value="newStatus">
                <button type="submit" @click="newStatus = 'ready'" class="w-full py-4 rounded-2xl bg-indigo-50 text-indigo-600 font-black text-[10px] uppercase hover:bg-indigo-600 hover:text-white transition-all">Siap Pakai</button>
                <button type="submit" @click="newStatus = 'maintenance'" class="w-full py-4 rounded-2xl bg-orange-50 text-orange-600 font-black text-[10px] uppercase hover:bg-orange-600 hover:text-white transition-all">Maintenance</button>
                <button type="submit" @click="newStatus = 'broken'" class="w-full py-4 rounded-2xl bg-red-50 text-red-600 font-black text-[10px] uppercase hover:bg-red-600 hover:text-white transition-all">Rusak</button>
            </form>
        </div>
    </div>

    <div x-show="modalMaint" x-cloak class="fixed inset-0 z-[110] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="modalMaint = false"></div>
        <div x-show="modalMaint" x-transition.scale.95 class="relative w-full max-w-md bg-white rounded-[2.5rem] shadow-2xl p-10 border border-white text-left">
            <div class="w-16 h-16 bg-orange-50 text-orange-600 rounded-2xl flex items-center justify-center text-2xl mb-6 shadow-inner"><i class="bi bi-calendar-event"></i></div>
            <h3 class="text-2xl font-black text-gray-900 mb-2">Jadwal Maintenance</h3>
            <form :action="maintRoute" method="POST" class="space-y-5">
                @csrf @method('PUT')
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Tanggal Rencana Servis</label>
                    <input type="date" name="maintenance_date" :value="selectedItem.maintenance_date" required class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl font-bold">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Rencana Tindakan</label>
                    <textarea name="maintenance_note" x-model="selectedItem.maintenance_note" rows="3" class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl outline-none text-sm font-medium"></textarea>
                </div>
                <div class="pt-4 flex gap-4">
                    <button type="button" @click="modalMaint = false" class="flex-1 px-6 py-4 rounded-2xl bg-gray-100 text-gray-600 font-bold text-sm">Batal</button>
                    <button type="submit" class="flex-1 px-6 py-4 rounded-2xl bg-orange-500 text-white font-bold text-sm shadow-lg shadow-orange-100 active:scale-95">Simpan Jadwal</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL HAPUS --}}
    <div x-show="modalDelete" x-cloak class="fixed inset-0 z-[130] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="modalDelete = false"></div>
        <div x-show="modalDelete" x-transition.scale.95 class="relative w-full max-w-sm bg-white rounded-[2.5rem] shadow-2xl p-10 text-center border border-white">
            <div class="w-20 h-20 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto mb-6"><i class="bi bi-trash3 text-3xl"></i></div>
            <h3 class="text-2xl font-bold text-gray-900 mb-2">Hapus Asset?</h3>
            <p class="text-sm text-gray-500 mb-10 leading-relaxed font-medium">Data <span class="font-black text-gray-900" x-text="selectedItem.name"></span> akan dihapus permanen.</p>
            <div class="flex gap-4">
                <button @click="modalDelete = false" class="flex-1 px-6 py-4 rounded-2xl bg-gray-100 text-gray-600 font-bold text-sm">Batal</button>
                <form :action="deleteRoute" method="POST" class="flex-1">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full px-6 py-4 rounded-2xl bg-[#EF4444] text-white font-bold text-sm shadow-lg hover:bg-red-600 transition-all font-jakarta uppercase">Hapus</button>
                </form>
            </div>
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
                items: @json($items),
                selectedItem: {},
                editRoute: '',
                maintRoute: '',
                statusRoute: '',
                deleteRoute: '',
                newStatus: '',

                get filteredItems() {
                    if (this.search === '') return this.items;
                    return this.items.filter(i => 
                        i.name.toLowerCase().includes(this.search.toLowerCase()) || 
                        i.asset_code.toLowerCase().includes(this.search.toLowerCase()) ||
                        (i.category && i.category.name.toLowerCase().includes(this.search.toLowerCase()))
                    );
                },

                openAddModal() {
                    this.isEdit = false;
                    this.photoPreview = null;
                    this.selectedItem = { name: '', asset_code: '', stock: '', description: '', status: 'ready', category_id: '', image: '' };
                    this.modalForm = true;
                },

                openEditModal(item) {
                    this.isEdit = true;
                    this.photoPreview = null; // Reset preview
                    this.selectedItem = {...item}; // Copy data item
                    this.editRoute = `/admin/barang/${item.id}`;
                    this.modalForm = true;
                },

                openDetailModal(item) {
                    this.selectedItem = {...item};
                    this.modalDetail = true;
                }
            }
        }
    </script>
</body>
</html>