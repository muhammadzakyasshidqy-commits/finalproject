<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * @return void
     */
    public function run(): void
    {
        //
        $now = now();

        DB::table('products')->insert([
            [
                'name' => 'Kemeja',
                'price' => 100,
                'stock' => 10,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Sweatpants',
                'price' => 50,
                'stock' => 5,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        ]);
    }
}
