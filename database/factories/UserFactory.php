<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'avatar' => null, // Можна додати fake()->imageUrl(), якщо потрібно
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role_id' => 2, // Значення за замовчуванням для звичайного юзера
        ];
    }

    // Зручний метод для створення адміна в тестах: User::factory()->admin()->create();
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role_id' => 1, 
        ]);
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}