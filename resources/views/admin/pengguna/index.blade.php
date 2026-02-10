<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Database Pengguna - TekniLog Admin</title>

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
        tab: 'staff',
        searchQuery: '',
        sidebarOpen: false, 
        
        // Modal States
        modalTambah: false, 
        modalEdit: false, 
        modalHapus: false,
        modalDetail: false,
        
        // Form & UI States
        showPwTambah: false,
        showPwEdit: false,
        photoPreview: null,
        
        // Data Handling
        selectedUser: { 
            id: '', name: '', username: '', email: '', role: '', 
            nisn: '', profile_photo_url: '', 
            department_id: '', class_id: '', 
            chairman_name: '', vice_chairman_name: '', 
            department: {}, class_room: {}, assigned_departments: [] 
        },
        assignedDeptIds: [],
        formRole: 'student', 
        
        // Routes
        deleteRoute: '',
        editRoute: '',
        userNameToDelete: '',
      }">

    {{-- Sidebar Admin --}}
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'" 
           class="fixed inset-y-0 left-0 z-50 flex flex-col w-72 bg-[#0F172A] text-white border-r border-slate-800 md:static md:flex-shrink-0 h-full transition-transform duration-300">
        @include('admin.partials.sidebar')
    </aside>

    <div class="flex flex-1 flex-col h-full min-w-0 overflow-hidden text-left">
        @include('admin.partials.header')

        <main class="flex-1 overflow-y-auto p-6 lg:p-10 pt-2 custom-scroll text-left leading-none">
            <div class="mx-auto w-full max-w-[1600px] space-y-8">
                
                {{-- Notifikasi Sukses --}}
                @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" class="bg-emerald-500 text-white px-6 py-4 rounded-2xl shadow-lg flex justify-between items-center mb-6">
                    <span class="font-bold text-sm uppercase tracking-widest"><i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}</span>
                    <button @click="show = false"><i class="bi bi-x-lg"></i></button>
                </div>
                @endif

                {{-- Notifikasi Error --}}
                @if($errors->any())
                <div class="bg-red-500 text-white px-6 py-4 rounded-2xl shadow-lg mb-6 leading-relaxed">
                    <p class="font-black text-xs uppercase tracking-widest mb-2">Terjadi Kesalahan:</p>
                    <ul class="list-disc list-inside text-[10px] font-bold uppercase">
                        @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                    </ul>
                </div>
                @endif

                {{-- 1. HEADER SECTION --}}
                <div class="flex flex-col xl:flex-row xl:items-end justify-between gap-6 leading-none">
                    <div class="text-left leading-none">
                        <h2 class="text-4xl font-black text-slate-900 tracking-tight uppercase">Database Otoritas</h2>
                        
                        {{-- TAB NAVIGATION --}}
                        <div class="flex flex-wrap bg-slate-200/50 p-1.5 rounded-2xl gap-1 mt-6 w-fit border border-slate-100 shadow-inner leading-none">
                            <button @click="tab = 'staff'; searchQuery = ''" :class="tab === 'staff' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500'" class="px-6 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all leading-none">
                                Staf & Petugas
                            </button>
                            <button @click="tab = 'kelas'; searchQuery = ''" :class="tab === 'kelas' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500'" class="px-6 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all leading-none">
                                Database Kelas
                            </button>
                            <button @click="tab = 'siswa'; searchQuery = ''" :class="tab === 'siswa' ? 'bg-white text-cyan-600 shadow-sm' : 'text-slate-500'" class="px-6 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all leading-none">
                                Siswa Individu
                            </button>
                        </div>
                    </div>
                    
                    <div class="flex flex-col md:flex-row items-center gap-4 w-full xl:w-auto leading-none">
                        <div class="relative w-full md:w-80 leading-none">
                            <i class="bi bi-search absolute left-5 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="text" x-model="searchQuery" placeholder="Cari nama / ID / NISN..." 
                                   class="w-full pl-12 pr-6 py-4 bg-white border border-gray-100 rounded-2xl outline-none font-bold text-xs shadow-sm focus:ring-4 focus:ring-indigo-500/10">
                        </div>
                        {{-- Button Add Dynamic Text --}}
                        <button @click="modalTambah = true; photoPreview = null; formRole = tab === 'staff' ? 'toolman' : (tab === 'kelas' ? 'class' : 'student'); assignedDeptIds = []" 
                                class="w-full md:w-auto inline-flex items-center justify-center gap-3 rounded-2xl bg-indigo-600 px-8 py-4 text-xs font-black text-white shadow-xl hover:bg-indigo-700 active:scale-95 uppercase tracking-widest transition-all leading-none">
                            <i class="bi bi-person-plus-fill text-lg"></i>
                            <span x-text="tab === 'staff' ? 'Tambah Staf' : (tab === 'kelas' ? 'Registrasi Kelas' : 'Tambah Siswa')"></span>
                        </button>
                    </div>
                </div>

                {{-- 2. ANALYTICS CARDS --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 leading-none">
                    <div class="bg-white p-6 rounded-[2.5rem] border border-gray-100 shadow-sm flex items-center gap-5 border-l-4 border-l-indigo-500">
                        <div class="w-14 h-14 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center text-2xl shadow-inner leading-none"><i class="bi bi-people"></i></div>
                        <div class="leading-none text-left"><p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 leading-none">Total Akun</p><p class="text-2xl font-black text-gray-900 leading-none">{{ $users->count() }}</p></div>
                    </div>
                    <div class="bg-white p-6 rounded-[2.5rem] border border-gray-100 shadow-sm flex items-center gap-5 border-l-4 border-l-emerald-500">
                        <div class="w-14 h-14 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center text-2xl shadow-inner leading-none"><i class="bi bi-tools"></i></div>
                        <div class="leading-none text-left"><p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 leading-none">Toolman</p><p class="text-2xl font-black text-gray-900 leading-none">{{ $users->where('role', 'toolman')->count() }}</p></div>
                    </div>
                    <div class="bg-white p-6 rounded-[2.5rem] border border-gray-100 shadow-sm flex items-center gap-5 border-l-4 border-l-blue-500">
                        <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center text-2xl shadow-inner leading-none"><i class="bi bi-building"></i></div>
                        <div class="leading-none text-left"><p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 leading-none">Akun Kelas</p><p class="text-2xl font-black text-gray-900 leading-none">{{ $users->where('role', 'class')->count() }}</p></div>
                    </div>
                    <div class="bg-white p-6 rounded-[2.5rem] border border-gray-100 shadow-sm flex items-center gap-5 border-l-4 border-l-cyan-500">
                        <div class="w-14 h-14 bg-cyan-50 text-cyan-600 rounded-2xl flex items-center justify-center text-2xl shadow-inner leading-none"><i class="bi bi-person-badge"></i></div>
                        <div class="leading-none text-left"><p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 leading-none">Siswa</p><p class="text-2xl font-black text-gray-900 leading-none">{{ $users->where('role', 'student')->count() }}</p></div>
                    </div>
                </div>

                {{-- 3. TABLES SECTION --}}
                <div class="rounded-[3rem] bg-white shadow-sm border border-gray-100 overflow-hidden text-left leading-none">
                    
                    {{-- TAB 1: STAFF (ADMIN & TOOLMAN) --}}
                    <div x-show="tab === 'staff'" x-transition>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm leading-none">
                                <thead>
                                    <tr class="bg-gray-50/50 text-[10px] uppercase tracking-[0.2em] text-gray-400 font-black">
                                        <th class="px-8 py-6">Profil Staf</th>
                                        <th class="px-8 py-6">Informasi Kontak</th>
                                        <th class="px-8 py-6">Level & Wewenang</th>
                                        <th class="px-8 py-6 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @foreach($users->whereIn('role', ['admin', 'toolman']) as $user)
                                    <tr class="group hover:bg-gray-50/50 transition-all leading-none text-left" x-show="searchQuery === '' || '{{ strtolower($user->name) }}'.includes(searchQuery.toLowerCase())">
                                        <td class="px-8 py-5">
                                            <div class="flex items-center gap-4 text-left leading-none">
                                                <img src="{{ $user->profile_photo_url }}" class="h-11 w-11 rounded-2xl object-cover shadow-sm border border-gray-100">
                                                <div class="text-left leading-none">
                                                    <p class="text-gray-900 font-black text-base uppercase mb-1 leading-none">{{ $user->name }}</p>
                                                    <p class="text-[10px] text-gray-400 font-bold uppercase leading-none tracking-widest">ID: {{ $user->username }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-8 py-5 text-left leading-none">
                                            <p class="text-[11px] font-black text-gray-800 leading-none mb-1">{{ $user->email }}</p>
                                            <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest leading-none">Login: {{ $user->username }}</p>
                                        </td>
                                        <td class="px-8 py-5 text-left leading-none align-top">
                                            <div class="flex flex-col items-start gap-2">
                                                <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-[9px] font-black uppercase border {{ $user->role === 'admin' ? 'bg-purple-50 text-purple-600 border-purple-100' : 'bg-emerald-50 text-emerald-600 border-emerald-100' }}">
                                                    {{ $user->role }}
                                                </span>
                                                @if($user->role === 'toolman')
                                                    <div class="flex flex-wrap gap-1.5 max-w-[250px]">
                                                        @foreach($user->assignedDepartments as $dept)
                                                            <span class="inline-block px-2 py-1 rounded-lg bg-white border border-gray-200 text-[8px] font-bold text-gray-500 uppercase tracking-wide shadow-sm">{{ $dept->name }}</span>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-8 py-5 text-center leading-none">
                                            <div class="flex items-center justify-center gap-2">
                                                <button @click="selectedUser = {{ $user->toJson() }}; modalDetail = true" class="w-10 h-10 rounded-xl bg-gray-50 text-gray-400 hover:bg-indigo-600 hover:text-white transition-all flex items-center justify-center shadow-sm"><i class="bi bi-eye-fill"></i></button>
                                                <button @click="selectedUser = {{ $user->toJson() }}; photoPreview = null; assignedDeptIds = {{ $user->assignedDepartments->pluck('id') }}; editRoute = '{{ route('admin.pengguna.update', $user->id) }}'; formRole = '{{ $user->role }}'; modalEdit = true" class="w-10 h-10 rounded-xl bg-gray-50 text-gray-400 hover:bg-blue-600 hover:text-white transition-all flex items-center justify-center shadow-sm"><i class="bi bi-pencil-square"></i></button>
                                                <button @click="modalHapus = true; deleteRoute = '{{ route('admin.pengguna.destroy', $user->id) }}'; userNameToDelete = '{{ $user->name }}'" class="w-10 h-10 rounded-xl bg-gray-50 text-gray-400 hover:bg-red-600 hover:text-white transition-all flex items-center justify-center shadow-sm"><i class="bi bi-trash3-fill"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- TAB 2: DATA KELAS (ROLE: CLASS) --}}
                    <div x-show="tab === 'kelas'" x-transition x-cloak>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm leading-none">
                                <thead>
                                    <tr class="bg-gray-50/50 text-[10px] uppercase tracking-[0.2em] text-gray-400 font-black">
                                        <th class="px-8 py-6">Identitas Kelas</th>
                                        <th class="px-8 py-6">Jurusan & Angkatan</th>
                                        <th class="px-8 py-6">Perangkat Kelas</th>
                                        <th class="px-8 py-6 text-center">Opsi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50 leading-tight">
                                    @foreach($users->where('role', 'class') as $user)
                                    <tr class="group hover:bg-gray-50/50 transition-all leading-none text-left" x-show="searchQuery === '' || '{{ strtolower($user->name) }}'.includes(searchQuery.toLowerCase())">
                                        <td class="px-8 py-5">
                                            <div class="flex items-center gap-4 text-left leading-none">
                                                <img src="{{ $user->profile_photo_url }}" class="h-11 w-11 rounded-2xl object-cover shadow-sm border border-gray-100">
                                                <div class="text-left leading-none">
                                                    {{-- Gunakan Nama Akun (Nama Kelas) --}}
                                                    <p class="text-gray-900 font-black text-base uppercase mb-1 leading-none">{{ $user->name }}</p>
                                                    <p class="text-[10px] text-gray-400 font-bold uppercase leading-none">{{ $user->email }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-8 py-5">
                                            {{-- ✅ FIXED: Tampilkan Nama Kelas yang terhubung --}}
                                            <span class="inline-block px-3 py-1.5 rounded-xl text-[10px] font-black uppercase text-indigo-600 bg-indigo-50 border border-indigo-100 leading-none">
                                                {{ $user->classRoom->name ?? 'Belum Terhubung' }}
                                            </span>
                                            {{-- Tampilkan Jurusan --}}
                                            <p class="text-[9px] font-bold text-gray-400 mt-1 uppercase tracking-wider">
                                                {{ $user->classRoom && $user->classRoom->department ? $user->classRoom->department->name : '-' }}
                                            </p>
                                        </td>
                                        <td class="px-8 py-5 text-left leading-none">
                                            <div class="space-y-1 leading-none">
                                                <p class="text-[11px] font-black text-gray-700 leading-none uppercase">K: {{ $user->chairman_name ?: '-' }}</p>
                                                <p class="text-[9px] font-bold text-gray-400 leading-none uppercase">W: {{ $user->vice_chairman_name ?: '-' }}</p>
                                            </div>
                                        </td>
                                        <td class="px-8 py-5 text-center leading-none">
                                            <div class="flex items-center justify-center gap-2">
                                                {{-- Load relasi classRoom --}}
                                                <button @click="selectedUser = {{ $user->load('classRoom.department')->toJson() }}; modalDetail = true" class="w-10 h-10 rounded-xl bg-gray-50 text-gray-400 hover:bg-indigo-600 hover:text-white transition-all flex items-center justify-center shadow-sm"><i class="bi bi-eye-fill"></i></button>
                                                {{-- Load ID kelas utk form edit --}}
                                                <button @click="selectedUser = {{ $user->toJson() }}; 
                                                                photoPreview = null; 
                                                                editRoute = '{{ route('admin.pengguna.update', $user->id) }}'; 
                                                                formRole = '{{ $user->role }}'; 
                                                                modalEdit = true" 
                                                        class="w-10 h-10 rounded-xl bg-gray-50 text-gray-400 hover:bg-blue-600 hover:text-white transition-all flex items-center justify-center shadow-sm">
                                                    <i class="bi bi-pencil-square"></i>
                                                </button>
                                                <button @click="modalHapus = true; deleteRoute = '{{ route('admin.pengguna.destroy', $user->id) }}'; userNameToDelete = '{{ $user->name }}'" class="w-10 h-10 rounded-xl bg-gray-50 text-gray-400 hover:bg-red-600 hover:text-white transition-all flex items-center justify-center shadow-sm"><i class="bi bi-trash3-fill"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- TAB 3: SISWA INDIVIDU --}}
                    <div x-show="tab === 'siswa'" x-transition x-cloak>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm leading-none">
                                <thead>
                                    <tr class="bg-gray-50/50 text-[10px] uppercase tracking-[0.2em] text-gray-400 font-black">
                                        <th class="px-8 py-6">Profil Siswa</th>
                                        <th class="px-8 py-6">Data Akademik</th>
                                        <th class="px-8 py-6">Kontak</th>
                                        <th class="px-8 py-6 text-center">Opsi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50 leading-tight">
                                    @foreach($users->where('role', 'student') as $user)
                                    <tr class="group hover:bg-gray-50/50 transition-all leading-none text-left" 
                                        x-show="searchQuery === '' || '{{ strtolower($user->name) }}'.includes(searchQuery.toLowerCase()) || '{{ $user->nisn }}'.includes(searchQuery)">
                                        <td class="px-8 py-5">
                                            <div class="flex items-center gap-4 text-left leading-none">
                                                <img src="{{ $user->profile_photo_url }}" class="h-11 w-11 rounded-2xl object-cover shadow-sm border border-gray-100">
                                                <div class="text-left leading-none">
                                                    <p class="text-gray-900 font-black text-base uppercase mb-1 leading-none">{{ $user->name }}</p>
                                                    <p class="text-[9px] text-cyan-600 bg-cyan-50 px-2 py-0.5 rounded-md inline-block font-bold uppercase leading-none tracking-widest mt-1">NISN: {{ $user->nisn ?: '-' }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-8 py-5">
                                            <span class="inline-block px-3 py-1.5 rounded-xl text-[10px] font-black uppercase text-gray-600 bg-gray-100 border border-gray-200 leading-none">
                                                {{ $user->classRoom->name ?? 'Belum Masuk Kelas' }}
                                            </span>
                                            <p class="text-[9px] text-gray-400 font-bold mt-1 uppercase">
                                                {{ $user->classRoom && $user->classRoom->department ? $user->classRoom->department->name : '-' }}
                                            </p>
                                        </td>
                                        <td class="px-8 py-5 text-left leading-none">
                                            <p class="text-[11px] font-black text-gray-800 leading-none mb-1">{{ $user->email }}</p>
                                            <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest leading-none">Login ID: {{ $user->username }}</p>
                                        </td>
                                        <td class="px-8 py-5 text-center leading-none">
                                            <div class="flex items-center justify-center gap-2">
                                                <button @click="selectedUser = {{ $user->load('classRoom')->toJson() }}; modalDetail = true" class="w-10 h-10 rounded-xl bg-gray-50 text-gray-400 hover:bg-cyan-600 hover:text-white transition-all flex items-center justify-center shadow-sm"><i class="bi bi-eye-fill"></i></button>
                                                
                                                <button @click="selectedUser = {{ $user->toJson() }}; photoPreview = null; editRoute = '{{ route('admin.pengguna.update', $user->id) }}'; formRole = '{{ $user->role }}'; modalEdit = true" class="w-10 h-10 rounded-xl bg-gray-50 text-gray-400 hover:bg-blue-600 hover:text-white transition-all flex items-center justify-center shadow-sm"><i class="bi bi-pencil-square"></i></button>
                                                
                                                <button @click="modalHapus = true; deleteRoute = '{{ route('admin.pengguna.destroy', $user->id) }}'; userNameToDelete = '{{ $user->name }}'" class="w-10 h-10 rounded-xl bg-gray-50 text-gray-400 hover:bg-red-600 hover:text-white transition-all flex items-center justify-center shadow-sm"><i class="bi bi-trash3-fill"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </main>
    </div>

    {{-- ✅ MODAL TAMBAH (DINAMIS SESUAI ROLE) ✅ --}}
    <div x-show="modalTambah" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="modalTambah = false"></div>
        <div x-show="modalTambah" x-transition.scale.95 class="relative w-full max-w-2xl bg-white rounded-[3rem] shadow-2xl p-10 border border-white text-left overflow-y-auto max-h-[90vh] custom-scroll leading-none">
            <h3 class="text-3xl font-black text-gray-900 font-jakarta mb-8 uppercase leading-none tracking-tight">Registrasi Pengguna</h3>
            <form action="{{ route('admin.pengguna.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6 text-left leading-none">
                @csrf
                <div class="flex items-center gap-6 p-6 bg-slate-50/50 rounded-3xl border border-dashed border-gray-200">
                    <div class="w-20 h-20 rounded-2xl overflow-hidden bg-white border flex-shrink-0">
                        <template x-if="!photoPreview"><div class="w-full h-full flex items-center justify-center text-gray-300"><i class="bi bi-person-plus-fill text-4xl"></i></div></template>
                        <template x-if="photoPreview"><img :src="photoPreview" class="w-full h-full object-cover"></template>
                    </div>
                    <div class="space-y-2 text-left">
                        <label class="text-[10px] font-black text-indigo-500 uppercase tracking-widest block leading-none">Foto Profil</label>
                        <input type="file" name="profile_photo" class="text-[10px] text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-indigo-50 file:text-indigo-600" accept="image/*" @change="const file = $event.target.files[0]; if(file){ const reader = new FileReader(); reader.onload = (e) => { photoPreview = e.target.result }; reader.readAsDataURL(file); }">
                    </div>
                </div>

                {{-- ROLE SELECTOR --}}
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-4">Pilih Level Akses</label>
                    <input type="hidden" name="role" :value="formRole">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <button type="button" @click="formRole = 'student'" :class="formRole === 'student' ? 'bg-cyan-600 text-white shadow-lg' : 'bg-gray-50 text-gray-400'" class="py-4 rounded-2xl text-[10px] font-black uppercase transition-all">Siswa (Pribadi)</button>
                        <button type="button" @click="formRole = 'class'" :class="formRole === 'class' ? 'bg-indigo-600 text-white shadow-lg' : 'bg-gray-50 text-gray-400'" class="py-4 rounded-2xl text-[10px] font-black uppercase transition-all">Akun Kelas</button>
                        <button type="button" @click="formRole = 'toolman'" :class="formRole === 'toolman' ? 'bg-emerald-600 text-white shadow-lg' : 'bg-gray-50 text-gray-400'" class="py-4 rounded-2xl text-[10px] font-black uppercase transition-all">Toolman</button>
                        <button type="button" @click="formRole = 'admin'" :class="formRole === 'admin' ? 'bg-purple-600 text-white shadow-lg' : 'bg-gray-50 text-gray-400'" class="py-4 rounded-2xl text-[10px] font-black uppercase transition-all">Admin</button>
                    </div>
                </div>

                {{-- FORM NAMA & ID (Berubah saat pilih Akun Kelas) --}}
                <div class="grid grid-cols-2 gap-5">
                    {{-- KONDISI 1: JIKA BUKAN AKUN KELAS (Text Input Biasa) --}}
                    <div x-show="formRole !== 'class'">
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-3">Nama Lengkap</label>
                        <input type="text" name="name" x-model="selectedUser.name" class="w-full px-6 py-5 bg-gray-50 border rounded-2xl outline-none font-bold text-sm">
                    </div>

                    {{-- ✅ UPDATE: JIKA AKUN KELAS (Dropdown Kelas, kirim value ke class_id) --}}
                    <div x-show="formRole === 'class'">
                        <label class="block text-[10px] font-black text-indigo-500 uppercase mb-3">Pilih Kelas (Nama Akun)</label>
                        {{-- Dropdown ini mengisi class_id dan juga nama (text) untuk display --}}
                        <select @change="selectedUser.name = $event.target.options[$event.target.selectedIndex].text; selectedUser.class_id = $event.target.value" 
                                class="w-full px-6 py-5 bg-indigo-50 border border-indigo-200 rounded-2xl font-bold text-sm outline-none cursor-pointer">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($classes as $kelas) <option value="{{ $kelas->id }}">{{ $kelas->name }}</option> @endforeach
                        </select>
                        {{-- Hidden inputs to send data properly --}}
                        <input type="hidden" name="name" x-model="selectedUser.name">
                        <input type="hidden" name="class_id" x-model="selectedUser.class_id"> 
                    </div>

                    <div><label class="block text-[10px] font-black text-gray-400 uppercase mb-3">ID Login (Username)</label><input type="text" name="username" required class="w-full px-6 py-5 bg-gray-50 border rounded-2xl outline-none font-bold text-sm"></div>
                </div>
                <div><label class="block text-[10px] font-black text-gray-400 uppercase mb-3">Email Sistem</label><input type="email" name="email" required class="w-full px-6 py-5 bg-gray-50 border rounded-2xl outline-none font-bold text-sm"></div>
                
                {{-- 🟢 FIELD KHUSUS SISWA --}}
                <div x-show="formRole === 'student'" class="p-8 bg-cyan-50/50 rounded-[2.5rem] border border-cyan-100 space-y-6 text-left transition-all">
                    <div>
                        <label class="block text-[10px] font-black text-cyan-600 uppercase mb-3">Nomor Induk Siswa Nasional (NISN)</label>
                        <input type="number" name="nisn" placeholder="Contoh: 00548xxxx" class="w-full px-6 py-5 bg-white border border-cyan-200 rounded-2xl font-bold text-sm outline-none">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-cyan-600 uppercase mb-3">Pilih Kelas</label>
                        <select name="class_id" class="w-full px-6 py-5 bg-white border border-cyan-200 rounded-2xl font-bold text-sm outline-none appearance-none cursor-pointer">
                            <option value="">-- Pilih Kelas Siswa --</option>
                            @if(isset($classes))
                                @foreach($classes as $kelas) <option value="{{ $kelas->id }}">{{ $kelas->name }}</option> @endforeach
                            @else
                                <option disabled>Data kelas tidak ditemukan</option>
                            @endif
                        </select>
                    </div>
                </div>

                {{-- 🟣 FIELD KHUSUS AKUN KELAS --}}
                <div x-show="formRole === 'class'" class="p-8 bg-indigo-50/50 rounded-[2.5rem] border border-indigo-100 space-y-6 text-left transition-all">
                    <div class="bg-yellow-50 p-4 rounded-xl border border-yellow-100 mb-2">
                        <p class="text-[9px] font-bold text-yellow-600 uppercase">Catatan:</p>
                        <p class="text-[9px] text-yellow-700 mt-1">Nama akun otomatis mengikuti kelas yang dipilih diatas.</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block text-[10px] font-black text-indigo-400 uppercase mb-3 leading-none">Ketua Kelas</label><input type="text" name="chairman_name" class="w-full px-5 py-4 border border-indigo-100 rounded-2xl text-sm font-bold shadow-sm"></div>
                        <div><label class="block text-[10px] font-black text-indigo-400 uppercase mb-3 leading-none">Wakil Ketua</label><input type="text" name="vice_chairman_name" class="w-full px-5 py-4 border border-indigo-100 rounded-2xl text-sm font-bold shadow-sm"></div>
                    </div>
                </div>

                {{-- 🟢 FIELD KHUSUS TOOLMAN --}}
                <div x-show="formRole === 'toolman'" class="p-8 bg-emerald-50/50 rounded-[2.5rem] border border-emerald-100 space-y-4 transition-all">
                    <label class="block text-[10px] font-black text-emerald-600 uppercase mb-4 leading-none">Otoritas Wilayah</label>
                    <div class="grid grid-cols-3 gap-3">@foreach($departments as $dept) <label class="flex items-center gap-2 p-3 bg-white border border-emerald-100 rounded-xl cursor-pointer"><input type="checkbox" name="assigned_dept_ids[]" value="{{ $dept->id }}" class="rounded text-emerald-600"><span class="text-[9px] font-black uppercase text-gray-600 leading-none">{{ $dept->name }}</span></label> @endforeach</div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-3">Password</label>
                    <div class="relative"><input :type="showPwTambah ? 'text' : 'password'" name="password" required class="w-full px-6 py-5 bg-gray-50 border rounded-2xl font-bold pr-14 outline-none"><button type="button" @click="showPwTambah = !showPwTambah" class="absolute right-5 top-1/2 -translate-y-1/2 text-gray-400"><i class="bi" :class="showPwTambah ? 'bi-eye-slash-fill' : 'bi-eye-fill'"></i></button></div>
                </div>
                <div class="pt-4 flex gap-4"><button type="button" @click="modalTambah = false" class="flex-1 px-6 py-5 rounded-2xl bg-gray-100 text-slate-500 font-black text-[10px] uppercase tracking-widest transition-all leading-none">Batal</button><button type="submit" class="flex-1 px-6 py-5 rounded-2xl bg-indigo-600 text-white font-black text-[10px] uppercase shadow-xl transition-all leading-none">Simpan</button></div>
            </form>
        </div>
    </div>

    {{-- ✅ MODAL EDIT (FULL DATA BINDING & DINAMIS) ✅ --}}
    <div x-show="modalEdit" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="modalEdit = false"></div>
        <div x-show="modalEdit" x-transition.scale.95 class="relative w-full max-w-2xl bg-white rounded-[3rem] shadow-2xl p-10 border border-white text-left overflow-y-auto max-h-[90vh] custom-scroll leading-none">
            <h3 class="text-3xl font-black text-gray-900 font-jakarta mb-8 uppercase leading-none tracking-tight">Perbarui Akun</h3>
            
            <form :action="editRoute" method="POST" enctype="multipart/form-data" class="space-y-6 text-left leading-none">
                @csrf @method('PUT')
                
                {{-- Role Hidden Input --}}
                <input type="hidden" name="role" x-model="selectedUser.role">

                <div class="flex items-center gap-6 p-6 bg-slate-50/50 rounded-3xl border border-dashed border-gray-200">
                    <div class="w-20 h-20 rounded-2xl overflow-hidden bg-white border flex-shrink-0 shadow-sm leading-none">
                        <template x-if="!photoPreview"><img :src="selectedUser.profile_photo_url" class="w-full h-full object-cover"></template>
                        <template x-if="photoPreview"><img :src="photoPreview" class="w-full h-full object-cover"></template>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-blue-500 uppercase tracking-widest block leading-none mb-2">Ubah Foto</label>
                        <input type="file" name="profile_photo" class="text-[10px] text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-blue-50 file:text-blue-600" accept="image/*" @change="const file = $event.target.files[0]; if(file){ const reader = new FileReader(); reader.onload = (e) => { photoPreview = e.target.result }; reader.readAsDataURL(file); }">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-5 text-left">
                    {{-- 🟢 EDIT: NAMA (TEXT) VS KELAS (DROPDOWN) --}}
                    <div x-show="formRole !== 'class'">
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 ml-1">Identitas Nama</label>
                        <input type="text" name="name" x-model="selectedUser.name" required class="w-full px-6 py-5 bg-gray-50 border rounded-2xl outline-none font-bold text-sm">
                    </div>
                    {{-- 🟣 EDIT: KHUSUS AKUN KELAS (DROPDOWN) --}}
                    <div x-show="formRole === 'class'">
                        <label class="block text-[10px] font-black text-indigo-500 uppercase mb-3 ml-1">Pilih Kelas (Nama Akun)</label>
                        <select @change="selectedUser.name = $event.target.options[$event.target.selectedIndex].text; selectedUser.class_id = $event.target.value"
                                class="w-full px-6 py-5 bg-indigo-50 border border-indigo-200 rounded-2xl font-bold text-sm outline-none">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($classes as $kelas) 
                                <option value="{{ $kelas->id }}" :selected="selectedUser.class_id == {{ $kelas->id }}">{{ $kelas->name }}</option> 
                            @endforeach
                        </select>
                        <input type="hidden" name="name" x-model="selectedUser.name">
                        <input type="hidden" name="class_id" x-model="selectedUser.class_id">
                    </div>

                    <div><label class="block text-[10px] font-black text-gray-400 uppercase mb-3 ml-1">Username Login</label><input type="text" name="username" x-model="selectedUser.username" required class="w-full px-6 py-5 bg-gray-50 border rounded-2xl outline-none font-bold text-sm"></div>
                </div>
                <div><label class="block text-[10px] font-black text-gray-400 uppercase mb-3 ml-1">Email Sistem</label><input type="email" name="email" x-model="selectedUser.email" required class="w-full px-6 py-5 bg-gray-50 border rounded-2xl outline-none font-bold text-sm"></div>

                {{-- 🟢 EDIT KHUSUS SISWA --}}
                <div x-show="formRole === 'student'" class="p-8 bg-cyan-50 rounded-[2.5rem] border border-cyan-200 space-y-6 text-left leading-none transition-all">
                    <div>
                        <label class="block text-[10px] font-black text-cyan-600 uppercase tracking-widest mb-3 ml-1">NISN Siswa</label>
                        <input type="number" name="nisn" x-model="selectedUser.nisn" class="w-full px-6 py-5 bg-white border border-cyan-200 rounded-2xl font-bold text-sm outline-none">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-cyan-600 uppercase tracking-widest mb-3 ml-1">Update Kelas</label>
                        <select name="class_id" x-model="selectedUser.class_id" class="w-full px-6 py-5 bg-white border border-cyan-200 rounded-2xl font-bold text-sm outline-none appearance-none">
                            <option value="">-- Pilih Kelas --</option>
                            @if(isset($classes))
                                @foreach($classes as $kelas) <option value="{{ $kelas->id }}">{{ $kelas->name }}</option> @endforeach
                            @endif
                        </select>
                    </div>
                </div>

                {{-- 🟣 EDIT KHUSUS AKUN KELAS (STRUKTUR) --}}
                <div x-show="formRole === 'class'" class="p-8 bg-indigo-50 rounded-[2.5rem] border border-indigo-200 space-y-6 text-left leading-none transition-all">
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block text-[10px] font-black text-indigo-400 uppercase mb-3 ml-1">Nama Ketua Kelas</label><input type="text" name="chairman_name" x-model="selectedUser.chairman_name" class="w-full px-5 py-4 border border-indigo-100 rounded-2xl text-sm font-bold shadow-sm outline-none"></div>
                        <div><label class="block text-[10px] font-black text-indigo-400 uppercase mb-3 ml-1">Nama Wakil Ketua</label><input type="text" name="vice_chairman_name" x-model="selectedUser.vice_chairman_name" class="w-full px-5 py-4 border border-indigo-100 rounded-2xl text-sm font-bold shadow-sm outline-none"></div>
                    </div>
                </div>

                {{-- 🟢 EDIT KHUSUS TOOLMAN --}}
                <div x-show="formRole === 'toolman'" class="p-8 bg-emerald-50/50 rounded-[2.5rem] border border-emerald-100 space-y-4 text-left leading-none transition-all">
                    <label class="block text-[10px] font-black text-emerald-600 uppercase tracking-widest mb-4 ml-1">Update Otoritas</label>
                    <div class="grid grid-cols-3 gap-3 leading-none">
                        @foreach($departments as $dept)
                            <label class="flex items-center gap-2 p-3 bg-white border border-emerald-100 rounded-xl cursor-pointer hover:bg-emerald-50 transition-all leading-none shadow-sm">
                                <input type="checkbox" name="assigned_dept_ids[]" value="{{ $dept->id }}" :checked="assignedDeptIds.includes({{ $dept->id }})" class="rounded text-emerald-600 leading-none">
                                <span class="text-[9px] font-black uppercase text-gray-600 leading-none">{{ $dept->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 ml-1 leading-none">Password Baru (Isi jika ingin ganti)</label>
                    <div class="relative leading-none"><input :type="showPwEdit ? 'text' : 'password'" name="password" class="w-full px-6 py-5 bg-gray-50 border rounded-2xl font-bold pr-14 outline-none leading-none"><button type="button" @click="showPwEdit = !showPwEdit" class="absolute right-5 top-1/2 -translate-y-1/2 text-gray-400 transition-colors hover:text-blue-600 leading-none"><i class="bi" :class="showPwEdit ? 'bi-eye-slash-fill' : 'bi-eye-fill'"></i></button></div>
                </div>

                <div class="pt-4 flex gap-4 leading-none text-left">
                    <button type="button" @click="modalEdit = false" class="flex-1 px-6 py-5 rounded-2xl bg-gray-100 text-slate-500 font-black text-[10px] uppercase shadow-sm tracking-widest hover:bg-gray-200 transition-all leading-none">Batal</button>
                    <button type="submit" class="flex-1 px-6 py-5 rounded-2xl bg-blue-600 text-white font-black text-[10px] uppercase shadow-xl hover:bg-blue-700 active:scale-95 transition-all leading-none">Simpan Database</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ✅ MODAL DETAIL (DIPERBAHARUI & LEBIH JELAS) ✅ --}}
    <div x-show="modalDetail" x-cloak class="fixed inset-0 z-[120] flex items-center justify-center p-4 text-left">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-md" @click="modalDetail = false"></div>
        <div x-show="modalDetail" x-transition.scale.95 class="relative w-full max-w-md bg-white rounded-[3rem] shadow-2xl overflow-hidden border border-white leading-none">
            
            {{-- Header with Pattern --}}
            <div class="relative h-32 w-full overflow-hidden" 
                 :class="{
                    'bg-purple-600': selectedUser.role === 'admin',
                    'bg-emerald-600': selectedUser.role === 'toolman',
                    'bg-indigo-600': selectedUser.role === 'class',
                    'bg-cyan-600': selectedUser.role === 'student'
                 }">
                 <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 16px 16px;"></div>
            </div>

            {{-- Profile Content --}}
            <div class="px-10 pb-10 relative">
                <div class="flex flex-col items-center -mt-16 mb-6">
                    <img :src="selectedUser.profile_photo_url" class="w-32 h-32 rounded-[2.5rem] object-cover shadow-2xl border-[6px] border-white bg-white">
                    <div class="mt-4 text-center">
                        <h3 class="text-2xl font-black text-gray-900 font-jakarta uppercase tracking-tight leading-tight" x-text="selectedUser.name"></h3>
                        <div class="mt-2">
                            <span class="inline-flex items-center px-4 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest border shadow-sm"
                                  :class="{
                                      'bg-purple-50 text-purple-600 border-purple-100': selectedUser.role === 'admin',
                                      'bg-emerald-50 text-emerald-600 border-emerald-100': selectedUser.role === 'toolman',
                                      'bg-indigo-50 text-indigo-600 border-indigo-100': selectedUser.role === 'class',
                                      'bg-cyan-50 text-cyan-600 border-cyan-100': selectedUser.role === 'student'
                                  }"
                                  x-text="selectedUser.role === 'class' ? 'AKUN KELAS' : (selectedUser.role === 'student' ? 'SISWA INDIVIDU' : selectedUser.role)">
                            </span>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    {{-- General Info --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100">
                            <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1">Username ID</p>
                            <p class="text-xs font-black text-gray-800" x-text="selectedUser.username"></p>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100">
                            <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1">Email</p>
                            <p class="text-xs font-black text-gray-800 break-all" x-text="selectedUser.email"></p>
                        </div>
                    </div>

                    {{-- Dynamic Content Based on Role --}}
                    
                    {{-- DETAIL: SISWA INDIVIDU --}}
                    <template x-if="selectedUser.role === 'student'">
                        <div class="p-6 bg-cyan-50/50 rounded-3xl border border-cyan-100 text-center">
                            <p class="text-[8px] font-black text-cyan-400 uppercase tracking-widest mb-2">Informasi Akademik</p>
                            <div class="mb-3">
                                <p class="text-xs font-bold text-gray-400">NISN</p>
                                <p class="text-lg font-black text-cyan-900" x-text="selectedUser.nisn || '-'"></p>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-400 mb-1">Kelas Asal</p>
                                <span class="inline-block px-3 py-1 rounded-lg bg-white border border-cyan-200 text-sm font-black text-cyan-600 uppercase" 
                                      x-text="selectedUser.class_room ? selectedUser.class_room.name : 'Belum Masuk Kelas'"></span>
                            </div>
                        </div>
                    </template>

                    {{-- DETAIL: AKUN KELAS --}}
                    <template x-if="selectedUser.role === 'class'">
                        <div class="p-6 bg-indigo-50/50 rounded-3xl border border-indigo-100 space-y-4">
                            <div>
                                <p class="text-[8px] font-black text-indigo-400 uppercase tracking-widest mb-2">Kelas Binaan</p>
                                {{-- Menampilkan Nama Kelas dari Relasi --}}
                                <p class="text-sm font-black text-indigo-900" x-text="selectedUser.class_room ? selectedUser.class_room.name : (selectedUser.department ? selectedUser.department.name : '-')"></p>
                            </div>
                            <div class="grid grid-cols-2 gap-4 pt-4 border-t border-indigo-100">
                                <div><p class="text-[8px] font-black text-indigo-400 uppercase mb-1">Ketua</p><p class="text-xs font-bold text-gray-700 uppercase" x-text="selectedUser.chairman_name || '-'"></p></div>
                                <div><p class="text-[8px] font-black text-indigo-400 uppercase mb-1">Wakil</p><p class="text-xs font-bold text-gray-700 uppercase" x-text="selectedUser.vice_chairman_name || '-'"></p></div>
                            </div>
                        </div>
                    </template>

                    {{-- DETAIL: TOOLMAN --}}
                    <template x-if="selectedUser.role === 'toolman'">
                        <div class="p-6 bg-emerald-50/50 rounded-3xl border border-emerald-100">
                            <p class="text-[9px] font-black text-emerald-600 uppercase tracking-widest mb-4 flex items-center gap-2">
                                <i class="bi bi-shield-check text-lg"></i> Wilayah Otoritas
                            </p>
                            <div class="flex flex-wrap gap-2">
                                <template x-for="dept in selectedUser.assigned_departments" :key="dept.id">
                                    <span class="inline-block px-3 py-1.5 rounded-lg bg-white border border-emerald-100 text-[9px] font-bold text-emerald-700 uppercase shadow-sm" x-text="dept.name"></span>
                                </template>
                                <template x-if="!selectedUser.assigned_departments || selectedUser.assigned_departments.length === 0">
                                    <span class="text-[10px] text-gray-400 font-bold italic w-full text-center py-2">Tidak ada otoritas wilayah.</span>
                                </template>
                            </div>
                        </div>
                    </template>

                    {{-- DETAIL: ADMIN --}}
                    <template x-if="selectedUser.role === 'admin'">
                        <div class="p-5 bg-purple-50/50 rounded-3xl border border-purple-100 text-center">
                            <p class="text-[10px] font-black text-purple-600 uppercase tracking-widest">
                                <i class="bi bi-patch-check-fill me-1"></i> Super Administrator
                            </p>
                            <p class="text-[9px] text-purple-400 font-bold mt-1">Memiliki akses penuh ke sistem.</p>
                        </div>
                    </template>

                    <button @click="modalDetail = false" class="w-full py-4 bg-slate-900 text-white rounded-2xl font-black text-[10px] uppercase shadow-xl hover:bg-slate-800 hover:shadow-2xl transition-all transform active:scale-95 leading-none tracking-widest">
                        Tutup Detail
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL HAPUS --}}
    <div x-show="modalHapus" x-cloak class="fixed inset-0 z-[110] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" @click="modalHapus = false"></div>
        <div x-show="modalHapus" x-transition.scale.95 class="relative w-full max-w-sm bg-white rounded-[3rem] shadow-2xl p-10 text-center border">
            <div class="w-20 h-20 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner"><i class="bi bi-trash3-fill text-3xl"></i></div>
            <h3 class="text-2xl font-black text-gray-900 mb-2 uppercase tracking-tight">Hapus Akun?</h3>
            <p class="text-sm text-gray-500 mb-10 leading-relaxed font-medium">Akun <span class="font-black text-gray-900 uppercase" x-text="userNameToDelete"></span> akan dihapus secara permanen.</p>
            <form :action="deleteRoute" method="POST" class="flex gap-4">
                @csrf @method('DELETE')
                <button type="button" @click="modalHapus = false" class="flex-1 py-4 rounded-2xl bg-gray-100 text-slate-500 font-black text-[10px] uppercase shadow-sm leading-none">Batal</button>
                <button type="submit" class="flex-1 py-4 rounded-2xl bg-red-500 text-white font-black text-[10px] uppercase shadow-xl leading-none">Ya, Hapus</button>
            </form>
        </div>
    </div>

</body>
</html>