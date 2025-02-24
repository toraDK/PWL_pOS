<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'supplier_id' => '001',
                'supplier_kode' => '1',
                'supplier_nama' => 'Adit',
                'supplier_alamat' => 'Jl. Kebon Jeruk',
            ],
            [
                'supplier_id' => '002',
                'supplier_kode' => '2',
                'supplier_nama' => 'Budi',
                'supplier_alamat' => 'Jl. Merdeka',
            ],
            [
                'supplier_id' => '003',
                'supplier_kode' => '3',
                'supplier_nama' => 'Citra',
                'supplier_alamat' => 'Jl. Sudirman',
            ],
        ];
        

        DB::table('m_supplier')->insert($data);
    }
}
