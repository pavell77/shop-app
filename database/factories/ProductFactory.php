<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        $name = $this->faker->unique()->words(3, true); 

        return [
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
            'description' => $this->faker->paragraph(),
            'image' => 'products/default.jpg', // Фейковий шлях до картинки
            'price' => $this->faker->randomFloat(2, 10, 2000), 
            'stock' => $this->faker->numberBetween(5, 50), // Ставимо > 0 для успішних тестів
            'is_active' => true,
            // Якщо категорія обов'язкова, розкоментуй рядок нижче:
            // 'category_id' => Category::factory(), 
        ];
    }
}