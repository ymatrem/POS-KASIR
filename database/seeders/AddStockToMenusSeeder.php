<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class AddStockToMenusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Set default stock untuk semua menu yang belum punya stok
        Menu::where('stock', 0)->update(['stock' => 10]);

        $this->command->info('Stok default telah ditambahkan ke menu!');
    }
}
