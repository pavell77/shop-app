<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class OrderService
{
    /**
     * Створює замовлення, списує залишки та очищує кошик.
     * 
     * @param int $userId
     * @param array $cart
     * @return Order
     * @throws \Exception
     */
    public function createOrderFromCart(int $userId, array $cart): Order
    {
        // Перевірка на порожній кошик перед початком транзакції
        if (empty($cart)) {
            throw new \Exception('Неможливо оформити порожній кошик.');
        }

        return DB::transaction(function () use ($userId, $cart) {
            // 1. Рахуємо загальну суму замовлення
            $totalPrice = array_reduce($cart, function ($carry, $item) {
                return $carry + ($item['price'] * $item['quantity']);
            }, 0);

            // 2. Створюємо основний запис замовлення
            $order = Order::create([
                'user_id' => $userId,
                'total_price' => $totalPrice,
                'status' => 'pending',
            ]);

            // 3. Обробляємо кожну позицію з кошика
            foreach ($cart as $productId => $item) {
                // Використовуємо lockForUpdate для запобігання race condition на складі
                $product = Product::lockForUpdate()->findOrFail($productId);

                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Товар {$product->name} закінчився або недостатньо на складі.");
                }

                // Створюємо запис у таблиці позицій замовлення
                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $productId,
                    'quantity'   => $item['quantity'],
                    'price'      => $item['price'],
                ]);

                // Списуємо кількість зі складу
                $product->decrement('stock', $item['quantity']);
            }

            // 4. Очищуємо кошик у сесії після успішного збереження в БД
            session()->forget('cart');

            return $order;
        });
    }
}