<?php

namespace App\Http\Controllers\Toolman;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\Item;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Karena ini Librify (Perpustakaan Universal), Petugas ngurusin SEMUA sirkulasi.
        // Tidak ada lagi batasan wilayah/jurusan.

        // 1. Hitung Statistik Utama (Global / Seluruh Sistem)
        $stats = [
            // Antrean request peminjaman buku dari semua member
            'pending' => Loan::where('status', 'pending')->count(),

            // Peminjaman aktif (buku yang masih dibawa peminjam)
            'active'  => Loan::where('status', 'approved')->count(),

            // Buku yang dikembalikan hari ini
            'returned_today' => Loan::where('status', 'returned')
                                    ->whereDate('updated_at', Carbon::today())
                                    ->count(),

            // Total eksemplar buku yang sedang berada di luar perpustakaan
            'total_items_out' => Loan::where('status', 'approved')->sum('quantity'),
        ];

        // 2. Ambil 5 Transaksi Terbaru (Global)
        // Kita load relasi user dan item biar datanya lengkap pas ditampilin
        $recentLoans = Loan::with(['user', 'item'])
                            ->latest()
                            ->take(5)
                            ->get();

        // 3. Ambil Buku yang Stoknya Tipis (Global)
        // Mengingatkan petugas kalau ada buku yang stok fisiknya tinggal sedikit
        $lowStockItems = Item::where('stock', '<=', 5)->get();

        return view('toolman.dashboard.index', compact('stats', 'recentLoans', 'lowStockItems'));
    }
}