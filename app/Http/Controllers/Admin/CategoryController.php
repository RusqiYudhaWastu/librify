<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Menampilkan halaman Manajemen Kategori Buku
     */
    public function index()
    {
        // 1. Ambil Kategori + Hitung jumlah buku di dalamnya
        // Kita pakai withCount untuk nampilin angka statis (Berapa banyak buku di kategori ini)
        $categories = Category::withCount('items')->latest()->get();

        return view('admin.barang.kategori', compact('categories'));
    }

    /* |--------------------------------------------------------------------------
    | CRUD KATEGORI BUKU (LIBRARY)
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ], [
            'name.unique' => 'Kategori ini sudah ada di dalam sistem.',
            'name.required' => 'Nama kategori tidak boleh kosong.'
        ]);

        Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name) // Tambahkan slug kalau di tabel ada (aman buat masa depan)
        ]);

        return redirect()->back()->with('success', 'Kategori buku berhasil ditambahkan ke sistem!');
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $id,
        ]);

        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name)
        ]);

        return redirect()->back()->with('success', 'Data kategori buku berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        
        // Proteksi: Cek apakah masih ada buku yang terikat ke kategori ini
        if ($category->items()->count() > 0) {
            return redirect()->back()->with('error', 'Gagal hapus! Kategori ini masih memiliki buku di dalamnya. Kosongkan atau pindahkan bukunya terlebih dahulu.');
        }

        $category->delete();

        return redirect()->back()->with('success', 'Kategori buku telah dihapus dari sistem.');
    }
}