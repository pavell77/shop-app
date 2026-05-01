<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\WayForPayService;

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

        // Рахуємо загальну кількість одиниць у кошику
        $totalCount = array_sum(array_column($cart, 'quantity'));

        if ($request->ajax()) {
            return response()->json([
                'message' => 'Товар додано!',
                'totalCount' => $totalCount
            ]);
        }

        return redirect()->back()->with('success', 'Товар додано!');
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
    public function checkout(OrderService $orderService, WayForPayService $wayForPayService)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Будь ласка, увійдіть.');
        }

        $cart = session()->get('cart', []);

        try {
            // 1. Створюємо замовлення (тут же списується склад і очищується сесія)
            $order = $orderService->createOrderFromCart(Auth::id(), $cart);
            
            // 2. Генеруємо HTML-форму WayForPay
            $paymentForm = $wayForPayService->getPaymentForm($order);
            
            // 3. Повертаємо в'юху з кнопкою
            return view('cart.checkout', compact('order', 'paymentForm'));
                    
        } catch (\Exception $e) {
            return redirect()->route('cart.index')
                ->with('error', 'Помилка: ' . $e->getMessage());
        }
    }
}