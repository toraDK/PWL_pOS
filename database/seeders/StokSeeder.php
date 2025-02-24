<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StokSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [];
        for ($i = 1; $i <= 15; $i++) {
            $supplier_id = ceil($i / 5); 
            $data[] = [
                'stok_id' => $i,
                'barang_id' => $i,
                'user_id' => 1,
                'supplier_id' => $supplier_id,
                'stok_tanggal' => now(),
                'stok_jumlah' => rand(10, 100),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('t_stok')->insert($data);
    }
}
