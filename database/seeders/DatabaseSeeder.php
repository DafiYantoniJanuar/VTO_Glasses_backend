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
    }
}
