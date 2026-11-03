<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bukus', function (Blueprint $table) {
            $table->id();
            // unique() menjawab masalah "data double" & "nomor buku tidak terdeteksi"
            $table->string('nomor_buku', 40)->unique();
            $table->string('judul', 200)->index();
            $table->string('pengarang', 120)->nullable();
            $table->string('penerbit', 120)->nullable();
            $table->unsignedSmallInteger('tahun_terbit')->nullable();
            $table->foreignId('kategori_id')->constrained('kategoris')->cascadeOnUpdate()->restrictOnDelete();
            $table->unsignedInteger('jumlah_total')->default(1);
            $table->unsignedInteger('jumlah_tersedia')->default(1);
            $table->string('rak', 30)->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('cover', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bukus');
    }
};
