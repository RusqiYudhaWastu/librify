<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
   <title>Librify | Sistem Perpustakaan Digital</title>

    {{-- Tailwind CSS --}}
    <script src="https://cdn.tailwindcss.com"></script>
    
    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    {{-- Icons & Animations --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Tilt.js (Efek 3D) --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vanilla-tilt/1.8.0/vanilla-tilt.min.js"></script>

    <style>
        :root {
            --primary: #6366f1;
            --secondary: #ec4899;
            --accent: #06b6d4;
            --dark: #0f172a;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: #000;
            color: #e2e8f0;
            overflow-x: hidden;
        }

        .font-heading { font-family: 'Space Grotesk', sans-serif; }

        .bg-noise {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.65' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)' opacity='0.05'/%3E%3C/svg%3E");
            pointer-events: none; z-index: 50;
        }

        .blob {
            position: absolute; filter: blur(80px); z-index: -1; opacity: 0.6;
            animation: moveBlob 10s infinite alternate;
        }
        .blob-1 { top: -10%; left: -10%; width: 500px; height: 500px; background: radial-gradient(circle, var(--primary), transparent); }
        .blob-2 { bottom: 10%; right: -10%; width: 400px; height: 400px; background: radial-gradient(circle, var(--secondary), transparent); }

        @keyframes moveBlob {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(20px, -20px) scale(1.1); }
        }

        .glass {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .text-gradient-hero {
            background: linear-gradient(to right, #fff 20%, #818cf8 40%, #c084fc 60%, #fff 80%);
            background-size: 200% auto;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: shine 5s linear infinite;
        }

        @keyframes shine { to { background-position: 200% center; } }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="antialiased selection:bg-indigo-500 selection:text-white">

    <div class="bg-noise"></div>
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
    </div>

<header 
    x-data="{ scrolled: false, mobileOpen: false }" 
    @scroll.window="scrolled = (window.pageYOffset > 20)"
    class="fixed top-0 left-0 right-0 z-[60] flex justify-center transition-all duration-500"
    :class="{ 'py-4': scrolled, 'py-6': !scrolled }"
>
    <div 
        class="relative flex items-center justify-between transition-all duration-500 border"
        :class="{ 
            'w-[92%] md:w-[85%] lg:max-w-6xl bg-[#0f172a]/80 backdrop-blur-xl rounded-full border-white/10 shadow-[0_8px_32px_rgba(0,0,0,0.3)] py-2.5 px-6': scrolled, 
            'w-full bg-transparent border-transparent px-6 lg:px-12': !scrolled 
        }"
    >
        
      <a href="#" class="flex items-center gap-3 group shrink-0">
    <div class="relative w-10 h-10 rounded-full bg-gradient-to-br from-[#1e1b4b] to-indigo-900 border border-indigo-500/30 flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300 overflow-hidden">
        <div class="absolute inset-0 bg-indigo-500/20 blur-md"></div>
        <span class="relative text-white font-bold text-lg font-heading">LB</span>
    </div>
    
    <div>
        <span class="text-xl font-bold font-heading tracking-tight text-white leading-none group-hover:text-indigo-200 transition-colors">
            Libri<span class="text-indigo-400">fy</span>
        </span>
    </div>
</a>

        <nav class="hidden md:flex items-center justify-center absolute left-1/2 -translate-x-1/2">
            <div class="flex items-center gap-1 p-1 rounded-full bg-white/5 border border-white/5 backdrop-blur-sm shadow-inner">
                <a href="#home" class="px-5 py-1.5 rounded-full text-sm font-medium text-gray-300 hover:text-white hover:bg-white/10 transition-all duration-300">Beranda</a>
                <a href="#roles" class="px-5 py-1.5 rounded-full text-sm font-medium text-gray-300 hover:text-white hover:bg-white/10 transition-all duration-300">Hak Akses</a>
                <a href="#alur" class="px-5 py-1.5 rounded-full text-sm font-medium text-gray-300 hover:text-white hover:bg-white/10 transition-all duration-300">Alur</a>
            </div>
        </nav>

        <div class="flex items-center gap-4 shrink-0">
            <a href="{{ route('login') }}" 
               class="hidden md:flex relative group px-6 py-2 rounded-full overflow-hidden shadow-lg shadow-indigo-500/20 transition-all duration-300 hover:shadow-indigo-500/40 hover:-translate-y-0.5 active:scale-95">
                <div class="absolute inset-0 bg-gradient-to-r from-indigo-600 to-purple-600 group-hover:scale-110 transition-transform duration-500"></div>
                <span class="relative flex items-center gap-2 text-sm font-bold text-white">
                    <i class="bi bi-person-circle text-xs"></i> 
                    <span>Login</span>
                </span>
            </a>

            <button @click="mobileOpen = !mobileOpen" class="md:hidden relative w-10 h-10 flex items-center justify-center rounded-full bg-white/5 hover:bg-white/10 text-white transition-colors focus:outline-none border border-white/10">
                <i :class="mobileOpen ? 'bi bi-x-lg' : 'bi bi-list'" class="text-xl transition-all duration-300"></i>
            </button>
        </div>
    </div>

    <div x-show="mobileOpen" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-5 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 -translate-y-5 scale-95"
         @click.away="mobileOpen = false"
         class="absolute top-full mt-3 w-[92%] max-w-sm bg-[#0f172a]/95 backdrop-blur-2xl border border-white/10 rounded-[2.5rem] p-5 shadow-2xl flex flex-col gap-2 md:hidden origin-top"
         style="display: none;">
        
        <div class="px-3 py-1 mb-2">
            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Navigation</p>
        </div>

        <a href="#home" @click="mobileOpen = false" class="p-4 rounded-2xl text-gray-300 hover:bg-white/5 hover:text-white flex items-center gap-4 transition-colors">
            <i class="bi bi-house-door text-indigo-400"></i> 
            <span class="font-semibold">Beranda</span>
        </a>
        <a href="#roles" @click="mobileOpen = false" class="p-4 rounded-2xl text-gray-300 hover:bg-white/5 hover:text-white flex items-center gap-4 transition-colors">
            <i class="bi bi-shield-lock text-indigo-400"></i> 
            <span class="font-semibold">Hak Akses</span>
        </a>
        <a href="#alur" @click="mobileOpen = false" class="p-4 rounded-2xl text-gray-300 hover:bg-white/5 hover:text-white flex items-center gap-4 transition-colors">
            <i class="bi bi-diagram-3 text-indigo-400"></i> 
            <span class="font-semibold">Alur Sistem</span>
        </a>
        
        <div class="h-px bg-white/10 my-2 mx-4"></div>
        
        <a href="{{ route('login') }}" class="p-4 rounded-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white flex items-center justify-center gap-2 font-bold transition-all shadow-lg shadow-indigo-600/20">
            <i class="bi bi-box-arrow-in-right"></i> Masuk ke Portal
        </a>
    </div>
</header>

    <main>
<section id="home" class="relative pt-32 pb-24 lg:pt-0 lg:pb-0 flex items-center min-h-screen overflow-hidden bg-[#030712]">
    
    <div class="absolute top-0 left-0 w-[500px] h-[500px] bg-indigo-600/10 rounded-full blur-[120px] -z-10 -translate-x-1/2 -translate-y-1/2"></div>
    <div class="absolute bottom-0 right-0 w-[500px] h-[500px] bg-purple-600/10 rounded-full blur-[120px] -z-10 translate-x-1/4 translate-y-1/4"></div>

    <div class="container mx-auto max-w-6xl px-8 md:px-12 lg:px-16 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 lg:gap-24 items-center">
            
            <div data-aos="fade-right" class="text-center lg:text-left mx-auto lg:mx-0 max-w-lg">
                
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-500/5 border border-indigo-500/10 text-[10px] font-bold text-indigo-400 mb-8 justify-center lg:justify-start tracking-[0.3em] uppercase">
                    <span class="relative flex h-1.5 w-1.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-indigo-500"></span>
                    </span>
                    Librify System v2.0
                </div>

                <h1 class="text-4xl md:text-5xl lg:text-6xl font-black leading-[1.1] mb-6 tracking-tight text-white">
                    Sistem Perpustakaan <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-cyan-400">Digital Terpadu.</span>
                </h1>
                
                <p class="text-gray-400 text-sm md:text-base leading-relaxed mb-10 opacity-80">
                    Solusi manajemen perpustakaan modern yang dirancang untuk kemudahan eksplorasi buku, transparansi, dan kecepatan akses data bagi Admin, Petugas, hingga Siswa.
                </p>

                <div class="grid grid-cols-2 gap-6 mb-12">
                    <div class="space-y-2">
                        <div class="flex items-center gap-2 text-indigo-400 justify-center lg:justify-start">
                            <i class="bi bi-qr-code-scan text-lg"></i>
                            <span class="text-xs font-bold uppercase tracking-wider text-white">Scan & Go</span>
                        </div>
                        <p class="text-[11px] text-gray-500 leading-normal">Peminjaman buku kilat dengan sistem QR cerdas.</p>
                    </div>
                    <div class="space-y-2">
                        <div class="flex items-center gap-2 text-cyan-400 justify-center lg:justify-start">
                            <i class="bi bi-star-half text-lg"></i>
                            <span class="text-xs font-bold uppercase tracking-wider text-white">Trust Score</span>
                        </div>
                        <p class="text-[11px] text-gray-500 leading-normal">Pantau status peminjaman & kedisiplinan secara live.</p>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-5">
                    <a href="#katalog" class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-500 text-white px-9 py-3.5 rounded-xl font-bold transition-all shadow-lg shadow-indigo-600/20 text-xs flex items-center justify-center gap-3 active:scale-95">
                        Mulai Eksplorasi <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>

            <div class="hidden lg:flex relative justify-center items-center" data-aos="fade-left" data-aos-delay="200">
                <div class="relative w-[450px] h-[450px]">
                    
                    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-48 h-48 bg-indigo-500/20 rounded-full blur-[80px]"></div>

                    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-30">
                        <div class="bg-[#0f172a] p-5 rounded-[2rem] border border-white/10 shadow-2xl flex flex-col items-center">
                            <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-indigo-500 to-indigo-700 flex items-center justify-center mb-2 relative">
                                <i class="bi bi-journal-bookmark-fill text-3xl text-white"></i>
                                <div class="absolute -inset-1 bg-indigo-500/30 rounded-2xl blur-md animate-pulse"></div>
                            </div>
                            <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest text-center">Library Core</p>
                        </div>
                    </div>

                    <div class="absolute -top-2 right-2 animate-[float_6s_ease-in-out_infinite] z-40">
                        <div class="bg-white/5 backdrop-blur-xl p-3 px-4 rounded-xl border border-white/10 flex items-center gap-3 shadow-xl">
                            <div class="w-6 h-6 rounded-full bg-indigo-500/20 flex items-center justify-center text-indigo-400 text-[10px]">
                                <i class="bi bi-shield-lock"></i>
                            </div>
                            <span class="text-[10px] font-bold text-white uppercase tracking-tight">Admin Portal</span>
                        </div>
                    </div>

                    <div class="absolute -bottom-2 left-2 animate-[float_8s_ease-in-out_infinite_1s] z-40">
                        <div class="bg-white/5 backdrop-blur-xl p-3 px-4 rounded-xl border border-white/10 flex items-center gap-3 shadow-xl">
                            <div class="w-6 h-6 rounded-full bg-amber-500/20 flex items-center justify-center text-amber-500 text-[10px]">
                                <i class="bi bi-person-badge-fill"></i>
                            </div>
                            <span class="text-[10px] font-bold text-white uppercase tracking-tight">Petugas Verifier</span>
                        </div>
                    </div>

                    <div class="absolute top-1/4 -left-6 animate-[float_7s_ease-in-out_infinite_0.5s] z-20">
                        <div class="bg-white/5 backdrop-blur-md p-2 px-3 rounded-lg border border-white/5 shadow-lg flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="text-[9px] text-gray-400 font-medium">Trust Score Monitor</span>
                        </div>
                    </div>

                    <div class="absolute bottom-1/4 -right-6 animate-[float_9s_ease-in-out_infinite_1.5s] z-20">
                        <div class="bg-white/5 backdrop-blur-md p-2 px-3 rounded-lg border border-white/5 shadow-lg flex items-center gap-2">
                            <div class="w-5 h-5 rounded-full bg-rose-500/20 flex items-center justify-center text-rose-400 text-[10px]">
                                <i class="bi bi-phone"></i>
                            </div>
                            <span class="text-[9px] text-gray-400 font-medium">Siswa App</span>
                        </div>
                    </div>

                    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full border border-dashed border-white/10 rounded-full animate-[spin_50s_linear_infinite] -z-10"></div>
                    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[85%] h-[85%] border border-white/5 rounded-full -z-10"></div>

                    <svg class="absolute top-0 left-0 w-full h-full pointer-events-none opacity-30" viewBox="0 0 400 400" fill="none">
                        <path d="M200 200L320 100" stroke="url(#grad1)" stroke-width="1" stroke-dasharray="4 4" class="animate-pulse"/>
                        <path d="M200 200L100 320" stroke="url(#grad2)" stroke-width="1" stroke-dasharray="4 4" class="animate-pulse"/>
                        <path d="M200 200L80 120" stroke="url(#grad3)" stroke-width="1" stroke-dasharray="4 4" class="animate-pulse"/>
                        <defs>
                            <linearGradient id="grad1" x1="200" y1="200" x2="320" y2="100" gradientUnits="userSpaceOnUse"><stop stop-color="#6366f1"/><stop offset="1" stop-color="#6366f1" stop-opacity="0"/></linearGradient>
                            <linearGradient id="grad2" x1="200" y1="200" x2="100" y2="320" gradientUnits="userSpaceOnUse"><stop stop-color="#f59e0b"/><stop offset="1" stop-color="#f59e0b" stop-opacity="0"/></linearGradient>
                            <linearGradient id="grad3" x1="200" y1="200" x2="80" y2="120" gradientUnits="userSpaceOnUse"><stop stop-color="#10b981"/><stop offset="1" stop-color="#10b981" stop-opacity="0"/></linearGradient>
                        </defs>
                    </svg>

                </div>
            </div>

        </div>
    </div>
</section>

<style>
@keyframes float {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-15px) rotate(1deg); }
}
@keyframes spin {
    from { transform: translate(-50%, -50%) rotate(0deg); }
    to { transform: translate(-50%, -50%) rotate(360deg); }
}
</style>

  <section id="roles" class="py-24 relative overflow-hidden bg-[#0b1120]">
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[300px] bg-indigo-500/5 rounded-full blur-[120px]"></div>
    </div>

    <div class="container mx-auto px-6 relative z-10">
        <div class="text-center max-w-2xl mx-auto mb-20" data-aos="fade-up">
            <span class="text-indigo-400 text-[10px] font-bold tracking-[0.3em] uppercase mb-3 block">Access Management</span>
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-4 tracking-tight">
                Struktur & <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-cyan-400">Hak Akses</span>
            </h2>
            <div class="h-1 w-12 bg-indigo-500 mx-auto rounded-full mb-6"></div>
            <p class="text-gray-400 text-sm md:text-base leading-relaxed">
                Sistem perpustakaan terintegrasi dengan pembagian tugas yang spesifik untuk efisiensi operasional dan transparansi.
            </p>
        </div>

        <div class="max-w-4xl mx-auto flex flex-col gap-y-16 relative">
            
            <div class="hidden md:block absolute left-1/2 top-0 bottom-0 w-[1px] bg-gradient-to-b from-gray-800 via-indigo-500/30 to-gray-800 -translate-x-1/2"></div>

            <div class="group relative flex flex-col md:flex-row items-center gap-6 md:gap-12 text-center md:text-left" data-aos="fade-right">
                <div class="w-full md:w-1/2 flex justify-center md:justify-end order-1">
                    <div class="relative">
                        <div class="w-16 h-16 rounded-2xl bg-indigo-500/10 flex items-center justify-center text-indigo-400 border border-indigo-500/20 group-hover:bg-indigo-500 group-hover:text-white transition-all duration-500 shadow-[0_0_25px_rgba(99,102,241,0.2)]">
                            <i class="bi bi-shield-lock text-3xl"></i>
                        </div>
                    </div>
                </div>
                <div class="hidden md:flex absolute left-1/2 -translate-x-1/2 w-3 h-3 rounded-full bg-indigo-500 border-4 border-[#0b1120] z-20 shadow-[0_0_10px_rgba(99,102,241,0.8)]"></div>
                <div class="w-full md:w-1/2 order-2">
                    <h3 class="text-xl font-bold text-white mb-1">Administrator</h3>
                    <p class="text-[10px] tracking-[0.2em] text-indigo-400 uppercase font-bold mb-2">Super User Access</p>
                    <p class="text-gray-400 text-sm leading-relaxed max-w-sm mx-auto md:mx-0">Otoritas penuh dalam manajemen database, konfigurasi sistem, pendaftaran akun petugas, dan rekapitulasi data perpustakaan global.</p>
                </div>
            </div>

            <div class="group relative flex flex-col md:flex-row items-center gap-6 md:gap-12 text-center md:text-right" data-aos="fade-left">
                <div class="w-full md:w-1/2 order-2 md:order-1">
                    <h3 class="text-xl font-bold text-white mb-1">Petugas Perpustakaan</h3>
                    <p class="text-[10px] tracking-[0.2em] text-amber-400 uppercase font-bold mb-2">Circulation Manager</p>
                    <p class="text-gray-400 text-sm leading-relaxed max-w-sm mx-auto md:ml-auto md:mr-0">Bertanggung jawab atas manajemen katalog buku, verifikasi sirkulasi peminjaman/pengembalian, pengecekan kondisi buku, dan evaluasi Trust Score.</p>
                </div>
                <div class="hidden md:flex absolute left-1/2 -translate-x-1/2 w-3 h-3 rounded-full bg-amber-500 border-4 border-[#0b1120] z-20 shadow-[0_0_10px_rgba(245,158,11,0.8)]"></div>
                <div class="w-full md:w-1/2 flex justify-center md:justify-start order-1 md:order-2">
                    <div class="w-16 h-16 rounded-2xl bg-amber-500/10 flex items-center justify-center text-amber-400 border border-amber-500/20 group-hover:bg-amber-500 group-hover:text-white transition-all duration-500 shadow-[0_0_25px_rgba(245,158,11,0.2)]">
                        <i class="bi bi-journal-check text-3xl"></i>
                    </div>
                </div>
            </div>

            <div class="group relative flex flex-col md:flex-row items-center gap-6 md:gap-12 text-center md:text-left" data-aos="fade-right">
                <div class="w-full md:w-1/2 flex justify-center md:justify-end order-1">
                    <div class="w-16 h-16 rounded-2xl bg-emerald-500/10 flex items-center justify-center text-emerald-400 border border-emerald-500/20 group-hover:bg-emerald-500 group-hover:text-white transition-all duration-500 shadow-[0_0_25px_rgba(16,185,129,0.2)]">
                        <i class="bi bi-people text-3xl"></i>
                    </div>
                </div>
                <div class="hidden md:flex absolute left-1/2 -translate-x-1/2 w-3 h-3 rounded-full bg-emerald-500 border-4 border-[#0b1120] z-20 shadow-[0_0_10px_rgba(16,185,129,0.8)]"></div>
                <div class="w-full md:w-1/2 order-2">
                    <h3 class="text-xl font-bold text-white mb-1">Wali Kelas & Guru</h3>
                    <p class="text-[10px] tracking-[0.2em] text-emerald-400 uppercase font-bold mb-2">Academic Monitor</p>
                    <p class="text-gray-400 text-sm leading-relaxed max-w-sm mx-auto md:mx-0">Memantau aktivitas literasi siswa, mengecek riwayat peminjaman buku kelas, serta memonitor Trust Score dan kedisiplinan siswa didikannya.</p>
                </div>
            </div>

            <div class="group relative flex flex-col md:flex-row items-center gap-6 md:gap-12 text-center md:text-right" data-aos="fade-left">
                <div class="w-full md:w-1/2 order-2 md:order-1">
                    <h3 class="text-xl font-bold text-white mb-1">Siswa (Peminjam)</h3>
                    <p class="text-[10px] tracking-[0.2em] text-rose-400 uppercase font-bold mb-2">Library Member</p>
                    <p class="text-gray-400 text-sm leading-relaxed max-w-sm mx-auto md:ml-auto md:mr-0">Akses personal untuk mengeksplorasi katalog, melakukan request peminjaman mandiri, dan melihat riwayat baca serta Trust Score individu.</p>
                </div>
                <div class="hidden md:flex absolute left-1/2 -translate-x-1/2 w-3 h-3 rounded-full bg-rose-500 border-4 border-[#0b1120] z-20 shadow-[0_0_10px_rgba(244,63,94,0.8)]"></div>
                <div class="w-full md:w-1/2 flex justify-center md:justify-start order-1 md:order-2">
                    <div class="w-16 h-16 rounded-2xl bg-rose-500/10 flex items-center justify-center text-rose-400 border border-rose-500/20 group-hover:bg-rose-500 group-hover:text-white transition-all duration-500 shadow-[0_0_25px_rgba(244,63,94,0.2)]">
                        <i class="bi bi-person-badge text-3xl"></i>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
   <section id="alur" class="py-20 bg-[#0b1120] relative overflow-hidden border-t border-white/5">
    
    <div class="absolute top-0 right-0 w-[400px] h-[400px] bg-indigo-600/5 rounded-full blur-[80px] pointer-events-none"></div>

    <div class="container mx-auto px-8 md:px-16 lg:px-24 max-w-6xl relative z-10">
        
        <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
            
            <div data-aos="fade-right">
                <span class="text-indigo-400 font-bold tracking-widest text-[10px] md:text-xs mb-3 block uppercase">Workflow Sistem</span>
                <h2 class="text-3xl md:text-4xl font-bold font-heading text-white leading-tight mb-8">
                    Proses Peminjaman <br> <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-cyan-400">Terstruktur & Disiplin.</span>
                </h2>
                
                <div class="space-y-6"> 
                    
                    <div class="group flex gap-5 relative">
                        <div class="absolute left-[19px] top-10 bottom-[-24px] w-px bg-indigo-500/20 group-last:hidden"></div>
                        
                        <div class="relative w-10 h-10 rounded-lg bg-[#1e293b] border border-indigo-500/30 flex items-center justify-center text-indigo-400 shrink-0 shadow-[0_0_10px_rgba(99,102,241,0.2)]">
                            <i class="bi bi-book-fill text-lg"></i>
                        </div>
                        <div>
                            <h4 class="text-base font-bold text-white mb-1 group-hover:text-indigo-300 transition-colors">1. Eksplorasi Katalog Universal</h4>
                            <p class="text-xs md:text-sm text-gray-400 leading-relaxed max-w-sm">
                                Sistem menyediakan katalog terbuka. Siswa <span class="text-indigo-300">dapat meminjam buku apa saja</span> yang tersedia di perpustakaan tanpa batasan khusus.
                            </p>
                        </div>
                    </div>

                    <div class="group flex gap-5 relative">
                        <div class="absolute left-[19px] top-10 bottom-[-24px] w-px bg-indigo-500/20"></div>
                        
                        <div class="relative w-10 h-10 rounded-lg bg-[#1e293b] border border-blue-500/30 flex items-center justify-center text-blue-400 shrink-0 shadow-[0_0_10px_rgba(59,130,246,0.2)]">
                            <i class="bi bi-person-badge-fill text-lg"></i>
                        </div>
                        <div>
                            <h4 class="text-base font-bold text-white mb-1 group-hover:text-blue-300 transition-colors">2. Request & Validasi Petugas</h4>
                            <p class="text-xs md:text-sm text-gray-400 leading-relaxed max-w-sm">
                                Siswa kirim request via aplikasi. Petugas memvalidasi, menyiapkan buku, dan <span class="text-blue-300">mencatat serah terima</span> peminjaman.
                            </p>
                        </div>
                    </div>

                    <div class="group flex gap-5 relative">
                        <div class="relative w-10 h-10 rounded-lg bg-[#1e293b] border border-rose-500/30 flex items-center justify-center text-rose-400 shrink-0 shadow-[0_0_10px_rgba(244,63,94,0.2)]">
                            <i class="bi bi-shield-exclamation text-lg"></i>
                        </div>
                        <div>
                            <h4 class="text-base font-bold text-white mb-1 group-hover:text-rose-300 transition-colors">3. Pengembalian & Trust Score</h4>
                            <p class="text-xs md:text-sm text-gray-400 leading-relaxed max-w-sm mb-2">
                                Petugas mengevaluasi status pengembalian buku:
                            </p>
                            <div class="flex flex-wrap gap-2 mb-2">
                                <span class="px-1.5 py-0.5 rounded bg-green-500/10 border border-green-500/20 text-[9px] font-bold text-green-400 uppercase">Tepat Waktu</span>
                                <span class="px-1.5 py-0.5 rounded bg-yellow-500/10 border border-yellow-500/20 text-[9px] font-bold text-yellow-400 uppercase">Telat</span>
                                <span class="px-1.5 py-0.5 rounded bg-red-500/10 border border-red-500/20 text-[9px] font-bold text-red-400 uppercase">Rusak/Hilang</span>
                            </div>
                            <p class="text-[10px] text-rose-300/80 italic border-l-2 border-rose-500/30 pl-2">
                                *Keterlambatan atau kerusakan akan <b>menurunkan Trust Score</b> dan dikenakan sanksi oleh Petugas.
                            </p>
                        </div>
                    </div>

                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 md:gap-4 relative" data-aos="fade-left">
                <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/10 to-purple-500/10 blur-3xl -z-10 rounded-full scale-75"></div>

                <div class="glass p-5 rounded-2xl flex flex-col items-center text-center border border-white/5 hover:border-indigo-500/30 transition-all duration-300">
                    <div class="w-8 h-8 mb-3 rounded-full bg-indigo-500/20 text-indigo-400 flex items-center justify-center">
                        <i class="bi bi-grid-fill text-sm"></i>
                    </div>
                    <p class="text-xl font-bold text-white mb-0.5">Bebas</p>
                    <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest">Akses Semua Buku</p>
                </div>

                <div class="glass p-5 rounded-2xl flex flex-col items-center text-center mt-6 border border-white/5 hover:border-blue-500/30 transition-all duration-300">
                    <div class="w-8 h-8 mb-3 rounded-full bg-blue-500/20 text-blue-400 flex items-center justify-center">
                        <i class="bi bi-check-all text-sm"></i>
                    </div>
                    <p class="text-xl font-bold text-white mb-0.5">100%</p>
                    <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest">Validasi Petugas</p>
                </div>

                <div class="glass p-5 rounded-2xl flex flex-col items-center text-center border border-white/5 hover:border-green-500/30 transition-all duration-300">
                    <div class="w-8 h-8 mb-3 rounded-full bg-green-500/20 text-green-400 flex items-center justify-center">
                        <i class="bi bi-clock-history text-sm"></i>
                    </div>
                    <p class="text-xl font-bold text-white mb-0.5">Realtime</p>
                    <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest">Tracking Status</p>
                </div>

                <div class="glass p-5 rounded-2xl flex flex-col items-center text-center mt-6 border border-white/5 hover:border-rose-500/30 transition-all duration-300 relative overflow-hidden group">
                    <div class="absolute inset-0 bg-rose-500/5 group-hover:bg-rose-500/10 transition-colors"></div>
                    <div class="w-8 h-8 mb-3 rounded-full bg-rose-500/20 text-rose-400 flex items-center justify-center relative z-10">
                        <i class="bi bi-star-half text-sm"></i>
                    </div>
                    <p class="text-xl font-bold text-white mb-0.5 relative z-10">Sistem</p>
                    <p class="text-[9px] font-bold text-rose-300 uppercase tracking-widest relative z-10">Trust Score</p>
                </div>

            </div>
        </div>
    </div>
