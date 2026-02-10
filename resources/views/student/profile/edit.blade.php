<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pengaturan Akun - TekniLog Siswa</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
        .custom-scroll::-webkit-scrollbar { width: 5px; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #06b6d4; border-radius: 20px; } /* Cyan Scrollbar */
    </style>
</head>

<body class="antialiased flex h-screen w-full overflow-hidden text-left" x-data="{ sidebarOpen: false }">

    {{-- Sidebar Student --}}
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'" 
           class="fixed inset-y-0 left-0 z-50 flex flex-col w-72 bg-slate-950 text-white border-r border-slate-900 md:static md:flex-shrink-0 h-full transition-transform duration-300">
        @include('student.partials.sidebar') 
    </aside>

    <div class="flex flex-1 flex-col h-full min-w-0 overflow-hidden">
        {{-- Header Student --}}
        @include('student.partials.header')

        <main class="flex-1 overflow-y-auto p-6 lg:p-10 pt-2 custom-scroll text-left leading-none">
            <div class="mx-auto w-full max-w-[1600px] space-y-8">
                
                {{-- 1. HEADER PROFILE --}}
                <div class="text-left leading-none">
                    <h2 class="text-4xl font-black text-gray-900 tracking-tight uppercase leading-none">Pengaturan Profil</h2>
                    <p class="text-sm font-bold text-cyan-600 mt-3 uppercase tracking-widest leading-none border-l-4 border-cyan-600 pl-4">Kelola foto dan informasi identitas Anda.</p>
                </div>

                {{-- Alert Sukses --}}
                @if (session('status') === 'profile-updated' || session('status') === 'password-updated')
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" class="bg-emerald-500 text-white p-5 rounded-3xl shadow-lg font-bold text-xs uppercase tracking-[0.1em]">
                        <i class="bi bi-check-circle-fill me-2 text-base"></i> Perubahan Berhasil Disimpan!
                    </div>
                @endif

                {{-- GRID LAYOUT: BERSEBELAHAN (XL) --}}
                <div class="grid grid-cols-1 xl:grid-cols-2 gap-8 items-start">
                    
                    {{-- ========================================== --}}
                    {{-- KOLOM KIRI: INFORMASI PROFIL & FOTO --}}
                    {{-- ========================================== --}}
                    <div class="bg-white p-8 lg:p-10 rounded-[3rem] border border-gray-100 shadow-sm leading-none h-full">
                        <div class="flex items-center gap-4 mb-8">
                            <div class="w-12 h-12 bg-cyan-50 text-cyan-600 rounded-2xl flex items-center justify-center text-xl shadow-inner"><i class="bi bi-person-bounding-box"></i></div>
                            <h3 class="text-xl font-black text-gray-900 uppercase tracking-tight">Informasi Personal</h3>
                        </div>
                        
                        <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-8" x-data="{ photoPreview: null }">
                            @csrf @method('patch')

                            {{-- UI Upload Foto Profil --}}
                            <div class="flex flex-col sm:flex-row items-center gap-6 p-6 bg-cyan-50/50 rounded-[2.5rem] border border-dashed border-cyan-200">
                                <div class="relative group flex-shrink-0">
                                    {{-- Preview Circle --}}
                                    <div class="w-28 h-28 rounded-[2.5rem] overflow-hidden border-4 border-white shadow-xl bg-white">
                                        <template x-if="!photoPreview">
                                            @if(Auth::user()->profile_photo_url)
                                                <img src="{{ Auth::user()->profile_photo_url }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center bg-cyan-100 text-cyan-600 font-bold text-3xl uppercase">{{ substr(Auth::user()->name, 0, 2) }}</div>
                                            @endif
                                        </template>
                                        <template x-if="photoPreview">
                                            <img :src="photoPreview" class="w-full h-full object-cover">
                                        </template>
                                    </div>
                                    {{-- Hover Overlay --}}
                                    <label for="profile_photo" class="absolute inset-0 flex items-center justify-center bg-cyan-600/60 text-white rounded-[2.5rem] opacity-0 group-hover:opacity-100 transition-all cursor-pointer">
                                        <i class="bi bi-camera-fill text-2xl"></i>
                                    </label>
                                </div>

                                <div class="text-center sm:text-left flex-1 space-y-2">
                                    <h4 class="text-sm font-black text-gray-800 uppercase tracking-wide">Foto Profil</h4>
                                    <p class="text-[10px] text-gray-400 font-medium leading-relaxed">Format JPG/PNG. Maksimal 2MB.</p>
                                    <input type="file" id="profile_photo" name="profile_photo" class="hidden" accept="image/*" 
                                           @change="const file = $event.target.files[0]; if (file) { const reader = new FileReader(); reader.onload = (e) => { photoPreview = e.target.result; }; reader.readAsDataURL(file); }">
                                    <label for="profile_photo" class="inline-flex px-5 py-2.5 bg-white border border-gray-200 rounded-xl text-[10px] font-black uppercase text-cyan-600 shadow-sm hover:bg-cyan-50 cursor-pointer transition-all mt-2">Pilih File</label>
                                </div>
                            </div>

                            <div class="space-y-6">
                                <div class="space-y-3">
                                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Nama Lengkap</label>
                                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl font-bold text-sm outline-none focus:ring-4 focus:ring-cyan-500/10 transition-all text-gray-700">
                                    @error('name') <p class="text-[10px] text-red-500 font-bold ml-1 uppercase">{{ $message }}</p> @enderror
                                </div>
                                <div class="space-y-3">
                                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Email Sekolah</label>
                                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl font-bold text-sm outline-none focus:ring-4 focus:ring-cyan-500/10 transition-all text-gray-700">
                                    @error('email') <p class="text-[10px] text-red-500 font-bold ml-1 uppercase">{{ $message }}</p> @enderror
                                </div>
                                
                                {{-- Readonly Fields (Info Kelas/NISN) --}}
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="space-y-3">
                                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Kelas Asal</label>
                                        <input type="text" readonly value="{{ $user->classRoom->name ?? '-' }}" class="w-full px-6 py-4 bg-cyan-50/50 border border-cyan-100 rounded-2xl font-black text-sm text-cyan-700 outline-none cursor-not-allowed uppercase">
                                    </div>
                                    <div class="space-y-3">
                                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">NISN</label>
                                        <input type="text" readonly value="{{ $user->nisn ?? '-' }}" class="w-full px-6 py-4 bg-cyan-50/50 border border-cyan-100 rounded-2xl font-black text-sm text-cyan-700 outline-none cursor-not-allowed">
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end pt-4 border-t border-gray-50">
                                <button type="submit" class="w-full sm:w-auto bg-slate-900 text-white px-10 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-cyan-600 transition-all shadow-xl active:scale-95 flex items-center justify-center gap-2">
                                    <i class="bi bi-save2-fill"></i> Simpan Profil
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- ========================================== --}}
                    {{-- KOLOM KANAN: KEAMANAN (PASSWORD + TOGGLE) --}}
                    {{-- ========================================== --}}
                    <div class="bg-white p-8 lg:p-10 rounded-[3rem] border border-gray-100 shadow-sm leading-none h-full">
                        <div class="flex items-center gap-4 mb-8">
                            <div class="w-12 h-12 bg-red-50 text-red-600 rounded-2xl flex items-center justify-center text-xl shadow-inner"><i class="bi bi-shield-lock"></i></div>
                            <h3 class="text-xl font-black text-gray-900 uppercase tracking-tight">Keamanan Akun</h3>
                        </div>
                        
                        <form method="post" action="{{ route('password.update') }}" class="space-y-6">
                            @csrf @method('put')
                            
                            {{-- Input 1: Password Lama --}}
                            <div class="space-y-3" x-data="{ show: false }">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Password Lama</label>
                                <div class="relative">
                                    <input :type="show ? 'text' : 'password'" name="current_password" class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl font-bold text-sm outline-none focus:ring-4 focus:ring-red-500/10 transition-all pr-12 text-gray-700">
                                    <button type="button" @click="show = !show" class="absolute right-5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-red-500 transition-colors">
                                        <i class="bi text-lg" :class="show ? 'bi-eye-slash-fill' : 'bi-eye-fill'"></i>
                                    </button>
                                </div>
                            </div>

                            {{-- Input 2: Password Baru --}}
                            <div class="space-y-3" x-data="{ show: false }">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Password Baru</label>
                                <div class="relative">
                                    <input :type="show ? 'text' : 'password'" name="password" class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl font-bold text-sm outline-none focus:ring-4 focus:ring-red-500/10 transition-all pr-12 text-gray-700">
                                    <button type="button" @click="show = !show" class="absolute right-5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-red-500 transition-colors">
                                        <i class="bi text-lg" :class="show ? 'bi-eye-slash-fill' : 'bi-eye-fill'"></i>
                                    </button>
                                </div>
                            </div>

                            {{-- Input 3: Konfirmasi Password --}}
                            <div class="space-y-3" x-data="{ show: false }">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Ulangi Password</label>
                                <div class="relative">
                                    <input :type="show ? 'text' : 'password'" name="password_confirmation" class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl font-bold text-sm outline-none focus:ring-4 focus:ring-red-500/10 transition-all pr-12 text-gray-700">
                                    <button type="button" @click="show = !show" class="absolute right-5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-red-500 transition-colors">
                                        <i class="bi text-lg" :class="show ? 'bi-eye-slash-fill' : 'bi-eye-fill'"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="flex justify-end pt-4 border-t border-gray-50">
                                <button type="submit" class="w-full sm:w-auto bg-red-600 text-white px-10 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-red-700 transition-all shadow-xl shadow-red-100 active:scale-95 flex items-center justify-center gap-2">
                                    <i class="bi bi-key-fill"></i> Update Password
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </main>
    </div>
</body>
</html>