<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Portal | TekniLog System</title>

    {{-- Tailwind CSS --}}
    <script src="https://cdn.tailwindcss.com"></script>
    {{-- Alpine JS (Untuk Interaksi Tab) --}}
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
        /* Glassmorphism Effect for Panel */
        .glass-panel {
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        /* Icon Cards */
        .icon-card {
            background: rgba(30, 41, 59, 0.8);
            backdrop-filter: blur(4px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
        }
        /* Central Hub Styling */
        .central-hub {
            background: linear-gradient(135deg, #1e1b4b, #312e81);
            border: 1px solid rgba(99, 102, 241, 0.3);
            box-shadow: 0 0 40px rgba(99, 102, 241, 0.4);
        }
        /* Orbit Lines */
        .orbit-ring {
            border: 1px dashed rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }
        /* Glow and Particles */
        .glow-particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: #6366f1;
            border-radius: 50%;
            box-shadow: 0 0 10px #6366f1;
        }
        /* Input Autofill Styling */
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

<body class="bg-[#020617] text-slate-300 font-sans min-h-screen flex items-center justify-center p-4 selection:bg-indigo-500 selection:text-white overflow-hidden relative">

    {{-- Ambient Background --}}
    <div class="fixed top-[-20%] left-[-10%] w-[600px] h-[600px] bg-indigo-600/10 rounded-full blur-[100px] animate-blob pointer-events-none"></div>
    <div class="fixed bottom-[-20%] right-[-10%] w-[600px] h-[600px] bg-purple-600/10 rounded-full blur-[100px] animate-blob pointer-events-none"></div>

    {{-- MAIN CONTAINER: Compact Size (Max-w-4xl) --}}
    <div class="w-full max-w-4xl h-auto lg:h-[650px] bg-[#0f172a]/80 backdrop-blur-md rounded-3xl shadow-2xl border border-white/5 flex overflow-hidden relative z-10">
        
        {{-- ========================================== --}}
        {{-- KIRI: VISUAL EKOSISTEM JURUSAN --}}
        {{-- ========================================== --}}
        <div class="hidden lg:flex w-5/12 relative flex-col items-center justify-center bg-[#0b1120] overflow-hidden border-r border-white/5 p-8 text-center">
            
            {{-- Grid Background --}}
            <div class="absolute inset-0 opacity-[0.03]" style="background-image: radial-gradient(#6366f1 1px, transparent 1px); background-size: 24px 24px;"></div>

            {{-- ORBIT SYSTEM ANIMATION --}}
            <div class="relative w-72 h-72 mb-8">
                {{-- Center: TekniLog Core --}}
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-20">
                    <div class="central-hub w-24 h-24 rounded-3xl flex items-center justify-center">
                        <span class="text-white font-bold text-3xl font-heading">TL</span>
                    </div>
                </div>

                {{-- Orbit Circles --}}
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-40 h-40 orbit-ring animate-spin-slow"></div>
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-56 h-56 orbit-ring animate-spin-slow" style="animation-duration: 25s;"></div>
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-72 h-72 orbit-ring animate-spin-slow" style="animation-duration: 30s;"></div>

                {{-- Floating Icons (Alat Jurusan) --}}
                <div class="absolute top-0 left-1/2 -translate-x-1/2 z-10"><div class="icon-card p-2 rounded-xl flex items-center justify-center text-blue-400"><i class="bi bi-laptop text-base"></i></div></div>
                <div class="absolute top-1/4 right-0 z-10"><div class="icon-card p-2 rounded-xl flex items-center justify-center text-rose-400"><i class="bi bi-camera-reels text-base"></i></div></div>
                <div class="absolute bottom-1/4 left-0 z-10"><div class="icon-card p-2 rounded-xl flex items-center justify-center text-amber-400"><i class="bi bi-wrench-adjustable text-base"></i></div></div>
                <div class="absolute bottom-0 left-1/2 -translate-x-1/2 z-10"><div class="icon-card p-2 rounded-xl flex items-center justify-center text-cyan-400"><i class="bi bi-fire text-base"></i></div></div>
                <div class="absolute top-1/4 left-0 z-10"><div class="icon-card p-2 rounded-xl flex items-center justify-center text-orange-400"><i class="bi bi-gear text-base"></i></div></div>
                <div class="absolute bottom-1/4 right-0 z-10"><div class="icon-card p-2 rounded-xl flex items-center justify-center text-purple-400"><i class="bi bi-tablet text-base"></i></div></div>
                <div class="absolute top-0 left-10 z-10"><div class="icon-card p-2 rounded-xl flex items-center justify-center text-green-400"><i class="bi bi-cpu text-base"></i></div></div>
                <div class="absolute bottom-0 right-10 z-10"><div class="icon-card p-2 rounded-xl flex items-center justify-center text-red-400"><i class="bi bi-sliders text-base"></i></div></div>
            </div>

            {{-- Text Description --}}
            <div class="relative z-10">
                <h2 class="text-2xl font-bold text-white mb-2">Satu Akses, <br> Semua Jurusan.</h2>
                <p class="text-xs text-slate-400 leading-relaxed max-w-[220px] mx-auto">
                    Manajemen alat RPL, Broadcast, Animasi, Otomotif & Las dalam satu genggaman.
                </p>
            </div>
            
            <div class="absolute bottom-4 text-[10px] text-slate-600">
                v2.1 Stable Release
            </div>
        </div>

        {{-- ====================== --}}
        {{-- KANAN: LOGIN FORM --}}
        {{-- ====================== --}}
        <div class="w-full lg:w-7/12 bg-[#0f172a] relative flex flex-col justify-center p-8 md:p-12" x-data="{ role: 'student' }">
            
            {{-- Tombol Kembali --}}
            <div class="absolute top-6 left-6 lg:top-8 lg:left-8 z-20">
                <a href="/" class="group flex items-center gap-2 text-xs font-bold text-slate-500 hover:text-white transition-colors">
                    <div class="w-6 h-6 rounded-full bg-slate-800 flex items-center justify-center group-hover:bg-indigo-600 transition-colors">
                        <i class="bi bi-arrow-left"></i>
                    </div>
                    <span>Kembali</span>
                </a>
            </div>

            {{-- Mobile Logo --}}
            <div class="lg:hidden flex items-center justify-center gap-2 mb-8 mt-4">
                <div class="w-8 h-8 rounded-lg bg-indigo-600 flex items-center justify-center text-white font-bold text-sm">TL</div>
                <span class="text-lg font-bold text-white">TekniLog</span>
            </div>

            <div class="max-w-sm w-full mx-auto">
                
                {{-- Header Form --}}
                <div class="mb-6 mt-4 lg:mt-0">
                    <h3 class="text-2xl font-bold text-white mb-1">Login Portal</h3>
                    <p class="text-slate-400 text-xs">Silakan pilih akses masuk Anda.</p>
                </div>

                {{-- ✅ ROLE SELECTOR BUTTONS ✅ --}}
                <div class="grid grid-cols-3 gap-2 mb-8 p-1 bg-slate-800/50 rounded-2xl border border-white/5">
                    {{-- Button: SISWA --}}
                    <button @click="role = 'student'" 
                            :class="role === 'student' ? 'bg-cyan-600 text-white shadow-lg shadow-cyan-500/20' : 'text-slate-400 hover:text-white hover:bg-white/5'"
                            class="flex flex-col items-center justify-center gap-1 py-3 rounded-xl transition-all duration-300 group">
                        <i class="bi bi-person-badge-fill text-lg"></i>
                        <span class="text-[10px] font-bold uppercase tracking-wide">Siswa</span>
                    </button>

                    {{-- Button: KELAS --}}
                    <button @click="role = 'class'" 
                            :class="role === 'class' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/20' : 'text-slate-400 hover:text-white hover:bg-white/5'"
                            class="flex flex-col items-center justify-center gap-1 py-3 rounded-xl transition-all duration-300 group">
                        <i class="bi bi-people-fill text-lg"></i>
                        <span class="text-[10px] font-bold uppercase tracking-wide">Kelas</span>
                    </button>

                    {{-- Button: STAF --}}
                    <button @click="role = 'staff'" 
                            :class="role === 'staff' ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-500/20' : 'text-slate-400 hover:text-white hover:bg-white/5'"
                            class="flex flex-col items-center justify-center gap-1 py-3 rounded-xl transition-all duration-300 group">
                        <i class="bi bi-shield-lock-fill text-lg"></i>
                        <span class="text-[10px] font-bold uppercase tracking-wide">Staf</span>
                    </button>
                </div>

                {{-- Alert Info --}}
                @if (session('status'))
                    <div class="p-3 mb-5 bg-indigo-500/10 border border-indigo-500/20 rounded-lg flex items-center gap-2 text-xs text-indigo-300">
                        <i class="bi bi-info-circle-fill"></i>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf

                    {{-- Dynamic Input: Email/NISN/Username --}}
                    <div class="space-y-1">
                        {{-- Label berubah sesuai tombol yg diklik --}}
                        <label class="text-[10px] font-bold uppercase tracking-wider ml-1 transition-colors duration-300"
                               :class="{
                                   'text-cyan-400': role === 'student',
                                   'text-indigo-400': role === 'class',
                                   'text-emerald-400': role === 'staff'
                               }"
                               x-text="role === 'student' ? 'NISN Siswa' : (role === 'class' ? 'Email Kelas' : 'Username / Email')">
                        </label>

                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                {{-- Icon berubah sesuai role --}}
                                <i class="transition-colors duration-300"
                                   :class="{
                                       'bi-card-heading text-cyan-500': role === 'student',
                                       'bi-envelope-at-fill text-indigo-500': role === 'class',
                                       'bi-person-lines-fill text-emerald-500': role === 'staff'
                                   }"></i>
                            </div>
                            
                            {{-- Note: name tetap 'email' agar diproses LoginRequest kita yang canggih tadi --}}
                            <input type="text" name="email" required autofocus
                                   class="w-full bg-[#1e293b] text-white border rounded-xl py-2.5 pl-10 pr-4 
                                          focus:outline-none focus:ring-1 transition-all text-sm placeholder:text-slate-600"
                                   :class="{
                                       'focus:border-cyan-500 focus:ring-cyan-500 border-slate-700/50': role === 'student',
                                       'focus:border-indigo-500 focus:ring-indigo-500 border-slate-700/50': role === 'class',
                                       'focus:border-emerald-500 focus:ring-emerald-500 border-slate-700/50': role === 'staff'
                                   }"
                                   :placeholder="role === 'student' ? 'Contoh: 005481xxxx' : (role === 'class' ? 'tkj1@sekolah.sch.id' : 'admin / toolman@sekolah.sch.id')"
                                   value="{{ old('email') }}">
                        </div>
                        @error('email') <span class="text-rose-400 text-[10px] ml-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Password --}}
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider ml-1">Password</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="bi bi-key-fill text-slate-500 group-focus-within:text-white transition-colors"></i>
                            </div>
                            <input type="password" name="password" id="password" required
                                   class="w-full bg-[#1e293b] text-white border border-slate-700/50 rounded-xl py-2.5 pl-10 pr-10
                                          focus:outline-none transition-all text-sm placeholder:text-slate-600"
                                   :class="{
                                       'focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500': role === 'student',
                                       'focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500': role === 'class',
                                       'focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500': role === 'staff'
                                   }"
                                   placeholder="••••••••">
                            <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-500 hover:text-white transition-colors cursor-pointer">
                                <i class="bi bi-eye-slash"></i>
                            </button>
                        </div>
                        @error('password') <span class="text-rose-400 text-[10px] ml-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Remember Me --}}
                    <div class="flex items-center pt-1">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="remember" 
                                   class="w-3.5 h-3.5 rounded border-slate-600 bg-[#1e293b] focus:ring-offset-0 transition-colors"
                                   :class="{
                                       'text-cyan-600 focus:ring-cyan-500': role === 'student',
                                       'text-indigo-600 focus:ring-indigo-500': role === 'class',
                                       'text-emerald-600 focus:ring-emerald-500': role === 'staff'
                                   }">
                            <span class="text-xs text-slate-400 hover:text-slate-300 select-none">Ingat Saya</span>
                        </label>
                    </div>

                    {{-- Submit Button --}}
                    <button type="submit" 
                            class="w-full py-3 px-4 font-bold rounded-xl shadow-lg transform hover:-translate-y-0.5 transition-all text-sm flex items-center justify-center gap-2 mt-2 text-white"
                            :class="{
                                'bg-cyan-600 hover:bg-cyan-500 shadow-cyan-500/20': role === 'student',
                                'bg-indigo-600 hover:bg-indigo-500 shadow-indigo-500/20': role === 'class',
                                'bg-emerald-600 hover:bg-emerald-500 shadow-emerald-500/20': role === 'staff'
                            }">
                        <span>Masuk Dashboard</span>
                        <i class="bi bi-arrow-right"></i>
                    </button>
                </form>

                {{-- Footer Info --}}
                <div class="mt-8 text-center border-t border-white/5 pt-4">
                    <p class="text-[10px] text-slate-500">
                        Belum punya akun? Hubungi <span class="font-bold cursor-pointer transition-colors" :class="role === 'student' ? 'text-cyan-400' : 'text-indigo-400'">Toolman Jurusan</span>.
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Script Toggle Password --}}
    <script>
        const toggleBtn = document.getElementById('togglePassword');
        const passInput = document.getElementById('password');
        const icon = toggleBtn.querySelector('i');

        toggleBtn.addEventListener('click', () => {
            const type = passInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passInput.setAttribute('type', type);
            
            if(type === 'text') {
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
                icon.classList.add('text-white');
            } else {
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
                icon.classList.remove('text-white');
            }
        });
    </script>
</body>
</html>