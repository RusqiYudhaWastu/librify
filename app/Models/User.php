<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Atribut yang dapat diisi secara massal (Mass Assignable).
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',               // admin, staff, teacher, class, student
        'username',  
        'nisn',               // Kolom NISN untuk validasi Siswa
        'chairman_name',      // Nama Ketua Kelas (Khusus role 'class')
        'vice_chairman_name', // Nama Wakil Ketua (Khusus role 'class')
        'class_id',           // ID Kelas (Untuk role 'class' & 'student')
        'profile_photo',      // Kolom Foto Profil
        'status',             // ✅ ADDED: 'pending', 'approved', 'rejected' untuk Sistem Approval
    ];

    /**
     * Atribut yang disembunyikan saat serialisasi.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Mendaftarkan Accessor ke dalam JSON
     */
    protected $appends = ['profile_photo_url'];

    /**
     * Cast atribut ke tipe data tertentu.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * ==========================================
     * HELPER FUNCTIONS UNTUK CEK ROLE LIRBIFY
     * Biar di Controller/Blade kodingnya enak
     * ==========================================
     */

    // Cek apakah Admin Server/Kepala Perpus
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    // ✅ NEW: Cek apakah Petugas Perpustakaan (Pengganti Toolman)
    public function isStaff()
    {
        return $this->role === 'staff';
    }

    // ✅ NEW: Cek apakah Guru / Wali Kelas
    public function isTeacher()
    {
        return $this->role === 'teacher';
    }

    // Cek apakah Akun Perwakilan Kelas Kolektif
    public function isClassRep()
    {
        return $this->role === 'class';
    }

    // Cek apakah Siswa Perindividu
    public function isStudent()
    {
        return $this->role === 'student';
    }

    /**
     * Accessor: profile_photo_url
     * Logic: Cek file di storage, kalau nggak ada pakai UI Avatars.
     */
    public function getProfilePhotoUrlAttribute()
    {
        if ($this->profile_photo && Storage::disk('public')->exists($this->profile_photo)) {
            return asset('storage/' . $this->profile_photo);
        }

        // Fallback: Pake UI Avatars kalau user belum upload foto
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=06b6d4&color=fff&bold=true';
    }

    /**
     * ==========================================
     * RELASI DATABASE
     * ==========================================
     */

    /**
     * Relasi ke ClassRoom (Data Kelas)
     * Digunakan oleh Role 'class', 'student', dan 'teacher' (jika wali kelas)
     */
    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }
}