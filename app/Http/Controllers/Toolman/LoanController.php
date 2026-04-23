<?php

namespace App\Http\Controllers\Toolman;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; 
use App\Notifications\SystemNotification;
use Carbon\Carbon;

class LoanController extends Controller
{
    /**
     * Tampilkan semua daftar pinjaman (Global untuk Librify).
     */
    public function index()
    {
        // Ambil semua data sirkulasi tanpa filter jurusan, karena petugas aksesnya global
        $requests = Loan::with(['user', 'item.categories'])
            ->latest()
            ->get()
            ->map(function($loan) {
                // Hitung nilai rata-rata perilaku peminjam saat mengembalikan buku sebelumnya
                $loan->user_rating = Loan::where('user_id', $loan->user_id)
                                         ->where('status', 'returned')
                                         ->avg('rating');
                return $loan;
            });
        
        return view('toolman.request.index', compact('requests'));
    }

    /**
     * Setujui Peminjaman Buku (ACC).
     */
    public function approve(Request $request, $id)
    {
        $loan = Loan::findOrFail($id);
        $item = Item::findOrFail($loan->item_id);

        // Wajibkan input 'loan_code' dari form
        $request->validate([
            'approved_quantity' => 'required|numeric|min:1|max:' . $item->stock,
            'loan_code'         => 'required|string', 
        ], [
            'approved_quantity.max' => 'Maaf, stok ' . $item->name . ' di perpustakaan tidak mencukupi. Saat ini hanya tersedia ' . $item->stock . ' buku.',
            'loan_code.required'    => 'Verifikasi gagal! Anda harus memasukkan Kode Peminjaman.',
        ]);

        // Cocokkan kode yang diinput Petugas dengan kode di database
        if (!empty($loan->loan_code) && strtoupper($request->loan_code) !== $loan->loan_code) {
            return redirect()->back()->with('error', 'Verifikasi Gagal! Kode peminjaman yang dimasukkan salah atau tidak sesuai.');
        }

        $finalQty = $request->approved_quantity;

        // Simpan data sekaligus agar stok dan statusnya sinkron
        DB::transaction(function () use ($loan, $item, $finalQty, $request) {
            // 1. Kurangi stok buku di perpustakaan
            $item->decrement('stock', $finalQty);

            // 2. Update status pinjaman jadi disetujui
            $loan->update([
                'quantity'   => $finalQty, 
                'status'     => 'approved',
                'loan_date'  => now(),
                'admin_note' => $request->admin_note ?? 'Permintaan disetujui. Silakan segera ambil buku di perpustakaan.'
            ]);
        });

        // Info batas waktu pengembalian
        $deadlineInfo = Carbon::parse($loan->return_date)->translatedFormat('d F Y H:i');

        // Dynamic Route Notif (Siswa vs Kelas)
        $routeNotif = $loan->user->role === 'student' ? route('student.request') : route('siswa.request');

        // Kirim kabar ke peminjam
        $loan->user->notify(new SystemNotification(
            'Peminjaman Buku Disetujui',
            'Mantap! Permintaan peminjaman ' . $item->name . ' sebanyak ' . $finalQty . ' buku telah disetujui. Silakan ambil bukunya dan mohon dikembalikan tepat waktu sebelum: ' . $deadlineInfo . '.',
            $routeNotif,
            'success'
        ));

        return redirect()->back()->with('success', 'Permintaan peminjaman ' . $item->name . ' sebanyak ' . $finalQty . ' buku berhasil disetujui dan notifikasi telah dikirim.');
    }

