<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; // Pastikan model User sudah ada

class UserController extends Controller
{
    /**
     * Menampilkan halaman list pengguna
     */
    public function index()
    {
        // Untuk sementara kita return view-nya saja
        // Pastikan folder resources/views/admin/pengguna/index.blade.php sudah ada
        return view('admin.pengguna.index');
    }

    /**
     * Form tambah pengguna (Opsional untuk nanti)
     */
    public function create()
    {
        return view('admin.pengguna.create');
    }
}