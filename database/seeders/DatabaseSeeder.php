<?php

namespace Database\Seeders;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\CategoriaProducto;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@admin.com',
            'password' => bcrypt('abcd1234')
        ]);

        // \App\Models\User::factory(10)->create();

        CategoriaProducto::factory()->count(5)->create();

        Cliente::factory()->count(10)->create();
        Producto::factory()->count(10)->create();
    }
}
