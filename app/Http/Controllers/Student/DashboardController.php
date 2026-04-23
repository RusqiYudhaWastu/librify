<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Loan; 

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // 1. Ambil rata-rata rating dari peminjaman yang SUDAH SELESAI (returned)
        $avgRating = Loan::where('user_id', $userId)
                         ->where('status', 'returned')
                         ->whereNotNull('rating')
                         ->avg('rating');

        // Jika belum pernah pinjam atau belum ada rating, kasih default bintang 5.0
        $avgRating = $avgRating ?? 5.0;

        // 2. Format untuk tampilan Bintang (contoh: 4.5, 3.8, 5.0)
        $starScore = number_format($avgRating, 1);

        // 3. Konversi ke persentase untuk panjang progress bar di UI (0-100)
        $trustScore = round(($avgRating / 5) * 100);

        // 4. Tentukan Label Status & Warna berdasarkan skala Bintang
        if ($avgRating >= 4.5) {
            $scoreColor = 'text-emerald-400';
            $barColor = 'bg-emerald-500';
            $statusText = 'Sangat Baik';
            $cardBorder = 'border-slate-800';
        } elseif ($avgRating >= 3.5) {
            $scoreColor = 'text-blue-400';
            $barColor = 'bg-blue-500';
            $statusText = 'Baik';
            $cardBorder = 'border-blue-500/50';
        } elseif ($avgRating >= 2.5) {
            $scoreColor = 'text-yellow-400';
            $barColor = 'bg-yellow-500';
            $statusText = 'Perlu Perhatian';
            $cardBorder = 'border-yellow-500/50';
        } else {
            $scoreColor = 'text-red-400';
            $barColor = 'bg-red-500';
            $statusText = 'Bermasalah';
            $cardBorder = 'border-red-500/50';
        }

        // 5. Statistik Peminjaman Pribadi
        $activeLoans = Loan::where('user_id', $userId)
                           ->whereIn('status', ['approved', 'pending']) 
                           ->count();
                           
        $returnedLoans = Loan::where('user_id', $userId)
                             ->where('status', 'returned')
                             ->count();
                             
        // Hitung denda yang statusnya 'unpaid'
        $totalFines = Loan::where('user_id', $userId)
                          ->where('fine_status', 'unpaid')
                          ->sum('fine_amount'); 

        // 6. Riwayat Transaksi Terakhir (5 Data)
        $recentActivities = Loan::with('item') 
            ->where('user_id', $userId)
            ->latest()
            ->take(5)
            ->get();

        // Lempar semua variabel ke Blade
        return view('student.dashboard.index', compact(
            'activeLoans', 
            'returnedLoans', 
            'totalFines', 
            'recentActivities',
            'starScore',
            'trustScore',
            'scoreColor',
            'barColor',
            'statusText',
            'cardBorder'
        ));
    }
}