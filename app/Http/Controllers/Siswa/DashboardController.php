<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 1. Ambil rata-rata rating dari peminjaman yang SUDAH SELESAI (returned)
        $avgRating = Loan::where('user_id', $user->id)
                         ->where('status', 'returned')
                         ->whereNotNull('rating')
                         ->avg('rating');

        // Jika belum pernah pinjam atau belum ada rating, kasih default bintang 5.0
        $avgRating = $avgRating ?? 5.0;

        // 2. Format untuk tampilan Bintang (contoh: 4.5, 3.8, 5.0)
        $starScore = number_format($avgRating, 1);

        // 3. Konversi ke persentase (buat panjang progress bar kuning di UI 0-100%)
        $trustScore = round(($avgRating / 5) * 100);

        // 4. Tentukan Label Status & Warna langsung berdasarkan skala Bintang
        if ($avgRating >= 4.5) {
            $trustStatus = 'Sangat Bertanggung Jawab';
            $trustColor  = 'text-emerald-400';
        } elseif ($avgRating >= 3.5) {
            $trustStatus = 'Bagus & Tepat Waktu';
            $trustColor  = 'text-blue-400';
        } elseif ($avgRating >= 2.5) {
            $trustStatus = 'Standar';
            $trustColor  = 'text-yellow-400';
        } elseif ($avgRating >= 1.5) {
            $trustStatus = 'Kurang Terawat';
            $trustColor  = 'text-orange-400';
        } else {
            $trustStatus = 'Sangat Bermasalah';
            $trustColor  = 'text-red-400';
        }

        // Data lainnya tetap sama
        $activeLoans = Loan::where('user_id', $user->id)
                           ->where('status', 'approved')
                           ->with('item')
                           ->get();
                           
        $stats = [
            'total_items'   => $activeLoans->sum('quantity'),
            'pending_count' => Loan::where('user_id', $user->id)->where('status', 'pending')->count(),
            'finished_count'=> Loan::where('user_id', $user->id)->where('status', 'returned')->count(),
            'broken_count'  => Loan::where('user_id', $user->id)->where('return_condition', 'rusak')->count(),
        ];
        
        $recentActivities = Loan::where('user_id', $user->id)
                                ->with('item')
                                ->latest()
                                ->take(5)
                                ->get();

        // Tambahkan $starScore ke compact biar langsung kepakai di Blade
        return view('siswa.dashboard.index', compact(
            'user', 
            'activeLoans', 
            'stats', 
            'recentActivities', 
            'trustScore', 
            'starScore', 
            'trustStatus', 
            'trustColor'
        ));
    }
}