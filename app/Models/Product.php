<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory; // 2. Додай цей рядок обов'язково

    // Також відразу дозволимо масове заповнення полів (це знадобиться пізніше)
    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'stock',
        'is_active',
        'category_id',
        'image'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class); // Товар належить категорії
    }
}
