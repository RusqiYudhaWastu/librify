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
        'role',               // admin, toolman, class, student
        'username',  
        'nisn',               // ✅ ADDED: Kolom NISN agar bisa diinput
        'department_id',      // Jurusan
        'chairman_name',      // Nama Ketua Kelas (Khusus role 'class')
        'vice_chairman_name', // Nama Wakil Ketua (Khusus role 'class')
        'class_id',           // ID Kelas (Untuk role 'class' & 'student')
        'profile_photo',      // Kolom Foto Profil
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
     * HELPER FUNCTIONS UNTUK CEK ROLE
     * Biar di Controller/Blade kodingnya enak
     * ==========================================
     */

    // Cek apakah Admin
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    // Cek apakah Toolman
    public function isToolman()
    {
        return $this->role === 'toolman';
    }

    // Cek apakah Perwakilan Kelas (Role Lama)
    public function isClassRep()
    {
        return $this->role === 'class';
    }

    // ✅ NEW: Cek apakah Siswa Perindividu (Role Baru)
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
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=6366f1&color=fff&bold=true';
    }

    /**
     * Relasi ke Department (Jalur Siswa & Ketua Kelas)
     */
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    /**
     * Relasi ke Departments (Jalur Toolman - Many to Many)
     */
    public function assignedDepartments()
    {
        return $this->belongsToMany(Department::class, 'department_user');
    }

    /**
     * Relasi ke ClassRoom
     * Digunakan oleh Role 'class' dan 'student'
     */
    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }
}