    /**
     * Tolak Peminjaman Buku.
     */
    public function reject(Request $request, $id)
    {
        $loan = Loan::findOrFail($id);

        $request->validate([
            'admin_note' => 'required|string|max:255'
        ], [
            'admin_note.required' => 'Mohon isi alasan penolakan agar peminjam tahu mengapa permintaannya tidak disetujui.'
        ]);

        $loan->update([
            'status'     => 'rejected',
            'admin_note' => $request->admin_note
        ]);

        $routeNotif = $loan->user->role === 'student' ? route('student.request') : route('siswa.request');

        // Kirim kabar ke peminjam
        $loan->user->notify(new SystemNotification(
            'Informasi Peminjaman',
            'Mohon maaf, permintaan peminjaman buku Anda tidak dapat kami proses saat ini. Catatan dari Petugas: "' . $request->admin_note . '". Silakan hubungi Petugas untuk info lebih lanjut.',
            $routeNotif,
            'danger'
        ));

        return redirect()->back()->with('success', 'Permintaan peminjaman berhasil ditolak, dan pesan pemberitahuan telah dikirimkan ke peminjam yang bersangkutan.');
    }

    /**
     * Proses buku yang dikembalikan (Selesai).
     */
    public function returnItem(Request $request, $id)
    {
        $loan = Loan::findOrFail($id);
        $item = Item::findOrFail($loan->item_id);

        // Validasi input data pengembalian
        $request->validate([
            'return_condition' => 'required|in:aman,rusak,hilang',
            'rating'           => 'required|integer|min:1|max:5',
            'admin_note'       => 'nullable|string|max:500',
            'fine_amount'      => 'nullable|numeric|min:0',
            'lost_quantity'    => 'nullable|integer|min:0|max:' . $loan->quantity,
            'return_note'      => 'nullable|string|max:255'
        ]);

        $lostQty = $request->lost_quantity ?? 0;
        $fineAmount = $request->fine_amount ?? 0;
        $condition = $request->return_condition;

        // ✅ UPDATE: Logic cerdas untuk sinkronisasi kerusakan dengan Katalog Admin
        DB::transaction(function () use ($loan, $item, $request, $lostQty, $fineAmount, $condition) {
            
            // Jika dilaporkan rusak/hilang tapi jumlahnya 0, kita asumsikan semua buku dalam transaksi ini bermasalah
            if (in_array($condition, ['rusak', 'hilang']) && $lostQty == 0) {
                $lostQty = $loan->quantity;
            }

            // Hitung berapa buku yang kembali dalam kondisi baik (aman)
            $restoredStock = $loan->quantity - $lostQty;

            // Tambahkan stok buku yang aman ke rak
            if ($restoredStock > 0) {
                $item->stock += $restoredStock;
            }

            // Tambahkan stok buku yang bermasalah ke status "Rusak/Hilang" di Katalog Admin
            if ($lostQty > 0) {
                $item->broken_stock += $lostQty;
            }

            // Auto-update status label di Katalog Admin
            if ($item->stock > 0) {
                $item->status = 'ready';
            } elseif ($item->maintenance_stock > 0) {
                $item->status = 'maintenance';
            } elseif ($item->broken_stock > 0) {
                $item->status = 'broken';
            } else {
                $item->status = 'ready';
            }
            
            $item->save();

            // Simpan info pengembalian
            $loan->update([
                'status'           => 'returned',
                'return_date'      => now(),
                'return_condition' => $condition,
                'rating'           => $request->rating,
                'fine_amount'      => $fineAmount,
                'lost_quantity'    => $lostQty,
                'fine_status'      => ($fineAmount > 0) ? 'unpaid' : 'paid',
                'return_note'      => $request->return_note,
                'admin_note'       => $request->admin_note ?? 'Buku sudah diverifikasi kembali dalam kondisi ' . strtoupper($condition) . '.'
            ]);
        });

        // Buat pesan notifikasi
        $notifMessage = 'Terima kasih! Buku ' . $item->name . ' telah berhasil dikembalikan dan diverifikasi oleh Petugas dalam kondisi ' . strtoupper($condition) . '.';
        if ($fineAmount > 0) {
            $notifMessage .= ' Perhatian: Terdapat tagihan denda sebesar Rp ' . number_format($fineAmount, 0, ',', '.') . ' yang perlu segera diselesaikan.';
        }

        $routeNotif = $loan->user->role === 'student' ? route('student.request') : route('siswa.request');

        $loan->user->notify(new SystemNotification(
            'Pengembalian Buku Selesai',
            $notifMessage,
            $routeNotif,
            $fineAmount > 0 ? 'warning' : 'success'
        ));

        return redirect()->back()->with('success', 'Proses pengembalian ' . $item->name . ' berhasil dicatat. Stok perpustakaan telah diperbarui otomatis sesuai dengan kondisi buku.');
    }

