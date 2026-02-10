<?php

namespace App\Http\Controllers\Toolman;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\SystemNotification;

class LoanController extends Controller
{
    /**
     * Tampilkan semua request dari siswa (Terfilter berdasarkan Otoritas Toolman).
     */
    public function index()
    {
        $user = Auth::user();
        $managedDeptIds = $user->assignedDepartments->pluck('id');

        $requests = Loan::with(['user.department', 'item.category'])
            ->whereHas('user', function($query) use ($managedDeptIds) {
                $query->whereIn('department_id', $managedDeptIds);
            })
            ->latest()
            ->get();
        
        return view('toolman.request.index', compact('requests'));
    }

    /**
     * Setujui Peminjaman (Approve).
     */
    public function approve(Request $request, $id)
    {
        $user = Auth::user();
        $managedDeptIds = $user->assignedDepartments->pluck('id');

        $loan = Loan::whereHas('user', function($query) use ($managedDeptIds) {
            $query->whereIn('department_id', $managedDeptIds);
        })->findOrFail($id);

        $item = Item::findOrFail($loan->item_id);

        $request->validate([
            'approved_quantity' => 'required|numeric|min:1|max:' . $item->stock,
        ], [
            'approved_quantity.max' => 'Gagal! Stok fisik di gudang hanya tersisa ' . $item->stock . ' unit.',
        ]);

        $finalQty = $request->approved_quantity;

        // Kurangi stok barang
        $item->decrement('stock', $finalQty);

        // Update status pinjaman
        $loan->update([
            'quantity' => $finalQty, 
            'status' => 'approved',
            'loan_date' => now(),
            'admin_note' => $request->admin_note ?? 'Permintaan disetujui. Silakan ambil barang di gudang.'
        ]);

        // Notifikasi ke Siswa
        $loan->user->notify(new SystemNotification(
            'Peminjaman Disetujui',
            'Permintaan alat ' . $item->name . ' telah disetujui. Silakan ambil di ruang alat.',
            route('siswa.request'),
            'success'
        ));

        return redirect()->back()->with('success', 'Peminjaman berhasil di-ACC sebanyak ' . $finalQty . ' unit!');
    }

    /**
     * Tolak Peminjaman (Reject).
     */
    public function reject(Request $request, $id)
    {
        $user = Auth::user();
        $managedDeptIds = $user->assignedDepartments->pluck('id');

        $loan = Loan::whereHas('user', function($query) use ($managedDeptIds) {
            $query->whereIn('department_id', $managedDeptIds);
        })->findOrFail($id);

        $request->validate([
            'admin_note' => 'required|string|max:255'
        ], [
            'admin_note.required' => 'Alasan penolakan wajib diisi bro!'
        ]);

        $loan->update([
            'status' => 'rejected',
            'admin_note' => $request->admin_note
        ]);

        // Notifikasi ke Siswa
        $loan->user->notify(new SystemNotification(
            'Peminjaman Ditolak',
            'Maaf, permintaan Anda ditolak. Alasan: ' . $request->admin_note,
            route('siswa.request'),
            'danger'
        ));

        return redirect()->back()->with('success', 'Permintaan peminjaman telah ditolak.');
    }

    /**
     * Konfirmasi Pengembalian (Selesai + Hitung Denda/Stok).
     */
    public function returnItem(Request $request, $id)
    {
        $user = Auth::user();
        $managedDeptIds = $user->assignedDepartments->pluck('id');

        $loan = Loan::whereHas('user', function($query) use ($managedDeptIds) {
            $query->whereIn('department_id', $managedDeptIds);
        })->findOrFail($id);

        $item = Item::findOrFail($loan->item_id);

        // ✅ Validasi Input
        $request->validate([
            'return_condition' => 'required|in:aman,rusak,hilang',
            'rating'           => 'required|integer|min:1|max:5',
            'admin_note'       => 'nullable|string|max:500',
            'fine_amount'      => 'nullable|numeric|min:0',
            'lost_quantity'    => 'nullable|integer|min:0|max:' . $loan->quantity, // Gak boleh input rusak lebih dari yg dipinjam
            'return_note'      => 'nullable|string|max:255'
        ]);

        $lostQty = $request->lost_quantity ?? 0;
        $fineAmount = $request->fine_amount ?? 0;

        // 1. LOGIC PENGEMBALIAN STOK
        // Stok yang kembali ke rak "Ready" = Total Pinjam - (Rusak + Hilang)
        // Barang rusak/hilang dianggap tidak kembali ke stok "Ready" (perlu penanganan terpisah/write-off)
        $restoredStock = $loan->quantity - $lostQty;

        if ($restoredStock > 0) {
            $item->increment('stock', $restoredStock);
        }

        // 2. Simpan Data Pengembalian
        $loan->update([
            'status'           => 'returned',
            'return_date'      => now(),
            'return_condition' => $request->return_condition,
            'rating'           => $request->rating,
            'fine_amount'      => $fineAmount,
            'lost_quantity'    => $lostQty,
            'fine_status'      => ($fineAmount > 0) ? 'unpaid' : 'paid', // Kalau ada denda, status UNPAID. Kalau 0, otomatis PAID.
            'return_note'      => $request->return_note,
            'admin_note'       => $request->admin_note ?? 'Barang kembali dengan kondisi ' . strtoupper($request->return_condition)
        ]);

        // 3. Notifikasi
        $notifMessage = 'Pengembalian ' . $item->name . ' selesai.';
        if ($fineAmount > 0) {
            $notifMessage .= ' Terdapat denda Rp ' . number_format($fineAmount) . ' (Status: Belum Lunas).';
        }

        $loan->user->notify(new SystemNotification(
            'Pengembalian Selesai',
            $notifMessage,
            route('siswa.request'),
            $fineAmount > 0 ? 'warning' : 'success'
        ));

        return redirect()->back()->with('success', 'Sesi peminjaman ditutup. Data stok dan denda telah diperbarui.');
    }

    /**
     * ✅ FITUR BARU: Tandai Denda Sebagai Lunas (Paid).
     */
    public function markAsPaid($id)
    {
        $user = Auth::user();
        $managedDeptIds = $user->assignedDepartments->pluck('id');

        $loan = Loan::whereHas('user', function($query) use ($managedDeptIds) {
            $query->whereIn('department_id', $managedDeptIds);
        })->findOrFail($id);

        if ($loan->fine_status === 'paid') {
            return redirect()->back()->with('error', 'Tagihan ini sudah lunas sebelumnya.');
        }

        // Update status jadi LUNAS
        $loan->update([
            'fine_status' => 'paid'
        ]);

        // Notifikasi ke Siswa
        $loan->user->notify(new SystemNotification(
            'Pembayaran Denda Diterima',
            'Terima kasih! Denda untuk peminjaman ' . $loan->item->name . ' telah dikonfirmasi LUNAS oleh Toolman.',
            route('siswa.request'),
            'success'
        ));

        return redirect()->back()->with('success', 'Status denda berhasil diubah menjadi LUNAS.');
    }
}