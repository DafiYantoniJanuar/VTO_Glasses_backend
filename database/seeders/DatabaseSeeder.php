<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin User: Mizuki
        User::updateOrCreate(
            ['email' => 'admin@vtogla.com'],
            [
                'name' => 'Mizuki',
                'password' => Hash::make('12345678'),
                'role' => 'admin',
            ]
        );

        // Alias version for admin.vtogla.com string support
        User::updateOrCreate(
            ['email' => 'admin.vtogla.com@vtoglasses.com'],
            [
                'name' => 'Mizuki',
                'password' => Hash::make('12345678'),
                'role' => 'admin',
            ]
        );

        // Initial Glasses Catalog Seed Data
        $dummyProducts = [
            [
                'name' => 'The Cambridge',
                'shape' => 'Round',
                'color' => 'Tortoise',
                'price' => 2175000,
                'category' => 'Sunglasses',
                'description' => 'A timeless round silhouette crafted from premium Italian acetate. The Cambridge offers UV400 protection and ultra-lightweight comfort.',
                'best_seller' => true,
                'rating' => 4.7,
                'reviews' => 89,
            ],
            [
                'name' => 'The Architect',
                'shape' => 'Square',
                'color' => 'Matte Black',
                'price' => 2460000,
                'category' => 'Sunglasses',
                'description' => 'Timeless design meets modern engineering. The Architect features aerospace-grade titanium frames and polarized lenses.',
                'best_seller' => false,
                'rating' => 4.6,
                'reviews' => 120,
            ],
            [
                'name' => 'The Maverick',
                'shape' => 'Aviator',
                'color' => 'Gold',
                'price' => 2760000,
                'category' => 'Sunglasses',
                'description' => 'Bold, iconic, unmistakable. The Maverick aviator features a classic teardrop silhouette with a lustrous gold frame.',
                'best_seller' => true,
                'rating' => 4.9,
                'reviews' => 210,
            ],
            [
                'name' => 'The Ghost',
                'shape' => 'Cat Eye',
                'color' => 'Clear Crystal',
                'price' => 2235000,
                'category' => 'Blue Light',
                'description' => 'Barely-there sophistication. The Ghost features ultra-clear acetate for an almost invisible look with blue light blocking.',
                'best_seller' => false,
                'rating' => 4.5,
                'reviews' => 65,
            ],
            [
                'name' => 'Classic Scholar',
                'shape' => 'Round',
                'color' => 'Dark Gray',
                'price' => 1890000,
                'category' => 'Reading Glasses',
                'description' => 'Refined and scholarly, the Classic Scholar combines a vintage-inspired round silhouette with modern lightweight materials.',
                'best_seller' => false,
                'rating' => 4.4,
                'reviews' => 44,
            ],
            [
                'name' => 'Aero Slim',
                'shape' => 'Aviator',
                'color' => 'Midnight Black',
                'price' => 2450000,
                'category' => 'Minus',
                'description' => 'The ultimate in minimal elegance. Aero Slim\'s ultra-thin titanium frame practically disappears on your face.',
                'best_seller' => true,
                'rating' => 4.8,
                'reviews' => 178,
            ],
        ];

        foreach ($dummyProducts as $prod) {
            Product::updateOrCreate(
                ['name' => $prod['name']],
                $prod
            );
        }

        // Seed Customer Users
        $customer1 = User::updateOrCreate(
            ['email' => 'andi@gmail.com'],
            ['name' => 'Andi Pratama', 'password' => Hash::make('password'), 'role' => 'user']
        );
        $customer2 = User::updateOrCreate(
            ['email' => 'siti@gmail.com'],
            ['name' => 'Siti Rahma', 'password' => Hash::make('password'), 'role' => 'user']
        );
        $customer3 = User::updateOrCreate(
            ['email' => 'budi@gmail.com'],
            ['name' => 'Budi Santoso', 'password' => Hash::make('password'), 'role' => 'user']
        );

        $prod1 = Product::where('name', 'The Cambridge')->first();
        $prod2 = Product::where('name', 'The Architect')->first();
        $prod3 = Product::where('name', 'The Maverick')->first();

        // Seed Dummy Orders so customers are verified buyers
        if ($prod1) {
            \App\Models\Order::firstOrCreate(
                ['email' => 'andi@gmail.com', 'total' => 2370750],
                [
                    'user_id' => $customer1->id,
                    'first_name' => 'Andi',
                    'last_name' => 'Pratama',
                    'address' => 'Jl. Sudirman No. 45',
                    'city' => 'Jakarta Selatan',
                    'zip' => '12190',
                    'items' => [
                        ['id' => $prod1->id, 'name' => $prod1->name, 'price' => $prod1->price, 'qty' => 1]
                    ],
                    'subtotal' => $prod1->price,
                    'tax' => round($prod1->price * 0.09),
                    'total' => $prod1->price + round($prod1->price * 0.09)
                ]
            );

            \App\Models\Review::updateOrCreate(
                ['user_id' => $customer1->id, 'product_id' => $prod1->id],
                [
                    'rating' => 5,
                    'comment' => 'Kacamata The Cambridge ini sangat pas dan nyaman dipakai seharian. Hasil coba AR di kamera sangat mirip dengan aslinya!',
                    'fit_feedback' => 'Sangat Pas',
                    'vto_accuracy' => 'Sesuai AR'
                ]
            );
        }

        if ($prod2) {
            \App\Models\Order::firstOrCreate(
                ['email' => 'siti@gmail.com', 'total' => 2681400],
                [
                    'user_id' => $customer2->id,
                    'first_name' => 'Siti',
                    'last_name' => 'Rahma',
                    'address' => 'Jl. Malioboro No. 12',
                    'city' => 'Yogyakarta',
                    'zip' => '55271',
                    'items' => [
                        ['id' => $prod2->id, 'name' => $prod2->name, 'price' => $prod2->price, 'qty' => 1]
                    ],
                    'subtotal' => $prod2->price,
                    'tax' => round($prod2->price * 0.09),
                    'total' => $prod2->price + round($prod2->price * 0.09)
                ]
            );

            \App\Models\Review::updateOrCreate(
                ['user_id' => $customer2->id, 'product_id' => $prod2->id],
                [
                    'rating' => 5,
                    'comment' => 'Material titaniumnya solid dan premium. Bentuk frame kotak terlihat elegan untuk wajah oval.',
                    'fit_feedback' => 'Sangat Pas',
                    'vto_accuracy' => 'Sesuai AR'
                ]
            );
        }

        if ($prod3) {
            \App\Models\Order::firstOrCreate(
                ['email' => 'budi@gmail.com', 'total' => 3008400],
                [
                    'user_id' => $customer3->id,
                    'first_name' => 'Budi',
                    'last_name' => 'Santoso',
                    'address' => 'Jl. Diponegoro No. 8',
                    'city' => 'Surabaya',
                    'zip' => '60241',
                    'items' => [
                        ['id' => $prod3->id, 'name' => $prod3->name, 'price' => $prod3->price, 'qty' => 1]
                    ],
                    'subtotal' => $prod3->price,
                    'tax' => round($prod3->price * 0.09),
                    'total' => $prod3->price + round($prod3->price * 0.09)
                ]
            );

            \App\Models\Review::updateOrCreate(
                ['user_id' => $customer3->id, 'product_id' => $prod3->id],
                [
                    'rating' => 4,
                    'comment' => 'Model aviator gold mewah sekali. Sedikit agak longgar di hidung saya tapi overall sangat memuaskan.',
                    'fit_feedback' => 'Sedikit Longgar',
                    'vto_accuracy' => 'Sesuai AR'
                ]
            );
        }
    }
}
