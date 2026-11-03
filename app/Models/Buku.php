<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\ValidationException;
use App\Support\RentangKode;
use InvalidArgumentException;

class Buku extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_buku', 'judul', 'pengarang', 'penerbit', 'tahun_terbit',
        'kategori_id', 'jumlah_total', 'jumlah_tersedia', 'rak', 'deskripsi', 'cover',
    ];

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class);
    }

    public function peminjamans(): HasMany
    {
        return $this->hasMany(Peminjaman::class);
    }

    public function eksemplars(): HasMany
    {
        return $this->hasMany(Eksemplar::class)->urut();
    }

    /** Eksemplar yang akan dipinjamkan berikutnya: kode terkecil yang berstatus tersedia. */
    public function eksemplarBerikutnya(): ?Eksemplar
    {
        return $this->eksemplars()->where('status', Eksemplar::STATUS_TERSEDIA)->first();
    }

    /**
     * Rentang kode eksemplar per awalan, mis. [['dari' => 'BK-101-001', 'sampai' => 'BK-101-010', 'jumlah' => 10]].
     * Memakai relasi eksemplars (ikut dimuat bila sudah di-eager-load).
     *
     * @return array<int, array{dari: string, sampai: string, jumlah: int}>
     */
    public function rentangKode(): array
    {
        return $this->eksemplars
            ->groupBy('prefix')
            ->map(fn ($grup) => [
                'dari' => $grup->first()->kode,
                'sampai' => $grup->last()->kode,
                'jumlah' => $grup->count(),
            ])
            ->values()
            ->all();
    }

    /** Teks ringkas rentang kode: "BK-101-001 – BK-101-010". */
    public function teksRentang(): string
    {
        $teks = array_map(
            fn ($r) => $r['dari'] === $r['sampai'] ? $r['dari'] : $r['dari'].' – '.$r['sampai'],
            $this->rentangKode()
        );

        return $teks ? implode(', ', $teks) : '—';
    }

    /** Menyamakan jumlah_total / jumlah_tersedia dengan data eksemplar (sumber kebenaran). */
    public function sinkronStok(): void
    {
        $dasar = Eksemplar::where('buku_id', $this->id);

        $this->forceFill([
            'jumlah_total' => (clone $dasar)->whereIn('status', [Eksemplar::STATUS_TERSEDIA, Eksemplar::STATUS_DIPINJAM])->count(),
            'jumlah_tersedia' => (clone $dasar)->where('status', Eksemplar::STATUS_TERSEDIA)->count(),
        ])->save();
    }

    /**
     * Menambah eksemplar dari rentang kode. Kode yang sudah dipakai (judul mana pun) ditolak,
     * sehingga tidak mungkin ada kode kembar.
     *
     * @throws ValidationException
     */
    public function tambahRentang(string $dari, string $sampai, string $kolom = 'kode_awal'): int
    {
        try {
            $daftar = RentangKode::buat($dari, $sampai);
        } catch (InvalidArgumentException $e) {
            throw ValidationException::withMessages([$kolom => $e->getMessage()]);
        }

        $kodeBaru = array_column($daftar, 'kode');
        $bentrok = Eksemplar::with('buku:id,judul')
            ->whereIn('kode', $kodeBaru)
            ->orderBy('kode')
            ->get();

        if ($bentrok->isNotEmpty()) {
            $contoh = $bentrok->take(3)
                ->map(fn ($e) => $e->kode.' (dipakai: '.($e->buku?->judul ?? 'judul lain').')')
                ->implode('; ');
            $sisa = $bentrok->count() > 3 ? ' dan '.($bentrok->count() - 3).' kode lain' : '';

            throw ValidationException::withMessages([
                $kolom => $bentrok->count().' kode sudah terdaftar: '.$contoh.$sisa.'. Ubah rentang agar tidak bentrok.',
            ]);
        }

        $sekarang = now();
        $baris = array_map(fn ($d) => [
            'buku_id' => $this->id,
            'kode' => $d['kode'],
            'prefix' => $d['prefix'],
            'urutan' => $d['urutan'],
            'status' => Eksemplar::STATUS_TERSEDIA,
            'created_at' => $sekarang,
            'updated_at' => $sekarang,
        ], $daftar);

        foreach (array_chunk($baris, 200) as $potong) {
            Eksemplar::insert($potong);
        }

        return count($baris);
    }

    public function tersedia(): bool
    {
        return $this->jumlah_tersedia > 0;
    }

    public function statusStok(): string
    {
        return $this->tersedia() ? 'Tersedia' : 'Dipinjam habis';
    }

    /** Pencarian katalog: judul, pengarang, penerbit, atau nomor buku. */
    public function scopeCari(Builder $query, ?string $kata): Builder
    {
        if (blank($kata)) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($kata) {
            $q->where('judul', 'like', "%{$kata}%")
                ->orWhere('pengarang', 'like', "%{$kata}%")
                ->orWhere('penerbit', 'like', "%{$kata}%")
                ->orWhere('nomor_buku', 'like', "%{$kata}%");
        });
    }

    public function scopeKategori(Builder $query, ?string $slug): Builder
    {
        if (blank($slug) || $slug === 'semua') {
            return $query;
        }

        return $query->whereHas('kategori', fn (Builder $q) => $q->where('slug', $slug));
    }
}
