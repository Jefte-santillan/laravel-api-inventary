<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TypeOperations;

class TypeOperationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TypeOperations::firstOrCreate([
            'name' => 'in',
        ], [
            'description' => 'Entrada',
        ]);

        TypeOperations::firstOrCreate([
            'name' => 'out',
        ], [
            'description' => 'Salida',
        ]);
    }
}
