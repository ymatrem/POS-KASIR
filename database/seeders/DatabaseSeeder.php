<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create demo admin user if not exists
        if (!User::where('email', 'demo@example.com')->exists()) {
            User::create([
                'name' => 'Admin Demo',
                'email' => 'demo@example.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]);
        }

        // Create demo cashier user if not exists
        if (!User::where('email', 'cashier@example.com')->exists()) {
            User::create([
                'name' => 'Kasir Demo',
                'email' => 'cashier@example.com',
                'password' => Hash::make('cashier'),
                'role' => 'cashier',
            ]);
        }

        // Create default category if not exists
        if (!Category::where('name', 'Makanan')->exists()) {
            Category::create([
                'name' => 'Makanan',
                'slug' => Str::slug('Makanan'),
                'description' => 'Kategori makanan',
            ]);
        }
    }
}
