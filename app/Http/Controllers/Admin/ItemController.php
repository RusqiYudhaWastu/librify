<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    /**
     * Tampilkan semua daftar katalog buku & kategori.
     */
    public function index()
    {
        // Eager Load 'categories' karena relasi sekarang multiple (Many-to-Many)
        $items = Item::with('categories')->latest()->get();
        $categories = Category::all();
        
        return view('admin.barang.index', compact('items', 'categories'));
    }

    /**
     * Simpan buku baru ke katalog (Termasuk Foto, Detail, & Multi Kategori).
     */
    public function store(Request $request)
    {
        // 1. Validasi Inputan
        $validated = $request->validate([
            'category_ids'   => 'required|array|min:1',
            'category_ids.*' => 'exists:categories,id',
            'name'         => 'required|string|max:255',
            'asset_code'   => 'required|unique:items,asset_code',
            'stock'        => 'required|numeric|min:0',
            'description'  => 'nullable|string',
            'image'        => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            
            // Validasi Detail Buku
            'author'       => 'nullable|string|max:255',
            'publisher'    => 'nullable|string|max:255',
            'publish_year' => 'nullable|integer|min:1900|max:' . date('Y'),
        ]);

        // 2. Siapkan Data Buku
        $data = [
            'name'              => $request->name,
            'asset_code'        => $request->asset_code,
            'stock'             => $request->stock,
            'maintenance_stock' => 0, 
            'broken_stock'      => 0, 
            'description'       => $request->description,
            'status'            => 'ready',
            'author'            => $request->author,
            'publisher'         => $request->publisher,
            'publish_year'      => $request->publish_year,
        ];

        // 3. LOGIC UPLOAD GAMBAR / COVER
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('items', 'public');
        }

        // 4. Simpan Buku ke Database
        $item = Item::create($data);

        // 5. SIMPAN RELASI MULTIPLE KATEGORI (Tabel Pivot)
        if ($request->has('category_ids')) {
            $item->categories()->attach($request->category_ids);
        }

        return redirect()->back()->with('success', 'Buku baru berhasil didaftarkan ke katalog!');
    }

    /**
     * Update data buku (Termasuk Ganti Cover, Detail & Multi Kategori).
     */
    public function update(Request $request, $id)
    {
        $item = Item::findOrFail($id);

        // 1. Validasi Inputan
        $request->validate([
            'category_ids'   => 'required|array|min:1',
            'category_ids.*' => 'exists:categories,id',
            'name'         => 'required|string|max:255',
            'asset_code'   => 'required|unique:items,asset_code,' . $id,
            'stock'        => 'required|numeric|min:0',
            'image'        => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            
            // Validasi Detail Buku
            'author'       => 'nullable|string|max:255',
            'publisher'    => 'nullable|string|max:255',
            'publish_year' => 'nullable|integer|min:1900|max:' . date('Y'),
        ]);

        // 2. Siapkan Data Update
        $data = [
            'name'         => $request->name,
            'asset_code'   => $request->asset_code,
            'stock'        => $request->stock, 
            'description'  => $request->description,
            'author'       => $request->author,
            'publisher'    => $request->publisher,
            'publish_year' => $request->publish_year,
        ];

        // 3. LOGIC GANTI GAMBAR / COVER
        if ($request->hasFile('image')) {
            // Hapus cover lama jika ada
            if ($item->image && Storage::disk('public')->exists($item->image)) {
                Storage::disk('public')->delete($item->image);
            }
            // Simpan cover baru
            $data['image'] = $request->file('image')->store('items', 'public');
        }

        // 4. Update Database Item
        $item->update($data);

        // 5. UPDATE RELASI MULTIPLE KATEGORI (Tabel Pivot)
        if ($request->has('category_ids')) {
            $item->categories()->sync($request->category_ids);
        }

        return redirect()->back()->with('success', 'Informasi buku berhasil diperbarui!');
    }

    /**
     * Update Distribusi Kondisi Buku (Tersedia, Perawatan, Rusak/Hilang) Manual oleh Admin.
     */
    public function updateStatus(Request $request, $id)
    {
        $item = Item::findOrFail($id);

        $request->validate([
            'action'   => 'required|in:to_maintenance,to_broken,resolve_maintenance,resolve_broken',
            'quantity' => 'required|integer|min:1'
        ]);

        $qty = (int) $request->quantity;
        $message = '';

        // Tentukan pergerakan stok berdasarkan "action" dari frontend
        switch ($request->action) {
            case 'to_maintenance':
                if ($item->stock < $qty) return back()->with('error', 'Stok tersedia tidak mencukupi untuk dipindah!');
                $item->stock -= $qty;
                $item->maintenance_stock += $qty;
                $message = "$qty Buku dipindahkan ke status Perawatan.";
                break;

            case 'to_broken':
                if ($item->stock < $qty) return back()->with('error', 'Stok tersedia tidak mencukupi untuk dipindah!');
                $item->stock -= $qty;
                $item->broken_stock += $qty;
                $message = "$qty Buku dipindahkan ke status Rusak/Hilang.";
                break;

            case 'resolve_maintenance':
                if ($item->maintenance_stock < $qty) return back()->with('error', 'Stok perawatan tidak valid!');
                $item->maintenance_stock -= $qty;
                $item->stock += $qty;
                $message = "$qty Buku selesai dirawat & siap dipinjam kembali.";
                break;

            case 'resolve_broken':
                if ($item->broken_stock < $qty) return back()->with('error', 'Stok rusak tidak valid!');
                $item->broken_stock -= $qty;
                $item->stock += $qty;
                $message = "$qty Buku rusak berhasil diganti/diperbaiki & siap dipinjam kembali.";
                break;
        }

        // AUTO-STATUS: Ubah status global (badge) sesuai jumlah stok terbanyak
        if ($item->stock > 0) {
            $item->status = 'ready'; 
        } elseif ($item->maintenance_stock > 0) {
            $item->status = 'maintenance'; 
        } elseif ($item->broken_stock > 0) {
            $item->status = 'broken';
        } else {
            $item->status = 'ready'; // Default
        }

        $item->save();

        return redirect()->back()->with('success', $message);
    }

    /**
     * Pasang Jadwal Perawatan Buku (Misal: Perbaikan Sampul).
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

        return redirect()->back()->with('success', 'Jadwal perawatan buku berhasil dipasang!');
    }

    /**
     * Hapus buku dari sistem (Termasuk Cover Foto dan Relasi Pivot).
     */
    public function destroy($id)
    {
        $item = Item::findOrFail($id);

        // HAPUS GAMBAR DARI STORAGE SAAT BUKU DIHAPUS
        if ($item->image && Storage::disk('public')->exists($item->image)) {
            Storage::disk('public')->delete($item->image);
        }

        // Hapus relasi di tabel pivot otomatis
        $item->categories()->detach();

        $item->delete();

        return redirect()->back()->with('success', 'Data buku telah dihapus permanen dari katalog.');
    }
}