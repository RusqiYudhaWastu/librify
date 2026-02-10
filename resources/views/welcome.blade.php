<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>TekniLog | Digital Workshop Management SMK</title>

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
            'w-[92%] md:w-[85%] lg:max-w-6xl bg-[#0f172a]/80 backdrop-blur-xl rounded-full border-white/10 shadow-[0_8px_32px_rgba(0,0,0,0.25)] py-2.5 px-4 md:px-6': scrolled, 
            'w-full bg-transparent border-transparent px-6 lg:px-12': !scrolled 
        }"
    >
        
        <a href="#" class="flex items-center gap-3 group shrink-0">
            <div class="relative w-10 h-10 rounded-full bg-gradient-to-br from-[#1e1b4b] to-indigo-900 border border-indigo-500/30 flex items-center justify-center shadow-lg group-hover:scale-105 transition-transform duration-300 overflow-hidden">
                <div class="absolute inset-0 bg-indigo-500/20 blur-md"></div>
                <span class="relative text-white font-bold text-lg font-heading">TL</span>
            </div>
            <div class="flex flex-col">
                <span class="text-xl font-bold font-heading tracking-tight text-white leading-none group-hover:text-indigo-200 transition-colors">
                    Tekni<span class="text-indigo-400">Log</span>
                </span>
                <span class="text-[9px] text-gray-400 tracking-[0.2em] uppercase mt-0.5" 
                      :class="{ 'hidden sm:block': scrolled }">
                    Digital Workshop
                </span>
            </div>
        </a>

        <nav class="hidden md:flex items-center justify-center absolute left-1/2 -translate-x-1/2">
            <div class="flex items-center gap-1 p-1 rounded-full bg-white/5 border border-white/5 backdrop-blur-sm shadow-inner">
                <a href="#home" class="px-4 py-1.5 rounded-full text-sm font-medium text-gray-300 hover:text-white hover:bg-white/10 transition-all duration-300">Beranda</a>
                <a href="#jurusan" class="px-4 py-1.5 rounded-full text-sm font-medium text-gray-300 hover:text-white hover:bg-white/10 transition-all duration-300">Jurusan</a>
                <a href="#alur" class="px-4 py-1.5 rounded-full text-sm font-medium text-gray-300 hover:text-white hover:bg-white/10 transition-all duration-300">Alur</a>
            </div>
        </nav>

        <div class="flex items-center gap-4 shrink-0">
            <a href="{{ route('login') }}" 
               class="hidden md:flex relative group px-5 py-2 rounded-full overflow-hidden shadow-lg shadow-indigo-500/20 transition-all duration-300 hover:shadow-indigo-500/40 hover:-translate-y-0.5">
                <div class="absolute inset-0 bg-gradient-to-r from-indigo-600 to-purple-600 group-hover:scale-110 transition-transform duration-500"></div>
                <span class="relative flex items-center gap-2 text-sm font-bold text-white">
                    <i class="bi bi-person-circle text-xs"></i> 
                    <span>Login</span>
                </span>
            </a>

            <button @click="mobileOpen = !mobileOpen" class="md:hidden relative w-10 h-10 flex items-center justify-center rounded-full bg-white/5 hover:bg-white/10 text-white transition-colors focus:outline-none">
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
         class="absolute top-full mt-2 w-[90%] max-w-sm bg-[#0f172a]/95 backdrop-blur-xl border border-white/10 rounded-2xl p-4 shadow-2xl flex flex-col gap-2 md:hidden origin-top"
         style="display: none;">
        
        <a href="#home" class="p-3 rounded-xl text-gray-300 hover:bg-white/5 hover:text-white flex items-center gap-3 transition-colors">
            <i class="bi bi-house-door text-indigo-400"></i> Beranda
        </a>
        <a href="#jurusan" class="p-3 rounded-xl text-gray-300 hover:bg-white/5 hover:text-white flex items-center gap-3 transition-colors">
            <i class="bi bi-mortarboard text-indigo-400"></i> Jurusan
        </a>
        <a href="#alur" class="p-3 rounded-xl text-gray-300 hover:bg-white/5 hover:text-white flex items-center gap-3 transition-colors">
            <i class="bi bi-diagram-3 text-indigo-400"></i> Alur
        </a>
        <a href="#katalog" class="p-3 rounded-xl text-gray-300 hover:bg-white/5 hover:text-white flex items-center gap-3 transition-colors">
            <i class="bi bi-grid text-indigo-400"></i> Katalog
        </a>
        
        <div class="h-px bg-white/10 my-1"></div>
        
        <a href="{{ route('login') }}" class="p-3 rounded-xl bg-indigo-600/20 text-indigo-300 hover:bg-indigo-600 hover:text-white flex items-center justify-center gap-2 font-bold transition-all">
            <i class="bi bi-box-arrow-in-right"></i> Portal Login
        </a>
    </div>

