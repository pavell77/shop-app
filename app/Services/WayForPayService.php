<?php

namespace App\Services;

use WayForPay\SDK\Wizard\PurchaseWizard;
use WayForPay\SDK\Domain\Product;
use WayForPay\SDK\Credential\AccountSecretCredential;
use WayForPay\SDK\Collection\ProductCollection;

class WayForPayService
{
    /**
     * Генерує HTML-форму для оплати замовлення через WayForPay.
     *
     * @param  \App\Models\Order  $order
     * @return string
     */
    public function getPaymentForm($order)
    {
        // Створюємо об'єкт облікових даних мерчанта
        $credential = new AccountSecretCredential(
            config('services.wayforpay.account'),
            config('services.wayforpay.secret')
        );

        // Створюємо колекцію продуктів
        $products = new ProductCollection();
        
        // Перевіряємо наявність позицій у замовленні
        if (isset($order->orderItems) && $order->orderItems->count() > 0) {
            foreach ($order->orderItems as $item) {
                $products->add(new Product(
                    $item->product->name ?? 'Товар',
                    $item->price,
                    $item->quantity
                ));
            }
        } else {
            // Резервний варіант, якщо позиції не завантажені
            $products->add(new Product(
                "Замовлення №{$order->id}", 
                $order->total_price, 
                1
            ));
        }

        // Налаштування платіжного візарда
        $wizard = PurchaseWizard::get($credential)
            ->setOrderReference($order->id . '_' . time()) // Додаємо time() для унікальності в тестах
            ->setAmount($order->total_price)
            ->setCurrency('UAH')
            ->setOrderDate(new \DateTime())
            ->setMerchantDomainName(config('services.wayforpay.domain'))
            ->setServiceUrl(route('payment.callback'))
            ->setReturnUrl(route('payment.success')) // Тепер веде на окрему сторінку успіху
            ->setProducts($products);

        // Повертаємо HTML-код форми у вигляді рядка
        return $wizard->getForm()->getAsString();
    }
}