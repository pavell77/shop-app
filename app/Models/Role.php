<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    // Дозволяємо масове заповнення для полів id та name
    protected $fillable = [
        'id',
        'name',
    ];

    /**
     * Отримати всіх користувачів, які мають цю роль.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}