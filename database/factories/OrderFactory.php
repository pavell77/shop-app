<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(), // Автоматично створить юзера, якщо не передати id
            'total_price' => $this->faker->randomFloat(2, 100, 5000),
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}