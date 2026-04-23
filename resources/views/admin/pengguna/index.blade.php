<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Database Pengguna - Librify Admin</title>

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
        
        /* Animasi Toast Notifikasi */
        @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        .toast-animate { animation: slideInRight 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    </style>
</head>

<body class="antialiased flex h-screen w-full overflow-hidden text-left font-jakarta" 
      x-data="{ 
        tab: 'staff',
        searchQuery: '',
        sidebarOpen: false, 
        
        modalTambah: false, 
        modalEdit: false, 
        modalHapus: false,
        modalDetail: false,
        modalImport: false,
        
        showPwTambah: false,
        showPwEdit: false,
        photoPreview: null,
        importFileName: '', 
        
        selectedUser: { 
            id: '', name: '', username: '', email: '', role: '', status: '',
            nisn: '', profile_photo_url: '', 
            class_id: '', 
            chairman_name: '', vice_chairman_name: '', 
            class_room: {} 
        },
        selectedUserStudents: [], 
        formRole: 'class', 
        
        deleteRoute: '',
        editRoute: '',
        userNameToDelete: '',
      }">

    {{-- ✅ FIXED NOTIFICATIONS --}}
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

    {{-- Sidebar Admin --}}
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'" class="fixed inset-y-0 left-0 z-50 flex flex-col w-72 bg-[#0F172A] text-white border-r border-slate-800 md:static md:flex-shrink-0 h-full transition-transform duration-300">
        @include('admin.partials.sidebar')
    </aside>

    <div class="flex flex-1 flex-col h-full min-w-0 overflow-hidden text-left">
        @include('admin.partials.header')

        <main class="flex-1 overflow-y-auto p-6 lg:p-10 pt-2 custom-scroll text-left leading-none">
            <div class="mx-auto w-full max-w-[1600px] space-y-8">
                
                {{-- 1. HEADER SECTION --}}
                <div class="flex flex-col xl:flex-row xl:items-end justify-between gap-6 leading-none">
                    <div class="text-left leading-none">
                        <h2 class="text-4xl font-black text-slate-900 tracking-tight uppercase">Database Otoritas</h2>
                        <div class="flex flex-wrap bg-slate-200/50 p-1.5 rounded-2xl gap-1 mt-6 w-fit border border-slate-100 shadow-inner leading-none">
                            <button @click="tab = 'staff'; searchQuery = ''" :class="tab === 'staff' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500'" class="px-6 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all leading-none">Staf & Petugas</button>
                            <button @click="tab = 'kelas'; searchQuery = ''" :class="tab === 'kelas' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500'" class="px-6 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all leading-none">Database Kelas</button>
                        </div>
                    </div>
                    
                    <div class="flex flex-col md:flex-row items-center gap-4 w-full xl:w-auto leading-none">
                        <div class="relative w-full md:w-80 leading-none">
                            <i class="bi bi-search absolute left-5 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="text" x-model="searchQuery" placeholder="Cari nama atau ID login..." class="w-full pl-12 pr-6 py-4 bg-white border border-gray-100 rounded-2xl outline-none font-bold text-xs shadow-sm focus:ring-4 focus:ring-indigo-500/10">
                        </div>
                        <button @click="modalTambah = true; photoPreview = null; formRole = tab === 'staff' ? 'staff' : 'class';" class="w-full md:w-auto inline-flex items-center justify-center gap-3 rounded-2xl bg-indigo-600 px-8 py-4 text-xs font-black text-white shadow-xl hover:bg-indigo-700 active:scale-95 uppercase tracking-widest transition-all leading-none">
                            <i class="bi bi-person-plus-fill text-lg"></i>
                            <span x-text="tab === 'staff' ? 'Tambah Staf' : 'Registrasi Kelas'"></span>
                        </button>
                    </div>
                </div>

                {{-- 2. ANALYTICS CARDS --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 leading-none">
                    <div class="bg-white p-6 rounded-[2.5rem] border border-gray-100 shadow-sm flex items-center gap-5 border-l-4 border-l-indigo-500"><div class="w-14 h-14 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center text-2xl shadow-inner leading-none"><i class="bi bi-people"></i></div><div class="leading-none text-left"><p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 leading-none">Total Akun</p><p class="text-2xl font-black text-gray-900 leading-none">{{ $users->count() }}</p></div></div>
                    <div class="bg-white p-6 rounded-[2.5rem] border border-gray-100 shadow-sm flex items-center gap-5 border-l-4 border-l-emerald-500"><div class="w-14 h-14 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center text-2xl shadow-inner leading-none"><i class="bi bi-person-badge"></i></div><div class="leading-none text-left"><p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 leading-none">Staf & Guru</p><p class="text-2xl font-black text-gray-900 leading-none">{{ $users->whereIn('role', ['staff', 'teacher'])->count() }}</p></div></div>
                    <div class="bg-white p-6 rounded-[2.5rem] border border-gray-100 shadow-sm flex items-center gap-5 border-l-4 border-l-blue-500"><div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center text-2xl shadow-inner leading-none"><i class="bi bi-building"></i></div><div class="leading-none text-left"><p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 leading-none">Akun Kelas</p><p class="text-2xl font-black text-gray-900 leading-none">{{ $users->where('role', 'class')->count() }}</p></div></div>
                    <div class="bg-white p-6 rounded-[2.5rem] border border-gray-100 shadow-sm flex items-center gap-5 border-l-4 border-l-cyan-500"><div class="w-14 h-14 bg-cyan-50 text-cyan-600 rounded-2xl flex items-center justify-center text-2xl shadow-inner leading-none"><i class="bi bi-mortarboard-fill"></i></div><div class="leading-none text-left"><p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 leading-none">Siswa Terdaftar</p><p class="text-2xl font-black text-gray-900 leading-none">{{ $users->where('role', 'student')->count() }}</p></div></div>
                </div>

                {{-- 3. TABLES SECTION --}}
                <div class="rounded-[3rem] bg-white shadow-sm border border-gray-100 overflow-hidden text-left leading-none">
                    
                    {{-- TAB 1: STAFF (ADMIN, STAFF, TEACHER) --}}
                    <div x-show="tab === 'staff'" x-transition>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm leading-none">
                                <thead>
                                    <tr class="bg-gray-50/50 text-[10px] uppercase tracking-[0.2em] text-gray-400 font-black">
                                        <th class="px-8 py-6">Profil Staf</th>
                                        <th class="px-8 py-6">Informasi Kontak</th>
                                        <th class="px-8 py-6">Level & Status</th>
                                        <th class="px-8 py-6 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @foreach($users->whereIn('role', ['admin', 'staff', 'teacher']) as $user)
                                    <tr class="group hover:bg-gray-50/50 transition-all leading-none text-left" x-show="searchQuery === '' || '{{ strtolower($user->name) }}'.includes(searchQuery.toLowerCase())">
                                        <td class="px-8 py-5">
                                            <div class="flex items-center gap-4 text-left leading-none">
                                                <img src="{{ $user->profile_photo_url }}" class="h-11 w-11 rounded-2xl object-cover shadow-sm border border-gray-100">
                                                <div class="text-left leading-none">
                                                    <p class="text-gray-900 font-black text-base uppercase mb-1 leading-none">{{ $user->name }}</p>
                                                    <p class="text-[10px] text-gray-400 font-bold uppercase leading-none tracking-widest">ID: {{ $user->username ?? '-' }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-8 py-5 text-left leading-none">
                                            <p class="text-[11px] font-black text-gray-800 leading-none mb-1">{{ $user->email }}</p>
                                        </td>
                                        <td class="px-8 py-5 text-left leading-none align-top">
                                            <div class="flex flex-col items-start gap-2">
                                                <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-[9px] font-black uppercase border 
                                                    {{ $user->role === 'admin' ? 'bg-purple-50 text-purple-600 border-purple-100' : 
                                                      ($user->role === 'staff' ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-blue-50 text-blue-600 border-blue-100') }}">
                                                    {{ $user->role }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-8 py-5 text-center leading-none">
                                            <div class="flex items-center justify-center gap-2">
                                                <button data-user="{{ json_encode($user) }}" @click="selectedUser = JSON.parse($el.dataset.user); modalDetail = true" class="w-10 h-10 rounded-xl bg-gray-50 text-gray-400 hover:bg-indigo-600 hover:text-white transition-all flex items-center justify-center shadow-sm"><i class="bi bi-eye-fill"></i></button>
                                                <button data-user="{{ json_encode($user) }}" @click="selectedUser = JSON.parse($el.dataset.user); photoPreview = null; editRoute = '{{ route('admin.pengguna.update', $user->id) }}'; formRole = '{{ $user->role }}'; modalEdit = true" class="w-10 h-10 rounded-xl bg-gray-50 text-gray-400 hover:bg-blue-600 hover:text-white transition-all flex items-center justify-center shadow-sm"><i class="bi bi-pencil-square"></i></button>
                                                <button data-name="{{ $user->name }}" @click="modalHapus = true; deleteRoute = '{{ route('admin.pengguna.destroy', $user->id) }}'; userNameToDelete = $el.dataset.name" class="w-10 h-10 rounded-xl bg-gray-50 text-gray-400 hover:bg-red-600 hover:text-white transition-all flex items-center justify-center shadow-sm"><i class="bi bi-trash3-fill"></i></button>
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
                                        <th class="px-8 py-6">Status Akun</th>
                                        <th class="px-8 py-6">Perangkat Kelas</th>
                                        <th class="px-8 py-6 text-center">Manajemen Siswa & Opsi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50 leading-tight">
                                    @foreach($users->where('role', 'class') as $user)
                                    <tr class="group hover:bg-gray-50/50 transition-all leading-none text-left" x-show="searchQuery === '' || '{{ strtolower($user->name) }}'.includes(searchQuery.toLowerCase())">
                                        <td class="px-8 py-5">
                                            <div class="flex items-center gap-4 text-left leading-none">
                                                <img src="{{ $user->profile_photo_url }}" class="h-11 w-11 rounded-2xl object-cover shadow-sm border border-gray-100">
                                                <div class="text-left leading-none">
                                                    <p class="text-gray-900 font-black text-base uppercase mb-1 leading-none">{{ $user->name }}</p>
                                                    <p class="text-[10px] text-gray-400 font-bold uppercase leading-none">{{ $user->email }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-8 py-5">
                                            <span class="inline-block px-3 py-1.5 rounded-xl text-[10px] font-black uppercase text-indigo-600 bg-indigo-50 border border-indigo-100 leading-none">
                                                {{ $user->classRoom->name ?? 'Belum Terhubung' }}
                                            </span>
                                        </td>
                                        <td class="px-8 py-5 text-left leading-none">
                                            <div class="space-y-1 leading-none">
                                                <p class="text-[11px] font-black text-gray-700 leading-none uppercase">K: {{ $user->chairman_name ?: '-' }}</p>
                                                <p class="text-[9px] font-bold text-gray-400 leading-none uppercase">W: {{ $user->vice_chairman_name ?: '-' }}</p>
                                            </div>
                                        </td>
                                        <td class="px-8 py-5 text-center leading-none">
                                            <div class="flex items-center justify-center gap-2">
                                                <button data-user="{{ json_encode($user->load('classRoom')) }}" 
                                                        data-students="{{ json_encode($users->where('role', 'student')->where('class_id', $user->class_id)->values()) }}"
                                                        @click="selectedUser = JSON.parse($el.dataset.user); selectedUserStudents = JSON.parse($el.dataset.students); modalDetail = true;" 
                                                        class="px-4 h-10 rounded-xl bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white transition-all flex items-center justify-center shadow-sm gap-2 font-black text-[9px] uppercase tracking-widest" title="Kelola Siswa di Kelas Ini">
                                                    <i class="bi bi-people-fill text-sm"></i> Kelola Siswa
                                                </button>
                                                
                                                <button data-user="{{ json_encode($user) }}" @click="selectedUser = JSON.parse($el.dataset.user); photoPreview = null; editRoute = '{{ route('admin.pengguna.update', $user->id) }}'; formRole = '{{ $user->role }}'; modalEdit = true" class="w-10 h-10 rounded-xl bg-gray-50 text-gray-400 hover:bg-blue-600 hover:text-white transition-all flex items-center justify-center shadow-sm"><i class="bi bi-pencil-square"></i></button>
                                                <button data-name="{{ $user->name }}" @click="modalHapus = true; deleteRoute = '{{ route('admin.pengguna.destroy', $user->id) }}'; userNameToDelete = $el.dataset.name" class="w-10 h-10 rounded-xl bg-gray-50 text-gray-400 hover:bg-red-600 hover:text-white transition-all flex items-center justify-center shadow-sm"><i class="bi bi-trash3-fill"></i></button>
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

    {{-- ✅ MODAL TAMBAH (CREATE) --}}
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

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-4">Pilih Level Akses</label>
                    <input type="hidden" name="role" :value="formRole">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <button type="button" @click="formRole = 'class'" :class="formRole === 'class' ? 'bg-indigo-600 text-white shadow-lg' : 'bg-gray-50 text-gray-400'" class="py-4 rounded-2xl text-[10px] font-black uppercase transition-all">Akun Kelas</button>
                        <button type="button" @click="formRole = 'student'" :class="formRole === 'student' ? 'bg-cyan-600 text-white shadow-lg' : 'bg-gray-50 text-gray-400'" class="py-4 rounded-2xl text-[10px] font-black uppercase transition-all">Siswa Pribadi</button>
                        <button type="button" @click="formRole = 'staff'" :class="formRole === 'staff' ? 'bg-emerald-600 text-white shadow-lg' : 'bg-gray-50 text-gray-400'" class="py-4 rounded-2xl text-[10px] font-black uppercase transition-all">Petugas</button>
                        <button type="button" @click="formRole = 'admin'" :class="formRole === 'admin' ? 'bg-purple-600 text-white shadow-lg' : 'bg-gray-50 text-gray-400'" class="py-4 rounded-2xl text-[10px] font-black uppercase transition-all">Admin</button>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-5">
                    <div x-show="formRole !== 'class'">
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-3">Nama Lengkap</label>
                        <input type="text" name="name" x-model="selectedUser.name" class="w-full px-6 py-5 bg-gray-50 border rounded-2xl outline-none font-bold text-sm" :disabled="formRole === 'class'" :required="formRole !== 'class'">
                    </div>
                    
                    <div x-show="formRole === 'class'">
                        <label class="block text-[10px] font-black text-indigo-500 uppercase mb-3">Pilih Kelas (Nama Akun)</label>
                        {{-- Menggunakan :disabled agar input ini diabaikan browser jika role bukan class --}}
                        <select name="class_id" x-model="selectedUser.class_id" @change="selectedUser.name = $event.target.options[$event.target.selectedIndex].text" class="w-full px-6 py-5 bg-indigo-50 border border-indigo-200 rounded-2xl font-bold text-sm outline-none cursor-pointer" :disabled="formRole !== 'class'" :required="formRole === 'class'">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($classes as $kelas) <option value="{{ $kelas->id }}">{{ $kelas->name }}</option> @endforeach
                        </select>
                        <input type="hidden" name="name" :value="selectedUser.name" :disabled="formRole !== 'class'">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-3">ID Login (Username)</label>
                        <input type="text" name="username" x-model="selectedUser.username" required class="w-full px-6 py-5 bg-gray-50 border border-gray-300 focus:border-indigo-500 rounded-2xl outline-none font-bold text-sm" placeholder="Ketik Username...">
                    </div>
                </div>
                
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-3">Email Sistem</label>
                    <input type="email" name="email" required class="w-full px-6 py-5 bg-gray-50 border rounded-2xl outline-none font-bold text-sm">
                </div>
                
                <div x-show="formRole === 'student'" class="p-8 bg-cyan-50/50 rounded-[2.5rem] border border-cyan-100 space-y-6 text-left transition-all">
                    <div>
                        <label class="block text-[10px] font-black text-cyan-600 uppercase tracking-widest mb-3 ml-1">NISN Siswa</label>
                        <input type="number" name="nisn" placeholder="Contoh: 00548xxxx" class="w-full px-6 py-5 bg-white border border-cyan-200 rounded-2xl font-bold text-sm outline-none" :disabled="formRole !== 'student'">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-cyan-600 uppercase tracking-widest mb-3 ml-1">Masukkan Ke Kelas</label>
                        <select name="class_id" x-model="selectedUser.class_id" class="w-full px-6 py-5 bg-white border border-cyan-200 rounded-2xl font-bold text-sm outline-none appearance-none cursor-pointer" :disabled="formRole !== 'student'" :required="formRole === 'student'">
                            <option value="">-- Pilih Kelas Siswa --</option>
                            @if(isset($classes)) @foreach($classes as $kelas) <option value="{{ $kelas->id }}">{{ $kelas->name }}</option> @endforeach @endif
                        </select>
                    </div>
                </div>

                <div x-show="formRole === 'class'" class="p-8 bg-indigo-50/50 rounded-[2.5rem] border border-indigo-100 space-y-6 text-left transition-all">
                    <div class="bg-yellow-50 p-4 rounded-xl border border-yellow-100 mb-2"><p class="text-[9px] font-bold text-yellow-600 uppercase">Catatan:</p><p class="text-[9px] text-yellow-700 mt-1">Nama akun otomatis mengikuti kelas yang dipilih diatas.</p></div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block text-[10px] font-black text-indigo-400 uppercase mb-3 leading-none">Ketua Kelas</label><input type="text" name="chairman_name" class="w-full px-5 py-4 border border-indigo-100 rounded-2xl text-sm font-bold shadow-sm" :disabled="formRole !== 'class'"></div>
                        <div><label class="block text-[10px] font-black text-indigo-400 uppercase mb-3 leading-none">Wakil Ketua</label><input type="text" name="vice_chairman_name" class="w-full px-5 py-4 border border-indigo-100 rounded-2xl text-sm font-bold shadow-sm" :disabled="formRole !== 'class'"></div>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-3">Password</label>
                    <div class="relative"><input :type="showPwTambah ? 'text' : 'password'" name="password" required class="w-full px-6 py-5 bg-gray-50 border rounded-2xl font-bold pr-14 outline-none"><button type="button" @click="showPwTambah = !showPwTambah" class="absolute right-5 top-1/2 -translate-y-1/2 text-gray-400"><i class="bi" :class="showPwTambah ? 'bi-eye-slash-fill' : 'bi-eye-fill'"></i></button></div>
                </div>
                <div class="pt-4 flex gap-4"><button type="button" @click="modalTambah = false" class="flex-1 px-6 py-5 rounded-2xl bg-gray-100 text-slate-500 font-black text-[10px] uppercase tracking-widest transition-all leading-none">Batal</button><button type="submit" class="flex-1 px-6 py-5 rounded-2xl bg-indigo-600 text-white font-black text-[10px] uppercase shadow-xl transition-all leading-none hover:bg-indigo-700 active:scale-95">Simpan</button></div>
            </form>
        </div>
    </div>

    {{-- ✅ MODAL EDIT --}}
    <div x-show="modalEdit" x-cloak class="fixed inset-0 z-[130] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="modalEdit = false"></div>
        <div x-show="modalEdit" x-transition.scale.95 class="relative w-full max-w-2xl bg-white rounded-[3rem] shadow-2xl p-10 border border-white text-left overflow-y-auto max-h-[90vh] custom-scroll leading-none">
            <h3 class="text-3xl font-black text-gray-900 font-jakarta mb-8 uppercase leading-none tracking-tight">Perbarui Akun</h3>
            <form :action="editRoute" method="POST" enctype="multipart/form-data" class="space-y-6 text-left leading-none">
                @csrf @method('PUT')
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
                    <div x-show="formRole !== 'class'">
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 ml-1">Identitas Nama</label>
                        <input type="text" name="name" x-model="selectedUser.name" class="w-full px-6 py-5 bg-gray-50 border rounded-2xl outline-none font-bold text-sm" :disabled="formRole === 'class'" :required="formRole !== 'class'">
                    </div>
                    
                    <div x-show="formRole === 'class'">
                        <label class="block text-[10px] font-black text-indigo-500 uppercase mb-3 ml-1">Pilih Kelas (Nama Akun)</label>
                        <select name="class_id" x-model="selectedUser.class_id" @change="selectedUser.name = $event.target.options[$event.target.selectedIndex].text" class="w-full px-6 py-5 bg-indigo-50 border border-indigo-200 rounded-2xl font-bold text-sm outline-none cursor-pointer" :disabled="formRole !== 'class'" :required="formRole === 'class'">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($classes as $kelas) <option value="{{ $kelas->id }}">{{ $kelas->name }}</option> @endforeach
                        </select>
                        <input type="hidden" name="name" :value="selectedUser.name" :disabled="formRole !== 'class'">
                    </div>

                    <div><label class="block text-[10px] font-black text-gray-400 uppercase mb-3 ml-1">Username Login</label><input type="text" name="username" x-model="selectedUser.username" required class="w-full px-6 py-5 bg-gray-50 border border-gray-300 focus:border-indigo-500 rounded-2xl outline-none font-bold text-sm"></div>
                </div>
                <div><label class="block text-[10px] font-black text-gray-400 uppercase mb-3 ml-1">Email Sistem</label><input type="email" name="email" x-model="selectedUser.email" required class="w-full px-6 py-5 bg-gray-50 border rounded-2xl outline-none font-bold text-sm"></div>

                <div x-show="formRole === 'student'" class="p-8 bg-cyan-50 rounded-[2.5rem] border border-cyan-200 space-y-6 text-left leading-none transition-all">
                    <div>
                        <label class="block text-[10px] font-black text-cyan-600 uppercase tracking-widest mb-3 ml-1">NISN Siswa</label>
                        <input type="number" name="nisn" x-model="selectedUser.nisn" class="w-full px-6 py-5 bg-white border border-cyan-200 rounded-2xl font-bold text-sm outline-none" :disabled="formRole !== 'student'">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-cyan-600 uppercase tracking-widest mb-3 ml-1">Pindah Kelas</label>
                        <select name="class_id" x-model="selectedUser.class_id" class="w-full px-6 py-5 bg-white border border-cyan-200 rounded-2xl font-bold text-sm outline-none appearance-none" :disabled="formRole !== 'student'" :required="formRole === 'student'">
                            <option value="">-- Pilih Kelas --</option>
                            @if(isset($classes)) @foreach($classes as $kelas) <option value="{{ $kelas->id }}">{{ $kelas->name }}</option> @endforeach @endif
                        </select>
                    </div>
                </div>

                <div x-show="formRole === 'class'" class="p-8 bg-indigo-50 rounded-[2.5rem] border border-indigo-200 space-y-6 text-left leading-none transition-all">
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block text-[10px] font-black text-indigo-400 uppercase mb-3 ml-1">Nama Ketua Kelas</label><input type="text" name="chairman_name" x-model="selectedUser.chairman_name" class="w-full px-5 py-4 border border-indigo-100 rounded-2xl text-sm font-bold shadow-sm outline-none" :disabled="formRole !== 'class'"></div>
                        <div><label class="block text-[10px] font-black text-indigo-400 uppercase mb-3 ml-1">Nama Wakil Ketua</label><input type="text" name="vice_chairman_name" x-model="selectedUser.vice_chairman_name" class="w-full px-5 py-4 border border-indigo-100 rounded-2xl text-sm font-bold shadow-sm outline-none" :disabled="formRole !== 'class'"></div>
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


    {{-- ✅ MODAL DETAIL UTAMA (DENGAN TABEL SISWA) ✅ --}}
    <div x-show="modalDetail" x-cloak class="fixed inset-0 z-[120] flex items-center justify-center p-4 text-left">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-md" @click="modalDetail = false"></div>
        <div x-show="modalDetail" x-transition.scale.95 class="relative w-full bg-white rounded-[3rem] shadow-2xl overflow-y-auto max-h-[90vh] custom-scroll border border-white leading-none" :class="selectedUser.role === 'class' ? 'max-w-4xl' : 'max-w-md'">
            
            <div class="relative h-32 w-full overflow-hidden" :class="{'bg-purple-600': selectedUser.role === 'admin','bg-emerald-600': selectedUser.role === 'staff','bg-indigo-600': selectedUser.role === 'class', 'bg-cyan-600': selectedUser.role === 'student'}">
                 <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 16px 16px;"></div>
            </div>

            <div class="px-10 pb-10 relative">
                <div class="flex flex-col items-center -mt-16 mb-6">
                    <img :src="selectedUser.profile_photo_url" class="w-32 h-32 rounded-[2.5rem] object-cover shadow-2xl border-[6px] border-white bg-white">
                    <div class="mt-4 text-center">
                        <h3 class="text-2xl font-black text-gray-900 font-jakarta uppercase tracking-tight leading-tight" x-text="selectedUser.name"></h3>
                        <div class="mt-2"><span class="inline-flex items-center px-4 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest border shadow-sm" :class="{'bg-purple-50 text-purple-600 border-purple-100': selectedUser.role === 'admin','bg-emerald-50 text-emerald-600 border-emerald-100': selectedUser.role === 'staff','bg-indigo-50 text-indigo-600 border-indigo-100': selectedUser.role === 'class', 'bg-cyan-50 text-cyan-600 border-cyan-100': selectedUser.role === 'student'}" x-text="selectedUser.role === 'class' ? 'AKUN KELAS' : selectedUser.role"></span></div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100"><p class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1">Username ID</p><p class="text-xs font-black text-gray-800" x-text="selectedUser.username || '-'"></p></div>
                        <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100"><p class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1">Email</p><p class="text-xs font-black text-gray-800 break-all" x-text="selectedUser.email"></p></div>
                    </div>

                    <template x-if="selectedUser.role === 'class'">
                        <div>
                            <div class="p-6 bg-indigo-50/50 rounded-3xl border border-indigo-100 space-y-4">
                                <div class="grid grid-cols-2 gap-4 border-indigo-100">
                                    <div><p class="text-[8px] font-black text-indigo-400 uppercase mb-1">Ketua Kelas</p><p class="text-xs font-bold text-gray-700 uppercase" x-text="selectedUser.chairman_name || '-'"></p></div>
                                    <div><p class="text-[8px] font-black text-indigo-400 uppercase mb-1">Wakil Ketua</p><p class="text-xs font-bold text-gray-700 uppercase" x-text="selectedUser.vice_chairman_name || '-'"></p></div>
                                </div>
                            </div>

                            {{-- TABEL SISWA DI DALAM KELAS --}}
                            <div class="mt-8 border-t border-gray-100 pt-8">
                                <div class="flex justify-between items-center mb-6">
                                    <div><h4 class="text-lg font-black text-gray-900 uppercase tracking-tight">Manajemen Siswa</h4><p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Siswa yang terdaftar di kelas ini</p></div>
                                    <div class="flex gap-2">
                                        <button @click="modalDetail = false; setTimeout(() => { importFileName = ''; modalImport = true }, 300)" class="px-5 py-3 bg-emerald-50 text-emerald-600 rounded-xl text-[9px] font-black uppercase tracking-widest border border-emerald-100 hover:bg-emerald-600 hover:text-white transition-all shadow-sm flex items-center gap-2"><i class="bi bi-file-earmark-excel-fill text-sm"></i> Import Excel</button>
                                        <button @click="modalDetail = false; setTimeout(() => { modalTambah = true; formRole = 'student'; selectedUser.class_id = selectedUser.class_id; }, 300)" class="px-5 py-3 bg-indigo-600 text-white rounded-xl text-[9px] font-black uppercase tracking-widest shadow-sm hover:bg-indigo-700 transition-all flex items-center gap-2"><i class="bi bi-person-plus-fill text-sm"></i> Tambah Siswa</button>
                                    </div>
                                </div>

                                <div class="bg-white rounded-3xl border border-gray-100 overflow-hidden shadow-sm">
                                    <table class="w-full text-left text-sm">
                                        <thead class="bg-gray-50/80 text-[10px] font-black text-gray-400 uppercase tracking-widest"><tr><th class="px-6 py-5">Nama Siswa</th><th class="px-6 py-5">NISN & Info</th><th class="px-6 py-5 text-center">Aksi</th></tr></thead>
                                        <tbody class="divide-y divide-gray-50">
                                            <template x-if="selectedUserStudents.length === 0"><tr><td colspan="3" class="px-6 py-12 text-center text-gray-400 font-bold text-[10px] uppercase tracking-widest italic">Belum ada data siswa di kelas ini.</td></tr></template>
                                            <template x-for="student in selectedUserStudents" :key="student.id">
                                                <tr class="hover:bg-gray-50/50 transition-all">
                                                    <td class="px-6 py-4"><div class="flex items-center gap-3"><div class="w-8 h-8 rounded-full bg-cyan-100 text-cyan-600 flex items-center justify-center font-bold text-xs"><i class="bi bi-person-fill"></i></div><span class="font-black text-gray-800 uppercase text-xs" x-text="student.name"></span></div></td>
                                                    <td class="px-6 py-4"><p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider" x-text="'NISN: ' + (student.nisn || '-')"></p><p class="text-[9px] text-gray-400 mt-1" x-text="student.email"></p></td>
                                                    <td class="px-6 py-4 text-center">
                                                        <div class="flex justify-center gap-2">
                                                            <button @click="modalDetail = false; setTimeout(() => { selectedUser = student; editRoute = '/admin/pengguna/' + student.id; formRole = 'student'; photoPreview = null; modalEdit = true; }, 300)" class="w-8 h-8 rounded-lg bg-blue-50 text-blue-500 hover:bg-blue-600 hover:text-white transition-all flex items-center justify-center"><i class="bi bi-pencil-square"></i></button>
                                                            <button @click="modalDetail = false; setTimeout(() => { modalHapus = true; deleteRoute = '/admin/pengguna/' + student.id; userNameToDelete = student.name; }, 300)" class="w-8 h-8 rounded-lg bg-red-50 text-red-500 hover:bg-red-600 hover:text-white transition-all flex items-center justify-center"><i class="bi bi-trash3-fill"></i></button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </template>

                    <template x-if="selectedUser.role === 'admin'">
                        <div class="p-5 bg-purple-50/50 rounded-3xl border border-purple-100 text-center"><p class="text-[10px] font-black text-purple-600 uppercase tracking-widest"><i class="bi bi-patch-check-fill me-1"></i> Super Administrator</p><p class="text-[9px] text-purple-400 font-bold mt-1">Memiliki akses penuh ke sistem Librify.</p></div>
                    </template>
                    
                    <template x-if="selectedUser.role === 'staff'">
                        <div class="p-5 bg-emerald-50/50 rounded-3xl border border-emerald-100 text-center"><p class="text-[10px] font-black text-emerald-600 uppercase tracking-widest"><i class="bi bi-shield-lock-fill me-1"></i> Petugas Perpustakaan</p><p class="text-[9px] text-emerald-500 font-bold mt-1">Bertugas mengelola data sirkulasi dan inventaris buku.</p></div>
                    </template>

                    <button @click="modalDetail = false" class="w-full py-4 bg-slate-900 text-white rounded-2xl font-black text-[10px] uppercase shadow-xl hover:bg-slate-800 hover:shadow-2xl transition-all transform active:scale-95 leading-none tracking-widest mt-6">Tutup Panel</button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL HAPUS --}}
    <div x-show="modalHapus" x-cloak class="fixed inset-0 z-[140] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" @click="modalHapus = false"></div>
        <div x-show="modalHapus" x-transition.scale.95 class="relative w-full max-w-sm bg-white rounded-[3rem] shadow-2xl p-10 text-center border">
            <div class="w-20 h-20 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner"><i class="bi bi-trash3-fill text-3xl"></i></div>
            <h3 class="text-2xl font-black text-gray-900 mb-2 uppercase tracking-tight">Hapus Akun?</h3>
            <p class="text-sm text-gray-500 mb-10 leading-relaxed font-medium">Akun <span class="font-black text-gray-900 uppercase" x-text="userNameToDelete"></span> akan dihapus secara permanen.</p>
            <form :action="deleteRoute" method="POST" class="flex gap-4">
                @csrf @method('DELETE')
                <button type="button" @click="modalHapus = false" class="flex-1 py-4 rounded-2xl bg-gray-100 text-slate-500 font-black text-[10px] uppercase shadow-sm leading-none hover:bg-gray-200">Batal</button>
                <button type="submit" class="flex-1 py-4 rounded-2xl bg-red-500 text-white font-black text-[10px] uppercase shadow-xl leading-none hover:bg-red-600">Ya, Hapus</button>
            </form>
        </div>
    </div>

    {{-- ✅ MODAL IMPORT EXCEL --}}
    <div x-show="modalImport" x-cloak class="fixed inset-0 z-[130] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="modalImport = false"></div>
        <div x-show="modalImport" x-transition.scale.95 class="relative w-full max-w-md bg-white rounded-[3rem] shadow-2xl p-10 border border-white text-center leading-none">
            <div class="w-20 h-20 bg-emerald-50 text-emerald-600 rounded-[1.5rem] flex items-center justify-center mx-auto mb-6 shadow-inner">
                <i class="bi bi-file-earmark-spreadsheet-fill text-4xl"></i>
            </div>
            <h3 class="text-2xl font-black text-gray-900 mb-2 uppercase tracking-tight">Import Data Siswa</h3>
            <p class="text-[10px] text-gray-500 mb-8 leading-relaxed font-medium uppercase tracking-widest">Kelas: <span class="font-black text-indigo-600" x-text="selectedUser.name"></span></p>
            
            <form action="{{ route('admin.pengguna.import') ?? '#' }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                <input type="hidden" name="class_id" x-model="selectedUser.class_id">
                
                {{-- AREA UPLOAD --}}
                <div class="border-2 border-dashed border-emerald-200 rounded-[2rem] p-8 hover:bg-emerald-50 transition-colors relative cursor-pointer group">
                    <input type="file" name="excel_file" accept=".xlsx, .xls, .csv" required 
                           @change="importFileName = $event.target.files[0] ? $event.target.files[0].name : ''"
                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                    
                    <template x-if="!importFileName">
                        <div>
                            <i class="bi bi-cloud-arrow-up-fill text-3xl text-emerald-300 group-hover:text-emerald-500 mb-3 block transition-colors"></i>
                            <p class="text-xs font-black text-gray-700">Pilih File Excel (.csv/.xlsx)</p>
                            <p class="text-[9px] font-bold text-gray-400 mt-2">Pastikan format kolom sesuai template.</p>
                        </div>
                    </template>

                    <template x-if="importFileName">
                        <div class="animate-pulse">
                            <i class="bi bi-file-earmark-check-fill text-3xl text-emerald-500 mb-3 block"></i>
                            <p class="text-xs font-black text-emerald-700 break-all px-4" x-text="importFileName"></p>
                            <p class="text-[9px] font-bold text-emerald-500 mt-2 tracking-widest uppercase">File Siap Di-Import</p>
                        </div>
                    </template>
                </div>
                
                <div class="flex gap-4 pt-2">
                    <button type="button" @click="modalImport = false; setTimeout(() => { modalDetail = true }, 300)" class="flex-1 py-4 rounded-2xl bg-gray-100 text-slate-500 font-black text-[10px] uppercase shadow-sm leading-none tracking-widest hover:bg-gray-200">Kembali</button>
                    <button type="submit" class="flex-1 py-4 rounded-2xl bg-emerald-600 text-white font-black text-[10px] uppercase shadow-xl hover:bg-emerald-700 active:scale-95 leading-none tracking-widest transition-all">Mulai Import</button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>