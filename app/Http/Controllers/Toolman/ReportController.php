<?php

namespace App\Http\Controllers\Toolman;

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
     * Tampilkan Halaman Laporan Petugas (2 Slide: Log & Masalah)
     */
    public function index(Request $request)
    {
        // 1. Inisialisasi Periode Tanggal (Default: Bulan Ini)
        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : now()->endOfMonth();

        // 2. Slide 1: Ambil Riwayat Sirkulasi Buku (Sesuai Filter)
        // ✅ UPDATE: Relasi department dan classRoom sudah dihapus
        $logs = Loan::with(['user', 'item'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->latest()
            ->get()
            ->map(function($log) {
                // Hitung rata-rata rating user dari riwayat peminjaman yang sudah selesai
                $log->user_rating = Loan::where('user_id', $log->user_id)
                    ->where('status', 'returned')
                    ->avg('rating');
                return $log;
            });

        // 3. Slide 2: Ambil Semua Laporan Kendala dari Member
        // ✅ UPDATE: Relasi department dan classRoom sudah dihapus
        $incomingProblems = ProblemReport::with(['user', 'item'])
            ->latest()
            ->get()
            ->map(function($problem) {
                // Petugas bisa liat track record pelapor masalah
                $problem->user_rating = Loan::where('user_id', $problem->user_id)
                    ->where('status', 'returned')
                    ->avg('rating');
                return $problem;
            });

        // 4. Hitung Summary
        $summary = [
            'total_logs'       => $logs->count(),
            'pending_problems' => $incomingProblems->where('status', 'pending')->count(),
            'period'           => $startDate->translatedFormat('d M Y') . ' - ' . $endDate->translatedFormat('d M Y'),
            'total_fines'      => $logs->sum('fine_amount') 
        ];

        return view('toolman.laporan.index', compact('logs', 'incomingProblems', 'summary'));
    }

    /**
     * Update Status Kendala & Catatan (Slide 2 - Aksi Petugas)
     */
    public function updateProblemStatus(Request $request, $id)
    {
        $request->validate([
            'status'     => 'required|in:pending,process,fixed,rejected',
            'admin_note' => 'nullable|string' 
        ]);

        $report = ProblemReport::findOrFail($id);
        
        $report->update([
            'status'     => $request->status,
            'admin_note' => $request->admin_note 
        ]);

        return redirect()->back()->with('success', 'Status laporan buku diperbarui dan catatan tersimpan.');
    }

    /**
     * Cetak Laporan Kerja Petugas ke PDF
     */
    public function exportPdf(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : now()->endOfMonth();

        // Ambil Data Sesuai Filter & Hitung Rating 
        // ✅ UPDATE: Relasi department dan classRoom sudah dihapus
        $data = Loan::with(['user', 'item'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->latest()
            ->get()
            ->map(function($log) {
                // Inject rating user ke data PDF
                $log->user_rating = Loan::where('user_id', $log->user_id)
                    ->where('status', 'returned')
                    ->avg('rating');
                return $log;
            });

        // Siapkan Summary untuk Header PDF
        $summary = [
            'total'       => $data->count(),
            'period'      => $startDate->translatedFormat('d F Y') . ' - ' . $endDate->translatedFormat('d F Y'),
            'toolman'     => Auth::user()->name, // Biarkan key ini jika file view PDF masih manggil $summary['toolman']
            'total_fines' => $data->sum('fine_amount')
        ];

        $pdf = Pdf::loadView('toolman.laporan.pdf', compact('data', 'summary'))
                  ->setPaper('a4', 'landscape');

        return $pdf->download('Laporan-Kerja-Petugas-'.now()->format('YmdHis').'.pdf');
    }
}