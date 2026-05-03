<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\PaymentController;

// 1. ПУБЛІЧНІ МАРШРУТИ (Доступні всім, включаючи роботів оплати)
Route::get('/', function () {
    return view('welcome');
});

// Було:
// Route::post('/payment/callback', function() {return response('OK', 200);})->name('payment.callback');
Route::post('/payment/callback', [PaymentController::class, 'callback'])->name('payment.callback');

Route::match(['get', 'post'], '/payment/success', function () {
    return view('payment.success');
})->name('payment.success');


// 2. МАРШРУТИ, ЗАХИЩЕНІ АВТОРИЗАЦІЄЮ
Route::middleware(['auth', 'verified'])->group(function () {
    
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Продукти
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::patch('/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

    // Профіль
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::patch('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.update-avatar');

    // Кошик (Сам перехід до оплати залишаємо під auth)
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/remove/{product}', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
});

require __DIR__.'/auth.php';