<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Satu fisik buku. Setiap eksemplar punya kode unik di seluruh perpustakaan,
 * sehingga satu judul dengan banyak salinan tidak pernah memiliki kode kembar.
 */
class Eksemplar extends Model
{
    protected $table = 'eksemplars';

    public const STATUS_TERSEDIA = 'tersedia';
    public const STATUS_DIPINJAM = 'dipinjam';
    public const STATUS_NONAKTIF = 'nonaktif';

    protected $fillable = ['buku_id', 'kode', 'prefix', 'urutan', 'status'];

    public function buku(): BelongsTo
    {
        return $this->belongsTo(Buku::class);
    }

    public function peminjamans(): HasMany
    {
        return $this->hasMany(Peminjaman::class);
    }

    public function peminjamanAktif(): HasOne
    {
        return $this->hasOne(Peminjaman::class)->where('status', Peminjaman::STATUS_DIPINJAM);
    }

    /** Urutan "kode terkecil dulu": awalan, lalu angka, lalu kode. */
    public function scopeUrut(Builder $query): Builder
    {
        return $query->orderBy('prefix')->orderBy('urutan')->orderBy('kode');
    }

    public function labelStatus(): string
    {
        return match ($this->status) {
            self::STATUS_TERSEDIA => 'Tersedia',
            self::STATUS_DIPINJAM => 'Dipinjam',
            default => 'Nonaktif',
        };
    }

    public function kelasBadge(): string
    {
        return match ($this->status) {
            self::STATUS_TERSEDIA => 'badge-tersedia',
            self::STATUS_DIPINJAM => 'badge-dipinjam',
            default => 'badge-ditolak',
        };
    }
}
