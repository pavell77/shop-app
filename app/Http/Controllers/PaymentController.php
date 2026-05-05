<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Mail\OrderPaidMail;
use App\Services\WayForPayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $wayForPayService;

    /**
     * Впроваджуємо сервіс через конструктор
     */
    public function __construct(WayForPayService $wayForPayService)
    {
        $this->wayForPayService = $wayForPayService;
    }

    /**
     * Обробка Callback від WayForPay
     */
    public function callback(Request $request)
    {
        // 1. Отримуємо дані та логуємо для відладки
        $data = $request->all();
        Log::info('WayForPay Callback Data:', $data);

        // 2. ПЕРЕВІРКА ПІДПИСУ (Безпека)
        if (!$this->wayForPayService->isSignatureValid($data)) {
            Log::warning('WayForPay Callback: Invalid signature attempted', ['data' => $data]);
            return response()->json(['status' => 'declined', 'error' => 'Invalid signature']);
        }

        // 3. Витягуємо чистий ID замовлення
        $orderReference = $data['orderReference'] ?? '';
        $orderId = explode('_', $orderReference)[0];

        $order = Order::find($orderId);

        if (!$order) {
            Log::error("WayForPay Callback: Order {$orderId} not found");
            return response()->json(['status' => 'declined', 'error' => 'Order not found']);
        }

        // 4. Перевіряємо статус транзакції
        if (isset($data['transactionStatus']) && $data['transactionStatus'] === 'Approved') {
            
            // Оновлюємо статус замовлення в БД
            $order->update(['status' => 'paid']);

            // ВІДПРАВКА ЛИСТА (йде в чергу, якщо налаштовано ShouldQueue)
            try {
                Mail::to($order->user->email)->send(new OrderPaidMail($order));
            } catch (\Exception $e) {
                Log::error("Failed to send OrderPaidMail for order {$order->id}: " . $e->getMessage());
            }

            // 5. Повертаємо офіційну відповідь через SDK (формат accept)
            return response()->json($this->wayForPayService->getSuccessResponse($orderReference));
        }

        Log::info("Order {$order->id} payment was not approved. Status: " . ($data['transactionStatus'] ?? 'unknown'));
        
        return response()->json(['status' => 'declined']);
    }

    /**
     * Сторінка, на яку WayForPay повертає клієнта після оплати
     */
    public function success()
    {
        return view('payment.success', [
            'message' => 'Дякуємо! Ваша оплата успішно отримана.'
        ]);
    }
}