<?php

namespace Database\Factories;

use BcMath\Number;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\=item>
 */
class itemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'type' => fake()->name(),
            'company' => fake()->name(),
            'price' =>  rand(2500,4500),
            'stock' =>  0,
        ];
    }
}
