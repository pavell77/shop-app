<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Mail\OrderPaidMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function callback(Request $request)
    {
        // 1. Отримуємо всі дані від WayForPay
        $data = $request->all();
        
        // Логуємо для відладки (можна подивитися в storage/logs/laravel.log)
        Log::info('WayForPay Callback Data:', $data);

        // 2. Витягуємо чистий ID замовлення (якщо ми додавали час через підкреслення)
        $orderReference = $data['orderReference'] ?? '';
        $orderId = explode('_', $orderReference)[0];

        $order = Order::find($orderId);

        // 3. Перевіряємо статус транзакції
        if ($order && isset($data['transactionStatus']) && $data['transactionStatus'] === 'Approved') {
            
            // Оновлюємо статус замовлення в БД
            $order->update(['status' => 'paid']);

            // ВІДПРАВКА ЛИСТА (йде в Redis через ShouldQueue)
            Mail::to($order->user->email)->send(new OrderPaidMail($order));

            // Важливо повернути саме таку відповідь для WayForPay
            return response()->json(['status' => 'accept']);
        }

        return response()->json(['status' => 'declined']);
    }
}