</section>
    </main>

  <footer class="pt-20 pb-10 bg-[#020617] border-t border-white/5 relative overflow-hidden font-sans">
    
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full max-w-5xl h-[500px] bg-indigo-900/10 rounded-full blur-[120px] pointer-events-none"></div>

    <div class="container mx-auto px-6 lg:px-12 relative z-10">
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 lg:gap-8 mb-16">
            
            <div class="lg:col-span-1">
                <a href="#" class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#1e1b4b] to-indigo-900 border border-indigo-500/30 flex items-center justify-center shadow-lg">
                        <span class="text-white font-bold text-lg font-heading">LB</span>
                    </div>
                    <span class="text-xl font-bold font-heading tracking-tight text-white">
                        Libri<span class="text-indigo-400">fy</span>
                    </span>
                </a>
                <p class="text-gray-400 text-sm leading-relaxed mb-6">
                    Sistem Peminjaman Buku Perpustakaan Terpadu. Memudahkan eksplorasi, peminjaman, dan pelacakan buku secara transparan dan akuntabel.
                </p>
                <div class="flex gap-4">
                    <a href="#" class="w-8 h-8 rounded-full bg-white/5 flex items-center justify-center text-gray-400 hover:bg-indigo-600 hover:text-white transition-all"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="w-8 h-8 rounded-full bg-white/5 flex items-center justify-center text-gray-400 hover:bg-indigo-600 hover:text-white transition-all"><i class="bi bi-globe"></i></a>
                    <a href="#" class="w-8 h-8 rounded-full bg-white/5 flex items-center justify-center text-gray-400 hover:bg-indigo-600 hover:text-white transition-all"><i class="bi bi-github"></i></a>
                </div>
            </div>

            <div class="lg:col-span-2">
                <h4 class="text-white font-bold mb-6">Pusat Bantuan & Akses</h4>
                <div class="p-5 rounded-2xl bg-[#0f172a] border border-white/5 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-20 h-20 bg-indigo-500/10 rounded-full blur-xl group-hover:bg-indigo-500/20 transition-all"></div>
                    
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-lg bg-indigo-500/20 text-indigo-400 flex items-center justify-center shrink-0">
                            <i class="bi bi-shield-lock-fill text-lg"></i>
                        </div>
                        <div>
                            <h5 class="text-white font-bold text-sm mb-1">Butuh Akun / Lupa Password?</h5>
                            <p class="text-xs text-gray-400 leading-relaxed mb-3">
                                Untuk alasan keamanan, pendaftaran akun Siswa & Petugas dilakukan secara terpusat.
                            </p>
                            <p class="text-xs text-indigo-300 font-medium">
                                Silakan hubungi <span class="text-white font-bold underline decoration-indigo-500/50">Petugas Perpustakaan</span> atau <span class="text-white font-bold underline decoration-indigo-500/50">Admin Sekolah</span> untuk mendapatkan akses login.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-1 lg:pl-8">
                <h4 class="text-white font-bold mb-6">Menu Pintas</h4>
                <ul class="space-y-3">
                    <li><a href="#home" class="text-sm text-gray-400 hover:text-indigo-400 transition-colors">Beranda</a></li>
                    <li><a href="#katalog" class="text-sm text-gray-400 hover:text-indigo-400 transition-colors">Katalog Buku</a></li>
                    <li><a href="#alur" class="text-sm text-gray-400 hover:text-indigo-400 transition-colors">Alur Peminjaman</a></li>
                    <li><a href="#trust-score" class="text-sm text-gray-400 hover:text-indigo-400 transition-colors">Cek Trust Score</a></li>
                    <li><a href="{{ route('login') }}" class="text-sm text-gray-400 hover:text-indigo-400 transition-colors">Login Petugas</a></li>
                </ul>
            </div>

        </div>

        <div class="border-t border-white/5 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
            <p class="text-gray-500 text-xs text-center md:text-left">
                &copy; {{ date('Y') }} Librify. All rights reserved. <br class="md:hidden">
                Developed by <span class="text-gray-400 font-bold">Librify Team.</span>
            </p>
            <div class="flex gap-6">
                <a href="#" class="text-xs text-gray-500 hover:text-white transition-colors">Kebijakan Privasi</a>
                <a href="#" class="text-xs text-gray-500 hover:text-white transition-colors">Syarat & Ketentuan</a>
            </div>
        </div>

    </div>
</footer>
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script>
        AOS.init({ duration: 800, once: true });
        VanillaTilt.init(document.querySelectorAll("[data-tilt]"), { max: 15, speed: 400, glare: true, "max-glare": 0.2 });
    </script>
</body>
</html>