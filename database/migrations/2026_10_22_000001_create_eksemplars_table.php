<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Kode per eksemplar buku. Satu judul punya banyak eksemplar, masing-masing dengan kode unik.
 * Data lama otomatis dibuatkan kode (NOMOR-BUKU-01, -02, ...) dan peminjaman aktif dikaitkan
 * ke kode terkecil, lalu jumlah stok disamakan. Aman dijalankan ulang.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('eksemplars')) {
            Schema::create('eksemplars', function (Blueprint $table) {
                $table->id();
                $table->foreignId('buku_id')->constrained('bukus')->cascadeOnDelete();
                $table->string('kode', 60)->unique();
                $table->string('prefix', 60)->default('');
                $table->unsignedBigInteger('urutan')->default(0);
                $table->enum('status', ['tersedia', 'dipinjam', 'nonaktif'])->default('tersedia');
                $table->timestamps();

                $table->index(['buku_id', 'status', 'prefix', 'urutan']);
            });
        }

        if (! Schema::hasColumn('peminjamans', 'eksemplar_id')) {
            Schema::table('peminjamans', function (Blueprint $table) {
                $table->unsignedBigInteger('eksemplar_id')->nullable()->after('buku_id');
                $table->foreign('eksemplar_id')->references('id')->on('eksemplars')->nullOnDelete();
            });
        }

        $this->isiDataLama();
    }

    public function down(): void
    {
        if (Schema::hasColumn('peminjamans', 'eksemplar_id')) {
            Schema::table('peminjamans', function (Blueprint $table) {
                $table->dropForeign(['eksemplar_id']);
                $table->dropColumn('eksemplar_id');
            });
        }

        Schema::dropIfExists('eksemplars');
    }

    private function isiDataLama(): void
    {
        $sekarang = now();

        foreach (DB::table('bukus')->orderBy('id')->get() as $buku) {
            if (DB::table('eksemplars')->where('buku_id', $buku->id)->exists()) {
                continue;
            }

            $aktif = DB::table('peminjamans')->where('buku_id', $buku->id)->where('status', 'dipinjam')->count();
            $jumlah = max((int) $buku->jumlah_total, $aktif);
            $lebar = max(2, strlen((string) $jumlah));
            $baris = [];

            for ($n = 1; $n <= $jumlah; $n++) {
                $baris[] = [
                    'buku_id' => $buku->id,
                    'kode' => $buku->nomor_buku.'-'.str_pad((string) $n, $lebar, '0', STR_PAD_LEFT),
                    'prefix' => $buku->nomor_buku.'-',
                    'urutan' => $n,
                    'status' => 'tersedia',
                    'created_at' => $sekarang,
                    'updated_at' => $sekarang,
                ];
            }

            foreach (array_chunk($baris, 200) as $potong) {
                DB::table('eksemplars')->insert($potong);
            }
        }

        // Peminjaman yang sedang berjalan dikaitkan ke kode terkecil yang masih tersedia.
        $berjalan = DB::table('peminjamans')
            ->where('status', 'dipinjam')
            ->whereNull('eksemplar_id')
            ->orderBy('id')
            ->get();

        foreach ($berjalan as $p) {
            $eks = DB::table('eksemplars')
                ->where('buku_id', $p->buku_id)
                ->where('status', 'tersedia')
                ->orderBy('prefix')->orderBy('urutan')->orderBy('kode')
                ->first();

            if ($eks) {
                DB::table('eksemplars')->where('id', $eks->id)->update(['status' => 'dipinjam']);
                DB::table('peminjamans')->where('id', $p->id)->update(['eksemplar_id' => $eks->id]);
            }
        }

        // Transaksi selesai lama tidak diketahui kodenya: dibiarkan kosong (tampil sebagai "—").
        foreach (DB::table('bukus')->get() as $buku) {
            $total = DB::table('eksemplars')->where('buku_id', $buku->id)->whereIn('status', ['tersedia', 'dipinjam'])->count();
            $tersedia = DB::table('eksemplars')->where('buku_id', $buku->id)->where('status', 'tersedia')->count();

            DB::table('bukus')->where('id', $buku->id)->update([
                'jumlah_total' => $total,
                'jumlah_tersedia' => $tersedia,
            ]);
        }
    }
};
