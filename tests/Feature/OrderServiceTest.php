<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderServiceTest extends TestCase
{
    use RefreshDatabase; // Очищує базу перед кожним тестом

    protected function setUp(): void
    {
        parent::setUp();

        // Створюємо ролі безпосередньо в базі даних тесту (:memory:)
        \App\Models\Role::create(['id' => 1, 'name' => 'admin']);
        \App\Models\Role::create(['id' => 2, 'name' => 'user']);

    }

    public function test_it_creates_an_order_successfully()
    {
        // 1. Підготовка (Arrange)
        $user = User::factory()->create();
        $product = Product::factory()->create([
            'name' => 'Тестовий товар',
            'price' => 100,
            'stock' => 10,
        ]);

        $cart = [
            $product->id => [
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => 2,
            ]
        ];

        // Імітуємо наявність кошика в сесії
        session(['cart' => $cart]);

        $service = new OrderService();

        // 2. Дія (Act)
        $order = $service->createOrderFromCart($user->id, $cart);

        // 3. Перевірка (Assert)
        // Чи створилося замовлення в базі?
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'user_id' => $user->id,
            'total_price' => 200,
            'status' => 'pending',
        ]);

        // Чи списався товар зі складу? (Було 10, купили 2 -> стало 8)
        $this->assertEquals(8, $product->fresh()->stock);

        // Чи очистився кошик у сесії?
        $this->assertFalse(session()->has('cart'));
    }

    public function test_it_fails_if_stock_is_insufficient()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 5]);

        $cart = [
            $product->id => [
                'name' => $product->name,
                'price' => 10,
                'quantity' => 10, // Більше ніж є на складі
            ]
        ];

        $service = new OrderService();

        // Очікуємо виключення (Exception)
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Товар {$product->name} закінчився або недостатньо на складі.");

        $service->createOrderFromCart($user->id, $cart);
    }
}