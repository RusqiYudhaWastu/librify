<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;

// 1. Import Admin Controllers
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ItemController as AdminItemController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\AuditController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;

// 2. Import Staff/Petugas Controllers (Folder bawaan masih Toolman)
use App\Http\Controllers\Toolman\DashboardController as StaffDashboardController;
use App\Http\Controllers\Toolman\LoanController as StaffLoanController;
use App\Http\Controllers\Toolman\ReportController as StaffReportController;

// 3. Import Siswa Controllers (Role: Class / Perwakilan Kelas)
use App\Http\Controllers\Siswa\DashboardController as SiswaDashboardController;
use App\Http\Controllers\Siswa\LoanController as SiswaLoanController;
use App\Http\Controllers\Siswa\ReportController as SiswaReportController; 

// 4. Import Student Controllers (Role: Student / Siswa Individu)
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Student\LoanController as StudentLoanController;
use App\Http\Controllers\Student\ReportController as StudentReportController;

/*
|--------------------------------------------------------------------------
| Web Routes - Librify System (Library Universal)
|--------------------------------------------------------------------------
*/

// --- A. LANDING PAGE ---
Route::get('/', function () {
    return view('welcome');
});

// --- B. DASHBOARD REDIRECTOR (FIXED TRAP) ---
Route::get('/dashboard', function () {
    $user = Auth::user();
    
    // ✅ PROTEKSI STATUS DIMATIKAN SEMENTARA BIAR BISA LOGIN MULUS
    // if (isset($user->status) && $user->status !== 'approved') {
    //     Auth::logout();
    //     return redirect()->route('login')->with('error', 'Akun Anda belum disetujui oleh Administrator.');
    // }
    
    // ✅ Redirect berdasarkan role (Gua tambahin ALIAS bahasa Indonesia biar ga ketendang)
    if (in_array($user->role, ['admin', 'superadmin'])) {
        return redirect()->route('admin.dashboard');
    } elseif (in_array($user->role, ['toolman', 'staff', 'petugas'])) {
        return redirect()->route('staff.dashboard');
    } elseif (in_array($user->role, ['class', 'kelas'])) {
        return redirect()->route('siswa.dashboard');
    } elseif (in_array($user->role, ['student', 'siswa', 'member'])) {
        return redirect()->route('student.dashboard');
    }
    
    // ✅ SAFETY NET: Kalau role ga jelas, paksa logout dan kasih tau errornya apa!
    Auth::logout();
    return redirect('/login')->with('error', 'Akses ditolak. Role akun Anda (' . $user->role . ') tidak dikenali sistem.'); 
    
})->middleware(['auth'])->name('dashboard'); // ✅ 'verified' dihapus sementara


// --- C. GROUP ROUTE: SUPER ADMIN & STAFF (Indigo Theme) ---
Route::middleware(['auth', 'role:admin,staff,toolman'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        
        Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
        
        // Manajemen Pengguna
        Route::get('/pengguna', [UserController::class, 'index'])->name('pengguna.index');
        Route::post('/pengguna/import', [UserController::class, 'import'])->name('pengguna.import');
        Route::post('/pengguna', [UserController::class, 'store'])->name('pengguna.store');
        Route::put('/pengguna/{id}', [UserController::class, 'update'])->name('pengguna.update');
        Route::delete('/pengguna/{id}', [UserController::class, 'destroy'])->name('pengguna.destroy');

        // Manajemen Buku / Katalog
        Route::get('/barang', [AdminItemController::class, 'index'])->name('barang.index'); 
        Route::post('/barang', [AdminItemController::class, 'store'])->name('barang.store'); 
        Route::put('/barang/{id}', [AdminItemController::class, 'update'])->name('barang.update'); 
        Route::put('/barang/{id}/status', [AdminItemController::class, 'updateStatus'])->name('barang.status'); 
        Route::put('/barang/{id}/maintenance', [AdminItemController::class, 'setMaintenance'])->name('barang.maintenance');
        Route::delete('/barang/{id}', [AdminItemController::class, 'destroy'])->name('barang.destroy');

        // Manajemen Kategori Koleksi & Kelas
        Route::get('/kategori', [CategoryController::class, 'index'])->name('kategori.index');
        Route::post('/kategori', [CategoryController::class, 'store'])->name('kategori.store');
        Route::put('/kategori/{id}', [CategoryController::class, 'update'])->name('kategori.update');
        Route::delete('/kategori/{id}', [CategoryController::class, 'destroy'])->name('kategori.destroy');

        Route::post('/kelas', [CategoryController::class, 'storeClass'])->name('kelas.store');
        Route::put('/kelas/{id}', [CategoryController::class, 'updateClass'])->name('kelas.update');
        Route::delete('/kelas/{id}', [CategoryController::class, 'destroyClass'])->name('kelas.destroy');

        // --- SEKSI AUDIT & LAPORAN ADMIN ---
        Route::get('/audit', [AuditController::class, 'index'])->name('audit');
        Route::get('/laporan', [AdminReportController::class, 'index'])->name('laporan');
        Route::get('/laporan/export', [AdminReportController::class, 'exportPdf'])->name('laporan.export');
    });


