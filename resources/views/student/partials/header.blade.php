<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
    
    body { font-family: 'Inter', sans-serif; }
    .font-jakarta { font-family: 'Plus Jakarta Sans', sans-serif; }
    [x-cloak] { display: none !important; }

    .header-floating {
        backdrop-filter: blur(12px);
        background-color: rgba(255, 255, 255, 0.9);
    }
    
    .notif-scroll::-webkit-scrollbar { width: 4px; }
    .notif-scroll::-webkit-scrollbar-thumb { background-color: #a5f3fc; border-radius: 10px; } /* Cyan Scrollbar */
</style>

{{-- Wrapper Utama --}}
<div x-data="{ 
    profileOpen: false, 
    notifOpen: false, 
    modalLogout: false,
    currentDate: '',
    init() {
        this.currentDate = new Date().toLocaleDateString('id-ID', { 
            weekday: 'long', 
            day: 'numeric', 
            month: 'long', 
            year: 'numeric' 
        });
    }
}" class="sticky top-0 z-40 w-full px-6 pt-6 pb-2 pointer-events-none">
    
    <header class="header-floating pointer-events-auto mx-auto max-w-[1550px] rounded-[2.5rem] shadow-[0_20px_50px_-15px_rgba(0,0,0,0.05)] border border-white/50 flex justify-between items-center px-10 py-4 transition-all duration-300">
        
        {{-- 1. BRAND SECTION (CYAN THEME) --}}
        <div class="flex items-center gap-4 flex-shrink-0">
            <div class="w-12 h-12 rounded-2xl bg-cyan-500 text-white flex items-center justify-center shadow-lg shadow-cyan-200">
                <i class="bi bi-mortarboard-fill text-xl"></i>
            </div>
            <div class="leading-tight hidden sm:block text-left">
                <h1 class="text-lg font-black text-gray-900 tracking-tight font-jakarta uppercase leading-none">TekniLog</h1>
                <p class="text-[9px] font-bold text-cyan-500 uppercase tracking-[0.2em] mt-1.5">Student Portal</p>
            </div>
        </div>

        {{-- 2. DATE CENTER --}}
        <div class="flex flex-col items-center flex-1">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-0.5">Hari Ini</p>
            <p class="text-sm font-black text-gray-800 font-jakarta leading-none" x-text="currentDate"></p>
        </div>

        {{-- 3. ACTION SECTION --}}
        <div class="flex items-center gap-4 flex-shrink-0">
            
            {{-- Notifikasi (DINAMIS) --}}
            <div class="relative">
                <button @click="notifOpen = !notifOpen" class="w-12 h-12 flex items-center justify-center rounded-2xl bg-white text-gray-400 border border-gray-100 hover:text-cyan-600 hover:border-cyan-100 transition-all shadow-sm relative">
                    <i class="bi bi-bell-fill text-lg"></i>
                    
                    {{-- Badge Count --}}
                    @if(Auth::user()->unreadNotifications->count() > 0)
                        <span class="absolute top-3 right-3 h-2 w-2 bg-orange-500 border-2 border-white rounded-full"></span>
                    @endif
                </button>
                
                <div x-show="notifOpen" @click.away="notifOpen = false" x-cloak x-transition.origin.top.right 
                     class="absolute right-0 mt-4 w-80 bg-white rounded-[2.5rem] shadow-2xl border border-gray-100 overflow-hidden z-50 text-left">
                    
                    <div class="p-6 border-b border-gray-50 bg-gray-50/50 flex justify-between items-center">
                        <span class="text-[10px] font-black text-gray-800 uppercase tracking-widest">Notifikasi</span>
                        @if(Auth::user()->unreadNotifications->count() > 0)
                            <a href="{{ route('notifications.markAllRead') }}" class="text-[9px] font-bold text-cyan-600 hover:text-cyan-800 uppercase tracking-wider">Tandai Baca</a>
                        @else
                            <span class="bg-cyan-100 text-cyan-600 text-[9px] font-black px-2 py-1 rounded-lg">SISWA</span>
                        @endif
                    </div>

                    <div class="max-h-[300px] overflow-y-auto notif-scroll">
                        @forelse(Auth::user()->notifications as $notification)
                            <a href="{{ route('notifications.read', $notification->id) }}" class="block p-4 border-b border-gray-50 hover:bg-cyan-50/30 transition-colors {{ $notification->read_at ? '' : 'bg-cyan-50/50' }}">
                                <div class="flex gap-3 text-left">
                                    <div class="flex-shrink-0 mt-1">
                                        <i class="bi bi-info-circle-fill text-cyan-500"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-[11px] font-bold text-gray-800 leading-tight mb-1">{{ $notification->data['title'] ?? 'Info Sistem' }}</p>
                                        <p class="text-[10px] text-gray-500 leading-relaxed">{{ $notification->data['message'] ?? '' }}</p>
                                        <p class="text-[9px] text-gray-400 mt-2 font-medium">{{ $notification->created_at->diffForHumans() }}</p>
                                    </div>
                                    @if(!$notification->read_at)
                                        <div class="flex-shrink-0 mt-2">
                                            <span class="block w-1.5 h-1.5 bg-cyan-500 rounded-full"></span>
                                        </div>
                                    @endif
                                </div>
                            </a>
                        @empty
                            <div class="p-8 text-center">
                                <i class="bi bi-bell-slash text-2xl text-gray-300 mb-2 block"></i>
                                <p class="text-[11px] text-gray-400 font-medium">Belum ada notifikasi baru.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- User Profile (Student) --}}
            <div class="relative">
                <button @click="profileOpen = !profileOpen" class="flex items-center gap-3 pl-2 pr-5 py-2 rounded-[1.8rem] bg-white border border-gray-100 shadow-sm hover:border-cyan-100 transition-all group">
                    {{-- FOTO PROFIL --}}
                    <img src="{{ Auth::user()->profile_photo_url }}" class="w-9 h-9 rounded-xl object-cover shadow-sm border border-cyan-50" alt="{{ Auth::user()->name }}">
                    
                    <div class="hidden sm:block text-left leading-none">
                        <p class="text-xs font-black text-gray-900 uppercase tracking-tight">{{ Str::limit(Auth::user()->name, 10) }}</p>
                        <p class="text-[8px] font-bold text-cyan-500 tracking-widest uppercase mt-1">Student</p>
                    </div>
                    <i class="bi bi-chevron-down text-[10px] text-gray-400 ml-1 group-hover:text-cyan-600"></i>
                </button>

                <div x-show="profileOpen" @click.away="profileOpen = false" x-cloak x-transition.origin.top.right 
                     class="absolute right-0 mt-4 w-64 bg-white rounded-[2.5rem] shadow-2xl border border-gray-100 overflow-hidden z-50 text-left">
                    <div class="p-8 text-center border-b border-gray-50 bg-cyan-50/20">
                        {{-- FOTO BESAR --}}
                        <img src="{{ Auth::user()->profile_photo_url }}" class="w-16 h-16 rounded-[1.5rem] object-cover mx-auto mb-4 shadow-lg border-4 border-white" alt="{{ Auth::user()->name }}">
                        
                        <h4 class="font-black text-gray-900 text-sm uppercase leading-tight">{{ Auth::user()->name }}</h4>
                        <div class="mt-2">
                            <span class="px-2 py-1 rounded-lg bg-white border border-cyan-100 text-[8px] font-bold text-cyan-600 uppercase shadow-sm">
                                {{ Auth::user()->classRoom->name ?? 'No Class' }}
                            </span>
                        </div>
                    </div>
                    <div class="p-3 space-y-1">
                        {{-- ✅ LINK EDIT PROFILE (ADDED) --}}
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-5 py-4 text-xs font-bold text-gray-600 rounded-2xl hover:bg-cyan-50 hover:text-cyan-600 transition-all">
                            <i class="bi bi-person-badge text-base"></i> Akun Saya
                        </a>

                        <button @click="modalLogout = true; profileOpen = false" class="w-full flex items-center gap-3 px-5 py-4 text-xs font-bold text-red-500 rounded-2xl hover:bg-red-50 transition-all text-left">
                            <i class="bi bi-box-arrow-right text-base"></i> Keluar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </header>

    {{-- MODAL LOGOUT --}}
    <div x-show="modalLogout" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4 pointer-events-auto text-left">
        <div x-show="modalLogout" x-transition.opacity @click="modalLogout = false" class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm"></div>
        <div x-show="modalLogout" x-transition.scale.95 class="relative w-full max-w-sm bg-white rounded-[3rem] shadow-2xl p-10 text-center border border-white">
            <div class="w-20 h-20 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner">
                <i class="bi bi-power text-3xl"></i>
            </div>
            <h3 class="text-2xl font-black text-gray-900 mb-3 font-jakarta uppercase leading-none">Akhiri Sesi?</h3>
            <p class="text-sm text-gray-500 mb-10 leading-relaxed font-medium">Apakah Anda yakin ingin keluar dari akun siswa?</p>
            <div class="flex gap-4 leading-none">
                <button @click="modalLogout = false" class="flex-1 px-6 py-4 rounded-2xl bg-gray-100 text-gray-600 font-bold text-xs uppercase tracking-widest hover:bg-gray-200 transition-all">
                    Batal
                </button>
                <form action="{{ route('logout') }}" method="POST" class="flex-1">
                    @csrf
                    <button type="submit" class="w-full px-6 py-4 rounded-2xl bg-[#EF4444] text-white font-bold text-xs uppercase tracking-widest shadow-xl shadow-red-200 hover:bg-red-600 transition-all active:scale-95">
                        Ya, Keluar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>