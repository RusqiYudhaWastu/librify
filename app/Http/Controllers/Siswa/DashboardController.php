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

        // 1. Ambil rata-rata rating dari semua peminjaman yang sudah selesai
        // Jika belum pernah pinjam, kasih default rating 5 (skor 100)
        $avgRating = Loan::where('user_id', $user->id)
                         ->whereNotNull('rating')
                         ->avg('rating') ?? 5;

        // 2. Konversi ke skala 100
        $trustScore = round(($avgRating / 5) * 100);

        // 3. Tentukan Label Status & Warna berdasarkan skor
        if ($trustScore >= 90) {
            $trustStatus = 'Sangat Bertanggung Jawab';
            $trustColor = 'text-emerald-400';
        } elseif ($trustScore >= 75) {
            $trustStatus = 'Bertanggung Jawab';
            $trustColor = 'text-blue-400';
        } elseif ($trustScore >= 50) {
            $trustStatus = 'Cukup Bertanggung Jawab';
            $trustColor = 'text-orange-400';
        } else {
            $trustStatus = 'Kurang Bertanggung Jawab';
            $trustColor = 'text-red-400';
        }

        // Data lainnya tetap sama
        $activeLoans = Loan::where('user_id', $user->id)->where('status', 'approved')->with('item')->get();
        $stats = [
            'total_items'   => $activeLoans->sum('quantity'),
            'pending_count' => Loan::where('user_id', $user->id)->where('status', 'pending')->count(),
            'finished_count'=> Loan::where('user_id', $user->id)->where('status', 'returned')->count(),
            'broken_count'  => Loan::where('user_id', $user->id)->where('return_condition', 'rusak')->count(),
        ];
        $recentActivities = Loan::where('user_id', $user->id)->with('item')->latest()->take(5)->get();

        return view('siswa.dashboard.index', compact('user', 'activeLoans', 'stats', 'recentActivities', 'trustScore', 'trustStatus', 'trustColor'));
    }
}