<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProductPolicy
{
    /**
     * Перед будь-якою перевіркою - адмін отримує зелене світло
     */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->role->name === 'admin') {
            return true;
        }
        return null;
    }

    public function viewAny(User $user): bool
    {
        // Перегляд товарів дозволено всім (адмін через before, юзер і манагер тут)
        return true;
    }

    public function view(User $user, Product $product): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        // Адмін вже пройшов через before, тут тільки повертаємо false для інших
        return false;
    }

    public function update(User $user, Product $product): bool
    {
        // Дозволено менеджеру (адмін через before)
        return $user->role->name === 'manager';
    }

    public function delete(User $user, Product $product): bool
    {
        return false; // Адмін через before
    }

    public function restore(User $user, Product $product): bool
    {
        return false;
    }

    public function forceDelete(User $user, Product $product): bool
    {
        return false;
    }
}