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
use Carbon\Carbon; // ✅ Penting buat manipulasi tanggal/jam

class LoanController extends Controller
{
    /**
     * Tampilkan Halaman Request (Katalog Barang & Riwayat Pinjam)
     * Terfilter Otomatis Berdasarkan Jurusan dari KELAS Siswa + Kategori Umum
     */
    public function index()
    {
        $user = Auth::user();
        
        // 1. Dapatkan ID Jurusan dari Kelas Siswa
        $userDeptId = $user->classRoom ? $user->classRoom->department_id : null;

        // 2. Ambil ID Kategori yang relevan:
        //    A. Kategori Khusus Jurusan Siswa (via Kelas)
        //    B. Kategori Umum (Tidak terikat jurusan manapun)
        $allowedCategoryIds = Category::whereHas('departments', function($query) use ($userDeptId) {
            if ($userDeptId) {
                $query->where('departments.id', $userDeptId);
            } else {
                $query->whereNull('departments.id'); // Fallback jika siswa belum masuk kelas
            }
        })
        ->orWhereDoesntHave('departments') // ✅ Ambil kategori Umum (Global)
        ->pluck('id');

        // 3. Ambil Barang yang:
        //    - Masuk dalam kategori yang diizinkan
        //    - Statusnya READY
        //    - Stoknya ADA (> 0)
        $items = Item::with('category')
                    ->whereIn('category_id', $allowedCategoryIds)
                    ->where('status', 'ready')
                    ->where('stock', '>', 0)
                    ->latest()
                    ->get();
        
        // 4. Ambil Riwayat Pinjam Siswa Ini (Pribadi)
        $myLoans = Loan::with('item')
                    ->where('user_id', Auth::id())
                    ->latest()
                    ->get();

        return view('student.request.index', compact('items', 'myLoans'));
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
        $userDeptId = $user->classRoom ? $user->classRoom->department_id : null;

        // 2. Validasi Otoritas Barang (Security Check)
        $isAllowed = Category::where('id', $item->category_id)
            ->where(function($query) use ($userDeptId) {
                $query->whereHas('departments', function($q) use ($userDeptId) {
                    if ($userDeptId) {
                        $q->where('departments.id', $userDeptId);
                    }
                })
                ->orWhereDoesntHave('departments'); // Izinkan kategori umum
            })->exists();

        if (!$isAllowed) {
            return redirect()->back()->with('error', 'Akses Ditolak! Barang ini tidak tersedia untuk jurusan Anda.');
        }

        // 3. Cek Stok Barang
        if ($request->quantity > $item->stock) {
            return redirect()->back()->with('error', 'Gagal! Stok barang tidak mencukupi.');
        }

        // 4. Hitung Tanggal Pengembalian Otomatis (Estimasi)
        $returnDate = now(); // Mulai dari sekarang

        if ($request->duration_unit === 'hours') {
            // ✅ FIX: Paksa (cast) durasi_amount menjadi (int) agar tidak TypeError
            $returnDate->addHours((int)$request->duration_amount);
        } else {
            // ✅ FIX: Paksa (cast) durasi_amount menjadi (int) dan set ke akhir hari
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
            
            $notifTitle = 'Request Peminjaman Baru';
            $notifMessage = $user->name . ' ingin meminjam ' . $item->name . ' (' . $request->quantity . ' unit) selama ' . $durasiText . '.';
            
            Notification::send($toolmen, new SystemNotification(
                $notifTitle,
                $notifMessage,
                route('toolman.request'), // Arahkan toolman ke halaman request
                'info'
            ));
        } catch (\Exception $e) {
            // Silent fail notif
        }

        return redirect()->back()->with('success', 'Permintaan pinjam berhasil dikirim. Menunggu persetujuan Toolman.');
    }
}