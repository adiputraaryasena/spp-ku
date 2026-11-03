<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EksemplarSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('eksemplars')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $bukus = DB::table('bukus')->pluck('id');
        $data = [];
        
        // Counter global untuk memastikan nomor urut terus berlanjut (05050001, 05050002, dst)
        $globalCounter = 1;

        foreach ($bukus as $bukuId) {
            for ($i = 1; $i <= 300; $i++) {
                // Format kode berlanjut terus dari 1 sampai 9600
                $kodeStr = '0505' . sprintf('%04d', $globalCounter);
                
                $data[] = [
                    'buku_id'    => $bukuId,
                    'kode'       => $kodeStr,
                    'prefix'     => '0505',
                    'urutan'     => $i,
                    'status'     => 'tersedia',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $globalCounter++;

                // Insert per 500 baris agar RAM tidak berat
                if (count($data) == 500) {
                    DB::table('eksemplars')->insert($data);
                    $data = [];
                }
            }
        }

        // Sisa data insert terakhir
        if (!empty($data)) {
            DB::table('eksemplars')->insert($data);
        }
    }
}