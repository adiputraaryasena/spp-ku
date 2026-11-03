<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Peminjaman extends Model
{
    use HasFactory;

    // Tambahkan baris ini agar Laravel membaca tabel 'peminjaman' (bukan 'peminjamen')
    protected $table = 'peminjamans';

    public const STATUS_PENDING = 'pending';
    public const STATUS_DIPINJAM = 'dipinjam';
    public const STATUS_DITOLAK = 'ditolak';
    public const STATUS_SELESAI = 'selesai';

    protected $fillable = [
        'kode_trx', 'user_id', 'buku_id', 'eksemplar_id', 'tanggal_pengajuan', 'tanggal_jatuh_tempo',
        'tanggal_kembali', 'status', 'catatan_siswa', 'catatan_admin', 'denda',
        'jumlah_perpanjangan', 'diverifikasi_oleh', 'diverifikasi_pada',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_pengajuan' => 'date',
            'tanggal_jatuh_tempo' => 'date',
            'tanggal_kembali' => 'date',
            'diverifikasi_pada' => 'datetime',
            'denda' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function buku(): BelongsTo
    {
        return $this->belongsTo(Buku::class);
    }

    /** Fisik buku yang dipinjam (punya kode unik). Terisi saat pengajuan disetujui. */
    public function eksemplar(): BelongsTo
    {
        return $this->belongsTo(Eksemplar::class);
    }

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh');
    }

    /** Membuat kode transaksi berurutan: TRX-000000123 */
    public static function buatKodeTrx(): string
    {
        $terakhir = static::max('id') ?? 0;

        return 'TRX-'.str_pad((string) ($terakhir + 1), 9, '0', STR_PAD_LEFT);
    }

    /** Selisih hari keterlambatan terhadap tanggal acuan (0 bila belum lewat). */
    public function hariTerlambat(?Carbon $acuan = null): int
    {
        if ($this->status === self::STATUS_DITOLAK) {
            return 0;
        }

        $acuan = $acuan
            ?? ($this->tanggal_kembali ? $this->tanggal_kembali->copy() : Carbon::today());

        if ($acuan->lessThanOrEqualTo($this->tanggal_jatuh_tempo)) {
            return 0;
        }

        return (int) round($this->tanggal_jatuh_tempo->diffInDays($acuan));
    }

    /** FR-05: denda otomatis = hari terlambat x tarif harian. */
    public function hitungDenda(?Carbon $acuan = null): int
    {
        return $this->hariTerlambat($acuan) * (int) config('sppku.denda_per_hari');
    }

    /** Sisa hari sampai jatuh tempo (0 = hari ini, negatif = terlambat). */
    public function sisaHari(): int
    {
        return (int) round(Carbon::today()->diffInDays($this->tanggal_jatuh_tempo->copy()->startOfDay(), false));
    }

    /** Sedang dipinjam dan sudah masuk masa pengingat (atau sudah terlambat). */
    public function perluDiingatkan(): bool
    {
        return $this->status === self::STATUS_DIPINJAM
            && $this->sisaHari() <= (int) config('sppku.pengingat_hari');
    }

    public function terlambat(): bool
    {
        return $this->status === self::STATUS_DIPINJAM && $this->hariTerlambat() > 0;
    }

    public function bolehDiperpanjang(): bool
    {
        return $this->status === self::STATUS_DIPINJAM
            && $this->jumlah_perpanjangan < (int) config('sppku.maks_perpanjangan')
            && $this->hariTerlambat() === 0;
    }

    /** Batas siswa datang mengambil buku: waktu pengajuan + batas_ambil_jam (24 jam). */
    public function batasAmbil(): ?Carbon
    {
        return $this->created_at
            ? $this->created_at->copy()->addHours((int) config('sppku.batas_ambil_jam'))
            : null;
    }

    /** Pengajuan masih pending tetapi batas ambilnya sudah lewat. */
    public function kedaluwarsaAmbil(): bool
    {
        $batas = $this->batasAmbil();

        return $this->status === self::STATUS_PENDING && $batas !== null && $batas->isPast();
    }

    /**
     * Menolak otomatis semua pengajuan pending yang tidak diambil dalam batas waktu.
     * Dipanggil ringan dari middleware, halaman terkait, dan perintah terjadwal.
     */
    public static function tolakKedaluwarsa(): int
    {
        $jam = (int) config('sppku.batas_ambil_jam');

        return static::where('status', self::STATUS_PENDING)
            ->whereNotNull('created_at')
            ->where('created_at', '<=', now()->subHours($jam))
            ->update([
                'status' => self::STATUS_DITOLAK,
                'catatan_admin' => 'Ditolak otomatis: buku tidak diambil di perpustakaan dalam '.$jam.' jam setelah pengajuan.',
                'diverifikasi_pada' => now(),
            ]);
    }

    public function labelStatus(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'Menunggu verifikasi',
            self::STATUS_DIPINJAM => 'Sedang dipinjam',
            self::STATUS_DITOLAK => 'Ditolak',
            self::STATUS_SELESAI => 'Selesai',
            default => $this->status,
        };
    }

    public function scopeStatus(Builder $query, ?string $status): Builder
    {
        if (blank($status) || $status === 'semua') {
            return $query;
        }

        return $query->where('status', $status);
    }
}