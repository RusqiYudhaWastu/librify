<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ClassRoom;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        // Tarik data kelas untuk dropdown di form siswa
        $classes = ClassRoom::all();
        return view('auth.register', compact('classes'));
    }

    public function store(Request $request): RedirectResponse
    {
        // 1. Validasi Inputan
        $request->validate([
            'role' => ['required', 'string', 'in:student,class'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            
            // Wajib jika role = student
            'nisn' => ['nullable', 'required_if:role,student', 'string', 'max:20'],
            'class_id' => ['nullable', 'required_if:role,student', 'exists:class_rooms,id'],

            // Wajib jika role = class
            'chairman_name' => ['nullable', 'required_if:role,class', 'string', 'max:255'],
            'vice_chairman_name' => ['nullable', 'required_if:role,class', 'string', 'max:255'],
        ]);

        // 2. Logic Simpan Data
        if ($request->role === 'class') {
            
            // ✅ OTOMATIS BIKIN KELAS: Kalau kelas yg diketik belum ada, otomatis dibuatin
            $namaKelas = strtoupper(trim($request->name)); // Format jadi huruf besar (cth: XII RPL 1)
            $kelas = ClassRoom::firstOrCreate(
                ['name' => $namaKelas]
            );

            // ✅ Buat akun user dan hubungkan ke ID Kelas yang baru aja dicek/dibuat
            $user = User::create([
                'name' => $namaKelas, // Nama akun pakai nama kelas
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'class',
                'chairman_name' => $request->chairman_name,
                'vice_chairman_name' => $request->vice_chairman_name,
                'class_id' => $kelas->id, // Langsung tersambung!
                'status' => 'pending',
            ]);

        } else {
            // Jika yang daftar adalah SISWA INDIVIDU
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'student',
                'nisn' => $request->nisn,
                'class_id' => $request->class_id, // Dari dropdown yang udah dipilih
                'status' => 'pending',
            ]);
        }

        event(new Registered($user));

        // ✅ FIX: Ganti 'status' jadi 'success' biar notifikasi pop-up hijau di halaman login langsung muncul!
        return redirect()->route('login')->with('success', 'Akun berhasil dibuat! Silakan login untuk melanjutkan.');
    }
}