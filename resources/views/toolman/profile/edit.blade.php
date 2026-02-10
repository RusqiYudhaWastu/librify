<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profil Petugas - TekniLog</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
        .custom-scroll::-webkit-scrollbar { width: 5px; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #10b981; border-radius: 20px; }
    </style>
</head>

<body class="antialiased flex h-screen w-full overflow-hidden text-left" x-data="{ sidebarOpen: false }">

    {{-- Sidebar Toolman --}}
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'" 
           class="fixed inset-y-0 left-0 z-50 flex flex-col w-72 bg-[#0F172A] text-white border-r border-slate-800 md:static md:flex-shrink-0 h-full transition-transform duration-300">
        @include('toolman.partials.sidebar') 
    </aside>

    <div class="flex flex-1 flex-col h-full min-w-0 overflow-hidden">
        {{-- Header Toolman --}}
        @include('toolman.partials.header')

        <main class="flex-1 overflow-y-auto p-6 lg:p-10 pt-2 custom-scroll text-left leading-none">
            {{-- Container Max Width 1600px agar layout split terlihat bagus --}}
            <div class="mx-auto w-full max-w-[1600px] space-y-8">
                
                {{-- HEADER PAGE --}}
                <div class="text-left leading-none">
                    <h2 class="text-4xl font-black text-slate-900 tracking-tight uppercase leading-none">Profil Petugas</h2>
                    <p class="text-sm font-bold text-emerald-600 mt-3 uppercase tracking-widest leading-none border-l-4 border-emerald-500 pl-4">Kelola informasi akun & pantau wilayah tugas.</p>
                </div>

                {{-- Alert Sukses --}}
                @if (session('status') === 'profile-updated' || session('status') === 'password-updated')
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" class="bg-emerald-500 text-white p-5 rounded-3xl shadow-lg font-bold text-xs uppercase tracking-[0.1em]">
                        <i class="bi bi-check-circle-fill me-2 text-base"></i> Perubahan Berhasil Disimpan!
                    </div>
                @endif

                {{-- GRID LAYOUT: BERSEBELAHAN (Profil & Password) --}}
                <div class="grid grid-cols-1 xl:grid-cols-2 gap-8 items-start">
                    
                    {{-- ========================================== --}}
                    {{-- KOLOM KIRI: INFORMASI PROFIL & FOTO --}}
                    {{-- ========================================== --}}
                    <div class="bg-white p-8 lg:p-10 rounded-[3rem] border border-gray-100 shadow-sm leading-none h-full">
                        <div class="flex items-center gap-4 mb-8">
                            <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center text-xl shadow-inner"><i class="bi bi-person-gear"></i></div>
                            <h3 class="text-xl font-black text-slate-900 uppercase tracking-tight">Informasi Dasar</h3>
                        </div>
                        
                        <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-8" x-data="{ photoPreview: null }">
                            @csrf @method('patch')

                            {{-- UI Upload Foto (Emerald Theme) --}}
                            <div class="flex flex-col sm:flex-row items-center gap-6 p-6 bg-emerald-50/30 rounded-[2.5rem] border border-dashed border-emerald-100">
                                <div class="relative group flex-shrink-0">
                                    {{-- Preview Circle --}}
                                    <div class="w-28 h-28 rounded-[2.5rem] overflow-hidden border-4 border-white shadow-xl bg-white">
                                        <template x-if="!photoPreview">
                                            <img src="{{ $user->profile_photo_url }}" class="w-full h-full object-cover">
                                        </template>
                                        <template x-if="photoPreview">
                                            <img :src="photoPreview" class="w-full h-full object-cover">
                                        </template>
                                    </div>
                                    {{-- Hover Overlay --}}
                                    <label for="profile_photo" class="absolute inset-0 flex items-center justify-center bg-emerald-900/60 text-white rounded-[2.5rem] opacity-0 group-hover:opacity-100 transition-all cursor-pointer">
                                        <i class="bi bi-camera-fill text-2xl"></i>
                                    </label>
                                </div>

                                <div class="text-center sm:text-left flex-1 space-y-2">
                                    <h4 class="text-sm font-black text-gray-800 uppercase tracking-wide">Foto Identitas</h4>
                                    <p class="text-[10px] text-gray-400 font-medium leading-relaxed">Format JPG/PNG. Maksimal 2MB.</p>
                                    <input type="file" id="profile_photo" name="profile_photo" class="hidden" accept="image/*" 
                                           @change="const file = $event.target.files[0]; if (file) { const reader = new FileReader(); reader.onload = (e) => { photoPreview = e.target.result; }; reader.readAsDataURL(file); }">
                                    <label for="profile_photo" class="inline-flex px-5 py-2.5 bg-white border border-emerald-100 rounded-xl text-[10px] font-black uppercase text-emerald-600 shadow-sm hover:bg-emerald-50 cursor-pointer transition-all mt-2">Pilih File</label>
                                </div>
                            </div>

                            <div class="space-y-6">
                                <div class="space-y-3">
                                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Nama Lengkap</label>
                                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl font-bold text-sm outline-none focus:ring-4 focus:ring-emerald-500/10 transition-all">
                                </div>
                                <div class="space-y-3">
                                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Email Petugas</label>
                                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl font-bold text-sm outline-none focus:ring-4 focus:ring-emerald-500/10 transition-all">
                                </div>
                            </div>

                            <div class="flex justify-end pt-4 border-t border-gray-50">
                                <button type="submit" class="w-full sm:w-auto bg-emerald-600 text-white px-10 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-emerald-700 transition-all shadow-xl shadow-emerald-100 active:scale-95 flex items-center justify-center gap-2">
                                    <i class="bi bi-floppy-fill"></i> Simpan Profil
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- ========================================== --}}
                    {{-- KOLOM KANAN: KEAMANAN (PASSWORD + TOGGLE) --}}
                    {{-- ========================================== --}}
                    <div class="bg-white p-8 lg:p-10 rounded-[3rem] border border-gray-100 shadow-sm leading-none h-full">
                        <div class="flex items-center gap-4 mb-8">
                            <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-2xl flex items-center justify-center text-xl shadow-inner"><i class="bi bi-shield-lock"></i></div>
                            <h3 class="text-xl font-black text-slate-900 uppercase tracking-tight">Keamanan Akses</h3>
                        </div>
                        
                        <form method="post" action="{{ route('password.update') }}" class="space-y-6">
                            @csrf @method('put')
                            
                            {{-- Input 1: Password Lama --}}
                            <div class="space-y-3" x-data="{ show: false }">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Password Lama</label>
                                <div class="relative">
                                    <input :type="show ? 'text' : 'password'" name="current_password" class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl font-bold text-sm outline-none focus:ring-4 focus:ring-rose-500/10 transition-all pr-12">
                                    <button type="button" @click="show = !show" class="absolute right-5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-rose-500 transition-colors">
                                        <i class="bi text-lg" :class="show ? 'bi-eye-slash-fill' : 'bi-eye-fill'"></i>
                                    </button>
                                </div>
                            </div>

                            {{-- Input 2: Password Baru --}}
                            <div class="space-y-3" x-data="{ show: false }">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Password Baru</label>
                                <div class="relative">
                                    <input :type="show ? 'text' : 'password'" name="password" class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl font-bold text-sm outline-none focus:ring-4 focus:ring-rose-500/10 transition-all pr-12">
                                    <button type="button" @click="show = !show" class="absolute right-5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-rose-500 transition-colors">
                                        <i class="bi text-lg" :class="show ? 'bi-eye-slash-fill' : 'bi-eye-fill'"></i>
                                    </button>
                                </div>
                            </div>

                            {{-- Input 3: Konfirmasi Password --}}
                            <div class="space-y-3" x-data="{ show: false }">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Ulangi Password</label>
                                <div class="relative">
                                    <input :type="show ? 'text' : 'password'" name="password_confirmation" class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl font-bold text-sm outline-none focus:ring-4 focus:ring-rose-500/10 transition-all pr-12">
                                    <button type="button" @click="show = !show" class="absolute right-5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-rose-500 transition-colors">
                                        <i class="bi text-lg" :class="show ? 'bi-eye-slash-fill' : 'bi-eye-fill'"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="flex justify-end pt-4 border-t border-gray-50">
                                <button type="submit" class="w-full sm:w-auto bg-rose-500 text-white px-10 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-rose-600 transition-all shadow-xl shadow-rose-100 active:scale-95 flex items-center justify-center gap-2">
                                    <i class="bi bi-key-fill"></i> Update Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- ========================================== --}}
                {{-- SECTION BAWAH: OTORITAS JURUSAN (KHUSUS TOOLMAN) --}}
                {{-- ========================================== --}}
                <div class="w-full bg-slate-900 text-white p-8 lg:p-12 rounded-[3rem] shadow-2xl relative overflow-hidden border border-slate-800">
                    {{-- Decoration --}}
                    <div class="absolute top-0 right-0 -mt-10 -mr-10 w-64 h-64 bg-emerald-500 rounded-full blur-[100px] opacity-20 pointer-events-none"></div>
                    <div class="absolute bottom-0 left-0 -mb-10 -ml-10 w-64 h-64 bg-blue-500 rounded-full blur-[100px] opacity-10 pointer-events-none"></div>
                    
                    <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-8 mb-8">
                        <div class="flex items-center gap-5">
                            <div class="w-16 h-16 bg-white/10 text-emerald-400 rounded-3xl flex items-center justify-center text-3xl backdrop-blur-sm border border-white/10 shadow-lg">
                                <i class="bi bi-buildings-fill"></i>
                            </div>
                            <div>
                                <h3 class="text-2xl font-black uppercase tracking-tight text-white leading-none">Wilayah Otoritas</h3>
                                <p class="text-xs font-bold text-emerald-400 uppercase tracking-widest mt-2">Daftar Jurusan di Bawah Tanggung Jawab Anda</p>
                            </div>
                        </div>
                        <div class="bg-white/5 px-6 py-3 rounded-2xl border border-white/10 backdrop-blur-sm">
                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Total Wilayah</span>
                            <p class="text-2xl font-black text-emerald-400 leading-none mt-1">{{ $user->assignedDepartments->count() }} <span class="text-sm text-slate-500">Jurusan</span></p>
                        </div>
                    </div>

                    {{-- List Jurusan Grid --}}
                    <div class="relative z-10">
                        @if($user->assignedDepartments->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                @foreach($user->assignedDepartments as $dept)
                                    <div class="flex items-center gap-3 p-5 bg-gradient-to-br from-white/5 to-white/[0.02] rounded-[2rem] border border-white/10 hover:border-emerald-500/50 hover:bg-emerald-500/10 transition-all group cursor-default">
                                        <div class="w-8 h-8 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-sm group-hover:scale-110 transition-transform">
                                            <i class="bi bi-check-lg"></i>
                                        </div>
                                        <span class="text-xs font-black uppercase tracking-wide text-slate-200 group-hover:text-white">{{ $dept->name }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-12 bg-white/5 rounded-[3rem] border border-dashed border-slate-700">
                                <div class="w-16 h-16 bg-slate-800 text-slate-500 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                                    <i class="bi bi-slash-circle"></i>
                                </div>
                                <p class="text-sm font-bold text-slate-400">Belum ada wilayah otoritas yang ditugaskan.</p>
                                <p class="text-[10px] text-slate-600 uppercase tracking-widest mt-1">Hubungi Administrator</p>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </main>
    </div>
</body>
</html>