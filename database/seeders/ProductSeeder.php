<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Hapus data lama (optional)
        DB::table('products')->delete();

        $products = [
            [
                'name' => 'Laptop Gaming ASUS ROG',
                'description' => 'Laptop gaming high performance dengan RTX 3060',
                'price' => 15000000,
                'stock' => 10,
                'image_url' => 'https://images.unsplash.com/photo-1603302576837-37561b2e2302',
                'is_active' => true,
            ],
            [
                'name' => 'Mouse Logitech G502',
                'description' => 'Gaming mouse dengan sensor HERO 25K',
                'price' => 750000,
                'stock' => 50,
                'image_url' => 'https://images.unsplash.com/photo-1527864550417-7fd91fc51a46',
                'is_active' => true,
            ],
            [
                'name' => 'Keyboard Mechanical RGB',
                'description' => 'Mechanical keyboard dengan switch Cherry MX',
                'price' => 1200000,
                'stock' => 30,
                'image_url' => 'https://images.unsplash.com/photo-1587829741301-dc798b83add3',
                'is_active' => true,
            ],
            [
                'name' => 'Monitor Gaming 27 inch',
                'description' => '144Hz refresh rate, 1ms response time',
                'price' => 3500000,
                'stock' => 15,
                'image_url' => 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf',
                'is_active' => true,
            ],
            [
                'name' => 'Headset Gaming HyperX',
                'description' => '7.1 Surround Sound',
                'price' => 850000,
                'stock' => 25,
                'image_url' => 'https://images.unsplash.com/photo-1618366712010-f4ae9c647dcb',
                'is_active' => true,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }

        $this->command->info('✅ Products seeded successfully!');
    }
}
