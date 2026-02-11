<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Loan;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\SystemNotification;
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon; // ✅ Penting buat manipulasi tanggal/jam

class LoanController extends Controller
{
    /**
     * Tampilkan Halaman Request (Katalog Barang & Riwayat Pinjam)
     * Terfilter Otomatis Berdasarkan Jurusan Akun Login + Kategori Umum
     */
    public function index()
    {
        $user = Auth::user();

        // 1. Ambil ID Kategori yang relevan:
        //    A. Kategori Khusus Jurusan Siswa
        //    B. Kategori Umum (Tidak terikat jurusan manapun)
        $allowedCategoryIds = Category::whereHas('departments', function($query) use ($user) {
            $query->where('departments.id', $user->department_id);
        })
        ->orWhereDoesntHave('departments') // ✅ Tambahkan ini: Ambil juga kategori tanpa relasi jurusan (Umum)
        ->pluck('id');

        // 2. Ambil Barang yang:
        //    - Masuk dalam kategori yang diizinkan (Khusus / Umum)
        //    - Statusnya READY
        //    - Stoknya ADA (> 0)
        $items = Item::with('category')
                    ->whereIn('category_id', $allowedCategoryIds)
                    ->where('status', 'ready')
                    ->where('stock', '>', 0)
                    ->latest()
                    ->get();
        
        // 3. Ambil Riwayat Pinjam punya Kelas ini saja (Termasuk Denda)
        $myLoans = Loan::with('item')
                    ->where('user_id', Auth::id())
                    ->latest()
                    ->get();

        return view('siswa.request.index', compact('items', 'myLoans'));
    }

    /**
     * Simpan Request Peminjaman Baru (Dengan Estimasi Durasi)
     */
    public function store(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'item_id'         => 'required|exists:items,id',
            'quantity'        => 'required|numeric|min:1',
            'reason'          => 'required|string|max:500',
            'duration_amount' => 'required|integer|min:1',      // ✅ Angka durasi
            'duration_unit'   => 'required|in:hours,days',        // ✅ Satuan (jam/hari)
        ]);

        $item = Item::findOrFail($request->item_id);
        $user = Auth::user();

        // 2. Validasi Otoritas Barang (Security Check)
        $isAllowed = Category::where('id', $item->category_id)
            ->where(function($query) use ($user) {
                $query->whereHas('departments', function($q) use ($user) {
                    $q->where('departments.id', $user->department_id);
                })
                ->orWhereDoesntHave('departments'); // ✅ Izinkan juga jika kategori umum
            })->exists();

        if (!$isAllowed) {
            return redirect()->back()->with('error', 'Otoritas Gagal! Barang ini tidak dialokasikan untuk jurusan Anda.');
        }

        // 3. Cek Stok Barang
        if ($request->quantity > $item->stock) {
            return redirect()->back()->with('error', 'Gagal! Stok barang tidak mencukupi.');
        }

        // 4. Hitung Tanggal Pengembalian Otomatis (Estimasi)
        $returnDate = now(); // Mulai dari sekarang

        if ($request->duration_unit === 'hours') {
            // ✅ FIX: Paksa (cast) menjadi (int) agar Carbon tidak error
            $returnDate->addHours((int)$request->duration_amount);
        } else {
            // ✅ FIX: Paksa (cast) menjadi (int) dan set ke akhir hari
            $returnDate->addDays((int)$request->duration_amount)->endOfDay();
        }

        // 5. Simpan Data Peminjaman
        Loan::create([
            'user_id'     => Auth::id(),
            'item_id'     => $request->item_id,
            'quantity'    => $request->quantity,
            'reason'      => $request->reason,
            'status'      => 'pending',
            'return_date' => $returnDate, // ✅ Simpan hasil perhitungan tanggal kembali
        ]);

        // 6. Notifikasi ke Toolman
        try {
            $toolmen = User::where('role', 'toolman')->get();
            
            // Format pesan notifikasi agar Toolman tau durasinya
            $durasiText = $request->duration_amount . ' ' . ($request->duration_unit === 'hours' ? 'Jam' : 'Hari');
            
            $notifTitle = 'Permintaan Peminjaman Baru';
            $notifMessage = $user->name . ' mengajukan peminjaman: ' . $item->name . ' (' . $request->quantity . ' unit) selama ' . $durasiText . '.';
            
            Notification::send($toolmen, new SystemNotification(
                $notifTitle,
                $notifMessage,
                route('toolman.request'), // Arahkan toolman ke halaman request
                'info'
            ));
        } catch (\Exception $e) {
            // Silent fail notif jika ada error mailer/driver
        }

        return redirect()->back()->with('success', 'Permintaan pinjam berhasil dikirim. Menunggu verifikasi Toolman!');
    }
}