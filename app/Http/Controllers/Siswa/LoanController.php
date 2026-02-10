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

        // 4. Ambil daftar Kategori untuk filter di View (Opsional)
        // $categories = Category::whereIn('id', $allowedCategoryIds)->get();

        return view('siswa.request.index', compact('items', 'myLoans'));
    }

    /**
     * Simpan Request Peminjaman Baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'item_id'  => 'required|exists:items,id',
            'quantity' => 'required|numeric|min:1',
            'reason'   => 'required|string|max:500',
        ]);

        $item = Item::findOrFail($request->item_id);
        $user = Auth::user();

        // VALIDASI OTORITAS:
        // Cek apakah barang ini milik kategori jurusan siswa ATAU kategori umum
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

        // Cek stok apakah cukup
        if ($request->quantity > $item->stock) {
            return redirect()->back()->with('error', 'Gagal! Stok barang tidak mencukupi.');
        }

        // Simpan Request
        Loan::create([
            'user_id'  => Auth::id(),
            'item_id'  => $request->item_id,
            'quantity' => $request->quantity,
            'reason'   => $request->reason,
            'status'   => 'pending',
        ]);

        // LOGIC NOTIFIKASI KE TOOLMAN
        try {
            // Ambil Toolman yang bertugas di jurusan siswa ini (Biar notifnya tepat sasaran)
            // Atau ambil semua toolman (sederhana)
            $toolmen = User::where('role', 'toolman')->get();
            
            $notifTitle = 'Permintaan Peminjaman Baru';
            $notifMessage = $user->name . ' mengajukan peminjaman: ' . $item->name . ' (' . $request->quantity . ' unit).';
            
            Notification::send($toolmen, new SystemNotification(
                $notifTitle,
                $notifMessage,
                route('toolman.request'),
                'info'
            ));
        } catch (\Exception $e) {
            // Silent fail notif
        }

        return redirect()->back()->with('success', 'Permintaan pinjam berhasil dikirim. Menunggu verifikasi Toolman!');
    }
}