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

        // 1. Ambil ID semua jurusan yang menjadi tanggung jawab Toolman ini
        $managedDeptIds = $user->assignedDepartments->pluck('id');

        // 2. Hitung Statistik Utama (Hanya untuk wilayah tanggung jawabnya)
        $stats = [
            // Antrean request dari siswa di jurusan yang dia kelola
            'pending' => Loan::where('status', 'pending')
                            ->whereHas('user', function($q) use ($managedDeptIds) {
                                $q->whereIn('department_id', $managedDeptIds);
                            })->count(),

            // Peminjaman aktif milik jurusannya
            'active'  => Loan::where('status', 'approved')
                            ->whereHas('user', function($q) use ($managedDeptIds) {
                                $q->whereIn('department_id', $managedDeptIds);
                            })->count(),

            // Pengembalian hari ini di jurusannya
            'returned_today' => Loan::where('status', 'returned')
                                    ->whereDate('updated_at', Carbon::today())
                                    ->whereHas('user', function($q) use ($managedDeptIds) {
                                        $q->whereIn('department_id', $managedDeptIds);
                                    })->count(),

            // Total unit barang milik jurusannya yang sedang berada di luar
            'total_items_out' => Loan::where('status', 'approved')
                                    ->whereHas('user', function($q) use ($managedDeptIds) {
                                        $q->whereIn('department_id', $managedDeptIds);
                                    })->sum('quantity'),
        ];

        // 3. Ambil 5 Transaksi Terbaru (Hanya dari siswa di wilayahnya)
        $recentLoans = Loan::with(['user.department', 'item'])
                            ->whereHas('user', function($q) use ($managedDeptIds) {
                                $q->whereIn('department_id', $managedDeptIds);
                            })
                            ->latest()
                            ->take(5)
                            ->get();

        // 4. Ambil Barang yang Stoknya Tipis (Hanya barang milik kategorinya jurusan tersebut)
        // Barang dianggap milik jurusannya jika kategori barang tersebut terhubung ke departemen si Toolman
        $lowStockItems = Item::where('stock', '<=', 5)
                            ->whereHas('category.departments', function($q) use ($managedDeptIds) {
                                $q->whereIn('departments.id', $managedDeptIds);
                            })->get();

        return view('toolman.dashboard.index', compact('stats', 'recentLoans', 'lowStockItems'));
    }
}