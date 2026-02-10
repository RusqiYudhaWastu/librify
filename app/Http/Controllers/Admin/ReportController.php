<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\Department;
use App\Models\ProblemReport; // ✅ Import Model ProblemReport
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
        $deptId = $request->department_id;

        // 2. SLIDE 1: Ambil Data Sirkulasi (Loan) -> Variabel $logs
        $logsQuery = Loan::with(['user.department', 'item'])
                    ->whereBetween('created_at', [$startDate, $endDate]);

        if ($deptId) {
            $logsQuery->whereHas('user', function($q) use ($deptId) {
                $q->where('department_id', $deptId);
            });
        }
        $logs = $logsQuery->latest()->get(); // ✅ Disimpan sebagai $logs agar cocok dengan View

        // 3. SLIDE 2: Ambil Data Keluhan Siswa (Global) -> Variabel $incomingProblems
        $problemsQuery = ProblemReport::with(['user.department', 'item'])
                        ->whereBetween('created_at', [$startDate, $endDate]); // ✅ Filter tanggal juga diterapkan di sini
        
        if ($deptId) {
            $problemsQuery->whereHas('user', function($q) use ($deptId) {
                $q->where('department_id', $deptId);
            });
        }
        $incomingProblems = $problemsQuery->latest()->get(); // ✅ Disimpan sebagai $incomingProblems agar cocok dengan View

        // 4. Data Pendukung
        $departments = Department::all();

        // 5. Variabel Summary untuk Statistik Dashboard
        $summary = [
            'total'         => $logs->count(),
            'done'          => $logs->where('status', 'returned')->count(),
            'active'        => $logs->where('status', 'approved')->count(),
            // Hitung yang rusak ATAU hilang
            'broken'        => $logs->whereIn('return_condition', ['rusak', 'hilang'])->count(),
            // Hitung kendala yang belum fixed
            'problem_count' => $incomingProblems->where('status', '!=', 'fixed')->count(), 
            'period'        => $startDate->translatedFormat('d M Y') . ' - ' . $endDate->translatedFormat('d M Y')
        ];

        return view('admin.laporan.index', compact('logs', 'incomingProblems', 'summary', 'departments'));
    }

    /**
     * Cetak Laporan Sirkulasi Barang ke PDF
     */
    public function exportPdf(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : now()->endOfMonth();
        $deptId = $request->department_id;

        $query = Loan::with(['user.department', 'item'])
                    ->whereBetween('created_at', [$startDate, $endDate]);

        if ($deptId) {
            $query->whereHas('user', function($q) use ($deptId) {
                $q->where('department_id', $deptId);
            });
        }

        $data = $query->latest()->get();

        $summary = [
            'total'  => $data->count(),
            'period' => $startDate->translatedFormat('d F Y') . ' - ' . $endDate->translatedFormat('d F Y'),
            'admin'  => Auth::user()->name
        ];

        $pdf = Pdf::loadView('admin.laporan.pdf', compact('data', 'summary'))
                  ->setPaper('a4', 'landscape');

        return $pdf->download('Laporan-Logistik-Admin-'.now()->format('YmdHis').'.pdf');
    }
}