<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // ✅ Import Facade Storage

class ItemController extends Controller
{
    /**
     * Tampilkan semua daftar inventaris & kategori.
     */
    public function index()
    {
        $items = Item::with('category')->latest()->get();
        $categories = Category::all();
        
        return view('admin.barang.index', compact('items', 'categories'));
    }

    /**
     * Simpan barang baru (Termasuk Foto).
     */
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name'        => 'required|string|max:255',
            'asset_code'  => 'required|unique:items,asset_code',
            'stock'       => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // ✅ Validasi Gambar
        ]);

        $data = [
            'category_id' => $request->category_id,
            'name'        => $request->name,
            'asset_code'  => $request->asset_code,
            'stock'       => $request->stock,
            'description' => $request->description,
            'status'      => 'ready',
        ];

        // ✅ LOGIC UPLOAD GAMBAR
        if ($request->hasFile('image')) {
            // Simpan ke folder 'storage/app/public/items'
            $data['image'] = $request->file('image')->store('items', 'public');
        }

        Item::create($data);

        return redirect()->back()->with('success', 'Barang baru berhasil diinput!');
    }

    /**
     * Update data barang (Termasuk Ganti Foto).
     */
    public function update(Request $request, $id)
    {
        $item = Item::findOrFail($id);

        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name'        => 'required|string|max:255',
            'asset_code'  => 'required|unique:items,asset_code,' . $id,
            'stock'       => 'required|numeric|min:0',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // ✅ Validasi Gambar
        ]);

        $data = [
            'category_id' => $request->category_id,
            'name'        => $request->name,
            'asset_code'  => $request->asset_code,
            'stock'       => $request->stock,
            'description' => $request->description,
        ];

        // ✅ LOGIC GANTI GAMBAR
        if ($request->hasFile('image')) {
            // 1. Hapus gambar lama jika ada
            if ($item->image && Storage::disk('public')->exists($item->image)) {
                Storage::disk('public')->delete($item->image);
            }
            // 2. Simpan gambar baru
            $data['image'] = $request->file('image')->store('items', 'public');
        }

        $item->update($data);

        return redirect()->back()->with('success', 'Informasi barang diperbarui!');
    }

    /**
     * Update Status Kondisi (Ready, Maintenance, Broken).
     */
    public function updateStatus(Request $request, $id)
    {
        $item = Item::findOrFail($id);
        $request->validate(['status' => 'required|in:ready,maintenance,broken']);
        $item->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Status kondisi barang berhasil diubah!');
    }

    /**
     * Pasang Jadwal Maintenance.
     */
    public function setMaintenance(Request $request, $id)
    {
        $item = Item::findOrFail($id);

        $request->validate([
            'maintenance_date' => 'required|date',
            'maintenance_note' => 'nullable|string',
        ]);

        $item->update([
            'maintenance_date' => $request->maintenance_date,
            'maintenance_note' => $request->maintenance_note,
        ]);

        return redirect()->back()->with('success', 'Jadwal maintenance berhasil dipasang!');
    }

    /**
     * Hapus barang dari sistem (Termasuk Fotonya).
     */
    public function destroy($id)
    {
        $item = Item::findOrFail($id);

        // ✅ HAPUS GAMBAR DARI STORAGE SAAT BARANG DIHAPUS
        if ($item->image && Storage::disk('public')->exists($item->image)) {
            Storage::disk('public')->delete($item->image);
        }

        $item->delete();

        return redirect()->back()->with('success', 'Data barang telah dihapus dari gudang.');
    }
}