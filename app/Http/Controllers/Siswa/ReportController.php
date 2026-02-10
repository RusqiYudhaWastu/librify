<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\Item;
use App\Models\ProblemReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; // ✅ Wajib import ini untuk fitur foto
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    /**
     * Tampilkan Halaman Riwayat & Laporan
     */
    public function index()
    {
        $user = Auth::user();

        // 1. Ambil Riwayat Peminjaman (Loans)
        // Variabel ini '$loans' wajib ada karena dipanggil di view
        $loans = Loan::with('item')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        // 2. Ambil Daftar Laporan Kendala (Reports)
        $reports = ProblemReport::with('item')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        // Kirim kedua variabel ke view dengan nama yang sesuai
        return view('siswa.laporan.index', compact('loans', 'reports'));
    }

    /**
     * Simpan Laporan Kendala Baru (Dengan Foto)
     */
    public function storeProblem(Request $request)
    {
        $request->validate([
            'loan_id'     => 'required|exists:loans,id', // Validasi ID Peminjaman
            'description' => 'required|string|min:10',
            'photo'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // Validasi Foto (Max 2MB)
        ]);

        // Ambil data loan untuk mendapatkan item_id yang valid
        $loan = Loan::findOrFail($request->loan_id);

        // Security Check: Pastikan akun ini benar peminjam barang tersebut
        if ($loan->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Validasi Gagal! Data peminjaman tidak valid.');
        }

        // Logic Upload Foto
        $photoPath = null;
        if ($request->hasFile('photo')) {
            // Simpan di folder 'problem_reports' di disk public
            $photoPath = $request->file('photo')->store('problem_reports', 'public');
        }

        // Simpan ke Database
        ProblemReport::create([
            'user_id'     => Auth::id(),
            'item_id'     => $loan->item_id, // Ambil ID barang dari data loan
            'description' => $request->description,
            'photo_path'  => $photoPath, // Simpan path foto
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
        
        // Query Dasar: Ambil data Loan (Riwayat)
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
            'name'  => $user->name, // Nama Akun Kelas (misal: XII PPLG 1)
            'class' => $user->department->name ?? 'Jurusan Tidak Diketahui',
            'date'  => now()->translatedFormat('d F Y'),
            'filter_start' => $request->start_date ? \Carbon\Carbon::parse($request->start_date)->translatedFormat('d M Y') : 'Awal',
            'filter_end' => $request->end_date ? \Carbon\Carbon::parse($request->end_date)->translatedFormat('d M Y') : 'Sekarang',
        ];

        // Load View PDF
        // Pastikan file view 'siswa.laporan.pdf' sudah ada
        $pdf = Pdf::loadView('siswa.laporan.pdf', compact('data', 'summary'));
        
        return $pdf->download('Laporan-Logistik-'.$user->name.'.pdf');
    }
}