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

        // 1. Statistik Peminjaman Pribadi
        $activeLoans = Loan::where('user_id', $userId)->where('status', 'borrowed')->count();
        $returnedLoans = Loan::where('user_id', $userId)->where('status', 'returned')->count();
        $totalFines = Loan::where('user_id', $userId)->sum('fine_amount'); 

        // 2. Riwayat Transaksi Terakhir (5 Data)
        $recentActivities = Loan::with('item') 
            ->where('user_id', $userId)
            ->latest()
            ->take(5)
            ->get();

        // ✅ UPDATE: Path disesuaikan dengan folder 'student/dashboard/index.blade.php'
        return view('student.dashboard.index', compact('activeLoans', 'returnedLoans', 'totalFines', 'recentActivities'));
    }
}