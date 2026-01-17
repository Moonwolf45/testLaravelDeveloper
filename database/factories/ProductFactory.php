<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'category_id' => Category::factory(),
            'in_stock' => $this->faker->boolean(80),
            'rating' => $this->faker->randomFloat(1, 1, 5),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