</header>

    <main>
     <section id="home" class="relative pt-32 pb-20 lg:pt-40 lg:pb-32 flex items-center min-h-screen overflow-hidden">
    
    <div class="absolute top-0 left-0 w-[400px] h-[400px] bg-indigo-600/10 rounded-full blur-[100px] -z-10 translate-x-[-30%] translate-y-[-20%]"></div>
    <div class="absolute bottom-0 right-0 w-[400px] h-[400px] bg-purple-600/10 rounded-full blur-[100px] -z-10 translate-x-[20%] translate-y-[20%]"></div>

    <div class="container mx-auto max-w-6xl px-8 md:px-12 lg:px-16 relative z-10">
        
        <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
            
            <div data-aos="fade-right" class="text-center lg:text-left mx-auto lg:mx-0 max-w-xl">
                
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-indigo-500/10 border border-indigo-500/20 text-[10px] md:text-xs font-bold text-indigo-300 mb-6 justify-center lg:justify-start">
                    <span class="flex h-2 w-2 relative">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-indigo-500"></span>
                    </span>
                    <span>Live System v2.0</span>
                </div>

                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold font-heading leading-[1.15] mb-6 tracking-tight text-white">
                    Kelola Alat <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-purple-400">Tanpa Ribet.</span>
                </h1>
                
                <p class="text-gray-400 text-sm md:text-base leading-relaxed mb-8 border-l-0 lg:border-l-2 border-indigo-500/30 lg:pl-4 max-w-lg mx-auto lg:mx-0">
                    Sistem inventaris terintegrasi untuk Siswa dan Toolman. Pinjam barang pakai QR Code, cek stok real-time, dan laporan otomatis.
                </p>

                <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4">
                    <a href="#katalog" class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-500 text-white px-8 py-3 rounded-full font-bold shadow-lg shadow-indigo-500/25 flex items-center justify-center gap-2 transition-all hover:-translate-y-1 text-sm">
                        Mulai Pinjam <i class="bi bi-arrow-right"></i>
                    </a>
                    <a href="#alur" class="w-full sm:w-auto px-8 py-3 rounded-full text-gray-300 font-semibold border border-white/10 hover:bg-white/5 hover:text-white transition-all text-sm flex items-center justify-center gap-2">
                        <i class="bi bi-play-circle"></i> Demo
                    </a>
                </div>

                <div class="mt-10 flex items-center justify-center lg:justify-start gap-8 pt-6 border-t border-white/5">
                    <div>
                        <h4 class="text-2xl font-bold text-white">1,250+</h4>
                        <p class="text-[10px] text-gray-500 uppercase tracking-wider">Total Alat</p>
                    </div>
                    <div class="w-px h-8 bg-white/10"></div>
                    <div>
                        <h4 class="text-2xl font-bold text-white">100%</h4>
                        <p class="text-[10px] text-gray-500 uppercase tracking-wider">Digital</p>
                    </div>
                </div>
            </div>

            <div class="relative flex justify-center items-center py-10 lg:py-0" data-aos="fade-left" data-aos-delay="200">
                
                <div class="relative w-[320px] h-[320px] md:w-[400px] md:h-[400px]">
                    
                    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-40 h-40 bg-indigo-500/20 rounded-full blur-3xl animate-pulse"></div>

                    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-20">
                        <div class="glass p-6 rounded-3xl border border-indigo-500/40 shadow-[0_0_50px_rgba(99,102,241,0.3)] bg-[#0f172a]/80 backdrop-blur-xl flex flex-col items-center">
                            <i class="bi bi-qr-code-scan text-5xl text-white mb-2"></i>
                            <span class="text-[10px] font-bold text-indigo-300 uppercase tracking-widest">System Core</span>
                        </div>
                    </div>

                    <div class="absolute top-0 left-4 md:left-10 animate-[float_6s_ease-in-out_infinite]">
                        <div class="glass p-4 rounded-2xl border border-white/10 bg-[#1e293b]/90 shadow-xl flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-blue-500/20 flex items-center justify-center text-blue-400">
                                <i class="bi bi-laptop text-xl"></i>
                            </div>
                            <div class="hidden md:block">
                                <p class="text-[10px] text-gray-400">RPL</p>
                                <p class="text-xs font-bold text-white">Macbook</p>
                            </div>
                        </div>
                    </div>

                    <div class="absolute top-10 right-0 animate-[float_5s_ease-in-out_infinite_1s]">
                        <div class="glass p-4 rounded-2xl border border-white/10 bg-[#1e293b]/90 shadow-xl flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-rose-500/20 flex items-center justify-center text-rose-400">
                                <i class="bi bi-camera-reels text-xl"></i>
                            </div>
                            </div>
                    </div>

                    <div class="absolute bottom-10 left-0 animate-[float_7s_ease-in-out_infinite_0.5s]">
                        <div class="glass p-4 rounded-2xl border border-white/10 bg-[#1e293b]/90 shadow-xl flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-amber-500/20 flex items-center justify-center text-amber-400">
                                <i class="bi bi-wrench-adjustable text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="absolute bottom-0 right-10 animate-[float_6s_ease-in-out_infinite_2s]">
                        <div class="glass p-4 rounded-2xl border border-white/10 bg-[#1e293b]/90 shadow-xl flex items-center gap-3">
                            <div class="hidden md:block text-right">
                                <p class="text-[10px] text-gray-400">Las</p>
                                <p class="text-xs font-bold text-white">Welding</p>
                            </div>
                            <div class="w-10 h-10 rounded-lg bg-cyan-500/20 flex items-center justify-center text-cyan-400">
                                <i class="bi bi-fire text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[280px] h-[280px] border border-dashed border-white/10 rounded-full animate-[spin_20s_linear_infinite] -z-10"></div>
                </div>
            </div>

        </div>
    </div>
