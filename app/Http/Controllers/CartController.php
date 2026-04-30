<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        return view('cart.index', compact('cart'));
    }

    public function add(Request $request, Product $product)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$product->id])) {
            $cart[$product->id]['quantity']++;
        } else {
            $cart[$product->id] = [
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => 1
            ];
        }

        session()->put('cart', $cart);
        return redirect()->route('cart.index')->with('success', 'Товар додано!');
    }

    public function remove($id)
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }
        return redirect()->back()->with('success', 'Товар видалено з кошика!');
    }

    /**
     * Оформлення замовлення
     */
    public function checkout(OrderService $orderService)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Будь ласка, увійдіть, щоб оформити замовлення.');
        }

        // Отримуємо актуальний вміст кошика з сесії
        $cart = session()->get('cart', []);

        try {
            // Передаємо два обов'язкові аргументи: ID користувача та масив кошика
            $order = $orderService->createOrderFromCart(Auth::id(), $cart);
            
            return redirect()->route('products.index')
                ->with('success', "Замовлення №{$order->id} успішно створено!");
                
        } catch (\Exception $e) {
            return redirect()->route('cart.index')
                ->with('error', 'Помилка оформлення: ' . $e->getMessage());
        }
    }
}