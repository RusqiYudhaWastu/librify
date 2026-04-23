<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Portal | Librify System</title>

    {{-- Tailwind CSS --}}
    <script src="https://cdn.tailwindcss.com"></script>
    {{-- Alpine JS (Untuk Interaksi Tab & Animasi) --}}
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
    </style>
</head>

<body x-data="{ mode: 'siswa' }" class="bg-[#020617] text-slate-300 font-sans min-h-screen flex items-center justify-center p-4 selection:bg-cyan-500 selection:text-white overflow-x-hidden relative">

    {{-- ✅ FLOATING NOTIFICATION SUKSES (TATA LETAK DIRAPIHKAN) --}}
    @if(session('success'))
    <div x-data="{ show: true }" 
         x-show="show" 
         x-transition:enter="transition ease-out duration-500 transform"
         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:translate-x-10"
         x-transition:enter-end="opacity-100 translate-y-0 sm:translate-x-0"
         x-transition:leave="transition ease-in duration-300 transform"
         x-transition:leave-start="opacity-100 translate-y-0 sm:translate-x-0"
         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:translate-x-10"
         x-init="setTimeout(() => show = false, 6000)" 
         x-cloak
         class="fixed top-6 right-0 sm:right-6 left-0 sm:left-auto mx-4 sm:mx-0 z-[100] flex items-start gap-4 bg-slate-900/90 backdrop-blur-xl border border-emerald-500/30 text-white px-5 py-4 rounded-2xl shadow-2xl max-w-sm w-auto">
        
        <div class="w-10 h-10 bg-emerald-500/20 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
            <i class="bi bi-check-circle-fill text-emerald-400 text-lg"></i>
        </div>
        <div class="flex-1 text-left">
            <p class="font-black text-[10px] uppercase tracking-widest text-emerald-400 leading-none mb-1.5">Berhasil</p>
            <p class="font-medium text-sm leading-relaxed text-slate-200">{{ session('success') }}</p>
        </div>
        <button @click="show = false" class="flex-shrink-0 ml-1 text-slate-500 hover:text-white transition-colors mt-0.5">
            <i class="bi bi-x-lg text-sm"></i>
        </button>
    </div>
    @endif

    {{-- ✅ ERROR NOTIFICATION (TATA LETAK DIRAPIHKAN) --}}
    @if($errors->any() || session('error'))
    <div x-data="{ show: true }" 
         x-show="show" 
         x-transition:enter="transition ease-out duration-500 transform"
         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:translate-x-10"
         x-transition:enter-end="opacity-100 translate-y-0 sm:translate-x-0"
         x-transition:leave="transition ease-in duration-300 transform"
         x-transition:leave-start="opacity-100 translate-y-0 sm:translate-x-0"
         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:translate-x-10"
         x-init="setTimeout(() => show = false, 6000)" 
         x-cloak
         class="fixed top-6 right-0 sm:right-6 left-0 sm:left-auto mx-4 sm:mx-0 z-[100] flex items-start gap-4 bg-slate-900/90 backdrop-blur-xl border border-rose-500/30 text-white px-5 py-4 rounded-2xl shadow-2xl max-w-sm w-auto">
        
        <div class="w-10 h-10 bg-rose-500/20 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
            <i class="bi bi-exclamation-triangle-fill text-rose-400 text-lg"></i>
        </div>
        <div class="flex-1 text-left">
            <p class="font-black text-[10px] uppercase tracking-widest text-rose-400 leading-none mb-1.5">Peringatan</p>
            <p class="font-medium text-sm leading-relaxed text-slate-200">{{ session('error') ?? 'Email atau password yang Anda masukkan salah.' }}</p>
        </div>
        <button @click="show = false" class="flex-shrink-0 ml-1 text-slate-500 hover:text-white transition-colors mt-0.5">
            <i class="bi bi-x-lg text-sm"></i>
        </button>
    </div>
    @endif

    {{-- Ambient Background --}}
    <div class="fixed top-[-20%] left-[-10%] w-[600px] h-[600px] bg-cyan-600/10 rounded-full blur-[100px] animate-blob pointer-events-none transition-colors duration-1000" :class="mode === 'siswa' ? 'bg-cyan-600/10' : 'bg-emerald-600/10'"></div>
    <div class="fixed bottom-[-20%] right-[-10%] w-[600px] h-[600px] bg-indigo-600/10 rounded-full blur-[100px] animate-blob pointer-events-none"></div>

    {{-- BUTTON KEMBALI --}}
    <a href="/" class="fixed top-4 left-4 z-50 group flex items-center gap-3 px-4 py-2 rounded-full bg-white/5 backdrop-blur-sm border border-white/10 hover:bg-white/10 hover:border-white/20 transition-all duration-300 shadow-lg">
        <div class="w-6 h-6 rounded-full bg-white/10 text-slate-300 flex items-center justify-center group-hover:bg-white/20 group-hover:text-white transition-colors">
            <i class="bi bi-arrow-left text-xs"></i>
        </div>
        <span class="text-xs font-semibold text-slate-300 group-hover:text-white">Kembali</span>
    </a>

    {{-- TOGGLE BUTTON --}}
    <button @click.prevent="mode = mode === 'siswa' ? 'staff' : 'siswa'" 
            class="fixed top-4 right-4 z-50 group flex items-center gap-3 px-4 py-2 rounded-full bg-white/5 backdrop-blur-sm border border-white/10 hover:bg-white/10 transition-all duration-300 shadow-lg cursor-pointer">
        <span class="text-xs font-semibold transition-colors hidden sm:block"
              :class="mode === 'siswa' ? 'text-emerald-400 group-hover:text-emerald-300' : 'text-cyan-400 group-hover:text-cyan-300'"
              x-text="mode === 'siswa' ? 'Login Guru/Petugas' : 'Login Siswa'">
        </span>
        <div class="w-6 h-6 rounded-full flex items-center justify-center transition-colors"
             :class="mode === 'siswa' ? 'bg-emerald-500/20 text-emerald-400 group-hover:bg-emerald-500 group-hover:text-white' : 'bg-cyan-500/20 text-cyan-400 group-hover:bg-cyan-500 group-hover:text-white'">
            <i class="bi text-xs" :class="mode === 'siswa' ? 'bi-shield-lock-fill' : 'bi-mortarboard-fill'"></i>
        </div>
    </button>

    {{-- MAIN CONTAINER --}}
    <div class="w-full max-w-4xl h-auto lg:h-[650px] bg-[#0f172a]/80 backdrop-blur-md rounded-3xl shadow-2xl border border-white/5 flex flex-col lg:flex-row overflow-hidden relative z-10 my-10 lg:my-0">
        
        {{-- KIRI: VISUAL EKOSISTEM --}}
        <div class="hidden lg:flex w-5/12 relative flex-col items-center justify-center bg-[#0b1120] overflow-hidden border-b lg:border-b-0 lg:border-r border-white/5 p-8 text-center transition-colors duration-700">
            
            {{-- Grid Background --}}
            <div class="absolute inset-0 opacity-[0.03]" :style="mode === 'siswa' ? 'background-image: radial-gradient(#06b6d4 1px, transparent 1px); background-size: 24px 24px;' : 'background-image: radial-gradient(#10b981 1px, transparent 1px); background-size: 24px 24px;'"></div>

            {{-- ORBIT SYSTEM ANIMATION --}}
            <div class="relative w-72 h-72 mb-8 scale-90 xl:scale-100">
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-20">
                    <div class="central-hub w-24 h-24 rounded-3xl flex items-center justify-center bg-gradient-to-br"
                         :class="mode === 'siswa' ? 'from-cyan-900 to-indigo-900' : 'from-emerald-900 to-indigo-900'">
                        <span class="text-white font-bold text-3xl font-heading">LB</span>
                    </div>
                </div>

                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-40 h-40 orbit-ring border-white/10 animate-spin-slow"></div>
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-56 h-56 orbit-ring border-white/10 animate-spin-slow" style="animation-duration: 25s;"></div>
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-72 h-72 orbit-ring border-white/10 animate-spin-slow" style="animation-duration: 30s;"></div>

                {{-- Floating Icons --}}
                <div class="absolute top-0 left-1/2 -translate-x-1/2 z-10"><div class="icon-card p-2 rounded-xl text-white"><i class="bi bi-book text-base"></i></div></div>
                <div class="absolute top-1/4 right-0 z-10"><div class="icon-card p-2 rounded-xl text-indigo-400"><i class="bi bi-journal-bookmark text-base"></i></div></div>
                <div class="absolute bottom-1/4 left-0 z-10"><div class="icon-card p-2 rounded-xl text-amber-400"><i class="bi bi-star-half text-base"></i></div></div>
                <div class="absolute bottom-0 left-1/2 -translate-x-1/2 z-10">
                    <div class="icon-card p-2 rounded-xl transition-colors duration-500" :class="mode === 'siswa' ? 'text-cyan-400' : 'text-emerald-400'">
                        <i class="bi bi-qr-code-scan text-base"></i>
                    </div>
                </div>
            </div>

            <div class="relative z-10">
                <h2 class="text-2xl font-bold text-white mb-2" x-text="mode === 'siswa' ? 'Satu Akses, Semua Buku.' : 'Sistem Kontrol Penuh.'"></h2>
                <p class="text-xs text-slate-400 leading-relaxed max-w-[220px] mx-auto" x-text="mode === 'siswa' ? 'Eksplorasi ribuan koleksi buku perpustakaan dalam satu genggaman.' : 'Kelola aset perpustakaan dan monitor aktivitas siswa secara real-time.'"></p>
            </div>
            
            <div class="absolute bottom-4 text-[10px] text-slate-600">v2.1 Stable Release</div>
        </div>

        {{-- KANAN: LOGIN FORM AREA --}}
        <div class="w-full lg:w-7/12 bg-[#0f172a] relative flex flex-col justify-center p-6 md:p-12 overflow-hidden">
            
            {{-- Mobile Logo --}}
            <div class="lg:hidden flex flex-col items-center justify-center gap-2 mb-8 mt-2">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br flex items-center justify-center text-white font-bold text-xl shadow-lg transition-colors duration-500"
                     :class="mode === 'siswa' ? 'from-cyan-600 to-indigo-600 shadow-cyan-500/30' : 'from-emerald-600 to-indigo-600 shadow-emerald-500/30'">LB</div>
                <div class="text-center">
                    <h1 class="text-xl font-bold text-white">Librify</h1>
                </div>
            </div>

            {{-- WRAPPER FOR TRANSITIONS --}}
            <div class="relative w-full max-w-sm mx-auto min-h-[400px]">

                {{-- FORM 1: SISWA --}}
                <div x-show="mode === 'siswa'"
                     x-transition:enter="transition ease-out duration-500 delay-100"
                     x-transition:enter-start="opacity-0 translate-x-8"
                     x-transition:enter-end="opacity-100 translate-x-0"
                     x-transition:leave="transition ease-in duration-300 absolute top-0 w-full"
                     x-transition:leave-start="opacity-100 translate-x-0"
                     x-transition:leave-end="opacity-0 -translate-x-8"
                     class="w-full">
                    
                    <div class="mb-8 text-center lg:text-left">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-[10px] font-bold text-cyan-400 mb-4 uppercase tracking-widest">
                            <i class="bi bi-mortarboard-fill"></i> Portal Siswa
                        </div>
                        <h3 class="text-2xl font-bold text-white mb-1">Selamat Datang</h3>
                        <p class="text-slate-400 text-xs">Login akses peminjaman Siswa / Kelas.</p>
                    </div>

                    <form method="POST" action="{{ route('login') }}" class="space-y-5">
                        @csrf
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold uppercase tracking-wider ml-1 text-cyan-400">NISN Siswa / Email Kelas</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="bi bi-person-badge-fill text-slate-500 group-focus-within:text-cyan-500 transition-colors duration-300"></i>
                                </div>
                                <input type="text" name="email" required autofocus
                                       class="w-full bg-[#1e293b] text-white border border-slate-700/50 rounded-xl py-2.5 pl-10 pr-4 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition-all text-sm placeholder:text-slate-600"
                                       placeholder="Cth: 005481xxxx / kelas@sekolah.sch.id" value="{{ old('email') }}">
                            </div>
                        </div>

                        <div class="space-y-1" x-data="{ show: false }">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider ml-1">Password</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="bi bi-key-fill text-slate-500 group-focus-within:text-cyan-500 transition-colors duration-300"></i>
                                </div>
                                <input :type="show ? 'text' : 'password'" name="password" required
                                       class="w-full bg-[#1e293b] text-white border border-slate-700/50 rounded-xl py-2.5 pl-10 pr-10 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition-all text-sm placeholder:text-slate-600"
                                       placeholder="••••••••">
                                <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-500 hover:text-white transition-colors cursor-pointer">
                                    <i class="bi" :class="show ? 'bi-eye' : 'bi-eye-slash'"></i>
                                </button>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-1">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="remember" class="w-3.5 h-3.5 rounded border-slate-600 bg-[#1e293b] text-cyan-600 focus:ring-cyan-500 focus:ring-offset-0 transition-colors">
                                <span class="text-xs text-slate-400 hover:text-slate-300 select-none">Ingat Saya</span>
                            </label>
                            @if (Route::has('password.request'))
                            @endif
                        </div>

                        <button type="submit" class="w-full py-3 px-4 font-bold rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white shadow-lg shadow-cyan-500/20 transform hover:-translate-y-0.5 transition-all text-sm flex items-center justify-center gap-2 mt-4">
                            <span>Masuk Dashboard</span>
                            <i class="bi bi-arrow-right"></i>
                        </button>
                    </form>

                    <div class="mt-8 text-center border-t border-white/5 pt-6">
                        <p class="text-[11px] text-slate-500">
                            Siswa baru dan belum memiliki akun? 
                            <br class="mb-1">
                            <a href="{{ route('register') }}" class="font-bold text-cyan-400 hover:text-cyan-300 transition-colors border-b border-cyan-400/30 pb-0.5">Daftar Sekarang di Sini</a>
                        </p>
                    </div>
                </div>

                {{-- FORM 2: STAFF / GURU --}}
                <div x-show="mode === 'staff'" x-cloak
                     x-transition:enter="transition ease-out duration-500 delay-100"
                     x-transition:enter-start="opacity-0 -translate-x-8"
                     x-transition:enter-end="opacity-100 translate-x-0"
                     x-transition:leave="transition ease-in duration-300 absolute top-0 w-full"
                     x-transition:leave-start="opacity-100 translate-x-0"
                     x-transition:leave-end="opacity-0 translate-x-8"
                     class="w-full">
                    
                    <div class="mb-8 text-center lg:text-left">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-[10px] font-bold text-emerald-400 mb-4 uppercase tracking-widest">
                            <i class="bi bi-shield-check"></i> Area Staff & Guru
                        </div>
                        <h3 class="text-2xl font-bold text-white mb-1">Portal Manajemen</h3>
                        <p class="text-slate-400 text-xs">Akses khusus Petugas Perpustakaan & Guru.</p>
                    </div>

                    <form method="POST" action="{{ route('login') }}" class="space-y-5">
                        @csrf
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold uppercase tracking-wider ml-1 text-emerald-400">Email Petugas / NIP Guru</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="bi bi-person-lines-fill text-slate-500 group-focus-within:text-emerald-500 transition-colors duration-300"></i>
                                </div>
                                <input type="text" name="email" required
                                       class="w-full bg-[#1e293b] text-white border border-slate-700/50 rounded-xl py-2.5 pl-10 pr-4 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all text-sm placeholder:text-slate-600"
                                       placeholder="Cth: petugas@... / guru@..." value="{{ old('email') }}">
                            </div>
                        </div>

                        <div class="space-y-1" x-data="{ show: false }">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider ml-1">Password</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="bi bi-key-fill text-slate-500 group-focus-within:text-emerald-500 transition-colors duration-300"></i>
                                </div>
                                <input :type="show ? 'text' : 'password'" name="password" required
                                       class="w-full bg-[#1e293b] text-white border border-slate-700/50 rounded-xl py-2.5 pl-10 pr-10 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all text-sm placeholder:text-slate-600"
                                       placeholder="••••••••">
                                <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-500 hover:text-white transition-colors cursor-pointer">
                                    <i class="bi" :class="show ? 'bi-eye' : 'bi-eye-slash'"></i>
                                </button>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-1">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="remember" class="w-3.5 h-3.5 rounded border-slate-600 bg-[#1e293b] text-emerald-600 focus:ring-emerald-500 focus:ring-offset-0 transition-colors">
                                <span class="text-xs text-slate-400 hover:text-slate-300 select-none">Ingat Saya</span>
                            </label>
                        </div>

                        <button type="submit" class="w-full py-3 px-4 font-bold rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white shadow-lg shadow-emerald-500/20 transform hover:-translate-y-0.5 transition-all text-sm flex items-center justify-center gap-2 mt-4">
                            <span>Otorisasi Masuk</span>
                            <i class="bi bi-shield-check"></i>
                        </button>
                    </form>

                    <div class="mt-8 text-center border-t border-white/5 pt-6">
                        <p class="text-[11px] text-slate-500">
                            Kendala login? Hubungi <span class="font-bold text-emerald-400">Administrator Server</span>.
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </div>

</body>
</html>