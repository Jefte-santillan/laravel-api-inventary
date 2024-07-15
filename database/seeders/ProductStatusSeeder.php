<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductStatus;

class ProductStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ProductStatus::firstOrCreate([
            'key' => 'active',
        ], [
            'description' => 'Activo',
        ]);

        ProductStatus::firstOrCreate([
            'key' => 'desactive',
        ], [
            'description' => 'Desactivado',
        ]);
    }
}
