<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Location;
use App\Models\Mutation;
use App\Models\Product;
use App\Models\ProductLocation;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(10)->create();
        $units = ['Pcs', 'Kg', 'Ltr', 'Box'];
        foreach ($units as $unit) {
            Unit::create([
                'name' => $unit,
                'description' => "Description for $unit",
            ]);
        }

        $categories = ['Electronics', 'Furniture', 'Clothing', 'Books'];
        foreach ($categories as $category) {
            Category::create([
                'name' => $category,
                'description' => "Description for $category",
            ]);
        }

        $products = [
            [
                'product_code' => 'PRD001',
                'name_product' => 'Laptop',
                'category_id' => 1,
                'unit_id' => 1,
            ],
            [
                'product_code' => 'PRD002',
                'name_product' => 'Sofa',
                'category_id' => 2,
                'unit_id' => 1,
            ],
            [
                'product_code' => 'PRD003',
                'name_product' => 'T-Shirt',
                'category_id' => 3,
                'unit_id' => 1,
            ],
            [
                'product_code' => 'PRD004',
                'name_product' => 'Novel',
                'category_id' => 4,
                'unit_id' => 1,
            ],
        ];

        foreach ($products as $product) {
            Product::create([
                'product_code' => $product['product_code'],
                'name_product' => $product['name_product'],
                'category_id' => $product['category_id'],
                'unit_id' => $product['unit_id'],
            ]);
        }

        $locations = [
            [
                'location_code' => 'LOC001',
                'location_name' => 'Warehouse A',
            ],
            [
                'location_code' => 'LOC002',
                'location_name' => 'Warehouse B',
            ],
            [
                'location_code' => 'LOC003',
                'location_name' => 'Storefront',
            ],
        ];

        foreach ($locations as $location) {
            Location::create([
                'location_code' => $location['location_code'],
                'location_name' => $location['location_name'],
            ]);
        }

        $productLocations = [
            ['product_id' => 1, 'location_id' => 1, 'stock' => 50],
            ['product_id' => 1, 'location_id' => 2, 'stock' => 30],
            ['product_id' => 2, 'location_id' => 1, 'stock' => 20],
            ['product_id' => 2, 'location_id' => 3, 'stock' => 15],
            ['product_id' => 3, 'location_id' => 2, 'stock' => 40],
            ['product_id' => 3, 'location_id' => 3, 'stock' => 25],
            ['product_id' => 4, 'location_id' => 1, 'stock' => 10],
            ['product_id' => 4, 'location_id' => 2, 'stock' => 5],
            ['product_id' => 4, 'location_id' => 3, 'stock' => 8],
        ];

        foreach ($productLocations as $pl) {
            ProductLocation::create([
                'product_id' => $pl['product_id'],
                'location_id' => $pl['location_id'],
                'stock' => $pl['stock'],
            ]);
        }

        $productLocations = ProductLocation::all();
        foreach ($productLocations as $pl) {
            Mutation::create([
                'user_id' => 1,
                'product_location_id' => $pl->id,
                'mutation_code' => 'INIT' . str_pad($pl->id, 3, '0', STR_PAD_LEFT),
                'mutation_date' => now()->subDays(10),
                'type' => 'in',
                'quantity' => $pl->stock,
                'note' => 'Initial stock for ProductLocation ID ' . $pl->id,
            ]);
        }

        $mutations = [
            [
                'user_id' => 2,
                'product_location_id' => 1,
                'mutation_code' => 'MUT002',
                'mutation_date' => now()->subDays(4),
                'type' => 'out',
                'quantity' => 5,
                'note' => 'Sold Laptop from Warehouse A'
            ],
            [
                'user_id' => 3,
                'product_location_id' => 2,
                'mutation_code' => 'MUT003',
                'mutation_date' => now()->subDays(3),
                'type' => 'in',
                'quantity' => 20,
                'note' => 'Restock Sofa at Warehouse B'
            ],
            [
                'user_id' => 4,
                'product_location_id' => 6,
                'mutation_code' => 'MUT004',
                'mutation_date' => now()->subDays(2),
                'type' => 'out',
                'quantity' => 7,
                'note' => 'Sold T-Shirt from Storefront'
            ],
            [
                'user_id' => 5,
                'product_location_id' => 5,
                'mutation_code' => 'MUT005',
                'mutation_date' => now()->subDays(1),
                'type' => 'in',
                'quantity' => 15,
                'note' => 'Restock T-Shirt at Warehouse B'
            ],
        ];

        foreach ($mutations as $mutation) {
            $productLocation = ProductLocation::find($mutation['product_location_id']);
            if ($productLocation) {
                if ($mutation['type'] === 'in') {
                    $productLocation->stock += $mutation['quantity'];
                } else {
                    $productLocation->stock -= $mutation['quantity'];
                    if ($productLocation->stock < 0) {
                        $productLocation->stock = 0;
                    }
                }
                $productLocation->save();
            }
            Mutation::create([
                'user_id' => $mutation['user_id'],
                'product_location_id' => $mutation['product_location_id'],
                'mutation_code' => $mutation['mutation_code'],
                'mutation_date' => $mutation['mutation_date'],
                'type' => $mutation['type'],
                'quantity' => $mutation['quantity'],
                'note' => $mutation['note'],
            ]);
        }
    }
}
