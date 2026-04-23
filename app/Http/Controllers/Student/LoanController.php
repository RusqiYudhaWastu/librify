<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Loan;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\SystemNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str; 
use Carbon\Carbon;

class LoanController extends Controller
{
    /**
     * Tampilkan Halaman Request (Katalog Buku & Riwayat Pinjam Pribadi)
     * Terfilter Otomatis (Global Librify)
     */
    public function index()
    {
        $user = Auth::user();
        
        // 1. Cek apakah user sedang punya tanggungan pinjaman buku
        $hasActiveLoan = Loan::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'approved', 'borrowed'])
            ->exists();

        // 2. Ambil Katalog Buku yang Available (Semua Buku)
        // ✅ FIX UTAMA: Ganti 'category' menjadi 'categories' karena sekarang multi-kategori
        $items = Item::with('categories')
                    ->where('status', 'ready')
                    ->where('stock', '>', 0)
                    ->latest()
                    ->get();
        
        // 3. Ambil Riwayat Pinjam Siswa Ini (Pribadi)
        $myLoans = Loan::with('item')
                    ->where('user_id', $user->id)
                    ->latest()
                    ->get();

        return view('student.request.index', compact('items', 'myLoans', 'hasActiveLoan'));
    }

    /**
     * Simpan Request Peminjaman Baru (Bisa Banyak Buku Sekaligus)
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // 1. BLOKIR JIKA MASIH ADA TANGGUNGAN
        $hasActiveLoan = Loan::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'approved', 'borrowed'])
            ->exists();

        if ($hasActiveLoan) {
            return redirect()->back()->with('error', 'Gagal! Anda masih memiliki transaksi peminjaman buku yang belum selesai/dikembalikan.');
        }

        // 2. Validasi Input (ARRAY of items)
        $request->validate([
            'items'             => 'required|array|min:1',
            'items.*.item_id'   => 'required|exists:items,id',
            'items.*.quantity'  => 'required|numeric|min:1',
            'reason'            => 'required|string|max:500',
            'duration_amount'   => 'required|integer|min:1',
            'duration_unit'     => 'required|in:hours,days',
        ]);

        $validatedItems = [];
        $itemNamesForNotif = [];

        // 3. PRE-CHECK STOK SEMUA BUKU
        foreach ($request->items as $reqItem) {
            $item = Item::findOrFail($reqItem['item_id']);

            // Cek Stok Buku
            if ($reqItem['quantity'] > $item->stock) {
                return redirect()->back()->with('error', 'Gagal! Stok buku "' . $item->name . '" tidak mencukupi.');
            }

            // Masukkan ke array penampung
            $validatedItems[] = [
                'model' => $item,
                'quantity' => $reqItem['quantity']
            ];
            
            // Format string buat notif
            $itemNamesForNotif[] = $item->name . ' (' . $reqItem['quantity'] . ' buku)';
        }

        // 4. GENERATE KODE UNIK (TOKEN PEMINJAMAN)
        $loanCode = strtoupper(Str::random(6));
        
        // Pastikan kodenya unik
        while (Loan::where('loan_code', $loanCode)->exists()) {
            $loanCode = strtoupper(Str::random(6));
        }

        // 5. Hitung Tanggal Pengembalian
        $returnDate = now(); 
        if ($request->duration_unit === 'hours') {
            $returnDate->addHours((int)$request->duration_amount);
        } else {
            $returnDate->addDays((int)$request->duration_amount)->endOfDay();
        }

        // 6. PROSES INSERT KE DATABASE (Looping array buku)
        foreach ($validatedItems as $data) {
            Loan::create([
                'user_id'         => $user->id,
                'item_id'         => $data['model']->id,
                'quantity'        => $data['quantity'],
                'reason'          => $request->reason,
                'status'          => 'pending',
                'loan_code'       => $loanCode, 
                'duration_amount' => $request->duration_amount, 
                'duration_unit'   => $request->duration_unit,
                'return_date'     => $returnDate, 
            ]);
        }

        // 7. Notifikasi ke Petugas / Staff
        try {
            $staffs = User::whereIn('role', ['toolman', 'staff'])->get();
            
            $unitLabel = ($request->duration_unit === 'hours' ? 'Jam' : 'Hari');
            $durasiText = $request->duration_amount . ' ' . $unitLabel;
            
            $barangList = implode(', ', $itemNamesForNotif);
            
            $notifTitle = 'Peminjaman Buku Baru: ' . $loanCode;
            $notifMessage = $user->name . ' ingin meminjam: ' . $barangList . ' selama ' . $durasiText . '. Kode: ' . $loanCode;
            
            Notification::send($staffs, new SystemNotification(
                $notifTitle,
                $notifMessage,
                route('staff.request'),
                'info'
            ));
        } catch (\Exception $e) {
            // Silent fail notif
        }

        // 8. TAMPILKAN KODE KE SISWA DI PESAN SUKSES
        return redirect()->back()->with('success', 'Peminjaman berhasil diajukan! KODE PEMINJAMAN ANDA: ' . $loanCode . '. Harap tunjukkan kode ini ke Petugas Perpustakaan.');
    }
}