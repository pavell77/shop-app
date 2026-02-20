<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

// 1. Публічні маршрути (доступні всім)
Route::get('/', function () {
    return view('welcome');
});

// 2. Маршрути, захищені авторизацією
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Дашборд
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Твої продукти (Каталог + Створення)
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');

    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::patch('/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

    // Профіль користувача
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Маршрути аутентифікації (Login, Register тощо)
require __DIR__.'/auth.php';