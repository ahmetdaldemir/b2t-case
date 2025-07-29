<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        User::factory()->admin()->create([
            'name' => 'Admin User',
            'email' => 'admin@b2b.com',
            'password' => bcrypt('password'),
        ]);

        // Create customer user
        User::factory()->customer()->create([
            'name' => 'Customer User',
            'email' => 'customer@b2b.com',
            'password' => bcrypt('password'),
        ]);

        // Create additional users
        User::factory(5)->customer()->create();
        User::factory(2)->admin()->create();

        // Create products
        Product::factory(20)->create();

        // Create orders with order items
        User::factory(3)->customer()->create()->each(function ($user) {
            Order::factory(rand(1, 3))->create([
                'user_id' => $user->id,
            ])->each(function ($order) {
                // Add 1-3 products to each order
                $products = Product::inRandomOrder()->take(rand(1, 3))->get();
                
                foreach ($products as $product) {
                    OrderItem::factory()->create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'quantity' => rand(1, 5),
                        'unit_price' => $product->price,
                    ]);
                }
                
                // Recalculate total price
                $order->calculateTotalPrice();
            });
        });
    }
}
