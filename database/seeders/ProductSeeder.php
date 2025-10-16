<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $products = [
            [
                'name' => 'Laptop Gaming ASUS ROG',
                'description' => 'Laptop gaming high performance dengan RTX 3060',
                'price' => 1,
                'stock' => 10,
                'image_url' => 'https://example.com/laptop.jpg',
                'is_active' => true,
            ],
            [
                'name' => 'Mouse Logitech G502',
                'description' => 'Gaming mouse dengan sensor HERO 25K',
                'price' => 2,
                'stock' => 50,
                'image_url' => 'https://example.com/mouse.jpg',
                'is_active' => true,
            ],
            [
                'name' => 'Keyboard Mechanical RGB',
                'description' => 'Mechanical keyboard dengan switch Cherry MX',
                'price' => 1,
                'stock' => 30,
                'image_url' => 'https://example.com/keyboard.jpg',
                'is_active' => true,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}