</section>

<style>
@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-15px); }
}
@keyframes spin {
    from { transform: translate(-50%, -50%) rotate(0deg); }
    to { transform: translate(-50%, -50%) rotate(360deg); }
}
</style>

       <section id="jurusan" class="py-20 relative overflow-hidden bg-[#0b1120]">
    
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-0 left-1/4 w-[400px] h-[400px] bg-indigo-600/10 rounded-full blur-[100px] mix-blend-screen"></div>
        <div class="absolute bottom-0 right-1/4 w-[400px] h-[400px] bg-blue-600/10 rounded-full blur-[100px] mix-blend-screen"></div>
    </div>

    <div class="container mx-auto px-6 relative z-10">
        
        <div class="text-center max-w-2xl mx-auto mb-12" data-aos="fade-up">
            <span class="inline-block py-1 px-3 rounded-full bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 text-[10px] font-bold tracking-widest uppercase mb-3">
                Kompetensi Keahlian
            </span>
            <h2 class="text-3xl font-bold text-white mb-3">
                Fasilitas & Jurusan
            </h2>
            <p class="text-gray-400 text-sm leading-relaxed max-w-lg mx-auto">
                Pilihan jurusan dengan standar industri dan fasilitas lengkap.
            </p>
        </div>

        <div class="flex flex-wrap justify-center gap-5">

            <div class="group w-full md:w-[calc(50%-1.25rem)] lg:w-[30%]" data-aos="fade-up">
                <div class="h-full bg-[#111827] rounded-2xl p-6 border border-white/5 hover:border-indigo-500/50 transition-all duration-300 relative overflow-hidden flex flex-col items-center text-center shadow-lg hover:shadow-indigo-500/20 hover:-translate-y-1">
                    <div class="absolute top-0 w-16 h-[2px] bg-indigo-500 shadow-[0_0_15px_rgba(99,102,241,0.7)]"></div>
                    
                    <div class="w-14 h-14 rounded-xl bg-[#1e293b] flex items-center justify-center mb-4 text-gray-400 group-hover:text-white group-hover:scale-105 transition-all duration-300 border border-white/5 shadow-inner">
                        <i class="bi bi-code-square text-2xl"></i>
                    </div>
                    
                    <h3 class="text-lg font-bold text-white mb-1">PPLG / RPL</h3>
                    <p class="text-[10px] font-medium text-indigo-400 uppercase tracking-widest mb-3">Software Engineering</p>
                    
                    <p class="text-xs text-gray-400 leading-relaxed">
                        Pengembangan software, aplikasi mobile, dan website dengan lab spek tinggi.
                    </p>
                </div>
            </div>

            <div class="group w-full md:w-[calc(50%-1.25rem)] lg:w-[30%]" data-aos="fade-up" data-aos-delay="100">
                <div class="h-full bg-[#111827] rounded-2xl p-6 border border-white/5 hover:border-rose-500/50 transition-all duration-300 relative overflow-hidden flex flex-col items-center text-center shadow-lg hover:shadow-rose-500/20 hover:-translate-y-1">
                    <div class="absolute top-0 w-16 h-[2px] bg-rose-500 shadow-[0_0_15px_rgba(244,63,94,0.7)]"></div>
                    
                    <div class="w-14 h-14 rounded-xl bg-[#1e293b] flex items-center justify-center mb-4 text-gray-400 group-hover:text-white group-hover:scale-105 transition-all duration-300 border border-white/5 shadow-inner">
                        <i class="bi bi-camera-reels text-2xl"></i>
                    </div>
                    
                    <h3 class="text-lg font-bold text-white mb-1">Broadcast</h3>
                    <p class="text-[10px] font-medium text-rose-400 uppercase tracking-widest mb-3">TV & Film Production</p>
                    
                    <p class="text-xs text-gray-400 leading-relaxed">
                        Studio kreatif untuk videografi, fotografi, tata cahaya, dan editing TV.
                    </p>
                </div>
            </div>

            <div class="group w-full md:w-[calc(50%-1.25rem)] lg:w-[30%]" data-aos="fade-up" data-aos-delay="200">
                <div class="h-full bg-[#111827] rounded-2xl p-6 border border-white/5 hover:border-purple-500/50 transition-all duration-300 relative overflow-hidden flex flex-col items-center text-center shadow-lg hover:shadow-purple-500/20 hover:-translate-y-1">
                    <div class="absolute top-0 w-16 h-[2px] bg-purple-500 shadow-[0_0_15px_rgba(168,85,247,0.7)]"></div>
                    
                    <div class="w-14 h-14 rounded-xl bg-[#1e293b] flex items-center justify-center mb-4 text-gray-400 group-hover:text-white group-hover:scale-105 transition-all duration-300 border border-white/5 shadow-inner">
                        <i class="bi bi-palette text-2xl"></i>
                    </div>
                    
                    <h3 class="text-lg font-bold text-white mb-1">Animasi 3D</h3>
                    <p class="text-[10px] font-medium text-purple-400 uppercase tracking-widest mb-3">Creative Arts</p>
                    
                    <p class="text-xs text-gray-400 leading-relaxed">
                        Rendering 3D, motion graphic, dan visual effect dengan perangkat presisi.
                    </p>
                </div>
            </div>

            <div class="group w-full md:w-[calc(50%-1.25rem)] lg:w-[30%]" data-aos="fade-up" data-aos-delay="300">
                <div class="h-full bg-[#111827] rounded-2xl p-6 border border-white/5 hover:border-amber-500/50 transition-all duration-300 relative overflow-hidden flex flex-col items-center text-center shadow-lg hover:shadow-amber-500/20 hover:-translate-y-1">
                    <div class="absolute top-0 w-16 h-[2px] bg-amber-500 shadow-[0_0_15px_rgba(245,158,11,0.7)]"></div>
                    
                    <div class="w-14 h-14 rounded-xl bg-[#1e293b] flex items-center justify-center mb-4 text-gray-400 group-hover:text-white group-hover:scale-105 transition-all duration-300 border border-white/5 shadow-inner">
                        <i class="bi bi-wrench-adjustable text-2xl"></i>
                    </div>
                    
                    <h3 class="text-lg font-bold text-white mb-1">Teknik Otomotif</h3>
                    <p class="text-[10px] font-medium text-amber-400 uppercase tracking-widest mb-3">Vehicle Engineering</p>
                    
                    <p class="text-xs text-gray-400 leading-relaxed">
                        Perawatan mesin, sistem injeksi, dan kelistrikan kendaraan ringan.
                    </p>
                </div>
            </div>

            <div class="group w-full md:w-[calc(50%-1.25rem)] lg:w-[30%]" data-aos="fade-up" data-aos-delay="400">
                <div class="h-full bg-[#111827] rounded-2xl p-6 border border-white/5 hover:border-cyan-500/50 transition-all duration-300 relative overflow-hidden flex flex-col items-center text-center shadow-lg hover:shadow-cyan-500/20 hover:-translate-y-1">
                    <div class="absolute top-0 w-16 h-[2px] bg-cyan-500 shadow-[0_0_15px_rgba(6,182,212,0.7)]"></div>
                    
                    <div class="w-14 h-14 rounded-xl bg-[#1e293b] flex items-center justify-center mb-4 text-gray-400 group-hover:text-white group-hover:scale-105 transition-all duration-300 border border-white/5 shadow-inner">
                        <i class="bi bi-fire text-2xl"></i>
                    </div>
                    
                    <h3 class="text-lg font-bold text-white mb-1">Teknik Pengelasan</h3>
                    <p class="text-[10px] font-medium text-cyan-400 uppercase tracking-widest mb-3">Welding Engineering</p>
                    
                    <p class="text-xs text-gray-400 leading-relaxed">
                        Fabrikasi logam dan konstruksi baja dengan metode las modern.
                    </p>
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
                
                <div class="space-y-6"> <div class="group flex gap-5 relative">
                        <div class="absolute left-[19px] top-10 bottom-[-24px] w-px bg-indigo-500/20 group-last:hidden"></div>
                        
                        <div class="relative w-10 h-10 rounded-lg bg-[#1e293b] border border-indigo-500/30 flex items-center justify-center text-indigo-400 shrink-0 shadow-[0_0_10px_rgba(99,102,241,0.2)]">
                            <i class="bi bi-shield-lock-fill text-lg"></i>
                        </div>
                        <div>
                            <h4 class="text-base font-bold text-white mb-1 group-hover:text-indigo-300 transition-colors">1. Akses Spesifik Jurusan</h4>
                            <p class="text-xs md:text-sm text-gray-400 leading-relaxed max-w-sm">
                                Sistem memfilter katalog. Siswa <span class="text-indigo-300">hanya bisa meminjam alat</span> sesuai kompetensi keahlian masing-masing.
                            </p>
                        </div>
                    </div>

                    <div class="group flex gap-5 relative">
                        <div class="absolute left-[19px] top-10 bottom-[-24px] w-px bg-indigo-500/20"></div>
                        
                        <div class="relative w-10 h-10 rounded-lg bg-[#1e293b] border border-blue-500/30 flex items-center justify-center text-blue-400 shrink-0 shadow-[0_0_10px_rgba(59,130,246,0.2)]">
                            <i class="bi bi-person-badge-fill text-lg"></i>
                        </div>
                        <div>
                            <h4 class="text-base font-bold text-white mb-1 group-hover:text-blue-300 transition-colors">2. Request & Validasi Toolman</h4>
                            <p class="text-xs md:text-sm text-gray-400 leading-relaxed max-w-sm">
                                Siswa kirim request via aplikasi. Toolman validasi, siapkan barang, dan <span class="text-blue-300">scan serah terima</span>.
                            </p>
                        </div>
                    </div>

                    <div class="group flex gap-5 relative">
                        <div class="relative w-10 h-10 rounded-lg bg-[#1e293b] border border-rose-500/30 flex items-center justify-center text-rose-400 shrink-0 shadow-[0_0_10px_rgba(244,63,94,0.2)]">
                            <i class="bi bi-exclamation-triangle-fill text-lg"></i>
                        </div>
                        <div>
                            <h4 class="text-base font-bold text-white mb-1 group-hover:text-rose-300 transition-colors">3. Cek Kondisi & Sanksi</h4>
                            <p class="text-xs md:text-sm text-gray-400 leading-relaxed max-w-sm mb-2">
                                Toolman menetapkan 3 status kondisi barang:
                            </p>
                            <div class="flex flex-wrap gap-2 mb-2">
                                <span class="px-1.5 py-0.5 rounded bg-green-500/10 border border-green-500/20 text-[9px] font-bold text-green-400 uppercase">Aman</span>
                                <span class="px-1.5 py-0.5 rounded bg-yellow-500/10 border border-yellow-500/20 text-[9px] font-bold text-yellow-400 uppercase">Rusak</span>
                                <span class="px-1.5 py-0.5 rounded bg-red-500/10 border border-red-500/20 text-[9px] font-bold text-red-400 uppercase">Hilang</span>
                            </div>
                            <p class="text-[10px] text-rose-300/80 italic border-l-2 border-rose-500/30 pl-2">
                                *Rusak/Hilang = <b>Denda Kolektif</b> dibebankan ke Kelas.
                            </p>
                        </div>
                    </div>

                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 md:gap-4 relative" data-aos="fade-left">
                <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/10 to-purple-500/10 blur-3xl -z-10 rounded-full scale-75"></div>

                <div class="glass p-5 rounded-2xl flex flex-col items-center text-center border border-white/5 hover:border-indigo-500/30 transition-all duration-300">
                    <div class="w-8 h-8 mb-3 rounded-full bg-indigo-500/20 text-indigo-400 flex items-center justify-center">
                        <i class="bi bi-funnel text-sm"></i>
                    </div>
                    <p class="text-xl font-bold text-white mb-0.5">Auto</p>
                    <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest">Filter Jurusan</p>
                </div>

                <div class="glass p-5 rounded-2xl flex flex-col items-center text-center mt-6 border border-white/5 hover:border-blue-500/30 transition-all duration-300">
                    <div class="w-8 h-8 mb-3 rounded-full bg-blue-500/20 text-blue-400 flex items-center justify-center">
                        <i class="bi bi-check2-circle text-sm"></i>
                    </div>
                    <p class="text-xl font-bold text-white mb-0.5">100%</p>
                    <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest">Validasi Toolman</p>
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
                        <i class="bi bi-cash-coin text-sm"></i>
                    </div>
                    <p class="text-xl font-bold text-white mb-0.5 relative z-10">Sanksi</p>
                    <p class="text-[9px] font-bold text-rose-300 uppercase tracking-widest relative z-10">Denda Per Kelas</p>
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
                        <span class="text-white font-bold text-lg font-heading">TL</span>
                    </div>
                    <span class="text-xl font-bold font-heading tracking-tight text-white">
                        Tekni<span class="text-indigo-400">Log</span>
                    </span>
                </a>
                <p class="text-gray-400 text-sm leading-relaxed mb-6">
                    Sistem Manajemen Inventaris Alat & Bahan Praktek SMK Berbasis QR Code. Terintegrasi, transparan, dan akuntabel.
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
                                Untuk alasan keamanan, pendaftaran akun Siswa & Toolman dilakukan secara terpusat.
                            </p>
                            <p class="text-xs text-indigo-300 font-medium">
                                Silakan hubungi <span class="text-white font-bold underline decoration-indigo-500/50">Kepala Program Keahlian (Kaprog)</span> atau <span class="text-white font-bold underline decoration-indigo-500/50">Guru Produktif</span> jurusan masing-masing untuk mendapatkan akses login.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-1 lg:pl-8">
                <h4 class="text-white font-bold mb-6">Menu Pintas</h4>
                <ul class="space-y-3">
                    <li><a href="#home" class="text-sm text-gray-400 hover:text-indigo-400 transition-colors">Beranda</a></li>
                    <li><a href="#jurusan" class="text-sm text-gray-400 hover:text-indigo-400 transition-colors">Daftar Jurusan</a></li>
                    <li><a href="#alur" class="text-sm text-gray-400 hover:text-indigo-400 transition-colors">Alur Peminjaman</a></li>
                    <li><a href="#katalog" class="text-sm text-gray-400 hover:text-indigo-400 transition-colors">Cek Katalog Alat</a></li>
                    <li><a href="{{ route('login') }}" class="text-sm text-gray-400 hover:text-indigo-400 transition-colors">Login Staff</a></li>
                </ul>
            </div>

        </div>

        <div class="border-t border-white/5 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
            <p class="text-gray-500 text-xs text-center md:text-left">
                &copy; {{ date('Y') }} TekniLog SMK. All rights reserved. <br class="md:hidden">
                Developed by <span class="text-gray-400 font-bold">RPL Students.</span>
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