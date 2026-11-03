<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Aman dijalankan ulang: dilewati bila kolom sudah ada (mis. dari impor spp_ku.sql).
        if (! Schema::hasColumn('users', 'telepon')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('telepon', 20)->nullable()->after('email');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'telepon')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('telepon');
            });
        }
    }
};
