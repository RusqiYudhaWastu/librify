<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage; // ✅ Wajib untuk hapus/simpan file
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Menampilkan formulir profil pengguna berdasarkan Role.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();

        // 🎯 Logic Pengalihan View Berdasarkan Role
        if ($user->role === 'admin') {
            return view('admin.profile.edit', [
                'user' => $user,
            ]);
        } elseif ($user->role === 'toolman') {
            return view('toolman.profile.edit', [
                'user' => $user,
            ]);
        } elseif ($user->role === 'student') {
            // ✅ TAMBAHAN: Arahkan ke view khusus Student (Cyan Theme)
            // Biar sidebar-nya bener (sesuai file yang kita buat sebelumnya)
            return view('student.profile.edit', [
                'user' => $user,
            ]);
        } else {
            // Default untuk role 'class' (Perwakilan Kelas - Blue Theme)
            // Asumsi file view lamanya ada di folder siswa
            return view('siswa.profile.edit', [
                'user' => $user,
            ]);
        }
    }

    /**
     * Memperbarui informasi profil pengguna (Termasuk Foto).
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        
        // 1. Isi data dasar (Name & Email)
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // 2. Logic Handle Upload Foto Profil ✅
        if ($request->hasFile('profile_photo')) {
            // Validasi manual untuk foto (Ukuran max 2MB)
            $request->validate([
                'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            ]);

            // Hapus foto lama jika ada di storage
            if ($user->profile_photo) {
                Storage::disk('public')->delete($user->profile_photo);
            }

            // Simpan foto baru ke folder 'profile_photos' di disk public
            $path = $request->file('profile_photo')->store('profile_photos', 'public');
            
            // Simpan path-nya ke database
            $user->profile_photo = $path;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Menghapus akun pengguna.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // Hapus foto profil dari storage sebelum user dihapus permanen ✅
        if ($user->profile_photo) {
            Storage::disk('public')->delete($user->profile_photo);
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}