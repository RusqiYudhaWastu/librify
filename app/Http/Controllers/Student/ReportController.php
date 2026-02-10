<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\Item;
use App\Models\ProblemReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; // ✅ Import Storage untuk manajemen file
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Menampilkan Halaman Riwayat & Laporan
     */
    public function index()
    {
        $user = Auth::user();

        // 1. Ambil Daftar Laporan (Untuk Tab Laporan)
        $reports = ProblemReport::with('item')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        // 2. Ambil Riwayat Peminjaman (Untuk Tab Riwayat & Dropdown Pilihan Barang)
        $loans = Loan::with('item')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        return view('student.laporan.index', compact('reports', 'loans'));
    }

    /**
     * Simpan Laporan Kendala Baru (Dengan Foto)
     */
    public function store(Request $request)
    {
        $request->validate([
            'loan_id'     => 'required|exists:loans,id', // Pastikan ID Peminjaman valid
            'description' => 'required|string|min:10',   // Deskripsi minimal 10 karakter
            'photo'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // Validasi Foto (Max 2MB)
        ]);

        // Ambil data loan untuk mendapatkan item_id
        $loan = Loan::findOrFail($request->loan_id);

        // Security Check: Pastikan user yang login adalah peminjam barang tersebut
        if ($loan->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Validasi Gagal! Data peminjaman tidak valid.');
        }

        // Handle Upload Foto
        $photoPath = null;
        if ($request->hasFile('photo')) {
            // Simpan foto di folder 'problem_reports' dalam storage public
            $photoPath = $request->file('photo')->store('problem_reports', 'public');
        }

        // Simpan ke Database
        ProblemReport::create([
            'user_id'     => Auth::id(),
            'item_id'     => $loan->item_id, // Ambil ID barang dari data loan
            'description' => $request->description,
            'photo_path'  => $photoPath, // Simpan path foto
            'status'      => 'pending'   // Status awal: Menunggu
        ]);

        return redirect()->back()->with('success', 'Laporan kendala berhasil dikirim. Menunggu respon Toolman.');
    }

    /**
     * Cetak PDF Riwayat dengan Filter Tanggal
     */
    public function exportPdf(Request $request)
    {
        $user = Auth::user();
        
        // Query Dasar: Ambil data Loan (Riwayat) milik user
        $query = Loan::with('item')->where('user_id', $user->id);

        // Filter Tanggal (Jika user mengisi form tanggal)
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('created_at', [
                $request->start_date . ' 00:00:00', 
                $request->end_date . ' 23:59:59'
            ]);
        }

        $data = $query->latest()->get();

        // Data Summary untuk Header PDF
        $summary = [
            'name'  => $user->name,
            'class' => $user->classRoom->name ?? 'Tanpa Kelas',
            'date'  => now()->translatedFormat('d F Y'),
            'filter_start' => $request->start_date ? Carbon::parse($request->start_date)->translatedFormat('d M Y') : 'Awal',
            'filter_end' => $request->end_date ? Carbon::parse($request->end_date)->translatedFormat('d M Y') : 'Sekarang',
        ];

        // Load View PDF (Pastikan file resources/views/student/laporan/pdf.blade.php ada)
        $pdf = Pdf::loadView('student.laporan.pdf', compact('data', 'summary'));
        
        // Download file PDF
        return $pdf->download('Riwayat-Inventaris-'.$user->name.'.pdf');
    }
}