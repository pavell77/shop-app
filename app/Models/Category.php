<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    // Додаємо цей масив:
    protected $fillable = ['name', 'slug'];

    public function products()
    {
        return $this->hasMany(Product::class); // У категорії багато товарів
    }
}
