<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['code' => 'R01', 'name' => 'Red Widget', 'price' => 3295],
            ['code' => 'G01', 'name' => 'Green Widget', 'price' => 2495],
            ['code' => 'B01', 'name' => 'Blue Widget', 'price' => 795],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(['code' => $product['code']], $product);
        }
    }
}
