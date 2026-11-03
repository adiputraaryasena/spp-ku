<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peminjamans', function (Blueprint $table) {
            $table->id();
            $table->string('kode_trx', 30)->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('buku_id')->constrained('bukus')->cascadeOnUpdate()->restrictOnDelete();
            $table->date('tanggal_pengajuan');
            $table->date('tanggal_jatuh_tempo');
            $table->date('tanggal_kembali')->nullable();
            $table->enum('status', ['pending', 'dipinjam', 'ditolak', 'selesai'])->default('pending')->index();
            $table->text('catatan_siswa')->nullable();
            $table->text('catatan_admin')->nullable();
            $table->unsignedInteger('denda')->default(0);
            $table->unsignedTinyInteger('jumlah_perpanjangan')->default(0);
            $table->foreignId('diverifikasi_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('diverifikasi_pada')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peminjamans');
    }
};
