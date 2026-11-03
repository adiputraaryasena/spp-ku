<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'kode', 'nama', 'email', 'telepon', 'kelas', 'role', 'password', 'aktif',
        'harus_ganti_password', 'password_diganti_pada',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'aktif' => 'boolean',
            'harus_ganti_password' => 'boolean',
            'password_diganti_pada' => 'datetime',
        ];
    }

    public function peminjamans(): HasMany
    {
        return $this->hasMany(Peminjaman::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isSiswa(): bool
    {
        return $this->role === 'siswa';
    }

    /** Akun masih memakai kata sandi bawaan dari petugas. */
    public function pakaiKataSandiBawaan(): bool
    {
        return (bool) $this->harus_ganti_password;
    }

    /** Inisial untuk avatar di pojok kanan atas navbar. */
    public function getInisialAttribute(): string
    {
        $pecah = preg_split('/\s+/', trim($this->nama));
        $awal = mb_substr($pecah[0] ?? 'U', 0, 1);
        $akhir = count($pecah) > 1 ? mb_substr(end($pecah), 0, 1) : '';

        return mb_strtoupper($awal.$akhir);
    }
}