    /**
     * Konfirmasi kalau denda sudah dibayar.
     */
    public function markAsPaid($id)
    {
        $loan = Loan::findOrFail($id);

        if ($loan->fine_status === 'paid') {
            return redirect()->back()->with('error', 'Pembayaran gagal diproses: Tagihan ini sudah tercatat lunas sebelumnya.');
        }

        $loan->update([
            'fine_status' => 'paid'
        ]);

        $routeNotif = $loan->user->role === 'student' ? route('student.request') : route('siswa.request');

        // Kabari peminjam
        $loan->user->notify(new SystemNotification(
            'Pembayaran Denda Diterima',
            'Terima kasih atas kerjasamanya! Pembayaran denda Anda untuk peminjaman buku ' . $loan->item->name . ' telah kami terima dan status tagihannya kini sudah LUNAS.',
            $routeNotif,
            'success'
        ));

        return redirect()->back()->with('success', 'Mantap! Pembayaran denda berhasil dikonfirmasi. Status tagihan peminjam kini telah menjadi LUNAS.');
    }

    /**
     * Proses banyak permintaan sekaligus (Setujui atau Tolak bareng-bareng).
     */
    public function batchAction(Request $request)
    {
        $request->validate([
            'ids'    => 'required|array',
            'ids.*'  => 'exists:loans,id',
            'action' => 'required|in:approve,reject'
        ]);

        $loans = Loan::whereIn('id', $request->ids)
            ->where('status', 'pending')
            ->get();

        if ($loans->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada data permintaan valid yang dipilih, atau permintaan tersebut mungkin sudah diproses sebelumnya.');
        }

        DB::beginTransaction();
        try {
            foreach ($loans as $loan) {
                $routeNotif = $loan->user->role === 'student' ? route('student.request') : route('siswa.request');

                if ($request->action === 'approve') {
                    // Validasi kode peminjaman untuk proses batch
                    $inputCode = $request->loan_codes[$loan->id] ?? null;
                    
                    if (!empty($loan->loan_code) && strtoupper($inputCode) !== $loan->loan_code) {
                        throw new \Exception('Kode peminjaman salah untuk ID Peminjaman #' . $loan->id . '. Dibatalkan.');
                    }

                    $item = Item::findOrFail($loan->item_id);
                    $finalQty = $request->approved_quantities[$loan->id] ?? $loan->quantity;

                    if ($item->stock >= $finalQty) {
                        $item->decrement('stock', $finalQty);

                        $loan->update([
                            'quantity'   => $finalQty,
                            'status'     => 'approved',
                            'loan_date'  => now(),
                            'admin_note' => $request->admin_note ?? 'Permintaan disetujui oleh Petugas Perpustakaan.'
                        ]);

                        $deadlineInfo = Carbon::parse($loan->return_date)->translatedFormat('d F Y H:i');
                        $loan->user->notify(new SystemNotification(
                            'Peminjaman Buku Disetujui',
                            'Permintaan buku ' . $item->name . ' milik Anda telah disetujui. Silakan ambil di perpustakaan dan ingat untuk mengembalikan sebelum batas waktu: ' . $deadlineInfo . '.',
                            $routeNotif,
                            'success'
                        ));
                    } else {
                        continue; // Lewati jika stok tidak cukup
                    }
                } elseif ($request->action === 'reject') {
                    $loan->update([
                        'status'     => 'rejected',
                        'admin_note' => $request->admin_note ?? 'Permintaan tidak dapat dipenuhi saat ini.'
                    ]);

                    $loan->user->notify(new SystemNotification(
                        'Informasi Peminjaman',
                        'Mohon maaf, permintaan buku Anda terpaksa kami tolak dalam peninjauan ini. Catatan Petugas: "' . ($request->admin_note ?? 'Permintaan tidak dapat dipenuhi saat ini.') . '"',
                        $routeNotif,
                        'danger'
                    ));
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Proses persetujuan berhasil dijalankan! Semua data peminjaman yang valid telah diperbarui statusnya.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Ups, ada masalah saat memproses data: ' . $e->getMessage());
        }
    }

    /**
     * Selesaikan banyak pengembalian sekaligus (Bacth Return).
     */
    public function batchReturn(Request $request)
    {
        $request->validate([
            'ids'      => 'required|array',
            'ids.*'    => 'exists:loans,id',
            'rating'   => 'required|integer|min:1|max:5',
            'returns'  => 'required|array', 
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->ids as $id) {
                $returnData = $request->returns[$id] ?? null;
                if (!$returnData) continue;

                $loan = Loan::findOrFail($id);
                $item = Item::findOrFail($loan->item_id);

                $lostQty = $returnData['lost_quantity'] ?? 0;
                $fineAmount = $returnData['fine'] ?? 0;
                $condition = $returnData['condition'] ?? 'aman';

                // ✅ UPDATE: Logic sinkronisasi jika pakai fitur centang semua
                if (in_array($condition, ['rusak', 'hilang']) && $lostQty == 0) {
                    $lostQty = $loan->quantity;
                }

                $restoredStock = $loan->quantity - $lostQty;
                
                if ($restoredStock > 0) {
                    $item->stock += $restoredStock;
                }
                
                if ($lostQty > 0) {
                    $item->broken_stock += $lostQty;
                }

                // Update Status Global Katalog
                if ($item->stock > 0) {
                    $item->status = 'ready';
                } elseif ($item->maintenance_stock > 0) {
                    $item->status = 'maintenance';
                } elseif ($item->broken_stock > 0) {
                    $item->status = 'broken';
                } else {
                    $item->status = 'ready';
                }
                $item->save();

                $loan->update([
                    'status'           => 'returned',
                    'return_date'      => now(),
                    'return_condition' => $condition,
                    'rating'           => $request->rating,
                    'fine_amount'      => $fineAmount,
                    'lost_quantity'    => $lostQty,
                    'fine_status'      => ($fineAmount > 0) ? 'unpaid' : 'paid',
                    'return_note'      => $returnData['note'] ?? null,
                    'admin_note'       => 'Buku sudah diverifikasi dan dikembalikan melalui proses serentak.'
                ]);
                
                $routeNotif = $loan->user->role === 'student' ? route('student.request') : route('siswa.request');

                $notifMsg = 'Terima kasih, buku ' . $item->name . ' telah kami konfirmasi pengembaliannya.';
                if($fineAmount > 0) {
                    $notifMsg .= ' Terdapat denda Rp ' . number_format($fineAmount, 0, ',', '.') . ' yang perlu dilunasi.';
                }
                
                $loan->user->notify(new SystemNotification(
                    'Pengembalian Buku Selesai',
                    $notifMsg,
                    $routeNotif,
                    $fineAmount > 0 ? 'warning' : 'success'
                ));
            }

            DB::commit();
            return redirect()->back()->with('success', 'Pengembalian buku secara serentak berhasil diselesaikan! Stok perpustakaan (Aman maupun Rusak) telah disesuaikan secara otomatis.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Ups, terjadi kesalahan sistem saat memproses pengembalian data: ' . $e->getMessage());
        }
    }
}