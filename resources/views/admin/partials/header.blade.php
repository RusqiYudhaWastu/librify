<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
    
    body { font-family: 'Inter', sans-serif; }
    .font-jakarta { font-family: 'Plus Jakarta Sans', sans-serif; }
    [x-cloak] { display: none !important; }

    .header-floating {
        backdrop-filter: blur(12px);
        background-color: rgba(255, 255, 255, 0.9);
    }
    
    /* Scrollbar Smooth untuk Notifikasi */
    .notif-scroll::-webkit-scrollbar { width: 4px; }
    .notif-scroll::-webkit-scrollbar-thumb { background-color: #c7d2fe; border-radius: 10px; }
</style>

{{-- Wrapper Utama --}}
<div x-data="{ 
    profileOpen: false, 
    notifOpen: false, 
    modalLogout: false,
    currentDate: '',
    init() {
        this.currentDate = new Date().toLocaleDateString('id-ID', { 
            weekday: 'short', 
            day: 'numeric', 
            month: 'short', 
            year: 'numeric' 
        });
    }
}" class="sticky top-0 z-40 w-full px-3 md:px-6 pt-4 md:pt-6 pb-2 pointer-events-none">
    
    <header class="header-floating pointer-events-auto mx-auto max-w-[1550px] rounded-2xl md:rounded-[2.5rem] shadow-[0_10px_40px_-15px_rgba(0,0,0,0.08)] border border-white/50 flex justify-between items-center px-4 md:px-10 py-3 md:py-4 transition-all duration-300">
        
        {{-- 1. BRAND SECTION (Updated dengan TOMBOL MENU) --}}
        <div class="flex items-center gap-3 md:gap-4 flex-shrink-0">
            
            {{-- TOMBOL HAMBURGER (Hanya muncul di Mobile) --}}
            <button type="button" @click="sidebarOpen = true" class="md:hidden -ml-2 mr-1 p-2 text-gray-500 hover:text-indigo-600 focus:outline-none transition-colors rounded-lg active:bg-gray-100">
                <i class="bi bi-list text-2xl leading-none"></i>
            </button>

            <div class="w-10 h-10 md:w-12 md:h-12 rounded-xl md:rounded-2xl bg-indigo-600 text-white flex items-center justify-center shadow-lg shadow-indigo-100">
                <i class="bi bi-cpu-fill text-lg md:text-xl"></i>
            </div>
            <div class="leading-tight text-left">
                <h1 class="text-sm md:text-lg font-black text-gray-900 tracking-tight font-jakarta uppercase leading-none">TekniLog</h1>
                <p class="text-[7px] md:text-[9px] font-bold text-indigo-500 uppercase tracking-[0.2em] mt-1 md:mt-1.5">System Online</p>
            </div>
        </div>

        {{-- 2. DATE CENTER (Hidden di HP sangat kecil) --}}
        <div class="hidden sm:flex flex-col items-center flex-1 px-4 text-center">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-0.5">Kalender Akademik</p>
            <p class="text-sm font-black text-gray-800 font-jakarta leading-none" x-text="currentDate"></p>
        </div>

        {{-- 3. ACTION SECTION --}}
        <div class="flex items-center gap-2 md:gap-4 flex-shrink-0">
            
            {{-- Notifikasi --}}
            <div class="relative">
                <button @click="notifOpen = !notifOpen" class="w-10 h-10 md:w-12 md:h-12 flex items-center justify-center rounded-xl md:rounded-2xl bg-white text-gray-400 border border-gray-100 hover:text-indigo-600 hover:border-indigo-100 transition-all shadow-sm relative">
                    <i class="bi bi-bell-fill text-base md:text-lg"></i>
                    @if(Auth::user()->unreadNotifications->count() > 0)
                        <span class="absolute top-2.5 right-2.5 md:top-3 md:right-3 h-2 w-2 bg-orange-500 border-2 border-white rounded-full"></span>
                    @endif
                </button>
                
                {{-- Dropdown Notif --}}
                <div x-show="notifOpen" @click.away="notifOpen = false" x-cloak x-transition.origin.top.right 
                     class="absolute right-[-50px] md:right-0 mt-4 w-[280px] md:w-80 bg-white rounded-3xl shadow-2xl border border-gray-100 overflow-hidden z-50 text-left">
                    
                    <div class="p-5 border-b border-gray-50 bg-gray-50/50 flex justify-between items-center">
                        <span class="text-[10px] font-black text-gray-800 uppercase tracking-widest">Notifikasi</span>
                        @if(Auth::user()->unreadNotifications->count() > 0)
                            <a href="{{ route('notifications.markAllRead') }}" class="text-[9px] font-bold text-indigo-600 hover:text-indigo-800 uppercase tracking-wider">Tandai Baca</a>
                        @else
                            <span class="bg-indigo-100 text-indigo-600 text-[9px] font-black px-2 py-1 rounded-lg">ADMIN</span>
                        @endif
                    </div>

                    <div class="max-h-[300px] overflow-y-auto notif-scroll">
                        @forelse(Auth::user()->notifications as $notification)
                            <a href="{{ route('notifications.read', $notification->id) }}" class="block p-4 border-b border-gray-50 hover:bg-indigo-50/30 transition-colors {{ $notification->read_at ? '' : 'bg-indigo-50/50' }}">
                                <div class="flex gap-3 text-left">
                                    <div class="flex-shrink-0 mt-1">
                                        @php 
                                            $type = $notification->data['type'] ?? 'info';
                                            $icon = [
                                                'success' => 'bi-check-circle-fill text-emerald-500',
                                                'warning' => 'bi-exclamation-triangle-fill text-amber-500',
                                                'danger' => 'bi-x-circle-fill text-red-500',
                                                'info' => 'bi-info-circle-fill text-blue-500'
                                            ][$type] ?? 'bi-info-circle-fill text-blue-500';
                                        @endphp
                                        <i class="bi {{ $icon }}"></i>
                                    </div>
                                    <div class="flex-1 leading-tight">
                                        <p class="text-[11px] font-bold text-gray-800 leading-tight mb-1">{{ $notification->data['title'] ?? 'Info Admin' }}</p>
                                        <p class="text-[10px] text-gray-500 leading-relaxed">{{ $notification->data['message'] ?? '' }}</p>
                                        <p class="text-[9px] text-gray-400 mt-2 font-medium">{{ $notification->created_at->diffForHumans() }}</p>
                                    </div>
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

            {{-- User Profile --}}
            <div class="relative">
                <button @click="profileOpen = !profileOpen" class="flex items-center gap-2 md:gap-3 pl-1.5 md:pl-2 pr-1.5 md:pr-5 py-1.5 md:py-2 rounded-xl md:rounded-[1.8rem] bg-white border border-gray-100 shadow-sm hover:border-indigo-100 transition-all group">
                    <img src="{{ Auth::user()->profile_photo_url }}" class="w-7 h-7 md:w-9 md:h-9 rounded-lg md:rounded-xl object-cover shadow-sm border border-indigo-50" alt="{{ Auth::user()->name }}">
                    
                    <div class="hidden sm:block text-left leading-none">
                        <p class="text-[10px] md:text-xs font-black text-gray-900 uppercase tracking-tight">{{ Str::limit(Auth::user()->name, 10) }}</p>
                        <p class="text-[7px] md:text-[8px] font-bold text-indigo-500 tracking-widest uppercase mt-1">Admin</p>
                    </div>
                    <i class="bi bi-chevron-down text-[8px] md:text-[10px] text-gray-400 md:ml-1 group-hover:text-indigo-600"></i>
                </button>

                <div x-show="profileOpen" @click.away="profileOpen = false" x-cloak x-transition.origin.top.right 
                     class="absolute right-0 mt-4 w-56 md:w-64 bg-white rounded-3xl shadow-2xl border border-gray-100 overflow-hidden z-50 text-left">
                    <div class="p-6 text-center border-b border-gray-50 bg-indigo-50/20">
                        <img src="{{ Auth::user()->profile_photo_url }}" class="w-14 h-14 rounded-2xl object-cover mx-auto mb-3 shadow-lg border-4 border-white" alt="{{ Auth::user()->name }}">
                        <h4 class="font-black text-gray-900 text-xs md:text-sm uppercase leading-tight">{{ Auth::user()->name }}</h4>
                        <p class="text-[9px] text-gray-400 font-bold uppercase tracking-widest mt-2">ID: #{{ Auth::user()->id }}</p>
                    </div>
                    <div class="p-2 space-y-1">
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-3 text-[11px] md:text-xs font-bold text-gray-600 rounded-xl hover:bg-indigo-50 hover:text-indigo-600 transition-all">
                            <i class="bi bi-person-badge text-base"></i> My Account
                        </a>
                        <button @click="modalLogout = true; profileOpen = false" class="w-full flex items-center gap-3 px-4 py-3 text-[11px] md:text-xs font-bold text-red-500 rounded-xl hover:bg-red-50 transition-all text-left">
                            <i class="bi bi-box-arrow-right text-base"></i> Logout System
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </header>

    {{-- MODAL LOGOUT --}}
    <div x-show="modalLogout" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4 pointer-events-auto">
        <div x-show="modalLogout" x-transition.opacity @click="modalLogout = false" class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm"></div>
        <div x-show="modalLogout" x-transition.scale.95 class="relative w-full max-w-[320px] md:max-w-sm bg-white rounded-[2rem] md:rounded-[3rem] shadow-2xl p-8 md:p-10 text-center border border-white">
            <div class="w-16 h-16 md:w-20 md:h-20 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner">
                <i class="bi bi-power text-2xl md:text-3xl"></i>
            </div>
            <h3 class="text-xl md:text-2xl font-black text-gray-900 mb-2 font-jakarta uppercase">Akhiri Sesi?</h3>
            <p class="text-[12px] md:text-sm text-gray-500 mb-8 leading-relaxed font-medium">Apakah Anda yakin ingin keluar? Sesi aktif Anda di TekniLog akan segera diakhiri.</p>
            <div class="flex flex-col sm:flex-row gap-3">
                <button @click="modalLogout = false" class="order-2 sm:order-1 flex-1 px-6 py-3.5 rounded-xl md:rounded-2xl bg-gray-50 text-gray-500 font-bold text-sm hover:bg-gray-100 transition-all">Batal</button>
                <form action="{{ route('logout') }}" method="POST" class="order-1 sm:order-2 flex-1">
                    @csrf
                    <button type="submit" class="w-full px-6 py-3.5 rounded-xl md:rounded-2xl bg-red-500 text-white font-bold text-sm shadow-lg shadow-red-100 hover:bg-red-600 active:scale-95 transition-all">Keluar</button>
                </form>
            </div>
        </div>
    </div>
</div>