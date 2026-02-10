<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Department; 
use App\Models\ClassRoom; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        // Load relasi classRoom dan department biar datanya muncul di tabel
        $users = User::with(['department', 'assignedDepartments', 'classRoom.department'])->latest()->get();
        
        $departments = Department::all();
        $classes = ClassRoom::all(); 

        return view('admin.pengguna.index', compact('users', 'departments', 'classes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'role' => 'required|in:admin,toolman,class,student',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            
            // ✅ UPDATE: Validasi Class ID wajib untuk Siswa DAN Akun Kelas
            'class_id' => 'required_if:role,student|required_if:role,class|nullable|exists:classes,id',
            
            // NISN cuma buat siswa
            'nisn' => 'nullable|numeric|unique:users,nisn',
        ]);

        $photoPath = null;
        if ($request->hasFile('profile_photo')) {
            $photoPath = $request->file('profile_photo')->store('profile_photos', 'public');
        }

        // Logic penentuan Department ID otomatis berdasarkan Class ID
        $deptId = null;
        if ($request->class_id) {
            $kelas = ClassRoom::find($request->class_id);
            $deptId = $kelas ? $kelas->department_id : null;
        }

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'profile_photo' => $photoPath,
            
            // ✅ UPDATE: Simpan class_id untuk role 'student' DAN 'class'
            'class_id' => ($request->role === 'student' || $request->role === 'class') ? $request->class_id : null,
            
            // Simpan department_id (otomatis dari relasi kelas)
            'department_id' => $deptId, 

            'nisn' => $request->role === 'student' ? $request->nisn : null,
            'chairman_name' => $request->role === 'class' ? $request->chairman_name : null,
            'vice_chairman_name' => $request->role === 'class' ? $request->vice_chairman_name : null,
        ]);

        if ($request->role === 'toolman' && $request->has('assigned_dept_ids')) {
            $user->assignedDepartments()->sync($request->assigned_dept_ids);
        }

        return redirect()->route('admin.pengguna.index')->with('success', 'Pengguna baru berhasil didaftarkan!');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'     => 'required|string|max:255',
            'username' => ['required', 'string', Rule::unique('users')->ignore($user->id)],
            'email'    => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'role'     => 'required|in:admin,toolman,class,student',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            
            // ✅ UPDATE VALIDASI
            'class_id' => 'required_if:role,student|required_if:role,class|nullable|exists:classes,id',
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

        // ✅ LOGIC BARU: PENYIMPANAN DATA
        if ($request->role === 'student' || $request->role === 'class') {
            // Ambil Department ID dari Kelas yang dipilih
            $kelas = ClassRoom::find($request->class_id);
            
            $user->class_id = $request->class_id;
            $user->department_id = $kelas ? $kelas->department_id : null;
            
            if($request->role === 'student') {
                $user->nisn = $request->nisn;
                $user->chairman_name = null;
                $user->vice_chairman_name = null;
            } else {
                // Role Class
                $user->nisn = null;
                $user->chairman_name = $request->chairman_name;
                $user->vice_chairman_name = $request->vice_chairman_name;
            }
            
            $user->assignedDepartments()->detach();

        } elseif ($request->role === 'toolman') {
            $user->class_id = null;
            $user->department_id = null;
            $user->nisn = null;
            $user->chairman_name = null;
            $user->vice_chairman_name = null;
            $user->assignedDepartments()->sync($request->assigned_dept_ids ?? []);

        } else { // Admin
            $user->class_id = null;
            $user->department_id = null;
            $user->nisn = null;
            $user->chairman_name = null;
            $user->vice_chairman_name = null;
            $user->assignedDepartments()->detach();
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
}