<?php

namespace App\Http\Controllers\Toolman;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\ProblemReport;
use Illuminate\Http\Request;
use Carbon\Carbon; // ✅ Penting buat manipulasi tanggal
use Barryvdh\DomPDF\Facade\Pdf; // ✅ Penting buat fitur cetak
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    /**
     * Tampilkan Halaman Laporan Toolman (2 Slide)
     */
    public function index(Request $request)
    {
        // 1. Inisialisasi Periode Tanggal (Default: Bulan Ini)
        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : now()->endOfMonth();

        // 2. Slide 1: Ambil Riwayat Sirkulasi Barang (Sesuai Filter)
        $logs = Loan::with(['user.department', 'item'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->latest()
            ->get();

        // 3. Slide 2: Ambil Semua Laporan Kendala dari Siswa
        // (Biasanya laporan butuh dipantau semua statusnya, tidak hanya range tanggal tertentu, 
        // tapi jika mau di-filter tanggal juga, tambahkan whereBetween seperti di atas)
        $incomingProblems = ProblemReport::with(['user.department', 'item'])
            ->latest()
            ->get();

        // 4. Hitung Summary (Wajib dikirim ke Blade agar tidak Error)
        $summary = [
            'total_logs'       => $logs->count(),
            'pending_problems' => $incomingProblems->where('status', 'pending')->count(),
            'period'           => $startDate->translatedFormat('d M Y') . ' - ' . $endDate->translatedFormat('d M Y')
        ];

        return view('toolman.laporan.index', compact('logs', 'incomingProblems', 'summary'));
    }

    /**
     * Update Status Kendala & Catatan (Slide 2)
     */
    public function updateProblemStatus(Request $request, $id)
    {
        // Validasi input status dan catatan admin
        $request->validate([
            'status'     => 'required|in:pending,checked,fixed',
            'admin_note' => 'nullable|string' // ✅ Wajib handle ini agar catatan tersimpan
        ]);

        $report = ProblemReport::findOrFail($id);
        
        // Update data
        $report->update([
            'status'     => $request->status,
            'admin_note' => $request->admin_note // Simpan catatan perbaikan
        ]);

        return redirect()->back()->with('success', 'Status laporan diperbarui dan catatan tersimpan.');
    }

    /**
     * Cetak Laporan Kerja Toolman ke PDF
     */
    public function exportPdf(Request $request)
    {
        // Pastikan filter tanggal di PDF sama dengan yang ada di layar
        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : now()->endOfMonth();

        // Ambil Data Sesuai Filter
        $data = Loan::with(['user.department', 'item'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->latest()
            ->get();

        $summary = [
            'total'   => $data->count(),
            'period'  => $startDate->translatedFormat('d F Y') . ' - ' . $endDate->translatedFormat('d F Y'),
            'toolman' => Auth::user()->name
        ];

        // Load View PDF khusus Toolman (Pastikan file view ini ada)
        $pdf = Pdf::loadView('toolman.laporan.pdf', compact('data', 'summary'))
                  ->setPaper('a4', 'landscape'); // Landscape biar tabel lega

        return $pdf->download('Laporan-Kerja-Toolman-'.now()->format('YmdHis').'.pdf');
    }
}