<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    /**
     * Tampilkan Log Audit Global (Semua Pergerakan Barang)
     *
     */
    public function index(Request $request)
    {
        // 1. Inisialisasi Query dengan Eager Loading agar kencang
        // Kita tarik data User, Jurusan, Item, dan Kategori sekaligus.
        $query = Loan::with(['user.department', 'item.category']);

        // 2. Logika Live Search (Cari berdasarkan Nama Peminjam, Nama Barang, atau Kode Aset)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($u) use ($search) {
                    $u->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('item', function($i) use ($search) {
                    $i->where('name', 'like', "%{$search}%")
                      ->orWhere('asset_code', 'like', "%{$search}%");
                });
            });
        }

        // 3. Ambil data dengan urutan terbaru (Audit Trail)
        $auditLogs = $query->latest()->get();

        // 4. Hitung Statistik Audit untuk Card di Dashboard
        $summary = [
            'total_logs' => $auditLogs->count(),
            'active_loans' => $auditLogs->where('status', 'approved')->count(),
            'broken_items' => $auditLogs->where('return_condition', 'rusak')->count(),
            'lost_items'   => $auditLogs->where('return_condition', 'hilang')->count(),
            'total_units'  => $auditLogs->sum('quantity'),
        ];

        return view('admin.audit.index', compact('auditLogs', 'summary'));
    }
}