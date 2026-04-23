<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ClassRoom; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
// ✅ IMPORT LIBRARY EXCEL
use App\Imports\StudentsImport;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    public function index()
    {
        // Hanya load relasi classRoom karena relasi jurusan udah dimusnahkan
        $users = User::with(['classRoom'])->latest()->get();
        
        // Ambil semua data kelas untuk dropdown form
        $classes = ClassRoom::all();

        return view('admin.pengguna.index', compact('users', 'classes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'role' => 'required|in:admin,staff,teacher,class,student',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            
            // Validasi Class ID wajib untuk Siswa DAN Akun Kelas (Tabelnya class_rooms)
            'class_id' => 'required_if:role,student|required_if:role,class|nullable|exists:class_rooms,id',
            
            // NISN cuma buat siswa
            'nisn' => 'nullable|numeric|unique:users,nisn',
        ]);

        $photoPath = null;
        if ($request->hasFile('profile_photo')) {
            $photoPath = $request->file('profile_photo')->store('profile_photos', 'public');
        }

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'profile_photo' => $photoPath,
            'status' => 'approved', // Admin yang buat, otomatis approved
            
            // Simpan class_id untuk role yang butuh kelas
            'class_id' => in_array($request->role, ['student', 'class', 'teacher']) ? $request->class_id : null,
            
            'nisn' => $request->role === 'student' ? $request->nisn : null,
            'chairman_name' => $request->role === 'class' ? $request->chairman_name : null,
            'vice_chairman_name' => $request->role === 'class' ? $request->vice_chairman_name : null,
        ]);

        return redirect()->route('admin.pengguna.index')->with('success', 'Pengguna baru berhasil didaftarkan!');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'     => 'required|string|max:255',
            'username' => ['required', 'string', Rule::unique('users')->ignore($user->id)],
            'email'    => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'role'     => 'required|in:admin,staff,teacher,class,student',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            
            // Validasi dengan tabel class_rooms
            'class_id' => 'required_if:role,student|required_if:role,class|nullable|exists:class_rooms,id',
            'nisn' => ['nullable', 'numeric', Rule::unique('users')->ignore($user->id)],
        ]);

        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo) {
                Storage::disk('public')->delete($user->profile_photo);
            }
            $user->profile_photo = $request->file('profile_photo')->store('profile_photos', 'public');
        }

        $user->name = $request->name;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->role = $request->role;

        // LOGIC PENYIMPANAN DATA BERDASARKAN ROLE LIRBIFY
        if (in_array($request->role, ['student', 'class', 'teacher'])) {
            $user->class_id = $request->class_id;
            
            if ($request->role === 'student') {
                $user->nisn = $request->nisn;
                $user->chairman_name = null;
                $user->vice_chairman_name = null;
            } elseif ($request->role === 'class') {
                $user->nisn = null;
                $user->chairman_name = $request->chairman_name;
                $user->vice_chairman_name = $request->vice_chairman_name;
            } else {
                // Teacher
                $user->nisn = null;
                $user->chairman_name = null;
                $user->vice_chairman_name = null;
            }
        } else { 
            // Admin atau Staff
            $user->class_id = null;
            $user->nisn = null;
            $user->chairman_name = null;
            $user->vice_chairman_name = null;
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('admin.pengguna.index')->with('success', 'Data Akun '.$user->name.' Berhasil Diperbarui!');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        if ($id == Auth::id()) return redirect()->back()->with('error', 'Gak bisa hapus diri sendiri!');

        if ($user->profile_photo) Storage::disk('public')->delete($user->profile_photo);
        $user->delete();
        
        return redirect()->route('admin.pengguna.index')->with('success', 'Akun telah dihapus!');
    }

    // FUNGSI UNTUK IMPORT EXCEL SISWA
    public function import(Request $request)
    {
        // Validasi file yang diupload dan tabel class_rooms
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls,csv|max:5120', 
            'class_id'   => 'required|exists:class_rooms,id'
        ]);

        try {
            // Jalankan proses import pakai Class StudentsImport
            Excel::import(new StudentsImport($request->class_id), $request->file('excel_file'));
            
            return redirect()->back()->with('success', 'Data ratusan siswa berhasil di-import ke kelas!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengimpor data! Pastikan format Excel benar dan email/username tidak duplikat. Error: ' . $e->getMessage());
        }
    }
}