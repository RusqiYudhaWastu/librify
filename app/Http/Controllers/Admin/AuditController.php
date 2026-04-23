<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AuditController extends Controller
{
    /**
     * Tampilkan Log Audit Global (Semua Sirkulasi Buku)
     * Dilengkapi Filter Tanggal, Status & Search
     */
    public function index(Request $request)
    {
        // 1. Setup Filter Tanggal (Default: Bulan Ini)
        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : now()->endOfMonth();

        // 2. Inisialisasi Query dengan Eager Loading Lengkap
        // ✅ UPDATE: Ganti 'item.category' jadi 'item.categories' karena relasinya udah Many-to-Many
        $query = Loan::with(['user', 'item.categories'])
                     ->whereBetween('created_at', [$startDate, $endDate]);

        // 3. FILTER STATUS
        // Menangkap input 'status' dari dropdown Blade
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 4. Logika Live Search (Advanced)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                // Cari berdasarkan Nama User
                $q->whereHas('user', function($u) use ($search) {
                    $u->where('name', 'like', "%{$search}%");
                })
                // Cari berdasarkan Judul Buku atau Kode Buku/ISBN
                ->orWhereHas('item', function($i) use ($search) {
                    $i->where('name', 'like', "%{$search}%")
                      ->orWhere('asset_code', 'like', "%{$search}%");
                });
            });
        }

        // 5. Ambil data (Audit Trail) & Hitung Rating User
        $auditLogs = $query->latest()->get()->map(function($log) {
            // Hitung rata-rata rating user dari riwayat yang sudah selesai
            $log->user_rating = Loan::where('user_id', $log->user_id)
                ->where('status', 'returned')
                ->avg('rating');
            
            return $log;
        });

        // 6. Hitung Statistik Audit (Dinamis berdasarkan hasil filter)
        $summary = [
            'total_logs'   => $auditLogs->count(),
            'active_loans' => $auditLogs->whereIn('status', ['approved', 'borrowed'])->count(),
            
            // Statistik Kondisi Buku
            'broken_items' => $auditLogs->where('return_condition', 'rusak')->count(),
            'lost_items'   => $auditLogs->where('return_condition', 'hilang')->count(),
            
            // Statistik Volume & Keuangan
            'total_units'  => $auditLogs->sum('quantity'),
            'total_fines'  => $auditLogs->sum('fine_amount'),
            
            // Info Periode untuk Tampilan
            'period'       => $startDate->translatedFormat('d M Y') . ' - ' . $endDate->translatedFormat('d M Y')
        ];

        return view('admin.audit.index', compact('auditLogs', 'summary'));
    }
}