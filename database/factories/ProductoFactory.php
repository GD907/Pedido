<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Producto;
use App\Models\CategoriaProducto;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Producto>
 */
class ProductoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {  return [
        'codigo' => $this->faker->unique()->isbn10,
        'nombre' => $this->faker->sentence,
        'descripcion' => $this->faker->paragraph,
        'proveedor' => $this->faker->company,
        'preciocompra' => $this->faker->randomFloat(2, 0, 1000),
        'precio' => $this->faker->randomFloat(2, 0, 2000),
        'stock' => $this->faker->numberBetween(0, 100),
        'unidades_caja' => $this->faker->numberBetween(1, 50),
        'umbralmin' => $this->faker->numberBetween(0, 20),
        'categoria_productos_id' => function () {
            return CategoriaProducto::factory()->create()->id;
        },
    ];
    }
}
