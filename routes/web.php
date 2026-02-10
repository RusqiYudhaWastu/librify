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

// 2. Import Toolman Controllers
use App\Http\Controllers\Toolman\DashboardController as ToolmanDashboardController;
use App\Http\Controllers\Toolman\LoanController as ToolmanLoanController;
use App\Http\Controllers\Toolman\ReportController as ToolmanReportController;

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
| Web Routes - TekniLog System (SMKN 1 CIOMAS)
|--------------------------------------------------------------------------
*/

// --- A. LANDING PAGE ---
Route::get('/', function () {
    return view('welcome');
});

// --- B. DASHBOARD REDIRECTOR ---
Route::get('/dashboard', function () {
    $user = Auth::user();
    
    if ($user->role === 'admin') {
        return redirect()->route('admin.dashboard');
    } elseif ($user->role === 'toolman') {
        return redirect()->route('toolman.dashboard');
    } elseif ($user->role === 'class') {
        return redirect()->route('siswa.dashboard');
    } elseif ($user->role === 'student') {
        return redirect()->route('student.dashboard');
    }
    
    return redirect('/'); 
})->middleware(['auth', 'verified'])->name('dashboard');


// --- C. GROUP ROUTE: SUPER ADMIN (Indigo Theme) ---
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        
        Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
        
        // Manajemen Pengguna
        Route::get('/pengguna', [UserController::class, 'index'])->name('pengguna.index');
        Route::post('/pengguna', [UserController::class, 'store'])->name('pengguna.store');
        Route::put('/pengguna/{id}', [UserController::class, 'update'])->name('pengguna.update');
        Route::delete('/pengguna/{id}', [UserController::class, 'destroy'])->name('pengguna.destroy');

        // Manajemen Barang / Asset
        Route::get('/barang', [AdminItemController::class, 'index'])->name('barang.index'); 
        Route::post('/barang', [AdminItemController::class, 'store'])->name('barang.store'); 
        Route::put('/barang/{id}', [AdminItemController::class, 'update'])->name('barang.update'); 
        Route::put('/barang/{id}/status', [AdminItemController::class, 'updateStatus'])->name('barang.status'); 
        Route::put('/barang/{id}/maintenance', [AdminItemController::class, 'setMaintenance'])->name('barang.maintenance');
        Route::delete('/barang/{id}', [AdminItemController::class, 'destroy'])->name('barang.destroy');

        // Manajemen Klasifikasi (Kategori & Jurusan)
        Route::get('/kategori', [CategoryController::class, 'index'])->name('kategori.index');
        Route::post('/kategori', [CategoryController::class, 'store'])->name('kategori.store');
        Route::put('/kategori/{id}', [CategoryController::class, 'update'])->name('kategori.update');
        Route::delete('/kategori/{id}', [CategoryController::class, 'destroy'])->name('kategori.destroy');

        Route::post('/jurusan', [CategoryController::class, 'storeDept'])->name('jurusan.store');
        Route::put('/jurusan/{id}', [CategoryController::class, 'updateDept'])->name('jurusan.update');
        Route::delete('/jurusan/{id}', [CategoryController::class, 'destroyDept'])->name('jurusan.destroy');
        
        // Manajemen Kelas
        Route::post('/kelas', [CategoryController::class, 'storeClass'])->name('kelas.store');
        Route::put('/kelas/{id}', [CategoryController::class, 'updateClass'])->name('kelas.update');
        Route::delete('/kelas/{id}', [CategoryController::class, 'destroyClass'])->name('kelas.destroy');

        // --- SEKSI AUDIT & LAPORAN ADMIN ---
        Route::get('/audit', [AuditController::class, 'index'])->name('audit');
        Route::get('/laporan', [AdminReportController::class, 'index'])->name('laporan');
        Route::get('/laporan/export', [AdminReportController::class, 'exportPdf'])->name('laporan.export');
    });


// --- D. GROUP ROUTE: TOOLMAN (Emerald Theme) ---
Route::middleware(['auth', 'role:toolman'])
    ->prefix('toolman')
    ->name('toolman.')
    ->group(function () {
        
        Route::get('/dashboard', [ToolmanDashboardController::class, 'index'])->name('dashboard');
        
        // Manajemen Request & Peminjaman
        Route::get('/request', [ToolmanLoanController::class, 'index'])->name('request');
        Route::put('/request/{id}/approve', [ToolmanLoanController::class, 'approve'])->name('request.approve');
        Route::put('/request/{id}/reject', [ToolmanLoanController::class, 'reject'])->name('request.reject');
        Route::put('/request/{id}/return', [ToolmanLoanController::class, 'returnItem'])->name('request.return');
        
        // Pelunasan Denda
        Route::put('/request/{id}/paid', [ToolmanLoanController::class, 'markAsPaid'])->name('request.paid');

        // --- SEKSI LAPORAN & KENDALA TOOLMAN ---
        Route::get('/laporan', [ToolmanReportController::class, 'index'])->name('laporan');
        Route::get('/laporan/export', [ToolmanReportController::class, 'exportPdf'])->name('laporan.export');
        Route::put('/laporan/masalah/{id}', [ToolmanReportController::class, 'updateProblemStatus'])->name('laporan.update_status');
    });


// --- E. GROUP ROUTE: CLASS REPRESENTATIVE / PERWAKILAN KELAS (Blue Theme) ---
Route::middleware(['auth', 'role:class'])
    ->prefix('siswa')
    ->name('siswa.')
    ->group(function () {
        
        Route::get('/dashboard', [SiswaDashboardController::class, 'index'])->name('dashboard');
        
        // Booking Alat (Untuk Satu Kelas)
        Route::get('/request', [SiswaLoanController::class, 'index'])->name('request');
        Route::post('/request', [SiswaLoanController::class, 'store'])->name('request.store');

        // --- SEKSI LAPORAN & KENDALA SISWA ---
        Route::get('/laporan', [SiswaReportController::class, 'index'])->name('laporan');
        Route::post('/laporan/masalah', [SiswaReportController::class, 'storeProblem'])->name('laporan.problem');
        Route::get('/laporan/export', [SiswaReportController::class, 'exportPdf'])->name('laporan.export');
    });

// --- F. GROUP ROUTE: STUDENT / SISWA INDIVIDU (Cyan Theme) ---
Route::middleware(['auth', 'role:student'])
    ->prefix('student')
    ->name('student.')
    ->group(function () {
        
        // Dashboard Pribadi
        Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
        
        // Booking Alat (Pribadi)
        Route::get('/request', [StudentLoanController::class, 'index'])->name('request');
        Route::post('/request', [StudentLoanController::class, 'store'])->name('request.store');

        // --- SEKSI LAPORAN & KENDALA PRIBADI ---
        Route::get('/laporan', [StudentReportController::class, 'index'])->name('laporan');
        Route::post('/laporan', [StudentReportController::class, 'store'])->name('laporan.store'); // ✅ FIXED: Route name matches controller
        Route::get('/laporan/export', [StudentReportController::class, 'exportPdf'])->name('laporan.export'); // ✅ FIXED: Route name matches controller
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