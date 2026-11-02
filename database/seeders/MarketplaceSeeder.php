<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class MarketplaceSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [];
        foreach (['TeknoID', 'NusaWear', 'HomeCraft', 'ActivePro', 'UrbanCo'] as $name) {
            $brands[] = Brand::firstOrCreate(['name' => $name]);
        }

        $groups = [
            'Elektronik' => [
                ['Mouse Wireless', 125000, 30],
                ['Keyboard Mechanical', 450000, 15],
                ['Headset USB', 275000, 20],
                ['Webcam Full HD', 320000, 12],
                ['Hub USB-C', 95000, 35],
            ],
            'Pakaian' => [
                ['Kemeja Oxford', 230000, 18],
                ['Jaket Track', 325000, 15],
                ['Celana Chino', 275000, 24],
                ['Kaos Katun', 89000, 50],
                ['Sweater Rajut', 210000, 14],
            ],
            'Rumah Tangga' => [
                ['Lampu Meja', 175000, 25],
                ['Botol Minum', 65000, 50],
                ['Rak Serbaguna', 340000, 11],
                ['Jam Dinding', 110000, 29],
                ['Kotak Penyimpanan', 85000, 40],
            ],
            'Olahraga' => [
                ['Bola Basket', 285000, 15],
                ['Matras Yoga', 135000, 26],
                ['Tali Skipping', 45000, 38],
                ['Resistance Band', 98000, 32],
                ['Hand Grip', 55000, 22],
            ],
            'Aksesori' => [
                ['Dompet Kartu', 90000, 36],
                ['Tas Selempang', 185000, 25],
                ['Topi Kanvas', 70000, 30],
                ['Gantungan Kunci', 25000, 70],
                ['Pouch Kabel', 60000, 48],
            ],
        ];

        foreach ($groups as $categoryName => $items) {
            $category = Category::firstOrCreate(['name' => $categoryName]);
            foreach ($items as $i => [$name, $price, $stock]) {
                Product::firstOrCreate(
                    ['name' => $name],
                    [
                        'description' => 'Contoh produk ' . $categoryName . ' untuk latihan API.',
                        'price' => $price,
                        'stock' => $stock,
                        'category_id' => $category->id,
                        'brand_id' => $brands[$i]->id,
                    ]
                );
            }
        }
    }
}
