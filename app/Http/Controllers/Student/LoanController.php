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
        // User (Student) -> ClassRoom -> Department
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
        $userDeptId = $user->classRoom ? $user->classRoom->department_id : null;

        // VALIDASI OTORITAS (Server Side Security):
        // Cek apakah barang ini milik kategori jurusan siswa ATAU kategori umum
        $isAllowed = Category::where('id', $item->category_id)
            ->where(function($query) use ($userDeptId) {
                $query->whereHas('departments', function($q) use ($userDeptId) {
                    if ($userDeptId) {
                        $q->where('departments.id', $userDeptId);
                    }
                })
                ->orWhereDoesntHave('departments'); // ✅ Izinkan juga jika kategori umum
            })->exists();

        if (!$isAllowed) {
            return redirect()->back()->with('error', 'Akses Ditolak! Barang ini tidak tersedia untuk jurusan kelas Anda.');
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
            // Kirim notifikasi ke Toolman yang bertugas di jurusan yang sama (atau semua toolman)
            $toolmen = User::where('role', 'toolman')->get();
            
            $notifTitle = 'Request Siswa Baru';
            $notifMessage = $user->name . ' (' . ($user->classRoom->name ?? 'No Class') . ') meminjam: ' . $item->name . ' (' . $request->quantity . ' unit).';
            
            Notification::send($toolmen, new SystemNotification(
                $notifTitle,
                $notifMessage,
                route('toolman.request'), // Arahkan toolman ke halaman request mereka
                'info'
            ));
        } catch (\Exception $e) {
            // Silent fail notif jika ada error mailer/driver
        }

        return redirect()->back()->with('success', 'Permintaan pinjam berhasil dikirim. Menunggu persetujuan Toolman.');
    }
}