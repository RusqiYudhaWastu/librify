<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Department;
use App\Models\ClassRoom; // ✅ Import Model Kelas
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Menampilkan halaman klasifikasi (Tab Kategori, Tab Jurusan & Tab Kelas).
     */
    public function index()
    {
        // 1. Ambil Kategori + Jurusan Terhubung + Daftar Barang di dalamnya
        $categories = Category::with(['departments', 'items'])->latest()->get();

        // 2. Ambil Jurusan + Daftar Kategori + DAFTAR KELAS (classRooms)
        $departments = Department::with(['categories', 'classRooms'])->withCount('categories')->latest()->get();
        
        return view('admin.barang.kategori', compact('categories', 'departments'));
    }

    /* |--------------------------------------------------------------------------
    | BAGIAN 1: CRUD KATEGORI BARANG (ALAT)
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'department_ids' => 'nullable|array', 
        ]);

        $category = Category::create(['name' => $request->name]);
        $category->departments()->sync($request->department_ids ?? []);

        return redirect()->back()->with('success', 'Kategori barang berhasil ditambahkan ke sistem!');
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $id,
            'department_ids' => 'nullable|array',
        ]);

        $category->update(['name' => $request->name]);
        $category->departments()->sync($request->department_ids ?? []);

        return redirect()->back()->with('success', 'Data kategori barang berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return redirect()->back()->with('success', 'Kategori barang telah dihapus.');
    }


    /* |--------------------------------------------------------------------------
    | BAGIAN 2: CRUD JURUSAN (DEPARTMENTS)
    |--------------------------------------------------------------------------
    */

    public function storeDept(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:departments,name'
        ]);

        Department::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name)
        ]);

        return redirect()->back()->with('success', 'Jurusan baru berhasil didaftarkan!');
    }

    public function updateDept(Request $request, $id)
    {
        $dept = Department::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:departments,name,' . $id
        ]);

        $dept->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name)
        ]);

        return redirect()->back()->with('success', 'Nama jurusan berhasil diubah!');
    }

    public function destroyDept($id)
    {
        $dept = Department::findOrFail($id);
        
        // Proteksi: Cek apakah jurusan masih terikat dengan kategori alat
        if ($dept->categories()->count() > 0) {
            return redirect()->back()->with('error', 'Gagal hapus! Jurusan ini masih memiliki otoritas pada beberapa kategori barang.');
        }

        $dept->delete();
        return redirect()->back()->with('success', 'Jurusan telah dihapus dari sistem.');
    }

    /* |--------------------------------------------------------------------------
    | BAGIAN 3: CRUD KELAS (CLASS ROOMS)
    |--------------------------------------------------------------------------
    */

    public function storeClass(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'name' => 'required|string|max:255',
            // 'academic_year' dihapus dari validasi karena inputnya sudah dihilangkan
        ]);

        ClassRoom::create([
            'department_id' => $request->department_id,
            'name' => $request->name,
            'academic_year' => '-', // Default value karena tidak diinput user
        ]);

        return back()->with('success', 'Kelas baru berhasil ditambahkan!');
    }

    // ✅ FITUR BARU: Update Nama Kelas
    public function updateClass(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $kelas = ClassRoom::findOrFail($id);
        $kelas->update([
            'name' => $request->name
        ]);

        return back()->with('success', 'Nama kelas berhasil diperbarui!');
    }

    public function destroyClass($id)
    {
        $kelas = ClassRoom::findOrFail($id);
        
        // Cek apakah kelas ini masih punya siswa aktif?
        if ($kelas->users()->count() > 0) {
            return back()->with('error', 'Gagal hapus! Masih ada siswa yang terdaftar di kelas ini.');
        }

        $kelas->delete();
        return back()->with('success', 'Kelas berhasil dihapus!');
    }
}