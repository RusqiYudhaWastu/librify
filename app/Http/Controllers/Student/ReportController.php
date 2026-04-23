<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\Item;
use App\Models\ProblemReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; // ✅ Import Storage untuk manajemen file
use Illuminate\Support\Facades\DB; // ✅ Import DB untuk query grafik
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Menampilkan Halaman Riwayat, Laporan & Rapot Grafik (SPA)
     */
    public function index()
    {
        $user = Auth::user();

        // 1. Ambil Riwayat Peminjaman (Loans)
        $loans = Loan::with('item')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        // 2. Ambil Daftar Laporan (Reports)
        $reports = ProblemReport::with('item')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        // --- 3. DATA RAPOT GRAFIK ---
        // Statistik Rating Individu
        $individualAverage = Loan::where('user_id', $user->id)
            ->whereNotNull('rating')
            ->avg('rating') ?? 0;

        // Statistik Rating Kelas (rata-rata teman sekelasnya)
        $classAverage = Loan::whereHas('user', function($query) use ($user) {
                $query->where('class_id', $user->class_id);
            })
            ->whereNotNull('rating')
            ->avg('rating') ?? 0;

        // Tren Rating Bulanan (Aman dari Strict Mode SQL)
        $monthlyRatings = Loan::select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month_year"), 
                DB::raw("MONTHNAME(created_at) as month_name"),            
                DB::raw("AVG(rating) as average_rating")
            )
            ->where('user_id', $user->id)
            ->whereNotNull('rating')
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month_year', 'month_name')
            ->orderBy('month_year', 'asc')
            ->get();

        $chartLabels = $monthlyRatings->pluck('month_name')->toArray();
        $chartData = $monthlyRatings->pluck('average_rating')->toArray();

        // Kirim semua data ke view
        return view('student.laporan.index', compact(
            'loans', 'reports', 'individualAverage', 'classAverage', 'chartLabels', 'chartData'
        ));
    }

    /**
     * Simpan Laporan Kendala Baru (Dengan Foto & Tingkat Keparahan)
     */
    public function store(Request $request)
    {
        // ✅ UPDATE: Tambahkan validasi untuk 'severity'
        $request->validate([
            'loan_id'     => 'required|exists:loans,id', 
            'severity'    => 'required|in:Ringan,Sedang,Parah', // Pastikan validasi severity ada
            'description' => 'required|string|min:10',   
            'photo'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048', 
        ]);

        $loan = Loan::findOrFail($request->loan_id);

        if ($loan->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Validasi Gagal! Data peminjaman tidak valid.');
        }

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('problem_reports', 'public');
        }

        // ✅ UPDATE: Masukkan 'severity' ke database
        ProblemReport::create([
            'user_id'     => Auth::id(),
            'item_id'     => $loan->item_id, 
            'loan_id'     => $loan->id,      
            'severity'    => $request->severity, // Field severity ditambahkan
            'description' => $request->description,
            'photo_path'  => $photoPath, 
            'status'      => 'pending'   
        ]);

        return redirect()->back()->with('success', 'Laporan kendala berhasil dikirim. Menunggu respon Toolman.');
    }

    /**
     * Cetak PDF Riwayat dengan Filter Tanggal
     */
    public function exportPdf(Request $request)
    {
        $user = Auth::user();
        
        $query = Loan::with('item')->where('user_id', $user->id);

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('created_at', [
                $request->start_date . ' 00:00:00', 
                $request->end_date . ' 23:59:59'
            ]);
        }

        $data = $query->latest()->get();

        $summary = [
            'name'         => $user->name,
            'class'        => $user->classRoom->name ?? 'Tanpa Kelas',
            'date'         => now()->translatedFormat('d F Y'),
            'filter_start' => $request->start_date ? Carbon::parse($request->start_date)->translatedFormat('d M Y') : 'Awal',
            'filter_end'   => $request->end_date ? Carbon::parse($request->end_date)->translatedFormat('d M Y') : 'Sekarang',
            'total_loans'  => $data->count(),
            'total_fines'  => $data->sum('fine_amount'),
        ];

        $pdf = Pdf::loadView('student.laporan.pdf', compact('data', 'summary'));
        
        return $pdf->download('Riwayat-Inventaris-'.$user->name.'.pdf');
    }
}