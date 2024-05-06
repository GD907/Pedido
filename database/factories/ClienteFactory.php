<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cliente>
 */
class ClienteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre_comercio' => $this->faker->company,
            'nombre' => $this->faker->firstName,
            'apellido' => $this->faker->lastName,
            'direccion' => $this->faker->streetAddress,
            'barrio' => $this->faker->city,
            'ciudad' => $this->faker->city,
            'telefono' => $this->faker->phoneNumber,
            'correo' => $this->faker->unique()->safeEmail,
        ];
    }
}
