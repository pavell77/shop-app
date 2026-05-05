<?php

namespace App\Services;

use WayForPay\SDK\Wizard\PurchaseWizard;
use WayForPay\SDK\Domain\Product;
use WayForPay\SDK\Credential\AccountSecretCredential;
use WayForPay\SDK\Collection\ProductCollection;
use WayForPay\SDK\Handler\ServiceUrlHandler;

class WayForPayService
{
    /**
     * Генерує HTML-форму для оплати замовлення.
     */
    public function getPaymentForm($order)
    {
        $credential = new AccountSecretCredential(
            config('services.wayforpay.account'),
            config('services.wayforpay.secret')
        );

        $products = new ProductCollection();
        
        if (isset($order->orderItems) && $order->orderItems->count() > 0) {
            foreach ($order->orderItems as $item) {
                $products->add(new Product(
                    $item->product->name ?? 'Товар',
                    $item->price,
                    $item->quantity
                ));
            }
        } else {
            $products->add(new Product(
                "Замовлення №{$order->id}", 
                $order->total_price, 
                1
            ));
        }

        $wizard = PurchaseWizard::get($credential)
            ->setOrderReference($order->id . '_' . time()) 
            ->setAmount($order->total_price)
            ->setCurrency('UAH')
            ->setOrderDate(new \DateTime())
            ->setMerchantDomainName(config('services.wayforpay.domain'))
            ->setServiceUrl(route('payment.callback'))
            ->setReturnUrl(route('payment.success')) 
            ->setProducts($products);

        return $wizard->getForm()->getAsString();
    }

    /**
     * Перевіряє підпис від WayForPay для Callback запиту.
     * 
     * @param array $data Дані з $request->all()
     * @return bool
     */
    public function isSignatureValid(array $data): bool
    {
        $credential = new AccountSecretCredential(
            config('services.wayforpay.account'),
            config('services.wayforpay.secret')
        );

        try {
            // SDK автоматично перевіряє signature всередині parseRequestData
            $handler = new ServiceUrlHandler($credential);
            $response = $handler->parseRequestData($data);
            
            return $response->getReason()->isOK();
        } catch (\Exception $e) {
            \Log::error("WayForPay Signature error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Генерує відповідь для WayForPay після успішної обробки callback.
     */
    public function getSuccessResponse($orderReference)
    {
        $credential = new AccountSecretCredential(
            config('services.wayforpay.account'),
            config('services.wayforpay.secret')
        );

        $handler = new ServiceUrlHandler($credential);
        return $handler->getSuccessResponse($orderReference);
    }
}