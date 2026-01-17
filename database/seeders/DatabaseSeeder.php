<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Создаем 5 категорий
        $categories = Category::factory()->count(5)->create();

        // Создаем 15 продуктов и привязываем к случайным категориям
        Product::factory()->count(15)->create([
            'category_id' => function () use ($categories) {
                return $categories->random()->id;
            }
        ]);
    }
}
