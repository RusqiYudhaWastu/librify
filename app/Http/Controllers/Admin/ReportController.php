<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\ProblemReport;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    /**
     * Tampilkan Halaman Laporan Admin (2 Slide: Sirkulasi & Keluhan Global)
     */
    public function index(Request $request)
    {
        // 1. Inisialisasi Filter Tanggal
        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : now()->endOfMonth();

        // 2. SLIDE 1: Ambil Data Sirkulasi Peminjaman Buku (Global)
        $logsQuery = Loan::with(['user', 'item'])
                         ->whereBetween('created_at', [$startDate, $endDate]);
        
        // Map untuk hitung Rating User
        $logs = $logsQuery->latest()->get()->map(function($item) {
            $item->user_rating = Loan::where('user_id', $item->user_id)
                                     ->where('status', 'returned')
                                     ->avg('rating');
            return $item;
        });

        // 3. SLIDE 2: Ambil Data Laporan Kendala/Kerusakan Buku (Global)
        $problemsQuery = ProblemReport::with(['user', 'item'])
                                      ->whereBetween('created_at', [$startDate, $endDate]);
        
        // Map untuk hitung Rating User (Pelapor)
        $incomingProblems = $problemsQuery->latest()->get()->map(function($item) {
            $item->user_rating = Loan::where('user_id', $item->user_id)
                                     ->where('status', 'returned')
                                     ->avg('rating');
            return $item;
        });

        // 4. Variabel Summary untuk Statistik Dashboard
        $summary = [
            'total'         => $logs->count(),
            'done'          => $logs->where('status', 'returned')->count(),
            'active'        => $logs->where('status', 'approved')->count(),
            'broken'        => $logs->whereIn('return_condition', ['rusak', 'hilang'])->count(),
            'problem_count' => $incomingProblems->where('status', '!=', 'fixed')->count(), 
            'period'        => $startDate->translatedFormat('d M Y') . ' - ' . $endDate->translatedFormat('d M Y'),
            'total_fines'   => $logs->sum('fine_amount') 
        ];

        // ✅ UPDATE: Hapus variabel $departments dari compact karena sudah tidak dipakai
        return view('admin.laporan.index', compact('logs', 'incomingProblems', 'summary'));
    }

    /**
     * Cetak Laporan Sirkulasi Buku ke PDF
     */
    public function exportPdf(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : now()->endOfMonth();

        // Query Data untuk PDF secara Global
        $query = Loan::with(['user', 'item'])
                     ->whereBetween('created_at', [$startDate, $endDate]);

        $data = $query->latest()->get();

        $summary = [
            'total'       => $data->count(),
            'period'      => $startDate->translatedFormat('d F Y') . ' - ' . $endDate->translatedFormat('d F Y'),
            'admin'       => Auth::user()->name,
            'total_fines' => $data->sum('fine_amount') 
        ];

        $pdf = Pdf::loadView('admin.laporan.pdf', compact('data', 'summary'))
                  ->setPaper('a4', 'landscape');

        // Ganti nama file download biar sesuai tema perpustakaan
        return $pdf->download('Laporan-Sirkulasi-Librify-'.now()->format('YmdHis').'.pdf');
    }
}