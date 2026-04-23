<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register Portal | Librify System</title>

    {{-- Tailwind CSS --}}
    <script src="https://cdn.tailwindcss.com"></script>
    {{-- Alpine JS --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'sans': ['Plus Jakarta Sans', 'sans-serif'],
                    },
                    animation: {
                        'spin-slow': 'spin 20s linear infinite',
                        'blob': 'blob 10s infinite',
                    },
                    keyframes: {
                        blob: {
                            '0%': { transform: 'translate(0px, 0px) scale(1)' },
                            '33%': { transform: 'translate(30px, -30px) scale(1.1)' },
                            '66%': { transform: 'translate(-20px, 20px) scale(0.9)' },
                            '100%': { transform: 'translate(0px, 0px) scale(1)' },
                        }
                    }
                }
            }
        }
    </script>

    {{-- Fonts & Icons --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />

    <style>
        [x-cloak] { display: none !important; }
        .icon-card {
            background: rgba(30, 41, 59, 0.8);
            backdrop-filter: blur(4px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
        }
        .central-hub {
            background: linear-gradient(135deg, #1e1b4b, #312e81);
            border: 1px solid rgba(99, 102, 241, 0.3);
            box-shadow: 0 0 40px rgba(99, 102, 241, 0.4);
        }
        .orbit-ring {
            border: 1px dashed rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }
        input:-webkit-autofill,
        input:-webkit-autofill:hover, 
        input:-webkit-autofill:focus, 
        input:-webkit-autofill:active{
            -webkit-box-shadow: 0 0 0 30px #1e293b inset !important;
            -webkit-text-fill-color: white !important;
            transition: background-color 5000s ease-in-out 0s;
        }
        select option {
            background-color: #1e293b;
            color: white;
        }
    </style>
</head>

<body x-data="{ mode: 'siswa' }" class="bg-[#020617] text-slate-300 font-sans min-h-screen flex items-center justify-center p-4 selection:bg-cyan-500 selection:text-white overflow-hidden relative">

    {{-- Ambient Background --}}
    <div class="fixed top-[-20%] left-[-10%] w-[600px] h-[600px] rounded-full blur-[100px] animate-blob pointer-events-none transition-colors duration-1000"
         :class="mode === 'siswa' ? 'bg-cyan-600/10' : 'bg-indigo-600/10'"></div>
    <div class="fixed bottom-[-20%] right-[-10%] w-[600px] h-[600px] rounded-full blur-[100px] animate-blob pointer-events-none transition-colors duration-1000"
         :class="mode === 'siswa' ? 'bg-indigo-600/10' : 'bg-cyan-600/10'"></div>

    {{-- BUTTON KEMBALI KE LOGIN --}}
    <a href="{{ route('login') }}" class="fixed top-4 left-4 z-50 group flex items-center gap-3 px-4 py-2 rounded-full bg-white/5 backdrop-blur-sm border border-white/10 hover:bg-white/10 hover:border-white/20 transition-all duration-300 shadow-lg">
        <div class="w-6 h-6 rounded-full bg-white/10 text-slate-300 flex items-center justify-center group-hover:bg-white/20 group-hover:text-white transition-colors">
            <i class="bi bi-arrow-left text-xs"></i>
        </div>
        <span class="text-xs font-semibold text-slate-300 group-hover:text-white hidden sm:block">Kembali ke Login</span>
    </a>

    {{-- TOGGLE BUTTON --}}
    <button @click.prevent="mode = mode === 'siswa' ? 'kelas' : 'siswa'" 
            class="fixed top-4 right-4 z-50 group flex items-center gap-3 px-4 py-2 rounded-full bg-white/5 backdrop-blur-sm border border-white/10 hover:bg-white/10 transition-all duration-300 shadow-lg cursor-pointer">
        <span class="text-xs font-semibold transition-colors hidden sm:block"
              :class="mode === 'siswa' ? 'text-indigo-400 group-hover:text-indigo-300' : 'text-cyan-400 group-hover:text-cyan-300'"
              x-text="mode === 'siswa' ? 'Daftar sbg Kelas' : 'Daftar sbg Siswa'">
        </span>
        <div class="w-6 h-6 rounded-full flex items-center justify-center transition-colors"
             :class="mode === 'siswa' ? 'bg-indigo-500/20 text-indigo-400 group-hover:bg-indigo-500 group-hover:text-white' : 'bg-cyan-500/20 text-cyan-400 group-hover:bg-cyan-500 group-hover:text-white'">
            <i class="bi text-xs" :class="mode === 'siswa' ? 'bi-people-fill' : 'bi-person-fill'"></i>
        </div>
    </button>

    {{-- MAIN CONTAINER --}}
    <div class="w-full max-w-5xl h-[85vh] max-h-[750px] bg-[#0f172a]/80 backdrop-blur-md rounded-3xl shadow-2xl border border-white/5 flex flex-col lg:flex-row overflow-hidden relative z-10">
        
        {{-- KIRI: VISUAL --}}
        <div class="hidden lg:flex w-5/12 relative flex-col items-center justify-center bg-[#0b1120] overflow-hidden border-r border-white/5 p-8 text-center transition-colors duration-700">
            <div class="absolute inset-0 opacity-[0.03]" :style="mode === 'siswa' ? 'background-image: radial-gradient(#06b6d4 1px, transparent 1px); background-size: 24px 24px;' : 'background-image: radial-gradient(#6366f1 1px, transparent 1px); background-size: 24px 24px;'"></div>

            <div class="relative w-72 h-72 mb-8">
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-20">
                    <div class="central-hub w-24 h-24 rounded-3xl flex items-center justify-center bg-gradient-to-br"
                         :class="mode === 'siswa' ? 'from-cyan-900 to-indigo-900' : 'from-indigo-900 to-purple-900'">
                        <span class="text-white font-bold text-3xl font-heading">LB</span>
                    </div>
                </div>
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-40 h-40 orbit-ring border-white/10 animate-spin-slow"></div>
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-56 h-56 orbit-ring border-white/10 animate-spin-slow" style="animation-duration: 25s;"></div>
                
                <div class="absolute top-0 left-1/2 -translate-x-1/2 z-10"><div class="icon-card p-2 rounded-xl" :class="mode === 'siswa' ? 'text-cyan-400' : 'text-indigo-400'"><i class="bi bi-person-plus text-base"></i></div></div>
                <div class="absolute bottom-1/4 left-0 z-10"><div class="icon-card p-2 rounded-xl text-rose-400"><i class="bi bi-shield-check text-base"></i></div></div>
                <div class="absolute bottom-1/4 right-0 z-10"><div class="icon-card p-2 rounded-xl text-emerald-400"><i class="bi bi-journal-text text-base"></i></div></div>
            </div>

            <div class="relative z-10">
                <h2 class="text-2xl font-bold text-white mb-2" x-text="mode === 'siswa' ? 'Eksplorasi Mandiri' : 'Kolektif Kelas'"></h2>
                <p class="text-xs text-slate-400 leading-relaxed max-w-[220px] mx-auto" 
                   x-text="mode === 'siswa' ? 'Daftar sebagai siswa untuk meminjam dan mengakses ribuan buku.' : 'Buat profil kelas dan daftarkan penanggung jawab kelas Anda.'"></p>
            </div>
        </div>

        {{-- KANAN: REGISTER FORM --}}
        <div class="w-full lg:w-7/12 bg-[#0f172a] relative flex flex-col justify-center h-full p-6 md:p-10 overflow-hidden">
            
            <div class="lg:hidden flex flex-col items-center justify-center gap-2 mb-6">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br flex items-center justify-center text-white font-bold text-xl shadow-lg transition-colors duration-500"
                     :class="mode === 'siswa' ? 'from-cyan-600 to-indigo-600 shadow-cyan-500/30' : 'from-indigo-600 to-purple-600 shadow-indigo-500/30'">LB</div>
                <h1 class="text-xl font-bold text-white">Librify</h1>
            </div>

            <div class="relative w-full max-w-xl mx-auto min-h-[520px]">

                {{-- INFO ALERT --}}
                <div class="p-3 mb-5 bg-amber-500/10 border border-amber-500/20 rounded-xl flex items-start gap-3 relative z-20">
                    <div class="mt-0.5 text-amber-400"><i class="bi bi-info-circle-fill"></i></div>
                    <div>
                        <p class="text-xs font-bold text-amber-400 mb-0.5">Sistem Approval Aktif</p>
                        <p class="text-[11px] text-amber-200/70 leading-relaxed">Setelah mendaftar, akun akan berstatus <b>Pending</b> dan harus di-ACC Petugas sebelum bisa login.</p>
                    </div>
                </div>

                {{-- ====================== --}}
                {{-- FORM 1: SISWA --}}
                {{-- ====================== --}}
                <div x-show="mode === 'siswa'"
                     x-transition:enter="transition ease-out duration-500 delay-100"
                     x-transition:enter-start="opacity-0 translate-x-8"
                     x-transition:enter-end="opacity-100 translate-x-0"
                     x-transition:leave="transition ease-in duration-300 absolute top-[80px] w-full"
                     x-transition:leave-start="opacity-100 translate-x-0"
                     x-transition:leave-end="opacity-0 -translate-x-8"
                     class="w-full">
                     
                    <div class="mb-5 text-center lg:text-left">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-[10px] font-bold text-cyan-400 mb-2 uppercase tracking-widest">
                            <i class="bi bi-person-badge-fill"></i> Registrasi Siswa
                        </div>
                        <h3 class="text-2xl font-bold text-white mb-1">Buat Akun Baru</h3>
                    </div>

                    <form method="POST" action="{{ route('register') }}" class="space-y-4">
                        @csrf
                        <input type="hidden" name="role" value="student">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-cyan-400 uppercase tracking-wider ml-1">Nama Lengkap</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="bi bi-person-fill text-slate-500 group-focus-within:text-cyan-500 transition-colors"></i>
                                    </div>
                                    <input type="text" name="name" value="{{ old('name') }}" required
                                           class="w-full bg-[#1e293b] text-white border border-slate-700/50 rounded-xl py-2.5 pl-10 pr-4 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 text-sm placeholder:text-slate-600"
                                           placeholder="Cth: Budi Santoso">
                                </div>
                                @error('name') <p class="text-rose-400 text-[10px] ml-1 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-cyan-400 uppercase tracking-wider ml-1">NISN Siswa</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="bi bi-person-vcard text-slate-500 group-focus-within:text-cyan-500 transition-colors"></i>
                                    </div>
                                    <input type="text" name="nisn" value="{{ old('nisn') }}" required
                                           class="w-full bg-[#1e293b] text-white border border-slate-700/50 rounded-xl py-2.5 pl-10 pr-4 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 text-sm placeholder:text-slate-600"
                                           placeholder="Cth: 0054812345">
                                </div>
                                @error('nisn') <p class="text-rose-400 text-[10px] ml-1 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-cyan-400 uppercase tracking-wider ml-1">Pilih Kelas</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="bi bi-diagram-3-fill text-slate-500 group-focus-within:text-cyan-500 transition-colors"></i>
                                    </div>
                                    <select name="class_id" required
                                            class="w-full bg-[#1e293b] text-white border border-slate-700/50 rounded-xl py-2.5 pl-10 pr-10 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 text-sm appearance-none cursor-pointer">
                                        <option value="" disabled selected>-- Pilih Kelas Anda --</option>
                                        @if(isset($classes) && count($classes) > 0)
                                            @foreach($classes as $class)
                                                <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                                            @endforeach
                                        @else
                                            <option value="1">X RPL 1</option>
                                            <option value="2">XI TKR 2</option>
                                        @endif
                                    </select>
                                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-500 group-focus-within:text-cyan-500 transition-colors">
                                        <i class="bi bi-chevron-down text-xs"></i>
                                    </div>
                                </div>
                                @error('class_id') <p class="text-rose-400 text-[10px] ml-1 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-cyan-400 uppercase tracking-wider ml-1">Email Aktif</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="bi bi-envelope-fill text-slate-500 group-focus-within:text-cyan-500 transition-colors"></i>
                                    </div>
                                    <input type="email" name="email" value="{{ old('email') }}" required
                                           class="w-full bg-[#1e293b] text-white border border-slate-700/50 rounded-xl py-2.5 pl-10 pr-4 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 text-sm placeholder:text-slate-600"
                                           placeholder="siswa@sekolah.sch.id">
                                </div>
                                @error('email') <p class="text-rose-400 text-[10px] ml-1 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="space-y-1" x-data="{ show: false }">
                                <label class="text-[10px] font-bold text-cyan-400 uppercase tracking-wider ml-1">Password</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="bi bi-key-fill text-slate-500 group-focus-within:text-cyan-500 transition-colors"></i>
                                    </div>
                                    <input :type="show ? 'text' : 'password'" name="password" required
                                           class="w-full bg-[#1e293b] text-white border border-slate-700/50 rounded-xl py-2.5 pl-10 pr-10 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 text-sm placeholder:text-slate-600"
                                           placeholder="Minimal 8 karakter">
                                    <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-500 hover:text-white transition-colors">
                                        <i class="bi" :class="show ? 'bi-eye' : 'bi-eye-slash'"></i>
                                    </button>
                                </div>
                                @error('password') <p class="text-rose-400 text-[10px] ml-1 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="space-y-1" x-data="{ show: false }">
                                <label class="text-[10px] font-bold text-cyan-400 uppercase tracking-wider ml-1">Konfirmasi Password</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="bi bi-shield-lock-fill text-slate-500 group-focus-within:text-cyan-500 transition-colors"></i>
                                    </div>
                                    <input :type="show ? 'text' : 'password'" name="password_confirmation" required
                                           class="w-full bg-[#1e293b] text-white border border-slate-700/50 rounded-xl py-2.5 pl-10 pr-10 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 text-sm placeholder:text-slate-600"
                                           placeholder="Ketik ulang password">
                                    <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-500 hover:text-white transition-colors">
                                        <i class="bi" :class="show ? 'bi-eye' : 'bi-eye-slash'"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="w-full py-3 px-4 font-bold rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white shadow-lg shadow-cyan-500/20 transform hover:-translate-y-0.5 transition-all text-sm flex items-center justify-center gap-2 mt-4">
                            <span>Daftar Sekarang</span>
                            <i class="bi bi-arrow-right"></i>
                        </button>
                    </form>
                </div>

                {{-- ====================== --}}
                {{-- FORM 2: KELAS (BARU) --}}
                {{-- ====================== --}}
                <div x-show="mode === 'kelas'" x-cloak
                     x-transition:enter="transition ease-out duration-500 delay-100"
                     x-transition:enter-start="opacity-0 -translate-x-8"
                     x-transition:enter-end="opacity-100 translate-x-0"
                     x-transition:leave="transition ease-in duration-300 absolute top-[80px] w-full"
                     x-transition:leave-start="opacity-100 translate-x-0"
                     x-transition:leave-end="opacity-0 translate-x-8"
                     class="w-full">
                     
                    <div class="mb-5 text-center lg:text-left">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-500/10 border border-indigo-500/20 text-[10px] font-bold text-indigo-400 mb-2 uppercase tracking-widest">
                            <i class="bi bi-people-fill"></i> Registrasi Kelas
                        </div>
                        <h3 class="text-2xl font-bold text-white mb-1">Daftarkan Profil Kelas</h3>
                    </div>

                    <form method="POST" action="{{ route('register') }}" class="space-y-4">
                        @csrf
                        <input type="hidden" name="role" value="class">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            
                            {{-- Baris 1: Nama Kelas & Email --}}
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-indigo-400 uppercase tracking-wider ml-1">Nama Kelas</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="bi bi-grid-1x2-fill text-slate-500 group-focus-within:text-indigo-500 transition-colors"></i>
                                    </div>
                                    <input type="text" name="name" value="{{ old('name') }}" required
                                           class="w-full bg-[#1e293b] text-white border border-slate-700/50 rounded-xl py-2.5 pl-10 pr-4 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm placeholder:text-slate-600"
                                           placeholder="Cth: XII RPL 1">
                                </div>
                                @error('name') <p class="text-rose-400 text-[10px] ml-1 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-indigo-400 uppercase tracking-wider ml-1">Email Kelas</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="bi bi-envelope-fill text-slate-500 group-focus-within:text-indigo-500 transition-colors"></i>
                                    </div>
                                    <input type="email" name="email" value="{{ old('email') }}" required
                                           class="w-full bg-[#1e293b] text-white border border-slate-700/50 rounded-xl py-2.5 pl-10 pr-4 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm placeholder:text-slate-600"
                                           placeholder="Cth: xiirpl1@sekolah.sch.id">
                                </div>
                                @error('email') <p class="text-rose-400 text-[10px] ml-1 mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- Baris 2: Ketua & Wakil Ketua --}}
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-indigo-400 uppercase tracking-wider ml-1">Ketua Kelas</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="bi bi-person-badge-fill text-slate-500 group-focus-within:text-indigo-500 transition-colors"></i>
                                    </div>
                                    <input type="text" name="chairman_name" value="{{ old('chairman_name') }}" required
                                           class="w-full bg-[#1e293b] text-white border border-slate-700/50 rounded-xl py-2.5 pl-10 pr-4 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm placeholder:text-slate-600"
                                           placeholder="Cth: Budi Santoso">
                                </div>
                                @error('chairman_name') <p class="text-rose-400 text-[10px] ml-1 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-indigo-400 uppercase tracking-wider ml-1">Wakil Ketua Kelas</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="bi bi-person-fill text-slate-500 group-focus-within:text-indigo-500 transition-colors"></i>
                                    </div>
                                    <input type="text" name="vice_chairman_name" value="{{ old('vice_chairman_name') }}" required
                                           class="w-full bg-[#1e293b] text-white border border-slate-700/50 rounded-xl py-2.5 pl-10 pr-4 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm placeholder:text-slate-600"
                                           placeholder="Cth: Siti Aminah">
                                </div>
                                @error('vice_chairman_name') <p class="text-rose-400 text-[10px] ml-1 mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- Baris 3: Password & Confirm --}}
                            <div class="space-y-1" x-data="{ show: false }">
                                <label class="text-[10px] font-bold text-indigo-400 uppercase tracking-wider ml-1">Password Akun</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="bi bi-key-fill text-slate-500 group-focus-within:text-indigo-500 transition-colors"></i>
                                    </div>
                                    <input :type="show ? 'text' : 'password'" name="password" required
                                           class="w-full bg-[#1e293b] text-white border border-slate-700/50 rounded-xl py-2.5 pl-10 pr-10 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm placeholder:text-slate-600"
                                           placeholder="Minimal 8 karakter">
                                    <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-500 hover:text-white transition-colors">
                                        <i class="bi" :class="show ? 'bi-eye' : 'bi-eye-slash'"></i>
                                    </button>
                                </div>
                                @error('password') <p class="text-rose-400 text-[10px] ml-1 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="space-y-1" x-data="{ show: false }">
                                <label class="text-[10px] font-bold text-indigo-400 uppercase tracking-wider ml-1">Konfirmasi Password</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="bi bi-shield-lock-fill text-slate-500 group-focus-within:text-indigo-500 transition-colors"></i>
                                    </div>
                                    <input :type="show ? 'text' : 'password'" name="password_confirmation" required
                                           class="w-full bg-[#1e293b] text-white border border-slate-700/50 rounded-xl py-2.5 pl-10 pr-10 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm placeholder:text-slate-600"
                                           placeholder="Ketik ulang password">
                                    <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-500 hover:text-white transition-colors">
                                        <i class="bi" :class="show ? 'bi-eye' : 'bi-eye-slash'"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="w-full py-3 px-4 font-bold rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white shadow-lg shadow-indigo-500/20 transform hover:-translate-y-0.5 transition-all text-sm flex items-center justify-center gap-2 mt-4">
                            <span>Daftarkan Akun Kelas</span>
                            <i class="bi bi-arrow-right"></i>
                        </button>
                    </form>
                </div>

                {{-- Footer Link --}}
                <div class="mt-4 text-center absolute bottom-0 w-full z-20">
                    <p class="text-[11px] text-slate-500">
                        Sudah punya akun yang aktif? 
                        <a href="{{ route('login') }}" class="font-bold text-slate-300 hover:text-white transition-colors border-b border-slate-600 pb-0.5 ml-1">Masuk di sini</a>
                    </p>
                </div>

            </div>
        </div>
    </div>

</body>
</html>