// --- D. GROUP ROUTE: PETUGAS PERPUS (Emerald Theme) ---
Route::middleware(['auth', 'role:toolman,staff'])
    ->prefix('staff')
    ->name('staff.')
    ->group(function () {
        
        Route::get('/dashboard', [StaffDashboardController::class, 'index'])->name('dashboard');
        
        // Manajemen Request & Peminjaman Buku
        Route::get('/request', [StaffLoanController::class, 'index'])->name('request');
        
        // Batching Actions
        Route::put('/request/batch-action', [StaffLoanController::class, 'batchAction'])->name('request.batch_action');
        Route::put('/request/batch-return', [StaffLoanController::class, 'batchReturn'])->name('request.batch_return');
        
        // Single Actions
        Route::put('/request/{id}/approve', [StaffLoanController::class, 'approve'])->name('request.approve');
        Route::put('/request/{id}/reject', [StaffLoanController::class, 'reject'])->name('request.reject');
        Route::put('/request/{id}/return', [StaffLoanController::class, 'returnItem'])->name('request.return');
        Route::put('/request/{id}/paid', [StaffLoanController::class, 'markAsPaid'])->name('request.paid');

        // --- SEKSI LAPORAN & KENDALA PETUGAS ---
        Route::get('/laporan', [StaffReportController::class, 'index'])->name('laporan');
        Route::get('/laporan/export', [StaffReportController::class, 'exportPdf'])->name('laporan.export');
        Route::put('/laporan/masalah/{id}', [StaffReportController::class, 'updateProblemStatus'])->name('laporan.update_status');
    });


// --- E. GROUP ROUTE: CLASS REPRESENTATIVE / AKUN KELAS (Blue Theme) ---
// ✅ Tambahin 'kelas' ke dalam middleware biar role indonesianya kebaca
Route::middleware(['auth', 'role:class,kelas'])
    ->prefix('siswa')
    ->name('siswa.')
    ->group(function () {
        
        Route::get('/dashboard', [SiswaDashboardController::class, 'index'])->name('dashboard');
        
        // Pinjam Buku (Untuk Satu Kelas)
        Route::get('/request', [SiswaLoanController::class, 'index'])->name('request');
        Route::post('/request', [SiswaLoanController::class, 'store'])->name('request.store');

        // --- SEKSI LAPORAN & KENDALA KELAS ---
        Route::get('/laporan', [SiswaReportController::class, 'index'])->name('laporan');
        Route::get('/laporan/rapot', [SiswaReportController::class, 'rapot'])->name('laporan.rapot');
        Route::post('/laporan/masalah', [SiswaReportController::class, 'storeProblem'])->name('laporan.problem');
        Route::get('/laporan/export', [SiswaReportController::class, 'exportPdf'])->name('laporan.export');
    });

// --- F. GROUP ROUTE: STUDENT / SISWA INDIVIDU (Cyan Theme) ---
// ✅ Tambahin 'siswa' ke dalam middleware
Route::middleware(['auth', 'role:student,siswa'])
    ->prefix('student')
    ->name('student.')
    ->group(function () {
        
        // Dashboard Pribadi
        Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
        
        // Pinjam Buku (Pribadi)
        Route::get('/request', [StudentLoanController::class, 'index'])->name('request');
        Route::post('/request', [StudentLoanController::class, 'store'])->name('request.store');

        // --- SEKSI LAPORAN & KENDALA PRIBADI ---
        Route::get('/laporan', [StudentReportController::class, 'index'])->name('laporan');
        Route::post('/laporan', [StudentReportController::class, 'store'])->name('laporan.store'); 
        Route::get('/laporan/export', [StudentReportController::class, 'exportPdf'])->name('laporan.export'); 
    });


// --- G. PROFILE MANAGEMENT ---
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


// --- H. NOTIFICATION SYSTEM (UNIVERSAL) ---
Route::middleware('auth')->group(function () {
    
    // 1. Tandai satu notifikasi sudah dibaca
    Route::get('/notifications/{id}/read', function ($id) {
        $notification = auth()->user()->notifications()->find($id);

        if($notification) {
            $notification->markAsRead();
            return redirect($notification->data['url'] ?? $notification->data['link'] ?? back());
        }

        return back();
    })->name('notifications.read');

    // 2. Tandai SEMUA notifikasi sudah dibaca
    Route::get('/notifications/mark-all-read', function () {
        auth()->user()->unreadNotifications->markAsRead();
        return back();
    })->name('notifications.markAllRead');

});

require __DIR__.'/auth.php';