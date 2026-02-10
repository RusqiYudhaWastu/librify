<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use App\Models\Loan;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Menampilkan Dashboard Utama Admin dengan data statistik real-time.
     */
    public function index()
    {
        // 1. Statistik Utama (Cards)
        $stats = [
            'total_users'      => User::count(),
            'total_items'      => Item::count(),
            'total_categories' => Category::count(),
            'active_loans'     => Loan::where('status', 'approved')->count(),
            'pending_requests' => Loan::where('status', 'pending')->count(),
            'maintenance'      => Item::where('status', 'maintenance')->count(),
            
            // Statistik Role Akun (Untuk Sidebar Status Otoritas)
            'role_admin'       => User::where('role', 'admin')->count(),
            'role_toolman'     => User::where('role', 'toolman')->count(),
            'role_class'       => User::where('role', 'class')->count(),
        ];

        // 2. Data Distribusi Aset Per Kategori (Untuk Progress Bars)
        $categories = Category::withCount('items')->get();

        // 3. Log Aktivitas Terbaru Lintas Unit (Eager Loading User & Item)
        $recentActivities = Loan::with(['user', 'item'])
                                ->latest()
                                ->take(6)
                                ->get();

        return view('admin.dashboard.index', compact('stats', 'categories', 'recentActivities'));
    }
}