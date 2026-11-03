<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 30)->unique();          // NIS siswa / kode petugas
            $table->string('nama', 120);
            $table->string('email', 120)->nullable()->unique();
            $table->string('kelas', 30)->nullable();
            $table->enum('role', ['admin', 'siswa'])->default('siswa');
            $table->string('password');
            $table->boolean('aktif')->default(true);
            // true = akun masih memakai kata sandi bawaan dan wajib menggantinya saat login
            $table->boolean('harus_ganti_password')->default(false);
            $table->timestamp('password_diganti_pada')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
